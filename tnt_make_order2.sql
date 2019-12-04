create or replace procedure MAKE_ORDER2 (
    idNewDoc in out NUMBER, sHdr VARCHAR2,
    -- = tip^dlvDat^idFirm^idOrg^idAdr^idRecv^idAg^cred^zakNo^zakDat^idSkl^
    --     howGot^rem^newAddr^nGeneratedLines^rGeneratedSum^secs^idAuth^
    --     dtCli^nomDoc^idAgEx^
    --  newAddr:= TIP_NP;NAME_NP;VID_PRCH;NAME_PRCH;DOM;KOR;KV;PRIM
    --  idOrg=null => use idAdr to resolve
    --  idFirm=null => use firm from org-agreement => def.firm
    --  idAgEx => curEmpAsExAg => from iFlags,iExAgDiv idAuth or another, 17.08.17
    --  zakDat= 'dd.mm.yyyy[ hh:mi:ss]'  -- 03.08.17
    --  dates:= dd.mm.yyyy
    --  if tip!= 2,4,6 then create in-bill, 25.01.16
    --  if idNewDoc = -2 at input, do NOT clear lines in BDOC.m_arLines
    sLines VARCHAR2,  -- = cmc1^kol1^prc1^..cmcN^kolN^prcN^
                      -- prc=0 or null use def-computed
    iFlags INTEGER, -- = 1*bSFact + 2*bResv + 4*bSamVyv + + 8*bKor
                    --  + 16*bAutoGen + 32*bAttToExist + 64*bUseDefSF
                    --  + 128*bUseDefCre + 256*bBonusAsNak
                    --  + 512*idxExAgDiv (1-15)      19.08.15
                    --  + 8192*bUseCurEmpAsExAgent   19.08.15
                    --  + 16384*bKolAsKor             9.02.16
    msg out VARCHAR2  -- ~30 bytes/doc +hdr+warn+deficit+bonus
  )
  -- on return: msg[0]=' ' ok, else error
  -- calls: GENPACK.GET_BANK_DATE(dNak, nDays)
  --        BDOC.PICK_NEW_TRIP(idTrip, dNak, idRoute)
  --        GENPACK.SPLIT(arr, str, '^');
  --        BDOC.INIT(idOrg,dNak, msg, idAdr);
  --        BDOC.STORE_LINE(idMC, kol, 0, msg);
  --        DIRS.TOV_ASSIGN_MISSED_SECTION(idMC)
  --        BDOC.MOVE_OST(idMC,idNewDoc,idSkl, -nKolD,'MAKE_ORDER2')
  --        BDOC.UPDATE_SUMS(idNewDoc,0)
  --        BONUS.PROCESS_SHIP(idNewDoc, mes)

  --  generate sale-doc for client,      re-started 29.10.09
  --              fully revised ver.2,  16-17.08.14
  --   18.08.15  create bonus as nakl if any
  --   16.10.15  force SF flag as in KATORG.STATUS
  --   13.06.17  store org tov.codes when got via kontur
  --   17.08.17  idAgEx in header params
  --   24.10.17  loop for MAKE_NEXT_DOC, separate doc with noVat tovar,
  --               divide non-bank over MaxNalSum bulk into parts
 is
  TYPE typArrN  IS TABLE OF NUMBER(12) INDEX BY BINARY_INTEGER;
  TYPE typRec IS RECORD (
    CMC    SPDOC.CMC%TYPE,
    KOL    SPDOC.KOL%TYPE,
    PRICE  SPDOC.PRICE%TYPE,
    PVAT   SPDOC.PROC_NDS%TYPE,
    SUMM   SPDOC.SUMMA%TYPE,
    GTD    SPDOC.CCUSTOMS%TYPE,
    ORKOD  SPDOC.ORGCMC%TYPE
  );
  TYPE typARec IS TABLE OF typRec INDEX BY PLS_INTEGER; -- 23.10.17

  cMaxNalSum  constant PLS_INTEGER:= 100000; -- max edge for kass pay, 23.10.17
  cSFact      constant PLS_INTEGER:=     1;  -- iFlags
  cResv       constant PLS_INTEGER:=     2;
  cSamVyv     constant PLS_INTEGER:=     4;
  cKor        constant PLS_INTEGER:=     8;
  cAutoGen    constant PLS_INTEGER:=    16;
  cAttToExi   constant PLS_INTEGER:=    32;
  cUseDefSF   constant PLS_INTEGER:=    64;
  cUseDefCre  constant PLS_INTEGER:=   128;
  cBonAsNak   constant PLS_INTEGER:=   256;
  cExAgDiv    constant PLS_INTEGER:=   512;
  cUseEmAsEAg constant PLS_INTEGER:=  8192;
  cKolAsKor   constant PLS_INTEGER:= 16384;
  -- flags
  bKorOnly  BOOLEAN:= false;  -- only whole korobki to include
  bDeliv    BOOLEAN:= true;   -- samovyvoz (0), delivery(1)
  bAttToExistDoc BOOLEAN:= false; -- attach to existing nom on delivery
  bUseDefSFa BOOLEAN:= false; -- if use SFac as set at KATORG.STATUS
  bUseDefCre BOOLEAN:= false; -- if use PayType as set at KATORG.PRLIST
  bBonusAsNa BOOLEAN:= false; -- if create bonus with nakl (as bill otherwise)
  bKolAsKor  BOOLEAN:= false; -- for in-docs
  -- header
  iTip      PLS_INTEGER:= 2; -- by def rashod
  iVkl      PLS_INTEGER:= 0; -- augment it later
  idSkl     PLS_INTEGER:= CST.cMainSkl;  -- by def
  iBill     INTEGER;
  dtNak     DATE;
  dtPlat    DATE;
  dtCli     DATE;
  dtZak     DATE;
  idFirm    INTEGER;
  idFirm0   INTEGER:= GENPACK.CONFIG_PAR(CST.cfgFirm0); -- firm w/o VAT,31.10.17
  idOrg     INTEGER;
  idAdr     INTEGER;
  idRecv    INTEGER;
  iCred     PLS_INTEGER;     -- for BASEDOC.CREDIT
  idAgent   INTEGER;
  docNom    VARCHAR2(20);
  idFirmB   INTEGER;   -- firm bank acc in docs to be generated
  idOrgB    INTEGER;   -- org bank acc in docs to be generated
  idTrip    INTEGER;
  iHowGot   INTEGER;  -- 0 by paper-prlist, 1 by phone REP, 2 by phone from-cli,
  --                     3 by phone cli-himself, 4 electronic, 5 web-trn
  --                     6 lever, 7 asina, 8 nefis, 9 kontur, 10 colgate,
  --                     11 evyap  (ref. CST)
  nLines    PLS_INTEGER;     -- compute from sLines
  nGenLines PLS_INTEGER;     -- given outside
  rGenSum   NUMBER(12,2);
  nWorkMin  INTEGER;  -- how long order was in work, min
  idAgEx    INTEGER;  -- extra (group-specific) agent, 6.03.15
  idxEx     INTEGER;  -- channel index: 1 lever,2 colg, 3 nefis, 4 evyap, 25.08.15
                      --    5 vesna, 23.08.17
                      -- used in KATSOTR.EFLAGS: +2*channelIdx
                      -- ex.ag.divisions are listed in CST.cExDiv*
  idAuth    INTEGER:= SSEC.AUTH_PATH.GET_CUR_USER_KATSOTR_ID;

  idMC      NUMBER(10);
  nKolO     PLS_INTEGER;
  nKolD     PLS_INTEGER;
  rPrc      NUMBER(12,3);
  nK        PLS_INTEGER;
  aResDoc   typArrN;    -- dict of reserve-docs, osts have been taken from
  lnRec     BDOC.typLineRec;
  warn      VARCHAR2(80);
  logm      VARCHAR2(120); -- log-mes, 09.02.19
  iDefic    INTEGER:= 0;
  exErr     EXCEPTION;
  -- arrays to accumulate tov.items, 23.10.17
  aTovs     typARec;  -- regular
  aTovs0    typARec;  -- to sell with vat-rate=0
  nTovs     PLS_INTEGER:= 0; -- count in aTovs,aTovs0
  nTovs0    PLS_INTEGER:= 0;
  aDefic    typARec;
  iTovFlg   INTEGER;  -- to track forced vat=0
  ixTov     PLS_INTEGER;  -- to track divide by cMaxNalSum
  dUnq      DATE:= sysdate; -- to put down while shift ret-clients, 30.11.18

  aH       GENPACK.typArrC:= GENPACK.typArrC(); -- header attr
  aL       GENPACK.typArrC:= GENPACK.typArrC(); -- lines
  cTip    constant INTEGER:= 1;
  cDlvD   constant INTEGER:= 2;
  cFirI   constant INTEGER:= 3;
  cOrgI   constant INTEGER:= 4;
  cAdrI   constant INTEGER:= 5;
  cRcvI   constant INTEGER:= 6;
  cAgtI   constant INTEGER:= 7;
  cCred   constant INTEGER:= 8;
  cZakN   constant INTEGER:= 9;
  cZakD   constant INTEGER:= 10;
  cSklI   constant INTEGER:= 11;
  cGotI   constant INTEGER:= 12;
  cTxt    constant INTEGER:= 13;
  cNwAd   constant INTEGER:= 14;
  cLnsG   constant INTEGER:= 15;
  cSumG   constant INTEGER:= 16;
  cMins   constant INTEGER:= 17;
  cAuth   constant INTEGER:= 18;  -- optional
  cDCli   constant INTEGER:= 19;  -- optional
  cNomD   constant INTEGER:= 20;  -- optional
  cAgEx   constant INTEGER:= 21;  -- optional
  cAllH   constant INTEGER:= 17;

  procedure SET_DATE_PLAT  --   -------------------------------------
   is
    nDays   INTEGER;    iBankD INTEGER;    dTerm  DATE;  idAg INTEGER;
  begin
    -- 28.02.08, credit is wanted so check agreement date
    --    and block if DATE < sysdate + 30
    select nvl(TERM,0),bitand(STATUS,CST.cOrgBankD)+0,DATEOKD, CAGENT
      into nDays, iBankD, dTerm, idAg
    from KATORG where ID= idOrg;
    IF dTerm < trunc(sysdate) + 60 THEN
      warn:= 'До истечения договора клиента < 60 дней. ';  --iCred:= 0;
      IF trunc(sysdate) + 1 >= dTerm THEN  -- 09.07.19, uncomm JSok,01.11.19
        iCred:= 0; nDays:= 0;
        warn:= 'договор истек, за наличные';
      END IF;
    END IF;
    -- cancelled on 20.11.12
    select DATEDMO into dTerm from KATSOTR where ID= idAg;
    IF dTerm is null or dTerm < trunc(sysdate) + 40 THEN
      warn:= 'До истечения договора МО торгпреда < 40 дней. '; --iCred:= 0;
    ELSIF dTerm < trunc(sysdate) + 60 THEN
      warn:= 'До истечения договора МО торгпреда < 60 дней!'||chr(10);
    END IF;
    -- set date to pay for all docs to be generated
    -- 31.07.07, include bank days option
    --IF iCred = 0 THEN     dtPlat:= dtNak + 1;   -- 30.03.16, off 25.10.17
    IF iBankD > 0 THEN dtPlat:= GENPACK.GET_BANK_DATE(nvl(dtCli,dtNak), nDays);
    ELSE               dtPlat:= nvl(dtCli,dtNak) + nDays;
    END IF;
  end;  -- SET_DATE_PLAT

  procedure FIND_TRIP_AND_NOM    --   ---------------------------
   is
    idRoute  INTEGER:= CST.cRoutSam;
    docNom0  VARCHAR2(20);
    dNak0    DATE;
    flg0     INTEGER;
    ix0      INTEGER;
  begin
    IF dtNak < trunc(sysdate)+1 THEN  -- do not assign trip, 18.09.18
      select CDRIVER into idRoute from ORG_ADR where ID= idAdr;
      IF idRoute != 97 THEN  -- exclude client-samovyv, 04.10.18
        idTrip:= null;
        return;
      END IF;
    END IF;
    IF bDeliv and bAttToExistDoc THEN
      -- find existing doc with the same (org,adr) in the next days
      select max(CTRIP) keep (dense_rank first order by DNAKL),
        max(NNAKL) keep (dense_rank first order by trunc(DNAKL)), min(DNAKL),
        max(VKL_KN) keep (dense_rank first order by trunc(DNAKL))
      into  idTrip, docNom0, dNak0, flg0
      from BASEDOC where DNAKL between trunc(sysdate)+1 and trunc(sysdate)+6
        and TIPSOPR= iTip and BILL is null
        --and bitand(VKL_KN,CST.cBitKK + CST.cBitResv)=0  -- noKK, noReserve
        and bitand(VKL_KN, CST.cBitResv)=0  -- noReserve
        and CORG= idOrg and ADR_DOST= idAdr;
      IF idTrip > 0 and bitand(flg0,CST.cBitKK)=0 THEN
        -- found, so set 'docNom' after docNom0
        ix0:= regexp_instr(docNom0,'-\d+$');
        IF ix0 > 0 THEN -- increment suffix
          docNom:= substr(docNom0,1,ix0)||(to_number(substr(docNom0,ix0+1))+1);
        ELSE
          docNom:= docNom0||'-1';
        END IF;
        dtNak:= dNak0;  -- fix 19.08.14
        return; -- idTrip,dtNak,docNom are set
      ELSIF idTrip > 0 THEN
        -- next doc found, but with KK, 9.10.14
        dtNak:= dNak0;
        return;
      END IF;
      -- override user's will, 9.10.14
      -- remove the bar, 23.12.14
      --dtNak:= sysdate + 1;
    END IF;
    IF bDeliv THEN
      select nvl(CDRIVER,decode(iTip,1,idRoute,21)) into idRoute
      from ORG_ADR where ID= idAdr;
      BDOC.PICK_NEW_TRIP(idTrip, dtNak, idRoute); -- only if bDeliv, 22.11.18
    END IF;
  exception
    WHEN no_data_found THEN
      msg:= 'MAKE_ORDER2: неверный адрес доставки';  raise exErr;
  end;  --  FIND_TRIP_AND_NOM

  procedure LEVER_SUBST is
    -- substitute main items with promo if possible, 09.02.19
    -- params: idOrg, idAdr, BDOC.m_arLines
    -- temporary code
    nn   PLS_INTEGER;  idTv  PLS_INTEGER;  nSubst PLS_INTEGER:= 0;
    nOst PLS_INTEGER;
  begin
    IF iTip != 2 THEN return; END IF; -- 14.02.19
    IF idOrg = 837 or idOrg = 20408 THEN return; END IF; -- SokJa, 16.08.19
    -- stop if address is lever-controlled
    select count(*) into nn
    from ORG_AGENT_EX a, KATSOTR e
    where a.CAGENT=e.ID and trunc(e.EFLAGS/2)=1 and e.VID > 0 and a.CADR= idAdr;
    IF nn > 0 THEN return; END IF;
    -- stop if org is net
    select VID into nn from KATORG where ID= idOrg;
    IF nn = 1 THEN return; END IF;
    FOR i IN 1..BDOC.m_arLines.count LOOP
      idTv:= BDOC.m_arLines(i).CMC;  nK:= BDOC.m_arLines(i).KOL;
      select CPARENT_GR into nn from KATMC_EXT where CMC= idTv and CIERARH=1;
      IF nn != 201883 THEN continue; END IF;
      -- stop if tov is in promo list
      select max(c.ID), max(o.OST_FREE) keep (dense_rank last order by c.ID)
        into nn, nOst
      from OST_TOV o, KATMC c
      where c.CROOT= idTv and idTv not in (
        -- 27.02.19, -20 in price promo
         300251, 246525, 274169, 241564, 19001569, 237642, 239437, 238103, 233504,
        221633, 18003050, 18003029, 18003039, 18003051, 18003045, 18003058, 18003035,
        18003030, 18003040, 18003052, 18003059, 295614, 307496, 212008, 18003054,
        18003055, 18003032, 18003042, 18003061, 18003060, 18003062, 18003031, 18003041,
        289668, 18003053, 18003064, 18003063, 235786, 223545, 235846, 18003046, 260372,
        304167, 18003049, 18003028, 18003038, 18003048, 18003044, 18003057, 18003034,
        18003027, 18003037, 18003047, 18003043, 18003056, 18003033, 18003026, 18003036,
        245769, 283858,
        18005549,225847,280841,263005,278562,255073,255075, -- 16.08.19
        18006616,18006617,276683,276441)
      --  300251, 246525, 274169, 241564, 237642, 239437, 238103, 233504,
      --  221633, 295614, 307496, 212008, 289668, 235786, 223545, 235846,
      --  260372, 304167, 245769, 283858, 306130, 18001763, 18002402,
      --  18000431, 294534, 18000433, 294533)
        and c.ID= o.CMC and o.CSKLAD= CST.cMainSkl and c.NOMENKL like 'акц%';
      IF nn > 0 and nOst > nK THEN -- found relevant promo item idTv -> nn
        -- check if already exists line with promo CMC=nn, 09.03.19
        FOR j IN 1..BDOC.m_arLines.count LOOP
          IF BDOC.m_arLines(j).CMC = nn THEN
            IF BDOC.m_arLines(j).KOL + nK <= nOst THEN -- sufficient ost
              BDOC.m_arLines(j).KOL:= BDOC.m_arLines(j).KOL + nK;
              BDOC.m_arLines(i).KOL:= 0;
              nSubst:= nSubst + 1;
            END IF;
            nn:= 0;
            exit;
          END IF;
        END LOOP;
        IF nn > 0 and -- only if price is less or eq, 15.08.19
          BDOC.GET_PRICE_CLIENT(nn) <= BDOC.GET_PRICE_CLIENT(BDOC.m_arLines(i).CMC)
            THEN
          BDOC.m_arLines(i).CMC:= nn; -- replace with promo
          nSubst:= nSubst + 1;
        END IF;
      END IF;
    END LOOP;
    IF nSubst > 0 THEN logm:= ' lever-subst='||nSubst; END IF;
  end;  --  LEVER_SUBST

  procedure STORE_DEFICIT_ONLY is
    -- empty bill + linked deficit lines, 04.09.18
  begin
    idNewDoc:= 0;
    idNewDoc:= NEXT_KEY('BASEDOC',CST.DY(dtNak),msg); -- 05.01.18
    IF idNewDoc is null THEN raise exErr; END IF;
    insert into BASEDOC(ID, TIPSOPR,PRINTED,VKL_KN,DNAKL,DPLAT,DCLIENT, NNAKL,
      SUMMA,NDS,SKIDKA, CFIRM,CORG,CGRUZFROM,CGRUZTO, CPODRFROM,CPODRTO,
      CMYBANK,CBANK,  CREDIT,CAGENT,CHECKER, DOVER,ADR_DOST,CTRIP,
      DKK,DZAKAZ,NZAKAZ, CPICKER, BILL)
    values(idNewDoc, iTip,0,iVkl, dtNak,dtPlat,dtCli, 'дефиц',
      0,0,0,  idFirm,idOrg,idFirm,idRecv, idSkl, 1,
      idFirmB,idOrgB,  iCred,idAgent, CST.cSamoVyv, aH(cTxt),idAdr, null,
      CST.DatNull, nvl(dtZak,sysdate),aH(cZakN), idAgEx, 1);
    IF aDefic.count > 0 and idNewDoc > 0 THEN  -- store deficit, 25.10.17
      FOR i IN 1..aDefic.count LOOP
        insert into CLIENT_DEFICIT(ID_DOC,IERR,CMC,KOL_SHORT,PRICE)
        values(idNewDoc,i, aDefic(i).CMC, aDefic(i).KOL, aDefic(i).PRICE); -- 12.01.12
      END LOOP;
      aDefic.delete;
    END IF;
  end;  --  STORE_DEFICIT_ONLY

  procedure MAKE_NEXT_DOC(arr typARec, bVat0 PLS_INTEGER) --------------
   is
    -- use tovs in arr starting with outer ixTov to make doc,  23.10.17
    -- ost already moved if needed
    -- output: idNewDoc, nLines
    docNo   BASEDOC.NNAKL%TYPE;
    idFrm   BASEDOC.CFIRM%TYPE:= idFirm;
    rSumT   BASEDOC.SUMMA%TYPE:= 0;
    bDiv    BOOLEAN:= (iTip = 2 and iCred != CST.cCrBank and iCred != CST.cCrPreP);
    iPrTyp  PLS_INTEGER:= 0;
    rPrc0   SPDOC.PRC0%TYPE;
    iFlAdd  PLS_INTEGER:= 0;
    i       PLS_INTEGER:= ixTov;
  begin
    idNewDoc:= 0;
    nLines:= 0; IF ixTov > arr.count THEN  return; END IF;
    IF bVat0 > 0 THEN
      idFrm:= idFirm0;
      iFlAdd:= CST.cBitVat0;
    END IF;
    FOR rc IN ( select b.ID from KATORG k, ORG_BANK b
                where k.ID=b.CORG(+) and k.ID= idFrm and b.CBANK > 0
                  and b.DEF < 4
                order by b.DEF desc,b.ID desc) LOOP
      idFirmB:= rc.ID;   exit;
    END LOOP;
    idNewDoc:= NEXT_KEY('BASEDOC',CST.DY(dtNak),msg); -- 05.01.18
    IF idNewDoc is null THEN raise exErr; END IF;
    IF docNom is null THEN
      -- extra numbering, 19.07.16
      -- use function, 31.12.17
      docNo:= NEXT_NOM_DOC(idFrm, iTip, CST.DY(dtNak));
      IF docNo is null THEN
        msg:= 'MAKE_ORDER2: блокирован выбор номера док-та'; raise exErr;
      END IF;
    ELSE
      docNo:= docNom;
    END IF;
    insert into BASEDOC(ID, TIPSOPR,PRINTED,VKL_KN,DNAKL,DPLAT,DCLIENT, NNAKL,
      SUMMA,NDS,SKIDKA, CFIRM,CORG,CGRUZFROM,CGRUZTO, CPODRFROM,CPODRTO,
      CMYBANK,CBANK,  CREDIT,CAGENT,CHECKER, DOVER,ADR_DOST,CTRIP,
      DKK,DZAKAZ,NZAKAZ, CPICKER, BILL)
    values(idNewDoc, iTip,0,iVkl+iFlAdd, dtNak,dtPlat,dtCli, docNo,
      0,0,0,  idFrm,idOrg,idFrm,idRecv, idSkl, 1,
      idFirmB,idOrgB,  iCred,idAgent, CST.cSamoVyv, aH(cTxt),idAdr, idTrip,
      CST.DatNull, nvl(dtZak,sysdate),aH(cZakN), idAgEx, iBill);
    WHILE i <= arr.count LOOP
      IF i > ixTov and ((bDiv and rSumT + arr(i).SUMM > cMaxNalSum)
            or i > ixTov+CST.cMaxDocLn) THEN  -- 05.01.18
        ixTov:= i; exit;
      END IF;
      iPrTyp:= BDOC.GET_PRICE_TYPE_CLIENT(arr(i).CMC); -- 10.03.19
      rPrc0:= case when bitand(iPrTyp,32+64+128) > 0 then  -- 10.03.19
            BDOC.GET_PRICE_CLIENT_A(arr(i).CMC) else null end;
      -- mark SPDOC.FLG= +8 if term-promo, 30.08.18
      -- BDOC.INIT was called before, so use GET_PRICE_CLIENT vs. GET_PRICE_CLIENT_A
      insert into SPDOC(ID,CPARENT,CMC,KOL,PRICE, PROC_NDS, SUMMA,
        CCUSTOMS, CSHEET, ORGCMC,FLG,PRC0)
      values( idNewDoc*1000+(i-ixTov+1), idNewDoc, arr(i).CMC, arr(i).KOL,
        arr(i).PRICE, arr(i).PVAT, arr(i).SUMM, arr(i).GTD, null, arr(i).ORKOD,
        case when bitand(iPrTyp,32+64) > 0 then 8 when bitand(iPrTyp,128)>0 then 4
           else null end, rPrc0);
      nLines:= nLines + SQL%ROWCOUNT;
      rSumT:= rSumT + arr(i).SUMM;
      IF iBill is null THEN  -- ost to adjust, moved here from below, 26.12.18
        nK:= BDOC.MOVE_OST(arr(i).CMC, idNewDoc, idSkl,
          case when mod(iTip,2)=1 then arr(i).KOL else -arr(i).KOL end,
          'MAKE_ORDER2');
      END IF;
      i:= i + 1;
    END LOOP;
    IF i > arr.count THEN ixTov:= i; END IF;
    -- avg.line price, 25.06.12
    IF nLines > 0 THEN
      insert into BASEDOC_EX(ID,CAUTHOR,DAUTHOR, LINES,LINES_GEN,SUM_GEN,HOW_GOT,
                              LINE_PRC, WORK_MIN,CAGENT_ALT)
      values(idNewDoc, idAuth,sysdate, nLines, nGenLines, rGenSum, iHowGot,
            rSumT/nLines, nWorkMin,aH(cAgtI));
      BDOC.UPDATE_SUMS(idNewDoc,0); -- no commit
      IF iBill is null THEN
        declare  mes VARCHAR2(80);  idDoc  INTEGER;
        begin
          BONUS.PROCESS_SHIP(idNewDoc, mes);
          msg:= msg||' id='||idNewDoc||' (строк '||nLines||', ='||rSumT||')'||
            (case when mes is null then '' else ', '||replace(mes,'@','+') end);
          IF idNewDoc > 0 and bBonusAsNa and instr(mes,'@бонус,id=')=1 THEN  -- 18.08.15
            idDoc:= regexp_substr(mes,'\d+',11);  -- 19.10.15
            SWITCH_NAKL(idDoc, 1+2+4, mes); -- make/set_trip/only_if_noKK
          END IF;
          BDOC_OP.SHIFT_RETCLI(idNewDoc, trunc(dUnq),dUnq); -- 30.11.18
        end;
      END IF; -- iBill= null
    END IF;
    IF aDefic.count > 0 and idNewDoc > 0 THEN  -- store deficit, 25.10.17
      FOR i IN 1..aDefic.count LOOP
        insert into CLIENT_DEFICIT(ID_DOC,IERR,CMC,KOL_SHORT,PRICE)
        values(idNewDoc,i, aDefic(i).CMC, aDefic(i).KOL, aDefic(i).PRICE); -- 12.01.12
      END LOOP;
      aDefic.delete;
    END IF;
    -- add dual-sale flag if needed, 22.11.17
    update KATORG set STATUS= STATUS + CST.cOrgDualS
    where ID= idOrg and bitand(STATUS,CST.cOrgDualS)= 0 and OUR_FIRM != idFrm;
    IF logm is not null THEN -- 09.02.19
      delete from PROC_LOGS where PNAME='MAKE_ORDER2' and DSTAMP < sysdate-10;
      insert into PROC_LOGS(DSTAMP,PNAME,TXT)
      values(sysdate,'MAKE_ORDER2',idNewDoc||': dn='||CST.D2M(dtNak)||
        ' corg='||idOrg||logm);
    END IF;
  end;  --  MAKE_NEXT_DOC

begin     --  main body   ---------------------------------------------------
  GENPACK.SPLIT(aH, sHdr, '^');
  GENPACK.SPLIT(aL, sLines, '^');
  IF aH.count < cAllH THEN
    msg:= 'MAKE_ORDER2: недостаточно параметров в заголовке заказа: '||aH.count
        ||' < '||cAllH;
    return;
  END IF;
  -- 1. set doc attibutes
  IF aH(cTip) > 0 THEN  iTip:= aH(cTip); END IF;
  dtNak:= to_date(aH(cDlvD),CST.cFmtD4) + (sysdate-trunc(sysdate));
  IF dtNak is null THEN msg:= 'MAKE_ORDER2: неверная дата накл.:'||aH(cDlvD);
    return;
  END IF;
  IF dtNak < trunc(sysdate) THEN
    msg:= 'MAKE_ORDER2: дата доставки в прошлом'; return;
  END IF;
  dtPlat:= dtNak;
  idFirm:= aH(cFirI);
  -- check for validness idOrg,idAdr, 08.02.18
  IF aH(cOrgI) is not null and regexp_instr(aH(cOrgI),'^\d+$') = 0 THEN
    msg:= 'MAKE_ORDER2: invalid idOrg - '||aH(cOrgI); return;
  END IF;
  idOrg:=  aH(cOrgI);
  IF aH(cAdrI) is not null and regexp_instr(aH(cAdrI),'^\d+$') = 0 THEN
    msg:= 'MAKE_ORDER2: invalid idAdr - '||aH(cAdrI); return;
  END IF;
  idAdr:= aH(cAdrI);
  IF iTip != 2 and iTip != 6 and iTip != 4 THEN
    iBill:= 1;
  END IF;
  IF idOrg is null THEN  select CORG into idOrg from ORG_ADR where ID= idAdr;
  END IF;
  IF idFirm is null THEN
    select nvl(OUR_FIRM, GENPACK.CONFIG_PAR(CST.cDefFirm)) into idFirm
    from KATORG k where ID= idOrg;
  END IF;
  idRecv:= nvl(aH(cRcvI), idOrg);  -- revert, Seredk, 01.02.19
  --idRecv:= idOrg;  -- 26.06.18 Ks.Degt
  iCred:= aH(cCred);
  IF regexp_instr(aH(cZakD),'\d\d?.\d\d.\d{4} \d\d:\d\d:\d\d$') = 1 THEN -- 03.08.17
    dtZak:= to_date(aH(cZakD), CST.cFmtD4T);
  ELSE
    dtZak:= to_date(aH(cZakD), CST.cFmtD4);
  END IF;
  IF aH(cSklI) > 0 THEN  idSkl:= aH(cSklI);  END IF; -- main by def.
  iHowGot:=  nvl(aH(cGotI), 0);
  nGenLines:= aH(cLnsG);
  begin
  rGenSum:=   replace(aH(cSumG),',','.');  -- 4.12.15
  exception
    when others then
      msg:= 'MAKE_ORDER2: conversion to float num=`'||aH(cSumG)||'`';  return;
  end;
  nWorkMin:=  aH(cMins);  -- how long order was in work, min
  IF aH.count >= cAuth and aH(cAuth) > 0 THEN  -- fix 19.11.15
    idAuth:= aH(cAuth);
  END IF;
  -- decode flags
  IF bitand(iFlags,cKolAsKor) = cKolAsKor THEN
    bKolAsKor:= true;
  END IF;
  IF aH.count >= cAgEx and aH(cAgEx) > 0 THEN
    select max(ID) into idAgEx from KATSOTR where ID= aH(cAgEx);
  END IF;
  IF idAgEx > 0 THEN  -- got him, 17.08.17
    null;
  ELSIF bitand(iFlags,cUseEmAsEAg) = cUseEmAsEAg THEN -- cur.user as ex-agent, 19.08.15
    idAgEx:= SSEC.AUTH_PATH.GET_CUR_USER_KATSOTR_ID;
  ELSE   -- sale-channel index:
    -- either given sale-channel index, or check on VTP for current user, 21.11.16
    idxEx:= bitand(iFlags,cExAgDiv*15)/cExAgDiv; --  =1 lever,2 colg,3 nefis,4 evyap
    IF idxEx = 0 THEN -- VTP-channel not given
      select bitand(max(EFLAGS),2*15)/2 into idxEx
      from KATSOTR  where ID= SSEC.AUTH_PATH.GET_CUR_USER_KATSOTR_ID;
    END IF;
    -- use idAuth as sec-agent when channel given in iFlags and idAuth > 0, 03.08.17
    IF idxEx > 0 and idAuth > 0 THEN  -- 03.08.17
      idAgEx:= idAuth;
    ELSIF idxEx > 0 THEN
      select max(s.ID) into idAgEx
      from KATSOTR s, ORG_AGENT_EX e
      where s.ID=e.CAGENT and e.CADR= idAdr and bitand(s.EFLAGS,2*15)= 2*idxEx;
    END IF;
  END IF;
  IF bitand(iFlags,cBonAsNak) >0  THEN bBonusAsNa:= true;      END IF;
  IF bitand(iFlags,cUseDefCre) >0 THEN bUseDefCre:= true;      END IF;
  IF bitand(iFlags,cUseDefSF) > 0 THEN bUseDefSFa:= true;      END IF;
  IF bitand(iFlags,cAttToExi) > 0 THEN bAttToExistDoc:= true;  END IF;
  IF bitand(iFlags,cAutoGen) > 0  THEN iVkl:= CST.cBitGD;     END IF;
  IF bitand(iFlags,cKor) > 0   THEN bKorOnly:= true;        END IF;
  IF bitand(iFlags,cResv) > 0  THEN iVkl:= iVkl + CST.cBitResv; END IF;
  IF bitand(iFlags,cSFact) > 0 THEN iVkl:= iVkl + CST.cBitSF;   END IF;
  IF bitand(iFlags,cSamVyv) > 0  THEN  --samovyv
    select nvl(max(ID),0) into idAdr
    from ORG_ADR where CORG=idFirm and DEFAULT_ADR=1;
    bDeliv:= false;
  END IF;
  IF aH.count >= cDCli and aH(cDCli) is not null THEN  -- 14.03.16
    dtCli:= to_date(aH(cDCli),CST.cFmtD4);
  END IF;
  IF aH.count >= cNomD and aH(cNomD) is not null THEN  -- 14.03.16
    docNom:= aH(cNomD);
  END IF;
  -- set agent for doc
  DECLARE  iSF  INTEGER; iCre INTEGER; iNoExp INTEGER; iRealz INTEGER;
      -- anonym->noExpo 29.11.16, 30.11.16
      -- iSF != cBitSF implies cBitNoExp, cOrgNoExpo acts implicitly, 19.07.17
      -- iRealz on leads to NoExp, 02.08.17
  BEGIN
    select CAGENT, decode(bitand(STATUS,CST.cOrgSFact), 0,0, CST.cBitSF),
        bitand(STATUS,CST.cOrgRealiz), trunc(bitand(PRLIST,255)/16)
      into idAgent, iSF, iRealz, iCre
    from KATORG where ID= idOrg;
    --  and -> or , 30.08.17
    iNoExp:= case when iSF = CST.cBitSF or iRealz=0 then 0
                else CST.cBitNoExp end; -- 19.07.17
    iVkl:= iVkl - bitand(iVkl,CST.cBitSF+CST.cBitNoExp) + iSF + iNoExp;
    IF bUseDefCre THEN  iCred:= iCre;
    END IF;
    IF idOrg = CST.cSotOrg THEN  -- 18.08.15 for sotr: current user as agent
      idAgent:= SSEC.AUTH_PATH.GET_CUR_USER_KATSOTR_ID;
    END IF;
    -- force return-docs to have DPLAT= DNAKL, 14.11.16
    IF iTip=3 or iTip=4 THEN
      iCred:= 0;
    END IF;
  END;
  nLines:= trunc(aL.count/3);
  IF iTip = 2 and iCred != CST.cCrBank THEN
    DBMS_OUTPUT.put_line('lines submitted: '||nLines||' ('||aL.count||')');
    FOR i IN 1..nLines LOOP   -- 18.06.15
      IF aL(3*i-1) * aL(3*i) > cMaxNalSum THEN
        msg:= 'MAKE_ORDER2: строка '||i||': сумма '||(aL(3*i-1) * aL(3*i))||
          ' больше '||cMaxNalSum;
        raise exErr;
      END IF;
    END LOOP;
  END IF;
  -- set banks
    -- 30.04.08 only banks (CBANK>0)
    -- 10.12.15  ORG_BANK.DEF=4 invalid
    --          idFirmB is in MAKE_NEXT_DOC()
    FOR rc IN (select b.ID from KATORG k, ORG_BANK b
              where k.ID=b.CORG(+) and k.ID= idOrg and b.DEF < 4
              order by b.DEF desc,b.ID desc)  LOOP
      idOrgB:= rc.ID;  exit;
    END LOOP;
  -- CST.cBitDlvM not used since 1.09.15
  --# IF bitand(iVkl,CST.cBitResv) = 0 THEN -- set starter deliv mark, 5.11.09
    --# iVkl:= iVkl + CST.cBitDlvM;
  --# END IF;
  IF nvl(idAdr,0) = 0 THEN  -- delivery addr, on samovyv it's already set
    select max(ID) keep (dense_rank first
      order by DEFAULT_ADR desc nulls last,ID desc) into idAdr
    from ORG_ADR
    where CORG=idOrg and nvl(DEFAULT_ADR,0) != 2;
  END IF;
  FOR rc IN (select ID from ORG_ADR where ID= idAdr and bitand(AFLG,1)=1) LOOP
    bAttToExistDoc:= false;  -- 09.02.18
  END LOOP;
  IF iTip = 2 or iTip = 6 THEN  -- 19.07.18
    FIND_TRIP_AND_NOM;  --  idAdr, dtNak => idTrip [docNom]
  END IF;
  IF 0 < iCred and iCred < CST.cCrResv THEN -- 13.03.12
    SET_DATE_PLAT;
  END IF;
  -- 2. prepare/check lines before save
  IF idNewDoc = -2 THEN msg:= '1';  -- not to clear lines in INIT()
  END IF;
  BDOC.INIT(idOrg,dtNak, msg, idAdr);
  -- prev.call sets BDOC.m_iOrgFl= VID+100*STATUS also,  11.07.17
  IF msg != '@' THEN   raise exErr;  END IF;
  FOR i IN 1..nLines  LOOP
    idMC:= aL(3*i-2);
    begin  -- catch lines with invalid tovar id
    -- check if item is for VP only, 6.06.13
    -- allow items for VP if nefis-channel, 25.08.15
    select regexp_instr(NOMENKL,'^дляВП') into nK from KATMC where ID= idMC;
    IF nK is null or nK = 0 or idxEx = 3 THEN  -- not found, or nefis-channel
      rPrc:= nvl(aL(3*i), 0);
      IF iTip = 1 and aL(3*i-1) > 0 THEN -- for in-docs, 22.01.16
        rPrc:= aL(3*i-1) * rPrc; msg:= '1';
      ELSIF iHowGot = CST.cByKntur THEN -- no restriction, 15.02.18
        msg:= '1';
      END IF;
      BDOC.STORE_LINE(idMC, aL(3*i-1), rPrc, msg);
      IF substr(msg,1,1) != '@' THEN raise exErr;  END IF;
    END IF;
    exception
      when no_data_found then
        msg:= 'MAKE_ORDER2: строка '||i||', нет товара с id='||idMC; raise exErr;
    end;
  END LOOP;
  LEVER_SUBST; -- 09.02.19

  msg:= ''; -- not Ok
  -- 3. create doc header
  --    we do not create header here but postpone till process tovs list, 23.10.17
  IF idFirm != idFirm0 THEN  -- pets meal via Mercado only, 30.07.19
    FOR i IN 1..BDOC.m_arLines.count LOOP
      select count(*) into nK from KATMC
      where ID= BDOC.m_arLines(i).CMC and NAME like 'корм %';
      IF nK > 0 THEN
        msg:= 'MAKE_ORDER2: корм для животных выписывается от Меркадо'; raise exErr;
      END IF;
    END LOOP;
  END IF;

  -- 4. pick from free ost, rest to demand in (idMC,nKolO)
  -- store modified reserve-doc IDs in aResDoc() to update them
  nLines:= 0; -- enumerate actually inserted
  FOR iT IN 1..BDOC.m_arLines.count  LOOP
    lnRec:= BDOC.m_arLines(iT);
    idMC:= lnRec.CMC;
    iTovFlg:= 0;  -- 23.10.17
    IF iTip = 2 and lnRec.KOL > 0 -- 25.03.16  bound sale KOL if set max
    THEN
      declare nKolMx INTEGER; rPrc NUMBER(12,2);
      begin
        select KOL_MAX,FLAGS into nKolMx,iTovFlg from KATMC where ID= idMC;
        IF mod(BDOC.m_iOrgFl,10) != 1 and nKolMx < lnRec.KOL THEN
          -- drop restriction for nets, 11.07.17
          rPrc:= lnRec.SUMM / lnRec.KOL;
          lnRec.KOL:= nKolMx;
          lnRec.SUMM:= nKolMx * rPrc;
        END IF;
      end;
    END IF;
    --IF bitand(iTovFlg,CST.cTov0VAT) > 0 and idFirm != idFirm0 THEN
    --  -- drop tov if we do not sell from proper firm, 31.10.17
    --  lnRec.KOL:= 0;
    --  lnRec.SUMM:= 0;
    --END IF;
    DIRS.TOV_ASSIGN_MISSED_SECTION(idMC);  -- 9.10.12
    IF iBill is null THEN  -- only output here
      nKolO:= lnRec.KOL;  -- ost to demand
      nKolD:= 0;                       -- in doc
      IF nKolO > 0 THEN
        FOR rc IN (select OST_FREE from OST_TOV where CMC=idMC and CSKLAD=idSkl
                    and OST_FREE > 0 for update) LOOP
          nK:= least(rc.OST_FREE, nKolO);
          IF bKorOnly and lnRec.PACK > 0 THEN
            nK:= trunc(nK/lnRec.PACK) * lnRec.PACK;
          END IF;
          nKolD:= nK;
          nKolO:= nKolO - nK;
        END LOOP;
        --IF nKolD > 0 THEN -- we took KOL > 0 to doc, so fix free ost
        --  nK:= BDOC.MOVE_OST(idMC,idNewDoc,idSkl, -nKolD,'MAKE_ORDER2');
        --END IF;
      END IF;
      -- 4.1 if exists KOL to demand from reserve-docs
      IF (nKolO >= lnRec.PACK and bKorOnly)
              or (nKolO > 0 and not bKorOnly)  THEN
        FOR rc IN (select a.KOL, b.ID, a.ID IDL from SPDOC a, BASEDOC b
                  where a.CPARENT=b.ID and b.CORG=idOrg and a.CMC=idMC
                    and DNAKL > sysdate and BILL is null  and TIPSOPR=2
                    and bitand(VKL_KN, CST.cBitResv)= CST.cBitResv
                    and CPODRFROM= idSkl and a.KOL > 0 ) LOOP
          nK:= least(rc.KOL, nKolO);
          IF bKorOnly and lnRec.PACK > 0 THEN
            nK:= trunc(nK/lnRec.PACK) * lnRec.PACK;
          END IF;
          nKolD:= nKolD + nK;
          nKolO:= nKolO - nK;
          IF nK > 0 THEN -- cut reserve-doc, add sum update 17.03.10
            update SPDOC set KOL= KOL - nK, SUMMA=(KOL-nK)*PRICE
            where ID= rc.IDL;
            IF not aResDoc.exists(rc.ID) THEN aResDoc(rc.ID):= 1;  END IF;
          END IF;
        END LOOP;
      END IF;
      -- 4.2 store deficit if any
      IF bKorOnly THEN
        nKolO:= trunc(nKolO/lnRec.PACK) * lnRec.PACK;
      END IF;
      IF nKolO > 0 THEN
        iDefic:= iDefic + 1;
        aDefic(iDefic).CMC:= idMC;  -- treat deficit with aDefic(), 25.10.17
        aDefic(iDefic).KOL:= nKolO;
        aDefic(iDefic).PRICE:= lnRec.SUMM/lnRec.KOL;
      END IF;
    ELSE  -- bill, in-doc
      nKolD:= case when bKolAsKor and lnRec.PACK > 0 then lnRec.PACK
              else 1 end * lnRec.KOL;
    END IF;
    IF nKolD > 0 THEN  -- store line
      declare sOrgKod SPDOC.ORGCMC%TYPE; -- 13.06.17
              rSum  SPDOC.SUMMA%TYPE; -- 23.10.17
      begin
      IF iHowGot = CST.cByKntur THEN  -- 13.06.17
        select max(KOD) into sOrgKod from ORG_TOV where CMC= idMC and CORG= idOrg;
      END IF;
      rPrc:= case when iTip=1 then
            round(lnRec.SUMM/(lnRec.KOL * case when bKolAsKor and lnRec.PACK > 0
                then lnRec.PACK else 1 end),3)
            else round(lnRec.SUMM/lnRec.KOL,2) end;
      -- instead of insert to SPDOC use aTovs/aTovs0, 23.10.17
      rSum:= case when iTip=1 then lnRec.SUMM else nKolD*rPrc end;
      IF bitand(iTovFlg, CST.cTov0VAT) > 0 THEN
        nTovs0:= nTovs0 + 1;
        aTovs0(nTovs0).CMC:= idMC;
        aTovs0(nTovs0).KOL:= nKolD;
        aTovs0(nTovs0).PRICE:= rPrc;
        aTovs0(nTovs0).PVAT:= 0;
        aTovs0(nTovs0).SUMM:= rSum;
        aTovs0(nTovs0).GTD:= lnRec.GTD;
        aTovs0(nTovs0).ORKOD:= sOrgKod;
      ELSE
        nTovs:= nTovs + 1;
        aTovs(nTovs).CMC:= idMC;
        aTovs(nTovs).KOL:= nKolD;
        aTovs(nTovs).PRICE:= rPrc;
        aTovs(nTovs).PVAT:= lnRec.VAT;
        aTovs(nTovs).SUMM:= rSum;
        aTovs(nTovs).GTD:= lnRec.GTD;
        aTovs(nTovs).ORKOD:= sOrgKod;
      END IF;
      end;
    END IF;
    DBMS_OUTPUT.put_line('iT= '|| iT || ', cmc=' || idMC||', kol0='||lnRec.KOL||
      ', kol='||nKolD||', leftToTake='||nKolO||', prc='||rPrc||
      ' iDefic='||iDefic||' flg='||to_char(iTovFlg,'xxxxxxx'));
  END LOOP; -- iT
  -- do NOT generate tovsheets here, let pickers do it
  --BDOC.MAKE_TOVSHEETS(idNewDoc, sMsg);

  DBMS_OUTPUT.put_line('nTovs= '||nTovs||', nTovs0= '||nTovs0);
  -- if idFirm != idFirm0, add VAT to aTovs0[], 04.11.17
  IF idFirm != idFirm0 THEN
    FOR i IN 1..aTovs0.count LOOP
      nTovs:= nTovs + 1;
      aTovs(nTovs).CMC:= aTovs0(i).CMC;
      aTovs(nTovs).KOL:= aTovs0(i).KOL;
      aTovs(nTovs).PRICE:= round(aTovs0(i).PRICE * (100+CST.cDefVAT)/100, 2);
      aTovs(nTovs).PVAT:= CST.cDefVAT;
      aTovs(nTovs).SUMM:= aTovs(nTovs).KOL* aTovs(nTovs).PRICE;
      aTovs(nTovs).GTD:=   aTovs0(i).GTD;
      aTovs(nTovs).ORKOD:= aTovs0(i).ORKOD;
    END LOOP;
    aTovs0.delete;
  END IF;
  -- make docs after aTovs, aTovs0, 24.10.17
  -- 1st doc made contains deficit storing, 25.10.17
  ixTov:= 1;
  WHILE ixTov <= aTovs.count LOOP
    MAKE_NEXT_DOC(aTovs, 0); -- ixTov is modified inside
  END LOOP;
  ixTov:= 1;
  WHILE ixTov <= aTovs0.count LOOP
    MAKE_NEXT_DOC(aTovs0, 1); -- ixTov is modified inside
  END LOOP;
  IF aTovs.count + aTovs0.count = 0 and aDefic.count > 0 THEN
    -- only deficit should be saved, 04.09.18
    STORE_DEFICIT_ONLY;
    iBill:= 1;
  END IF;
  IF iBill is null THEN
    declare  idDoc  INTEGER; begin
      idDoc:= aResDoc.first;
      WHILE idDoc > 0 LOOP  -- reserve-docs changed
        BDOC.UPDATE_SUMS(idDoc,0);
        DBMS_OUTPUT.put_line('reserve-doc used: id='||idDoc);
        idDoc:= aResDoc.next(idDoc);
      END LOOP;
    end;
    msg:= ' '||warn||' создана накладная '|| msg||', дефицит: '||iDefic;
  ELSE
    msg:= ' создан счет '|| msg;
  END IF;
  commit;
exception
  when exErr then
    rollback;
end;
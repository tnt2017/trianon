<?php
   // header('Content-Type: application/json; charset=windows-1251'); //utf-8 windows-1251 charset=utf-8
   header("Content-Type: text/html; charset=windows-1251");

	//phpinfo();
	require_once "orasql.php";
	require_once "utils.php";
    
    $par=sdecode($_REQUEST['par']);
    //echo $par[1];
    //echo $_REQUEST['set_zalist'];
    //echo "json";

    try 
	{
        $db= new CMyDb();
	    $par[0]='ROSTOA$NSK08';
	    $par[1]="345544";
	    $db->connect($par[0],$par[1] , "trn");
    	$arlg= explode("$", $par[0]);
	    //echo "BD CONNECT";

        $idGrp="";
        $idOrg="13857";
        $dat="07.11.2019";
        $idAdr="0";

    if(isset($_REQUEST[set_zalist])) ////// ОТМЕЧАЕМ ЗВЕЗДОЧКОЙ
    {
        //procedure SAVE_ENLISTED(idOrg INTEGER, idAdr INTEGER, dtOn DATE,
        //sLines VARCHAR2, msg out VARCHAR2, iDir INTEGER)
        echo "set_zalist";

        $idOrg=$_REQUEST[idOrg]; //1900036; // id организации
        $idAdr=0; // адрес точки

        $sLines=$_REQUEST[idTov] . "^4^0^^"; // 19003412 // id товара для теста лак
        $dtOn="08.11.2019";
        $iDir=0;

      $db->parse("begin $arlg[1].PRLIST.SAVE_ENLISTED(:idOrg,:idAdr,to_date(:dt,'dd.mm.yyyy'),:sLines,:msg,".
      ":iDir); end;");
        $db->bind(":idOrg", $idOrg, SQLT_INT);
        $db->bind(":idAdr", $idAdr, SQLT_INT);
        $db->bind(":dt", $dtOn, SQLT_CHR);
        $db->bind(":sLines", $sLines, SQLT_CHR);
        $db->bind(":msg", $msg, SQLT_CHR, 128);
        $db->bind(":iDir", $iDir, SQLT_INT);
        $db->execute();
       // $db->execute_cursor($cur);
        echo $msg;
    }


    if(isset($_REQUEST[set_razlist])) ////// ОТМЕЧАЕМ ЗВЕЗДОЧКОЙ
    {
        //procedure SAVE_ENLISTED(idOrg INTEGER, idAdr INTEGER, dtOn DATE,
        //sLines VARCHAR2, msg out VARCHAR2, iDir INTEGER)
        echo "set_razlist";

        $idOrg=$_REQUEST[idOrg]; //1900036; // id организации
        $idAdr=0; // адрес точки

        $sLines=$_REQUEST[idTov] . "^0^0^^"; // 19003412 // id товара для теста лак
        $dtOn="08.11.2019";
        $iDir=0;

      $db->parse("begin $arlg[1].PRLIST.SAVE_ENLISTED(:idOrg,:idAdr,to_date(:dt,'dd.mm.yyyy'),:sLines,:msg,".
      ":iDir); end;");
        $db->bind(":idOrg", $idOrg, SQLT_INT);
        $db->bind(":idAdr", $idAdr, SQLT_INT);
        $db->bind(":dt", $dtOn, SQLT_CHR);
        $db->bind(":sLines", $sLines, SQLT_CHR);
        $db->bind(":msg", $msg, SQLT_CHR, 128);
        $db->bind(":iDir", $iDir, SQLT_INT);
        $db->execute();
       // $db->execute_cursor($cur);
        echo $msg;
    }


    if(isset($_REQUEST[set_waitlist])) ////// ДОБАВЛЯЕМ В ЛИСТ ОЖИДАНИЯ
    {
        //procedure SAVE_WAIT_LIST(idOrg INTEGER, idAdr INTEGER, tovs VARCHAR2, dtTill DATE,
        //iFlg INTEGER, msg out VARCHAR2)

        $idOrg=$_REQUEST[idOrg]; //1900036; // id организации
        $idAdr=0; // адрес точки
        $tovs=$_REQUEST[idTov] . "^" . $_REQUEST[KOL] .  "^";
        $dtTill=$_REQUEST[dtTill]; //"15.11.2019";
        $iFlg=1;        

        $db->parse("begin $arlg[1].BDOC_OP.SAVE_WAIT_LIST(:idOrg,:idAdr, :tovs,to_date(:dtTill,'dd.mm.yyyy'),".
        ":iFlg, :msg); end;");
          $db->bind(":idOrg", $idOrg, SQLT_INT);
          $db->bind(":idAdr", $idAdr, SQLT_INT);
          $db->bind(":tovs", $tovs, SQLT_CHR);
          $db->bind(":dtTill", $dtTill, SQLT_CHR);
          $db->bind(":iFlg", $iFlg, SQLT_INT);
          $db->bind(":msg", $msg, SQLT_CHR, 128);
          $db->execute();
          echo $msg;
    }

    if(isset($_REQUEST[get_waitlist]))
    {
        /// procedure WAIT_LIST(cur out typCurGen, idOrg INTEGER, idAdr INTEGER, idEmp INTEGER)

        $idOrg=$_REQUEST[idOrg];
        $idAdr=0; //$_REQUEST[idAdr];
        $idEmp=-1;

        $db->parse("begin $arlg[1].BDOC_OP.WAIT_LIST(:cur,:idOrg,:idAdr,:idEmp); end;");        
        $db->bind(":cur", $cur, OCI_B_CURSOR);
        $db->bind(":idOrg", $idOrg, SQLT_INT);
        $db->bind(":idAdr", $idAdr, SQLT_INT);
        $db->bind(":idEmp", $idEmp, SQLT_INT);

        $db->execute();
        $db->execute_cursor($cur);

        echo "[";
        while($row = $db->fetch_cursor($cur))
        {
            $waitlist[] = $row;
            echo "{\"ID\": \"" . $row[CMC] . "\", ";
            echo "\"TOV\": \"" . $row[TOV] . "\", ";
            echo "\"KOL\": \"" . $row[KOL] . "\", ";
            echo "\"DTILL\": \"" . $row[DTILL] . "\"}, ";           
            
        }            
        echo "{\"\":\"\"}]";

        ///var_dump($waitlist);
    }



    if(isset($_REQUEST[get_dt_till]))
    {
        $str_ret="??/??/????";
        //$str_ret = mktime(0, 0, 0, date("m")-1, date("d"),   date("Y"));

        $idTov=$_REQUEST[idTov];
        $db->parse("begin $arlg[1].BDOC_OP.WAIT_ONE_TOV(:cur, :idTov ); end;");
        $db->bind(":cur", $cur, OCI_B_CURSOR);
        $db->bind(":idTov", $idTov, SQLT_INT);
        $db->execute();
        $db->execute_cursor($cur);
        while($row = $db->fetch_cursor($cur))
        {
            $str_ret = $row[D_MIN];
        }

        echo $idTov . ":" . $str_ret;
    }


    if(isset($_REQUEST[set_prc])) ////// мониторинг цен: цена конкурента/цена на полке
    {
        //procedure TOVREP_SAVE(idDoc out INTEGER, sData VARCHAR2, sLines VARCHAR2,
        //msg out VARCHAR2)

        $idDoc=0; // возвращаемый параметр

        //  -- sData='nom^corg^cadr^crival^txt^dtcli^dtil^tip^', dtcli,dtil=dd.mm.yyyy
        //  -- sLines='cmc1^kol1^ost1^prc1^prcr1^..', each line is supposed to be non-empty

        $sData="^" . $_REQUEST[idOrg] . "^" . $_REQUEST[idAdr] . "^" . $_REQUEST[idKonkur] . "^^^";

        if($_REQUEST[mon_type]=="1")
        $sLines=$_REQUEST[idTov] . "^^^" . $_REQUEST[price] . "^^";  // цена на полке

        if($_REQUEST[mon_type]=="2")
        $sLines=$_REQUEST[idTov] . "^^^^" . $_REQUEST[price];       // цена конкурента

        $db->parse("begin $arlg[1].REP_DOC.TOVREP_SAVE(:idDoc, :sData, :sLines, :msg); end;");
          $db->bind(":idDoc", $idDoc, SQLT_INT);
          $db->bind(":sData", $sData, SQLT_CHR);
          $db->bind(":sLines", $sLines, SQLT_CHR);
          $db->bind(":msg", $msg, SQLT_CHR, 128);
          $db->execute();
         // $db->execute_cursor($cur);
          echo $msg . "\r\n";

          echo "sData=" . $sData . "\r\n";
          echo "sLines=" . $sLines . "\r\n";    
    }



    if(isset($_REQUEST[set_prc_tovar])) 
    {
        //procedure SAVE_ORG_PRICE(idOrg INTEGER, idAddr INTEGER, idTov INTEGER, rPrc NUMBER,
        //rDisc NUMBER, dtOn DATE, iRet out INTEGER)

        $idOrg=$_REQUEST[idOrg];
        $idAdr=$_REQUEST[idAdr];
        $idTov=$_REQUEST[idTov];
        $dtOn="21.11.2019";

        $rPrc="";
        $rDisc=$_REQUEST[rDisc];
        $iRet=0;

        ///echo $idOrg . ":" .  $idAdr . ":" . $idTov . ":" . $rPrc . ":" . $rDisc . ":" . $dtOn;
        $db->parse("begin $arlg[1].PRLIST.SAVE_ORG_PRICE(:idOrg, :idAdr, :idTov, :rPrc, :rDisc, to_date(:dtOn,'dd.mm.yyyy'), :iRet ); end;");
        $db->bind(":idOrg", $idOrg, SQLT_INT);
        $db->bind(":idAdr", $idAdr, SQLT_INT);
        $db->bind(":idTov", $idTov, SQLT_INT);
        $db->bind(":rPrc", $rPrc, SQLT_CHR);
        $db->bind(":rDisc", $rDisc, SQLT_CHR);
        $db->bind(":dtOn", $dtOn, SQLT_CHR);
        $db->bind(":iRet", $iRet, SQLT_CHR);
        $db->execute();

        echo "iret=" . $iRet . " (0 no action, 1 updated, 2 inserted, 3 deleted)";
    }

    if(isset($_REQUEST[get_grp_disc])) ////// получить скидка на группу товаров!!!
    {
        $idOrg=1900036; //$_REQUEST[idOrg];
        $idAdr=0; // $_REQUEST[idAdr];
        $idGrp=235085; //$_REQUEST[idGrp];

        if(isset($_REQUEST[idOrg]))
        $idOrg=$_REQUEST[idOrg];

        if(isset($_REQUEST[idAdr]))
        $idAdr=$_REQUEST[idAdr];

        if(isset($_REQUEST[idGrp]))
        $idGrp=$_REQUEST[idGrp];

        $db->parse("begin $arlg[1].PRLIST.GRP_DISC_HIST(:cur, :idOrg, :idAdr, :idGrp); end;");
        $db->bind(":cur", $cur, OCI_B_CURSOR);

        $db->bind(":idOrg", $idOrg, SQLT_INT);
	    $db->bind(":idAdr", $idAdr, SQLT_INT);
        $db->bind(":idGrp", $idGrp, SQLT_INT);
	    $db->execute();
 
        $db->execute();
        $db->execute_cursor($cur);
        while($row = $db->fetch_cursor($cur))
        {
          $tovs[] = $row;
        }            
        //var_dump($tovs);

        if(count($tovs)>0)
        echo $tovs[0][DISC];
    }


    if(isset($_REQUEST[set_grp_disc])) ////// установить скидку на группу товаров!!!
    {
        // procedure SAVE_GRP_DISC(idOrg INTEGER, idAdr INTEGER, idGrp INTEGER,
        // dtOn DATE, rDisc NUMBER, msg in out VARCHAR2)

        $idOrg=$_REQUEST[idOrg];
        $idAdr=$_REQUEST[idAdr];
        $idGrp=$_REQUEST[idGrp];
        $dtOn="26.11.2019";
        $rDisc=$_REQUEST[rDisc];

        $db->parse("begin $arlg[1].PRLIST.SAVE_GRP_DISC(:idOrg, :idAdr, :idGrp, to_date(:dtOn,'dd.mm.yyyy'), :rDisc, :msg); end;");
        $db->bind(":idOrg", $idOrg, SQLT_INT);
        $db->bind(":idAdr", $idAdr, SQLT_INT);
        $db->bind(":idGrp", $idGrp, SQLT_INT);
        $db->bind(":dtOn", $dtOn, SQLT_CHR);
        $db->bind(":rDisc", $rDisc, SQLT_CHR);
        $db->bind(":msg", $msg, SQLT_CHR, 128);
        $db->execute();

        echo $idOrg . ":" .  $idAdr . ":" . $idGrp . ":" . $dtOn . ":" . $rDisc;
       // $db->execute_cursor($cur);
        echo $msg . "\r\n";
    }
    


    if(isset($_REQUEST[get_tovlist])) ///// список товаров
    {
        $db->parse("begin $arlg[1].BDOC.INIT(:id,to_date(:dt,'dd.mm.yyyy'),:msg,:adr); end;");
        $db->bind(":id", $idOrg, SQLT_INT);
	    $db->bind(":dt", $dat, SQLT_CHR);
	    $db->bind(":msg", $msg, SQLT_CHR, 128);
	    $db->bind(":adr", $idAdr, SQLT_INT);
	    $db->execute();

	    $patt="^19009572^";
	    $ostmin="0";
	    $max="100";
	    $days="30";

        $db->parse("begin $arlg[1].BDOC_EX.LIST_TOVS(:cur,:sk,:patt,:omin,:max,".
                ":days); end;");
        $db->bind(":cur", $cur, OCI_B_CURSOR);
        $db->bind(":sk", $sk, SQLT_INT);
        $db->bind(":patt", $patt, SQLT_CHR);
        $db->bind(":omin", $ostmin, SQLT_INT);
        $db->bind(":max", $max, SQLT_INT);
        $db->bind(":days", $days, SQLT_INT);
        $db->execute();
        $db->execute_cursor($cur);
        while($row = $db->fetch_cursor($cur))
        {
          $tovs[] = $row;
        }            

      var_dump($tovs);

      foreach($tovs as $row)
      {
         /* echo "{";
          foreach($row  as $key => $value)
          {
              echo '"' . $key . '"' . ":" . '"' . $value . '", ';
          }
          echo "}";*/
      }
    }


    if(isset($_REQUEST[journal]) && isset($_REQUEST[journal_type]))
    {
        $idFirm=0;
        $dtOn1='23.11.2019';
        $dtOn2='26.11.2019';

        if(isset($_REQUEST[dtOn1]))
        $dtOn1=$_REQUEST[dtOn1];

        if(isset($_REQUEST[dtOn2]))
        $dtOn2=$_REQUEST[dtOn2];

        if(isset($_REQUEST[cag]))
        $cag=$_REQUEST[cag];

        if($_REQUEST[journal_type]=='journal_schetov')
        {
        $db->parse("begin $arlg[1].JRN.SFAKT_R(:cur,to_date(:dtOn1,'dd.mm.yyyy'),to_date(:dtOn2,'dd.mm.yyyy'),:idFirm); end;");        
        $db->bind(":cur", $cur, OCI_B_CURSOR);
        $db->bind(":dtOn1", $dtOn1, SQLT_CHR);
        $db->bind(":dtOn2", $dtOn2, SQLT_CHR);
        $db->bind(":idFirm", $idFirm, SQLT_INT);
        $db->execute();
        $db->execute_cursor($cur);

        echo "[";
        while($row = $db->fetch_cursor($cur))
        {
            $waitlist[] = $row;
            $row[ORG] = str_replace('"','\"',$row[ORG]); 
            $row[ADR] = str_replace('"','\"',$row[ADR]); 
            $row[TXT] = str_replace('\\','\\\\',$row[TXT]); 
            $row[TXT] = str_replace('"','\"',$row[TXT]); 

            if($row['CAGENT']==$cag)
            {
            echo "{ ";
            echo '"ID":"' . $row[ID] . '", ';
            echo '"SF":"' . $row[SF] . '", ';
            echo '"TYPE":"' . $row[TYPE] . '", ';
            echo '"DATA":"' . $row[DSF] . '", ';
            echo '"NNAKL":"' . $row[NNAKL] . '", ';
            echo '"SUMMA":"' . $row[SUMMA]  . '", ';
            echo '"ORG":"' . $row[ORG] . '", ';
            echo '"ROUTE":"' . $row[ROUTE] . '", ';
            echo '"SKL":"' . $row['SKL'] . '", ';
            echo '"FIRM":"' . $FIRM . '", ';
            echo '"ADR":"' . $row['ADR'] . '", ';           
            echo '"TXT":"' . $row['TXT'] . '", ';
            echo '"CORG":"' . $row['CORG'] . '", ';
            echo '"CAGENT":"' . $row['CAGENT'] . '", ';
            echo '"is_parent":"' . $is_parent . '"';
            echo "},"; 
            }
        }            
        echo "{\"\":\"\"}]";
     }

     if($_REQUEST[journal_type]=='journal_nakladnih')
     {
         $idFirm=0;
         $idOrg=0;
         $dtOn1='23.11.2019';
         $dtOn2='26.11.2019';
 
         if(isset($_REQUEST[dtOn1]))
         $dtOn1=$_REQUEST[dtOn1];
 
         if(isset($_REQUEST[dtOn2]))
         $dtOn2=$_REQUEST[dtOn2];
 
         if(isset($_REQUEST[cag]))
         $cag=$_REQUEST[cag];
         
         $db->parse("begin $arlg[1].JRN.NAKL_R(:cur,to_date(:dtOn1,'dd.mm.yyyy'),to_date(:dtOn2,'dd.mm.yyyy'),:idFirm,:idOrg); end;");        
         $db->bind(":cur", $cur, OCI_B_CURSOR);
         $db->bind(":dtOn1", $dtOn1, SQLT_CHR);
         $db->bind(":dtOn2", $dtOn2, SQLT_CHR);
         $db->bind(":idFirm", $idFirm, SQLT_INT);
         $db->bind(":idOrg", $idOrg, SQLT_INT);

         $db->execute();
         $db->execute_cursor($cur);
 
         echo "[";
         while($row = $db->fetch_cursor($cur))
         {
             $waitlist[] = $row;
             $row[ORG] = str_replace('"','\"',$row[ORG]); 
             $row[ADR] = str_replace('\\','\\\\',$row[ADR]); 
             $row[ADR] = str_replace('"','\"',$row[ADR]); 
             $row[ADR] = str_replace('\r','',$row[ADR]); 
             $row[ADR] = str_replace('\n','',$row[ADR]); 
             $row[TXT] = str_replace('\\','\\\\',$row[TXT]); 
             $row[TXT] = str_replace('"','\"',$row[TXT]); 
              

             if($row['CAGENT']==$cag && ($row['FLAGS'] & 1)==1) // 
             {
             echo "{ ";
             echo '"ID":"' . $row[ID] . '", ';
             echo '"SF":"' . $row[SF] . '", ';
             echo '"TYPE":"' . $row[TYPE] . '", ';
             echo '"DATA":"' . $row[DT] . '", ';
             echo '"NNAKL":"' . $row[NNAKL] . '", ';
             echo '"SUMMA":"' . $row[SUMMA]  . '", ';
             echo '"ORG":"' . $row[ORG] . '", ';
             echo '"ROUTE":"' . $row[ROUTE] . '", ';
             echo '"SKL":"' . $row['SKL'] . '", ';
             echo '"FIRM":"' . $FIRM . '", ';
             echo '"ADR":"' . $row['ADR'] . '", ';           
             echo '"TXT":"' . $row['TXT'] . '", ';
             echo '"CORG":"' . $row['CORG'] . '", ';
             echo '"CAGENT":"' . $row['CAGENT'] . '", ';
             echo '"CRESERV":"' . ($row['FLAGS'] & 1) . '", ';
             echo '"is_parent":"' . $is_parent . '"';
             echo "},"; 
            }
         }            
         echo "{\"\":\"\"}]";
      }
    }

     //procedure ( cur out typCurGen, dat0 DATE,dat1 DATE, idFirm INTEGER,
     //idOrg INTEGER)





     if(isset($_REQUEST[get_doc_header]))
     {
        if(isset($_REQUEST[idDoc]))
        $idDoc=$_REQUEST[idDoc];

        $db->parse("begin $arlg[1].BDOC_EX.GET_HEADER(:cur,:idDoc); end;");
        $db->bind(":cur", $cur, OCI_B_CURSOR);
        $db->bind(":idDoc", $idDoc, SQLT_INT);
        
        $db->execute();
        $db->execute_cursor($cur);

        echo "[";
        while($row = $db->fetch_cursor($cur))
        {
          $tovs[] = $row;
          $row[ORG] = str_replace('"','\"',$row[ORG]); 

            echo "{ ";
            echo '"NNAKL":"' . $row[NNAKL] . '", ';
            echo '"DN":"' . $row[DN] . '", ';
            echo '"AGENT":"' . $row[AGENT] . '", ';
            echo '"OPER":"' . $row[OPER] . '", ';
            echo '"ORG":"' . $row[ORG]  . '"';
            echo "},"; 
        }            
        echo "{\"\":\"\"}]";

        //var_dump($tovs);
     }




     //procedure GET_LINES( cur out typCurGen, idDoc INTEGER, iSort INTEGER)

     if(isset($_REQUEST[get_doc_lines]))
     {
        if(isset($_REQUEST[idDoc]))
        $idDoc=$_REQUEST[idDoc];

        $db->parse("begin $arlg[1].BDOC_EX.GET_LINES(:cur,:idDoc,:iSort); end;");
        $db->bind(":cur", $cur, OCI_B_CURSOR);

        $db->bind(":idDoc", $idDoc, SQLT_INT);
        $db->bind(":iSort", $iSort, SQLT_INT);

        $db->execute();
        $db->execute_cursor($cur);

        echo "[";
        while($row = $db->fetch_cursor($cur))
        {
          $tovs[] = $row;

          echo "{ ";
            echo '"CMC":"' . $row[CMC] . '", ';
            echo '"NAME":"' . $row[NAME] . '", ';
            echo '"PRICE":"' . $row[PRICE] . '", ';
            echo '"KOL":"' . $row[KOL] . '", ';
            echo '"SUMMA":"' . $row[SUMMA]  . '"';
            echo "},"; 
        }            
        echo "{\"\":\"\"}]";

        //var_dump($tovs);
     }


          
     if(isset($_REQUEST[delete_doc]))
     {
        if(isset($_REQUEST[idDoc]))
        $idDoc=$_REQUEST[idDoc];

        $db->parse("begin $arlg[1].BDOC_EX.GET_HEADER(:cur,:idDoc); end;");
        $db->bind(":cur", $cur, OCI_B_CURSOR);
        $db->bind(":idDoc", $idDoc, SQLT_INT);
        
        $db->execute();
        $db->execute_cursor($cur);

        while($row = $db->fetch_cursor($cur))
        {
          $tovs[] = $row;
        //var_dump($tovs);
        }

        $row=$tovs[0];
        //echo $row[]

    $SRP=chr(30);
    $sData=$row[TIPSOPR] . $SRP . $row[VKL_KN] . $SRP . "0" . $SRP . "01-01-0001 00:00:00" . $SRP . 
           $row[NNAKL] . $SRP . "1" . $SRP . $row[CREDIT] . $SRP . $row[DPLAT] . $SRP . 
           $row[DCLIENT] . $SRP . $row[DEBUG];

    for($i=0;$i<22;$i++)
    {
        $sData=$sData . $SRP;
    }

    echo $sData;

    $sLines='';
    //$msg="";

    $db->parse("begin $arlg[1].SAVE_DOC_2(:idDoc,:sData,:sLines,:msg); end;");
    $db->bind(":idDoc", $idDoc, SQLT_INT);
    $db->bind(":sData", $sData, SQLT_CHR);
    $db->bind(":sLines", $sLines, SQLT_CHR);
    $db->bind(":msg", $msg, SQLT_CHR,255);
    $db->execute();

    echo $msg;
    }


     if(isset($_REQUEST[switch_nakl]))
     {
        if(isset($_REQUEST[idDoc]))
        $idDoc=$_REQUEST[idDoc];

        //create or replace procedure SWITCH_NAKL(idDoc INTEGER, iVkl INTEGER,
        //msg out VARCHAR2)

        $iVkl=1;

        $db->parse("begin $arlg[1].SWITCH_NAKL(:idDoc,:iVkl,:msg); end;");
        $db->bind(":idDoc", $idDoc, SQLT_INT);
        $db->bind(":iVkl", $iVkl, SQLT_INT);        
        $db->bind(":msg", $msg, SQLT_CHR,255);
        $db->execute();
    
        echo $msg;
     }


} 
catch(Exception $e) 
   {
          echo "myerror{err:". $e->getMessage(). "}";
   }
 



?>

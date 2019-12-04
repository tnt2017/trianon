<?php

header("Content-Type: text/html; charset=windows-1251");
require_once "orasql.php";
require_once "utils.php";

if( isset($_REQUEST['org']) && isset($_REQUEST['par']) )
{
  $par= sdecode($_REQUEST['par']);
  $idOrg= $_REQUEST['org'];
  $idGrz= $_REQUEST['grz'];
  $idAdr= $_REQUEST['adr'];
  $iCred= $_REQUEST['cre'];
  $idAgt= $_REQUEST['agt'];
  $flgs=  $_REQUEST['flg'];   //  sf/resv/samv/add/bonN
  $dat=   $_REQUEST['dt'];
  $zakNo= esc_decode($_REQUEST['zno']);
  $rem  = esc_decode($_REQUEST['txt']);
  $aMC=   explode(",", $_REQUEST['cmc']);
  $aKol=  explode(",", $_REQUEST['kol']);
  $aSum=  explode(",", $_REQUEST['sum']);
  $aPrc=  $_REQUEST['prc']=='' ? array() : explode(",", $_REQUEST['prc']);
  $ipadr=  $_REQUEST['ip'];
  $rmusr=  $_REQUEST['rusr'];
  $flgs += 128;  // use def.credit from KATORG.PRLIST, 26.10.16
  try {
    $db= new CMyDb();
    $db->connect($par[0], $par[1], "trn");

    $arlg= explode("$", $par[0]);
    // save new group discounts
    // 26.06.15  use PRLIST.SAVE_DISCS_VIA_PRCS (was SAVE_GRP_DISC_ARR)
    $lst= "";
    for($i=0; $i < count($aPrc)/2; $i++) {   // cmc,price
      if( $aPrc[$i*2] > 0 && $aPrc[$i*2+1] > 0 ) {
        $lst.= $aPrc[$i*2]."^".$aPrc[$i*2 + 1]."^";
      }
    }

    $msg="";
    echo "BDOC.INIT\r\n";

    $db->parse("begin $arlg[1].BDOC.INIT(:id,to_date(:dt,'dd.mm.yyyy'),:msg,:adr); end;");
    $db->bind(":id", $idOrg, SQLT_INT);
    $db->bind(":dt", $dat, SQLT_CHR);
    $db->bind(":msg", $msg, SQLT_CHR, 128);
    $db->bind(":adr", $idAdr, SQLT_INT);
    $db->execute();

    echo "BDOC.CREATE_BILL";

    $iTip=2;
    $iVkl=0;
    $iFlags=0;
    $idSkl=4;
    $sNom="";
    $sRem="test";
    $idDoc=""; // out;
    $msg="";   // out;

    $db->parse("begin $arlg[1].BDOC.CREATE_BILL(:iTip,:iVkl,:iFlags,:idSkl,:idDoc,:msg,:sNom,:idAdr,:sRem ); end;");
    $db->bind(":iTip", $iTip, SQLT_INT);
    $db->bind(":iVkl", $iVkl, SQLT_INT);
    $db->bind(":iFlags", $iFlags, SQLT_INT);
    $db->bind(":idSkl", $idSkl, SQLT_INT);
    $db->bind(":idDoc", $idDoc, SQLT_INT);
    $db->bind(":msg", $msg, SQLT_CHR, 128);
    $db->bind(":sNom", $sNom, SQLT_CHR, 128);
    $db->bind(":idAdr", $idAdr, SQLT_INT);
    $db->bind(":sRem", $sRem, SQLT_CHR, 128);
    $db->execute();

    echo $msg;
    echo $idDoc;

    //procedure  CREATE_BILL( iTip INTEGER,  iVkl INTEGER, iFlags INTEGER,
    //idSkl INTEGER, idDoc out NUMBER, msg out VARCHAR2,
    //sNom VARCHAR2, idAddr INTEGER, sRem VARCHAR2)
    //procedure STORE_LINE( idMC INTEGER, nKol INTEGER, rSum NUMBER, msg in out VARCHAR2)
   
    // save lines + header
    if( count($aMC) > 0 && count($aKol) > 0 ) 
    {
      $cnt= min(count($aMC),count($aKol));
      // lines
      $lines= "";
      for($i=0; $i < $cnt; $i++) 
      {
        $idMC= $aMC[$i]; $kol= $aKol[$i]; $rSum= $aSum[$i];
        $lines .= "$idMC^$kol^^";
        //procedure STORE_LINE( idMC INTEGER, nKol INTEGER, rSum NUMBER, msg in out VARCHAR2)
        echo "STORELINE " . $lines;
        $db->parse("begin $arlg[1].BDOC.STORE_LINE(:idMC,:nKol,:rSum,:msg); end;");
        $db->bind(":idMC", $idMC, SQLT_INT);
        $db->bind(":nKol", $kol, SQLT_INT);
        $db->bind(":rSum", $rSum, SQLT_INT);
        $db->bind(":msg", $msg, SQLT_CHR, 1024);
        $db->execute();
        echo "$msg";
      }
      echo "ADD_TO_BILL";

      $db->parse("begin $arlg[1].BDOC.ADD_TO_BILL(:idDoc,:msg); commit; end;");
      $db->bind(":idDoc", $idDoc, SQLT_INT);
      $db->bind(":msg", $msg, SQLT_CHR, 1024);
      $db->execute();
 
    }
    else
      echo "нет строк";
  }
  catch(Exception $e) {
    echo "{err:". $e->getMessage(). "}";
  }
}
else {
  echo "ошибка при создании заявки: неверные входные данные";
}
?>

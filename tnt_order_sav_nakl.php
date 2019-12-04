<?php
// make nakl for client order, 22.09.13
//  3.10.13  add reserve,samomvyv flags, use sum[i] > 0
//  9.04.14  add log action
// 23.12.14  save with MAKE_ORDER2()
// 26.06.15  use SAVE_DISCS_VIA_PRCS()

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
    if( strlen($lst) > 0 ) {
      echo "grp-discs len = ".strlen($lst)."<br>";
      $db->parse("begin $arlg[1].PRLIST.SAVE_DISCS_VIA_PRCS(".
                   "to_date(:dt,'dd.mm.yyyy'),:org,:lst,:msg); end;");
      $db->bind(":dt",  $dat, SQLT_CHR);
      $db->bind(":org", $idOrg, SQLT_INT);
      $db->bind(":lst", $lst, SQLT_CHR);
      $db->bind(":msg", $msg, SQLT_CHR, 128);
      $db->execute();
      echo "$msg<br>";
    }
    // save lines + header
    if( count($aMC) > 0 && count($aKol) > 0 ) {
      $cnt= min(count($aMC),count($aKol));
      // lines
      $lines= "";
      for($i=0; $i < $cnt; $i++) {
        $idMC= $aMC[$i]; $kol= $aKol[$i]; $rSum= $aSum[$i];
        $lines .= "$idMC^$kol^^";
      }
      // header
      $tip=2; $idFirm=""; $idSkl="";  // def firm,skl
      $howGot=5;
      $hdr= "$tip^$dat^$idFirm^$idOrg^$idAdr^$idGrz^$idAgt^$iCred^$zakNo^^".
        "$idSkl^$howGot^$rem^^0^0^0^";
      // now header
      $idDoc= 0;
      $msg= "";
      $db->parse("begin $arlg[1].MAKE_ORDER2(:id,:hdr,:lns,:flg,:msg); end;");
      $db->bind(":id", $idDoc, SQLT_INT);
      $db->bind(":hdr", $hdr, SQLT_CHR);
      $db->bind(":lns", $lines, SQLT_CHR);
      $db->bind(":flg", $flgs, SQLT_INT);
      $db->bind(":msg", $msg, SQLT_CHR, 1024);
      $db->execute();
      echo "$msg";
      $logf= "/home/raa/www/log/".date('Y').".log";  // log action, 9.04.14
      if( is_writable($logf) ) {
        $fLog= fopen($logf,'a');
        if( $fLog ) {
          fwrite($fLog, sprintf("%s^%s^%s^%s^MAKE_ORDER2^id=%s^flgs=%x\n",
            $ipadr, date('d.m.y H:i:s'), $rmusr, $par[0], $idDoc,$flgs) );
          fclose($fLog);
        }
      }
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

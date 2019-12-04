<?php
// list org buddies and addr, 13.09.13
//  4.10.13  add recent docs to edit

header("Content-Type: application/json; charset=windows-1251");
require_once "orasql.php";
require "utils.php";

if( isset($_REQUEST['id']) && isset($_REQUEST['par']) )
{
  $par= sdecode($_REQUEST['par']);
  $idOrg= $_REQUEST['id'];
  $tip= isset($_REQUEST['tip']) ? $_REQUEST['tip'] : 2;
  try {
    $db= new CMyDb();
    $db->connect($par[0], $par[1], "trn");

    $arlg= explode("$", $par[0]);
    $db->parse("begin $arlg[1].DIRS.ORG_LIST_BUDDIES(:cur,:id); end;");
    $db->bind(":cur", $cur, OCI_B_CURSOR);
    $db->bind(":id", $idOrg, SQLT_INT);
    $db->execute();
    $db->execute_cursor($cur);
    $grz= '';
    while( $row = $db->fetch_cursor($cur) )
    {
      $grz.="<option value=$row[ID]>$row[NAME]";
    }
    $db->parse("begin $arlg[1].DIRS.ADDR_LIST(:cur,:id); end;");
    $db->bind(":cur", $cur, OCI_B_CURSOR);
    $db->bind(":id", $idOrg, SQLT_INT);
    $db->execute();
    $db->execute_cursor($cur);
    $adr= '<option value="0">---</option>';
    while( $row = $db->fetch_cursor($cur) )
    {
      if( $row['DEF'] != 2 ) {  // not inactive
        $adr.="<option value=$row[IDADR] ".($row['DEF'] == 1 ? "selected":"").
              ">$row[ADR] ($row[IDADR])";
      }
    }
    $db->parse("begin $arlg[1].BDOC_EX.RECENT_RAW_DOCS(:cur,:org,:tip); end;");
    $db->bind(":cur", $cur, OCI_B_CURSOR);
    $db->bind(":org", $idOrg, SQLT_INT);
    //$tip=$tip+16;
    $tip=16;
    $db->bind(":tip", $tip, SQLT_INT);
    $db->execute();
    $db->execute_cursor($cur);
    $docs= '<option value=0> - Ќовый -';
    while( $row = $db->fetch_cursor($cur) )
    {
       $docs.="<option value=$row[ID] >$row[DN] $row[NNAKL] $row[SUMMA] ($row[CADR]) ";
       if($row[BILL]=='1')
       $docs.= "счет";
       else
       $docs.= "накладна€";

    }
    $db->parse("begin $arlg[1].DIRS.ORG_DEFAULTS(:cur,:id); end;");
    $db->bind(":cur", $cur, OCI_B_CURSOR);
    $db->bind(":id", $idOrg, SQLT_INT);
    $db->execute();
    $db->execute_cursor($cur);
    if( $row = $db->fetch_cursor($cur) ) {
      $sf= $row['SF'];
      $cred= $row['CREDIT'];
      $firm= $row['CFIRM'];
      $idAg= $row['CAGENT'];
      $agt=  $row['AGENT'];
    }
    else
      throw new Exception("не найден контрагент (id=$idOrg)");
    $ret_arr = array();
    $ret_arr['grz'] = iconv('cp1251', 'utf-8', $grz);
    $ret_arr['adr'] = iconv('cp1251', 'utf-8', $adr);
    $ret_arr['cre'] = iconv('cp1251', 'utf-8', $cred);
    $ret_arr['sf'] = $sf;
    $ret_arr['firm'] = $firm;
    $ret_arr['cag'] = $idAg;
    $ret_arr['agt'] = iconv('cp1251', 'utf-8', $agt);
    $ret_arr['docs'] = iconv('cp1251', 'utf-8', $docs);
    
    echo json_encode($ret_arr);
    
//     echo "{ grz: '$grz', adr: '$adr', cre: $cred, sf: $sf,".
//       " firm: $firm, cag: $idAg, agt: '$agt', docs: '$docs'}";
  }
  catch(Exception $e) {
    echo "{err:". $e->getMessage(). "}";
  }
}
?>

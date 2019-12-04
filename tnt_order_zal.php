<?php
echo "[";

require_once "orasql.php";
require_once "utils.php";

define('BYKOR', 1024);
define('WTHT_NDS', 2048);//without NDS

function is_present($idT)    // 14.01.14
{
  $dir= '/home/raa/www/ftp/tpic/';
  $base= $dir . substr($idT,0,3) . "/$idT";
  if( is_readable("$base.jpg") || is_readable("$base.png") )
    return 1;
  for($i=0; $i < 9; $i++) {
    if( is_readable("${base}_$i.jpg") || is_readable("${base}_$i.png") )
      return 1;
  }
  return 0;
}

function get_desc($db, $base, $idTov, $idStore)
{
  $db->parse("begin $base.DIRS.TOV_GET(:cur, :idTov, :idStore); end;");
  $db->bind(":cur", $cur, OCI_B_CURSOR);
  $db->bind(":idTov", $idTov, SQLT_INT);
  $db->bind(":idStore", $idStore, SQLT_INT);
  $db->execute();
  $db->execute_cursor($cur);
  // $ret = '';
  $row = $db->fetch_cursor($cur);
  $ret = iconv('cp1251', 'utf-8', $row['TXT']);
  if ($ret == '') {
    $ret = false;
  } else {
    $ret = true;
  }

  return $ret;
}

function numTo2powers( $num, $prefix) {
  // num= 2^i1 + 2^i2 + 2^i3 + ..  -> 'i1,i2,i3'
  $str= decbin($num);  // like 110100
  $len= strlen($str);
  $repr= '';
  for($i= 0; $i < $len; $i++) {
    if( substr($str,$len-$i-1,1) == '1' )
      $repr.= ",$prefix$i";
  }
  return substr($repr,1);
}

header("Content-Type: text/html; charset=windows-1251");
if( isset($_REQUEST['org']) && isset($_REQUEST['par']) )
{
  $par= sdecode($_REQUEST['par']);
  $idOrg= $_REQUEST['org'];
  $idAdr= "0"; //$_REQUEST['adr'];

  $dat= $_REQUEST['dt']; // as dd.mm.yyyy
  $ago= isset($_REQUEST["ago"]) && $_REQUEST["ago"] > 2 ? $_REQUEST["ago"] : 90;
  try {
    $db= new CMyDb();
    $db->connect($par[0], $par[1], "trn");
    $arlg= explode("$", $par[0]);
    
    //--------------------------------------------------------------------------
    $dtEnd = $dat;
    $dtBeg = date('j.m.Y',strtotime($dat."-$ago day"));
    $dtTil = $dtEnd;
    $actTovs = array();
    $actNTovs = array();
     
    //------------------------------------------------------------

    //echo $idOrg . ' ' . $idAdr . ' ' . $dat . ' ' . $ago ;

    $db->parse("begin $arlg[1].PRLIST.LIST_ENLISTED(:cur,:org,:adr,".
		"to_date(:dt,'dd.mm.yyyy'),:ago); end;");
    $db->bind(":cur", $cur, OCI_B_CURSOR);
    $db->bind(":org", $idOrg, SQLT_INT);
    $db->bind(":adr", $idAdr, SQLT_INT);
    $db->bind(":dt",  $dat, SQLT_CHR);
    $db->bind(":ago", $ago, SQLT_INT);
    $db->execute();
    $db->execute_cursor($cur);
    $iL= 0;
    while( $row = $db->fetch_cursor($cur) )
    {   
      //echo $row['PRICE'];
      

        $bgColor = "";
        $act_line = "";
   
            
      $disc= ($row['BASE'] > 0 && isset($row['PRICE']) ?
        round((1 - $row['PRICE']/$row['BASE'])*100,2) : 0);
      $maxdisc= (isset($row['PRICE']) && $row['PRICE'] > 0 ?
        round((1 - $row['SMP']/$row['PRICE'])*100,2) : 0);
      $maxdiscB= ($row['BASE'] > 0 ?
        round((1 - $row['SMP']/$row['BASE'])*100,2) : 0);
      $isPic= isset($row['CMC']) && is_present($row['CMC']) ? 1:0;
      $isDesc = isset($row['CMC']) && get_desc($db, $arlg[1],$row['CMC'],4) ? 1:0;
      //------------------------------------------------------------------------
      $korQuant= ($row['TOVFLG'] & BYKOR ? "q=$row[UPAK]" : "");
      $bgColor = ($row['TOVFLG'] & WTHT_NDS ? "background-color:yellow;":$bgColor);
      $add_title = ($row['TOVFLG'] & WTHT_NDS ? "тов.Без НДС":"");
      //------------------------------------------------------------------------
      $flg= $row['FLG'];
      
      $kol= round($row['SOLD_ORG'] / $row['PRICE']);
      if($flg != 4 && $flg != 5)// ne otobrazat esli zalistovki net
      {
        continue;
      }
        
      $iL++;

      /*
      echo "<tr ngrp='$row[GRP]' cgrp='$row[CGRP]' f='$flg'>".
        "<td>$iL</td>".
        "<td pic=$isPic>$row[CMC]</td>".
        "<td style='$bgColor' title='$add_title' desc=$isDesc id_tov=$row[CMC]>$row[NAME]</td>".
        "<td class=c>$kol</td>".
        "<td class=kol $korQuant></td>".
        "<td class=c>$row[OST_FREE]($row[KOL_RES])</td>".
        "<td class=r>".round($row['PRICE'],2)."</td>".
        "<td class=c b='$row[BASE]'>$disc</td>".
        "<td class=c v='$row[SMP]' m='$maxdiscB'>$maxdisc</td>".
        "<td>$act_line</td>";
      echo "</tr>";
      */

      echo "{";
        echo '"ID":"' . $row[CMC] . '", ';
        echo '"NAME":"' . $row[NAME] . '", ';
        echo '"$minK":"' . $minK  . '", ';
        echo '"korQuant":"' . $row[korQuant] . '", ';
        echo '"OST":"' . $row[OST_FREE] . '", ';
        echo '"PRICE_ORG":"' . round($row['PRICE_ORG'],2) . '", ';
        echo '"PRICE_B":"' . $disc . '", ';
        echo '"SMP":"' . $maxdisc . '", ';
        echo '"ENL_FLG":"' . $row['ENL_FLG'] . '", ';  
        echo '"CGRP_B":"' . $row['CGRP_B'] . '", ';
        echo '"prfx":"' . $prfx . '", ';
        echo '"gflg":"1", ';
        echo '"is_child":"' . $is_child . '", ';
        echo '"is_parent":"' . $is_parent . '"';
        echo "},";    

    }
  }
  catch(Exception $e) {
    echo "{err:". $e->getMessage(). "}";
  }
}
else {
  echo "<tr><td>неверные входные данные</tr>";
}

echo "{}]";


?>
<?php
echo "[";

// propose tovar for client order (based on mk_order_tov.php), 24.08.16

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
  $idAdr= $_REQUEST['adr'];
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
    
     
    $db->parse("begin $arlg[1].BDOC_OP.PROPOSE_ORDER(:cur,:org,:adr,".
                            "to_date(:dt,'dd.mm.yyyy'),:ago); end;");
    $db->bind(":cur", $cur, OCI_B_CURSOR);
    $db->bind(":org", $idOrg, SQLT_INT);
    $db->bind(":adr", $idAdr, SQLT_INT);
    $db->bind(":dt", $dat,  SQLT_CHR);
    $db->bind(":ago", $ago, SQLT_INT);
    $db->execute();
    $db->execute_cursor($cur);
    $iL= 0;
    while( $row = $db->fetch_cursor($cur) )
    {
        $bgColor = "";
        if(key_exists($row['CMC'],$actTovs))
        {
            $oneTov = $actTovs[$row['CMC']];
            foreach ($oneTov as $oneAct)
            {
                $begin_mtk = strtotime($oneAct['DBEG']);
                $end_mtk = strtotime($oneAct['DEND']);
                $check_mtk = strtotime($dtTil);
                if($begin_mtk <= $check_mtk && $check_mtk <= $end_mtk)
                {
                    $bgColor = "background-color:mistyrose;";
                }
            }
        }
        if(key_exists($row['CMC'],$actNTovs))
        {
            $oneTov = $actNTovs[$row['CMC']];
            foreach($oneTov as $oneAct)
            {
                if($oneAct['ACT_ACT'] == 2)
                {
                    $bgColor = "background-color:mistyrose;";
                }
            }
        }
        
      $disc= ($row['PRICE_B'] > 0 && isset($row['PRICE_ORG']) ?
        round((1 - $row['PRICE_ORG']/$row['PRICE_B'])*100,2) : 0);
      $maxdisc= (isset($row['PRICE_ORG']) && $row['PRICE_ORG'] > 0 ?
        round((1 - $row['SMP']/$row['PRICE_ORG'])*100,2) : 0);
      $maxdiscB= ($row['PRICE_B'] > 0 ?
        round((1 - $row['SMP']/$row['PRICE_B'])*100,2) : 0);
      $isPic= isset($row['CMC']) && is_present($row['CMC']) ? 1:0;
      //-----------------------------------------------------------------------
      $korQuant= ($row['FLAGS'] & BYKOR ? "q=$row[PACK2]" : "");  // 4.04.14
      $bgColor = ($row['FLAGS'] & WTHT_NDS ? "background-color:yellow;":$bgColor);
      $add_title = ($row['FLAGS'] & WTHT_NDS ? "˜˜˜.˜˜˜ ˜˜˜":"");
      //-----------------------------------------------------------------------
      $kol= isset($row['KOL_ORDER']) && $row['KOL_ORDER']>0 ? $row['KOL_ORDER'] : "";
      if( $kol > $row['KLAST'] ) $kol= $row['KLAST'];
      $iL++;
      //echo "{".
        //"Il= $iL</td>".
      
      /*  "<td pic=$isPic>$row[CMC]</td>".
        "<td style='$bgColor' title='$row[KSOLD] ($row[KLAST]) ˜˜, ˜˜ $row[CNT] ˜˜˜,".
                    " $row[DMIN] - $row[DMAX] ($add_title)'>$row[NAME]</td>".
        "<td class=c>$kol</td>".
        "<td class=kol $korQuant></td>".
        "<td class=c>$row[OST_FREE]</td>".
        "<td class=r>".round($row['PRICE_ORG'],2)."</td>".
        "<td class=c b='$row[PRICE_B]'>$disc</td>".
        "<td class=c v='$row[SMP]' m='$maxdiscB'>$maxdisc</td>".
      "</tr>";
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
  echo "<tr><td>˜˜˜˜˜˜˜˜ ˜˜˜˜˜˜˜ ˜˜˜˜˜˜</tr>";
}
echo "{}]";

?>

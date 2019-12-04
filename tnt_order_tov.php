<?php
// list tovar for client order, 15.09.13
// 14.01.14  add pictures
//  4.04.14  quant by kor: like q=12

header("Content-Type: text/html; charset=windows-1251");

require_once "orasql.php";
require_once "utils.php";

define('BYKOR', 1024);
define('WTHT_NDS', 2048);//without NDS

//echo  $_REQUEST['gr'] . ", "  . $_REQUEST['par'];

function is_present($idT)    // 14.01.14
{
  $dir= '/home/raa/www/ftp/tpic/';
  $base = '';
  if(strlen($idT) == 6)
  {
    $base= $dir . substr($idT,0,3) . "/$idT";
  }
  else if(strlen($idT) == 8)
  {
    $base= $dir . substr($idT,0,5) . "/$idT";  
  }
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

if( isset($_REQUEST['gr']) && isset($_REQUEST['par']) )
{
  $par= sdecode($_REQUEST['par']);
  $idGrp= $_REQUEST['gr'];
  $idChild = $_REQUEST['child'];
  $idHier= $_REQUEST['hr'];
  $idOrg= $_REQUEST['org'];
  $idAdr= $_REQUEST['adr'];
  $dat= $_REQUEST['dt'];
  $ost0=  $_REQUEST['ost'];  // if include ost=0
  $Unite = $_REQUEST['unite'];
  $patt= ($idHier==32 ? chr(30) : esc_decode($_REQUEST['pat']));
  $tovs = array();
  $childs = array();
  $TYPE_ORG = 0;
  try {
    $db= new CMyDb();
    $db->connect($par[0], $par[1], "trn");

    $arlg= explode("$", $par[0]);
    $db->parse("begin $arlg[1].DIRS.ORG_GET(:cur,:corg); end;");
    $db->bind(":cur", $org_cur, OCI_B_CURSOR);
    $db->bind(":corg", $idOrg, SQLT_INT);
    $db->execute();
    $db->execute_cursor($org_cur);
    if($org_row = $db->fetch_cursor($org_cur))
    {
        $TYPE_ORG = $org_row['VID'];
    }
    
    
    
    $db->parse("begin $arlg[1].BDOC.INIT(:id,to_date(:dt,'dd.mm.yyyy'),:msg,:adr); end;");
    $db->bind(":id", $idOrg, SQLT_INT);
    $db->bind(":dt", $dat, SQLT_CHR);
    $db->bind(":msg", $msg, SQLT_CHR, 128);
    $db->bind(":adr", $idAdr, SQLT_INT);
    $db->execute();
    if( $msg != '@' ) {
      echo "<tr><td>$msg</tr>"; exit(1);
    }
    $sk= 0; // normal
    $ostmin= $ost0 ? 0 : 1;
    $idSeek= 0;
    $days= 30;
    if( $idHier > 0 && $idHier != 32 ) {  // by group
      $flt= "";
      $db->parse("begin $arlg[1].BDOC_EX.LIST_TOVS(:cur,:grp,:sk,:flt,:omin,:idse,".
                ":hr,:days); end;");
      $db->bind(":cur", $cur, OCI_B_CURSOR);
      $db->bind(":grp", $idGrp, SQLT_INT);
      $db->bind(":sk", $sk, SQLT_INT);
      $db->bind(":flt", $patt, SQLT_CHR);
      $db->bind(":omin", $ostmin, SQLT_INT);
      $db->bind(":idse", $idSeek, SQLT_INT);
      $db->bind(":hr", $idHier, SQLT_INT);
      $db->bind(":days", $days, SQLT_INT);
      $db->execute();
      $db->execute_cursor($cur);
      while($row = $db->fetch_cursor($cur))
      {
          $tovs[] = $row;
      }
      if($idHier == 1 && $idGrp != 200001 && $idChild != 0)
      {
        $db->parse("begin $arlg[1].BDOC_EX.LIST_TOVS(:cur,:grp,:sk,:flt,:omin,:idse,".
                ":hr,:days); end;");
        $db->bind(":cur", $cur, OCI_B_CURSOR);
        $db->bind(":grp", $idChild, SQLT_INT);
        $db->bind(":sk", $sk, SQLT_INT);
        $db->bind(":flt", $patt, SQLT_CHR);
        $db->bind(":omin", $ostmin, SQLT_INT);
        $db->bind(":idse", $idSeek, SQLT_INT);
        $db->bind(":hr", $idHier, SQLT_INT);
        $db->bind(":days", $days, SQLT_INT);
        $db->execute();
        $db->execute_cursor($cur);
        while($row = $db->fetch_cursor($cur))
        {
            if(isset($row['CROOT']))
            {
                $childs[$row['CROOT']][] = $row;
            }
        }
      }
    }
    elseif( preg_match("/^\\d+$/",$patt) ) { // idTov is given
      $id= intval($patt);
      $db->parse("begin $arlg[1].BDOC_EX.ONE_TOV(:cur,:mc,:sk,:days); end;");
      $db->bind(":cur", $cur, OCI_B_CURSOR);
      $db->bind(":mc", $id, SQLT_INT);
      $db->bind(":sk", $sk, SQLT_INT);
      $db->bind(":days", $days, SQLT_INT);
      $db->execute();
      $db->execute_cursor($cur);
      while($row = $db->fetch_cursor($cur))
      {
          $tovs[] = $row;
      }
    }
    else {  // look-through list, 3.10.13
            // $patt=chr(30) for a list of fixed prc/disc tovs, 13.09.16
      $max= ($patt==chr(30) ? 3000 : 1000);
      $db->parse("begin $arlg[1].BDOC_EX.LIST_TOVS(:cur,:sk,:flt,:omin,:max,".
                ":days); end;");
      $db->bind(":cur", $cur, OCI_B_CURSOR);
      $db->bind(":sk", $sk, SQLT_INT);
      $db->bind(":flt", $patt, SQLT_CHR);
      $db->bind(":omin", $ostmin, SQLT_INT);
      $db->bind(":max", $max, SQLT_INT);
      $db->bind(":days", $days, SQLT_INT);
      $db->execute();
      $db->execute_cursor($cur);
      while($row = $db->fetch_cursor($cur))
      {
          $tovs[] = $row;
      }
    }
    
    $iL= 0;

    echo "[";

    if( !( ($idGrp==200001 && $idHier==1) || $idGrp==0 ||
        ($idGrp==288125 && $idHier==2) ) ) { 
          
           echo "{ ";
           echo '"NAME":"' . "uplevel" . '", ';
           echo '"iL":"' . $iL . '", ';
           echo '"idGrp":"' . $idGrp . '"';           
           echo "}, "; 


          /*// way up
      echo "<div style='display: none' class='hidden'>";
      echo "<pre>";
      print_r($idGrp);
      print_r($row);
      echo "</pre>";
      echo "</div>";
      echo "<tr t='-1'>".
          "<td>$iL</td>".
          "<td>0</td>".
          "<td colspan=6>... в корень ...</td>".
          "</tr><tr t=0>".
          "<td>$iL</td>".
          "<td>$idGrp</td>".
          "<td colspan=6>... наверх ...</td>".
        "</tr>\n";*/

    }
    function show_line($row,$is_child,$is_parent)
    {
      //print_r($row);

        global $db,$arlg,$iL,$idHier,$idGrp,$Unite;
      $id = $row["ID"];
      $name = $row["NAME"];
      $bgColor = '';
      $disc= ($row['PRICE_B'] > 0 && isset($row['PRICE_ORG']) ?
          round((1 - $row['PRICE_ORG']/$row['PRICE_B'])*100,2) : 0);
      $maxdisc= (isset($row['PRICE_ORG']) && $row['PRICE_ORG'] > 0 ?
          round((1 - $row['SMP']/$row['PRICE_ORG'])*100,2) : 0);
      $maxdiscB= ($row['PRICE_B'] > 0 ?
          round((1 - $row['SMP']/$row['PRICE_B'])*100,2) : 0);
      $isPic= isset($row['ID']) && is_present($row['ID']) ? 1:0;
      $isDesc = isset($row['ID']) && get_desc($db, $arlg[1],$row['ID'],4) ? 1:0;
      //-----------------------------------------------------------------------
      $korQuant= ($row['FLAGS'] & BYKOR ? "q=$row[PACK2]" : "");  // 4.04.14
      $bgColor = ($row['FLAGS'] & WTHT_NDS ? "background-color:yellow;":$bgColor);
      $add_title = ($row['FLAGS'] & WTHT_NDS ? "тов.Без НДС":"");
      //-----------------------------------------------------------------------
      $gflg= isset($row['GROUP_FLAG']) ? $row['GROUP_FLAG'] : 1;  // 5.03.15
      $prfx= ($row['PRICE_TYPE'] & (4 + 16) ? "class=fx" : "");
      // prc/disc is fixed,  13.09.16
      $minK= ($row['KOL_MIN']>1 ? " &gt;".($row['KOL_MIN']-1) : ""); // 11.04.18
      $iL++;
      
      $style = ($is_child ? "style='color:blue'":($is_parent ? "style='color:red'" : ""));
      if(isset($row['CROOT']) && $row['CROOT'] != 0 && $gflg == 0 && $Unite)
      {
          $style = "style='display:none'";
      }
      $title = ($is_child ? "товар из дочерней группы":"");

     /////////////////////////////////////////////// ? "<td colspan=6 $prfx>$row[NAME]</td>" :  "</tr>"
 

    if($idHier>0 && $idHier<32 && $gflg==0)
    {
      // это группа
    }


      if(is_numeric ($maxdisc))
      {
      echo "{ ";
      echo '"ID":"' . $row[ID] . '", ';

      $row[NAME] = str_replace('"','\\"',$row[NAME]); 


      echo '"NAME":"' . $row[NAME] . '", ';

      echo '"GRP":"' . $row[GRP] . '", ';
      echo '"CGRP":"' . $row[CGRP] . '", ';
      echo '"GRPBASE":"' . $row[GRPBASE] . '", ';

      echo '"$minK":"' . $minK  . '", ';
      echo '"korQuant":"' . $row[korQuant] . '", ';
      echo '"OST":"' . $row[OST] . '", ';
      echo '"PRICE_ORG":"' . round($row['PRICE_ORG'],2) . '", ';
      echo '"DISC":"' . $disc . '", ';

      echo '"PRICE_B":"' . $row['PRICE_B'] . '", ';

      echo '"SMP":"' . $row['SMP'] . '", ';
      echo '"MAXDISC":"' . $maxdisc . '", ';
      echo '"ENL_FLG":"' . $row['ENL_FLG'] . '", '; /* залистовка 4=предложение, 5 спецификация, */
      echo '"CGRP_B":"' . $row['CGRP_B'] . '", ';
      echo '"prfx":"' . $prfx . '", ';
      echo '"gflg":"' . $gflg . '", ';

      echo '"is_child":"' . $is_child . '", ';
      echo '"is_parent":"' . $is_parent . '"';

      echo "}"; 
      }
      else
      {
        echo "{ ";
          echo '"ID":"' . $row[ID] . '", ';
    
          $row[NAME] = str_replace('"','\\"',$row[NAME]); 
    
    
          echo '"NAME":"' . $row[NAME] . '!!!!!!!!!!", ';

          echo '"GRP":"' . $row[GRP] . '", ';
          echo '"CGRP":"' . $row[CGRP] . '", ';
          echo '"GRPBASE":"' . $row[GRPBASE] . '", ';


          echo '"$minK":"' . $minK  . '", ';
          echo '"korQuant":"' . $row[korQuant] . '", ';
          echo '"OST":"' . $row[OST] . '", ';
          echo '"PRICE_ORG":"' . round($row['PRICE_ORG'],2) . '", ';
          echo '"PRICE_B":"' . $disc . '", ';
          echo '"SMP":"' . $maxdisc . '", ';
          echo '"ENL_FLG":"' . $row['ENL_FLG'] . '", '; /* залистовка 4=предложение, 5 спецификация, */
          echo '"CGRP_B":"' . $row['CGRP_B'] . '", ';
          echo '"prfx":"' . $prfx . '", ';
          echo '"gflg":"' . $gflg . '", ';
    
          echo '"is_child":"' . $is_child . '", ';
          echo '"is_parent":"' . $is_parent . '"';
    
          echo "}"; 

      }

    }
    
    
    foreach($tovs as $row)
    {
            $tov_childs = $childs[$row['ID']];
            $have_child = count($tov_childs);
            if($have_child > 0 && $TYPE_ORG != 1)//$TYPE_ORG == 1 esli ORG seteviki
            {
            $i=0;
            if($i>0)
            echo ",";
	$i++;

                if($row[ENL_FLG] == 4 || $row[ENL_FLG] == 5)
                {
                    show_line($row,false,true);
                }
                else
                {
                    // выбираем дочерний товар с наименьшей ценой
                    $child_price = 1000000;
                    $child_index = -1;
                    $price_parent = round($row['PRICE_ORG'],2);
                    $i = 0;
                    foreach($tov_childs as $one_child)
                    {
                        $price_child_tmp = round($one_child['PRICE_ORG'],2);
                        if($price_child_tmp < $child_price)
                        {
                            $child_index = $i;
                            $child_price = $price_child_tmp;
                        }
                        
                        $i++;
                    }
                    
                    if($tov_childs[$child_index]['OST'] > 0)
                    {
                        show_line($tov_childs[$child_index],true,false);
                    }
                    else 
                    {
                        show_line($row,false,true);
                    }
                }
            }
            else
            {
                show_line($row,false,false);
            }

	echo ",";
    }
  }
  catch(Exception $e) {
    echo "{err:". $e->getMessage(). "}";
  }
}
else 
{
  echo "[неверные входные данные";
  print_r($_REQUEST);
}

    echo "{}]";

?>

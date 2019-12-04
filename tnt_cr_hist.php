<?php
// cartoon-like credit-history, started 5.09.13
// 11.09.13  attach calendar controls to dates
// 12.11.13  add org attribites info
// 10.07.14  show orig.correction

session_start();
require_once "orasql.php";
require_once "utils.php";
require_once "auth_chk.php";
?>
<html>
<head>
<meta http-equiv="Content-Language" content="ru">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Style-Type" content="text/css">
<title>Кредит-история</title>
<link rel=stylesheet type="text/css"  href="default.css">
<link rel=stylesheet type="text/css"  href="js/dyna_cal.css">
<link rel="shortcut icon" href="images/favicon.ico">
<link rel="icon" href="images/favicon.ico">
<style type="text/css">
 .org { width: 400px }
 .tds { font-size: 90%; background-color: white; border: 2px solid beige;}
 .tds td { margin: 1px; background-color: #E0E0E0; cursor: pointer; }
 .tds td:hover { background-color: beige; }
 .bo { font-weight: bold; }
 .np { background-color: violet; }
 .cr { background-color: yellow; }
 .oi { color: brown; font-style: italic; }
 .idd {cursor: pointer; color: brown; font-weight: bold; }
</style>
<script src="js/dates.js" ></script>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="js/jqlib.js" ></script>
<script src="js/dyna_cal.js" ></script>
<!-- Autocomplete -->
<!-- JS file -->
<script src="main/js/easyAutoComplete/jquery.easy-autocomplete.js"></script> 

<!-- CSS file -->
<link rel="stylesheet" href="main/js/easyAutoComplete/easy-autocomplete.min.css">
</head>
<body>
<div class=c><span class=head> Кредит-история</span><br>
[<a href="default.php">Главная</a>]</div>
<hr>

<form method=post target=_self  name=frmMain >
<?php
try {
  $db= new CMyDb();
  $bFault= 0;
  $bRight= isset($_POST['sRight']) ? $_POST['sRight'] : 0;
  $ip= $_SERVER['REMOTE_ADDR'];

  if( auth_check( $ip, $_SESSION['OraLogin'],$_SESSION['OraPwd']) )
     exit(1);

  // try connect
  try {
    if( !isset($_SESSION['OraLogin']) || !isset($_SESSION['OraPwd']) )
      throw new Exception('on first connect: login/passwd is not set');

    $db->connect($_SESSION['OraLogin'], $_SESSION['OraPwd'], "trn");
    if( is_file("tmp/$ip") ) unlink("tmp/$ip");
  }
  catch(Exception $e) {
    $bFault= 1;
    // first run is detected by 'bRight'/sInit elements
    if( isset($_POST['sRight']) || isset($_POST['sInit']) ) {
      echo "<span class=err>" . $e->getMessage() .
      "<br>Имя / пароль не верны. </span>\n";
    }
  }
  if( !$bFault && !$bRight )  // verify right for this report
  {
    $bRight= auth_right($db, 102, 0);
  }
  if( !$bFault && $bRight ) // login is valid, right is granted
  {
    $beg= isset($_REQUEST['datBeg']) ? $_REQUEST['datBeg'] : "";
    $end= isset($_REQUEST['datEnd']) ? $_REQUEST['datEnd'] : "";
    $idOrg= isset($_REQUEST['corg']) ? $_REQUEST['corg'] : 0;
    $org= isset($_REQUEST['sOrg']) ? esc_decode($_REQUEST['sOrg']) : "";
    $radD= isset($_REQUEST['radD']) ? $_REQUEST['radD'] : 1;
    $oatt= isset($_REQUEST['oatt']) ? $_REQUEST['oatt'] : "";
    if( !isset($_POST['sRight']) ) // for the 1st time here
    {
      $beg= date("j.m.Y", strtotime("-2 months"));
      $end= date("j.m.Y");
    }
    $arlg= explode("$", $_SESSION['OraLogin']);
    if( $idOrg > 0 && $oatt == "" ) {
      $db->parse("begin $arlg[1].DIRS.ORG_DEFAULTS(:cur,:org); end;");
      $db->bind(":cur", $cur, OCI_B_CURSOR);
      $db->bind(":org", $idOrg, SQLT_INT);
      $db->execute();
      $db->execute_cursor($cur);
      if( $row= $db->fetch_cursor($cur) ) {
        $sf= isset($row['SF']) && $row['SF'] ? 'Да':'Нет';
        $kpp= isset($row['KPP']) ? $row['KPP'] : '';
        $aim= isset($row['AIM']) ? $row['AIM'] : '';
        $oatt= "ИНН/КПП <b>$row[INN] / $kpp</b>&nbsp;&nbsp;".
          "тел. <b>$row[TEL]</b>&nbsp;&nbsp;&nbsp;".
          "юр.адрес : <i>$row[ADR]</i><br>".
          "сф : <i>$sf</i>&nbsp;&nbsp; контакт : <i>$row[CONTACT]</i>&nbsp;&nbsp;".
          "прим-е : <i>$row[TXT]</i>&nbsp;&nbsp;&nbsp;".
          "цель : <b>$aim</b>&nbsp;&nbsp; ".
          "торгпред : <i>$row[AGENT] ($row[CAGENT])</i>";
      }
    }
?>
<input type=hidden name=sRight value='<?php echo $bRight;?>' >
<input type=hidden name=corg value=<?php echo $idOrg;?> >
<input type=hidden name=oatt value='<?php echo $oatt;?>' >
<script src="js/dyna_calco.js"></script>
<table border=0 cellspacing=5>
<tr><td>Период :</td><td>
  <input name=datBeg size=8 value='<?php echo $beg;?>' onChange="this.value=CheckDate(this.value);">
  <img src="images/cal.gif" width=16 height=16 onclick="ds_sh('datBeg')" />
  --
  <input name=datEnd size=8 value='<?php echo $end;?>' onChange="this.value=CheckDate(this.value);">
  <img src="images/cal.gif" width=16 height=16 onclick="ds_sh('datEnd')" />
  </td><td><input type=submit class=btnS value="Показать" ></td>
</tr><tr>
  <td>Контрагент :</td>
  <td><input name=sOrg value='<?php echo $org;?>' class=org autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" disabled></td><td></td>
</tr><tr>
  <td>По дате :</td>
  <td><input type=radio name=radD value=1 <?php echo ($radD==1? "checked":"");?> > &nbsp;отгрузки
    &nbsp;&nbsp;&nbsp;
    <input type=radio name=radD value=2 <?php echo ($radD==2? "checked":"");?> > &nbsp;расчета
  </td>
</tr>
</table>
<div id=orgatt><?php echo $oatt;?></div>
<script language="javascript">
var par="<?php echo sencode($_SESSION['OraLogin']).'g0'.sencode($_SESSION['OraPwd']);?>";

function enterOrg(idOrg)
{
  $("input[name=corg]").val(idOrg);
  $.ajax({
    url: "org_attr.php",
    type: 'POST',
    dataType: 'json',
    data: {org : idOrg, 'par': par},
    success: function(dat,stat,xmlReq) {
      if( dat.err )
        $("#orgatt").html("<span class=err>"+dat.err+"</span>")
      else
        $("#orgatt").html("ИНН/КПП <b>"+dat.inn+" / "+dat.kpp+"</b>&nbsp;&nbsp;"+
          "тел. <b>"+dat.tel+"</b>&nbsp;&nbsp;&nbsp;"+
          "юр.адрес : <i>"+dat.adr+"</i><br>"+
          "сф : <i>"+dat.sf+"</i>&nbsp;&nbsp; контакт : <i>"+dat.contact+"</i>&nbsp;&nbsp;"+
          "прим-е : <i>"+dat.txt+"</i>&nbsp;&nbsp;&nbsp;"+
          "цель : <b>"+dat.aim+"</b>&nbsp;&nbsp;"+
          " торгпред : <i>"+dat.rep+"("+dat.crep+")"+"</i>")
    },
    complete: function(xmlReq,stat) {  }
  })
}
function deleteOrg(e)
{
  $("input[name=corg]").val(0);
  $("#orgatt").text('')
}

$(document.body).ready(function() {
  //makeBuddy( $("input[name=sOrg]"), {}, "tds", enterOrg, "org_sel_a.php", deleteOrg)
  
  $.ajax({
        type: 'POST',
        url: "main/extras/org_list.php",
        dataType: 'json',
        cache: false,
        success: function(data) 
        {
            $("input[name=sOrg]").prop('disabled', false);
            var options = {
                    data: data,
                    getValue: "name",
                    list: {
                                    maxNumberOfElements: 40,
                                    sort: {
                                        enabled: true
                                    },
                                    match: {
                                            enabled: true
                                    },
                                    onChooseEvent: function() {
                                    var value = $("input[name=sOrg]").getSelectedItemData().id;

                                    enterOrg(value);
                            }
                    }
            };

            $("input[name=sOrg]").easyAutocomplete(options);
        },
        error: function(xhr, textStatus, hz)
        {
            console.log(textStatus);
        },
        complete: function() 
        {
        }
    });
  var tb= document.getElementById('cts')
  if( tb ) {
    $("td:nth-child(10)", tb).filter(function() {
      return parseInt($(this).text()) < 0
    }).addClass('idd').click(function() {
      var org= escape(frmMain.sOrg.value),
        aTd= $(this).parent().children('td'),
        nom= escape(aTd.eq(2).text()), dat= aTd.eq(0).text(),
        adr= escape(aTd.eq(7).text()), id= -parseInt($(this).text())
      window.open("doc_lines.php?id="+id+"&nom="+nom+"&dt="+dat+
                "&&org="+org+"&adr="+adr)
    })
  }
})
</script>
<?php
    if( isset($_POST['sRight']) ) // 2nd+
    {
      $idF= '';
      $tip= ($radD==2 ? 1 : 0) + 0x10;
      $db->parse("begin $arlg[1].CREDIT_HIST(:cur,:org,to_date(:d0,'dd.mm.yyyy'),".
                 "to_date(:d1,'dd.mm.yyyy'),:tip,:firm,:lim,:agr,:per,:msg); end;");
      $db->bind(":cur", $cur, OCI_B_CURSOR);
      $db->bind(":org", $idOrg, SQLT_INT);
      $db->bind(":d0",  $beg, SQLT_CHR, 20);
      $db->bind(":d1",  $end, SQLT_CHR, 20);
      $db->bind(":tip", $tip, SQLT_INT);
      $db->bind(":firm", $idF, SQLT_CHR);  // was int, 10.10.16 
      $db->bind(":lim", $lim, SQLT_CHR, 80);
      $db->bind(":agr", $agr, SQLT_CHR, 80);
      $db->bind(":per", $per, SQLT_CHR, 80);
      $db->bind(":msg", $msg, SQLT_CHR, 80);
      $db->execute();
      $db->execute_cursor($cur);
      echo "<p>$lim &nbsp;&nbsp;&nbsp;$agr";
?>
<p><table border=1 cellspacing=2 style="font-size: 90%">
<tr>
<th> дата отгр
<th> дата расч
<th> номер
<th> тип
<th> отгрузка
<th> оплата
<th> сальдо
<th> адрес
<th> прим-е
<th> id
</tr><tbody id=cts>
<?php
      define("F_ACTS", 0x20);
      define("F_NPRN", 0x80);
      define("F_ORIG", 0x400);

      $saldo= 0; $ships= 0; $pays= 0; $shipsT= 0; $paysT= 0;
      $ds= $radD==1 ? "class=bo" : "";
      $da= $radD==2 ? "class=bo" : "";
      $iL= 0;
      while( $row = $db->fetch_cursor($cur) )
      {
        $kod= $row['KOD'] & 0xF;
        if( $kod != 2 && $kod != 6 ) {
          $saldo += $row['SHIP'] + $row['PAY'];
          $shipsT+= $row['SHIP'];
          $paysT+= $row['PAY'];
        }
        if( (($radD==1 && $row['DS_IN']) ||
            ($radD==2 && $row['DA_IN'])) && ! $row['IS_INI'] ) {
          $np= $row['KOD'] & F_NPRN ? "class=np" : "";
          $cr= $row['KOD'] & F_ACTS ? " cr" : "";
          $oi= $row['KOD'] & F_ORIG ? " oi" : "";
          $sf= isset($row['SF']) ? $row['SF'] : '';
          if( $kod != 2 && $kod != 6 ) {
            $ships+= $row['SHIP'];
            $pays+= $row['PAY'];
          }
          echo "<tr>".
            "<td $ds>$row[DNAK]</td>".
            "<td $da>$row[DPL]</td>".
            "<td $np>$row[NOM] $sf</td>".
            "<td>$row[TYP]</td>".
            "<td class='r$cr$oi'>$row[SHIP]</td>".
            "<td class=r>$row[PAY]</td>".
            "<td class=rb>".round($saldo,2)."</td>".
            "<td>".(isset($row['ADDR']) ? $row['ADDR'] : '')."</td>".
            "<td>$row[RM]</td>".
            "<td>$row[IDD]</td>".
          "</tr>\n";
          $iL++;
        }
      }
      echo "</tbody></table><p>строк : <b>$iL</b>&nbsp;&nbsp;".
        "отгрузок : <b>$ships</b> (всего <b>$shipsT</b>)&nbsp;&nbsp;".
        " оплат : <b>$pays</b> (всего <b>$paysT</b>)</p>";
    } // 2nd+
  } // login is valid
  if( $bFault ) // show authorization dialog
  {
    save_auth_params($ip, $_SESSION['capt']);
  }
}
catch(Exception $e) {
  echo "<p class=err>exception: " . $e->getMessage() . "</p>\n";
}
?>
</form>
<HR>
<p class=foot>
RaaSoft.<br>Copyright &copy; 2013-2014 [Trianon]. All rights reserved. <BR>
Revised: 10.07.14
</body>
</html>

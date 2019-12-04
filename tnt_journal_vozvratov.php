<?php
// clients returns journal, w/o jquery re-started 3.09.19

session_start();
require_once "orasql.php";
require_once "utils.php";
require_once "auth_chk.php";
$year0= "2016";
$mtime= filemtime(basename($_SERVER['SCRIPT_NAME']));
$wrange= (date('Y',$mtime)==$year0 ? $year0 : "$year0 - ".date('Y',$mtime));
?>
<html>
<head>
<meta http-equiv="Content-Language" content="ru">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Style-Type" content="text/css">
<title>Возвраты клиентов</title>
<link rel=stylesheet type="text/css"  href="default.css">
<link rel=stylesheet type="text/css"  href="js/dyna_cal.css">
<link rel="shortcut icon" href="images/favicon.ico">
<link rel="icon" href="images/favicon.ico">
<style type="text/css">
 .hot { background-color: beige; color: maroon; cursor: pointer;}
 .atsk { background-color: aquamarine }
 .atem { background-color: yellow }
 .off { padding-left: 30px;}
 .hit { cursor: pointer;}
 div#adoc { position: absolute; display: none; background-color: oldlace;
      border: 2px solid gray;}
</style>
<script src="js/utpure.js" ></script>
<script src="js/dyna_cal.js" ></script>
</head>
<body>
<div class=c><span class=head> Возвраты клиентов</span><br>
[<a href="default.php">Главная</a>]
[<a href="retc_emp.php" target=_blank>СкладСотрудника</a>]
[<a href="retc_emps.php" target=_blank>По всем</a>]
[<a href="retc_skl.php" target=_blank>ПриемНаСклад</a>]
</div>
<hr>

<form method=post target=_self  name=frmMain >
<?php
try {
  $db= new CMyDb();
  $bFault= 0;
  $bRight= isset($_POST['sRight']) ? $_POST['sRight'] : 0;
  if( in_array( $_SERVER['SERVER_ADDR'], $ext_server) ) {
    // external site
    $ip= $_SERVER['REMOTE_ADDR'];
    if( auth_check($ip, $_SESSION['OraLogin'],$_SESSION['OraPwd']) )
      exit(1);
  }
  else {
    // inner site
    if( isset($_POST['sInit']) && $_POST['sInit'] > 0 )
      auth_parse();
  }
  // try connect
  try {
    if( !isset($_SESSION['OraLogin']) || !isset($_SESSION['OraPwd']) )
      throw new Exception('on first connect: login/passwd is not set');
    $db->connect($_SESSION['OraLogin'], $_SESSION['OraPwd'], "trn");
    if( in_array( $_SERVER['SERVER_ADDR'], $ext_server) && is_file("../login/$ip")) {
      unlink("../login/$ip");
    }
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
    $bRight= auth_right( $db, 102, 0);
  }
  if( !$bFault && $bRight ) // login is valid, right is granted
  {
    $beg= isset($_REQUEST['datBeg']) ? $_REQUEST['datBeg'] : "";
    $end= isset($_REQUEST['datEnd']) ? $_REQUEST['datEnd'] : "";
    if( !isset($_POST['sRight']) ) // for the 1st time here
    {
      $beg= "1." . date("m.Y");
      $end= date("j.m.Y");
    }
?>
<input type=hidden name=sRight value='<?php echo $bRight;?>' >
<table border=0 cellspacing=5>
<tr><td>Период :</td><td>
  <input name=datBeg size=9 value='<?php echo $beg;?>' onChange="this.value=checkDate(this.value);">
  <img src="../images/cal.gif" width=16 height=16 onclick="ds_sh('datBeg')">
  &nbsp;&nbsp; &ndash; &nbsp;&nbsp;
  <input name=datEnd size=9 value='<?php echo $end;?>' onChange="this.value=checkDate(this.value);">
  <img src="../images/cal.gif" width=16 height=16 onclick="ds_sh('datEnd')">
  </td><td class=off><input type=submit class=btnS value="Показать" ></td>
</tr><tr>
  <td>Фильтр :</td><td><input name=sFilter value="" style="width:200px"></td>
  <td>убрать курсор из поля, чтобы применить</td>
</tr>

<tr>

  <td>Контрагент :</td>

  <td><input name=sOrg value="<?php echo esc_decode($_REQUEST['sOrg']); ?>" class=org autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" disabled></td>

</tr>

</table>
<script src="../js/dyna_calco.js"></script>
<?php
    if( isset($_POST['sRight']) ) // 2nd+
    {
      $idFirm= 0; // all
      $idOrg= 0; // all
      $arlg= explode("$", $_SESSION['OraLogin']);
      $db->parse("begin $arlg[1].JRN.RET_CLI(:cur,to_date(:d0,'dd.mm.yyyy'),".
              "to_date(:d1,'dd.mm.yyyy'),:firm,:org); end;");
      $db->bind(":cur", $cur, OCI_B_CURSOR);
      $db->bind(":d0",  $beg, SQLT_CHR);
      $db->bind(":d1",  $end, SQLT_CHR);
      $db->bind(":firm",$idFirm, SQLT_INT);
      $db->bind(":org", $idOrg,  SQLT_INT);
      $db->execute();
      $db->execute_cursor($cur);
?>

<script type="text/javascript">
var par="<?php echo sencode($_SESSION['OraLogin']).'g0'.sencode($_SESSION['OraPwd']);?>";

var cId=0,cKK=1,cDat=2,cNom=3,cSum=4,cRut=5,cOrg=6,cAdr=7,cAgt=8,cExp=9,cSkl=10,
  cRem=11;
var eDocId;

function applyFilter()
{
  var filtV= frmMain.sFilter.value,
    atr= document.getElementById("cts").children
  for(var i=0; i < atr.length; i++) {
    if( filtV=="" )
      atr[i].style.display= "table-row"
    else {
      var atd= atr[i].children,
        isOn= (atd[cNom].textContent.indexOf(filtV)>=0 ||
          atd[cRut].textContent.indexOf(filtV)>=0 || atd[cOrg].textContent.indexOf(filtV)>=0 ||
          atd[cAdr].textContent.indexOf(filtV)>=0 || atd[cAgt].textContent.indexOf(filtV)>=0 ||
          atd[cExp].textContent.indexOf(filtV)>=0 || atd[cSkl].textContent.indexOf(filtV)>=0 ||
          atd[cRem].textContent.indexOf(filtV)>=0)
      atr[i].style.display= (isOn ? "table-row" : "none")
    }
  }
}

function onHitDoc(ev) // show lines + accept
{
  eDocId= ev.target
  var  off= fullOffset(eDocId), ht= eDocId.offsetHeight +4,
    idDoc= eDocId.textContent, eNom= eDocId.parentNode.children[cNom]
  frmMain.btnAccept.disabled= eNom.className != "hit"
  ajax_load("../de/doc_lns.php", {"par":par, id: idDoc},
    function(resp, kod,status) {
      if( kod == 200 ) {
        var div= document.getElementById("adoc")
        div.lastElementChild.innerHTML= resp
        div.style.left= "16ex"
        div.style.top= off.top + ht
        div.style.display= "block"
      }
      else
        alert("ajax: "+kod+" "+status)
    })
}

function onViewDoc(ev) {
  var tr= ev.target.parentNode, atd= tr.children,
    id= atd[cId].textContent, nom= esc(atd[cNom].textContent), dat= esc(atd[cDat].textContent),
    org= esc(atd[cOrg].textContent), adr= esc(atd[cAdr].textContent)
  window.open("../de/doc_lines.php?id="+id+"&dt="+dat+"&nom="+nom+"&org="+org+
    "&adr="+adr+"&mls=")
}

function hideDocLinesBox()
{
  var d= document.getElementById("adoc")
  d.style.display= "none"
  d.querySelector("span").innerHTML= ""
}

function onAccept(ev)
{
  if( eDocId.getAttribute("done") == 1 ) {
    alert("Уже принято"); return
  }
  var sDta="", dv=document.getElementById("adoc"),
    atr= dv.querySelector("tbody").children
  for(var i=0; i < atr.length; i++) {  // #,cmc,tov,kol,kolF,prc,sum
    var atd= atr[i].children
    if( parseInt(atd[3].textContent) > 0 )
      sDta+= atd[1].textContent+"^"+atd[3].textContent+"^"
  }
  if( sDta != "" && eDocId ) {
    ajax_load("retc_sav.php", {"par":par, idd: eDocId.textContent, dta: sDta},
      function( resp, kod,status) {
        if( kod == 200 ) {
          eDocId.setAttribute("done",1)
          dv.querySelector("span").innerHTML= resp
        }
        else
          alert("ajax: "+kod+" "+status)
        eDocId= null
      })
  }
  else
    alert("в документе нет непустых строк")
}

window.onload= function()
{
  frmMain.sFilter.onchange= applyFilter
  document.body.onkeydown= function(ev) {
    if( ev.keyCode == 27 )
      hideDocLinesBox()
  }
  var atr= document.getElementById("cts").children
  for(var i=0; i < atr.length; i++) {
    var atd= atr[i].children
    atd[cId].onclick= onHitDoc
    atd[cNom].onclick= onViewDoc
  }
  frmMain.btnAccept.onclick= onAccept
  document.getElementById("adoc").querySelector("img").onclick= hideDocLinesBox
}

</script>

<div id=adoc><div><span></span>
  <input type=button name=btnAccept value="Принять" style="float:left">
  <img src="../images/close.png" width=24 height=24 style="cursor:pointer;float:right" />
  </div>
<div style="clear:both"></div>
</div>

<p style="font-size:80%"><b>NB.</b>
<span class="nak">номер</span> &ndash; есть накладная (иначе - счет),<br>
склад = скл-id &ndash; товар у сотрудника с таким id,
"Главный" + есть накладная &ndash; товар передан на склад, &nbsp;&nbsp;
"Главный" + нет накладной &ndash; товар у клиента не забирался,<br>
Фильтр применяется к полям "Номер", "Маршрут", "Клиент", "Адрес", "Торгпред",
  "Экспедитор", "Склад", "Примечание"<br>
  <span class=atem>Номер</span> &ndash; товар по документу у сотрудника,
  <span class=atsk>Номер</span> &ndash; товар по документу принят складом,
</p>
<p><table border=1 cellspacing=2 style="font-size:90%">
<thead><tr>
<th>id</th>
<th>KK</th>
<th>дата</th>
<th>номер</th>
<th>сумма</th>
<th>маршрут</th>
<th>клиент</th>
<th>адрес</th>
<th>торгпред</th>
<th>экспедитор</th>
<th>склад</th>
<th>примеч-е</th>
</tr></thead>
<tbody id=cts>
<?php
      while( $row = $db->fetch_cursor($cur) )
      {
        $skl= "$row[SKL]"=="" ? "скл-$row[CPODRFROM]" : "$row[SKL]";
        $sf= "$row[SF]";
        $cls= ($sf[0]=="*" ? " at".("$row[SKL]"=="" ? "em":"sk") :"");
        echo <<<ROWL
<tr tip="$row[TIPSOPR]">
  <td class=hot>$row[ID]</td>
  <td title="$row[SF]">$row[MK]</td>
  <td>$row[DT]</td>
  <td class="hit$cls">$row[NNAKL]</td>
  <td class=r>$row[SUMMA]</td>
  <td>$row[ROUTE] ($row[CTRIP])</td>
  <td>$row[ORG] ($row[CORG])</td>
  <td>$row[ADR] ($row[ADR_DOST])</td>
  <td>$row[AG]</td>
  <td>$row[EX]</td>
  <td>$skl</td>
  <td>$row[RM]</td>
</tr>\n
ROWL;
      }
      echo "</tbody></table>";
    } // 2nd+
  } // login is valid
  if( $bFault ) // show authorization dialog
  {
    if( in_array( $_SERVER['SERVER_ADDR'], $ext_server) ) {
      // external site
      save_auth_params($ip,$_SESSION['capt']);
    }
    else {  // inner site
      auth_dlg( $db);
    }
  }
}
catch(Exception $e) {
  echo "<p class=err>exception: " . $e->getMessage() . "</p>\n";
}
?>
</form>
<HR>
<p class=foot>
RaaSoft.<br>Copyright &copy; <?php echo $wrange;?> [Trianon]. All rights reserved. <BR>
Revised: <?php echo date('j.m.Y',$mtime);?>
</body>
</html>

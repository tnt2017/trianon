<?php
// make new org, started 15-21.03.14
//  4.04.14  allow potential clients
// 24.06.14  strict check for INN
//  7.10.14  extra null param (GRUZPOL) for save adr
// 22.07.15  use auth_chk.php, add chkCoP, chkDgB, headers for 3 parts
// 11.11.15  small corrections after Yura's merge
// 19.11.15  unified merch-day, use symb.consts, org-prlist options

session_start();
require_once "orasql.php";
require_once "utils.php";
require_once "auth_chk.php";
$mtime= filemtime(basename($_SERVER['SCRIPT_NAME']));
?>
<html>
<head>
<meta http-equiv="Content-Language" content="ru">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Style-Type" content="text/css">
<title>Новый контрагент</title>
<link rel=stylesheet type="text/css"  href="default.css">
<link rel=stylesheet type="text/css"  href="css/jquery-ui.css">
<link rel="shortcut icon" href="images/favicon.ico">
<link rel="icon" href="images/favicon.ico">
<style type="text/css">
 .box6 { width: 580px }
 .box4 { width: 440px }
 .box3 { width: 300px }
 .box2 { width: 253px }
 .box1 { width: 100px }
 .box0 { width: 50px }
 .tds { font-size: 90%; background-color: white; border: 2px solid blue;}
 .tds td { margin: 2px; background-color: #E0E0E0; cursor: pointer; }
 .tds td:hover { background-color: aqua; }
 .a { color: red }
  #pbar { display: none }
 .hdr { padding-left: 20px;  font-weight: bold }
 .shf { padding-left: 2ex }
</style>
<script src="js/jquery-1.3.2.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="js/jqlib.js?534753475893" ></script>

<script>

function handle(e) 
{
  //debugger;
  console.log('handle ekey=' + e.key);

  if(e.key=="Delete" || e.key=="Backspace")
  {
    delOrg();
  }
}

   
</script>



</head>
<body>
<div class=c><span class=head> Создание нового контрагента</span><br>
[<a href="default.php">Главная</a>]</div>
<hr>

<form method=post target=_self  name=frmMain >
<?php
try {
  $db= new CMyDb();
  $bFault= 0;
  $bRight= isset($_POST['sRight']) ? $_POST['sRight'] : 0;
    
  if( in_array($_SERVER['SERVER_ADDR'], $ext_server) ) {
    // external site
    $ip = $_SERVER['REMOTE_ADDR'];
    if( auth_check( $ip, $_SESSION['OraLogin'],$_SESSION['OraPwd']) ) {
      exit(1);
    }
  } else {
    // inner site
    if( isset($_POST['sInit']) && $_POST['sInit'] > 0 ) {
      auth_parse();
    }
  }
  // try connect
  try {
    if (!isset($_SESSION['OraLogin']) || !isset($_SESSION['OraPwd']))
	    throw new Exception('on first connect: login/passwd is not set');
    $db->connect($_SESSION['OraLogin'], $_SESSION['OraPwd'], "trn");
    if( in_array($_SERVER['SERVER_ADDR'], $ext_server) ) {
      if( is_file("tmp/$ip") )
        unlink("tmp/$ip");
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
    $arlg= explode("$", $_SESSION['OraLogin']);
    $org = isset($_REQUEST['sOrg']) ? $_REQUEST['sOrg'] : "";
    $idOrg = isset($_REQUEST['idOrg']) ? $_REQUEST['idOrg'] : 0;

?>
<input type=hidden name=sRight value='<?php echo $bRight;?>' >
<p><b>1.</b> <span class=hdr>Название и реквизиты клиента</span>
<table border=0 cellspacing=5>
<tr><td>название <span class=a></span>:</td><td><input name=sOrg id="sOrg" class=box6 value='' autocomplete=off down=1 onclick="select(this);" onkeyup="handle"></td>
</tr>
<tr><td>юр.форма <span class=a></span>:</td><td><select name=cbJur size=1 >
  <option value='-1'>---<option value='0'>OOO<option value='1'>ОАО<option value='2'>ЗАО<option value='3'>ИП<option value='4'>ГУП<option value='5'>ФГУП<option value='6'>МУП<option value='7'>НГООИ<option value='8'>ПО<option value='9'>ОП<option value='10'>ТСЖ<option value='11'>ПАО<option value='12'>АО<option value='13'>КФХ<option value='14'>&nbsp;</select>
  <span>ИНН <span class=a></span>:&nbsp;&nbsp;<input name=sINN class=box1 value='' ></span>
  <span title='не нужен для ИП'>КПП <span class=a></span>:&nbsp;&nbsp;<input name=sKPP class=box1 value='' ></span>
  <span>вид :&nbsp;<select name=cbVid size=1>
  <option value=0>--<option value=1>сеть<option value=2>розница<option value=3>рынок<option value=4>опт<option value=5>лоток<option value=6>прочее</select>
  </span></td></tr>
<tr><td>юр.адрес <span class=a></span>:</td><td><input name=sAdr class=box6 value='' ></td></tr>
<tr><td>свид-во :</td><td><input name=sSvi class=box6 value='' ></td></tr>
<tr><td>эл.почта :</td><td><input name=sMail class=box6 value='' ></td></tr>
<tr><td>телефон <span class=a></span>:</td><td><input name=sTel class=box3 value=''>
  <span>сот.тел. для сообщ-й: <input name=sTel2  value='' class=box1 title='формат: 9... без разделителей 8-ки и 7-ки'></span></td></tr>
  <tr><td colspan="2" align="center"><span class="box1" style="font-weight: bold; color: red;">(формат: 10 цифр без разделителей, 8-ки и 7-ки)</span></td></tr>
<tr><td>контакт :</td><td>
  <input name=sCtkt class=box3 value='' >
  <span>расчет :&nbsp;&nbsp;<select name=cbAcc  size=1 >
  <option value=0>наличные<option value=1>кредит за наличные<option value=2>безналично
  <option value=3>резерв<option value=4>предоплата</select>
  </span>
</td></tr>
<tr><td>рассылка :</td><td>
  <input type=checkbox name=chkRlyAct > акции
  <span class=shf><input type=checkbox name=chkRlyUp > повышение цен</span>
  <span class=shf><input type=checkbox name=chkRlyPrc > прайс-листы</span>
  <span class=shf><input type=checkbox name=chkRlyAgE > ответ на email агента</span>
</td></tr>
<tr><td>прайс-лист:</td><td>
  <span class=shf>иерархия &nbsp;<select name=cbPrHier size=1>
    <option value=0>базовая<option value=1>категории</select></span>
  <span class=shf>файл-формат: &nbsp;<select name=cbPrFmt size=1>
    <option value=0>excel<option value=1>pdf<option value=2>csv</select></span>
  <span class=shf><input type=checkbox name=chkPrOrgGr> по группам клиента</span>
</td></tr>
<tr><td>примеч-е :</td><td><input name=sTxt class=box6 value='' ></td></tr>
<tr><td></td><td><table cellspacing=1>
  <tr><td><span><input type=checkbox name=chkSF > &nbsp;сч-фактура </span></td>
    <td><span><input type=checkbox name=chkT12 >&nbsp; торг-12 </span></td>
    <td><span><input type=checkbox name=chkCoP >&nbsp; контроль отбора </span></td>
  </tr><tr>
    <td><span><input type=checkbox name=chkDgB >&nbsp; дог-счет </span></td>
    <td colspan=3><span>запрет округл. :&nbsp;вниз
    <input type=checkbox name=chkRnDn checked >
    &nbsp;вверх  <input type=checkbox name=chkRnUp checked ></span></td>
  </tr>
  </table></tr>
<tr><td>торгпред :<td><select name=cbAg size=1>
<?php
    $vid= 1; //agents
    $db->parse("begin $arlg[1].DIRS.EMP_4REP_HIER(:cur,:vid); end;");
    $db->bind(":cur", $cur, OCI_B_CURSOR);
    $db->bind(":vid", $vid, SQLT_INT);
    $db->execute();
    $db->execute_cursor($cur);
    while( $row = $db->fetch_cursor($cur) ) {
      echo "<option value=$row[ID] >$row[NAME]";
    }
?>
  </select></td></tr>
<tr><td></td>
<td><input type=button class=btnS name=bnSave value="Сохранить" >
  <span><i>NB. Если не указан ИНН, клиент будет потенциальным</i></span></td></tr>
<tr style="margin-top: 10px"><td colspan=3>
<b>2.<b> <span class=hdr>Установка цен (перед этим действием клиент уже должен существовать в базе)</span></td></tr>
<tr><td>цены как у :</td><td><input name=sOrg2 class=box4 value='' autocomplete=off down=1>
  <span><input type=button name=bnPricesAs value='Применить'></span></td></tr>
</table>
<div id=msg></div><div id=pbar><img src="images/progbar-1.gif"></div>
<p style="margin-top: 20px">
<b>3.<b> <span class=hdr>Адреса клиента (перед созданием адреса клиент должен существовать)</span><br>
<table cellspacing=4>
<tr><td>Адреса доставки :</td><td>
<select name=cbAddr class=box6 size=1 >
  <option value=0> - новый -
</select>
</td></tr>
<tr><td>нас.пункт <span class=a></span>:</td><td><input name=sPkt class=box6 value='' autocomplete=off down=1></td></tr>
<tr><td> <td style="font-size:80%; font-style: italic">
  NB. Для сокращения списка нас.пунктов начните с кода региона,
    напр. 54нов, 42топ</td></tr>
<tr><td>другое назв-е <span class=a></span>:</td>  
  <td><input name=sNasPtT value="" size=5> &nbsp; &nbsp;
  <input name=sNasPt class=box3 value='' ></td></tr>
<tr><td>улица <span class=a></span>:</td><td><input name=sUlT size=6 value="" >
  &nbsp;&nbsp;<input name=sUl class=box3 value='' autocomplete=off down=1>
  &nbsp;&nbsp;дом <span class=a></span>: &nbsp;&nbsp;<input name=sDom value='' size=6 >
  &nbsp;&nbsp;
  <select name=cbKrp size=1 >
  <option value=''>/<option value='корп.'>корп.<option value='стр.'>стр.<option value='влад.'>влад.<option value='офис'>офис<option value='пом.'>пом.<option value='пав-н'>пав-н<option value='маг.'>маг.<option value='киоск'>киоск<option value='конт.'>конт.</select>
  &nbsp; <input name=sKrp value='' size=6 title='номер'> </td></tr>
<tr><td>раб. часы :</td><td><input name=sWkHr0 class=box0 value='' > &nbsp;--
  &nbsp;<input name=sWkHr1 class=box0 value='' >
  <span>перерыв : &nbsp;<input name=sWkHr2 class=box0 value='' > &nbsp;--
  &nbsp;<input name=sWkHr3 class=box0 value='' ></span></td>
</tr><tr><td>площадь, кв.м <span class=a></span>:</td>
  <td>наш товар &nbsp;<input name=sArea class=box0 value='' > &nbsp;&nbsp;
  полная &nbsp; <input name=sAreaT class=box0 value='' ></td>
</tr><tr><td>расписание доставки :</td>
  <td> <input type=checkbox name=chkMon> понедельник,
  <input type=checkbox name=chkTue> вторник,
  <input type=checkbox name=chkWed> среда,
  <input type=checkbox name=chkThu> четверг,
  <input type=checkbox name=chkFri> пятница,
  <input type=checkbox name=chkSat> суббота,<br>
  пропускать <input name=sWeekOff value="" class=box0> недель (0-31),
  первая неделя доставки в году <input name=sWeek1 value="" class=box0> (0-31)</td>
</tr><tr><td>расписание заказ мест. :</td>
  <td> <input type=checkbox name=chkMonP> понедельник,
  <input type=checkbox name=chkTueP> вторник,
  <input type=checkbox name=chkWedP> среда,
  <input type=checkbox name=chkThuP> четверг,
  <input type=checkbox name=chkFriP> пятница,
  <input type=checkbox name=chkSatP> суббота,<br>
  пропускать <input name=sWeekOffP value="" class=box0> недель (0-31),
  первая неделя заказа в году <input name=sWeek1P value="" class=box0> (0-31)</td>
</tr><tr><td>расписание заказ эл. :</td>
  <td> <input type=checkbox name=chkMonE> понедельник,
  <input type=checkbox name=chkTueE> вторник,
  <input type=checkbox name=chkWedE> среда,
  <input type=checkbox name=chkThuE> четверг,
  <input type=checkbox name=chkFriE> пятница,
  <input type=checkbox name=chkSatE> суббота,<br>
  пропускать <input name=sWeekOffE value="" class=box0> недель (0-31),
  первая неделя заказа в году <input name=sWeek1E value="" class=box0> (0-31)</td>
</tr><tr><td>расписание рассылки :</td>
  <td> <input type=checkbox name=chkMonR> понедельник,
  <input type=checkbox name=chkTueR> вторник,
  <input type=checkbox name=chkWedR> среда,
  <input type=checkbox name=chkThuR> четверг,
  <input type=checkbox name=chkFriR> пятница,
  <input type=checkbox name=chkSatR> суббота,<br>
  пропускать <input name=sWeekOffR value="" class=box0> недель (0-31),
  первая неделя рассылки в году <input name=sWeek1R value="" class=box0> (0-31)</td>
  </tr><tr>
<td>расписание мерчендайзера :</td>
  <td> <input type=checkbox name=chkMonM> понедельник,
  <input type=checkbox name=chkTueM> вторник,
  <input type=checkbox name=chkWedM> среда,
  <input type=checkbox name=chkThuM> четверг,
  <input type=checkbox name=chkFriM> пятница,
  <input type=checkbox name=chkSatM> суббота,<br>
  пропускать <input name=sWeekOffM value="" class=box0> недель (0-31),
  первая неделя работы мерчендайзера в году <input name=sWeek1M value="" class=box0> (0-31)</td>
</tr>
<tr><td>примечание :</td><td><input name=sAdRem class=box6 value='' ></td></tr>
<tr>
<td>маршрут <span class=a></span>:</td><td ><input name=sRoute value='' class=box2 autocomplete=off down=1>
&nbsp;район <span class=a></span>:&nbsp;<select name=sRegion value='0' class=box2>
<?php
    $db->parse("begin $arlg[1].ORGS.LIST_REGIONS(:cur,:nCnt); end;");
    $db->bind(":cur", $cur, OCI_B_CURSOR);
    $db->bind(":nCnt", $nCnt, SQLT_INT);
    $db->execute();
    $db->execute_cursor($cur);
    while( $row = $db->fetch_cursor($cur) ) {
      echo "<option value=$row[ID] >$row[NAME]";
    }
?>
</td>
</tr>
<tr><td></td>
<td><input type=button name=bnAdrSave class=btnL value="Сохранить адрес" ></td></tr>
</table>
</p>

<div id=msg></div><div id=pbar><img src="images/progbar-1.gif"></div>
<p style="margin-top: 20px">
<b>4.<b> <span class=hdr>Штат клиента
 (перед созданием штатной единицы клиент должен существовать)</span><br>
<table cellspacing=4>
<tr><td>Контакт :</td><td>
<select id="cbEmps" name=cbEmps class=box6 size=1 >
  <option value=0> - новый -
</select>
</td></tr>
<tr><td colspan="2"><hr /></td></tr>
<tr><td>Должность <span class=a></span>:</td><td>
<select id="cbEmpPos" name=cbEmpPos class=box6 size=1 >
  <option value=0>не важно</option>
  <option value=1>продажи</option>
  <option value=2>закупки</option>
  <option value=3>бухгалтер</option>
  <option value=4>бухгалтер-сверки</option>
  <option value=5>директор</option>
  <option value=6>глав.бухгалтер</option>
  <option value=7>коммерч.директор</option>
  <option value=8>склад</option>
  <option value=9>зам.директора</option>
  <option value=10>секретарь</option>
  <option value=11>офис-менеджер</option>
  <option value=12>завхоз</option>
  <option value=13>менеджер</option>
  <option value=14>продавец</option>
  <option value=15>товаровед</option>
</select>
</td></tr>
<tr><td>ФИО <span class=a></span>:</td><td><input id="sEmpName" name=sEmpName class=box6 value='' ></td></tr>
<tr><td colspan="2">Контакты (укажите хотя бы одно из полей)<span class=a></span>:</td></tr>
<tr><td>телефон рабочий <span class=a></span>:</td><td><input id="sEmpTel" name=sEmpTel class=box6 value='' ></td></tr>
<tr><td>телефон моб. <span class=a></span>:</td><td><input id="sEmpMob" name=sEmpMob class=box6 value='' ></td></tr>
<tr><td>e-mail <span class=a></span>:</td><td><input id="sEmpMail" name=sEmpMail class=box6 value='' ></td></tr>
<tr><td>день рождения :</td><td><input id="sEmpBirth" type="text" name=sEmpBirth >&nbsp;(формат: дд-мм-гггг)</td></tr>
<tr><td>примечание :</td><td><input id="sEmpComment" name=sEmpComment class=box6 value='' ></td></tr>
<tr><td colspan="2" align="center"><input id="bnEmpSave" type=button name=bnEmpSave class=btnL value="Сохранить контакт" ></td></tr>
</table>
</p>

<p><span class=a></span> &ndash; обязательные поля для сохранения.</p>
<script type="text/javascript">
var par="<?php echo sencode($_SESSION['OraLogin']).'g0'.sencode($_SESSION['OraPwd']);?>"
var cSF=8, cT12=32, cRnUp=1024,cRnDn=2048, cOtb=4096,cDgB=131072

var aAdrs, aEmps, iPrL=0,
  cNPt=0, cNP=1, cULt=2, cUL=3, cDOM=4, cREM=5, cCPT=6,cPT=7,cCRT=8,cRT=9,
  cAR=10, cWH0=11, cWH1=12, cWH2=13, cWH3=14, cArT=15, cDlvD=16, cRgn=17,
  cPhyD=18, cEleD=19, cRlyD=20, cMerD=21, cKrp=22, cKrpTyp=23

var PR_CRE0= 0x10, PR_CRE= 0xF0, PR_HIER0= 0x100, PR_HIER= 0x300,
    PR_BYGRP= 0x800, PR_RLY0= 0x1000, PR_RLY= 0xF000,
    PR_FMT0= 0x10000,  PR_FMT=0x30000

var I_DLV=0, I_PHYS=1, I_ELEC=2, I_RLY=3, I_MERCH=4    

function hhmm2num( hhmm ) {
  var aM= hhmm.match( /(\d\d?)([ :.-](\d\d))?/ )
  if( aM ) {
    return Math.round((parseInt(aM[1])*60 + (aM[3] ? parseInt(aM[3]) : 0))*10/6)/100
  }
  else
    return 0
}
function num2hhmm( hr )
{
  var rHr= parseFloat(hr), nHr= Math.floor(rHr),
     nMin= Math.round((rHr - nHr)*60)
     
  if (!isNaN(rHr) && !isNaN(nHr) && !isNaN(nMin))
  	return nHr+":"+(nMin > 9 ? nMin : "0"+nMin)
  else
	return ""
}

function make_action_day( iTyp )
{
  var dd= (iTyp == I_DLV ?
    (frmMain.chkMon.checked ? 1 : 0)+ (frmMain.chkTue.checked ? 2 : 0)+
    (frmMain.chkWed.checked ? 4 : 0)+ (frmMain.chkThu.checked ? 8 : 0)+
    (frmMain.chkFri.checked ? 16 : 0)+ (frmMain.chkSat.checked ? 32 : 0)+
    128 * (parseInt(frmMain.sWeekOff.value) < 32 ? frmMain.sWeekOff.value : 0)+
    4096* (parseInt(frmMain.sWeek1.value) < 32 ? frmMain.sWeek1.value : 0)
    : ( iTyp == I_PHYS ? 
    (frmMain.chkMonP.checked ? 1 : 0)+ (frmMain.chkTueP.checked ? 2 : 0)+
    (frmMain.chkWedP.checked ? 4 : 0)+ (frmMain.chkThuP.checked ? 8 : 0)+
    (frmMain.chkFriP.checked ? 16 : 0)+ (frmMain.chkSatP.checked ? 32 : 0)+
    128 * (parseInt(frmMain.sWeekOffP.value) < 32 ? frmMain.sWeekOffP.value : 0)+
    4096* (parseInt(frmMain.sWeek1P.value) < 32 ? frmMain.sWeek1P.value : 0)
    : ( iTyp == I_ELEC ? 
    (frmMain.chkMonE.checked ? 1 : 0)+ (frmMain.chkTueE.checked ? 2 : 0)+
    (frmMain.chkWedE.checked ? 4 : 0)+ (frmMain.chkThuE.checked ? 8 : 0)+
    (frmMain.chkFriE.checked ? 16 : 0)+ (frmMain.chkSatE.checked ? 32 : 0)+
    128 * (parseInt(frmMain.sWeekOffE.value) < 32 ? frmMain.sWeekOffE.value : 0)+
    4096* (parseInt(frmMain.sWeek1E.value) < 32 ? frmMain.sWeek1E.value : 0)
    : ( iTyp == I_RLY  ?
    (frmMain.chkMonR.checked ? 1 : 0)+ (frmMain.chkTueR.checked ? 2 : 0)+
    (frmMain.chkWedR.checked ? 4 : 0)+ (frmMain.chkThuR.checked ? 8 : 0)+
    (frmMain.chkFriR.checked ? 16 : 0)+ (frmMain.chkSatR.checked ? 32 : 0)+
    128 * (parseInt(frmMain.sWeekOffR.value) < 32 ? frmMain.sWeekOffR.value : 0)+
    4096* (parseInt(frmMain.sWeek1R.value) < 32 ? frmMain.sWeek1R.value : 0)
    : ( iTyp == I_MERCH  ?
    (frmMain.chkMonM.checked ? 1 : 0)+ (frmMain.chkTueM.checked ? 2 : 0)+
    (frmMain.chkWedM.checked ? 4 : 0)+ (frmMain.chkThuM.checked ? 8 : 0)+
    (frmMain.chkFriM.checked ? 16 : 0)+ (frmMain.chkSatM.checked ? 32 : 0)+
    128 * (parseInt(frmMain.sWeekOffM.value) < 32 ? frmMain.sWeekOffM.value : 0)+
    4096* (parseInt(frmMain.sWeek1M.value) < 32 ? frmMain.sWeek1M.value : 0)
    :
    0
    ) ))))
  return dd 
}
function set_action_day( iTyp, iActDay )
{
  // iTyp: 0 deliv, 1 phys.zakaz(P), 2 elect.zakaz(E), 3 relay(R), 4 merchand(M)
  //   defines iActDay
  var iWeek1= (iActDay & 4096*31)/4096,
    iWeekOff= (iActDay & 128*31)/128, iDay= (iActDay & 127)
  if( iTyp == I_DLV ) {  // deliv. day
  frmMain.chkMon.checked= (iDay & 1)
  frmMain.chkTue.checked= (iDay & 2)
  frmMain.chkWed.checked= (iDay & 4)
  frmMain.chkThu.checked= (iDay & 8)
  frmMain.chkFri.checked= (iDay & 16)
  frmMain.chkSat.checked= (iDay & 32)
  frmMain.sWeekOff.value= iWeekOff
  frmMain.sWeek1.value= iWeek1
  }
  else if( iTyp == I_PHYS ) {  // phys. zakaz day
  frmMain.chkMonP.checked= (iDay & 1)
  frmMain.chkTueP.checked= (iDay & 2)
  frmMain.chkWedP.checked= (iDay & 4)
  frmMain.chkThuP.checked= (iDay & 8)
  frmMain.chkFriP.checked= (iDay & 16)
  frmMain.chkSatP.checked= (iDay & 32)
  frmMain.sWeekOffP.value= iWeekOff
  frmMain.sWeek1P.value= iWeek1
  }
  else if( iTyp == I_ELEC ) {  // electron. zakaz day
  frmMain.chkMonE.checked= (iDay & 1)
  frmMain.chkTueE.checked= (iDay & 2)
  frmMain.chkWedE.checked= (iDay & 4)
  frmMain.chkThuE.checked= (iDay & 8)
  frmMain.chkFriE.checked= (iDay & 16)
  frmMain.chkSatE.checked= (iDay & 32)
  frmMain.sWeekOffE.value= iWeekOff
  frmMain.sWeek1E.value= iWeek1
  }
  else if( iTyp == I_RLY ) {  // relay  day
  frmMain.chkMonR.checked= (iDay & 1)
  frmMain.chkTueR.checked= (iDay & 2)
  frmMain.chkWedR.checked= (iDay & 4)
  frmMain.chkThuR.checked= (iDay & 8)
  frmMain.chkFriR.checked= (iDay & 16)
  frmMain.chkSatR.checked= (iDay & 32)
  frmMain.sWeekOffR.value= iWeekOff
  frmMain.sWeek1R.value= iWeek1
  }
  else if( iTyp == I_MERCH ) {  // merchandiser  day
  frmMain.chkMonM.checked= (iDay & 1)
  frmMain.chkTueM.checked= (iDay & 2)
  frmMain.chkWedM.checked= (iDay & 4)
  frmMain.chkThuM.checked= (iDay & 8)
  frmMain.chkFriM.checked= (iDay & 16)
  frmMain.chkSatM.checked= (iDay & 32)
  frmMain.sWeekOffM.value= iWeekOff
  frmMain.sWeek1M.value= iWeek1
  }
}

function loadOrg(e, idOrg)
{
  e.parent().attr('corg', idOrg)
  $.ajax({       // load org attr
    url: "mk_org_att.php",
    dataType: 'json',
    data: {'id': idOrg, 'par': par},
    success: function(dat, stat, xmlReq) {
      if( dat.err ) {
        $("#msg").html("<span class=err>"+dat.err+"</span>")
      }
      else {
        var vid= parseInt(dat.vid) % 10,
          relay= (dat.rly ? parseInt(dat.rly) : 0)
        iPrL= parseInt(dat.prl)  // static cumulative flag PRLIST
        var iPrByGrps= (iPrL & PR_BYGRP ? 1 : 0),
          iPrHier= (iPrL & PR_HIER) /PR_HIER0,
          iPrFmt=  (iPrL & PR_FMT) /PR_FMT0
        $("input[name=sOrg]").parent().attr({'org': dat.org, mod: 0})
        frmMain.cbVid.options[vid].selected= true
        frmMain.cbJur.options[dat.jur >= 0 ? dat.jur+1 : 0].selected= true
        frmMain.sINN.value= dat.inn
        frmMain.sKPP.value= dat.kpp
        frmMain.sAdr.value= dat.adr
        frmMain.sTel.value= dat.tel
        frmMain.sTel2.value= dat.tel2
        frmMain.sCtkt.value= dat.ctkt
        for(var i=0; i < frmMain.cbAg.options.length; i++)
          if( frmMain.cbAg.options[i].value == dat.cag ) {
            frmMain.cbAg.options[i].selected= true; break
          }
        frmMain.sSvi.value= dat.svi
        frmMain.sMail.value= dat.mail
        frmMain.sTxt.value= dat.txt
        if( dat.cre >= 0 )
          frmMain.cbAcc.options[dat.cre].selected=true
        frmMain.chkSF.checked= dat.flg & cSF
        frmMain.chkT12.checked= dat.flg & cT12
        frmMain.chkRnUp.checked= dat.flg & cRnUp
        frmMain.chkRnDn.checked= dat.flg & cRnDn
        frmMain.chkCoP.checked= dat.flg & cOtb
        frmMain.chkDgB.checked= dat.flg & cDgB
        frmMain.chkRlyAct.checked= relay & 1
        frmMain.chkRlyUp.checked= relay & 2
        frmMain.chkRlyPrc.checked= relay & 4
        frmMain.chkRlyAgE.checked= relay & 8
        frmMain.cbPrHier.options[iPrHier].selected= true
        frmMain.cbPrFmt.options[iPrFmt].selected= true
        frmMain.chkPrOrgGr.checked= iPrByGrps
        frmMain.cbAddr.innerHTML= dat.adrs
        aAdrs= dat.aadr  // [tipNP,nameNP,tipUl,nameUl,dom,rem,cpkt,pkt,crut,rut]
        frmMain.cbAddr.onchange= onSelectAddr
        onSelectAddr();

        frmMain.cbEmps.innerHTML = dat.emps
        aEmps = dat.eemp
        frmMain.cbEmps.onchange = onSelectEmp
        onSelectEmp();

        $("#msg").text('')
      }
    },
    error: function(xmlReq, stat, errHtml) {
      $("#msg").html("<span class=err>"+stat+" : "+errHtml+"</span>")
    }
  })
  makeBuddy( $("input[name=sOrg2]"), {}, "tds", loadOrg2, "org_sel_a.php", delOrg2)
}

function selectByVal( e, v )
{
  for( i=0; i < e.options.length; i++) {
    if( e.options[i].text == v ) { e.options[i].selected= true; return }
  }
}

function onSelectAddr()
{
  var ix= frmMain.cbAddr.selectedIndex - 1;
  frmMain.sRegion.value = 0;
  if( ix >= 0 && ix < aAdrs.length ) {
    frmMain.sPkt.value= aAdrs[ix][cPT]
    $("input[name=sPkt]").parent().attr('cpkt', aAdrs[ix][cCPT])
    frmMain.sNasPtT.value= aAdrs[ix][cNPt],
    frmMain.sNasPt.value= aAdrs[ix][cNP]
    $("input[name=sUl]").data('urlpar', 'ext='+aAdrs[ix][cCPT])
    frmMain.sUlT.value= aAdrs[ix][cULt]
    frmMain.sUl.value= aAdrs[ix][cUL]
    frmMain.sDom.value= aAdrs[ix][cDOM]
    frmMain.sAdRem.value= aAdrs[ix][cREM]
    frmMain.sRoute.value= aAdrs[ix][cRT]
    $("input[name=sRoute]").parent().attr('croute', aAdrs[ix][cCRT])
    frmMain.sWkHr0.value= num2hhmm(aAdrs[ix][cWH0])
    frmMain.sWkHr1.value= num2hhmm(aAdrs[ix][cWH1])
    frmMain.sWkHr2.value= num2hhmm(aAdrs[ix][cWH2])
    frmMain.sWkHr3.value= num2hhmm(aAdrs[ix][cWH3])
    frmMain.sArea.value= aAdrs[ix][cAR]
    frmMain.sAreaT.value= aAdrs[ix][cArT]
    frmMain.sRegion.value = aAdrs[ix][cRgn];
    set_action_day(I_DLV,  aAdrs[ix][cDlvD])  // delivery day
    set_action_day(I_PHYS, aAdrs[ix][cPhyD])  // phys. zakaz day
    set_action_day(I_ELEC, aAdrs[ix][cEleD])  // electron.zakaz day
    set_action_day(I_RLY,  aAdrs[ix][cRlyD])  // relay  day
    set_action_day(I_MERCH,aAdrs[ix][cMerD])  // merchandiser day
    frmMain.sKrp.value = aAdrs[ix][cKrp];
    $('select[name=cbKrp] option[value='+aAdrs[ix][cKrpTyp]+']').attr('selected', 'selected')
  }
  $("#msg").text('')
}

function onSelectEmp()
{
  var ix = frmMain.cbEmps.selectedIndex - 1
  if(ix >= 0 && ix < aEmps.length) {
      var objEmp = aEmps[ix];
      frmMain.cbEmpPos.value = objEmp['cpos'];
      frmMain.sEmpName.value = objEmp['ename'];
      frmMain.sEmpTel.value = objEmp['tel_wrk'];
      frmMain.sEmpMob.value = objEmp['tel_mob'];
      frmMain.sEmpMail.value = objEmp['mail'];
      frmMain.sEmpBirth.value = objEmp['dbirth'];
      frmMain.sEmpComment.value = objEmp['txt'];
  }else{
      frmMain.cbEmpPos.value = 0;
      frmMain.sEmpName.value = '';
      frmMain.sEmpTel.value = '';
      frmMain.sEmpMob.value = '';
      frmMain.sEmpMail.value = '';
      frmMain.sEmpBirth.value = '';
      frmMain.sEmpComment.value = '';
  }
  $("#msg").text('')
}

function delOrg()
{
  frmMain.cbVid.options[0].selected=true
  frmMain.cbJur.options[0].selected=true
  frmMain.sINN.value= ''
  frmMain.sKPP.value= ''
  frmMain.sAdr.value= ''
  frmMain.sTel.value= ''
  frmMain.sTel2.value= ''
  frmMain.sCtkt.value= ''
  frmMain.sSvi.value= ''
  frmMain.sMail.value= ''
  frmMain.sTxt.value= ''
  frmMain.cbAcc.options[0].selected= true
  frmMain.cbAddr.innerHTML= ''
  aAdrs= []
  $("input[name=sOrg]").parent().attr({org:'', corg: 0, mod: 0})
}

function saveOrg(evt)
{
  var idMain= $("input[name=sOrg]").parent().attr('corg'),
    orgName= (idMain > 0 ? $("input[name=sOrg]").parent().attr('org')
              : frmMain.sOrg.value)
  var pote = (frmMain.sOrg.value.indexOf("поте") > -1)

  // check attr values
  if( frmMain.sOrg.value.length < 2 ) {
    alert('Нет названия клиента'); return
  }
  if (!pote)
  {
    if( frmMain.cbJur.selectedIndex == 0 ) {
      alert('Не выбран юридический тип клиента'); return
    }
    if( frmMain.sINN.value!='' &&
        frmMain.sINN.value.search( /^(\d{10}|\d{12})$/ ) < 0 ) {
      alert('Неверный ИНН : должно быть 10 или 12 цифр'); return
    }
    if( frmMain.sKPP.value != '' &&
      (''+frmMain.sKPP.value).search( /^\d{9}$/ ) < 0 ) {
      alert('Неверный КПП : должно быть 9 цифр'); return
    }
    if( frmMain.sAdr.value.length < 5 ) {
      alert('Не указан юридический адрес клиента'); return
    }
    //if( frmMain.cbJur.selectedIndex == 4 && frmMain.sSvi.value.length < 10 ) {
    //  alert('Не указано свидетельство для ИП'); return
    //}
    if( frmMain.sINN.value!='' && frmMain.sTel.value.length < 5 ) {
      alert('Не указан телефон клиента'); return
    }
  }

  $("div#pbar").show()
  var d= String.fromCharCode(30),  // delim
    aM= frmMain.sINN.value.match( /^(\d{10}|\d{12})$/ ),
    sInn= (aM ? aM[0] : '')
    vid= frmMain.cbVid.selectedIndex>0 ?
      frmMain.cbVid.selectedIndex + (sInn=='' ? 60:0) : '',
    flg= (frmMain.chkSF.checked ? cSF:0)+(frmMain.chkT12.checked ? cT12:0)+
        (frmMain.chkRnUp.checked ? cRnUp:0)+(frmMain.chkRnDn.checked ? cRnDn:0)
        +(frmMain.chkCoP.checked ? cOtb:0)+(frmMain.chkDgB.checked ? cDgB:0),
    acc= frmMain.cbAcc.selectedIndex,
    rly= (frmMain.chkRlyAct.checked ? 1 : 0)+(frmMain.chkRlyUp.checked ? 2 : 0)+
        (frmMain.chkRlyPrc.checked ? 4 : 0) + (frmMain.chkRlyAgE.checked ? 8 : 0),
    hier= frmMain.cbPrHier.selectedIndex,
    fmt= frmMain.cbPrFmt.selectedIndex,
    byGrps= (frmMain.chkPrOrgGr.checked ? PR_BYGRP : 0),
    // build cumulative flag PRLIST using stored iPrL
    // add PAY_OP=null, 17.08.17
    // add PAY_O2=null,  8.11.17
    // add hier missed,  6.11.18
    prl= iPrL - (iPrL & PR_CRE) + acc * PR_CRE0 - (iPrL & PR_HIER) + hier * PR_HIER0
        - (iPrL & PR_RLY) + rly * PR_RLY0
        - (iPrL & PR_BYGRP) + byGrps - (iPrL & PR_FMT) + fmt * PR_FMT0,
    iAgt= frmMain.cbAg.selectedIndex,
    idAg= (iAgt>=0 ? ''+frmMain.cbAg.options[iAgt].value : ''),
    dat= sInn+d+(frmMain.cbJur.selectedIndex-1)+d+
      vid+d+d+flg+d+d+d+d+d+d+idAg+d+d+d+d+d+d+d+frmMain.sKPP.value+d+d+d+
      frmMain.sMail.value+d+d+d+d+d+prl+d+d+d+d+
      orgName+d+frmMain.sAdr.value+d+frmMain.sTel.value+d+frmMain.sTel2.value+
      d+d+frmMain.sCtkt.value+d+frmMain.sSvi.value+d+frmMain.sTxt.value+d+d+d+d
  dat= escape(dat)
  $("#msg").load("mk_org_sav.php", {'par': par, 'id': idMain, 'data': dat},
    function (resp, stat, xmlReq) { // on complete
      if( stat != 'success' )
        alert('status: '+stat+' - '+resp)
      $("div#pbar").hide()
    })
}

function saveAddr(evt)
{
  var idOrg= $("input[name=sOrg]").parent().attr('corg')
  if( idOrg > 0 ) {
    var idRoute= $("input[name=sRoute]").parent().attr('croute'),
      idPkt= $("input[name=sPkt]").parent().attr('cpkt'),
      idUl= $("input[name=sUl]").parent().attr('cul')
    if( !idUl ) idUl= ""
    if( !(idRoute > 0) ) {
      alert('не выбран маршрут'); return
    }
    if( !(idPkt > 0) ) {
      alert('не выбран населенный пункт'); return
    }
    if( frmMain.sNasPt.value.length == 0 ) {
      alert('не указано название населенного пункта'); return
    }
    if( frmMain.sUl.value.length < 2 && (""+idUl).length < 14 ) {
      alert('не указано название улицы'); return
    }
    if( frmMain.sDom.value.length == 0 ) {
      alert('не указан номер дома'); return
    }

    if( frmMain.sArea.value.length == 0 || frmMain.sAreaT.value.length == 0) {
      alert('не указаны площади'); return
    }

    var d= String.fromCharCode(30), // delim
      iAdr= frmMain.cbAddr.selectedIndex,
      idAdr= (iAdr>= 0 ? frmMain.cbAddr.options[iAdr].value : 0),
      iDlvD= make_action_day(I_DLV),
      iPhyD= make_action_day(I_PHYS),
      iEleD= make_action_day(I_ELEC),
      iRlyD= make_action_day(I_RLY),
      iMerchD= make_action_day(I_MERCH),
      iKrp= frmMain.sKrp.value,
      iKrpTyp= $('select[name=cbKrp] option:selected').val(),
      dat=idOrg+d+frmMain.sRegion.value+d+d+idRoute+d+idPkt+d+
        hhmm2num(frmMain.sWkHr0.value)+d+hhmm2num(frmMain.sWkHr1.value)+d+
        hhmm2num(frmMain.sWkHr2.value)+d+hhmm2num(frmMain.sWkHr3.value)+d+
        frmMain.sArea.value+d+frmMain.sNasPtT.value+d+
        frmMain.sNasPt.value+d+ frmMain.sUlT.value+d+ idUl+d+
        frmMain.sDom.value+d+iKrp+d+d+frmMain.sAdRem.value+
        d+d+d+d+d+d+frmMain.sAreaT.value+d+iDlvD+d+iKrpTyp+d+iPhyD+d+iEleD+d+iRlyD+
        d+iMerchD+d
    dat= escape(dat)
    $("div#pbar").show()
    $("#msg").load("mk_orgadr_sav.php", {'par': par, 'id': idAdr, 'data': dat},
      function (resp, stat, xmlReq) { // on complete
        if( stat != 'success' )
          alert('status: '+stat+' - '+resp)
        $("div#pbar").hide()
      })
  }
  else
    alert('не выбран контрагент')
}

function saveEmp(evt)
{
  var idOrg= $("input[name=sOrg]").parent().attr('corg')
  if( idOrg > 0 ) {

      var iEmp = frmMain.cbEmps.value;
      var cPos = frmMain.cbEmpPos.value;
      var sName = frmMain.sEmpName.value;
      var sWTel = frmMain.sEmpTel.value;
      var sMTel = frmMain.sEmpMob.value;
      var sMail = frmMain.sEmpMail.value;
      var sDBirth = frmMain.sEmpBirth.value;
      var sComment = frmMain.sEmpComment.value;

      if(!sName || sName.trim().length == 0){
          alert('Имя контакта в штате должно быть указано');
          return;
      }

      if((!sWTel || sWTel.trim().length == 0) &&
         (!sMTel || sMTel.trim().length == 0) &&
         (!sMail || sMail.trim().length == 0)){
          alert('Хотя бы один из телефонов или e-mail в штате должен быть указан');
          return;
      }

      sWTel = sWTel ? sWTel:'';
      sMTel = sMTel ? sMTel:'';
      sMail = sMail ? sMail:'';
      sDBirth = sDBirth ? sDBirth:'';
      sComment = sComment ? sComment : '';
    
      var d = String.fromCharCode(30);
      var dat = escape(''+iEmp+d+cPos+d+sName+d+sWTel+d+sMTel+d+sMail+d+sDBirth+d+sComment+d+''+d+''+d+''+d+''+d+''+d);
      $("div#pbar").show();

      $("#msg").load("mk_orgemp_sav.php", {'par': par, 'idOrg': idOrg, 'data': dat},
        function (resp, stat, xmlReq) { // on complete
          if( stat != 'success' )
            alert('status: '+stat+' - '+resp)
          $("div#pbar").hide()
      })

      //loadOrg($("input[name=sOrg]"), $("input[name=sOrg]").parent().attr('corg'));
  }
  else
    alert('не выбран контрагент')
}

function loadOrg2(e, idOrg)
{
  var idMainOrg= $("input[name=sOrg]").parent().attr('corg')
  if( idMainOrg > 0 ) {
    e.parent().attr('corg', idOrg)
    frmMain.bnPricesAs.onclick= function() {
      var idMain= $("input[name=sOrg]").parent().attr('corg'),
        idSec= $("input[name=sOrg2]").parent().attr('corg')
      alert('main='+idMain+'  second='+idSec)
      if( idMain > 0 && idSec > 0 ) {
        $("div#pbar").show()
        $("#msg").load("mk_org_cpy.php", {'par': par, 'src': idSec, 'dst': idMain},
          function(resp, stat, xmlReq) {  // on complete
            if( stat != 'success' )
              alert('status: '+stat+' - '+resp)
            $("div#pbar").hide()
          })
      }
      else
        alert('не задан контрагент: источник или получатель')
    }
  }
  else
    alert('Не задан основной контрагент')
}

function delOrg2()
{
  $("input[name=sOrg2]").parent().attr('corg', 0)
  frmMain.bnPricesAs.onclick= undefined
}

function loadPunkt(e, idPunkt)
{
  $(e).parent().attr('cpkt', idPunkt)
  if( frmMain.sNasPt.value.length == 0 ) {
    var pkt= frmMain.sPkt.value, aPar= pkt.split( / +/ )
    if( aPar.length > 1 ) {
      frmMain.sNasPtT.value= aPar[0]
      frmMain.sNasPt.value= aPar[1];
    }
  }
  $("input[name=sUl]").data('urlpar','ext='+idPunkt)
}
function delPunkt()
{
  $("input[name=sPkt]").parent().attr('cpkt', 0)
  $("input[name=sUl]").data('urlpar','ext=0')
}

function loadRoute(e, idRoute)
{
  $("input[name=sRoute]").parent().attr('croute', idRoute)
}
function delRoute()
{
  $("input[name=sRoute]").parent().attr('croute', 0)
}

function loadStreet(e, idStreet)
{
  var aF= $(e).val().split(/ +/)
  $(e).parent().attr('cul', idStreet)
  if( aF.length > 1 )
    frmMain.sUlT.value= aF[aF.length-1]
}

function delStreet()
{
  $("input[name=sUl]").parent().attr({'cul': 0, 'ult': ''})
}

$(document.body).keydown(function(evt) {  // disable Enter as submit
  if( evt.keyCode == 13 )
    evt.returnValue= false
})

$(document.body).ready(function() {
  $("span.a").html('&lowast;')
  $("input[name=sOrg]").parent().attr('corg',0)
  makeBuddy( $("input[name=sOrg]"), {}, "tds", loadOrg, "org_sel_p.php", delOrg)
  $("table td:nth-child(2) span").not(".a").css({'padding-left': '20px'})
  frmMain.bnSave.onclick= saveOrg
  makeBuddy( $("input[name=sPkt]"), {}, "tds", loadPunkt, "pkt_sel.php", delPunkt)
  makeBuddy( $("input[name=sRoute]"), {}, "tds", loadRoute, "rut_sel.php", delRoute)
  makeBuddy( $("input[name=sUl]"), {}, "tds", loadStreet, "str_sel.php", delStreet)
  frmMain.bnAdrSave.onclick= saveAddr
  frmMain.bnEmpSave.onclick= saveEmp

  sOrg.onkeyup = handle;


  $('#sEmpBirth').datepicker({
      monthNames: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
      monthNamesShort: ["Янв", "Фев", "Март", "Апр", "Май", "Июнь", "Июль", "Авг", "Сент", "Окт", "Ноя", "Дек"],
      dayNames: [ "Воскресение", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота" ],
      dayNamesShort: [ "Вск", "Пнд", "Вт", "Ср", "Чт", "Пт", "Сб" ],
      dayNamesMin: [ "Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб" ],
      dateFormat: "dd-mm-yy",
      changeYear: true,
      changeMonth: true,
      firstDay: 1,
      yearRange: "-100:-0",
      nextText: "Следующий месяц",
      prevText: "Предыдущий месяц",
      showMonthAfterYear: true,
      beforeShow: function(input, inst) {
        var cal = inst.dpDiv;
        var top  = $(this).offset().top + $(this).outerHeight();
        var left = $(this).offset().left;
        setTimeout(function() {
            cal.css({
                'top' : top,
                'left': left
            });
        }, 10);
      }
  });
  frmMain.sRegion.value= 0;

<?php
    if (isset($_REQUEST["autoLoad"])) {
      $orgMod= str_replace("'", "\\'", esc_decode($org));
      echo "
        $('input[name=sOrg]').val('$orgMod');
        loadOrg($('input[name=sOrg]'), $idOrg);";
    }
?>
})
</script>
<?php
  } // login is valid
  if( $bFault ) // show authorization dialog
  {
    // show list of users
    if( in_array($_SERVER['SERVER_ADDR'], $ext_server) ) {
      // external site
      save_auth_params($ip, $_SESSION['capt']);
    } else {
      // inner site
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
RaaSoft.<br>Copyright &copy; 2014 - <?php echo date('Y',$mtime);?> 
[Trianon]. All rights reserved. <BR>
Revised: <?php echo date('d.m.Y', $mtime);?>
</body>
</html>

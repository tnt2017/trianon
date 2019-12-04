<?php
// simple list of delivery on date by agent, started 23.03.12
//  5.09.13  add expeditor
// 11.09.13  add modified/without-kk, remark
//  9.10.13  reserve-flg
// 18.10.13  move date when in reserve, base-remark
// 22.11.13  turn bonus: nakl/bill if no KK
// 12.01.14  add ':' ahead of ID to push browser off tel.num
//  5.03.15  add cred-typ col

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
  <title>Доставка по торгпреду</title>
  <link rel=stylesheet type="text/css"  href="default.css">
  <link rel=stylesheet type="text/css"  href="js/dyna_cal.css">
  <link rel="shortcut icon" href="images/favicon.ico">
  <link rel="icon" href="images/favicon.ico">
  <style type="text/css">
    .kk { background-color: #FF8080;}
    .mod {background-color: yellow; }
    .idd { color: brown; cursor: pointer; font-weight: bold; }
    .dat { width: 11ex; }
    .rsv { background-color: #C0C0C0 }
    .dt { background-color: lightblue }
    .tt { background-color: #e5e5e5; text-align: right; }
    .btn_exp { padding: 4px; }

    .doc_row {
        display: block;
        width: 99%;
        border: 1px solid black;
        overflow: hidden;
        text-align: center;
    }
    .doc_row div {
        display: inline-block;
        padding: 4px;
        text-align: center;
        width: auto;
    }
  </style>
  <script src="js/dates.js" ></script>
  <script src="js/jquery-1.12.0.min.js"></script>
  <script src="js/dyna_cal.js" ></script>
  <script src="js/jqlib.js?v=6" ></script>
</head>
<body>
  <div id="page_overlay"></div>
  <div class=c><span class=head> Доставка по торгпреду</span><br>
      [<a href="default.php">Главная</a>]</div>
  <hr>

  <form method=post target=_self  name=frmMain id="frmMain" >
<?php
try {
  $db = new CMyDb();
  $bFault = 0;
  $bRight = isset($_POST['sRight']) ? $_POST['sRight'] : 0;

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
    $idEmp= isset($_REQUEST['cbEmp']) ? $_REQUEST['cbEmp'] : 0;
    $end= isset($_REQUEST['datEnd']) ? $_REQUEST['datEnd'] : "";
    if( !isset($_POST['sRight']) ) // for the 1st time here
    {
      $end= date("j.m.Y", strtotime("+1 day"));
    }

    $arlg= explode("$", $_SESSION['OraLogin']);
    define("EMP_AGENT", 1);
?>
  <input type=hidden name=sRight value='<?php echo $bRight;?>' >
  <script src="js/dyna_calco.js"></script>
  <table border=0 cellspacing=5>
  <tr><td>Дата :</td><td>
      <input name=datEnd class=dat value='<?php echo $end;?>' onChange="this.value=CheckDate(this.value);">
      <img src="images/cal.gif" width=16 height=16 onclick="ds_sh('datEnd')">
      </td><td><input type=submit class=btnS value="Показать" ></td>
  </tr><tr>
    <td>Торгпред :</td><td><select name=cbEmp size=1>
<?php
      $vid= EMP_AGENT;
      $db->parse("begin $arlg[1].DIRS.EMP_LIST(:cur,:vid,:cnt); end;");
      $db->bind(":cur", $cur, OCI_B_CURSOR);
      $db->bind(":vid", $vid, SQLT_INT);
      $db->bind(":cnt", $cnt, SQLT_INT);
      $db->execute();
      $db->execute_cursor($cur); 
      while( $row = $db->fetch_cursor($cur) ) { ///$idEmp
          echo "<option value=$row[ID] ".($_REQUEST[cag]==$row['ID'] ? "selected":""). 
          "> $row[NAME]";
      }
?>
      </select></td>
    <td><span class=kk>клиент</span> (без КК), &nbsp;&nbsp;
      <span class=mod>сумма</span> (вычерки),&nbsp;&nbsp;
      <span class=rsv>номер</span> (резерв),&nbsp;&nbsp;
      <span class=dt>дата</span> (счет)</td>
    </tr>


<tr>
  <td>Контрагент :</td>
  <td>
  <div class="easy-autocomplete" style="width: 400px;">

  <?php // echo esc_decode($_REQUEST['sOrg']); ?>

    <input name=sOrg value="" class=org autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" id="eac-5912">
    <div class="easy-autocomplete-container" id="eac-container-eac-5912"><ul style="display: none;"></ul></div></div>
  </td>

</tr>
  </table>

<?php
      if( isset($_POST['sRight']) ) // 2nd+
      {
        $db->parse("begin $arlg[1].DELIV.LIST_AGENT_TRIPS(:cur,".
            "to_date(:dt,'dd.mm.yyyy'),:emp); end;");
        $db->bind(":cur", $cur, OCI_B_CURSOR);
        $db->bind(":emp", $idEmp, SQLT_INT);
        $db->bind(":dt",  $end,  SQLT_CHR);
        $db->execute();
        $db->execute_cursor($cur);
?>

<script type="text/javascript">
  var par="<?php echo sencode($_SESSION['OraLogin']).'g0'.sencode($_SESSION['OraPwd']);?>"
  var cID=0, cNO=1,cTYP=2, cDAT=3, cORG=4,cADR=5,cSUM=6,cREM=7,cRM2=8,cAUT=9,
  cALL=10
  var eNom, eDat,eRem, vE;
  var ids = '';


  var n= frmMain.cbEmp.options.length
          for(i=0; i < n; i++) {
            if( frmMain.cbEmp.options[i].value == <?php echo $_REQUEST[cag]; ?> ) {
              frmMain.cbEmp.selectedIndex= i; break }
          }


function onId(ev) {
  var td= ev.target, aTd= td.parentNode.children,
    org= escape(aTd[cORG].textContent),
    nom= escape(aTd[cNO].textContent), dat= aTd[cDAT].textContent,
    adr= escape(aTd[cADR].textContent), id= td.textContent.substr(1),
    mails = escape(td.getAttribute("mail"))
  window.open("doc_lines.php?id="+id+"&force_old=1&nom="+nom+"&dt="+dat+
        "&&org="+org+"&adr="+adr+"&mls="+mails)
}

function onNom(ev)
{
  var td= ev.target, bRsv= td.className.indexOf("rsv") >= 0 ? 1:0
  if( confirm("Хотите перевести док-т "+(bRsv ? "из резерва?":"в резерв?")) ) {
    var idd= td.parentNode.firstElementChild.textContent.substr(1)
    eNom= td
    $("#msg").load("ag_deliv_rsv.php", {'par': par, id: idd, mode: 0},
      function(resp,stat,xmlReq) {  // complete
        if( stat == "success" && (""+resp).match( /^\s*$/ ) ) {
            eNom.className= (bRsv ? "":"rsv")
        }
        eNom= null
      })
  }
}

function onDat(ev)
{
  var td= ev.target, dt0= td.textContent, aD= dt0.split("."),
  dt= aD[0]+"."+aD[1]+"."+(2000+parseInt(aD[2]))
  var ret= prompt('Введите новую дату док-та (больше текущей):', dt)
  if( ret ) {
    var dta= td.parentNode.firstElementChild.textContent.substr(1)+";"+ret
    eDat= td
    vE= ret
    $("#msg").load("ag_deliv_rsv.php", {'par': par, id: dta, mode: 2},
      function(resp,stat,xmlReq) {  // complete
          if( stat == "success" && (""+resp).match( /^\s*$/ ) ) {
            eDat.textContent= vE
          }
          eDat= null
      })
  }
}

function onDeliv(ev)
{
  var td= ev.target,isBill= td.className.indexOf("dt") >= 0 ? 1:0,
  ret= confirm('Хотите '+(isBill ? 'поставить на доставку':'снять с доставки')+'?')
  if( ret ) {
    var idd= td.parentNode.firstElementChild.textContent.substr(1)
    $("#msg").load("ag_deliv_rsv.php", {"par": par, id: idd, mode: 3, vk: isBill},
      function(resp,stat,xmlReq) {  // complete
        if( stat != "success" )
          alert("Error: "+stat+" : "+resp)
        else {
          td.className= (isBill ? "":"dt")
        }
      })
  }
}

function onSum(ev)
{
  var td= ev.target
  if( confirm("Хотите выставить цены в документе?") ) {
    var idd= td.parentNode.firstElementChild.textContent.substr(1)
    $("#msg").load("ag_deliv_rsv.php", {'par': par, id: idd, mode: 1},
      function(resp,stat,xmlReq) {  // complete
        if( stat != "success" )
          alert("Error: "+stat+" : "+resp)
      })
  }
}

function onRem(ev)
{
  var td= ev.target, atd=td.parentNode.children, nom= atd[cNO].textContent,
  r= prompt("Введите новое примечание ("+nom+")", td.textContent)
  if( r ) {
    var idd= atd[cID].textContent.substr(1)
    eRem= td
    vE= r
    $("#msg").load("ag_deliv_rem.php", {'par': par, 'id': idd, 'rm': escape(r), 'ix':1},
      function(resp,stat,xmlReq) {
        if(stat != "success") alert("remark save error: "+resp)
        else eRem.textContent= vE
        eRem= null
      })
  }
}

function onRemDeliv(ev)
{
  var td= ev.target, atd=td.parentNode.children, nom= atd[cNO].textContent,
  r= prompt("Введите новое примечание для доставки ("+nom+")", td.textContent)
  if( r ) {
    var idd= atd[cID].textContent.substr(1)
    eRem= td
    vE= r
    $("#msg").load("ag_deliv_rem.php", {'par': par, 'id': idd, 'rm': escape(r)},
      function(resp,stat,xmlReq) {
        if(stat != "success") alert("remark save error: "+resp)
        else eRem.textContent= vE
        eRem= null
      })
  }
}


$(document.body).ready(function() {

 
  var n= frmMain.cbEmp.options.length
          for(i=0; i < n; i++) {
            if( frmMain.cbEmp.options[i].value == <?php echo $_REQUEST[cag]; ?> ) {
              frmMain.cbEmp.selectedIndex= i; break }
          }
  alert('cbEmp setting');
 

  var cts= document.getElementById('cts'), atr= cts.children
  for(var i=0; i < atr.length; i++) {
    var atd= atr[i].children
    if( atd.length == cALL ) {
      atd[cID].className= "idd"
      atd[cID].onclick= onId
      if( atd[cORG].className.indexOf("kk") >= 0 ) {
        atd[cNO].style.cursor= "pointer"
        atd[cNO].onclick= onNom
        atd[cSUM].style.cursor= "pointer"
        atd[cSUM].onclick= onSum
      }
      if( atr[i].getAttribute("tip")==2 && atd[cTYP].className.indexOf("rsv") >= 0) {
        atd[cDAT].style.cursor= "pointer"
        atd[cDAT].onclick= onDat
      }
      if( atr[i].getAttribute("tip")==6 ) {
        atd[cDAT].style.cursor= "pointer"
        atd[cDAT].onclick= onDeliv
      }
      atd[cREM].style.cursor= "pointer"
      atd[cREM].onclick= onRem
      atd[cRM2].style.cursor= "pointer"
      atd[cRM2].onclick= onRemDeliv
    }
  }
  $('.btn_exp').click(function(e) {
    id = $(this).attr('id');
    file_type = 10;
    switch(id) {
        case 'btn_exp':
            file_type = 10;
            break;
        case 'btn_exp_csv':
            file_type = 17;
            break;
    }
    $('#exp_files').html('<hr/><p>Пожалуйста подождите...</p><img src="images/progbar-1.gif">');
    $.ajax({
      url: 'doc_export.php',
      type: 'POST',
      dataType: 'json',
      cache: false,
      async: true,
      data: {
          multi: 1,
          file_type: file_type,
          b_id_doc: ids
      },
      error: function(xhr, textStatus, errorThrown) {
          var errorText = '<b style="color: red; font-weight:bold">Произошла ошибка</b>:<br />';
          errorText += '<p align="left">' + (textStatus ? textStatus +'<br />': '') + (errorThrown ? errorThrown : '') + '</p>';
          $('#exp_files').html(errorText);
      },
      success: function(result) {
          if(result.code == 'success') {
              var errorText = '<p align="left">' + (result.message ? result.message : '') + '</p>';
              var fc = 0;
              for(var idx  in result.files) {
                  file = result.files[idx];
                  errorText += ((fc > 0 && fc%4 == 0) ? '<br />' : '') + '<a href="' + file.href + '">' + file.text + '</a>&nbsp;&nbsp;&nbsp;';
                  fc++;
              }
              $('#exp_files').html(errorText);
          } else {
              var errorText = '<b style="color: red; font-weight:bold">Произошла ошибка</b>:<br />';
              errorText += '<p align="left">' + (result.message ? result.message : '') + '</p>';
              $('#exp_files').html(errorText);
          }
      }
    }) // ajax
  })

  // from bottom
}) // ready

</script>

  <p style="font-size: 70%">NB. при нажатии на<br>
      1) ID док-та - он открывается в отдельном окне, &nbsp;&nbsp;
      2) номер - при отсутствии КК можно снять/поставить в резерв, &nbsp;&nbsp;
      3) дата - для резерва можно ее передвинуть, для бонуса без КК - в счет/накл<br>
      4) сумма - при отсутствии КК возможность выставить цены в док-те, &nbsp;&nbsp;
      5) оба примечания - изменить примечание
  </p>
  <div id=msg></div>
  <input type="button" id="btn_exp" class="btn_exp" value="Скачать все файлы (Excel-основной 2)">
  <input type="button" id="btn_exp_csv" class="btn_exp" value="Скачать все файлы (CSV-основной 2)">
  <div id="exp_files"></div>
    <p><table border="1" cellspacing="2" width="99%">
    <tr>
        <th> id
        <th> номер
        <th> тип
        <th> дата
        <th> клиент
        <th> адрес
        <th> сумма
        <th> примечание
        <th> для доставки
        <th> автор
    </tr>
    <tbody id=cts>
<?php
      $idTrip = 0;
      $sumTrip = 0; $nDocs = 0;
      $ids = '';
      $class = '';
      while( $row = $db->fetch_cursor($cur) )
      {
        if( $idTrip != $row['CTRIP'] ) { // header
            if( $idTrip > 0 )   // footer
                echo "<tr class=tt><td colspan=6>Итого по <b>$nDocs</b> документам :".
                "<td class=rb>".round($sumTrip,2)."</td><td colspan=4>&nbsp;</td></tr>";
            echo "<tr>".
            "<td colspan=10 class=cb> $row[ROUTE] - $row[DT] ($row[CTRIP])  /  $row[EXPED]".
            "</tr>";
            $idTrip = $row['CTRIP'];
            $sumTrip = 0;  $nDocs = 0;
        }
        $ids .= (strlen($ids) > 0 ? ',' : '').$row['ID'];
        $mod = $row['FLG'] & 1 ? " mod":"";
        $kk = $row['FLG'] & 2 ? "class=kk":"";
        $rsv = $row['FLG'] & 4 ? "class=rsv":"";
        $bill = $row['FLG'] & 8 ? "class=dt":"";

        $json_data = json_encode(array('CTRIP' => $row['CTRIP'], 'ID' => $row['ID'],
          'NNAKL' => iconv('cp1251', 'utf-8', $row[NNAKL]),
          'ORG' => iconv('cp1251', 'utf-8', $row['ORG']), 'MAIL' => $user_mails));

        echo <<<ROWL
<tr f="$row[FLG]" tip=$row[TIP] data-row="$json_data">
  <td >:$row[ID]</td>
  <td $rsv>$row[NNAKL]</td>
  <td $rsv>$row[CREDTYP]</td>
  <td $bill>$row[DNAK]</td>
  <td $kk>$row[ORG]</td>
  <td>$row[ADR] ($row[CADR])</td>
  <td class="r$mod">$row[SUMM]</td>
  <td>$row[RM]</td>
  <td>$row[REM2]</td>
  <td>$row[AUTHOR]</td>
</tr>
ROWL;
        $sumTrip += $row['SUMM'];
        $nDocs++;
    }
    // footer
    echo "<tr class=tt><td colspan=6>Итого по <b>$nDocs</b> документам :".
    "<td class=rb>".round($sumTrip,2)."</td><td colspan=3>&nbsp;</td></tr></tbody>";
    echo "</table>\n";
?>

<script type="text/javascript">
    ids = <?php echo "'".$ids."'";?>





</script>
<?php
      } // 2nd+
  } // login is valid
  if( $bFault ) // show authorization dialog
  {
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
<br>Copyright &copy; <?php echo date('Y')?> [Trianon]. All rights reserved. <BR>
Revised: <?php echo date('d.m.Y', filemtime(basename($_SERVER['SCRIPT_NAME'])))?>

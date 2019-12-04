<?php
session_start();
require_once "orasql.php";
require_once "utils.php";
require_once "auth_chk.php";
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Language" content="ru">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<style  type="text/css">

#main {margin:0 auto; width: 300px;}
#search {border: 1px solid #888;width:300px;height:24px;padding:0;}
#poisk {border:0px;outline:none;width:270px;height:24px;padding-left:5px;box-sizing: border-box;float:left;font-size:18px;}
#otmena {float:left;width:24px;height:24px;font-size:20px;display:none; color:#777;}
#krest {height: 20px; width:20px;padding-top:-5px;}

.center {
    width: 200px; /* Ширина элемента в пикселах */
    padding: 10px; /* Поля вокруг текста */
    margin: auto; /* Выравниваем по центру */
    background: #fc0; /* Цвет фона */
   }

@media screen and (max-width:1000px)
{
#main {width: 100%; margin:0;}
#search {width:100%;margin:0;}
#poisk {width: calc(100% - 24px);padding-left:0;}
}

</style>

<script language=javascript>

var globalObj, globalBasket=JSON.parse("[]"); 

var globalI=0;

function handle(object){
     var inp = document.createElement("input");
     inp.type = "text";
     inp.value = object.innerText;
     inp.size=3;

     object.innerText = "";
     object.appendChild(inp);
     
     var _event = object.onclick;
     object.onclick = null;

     inp.onkeydown = function(e){

      alert(e.keyCode);

         if(e.keyCode == 13) //=
         {
               object.innerText = inp.value;

               alert(object.innerText);
               //alert(object.nextSibling.innerText);

               object.onclick = _event;
               object.removeChild(inp);
         }

     };

     inp.onblur = function(e)
     {       
      object.innerText = inp.value;
               object.onclick = _event;
               object.removeChild(inp);
     };
}


function fokusA(){
  document.getElementById("search").style.borderWidth='2px';
};
function fokusB(){
  document.getElementById("search").style.borderWidth='1px';
};
function vvod(){
  if (document.getElementById("poisk").value.length>0) {
    document.getElementById("otmena").style.display="block";
  }  
  else 
  {document.getElementById("otmena").style.display="none";}
};
function steret() {
  document.getElementById("poisk").value="";
  document.getElementById("otmena").style.display="none";
  alert('убираем вьюху поиска');
  $("div#info_poisk").hide();
  $("div#info_cats").show();
};



function onMainScroll()
{
  //debugger;

  var offTop= document.body.scrollTop,
    oDiv= document.getElementById('fxbtn')

   //alert(window.pageYOffset);

   if(window.pageYOffset>800)
   {
    //$(oDiv).offset({top:30, left:100})

    $(".fxbtn").css({"top":"5px", "left":"0"})
    $(oDiv).css('visibility','visible')
   }
   else
   {
    $(oDiv).css('visibility','hidden')
   }

    
  /*if( oDiv ) {
    var vis=  $(oDiv).css('visibility') //oDiv.style.visibility
    if( vis == "hidden" && offTop > nScrollAnchor )
      //oDiv.style.visibility= "visible"
      $(oDiv).css('visibility','visible')
    else if( vis != "hidden" && offTop <= nScrollAnchor )
      //oDiv.style.visibility= "hidden"
      $(oDiv).css('visibility','hidden')
  }*/
}
 

</script>


<title>Заказ клиента</title>
<link rel=stylesheet type="text/css" href="default.css">
<link href="lightbox.css" rel="stylesheet" />
<link rel="stylesheet" href="tnt_my_styles.css">

</style>
<script src="js/dates.js" ></script>
<script src="js/jqlib.js" ></script>
<script src="js/dyna_cal.js" ></script>
<script src="js/dyna_calco.js"></script>
<script src="tnt_table_search.js" ></script>

<!-- Autocomplete -->
<!-- JS file -->
<script src="main/js/easyAutoComplete/jquery.easy-autocomplete.js"></script> 

<!-- CSS file -->
<link rel="stylesheet" href="main/js/easyAutoComplete/easy-autocomplete.min.css">
<link rel="stylesheet" href="main/js/dyna_cal.css">
</head>



<body>





<div id=fxbtn >
<table cellspacing=3>
<tr>
  <td><img src="images/uparr.png"></img></td>
  <td><input type='button' value='в корень'> </td>
  <td><input type='button' value='наверх'></td>
 </tr>
</table>
</div>



<div class=c>
 <H2>[<a href="default.php">Главная</a>]</H2>
</div>
 
<button onclick="getLocation()">Получить координаты</button>
<script>
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else { 
        x.innerHTML = "Geolocation is not supported by this browser.";
    }
}

function showPosition(position) {
    $("input[name=coords]").val(position.coords.latitude + ", " + position.coords.longitude);
}
</script>




<form method=post target=_self  name=frmMain >

<?php
try {
  $db= new CMyDb();
  $bFault= 0;
  $bRight= isset($_POST['sRight']) ? $_POST['sRight'] : 0;
  $ip= $_SERVER['REMOTE_ADDR'];

  //if( auth_check( $ip, $_SESSION['OraLogin'],$_SESSION['OraPwd']) )
  //   exit(1);

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
    //$bRight = 1;for testing
  }
  if( !$bFault && $bRight ) // login is valid, right is granted
  {
    $dtOn= isset($_REQUEST['datOn']) ? $_REQUEST['datOn'] : "";
    $idOrg= isset($_REQUEST['corg']) ? $_REQUEST['corg'] : 0;
    $org= isset($_REQUEST['sOrg']) ? esc_decode($_REQUEST['sOrg']) : "";
    if( !isset($_POST['sRight']) ) // for the 1st time here
    {
      $wday= date("w");  // 0-6 sun-sat, to get next work day as dtOn
      $add= 1;
      if( $wday == 5 ) $add= 3;
      elseif( $wday == 6 ) $add= 2;
      $dtOn= date("j.m.Y", strtotime("+$add day"));
    }
    $period = isset($_REQUEST['nPeriod']) ? $_REQUEST['nPeriod']: 30;
    
    $dtStart = date("j.m.Y", strtotime($dtOn." -$period day"));
?>
<input type=hidden name=sRight value='<?php echo $bRight;?>' >
<input type=hidden name=corg value=<?php echo $idOrg;?> >
<input type=hidden name=cfirm value="" >
<input type=hidden name=clocal value="" >

<script language=javascript>
var par="<?php echo sencode($_SESSION['OraLogin']).'g0'.sencode($_SESSION['OraPwd']);?>"
var eKol, eKolA, iPane=0,  // 0 tovs, 1 zakaz
  aGrp= new Array(),
  cNN=0, cPIC=1, cCMC=2, cNAME=3, cStat = 4,cKOL=5, cOST=6, cPRC=7, cMONITOR=8, cDISC=9,cMDIS=10,cGRP=9,
  nScrollAnchor= 30,
  cFxSum= 3;
var ipadr= "<?php echo $_SERVER['REMOTE_ADDR']?>", usr= "", idDocMade=0;
var cntZakLocal = 0;
var dtOn = '<?php echo $dtOn; ?>';
var dtStart = '<?php echo $dtStart; ?>';
var period = <?php echo $period; ?>;

</script>

<script src="tnt_postat_pozalist.js"></script>
<script src="tnt_korzina.js"></script>

<script language=javascript>
 

function getTS()
{
    now = new Date();
    yyyy = now.getFullYear()
    mm = now.getMonth() + 1
    dd = now.getDate()
    hh = now.getHours()
    mi = now.getMinutes()
    ss = now.getSeconds()

    if (mm.toString().length < 2)
	mm = "0" + mm
    if (dd.toString().length < 2)
	dd = "0" + dd
    if (hh.toString().length < 2)
	hh = "0" + hh
    if (mi.toString().length < 2)
	mi = "0" + mi
    if (ss.toString().length < 2)
	ss = "0" + ss

	return yyyy + "-" + mm + "-" + dd + " " + hh + ":" + mi + ":" + ss
}

function openDispDlg( e, a )
{
  var off= e.offset(), x= off.left - 50, y= off.top + e.height() + 10, dlg= $("div#dispDlg")
  dlg.css({left: x, top: y}).show()
  $('input[name=closeDisp]', dlg).on("click", function () {
    dlg.hide()
    $('input[name=closeDisp]', dlg).off("click")
  })
}


		/*$(document).ready(function() 
		{
			$("a.gallery, a.iframe").fancybox();
			url = $("a.modalbox").attr('href').replace("for_spider","content2");
			$("a.modalbox").attr("href", url);	
			$("a.modalbox").fancybox(
			{								  
			"frameWidth" : 400,	 
			"frameHeight" : 400 
								  
			});
		});*/

 

$(document.body).ready(function() {

 
  s = localStorage.getItem("stored")
  if (typeof s !== 'undefined' && s !== null)
  {
    s = s.split(';')
    txt = "<option value='0'>локальные заявки ("+((s.length-1) >= 0 ? (s.length-1) : 0)+")</option>"
    for (var i=0; i<s.length - 1; i++)
    {
      t = s[i].split("^")
      txt += "<option value='"+s[i]+"'>"+t[0]+": "+t[2]+"</option>"
      cntZakLocal ++
    }
    $('select[name=locals]').html(txt)
    $('select[name=locals]').on('change', function() {
      if ($('option:selected', this).val() !== '0')
      {
	$('input[name=clocal]').val($('option:selected', this).val())
	
	if(typeof(Storage) !== "undefined") {
	    i = $('input[name=clocal]').val()
	    enterOrgStorage($(this), localStorage.getItem(i + "~~" + "corg"), i)
	} else {
	    alert('локальное сохранение не поддерживается вашим браузером')
	}
      }
    })
  }
  else
  {
    txt = "<option value='0'>локальные заявки (0)</option>"
    $('select[name=locals]').html(txt)
  }
  
  


  $("div#kolDlg").hide()
  $("input[name=datOn]").addClass('dat')
  $(".off").next().addClass('offn')
  
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
                                    //alert('onChooseEvent');

                                    function sleep (time) 
                                    {
                                      return new Promise((resolve) => setTimeout(resolve, time));
                                    }
                                    sleep(500).then(() => 
                                    {
                                    //выбираем иерархию=категории
                                    $("#cbHier [value='2']").attr("selected", "selected");
                                    cbHierChange();

                                        sleep(500).then(() => 
                                        {
                                        });
                                     //LoadTovList();
                                    });
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

//   makeBuddy( $("input[name=sOrg]"), {}, "tds", enterOrg, "org_sel_p.php", deleteOrg)
  $("select[name=cbGruz], select[name=cbAddr], input[name=sRem], input[name=sTov]")
  .addClass('org')
  $("div#tov_sec, div#zak_sec").hide()
  $("table#props td:nth-child(3)").css({'padding-left' : '20px'})
  $("input[name=bnToCart], input[name=bnToCart2]").on('click',function() {
    $("div#tov_sec").hide()
    $("div#zak_sec").show()

    //globalBasket.push( globalObj[5] );  // тестим добавление в корзину

    RenderBasket(globalBasket);
  })
  $("input[name=bnToTov], input[name=bnToTov2]").on('click',function() {
    $("div#zak_sec").hide()
    $("div#tov_sec").show()
  })

  $("select[name=cbDoc]").change(changeDoc)

  $("img#dn1").on('click',function() {
    $("div#hdr_sec").show()
    $("img#up1").show()
    $("img#dn1").hide()
  })

  $("img#up1").on('click',function() {
    $("div#hdr_sec").hide()
    $("img#dn1").show()
    $("img#up1").hide()
  })
  
  $("div#hdr_sec").hide()

    $("div#enldlg0").hide();
    $("div#enldlg1").hide();
    $("div#enldlg2").hide();
    $("div#enldlg3").hide();
    $("div#enldlg4").hide();
    $("div#enldlg5").hide();

  $("input[name=bnNew]").on('click',clearZakaz)
   $("input[name=bnSaveL]").on('click',function() {
    if(typeof(Storage) !== "undefined") {
	if ($('input[name=corg]').val().length > 0)
	{
	    nw = ''
	    if (typeof $('input[name=clocal]').val() !== 'undefined' && $('input[name=clocal]').val().length > 0)
	    {
		s = localStorage.getItem("stored")
		s = s.split(';')
		for (var i=0; i<s.length - 1; i++)
		{
		    t = s[i].split("^")
		    
		    if ($('input[name=corg]').val() == t[1] && $('input[name=clocal]').val() == s[i])
		    {
			nw = s[i]
			break
		    }
		}
	    }
	    
	  if (nw.length == 0 || nw === '')
	  {
		  ts = getTS()
		  cur = localStorage.getItem("stored")
		  nw = ts + "^" + $('input[name=corg]').val() + "^" + $('input[name=sOrg]').val()

		  st = nw + ";" + (typeof cur !== 'undefined' && cur !== null ? cur : '')
		  localStorage.setItem("stored", st)
		  $('input[name=clocal]').val(nw)
  		cntZakLocal ++
    }
    

    localStorage.setItem(nw + "~~" + "corg", $('input[name=corg]').val())
    localStorage.setItem(nw + "~~" + "sOrg", $('input[name=sOrg]').val())
	    localStorage.setItem(nw + "~~" + "cbDoc", $('select[name=cbDoc] option:selected').val())
	    localStorage.setItem(nw + "~~" + "cbGruz", $('select[name=cbGruz] option:selected').val())
	    localStorage.setItem(nw + "~~" + "cbAddr", $('select[name=cbAddr] option:selected').val())
	    localStorage.setItem(nw + "~~" + "sZakNo", $('input[name=sZakNo]').val())
	    localStorage.setItem(nw + "~~" + "cbHier", $('select[name=cbHier] option:selected').val())
	    localStorage.setItem(nw + "~~" + "datOn", $('input[name=datOn]').val())
	    localStorage.setItem(nw + "~~" + "cbCre", $('select[name=cbCre] option:selected').val())
	    localStorage.setItem(nw + "~~" + "summ", $('td#summ').html())
	    localStorage.setItem(nw + "~~" + "agt", $('span#agt').html())
	    localStorage.setItem(nw + "~~" + "cag", (typeof $('span#agt').attr('cag') !== 'undefined' ? $('span#agt').attr('cag') : ''))
	    localStorage.setItem(nw + "~~" + "sRem", $('input[name=sRem]').val())
	    localStorage.setItem(nw + "~~" + "chkOst0", $('input[name=chkOst0]').attr('checked'))
	    localStorage.setItem(nw + "~~" + "sTov", $('input[name=sTov]').val())
	    localStorage.setItem(nw + "~~" + "chkSF", $('input[name=chkSF]').attr('checked'))
	    localStorage.setItem(nw + "~~" + "chkRsv", $('input[name=chkRsv]').attr('checked'))
	    localStorage.setItem(nw + "~~" + "chkBoN", $('input[name=chkBoN]').attr('checked'))
	    localStorage.setItem(nw + "~~" + "chkSmv", $('input[name=chkSmv]').attr('checked'))
	    localStorage.setItem(nw + "~~" + "chkAdd", $('input[name=chkAdd]').attr('checked'))
	    localStorage.setItem(nw + "~~" + "costLn", $('span#costLn').html())
	    localStorage.setItem(nw + "~~" + "nacZak", $('span#nacZak').html())
	    
      z = JSON.stringify(globalBasket);
      
      /*for(var i=0;i<globalBasket.length;i++) ////////////// мое сохранение локальной заявки в корзину 03-12-2019
      {
        z += JSON.stringify(globalBasket) + "^"
      }*/

      

	    //   $('table tbody#zakaz > tr').each(function() {
	    //	z += $(this)[0].outerHTML + "^"
	    //   })
      // mod^#^pic^id^nam^kol^ost^ini^prc^b^discount^v^m^maxDiscount^grpDiscount^


      localStorage.setItem(nw + "~~" + "zakaz", z)
	    
	    //alert('заявка сохранена локально')
	    if (typeof ts !== 'undefined' && ts.length > 0)
	    {
		s = localStorage.getItem("stored")
		txt = "<option value='0'>локальные заявки ("+cntZakLocal+")</option>"
		if (typeof s !== 'undefined' && s !== null)
		{
		  s = s.split(';')
		  for (var i=0; i<s.length - 1; i++)
		  {
		    t = s[i].split("^")
		    txt += "<option value='"+s[i]+"'>"+t[0]+": "+t[2]+"</option>"
		  }
		}
		$('select[name=locals]').html(txt)
		$('select[name=locals]').on('change', function() {
		  if ($('option:selected', this).val() !== '0')
		  {
		    $('input[name=clocal]').val($('option:selected', this).val())
		    
		    if(typeof(Storage) !== "undefined") {
			i = $('input[name=clocal]').val()
			enterOrgStorage($(this), localStorage.getItem(i + "~~" + "corg"), i)
		    } else {
			alert('локальное сохранение не поддерживается вашим браузером')
		    }
		  }
		})
	    }
	}
	else
	{
	    alert('выбирите контрагента')
	}
    } else {
	alert('локальное сохранение не поддерживается вашим браузером')
    }
  })
  $("input[name=bnDelL]").on('click',function() {
    if(typeof(Storage) !== "undefined") {
	localStorage.clear()
	
	cntZakLocal = 0
	
	txt = "<option value='0'>локальные заявки (0)</option>"
	$('select[name=locals]').html(txt)
    } else {
	alert('локальное сохранение не поддерживается вашим браузером')
    }
  })

  function cbHierChange()
  {
    //alert('cbHier change')
    var idx= frmMain.cbHier.selectedIndex,
      idGrp= (idx==1 ? 200001 : (idx==2 ? 288125 : 0))
    loadTovs(idGrp,0)
    $("#grpname").text(". ").attr('idval',idGrp)
    aGrp.size= 0
  }
  

  $("select[name=cbHier]").change(function() {
    cbHierChange();
  })


  $("img#bnLsTovs").on('click',function() 
  {
    $("#grpname").text(". ").attr('idval',0)
    aGrp.size= 0
    //alert('вызов из поиска');
    loadTovs(-1,0,'div#info_poisk');
  })
  
  //$("input[name=bnProZak]").on('click',loadTovs_Statistic); ///// заказ по статистике 
  //$("input[name=bnZalZak]").on('click',loadTovs_Zalistovka); /// заказ по залистовке

  //$("#dn1").hide()
  // digit pad, use eKol
  $("#bn01").on('click',function() { eKol.text(eKol.text()+'1') })
  $("#bn02").on('click',function() { eKol.text(eKol.text()+'2') })
  $("#bn03").on('click',function() { eKol.text(eKol.text()+'3') })
  $("#bn04").on('click',function() { eKol.text(eKol.text()+'4') })
  $("#bn05").on('click',function() { eKol.text(eKol.text()+'5') })
  $("#bn06").on('click',function() { eKol.text(eKol.text()+'6') })
  $("#bn07").on('click',function() { eKol.text(eKol.text()+'7') })
  $("#bn08").on('click',function() { eKol.text(eKol.text()+'8') })
  $("#bn09").on('click',function() { eKol.text(eKol.text()+'9') })
  $("#bn00").on('click',function() { eKol.text(eKol.text()+'0') })
  $("#bnCa").on('click',function() { eKol.text('') })
  $("#bnDe").on('click',function() { eKol.text(eKol.text()+'.') })
  $("#bnEx").on('click',function() {  // append to zakaz and hide
    updateZakaz()
    $("div#kolDlg").hide()
    $('input[name=bnSaveL]').click();
  })

  var nScrollAnchor= 30 + $("input[name=bnOpenCredHist]").offset().top
  document.onscroll= onMainScroll
  $("div#fxbtn td:eq(0)").on('click',function() {
    $("div#fxbtn").css('visibility','hidden')
    $(document.body).scrollTop(0)
  })
  $("div#fxbtn td:eq(1)").on('click',function() { // to root-dir
    $("div#fxbtn").css('visibility','hidden')
    $(document.body).scrollTop(nScrollAnchor-30)
    toRootDir()
  })
  $("div#fxbtn td:eq(2)").on('click',function() { // to up-dir
    $("div#fxbtn").css('visibility','hidden')
    $(document.body).scrollTop(nScrollAnchor-30)
    if( $("tbody#tovs tr").length > 1 ) {
      var eTov= $("tbody#tovs tr:eq(1) td:eq(2)")
      toUpDir(eTov)
    }
  })
  $("div#fxbtn td:lt(3)").css({'width': '14ex'})
//   $("div#fxbtn td:lt(3):hover").css({'width': '14ex','color': 'green',
//           'font-weight': 'bold','cursor': 'pointer'})
  $("div#fxbtn td:eq(3)").css({'width':'auto','padding-left': '10px'})

  //alert('вкл/выкл залистовки=' +   $('input[name=radioName]:checked', '#frm_zalist').val());   


  $("select[name=cbGruz]").on('click', function() {
    alert('111');
    var idx= frmMain.cbHier.selectedIndex,
      idGrp= (idx==1 ? 200001 : (idx==2 ? 288125 : 0))

    alert('вызов1');
    loadTovs(idGrp,0)
    $("#grpname").text(". ").attr('idval',idGrp)
    aGrp.size= 0
  });


  $("#div1_ok").on('click', function() 
  { 
	  alert('залистовка id=' + $("#div1_id").val());   
	  eKol.removeClass('glyphicon-star-empty');  
	  eKol.addClass('glyphicon-star'); 
	  addToZaList($("#div1_id").val()) 
	  //removeFromZaList($("#div1_id").val()) 
  } );

  $("#div2_ok").on('click', function() { eKol.text($("#div2_vvod").val()); 
                                         $("#div2_vvod").val(""); 
                                         HideDiv2() });

  $("#div3_ok").on('click', function() { MakeWaitList()  } );

  //$("#btn_wait_list").on('click', function() { load_waitlist() }) ;

  $("#div4_ok").on('click', function() { SetPrice(); alert('цена=' + $("#div4_price").val()); $("#div4_price").val("")  } );

  $("#div5_ok1").on('click', function() { 
    
  alert('div5_ok1');
  var dg=parseFloat($("#div5_discount_grp").val());
  var md=parseFloat($("#div5_max_discount").val());

  //alert(dg);
  //alert(md);

    if(dg > md)    
    {
        alert('скидка превышает максимальную');        
    }
    else
    {
      SetGroupDiscount(1,frm_discount.div5_discount_grp.value); //  на группу
    } 
  } );



  $("#div5_ok2").on('click', function() { 
    
    alert('div5_ok2');
  
    var dg=parseFloat($("#div5_discount_grp").val());
    var md=parseFloat($("#div5_max_discount").val());
  
    //alert(dg);
    //alert(md);
  
      if(dg > md)    
      {
          alert('скидка превышает максимальную');        
      }
      else
      {
        SetGroupDiscount(2,frm_discount.div5_discount_tovar.value); //  на товар
      } 
    } );



  $("#div5_new_price").on('change', function() { 

    var base=$("#div5_price_b").val();
    var price=$("#div5_new_price").val();
    var smp=$("#div5_smp").val();

    if(parseFloat(price)<parseFloat(smp))
    {
      alert('Цена меньше СМЦ');
      return;
    }

    var disc=(1-parseFloat(price)/parseFloat(base))*100 ;
    disc=disc.toFixed(2);
    $("#div5_discount_grp").val(disc);
    $("#div5_discount_tovar").val(disc);
  });



  $("#div5_discount_grp").on('change', function() { 
  
  var base=$("#div5_price_b").val();
  var disc=$("#div5_discount_grp").val();
  var maxdisc=$("#div5_max_discount").val();
  var new_price=base/100*(100-disc);
  
  if(disc>maxdisc)
  {
    alert('Скидка превышает максимальную');
    $("#div5_discount_grp").val("");
    return;
  }

  new_price=new_price.toFixed(2);
  $("#div5_new_price").val(new_price);
  });



  $("#div5_discount_tovar").on('change', function() { 
  
  var base=$("#div5_price_b").val();
  var disc=$("#div5_discount_tovar").val();
  var maxdisc=$("#div5_max_discount").val();
  var new_price=base/100*(100-disc);
  
  if(disc>maxdisc)
  {
    alert('Скидка превышает максимальную');
    $("#div5_discount_tovar").val("");
    return;
  }

  new_price=new_price.toFixed(2);
  $("#div5_new_price").val(new_price);
  });
  
  

  $('#div4_price').on('keydown', (function(e) {
    if(e.keyCode === 13) {
        alert('1');
    }
  }));


  $("input[name=bnLoadPics]").on('change',loadPictures);
  $("input[name=chkShowDisc]").on('change',ShowHideColumns);
  $("input[name=chkShowPics]").on('change',ShowHidePics);
  $("input[name=chkShowStats]").on('change',ShowHideStats);

  $("input[name=chkShowDiv1]").on('change',ShowHideDiv1);
  $("input[name=chkShowDiv2]").on('change',ShowHideDiv2);

  frmMain.chkShowPics.checked=false;
  frmMain.chkShowDisc.checked=false;
  frmMain.chkShowStats.checked=false;

  /*
  $('#info-table tr > *:nth-child('+2+')').toggle();
  $('#info-table tr > *:nth-child('+4+')').toggle();
  $('#info-table tr > *:nth-child('+8+')').toggle();
  $('#info-table tr > *:nth-child('+9+')').toggle();
*/

  $("input[name=bnHlp]").on('click',function() {
    if( $("tr#hlp:hidden").length > 0 )  $("tr#hlp").show()
    else  $("tr#hlp").hide()
  })
  
  $('input[name=bnOpenRept]').click(function(evt){
      var sOrg = escape(frmMain.sOrg.value),
        idOrg = frmMain.corg.value;
        window.open('mk_rept.php?corg='+idOrg+'&sOrg='+sOrg+'&datOn='+dtOn);
  });


  /////////////////////  журналы ///////////////////////////////////


  $('input[name=bnOpenCredHist]').click(function(evt){
        var agt=$('#agt').html();
        var sOrg = escape(frmMain.sOrg.value), idOrg = frmMain.corg.value;
        var cag=$('span#agt').attr('cag');

        window.open('tnt_cr_hist.php?idOrg='+idOrg+'&sOrg='+sOrg+'&datBeg='+dtStart+'&datEnd='+dtOn + '&agt=' + agt + '&cag=' + cag);
      });

      $('input[name=bnAgDeliv]').click(function(evt){
        var agt=$('#agt').html();
        var sOrg = escape(frmMain.sOrg.value), idOrg = frmMain.corg.value;
        var cag=$('span#agt').attr('cag');

        window.open('tnt_ag_deliv.php?idOrg='+idOrg+'&sOrg='+sOrg+'&datBeg='+dtStart+'&datEnd='+dtOn + '&agt=' + agt + '&cag=' + cag);
      });
      
      $('input[name=bnDostavkaJournal]').click(function(evt){
        var agt=$('#agt').html();
        var sOrg = escape(frmMain.sOrg.value), idOrg = frmMain.corg.value;
        var cag=$('span#agt').attr('cag');

        window.open('tnt_journal_vozvratov.php?idOrg='+idOrg+'&sOrg='+sOrg+'&datBeg='+dtStart+'&datEnd='+dtOn + '&agt=' + agt + '&cag=' + cag);
      });

      $('input[name=bnJournalSchetov]').click(function(evt){
        var agt=$('#agt').html();
        var sOrg = escape(frmMain.sOrg.value), idOrg = frmMain.corg.value;
        var cag=$('span#agt').attr('cag');

        window.open('tnt_journal_schetov.php?idOrg='+idOrg+'&sOrg='+sOrg+'&datBeg='+dtStart+'&datEnd='+dtOn + '&agt=' + agt + '&cag=' + cag);
      });


  $('input[name=bnOpenZalist]').click(function(evt){
        var iAdr= frmMain.cbAddr.selectedIndex;
        var idAdr= frmMain.cbAddr.options[iAdr].value;
        var sAdr= escape(frmMain.cbAddr.options[iAdr].text);
        
        var sOrg = escape(frmMain.sOrg.value),
        idOrg = frmMain.corg.value;
        
        window.open("prle_enl.php?corg="+idOrg+"&org="+sOrg+"&cadr="+idAdr+"&adr="+sAdr+
          "&dt="+dtOn+"&ago="+period+"&dir=0");
  });
  
  $('select[name=cbAddr]').change(function(evt){
      alert('cbAddr change');
      getZalistovkaOrder();
  });
  
  var new_idOrg = frmMain.corg.value;
  if(new_idOrg != 0)
  {
      enterOrg(new_idOrg);
  }
  
  
  
  // open tov section automaticaly
  $("input[name=bnToTov]").click();
  getLocation();
  
})
</script>
<p align='center'>
<select name='locals'></select> Торгпред: <span id=agt cag=""></span>
</p>


<?php include('tnt_ordoc_table.php'); ?>
 
<? /***************  основная форма *******************************/  ?>
<div id=hdr_sec></div>
  
<div id=pgbar> <img src="images/progbar-1.gif" /> </div> 

<p>

<table id='tabpoisk' border=1 cellspacing=4 class="myclass222">
<tr>
<td>Иерархия </td>
<td>
  <select name='cbHier' size=1 id='cbHier'>
  <option value=0 selected>нет
  <option value=1>базовая
  <option value=2 >категории
  <option value=8>промо
  <option value=16>новинки
  <option value=32>фикс.цены
  </select>
</td>
<td id=Сумма>Сумма</td>
<td id=summ>0.00</td>
</tr>

<tr>
  <td>Сквозной поиск :</td>
  <td title=""> 
  
<div id="main">
<div id="search">
    <input type="text" name=sTov id="poisk" class="form-control myclass111" onfocus="fokusA()" onblur="fokusB()" oninput="vvod()" onclick="select(this);" />
    <div id="otmena" onclick="steret()">
        <img id="krest" src="krest.png" />
    </div>
  </div>
  <div style="clear:both;"></div>
</div>
  
  
  </td>
  <td style="vertical-align:top"><img src="images/lookup.png" width=25 height=24 id="bnLsTovs" name="bnLsTovs" />
  &nbsp;&nbsp;<img id=ldTovs src="images/progbar-1.gif" style="display:none" /></td>
  <td> </td>
</tr>


<tr>
<td colspan=4>

<input type="button" value="Правильный поиск" OnClick="search_prepare();">

</td>
</tr>
</table>
</p> 
</div>

<br>

<?php echo file_get_contents('tnt_checkboxes.htm'); ?>
<?php echo file_get_contents('tnt_hidden_divs.htm'); ?>

<center>
<input class="form-control myclass111" type="text" placeholder="Введите текст для поиска .." name="search-text" id="search-text" onkeyup="MySearch(frmMain.chkRenderUL.checked); "  onclick="select(this);">
</center>
<br>
</form>

<script src="tnt_lightbox.js"></script>
<script src="tnt_render_table.js" ></script>
<script src="tnt_alexey_funcs.js"></script>


<?  /************* корзина  *********************/ ?>

<div id=zak_sec>

</div>

<?php
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



<div id=tov_sec >
  <div id="info_cats" style="width: 100%"></div>
  <div id="info_poisk" style="width: 100%"></div>
</div>

<div align="center" id="elem_count_info" style="width: 100%"></div>

<br><br><br><br><br><br>

</body>
</html>
    
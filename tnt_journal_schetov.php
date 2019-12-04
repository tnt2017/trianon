<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Language" content="ru">
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
  <meta http-equiv="Content-Style-Type" content="text/css">
  <title>������ ������</title>
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


    div#enldlg5 { position: absolute; display: none; border: 2px solid grey; background: lightgrey; z-index: 7; }

    .pop-block {
  display: inline-block;
  position: relative; /* ��������� ����������� */
  width: 50%;
  height: 300px; /* ������ ����� ����� */
  margin: 1%;
}
.close-block {
  display: block;
  position: absolute;
  top: 8px;
  right: 8px;
  width: 16px;
  height: 16px;
  background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAAElBMVEXqAAD8oaH/AAD/ra3/vr7///91+I/7AAAAQklEQVR42oXPwQoAIAgD0JXu/385FfTQgnYweKAp9pUXmE+swAFWAG8owQBTqjQQ8SQOcAdRQVtkqHy7dDFd/X/tAVqqAopyUfkOAAAAAElFTkSuQmCC);
  cursor: pointer;
}
.pop-block p {
  width: 100%;
  height: auto;
}


#pop-checkbox {
  display: none;
}




  </style>
  <script src="js/dates.js" ></script>
  <script src="js/jquery-1.12.0.min.js"></script>
  <script src="js/dyna_cal.js" ></script>
  <script src="js/jqlib.js?v=6" ></script>

<script src='js/jquery-1.3.2.min.js'></script>



<script>

function handle(object, price){

     //alert(object.innerText);
     //var s=prompt("������� ����� ��������: ",price);

     //alert(object.innerText);
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
     };*/
}

</script>





</head>
















































<body>


  <div id="page_overlay"></div>
  <div class=c><span class=head>������ ������</span><br>
      [<a href="default.php">�������</a>]</div>
  <hr>



  <form method=post target=_self  name=frmMain id="frmMain" >.


  <input type="button" class="btnS" value="��������" onclick="load_journals()">
  <input type="button" class="btnS" value="�������� doc" onclick="load_doc(190109645)">

  

  <input type=hidden name=sRight value='3' >
  <script src="js/dyna_calco.js"></script>
  <table border=0 cellspacing=5>
  <tr><td>���� :</td><td>
      <input name=datBeg class=dat value='<?php echo $_REQUEST[datBeg]; ?>' onChange="this.value=CheckDate(this.value);">
      <img src="images/cal.gif" width=16 height=16 onclick="ds_sh('datBeg')">

      <input name=datEnd class=dat value='<?php echo $_REQUEST[datEnd]; ?>' onChange="this.value=CheckDate(this.value);">
      <img src="images/cal.gif" width=16 height=16 onclick="ds_sh('datEnd')">
      </td><td></td>
  </tr><tr>
    <td>�������� :</td><td><select name=cbEmp size=1>
<option value=1800028 > ������ �.�.<option value=500 > ������ �.�.<option value=1433 > ������ �.�.<option value=1900102 > ������������ �.�.<option value=1900049 > ������� �.�.<option value=3167 > ����� �.�.<option value=3346 > ���������� �.�.<option value=1900004 > ������� �.�.<option value=1900063 > ����� �.�.<option value=2124 > ������ �.�.<option value=2953 > ����������  �.�.<option value=1900075 > ������� �.�.<option value=3422 > ������� �.�.<option value=1900064 > ���������� �.�.<option value=1900118 > ������ �.�.<option value=2159 > ��������  �.�.<option value=2810 > ������� �.�.<option value=3488 > ���������� �.�.<option value=2578 > ��������� �.�.<option value=3489 > ������� �.�.<option value=1900057 > �������� �.�.<option value=2694 > ��������� �.�.<option value=1900080 > ��������� �.�.<option value=1900016 > �������� �.�.<option value=3258 > �������� �.�.<option value=1900113 > ��������� �.�.<option value=1597 > ��������� �.�.<option value=3219 > �������� �.�.<option value=3102 > ����� �.�.<option value=1900052 > ������� �.�.<option value=3276 > ��������� (�������) �.�.<option value=3303 > �������� �.�.<option value=1943 > ������ �.�.<option value=1900021 > �������� �.�.<option value=3362 > ������� �.�.<option value=1800164 > �������� �.�.<option value=1900026 > ���������� �.�.<option value=1900011 > �������� �.�.<option value=1900151 > ������ �.�.<option value=1900135 > ������ �.�.<option value=1900039 > ������� �.�.<option value=1900069 > ������� �.�.<option value=1900148 > ������ �.�.<option value=1697 > �.������-��-��� ..<option value=1613 > �.������� ..<option value=1900099 > ���������� �.�.<option value=1900032 > �������� �.�.<option value=1800034 > ��������� �.�.<option value=2030 > ������ �.�.<option value=3479 > ������� �.�.<option value=1900173 > ������� �.�.<option value=1900051 > ��������� �.�.<option value=2974 > ����������� �.�.<option value=2392 > ��������� �.�.<option value=1589 > �������� �.�.<option value=3295 > ��������  �.�.<option value=1900094 > ������ �.�.<option value=3080 > ������� �.�.<option value=1900180 > ������ �.�.<option value=3145 > ������ �.�.<option value=1900046 > ��������  �.�.<option value=3408 > �������� �.�.<option value=1900126 > �������� �.�.<option value=3407 > ��������� �.�.<option value=1900114 > ��������� �.�.<option value=1900167 > ������� �.�.<option value=1800147 > ������� �.�.<option value=1900007 > �����  �.�.<option value=1356 > ������� �.�.<option value=23 > ����� �.�.<option value=3354 > ����� �.�.<option value=2137 > �������� �.�.<option value=1800066 > ��������� �.�.<option value=1903 > �������� �.�.<option value=1381 > ��������� �.�.<option value=3325 > ��������� �.�.<option value=1800178 > ������� �.�.<option value=1900119 > ������� �.�.<option value=1900107 > ����� �.�.<option value=3338 > ������ �.�.<option value=1800080 > �������� �.�.<option value=2445 > ����� �.�.<option value=1900054 > ��������� �..<option value=3367 > ������������ �.�.<option value=2687 > ���������� �.�.<option value=1900134 > ���������� �� �.�.<option value=1800059 > ��������� �.�.<option value=3178 > �������� �.�.<option value=2889 > �����������  �.�.<option value=1900125 > �������� �.�.<option value=1800156 > ��������� �.�.<option value=3484 > ���������� �.�.<option value=3440 > ������� �.�.<option value=2661 > ������� �.�.<option value=1800110 > ������� �.�.<option value=1900112 > �������� �.�.<option value=1800074 > ������ �.�.<option value=1800054 > ���� �.�.<option value=1900146 > ����������� �.�.<option value=1900018 > ������� �.�.<option value=2170 > ������� �.�.<option value=2898 > �������� �.�.<option value=1900170 > ������� �.�.<option value=1900081 > ������ �.�.<option value=3261 > ������ �.�.<option value=1900162 > ������� �.�.<option value=1900136 > ������� �.�.<option value=1393 > �������� �.�.<option value=1900155 > ����������� �.�.<option value=1367 > ������� �.�.<option value=1900059 > ��������� �.�.<option value=2995 > ������� �.�.<option value=3275 > ������������ �.�.<option value=3165 > ���������� �.�.<option value=3079 > ����������  �.�.<option value=1900088 > ������� �.�.<option value=1900158 > �������� �.�.<option value=1800169 > ������� �.�.<option value=1624 > ��������� �.�.<option value=1800118 > ������� �.�.<option value=3284 > ���� �.�.<option value=1900153 > ������� �.�.<option value=2941 > ��������� �.�.<option value=3218 > ��������� �.�.<option value=1900127 > �������� �.�.<option value=2748 > ������� �.�.<option value=1800049 > ����� �.�.<option value=2586 > �������� �.�.<option value=3504 > �������� �.�.<option value=1900139 > �������� �.�.<option value=2481 > ��������� �.�.<option value=2987 > �������� �.�.<option value=1900047 > ������� �.�.<option value=1900117 > ��������  �.�.<option value=2289 > ������� �.�.<option value=1900017 > ��������  �.�.<option value=1900068 > ����� �.�.<option value=1900129 > ������� �.�.<option value=3419 > ������ �.�.<option value=1800048 > �������� �.�.<option value=3324 > �������� �.�.<option value=1900035 > �������� �.�.<option value=3425 > �������� �.�.<option value=1900168 > �������� �.�.<option value=3193 > ���������� �.�.<option value=1900078 > ������� �.�.<option value=3290 > ������� �.�.<option value=3451 > ��������� �.�.<option value=1800014 > �������� �.�.<option value=1900038 > ������� �.�.<option value=1900100 > ��������  �.�.<option value=1900005 > ������ �.�.<option value=1900110 > ������ �.�.<option value=1900121 > �������� �.�.<option value=1800010 > ���������� �.�.<option value=1800076 > ������� �.�.<option value=1800029 > ������ �.�.<option value=1900116 > �������� �.�.<option value=1900165 > ����������� �.�.<option value=1800078 > ������������ �.�.<option value=3234 > ���� �.�.<option value=1900175 > �������� �.�.<option value=1800079 > ������� 5� �.�.<option value=1800058 > ������ �.�.<option value=1501 > ����� �.�.<option value=3134 > �������� �.�.<option value=1900086 > ����������� �.�.<option value=1900157 > ���������� �.�.<option value=1800056 > �������   �.�.<option value=2680 > ����������  �.�.<option value=3238 > ���������� (��������) �.�.<option value=1800043 > ������� �.�.<option value=1500 > �������� �.�.<option value=1800001 > ��������� �.�.<option value=1800154 > �������  �.�.<option value=1900073 > �������� �.�.<option value=1900067 > �������� �.�.<option value=1800112 > ���������� �.�.<option value=1503 > ������� �.�.<option value=1900043 > ������������ �.�.<option value=3452 > �������� �.�.<option value=1900166 > ������� �.�.<option value=2117 > �������� �.�.<option value=1900101 > ���������� �.�.<option value=2927 > ������ �.�.<option value=3337 > ������ �.�.<option value=2085 > ����� �������� ..<option value=1800086 > ������� �.�.<option value=1800011 > �������� �.�.<option value=2864 > ��������  �.�.<option value=1900177 > ����� �.�.<option value=1800124 > ������������ �.�.<option value=1900071 > ������� �.�.<option value=1900096 > �������� �.�.<option value=1800157 > ��������� �.�.<option value=3453 > �������� �.�.<option value=3485 > �������� �.�.<option value=1900103 > ���������� �.�.<option value=2961 > �������� �.�.<option value=3206 > ���������� �.�.<option value=3142 > �������� �.�.<option value=1900174 > ���������� �.�.<option value=2780 > ������� �.�.<option value=3215 > ������� �� �.�.<option value=2652 > ����������  �.�.<option value=1800103 > ������ �.�.<option value=1900014 > ������  �.�.<option value=1900074 > ������ �.�.<option value=1614 > ���������� �.�.<option value=1900159 > ������� �.�.<option value=1900171 > ��������� �.�.<option value=1236 > ������ �.�.<option value=3409 > ��������� �.�.<option value=2957 > ������ �.�.<option value=2140 > �������  �.�.<option value=1800137 > �������  �.�.<option value=1900077 > �������� �.�.<option value=1900082 > ���������� �.�.<option value=1900092 > ������ �..<option value=3454 > ������ �.�.<option value=137 > ������ �.�.<option value=1800109 > ������� �.�.<option value=1900163 > ���������� �.�.<option value=3428 > ������� �.�.<option value=1314 > ������ �.�.<option value=1900176 > ������� �.�.<option value=1900130 > ���������� �.�.<option value=1900030 > �������� �.�.<option value=1800020 > �������� �� �.�.<option value=1693 > ��������� �.�.<option value=1996 > ���������  �.�.<option value=1519 > ������ �.�.<option value=1900089 > ����� �.�.<option value=1900120 > ����� �� �.�.<option value=1900142 > ��������  �.�.<option value=1900105 > �������� �.�.<option value=1900150 > ��������� �.�.<option value=1800153 > �������� �..<option value=3314 > �������� �.�.<option value=1900137 > ��������� �.�.<option value=1900111 > ����������  �.�.<option value=2819 > ��������  �.�.<option value=2970 > �������  �.�.<option value=1900028 > ���������� �.�.<option value=1800015 > ������� �.�.<option value=1900079 > ����� �.�.<option value=1900161 > ��������� �.�.<option value=1900015 > ������ �.�.<option value=1800180 > ������� �.�.<option value=3406 > ����� �.�.<option value=55 > ����� �.�.<option value=1900178 > ������� �.�.<option value=1800108 > ������ �.�.<option value=1629 > ��� ..<option value=1150 > ��������� �.�.<option value=1800172 > ������� �.�.<option value=1800149 > ����������� �.�.<option value=2327 > ��������  �.�.<option value=1900019 > ������ �.�.<option value=1900132 > ���������� �.�.<option value=1312 > �������� �.�.<option value=1800119 > ������ �.�.<option value=3456 > �������� �.�.<option value=2933 > ��������  �.�.<option value=3490 > �������� �.�.<option value=1900062 > �������� �.�.<option value=3370 > ��������� �.�.<option value=1900172 > �������� �.�.<option value=3476 > �������� �.�.<option value=1900053 > ������� �.�.<option value=1900029 > ���������� �.�.<option value=1900001 > ������������� �.�.<option value=1900093 > ������� �..<option value=2926 > ��������� �.�.<option value=1900037 > ��������� �.�.<option value=1900070 > ��������� �� �.�.<option value=2744 > ��������� �.�.<option value=3500 > ������� �.�.<option value=1900149 > ����������� �.�.<option value=1900179 > ����������� �.�.<option value=2057 > �������� �.�.<option value=1302 > �������� �.�.<option value=1900106 > ����������  �.�.<option value=1900009 > ������� �.�.<option value=1515 > �������� �.�.<option value=1800159 > ���������� �.�.<option value=2371 > ��������� �.�.<option value=1900124 > �������� �.�.<option value=3122 > �������� �.�.<option value=1362 > ����������� �.�.<option value=3071 > �������� �.�.<option value=1800008 > ������� �.�.<option value=2971 > �������� �.�.<option value=3384 > ��������� ����� �.�.<option value=1900055 > ������ �.�.<option value=2840 > ��������� �.�.<option value=2980 > ������������� �.�.<option value=3435 > ������ �.�.<option value=1900066 > ������� ������� �.�.<option value=2180 > ������� �.�.<option value=2108 > �������� �.�.<option value=1900058 > �������� �.�.<option value=1900010 > ����������� �.�.<option value=1900083 > ������� �.�.<option value=1900164 > ������� �.�.<option value=1397 > ��������� �.�.<option value=1900154 > ������ �.�.<option value=1900140 > ������ �.�.<option value=1800171 > ������� �.�.<option value=3229 > ������� �.�.<option value=2924 > �������� �.�.<option value=1900023 > ������� �.�.<option value=1900147 > �������� �.�.<option value=1900090 > ��������� �.�.<option value=1800025 > ��������� �.�.<option value=1900141 > ����������� �.�.<option value=2802 > ��������� �.�.<option value=1900156 > ����� �.�.<option value=1714 > ������� �.�.<option value=1900087 > �������� �.�.<option value=3297 > �������� �.�.<option value=1900042 > �������� �.�.<option value=1900145 > ����������� �.�.<option value=1900133 > ������� �.�.<option value=1900131 > �������� �.�.<option value=2996 > ������� �.�.<option value=1900181 > ������ �.�.<option value=2869 > ������ �.�.<option value=3411 > ������� �.�.<option value=1800052 > ������� �.�.<option value=3501 > ������� �.�.<option value=2919 > �������� �.�.<option value=1900076 > ������ �.�.<option value=3457 > ������ �.�.<option value=1900115 > �������� �.�.<option value=2278 > ������ ����� �..<option value=1557 > ������ �.�.<option value=1800041 > ������� �.�.<option value=1900084 > ������ �.�.<option value=2777 > �������� (���������) �.�.<option value=1900138 > ���� �.�.<option value=3431 > ������ �.�.<option value=3432 > ������� �.�.<option value=3412 > ������� �.�.<option value=1900098 > ��������� �.�.<option value=2678 > ������ �.�.<option value=1900169 > �������� �.�.<option value=3386 > ������ �.�.<option value=2107 > �������� �.�.<option value=1900104 > ������� �.�.<option value=1900085 > ������� �.�.<option value=2105 > ������ �.�.<option value=1900095 > �������� �.�.<option value=1900123 > ������ �.�.<option value=1800173 > ������� �.�.<option value=2573 > �������(��� ������) �.�.<option value=1900024 > ��������� �.�.<option value=1852 > ������ �.�.<option value=2682 > ������ �.�.<option value=1800167 > ������� �.�.<option value=1478 > ������ �.�.<option value=3214 > ������ �.�.<option value=2549 > ������ �� �.�.<option value=3171 > ������� �.�.<option value=1900008 > ����� �.�.<option value=796 > ��������  �.�.<option value=1900050 > �������� �.�.      </select></td>

    </tr>

<tr>
  <td>���������� :</td>
  <td>
  <div class="easy-autocomplete" style="width: 400px;">
    <input name=sOrg value="" class=org autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" id="eac-5912">
    <div class="easy-autocomplete-container" id="eac-container-eac-5912"><ul style="display: none;"></ul></div></div>
  </td>

</tr>
  </table>

<h2>�����</h2>
  <div id='div_scheta'></div>

<h2>��������� � �������</h2>
  <div id='div_rezervi'></div>


  <input type="checkbox" id="pop-checkbox"> <!--��� ����������� ��������������� � �����-->
  <div id="enldlg5" style="left: 339.317px; top: 820px; display: block; padding: 20px;" class='pop-block'>

  <label for="pop-checkbox" class="close-block" onclick="$('div#enldlg5').hide();"></label> <!--������ ��������; �������� ��� for ������������� id � input-->


<h2>��������</h2>
  <div id='doc_head'></div>
  <br>
  <div id='document'></div>

  </div>


</form>
<HR>
<p class=foot>
<br>Copyright &copy; 2019 [Trianon]. All rights reserved. <BR>
Revised: 26.11.2019



<script>

/* ������ �� �������� �� ���� �� ���� �������� ����� ���������!!!!!!!!!!!!
  $('select option[value="3435"]).prop('selected', true);
  $("#cbEmp").val("3435").change()
  $("#cbEmp option[value='Mazda']").val('3435').attr("selected", true);
  $("#cbEmp").val("3435");
  $("select").val("").trigger("chosen:updated")
  $("#cbEmp").val(3435);
  alert('1');
*/



  var n= frmMain.cbEmp.options.length
          for(i=0; i < n; i++) {
            if( frmMain.cbEmp.options[i].value == <?php echo $_REQUEST[cag]; ?> ) {
              frmMain.cbEmp.selectedIndex= i; break }
          }

            document.addEventListener('click', function(event) {

            if(event.target.id!="")
            {
              var param=event.target.getAttribute("param");

              console.log('id=' + event.target.id);
              console.log('param=' + param);
              if(param=='prc')
              {
              console.log('����=' + event.target.innerText);
              var new_price=prompt('������� ����� ����', event.target.innerText);
              }

              if(param=='kol')
              {
              console.log('����=' + event.target.innerText);
              var new_kol=prompt('������� ����� ����������', event.target.innerText);
              }
            }


            if (event.target.dataset.counter != undefined) 
            { // ���� ���� �������...
            event.target.value++;
            }

          });


 

  function load_journal(imya_jurnala, div_name)
  {
    var par='0e324d80f596bd4de2a13663g06f492be18ee3';
    console.log('load_journal :: imya_jurnala=' + imya_jurnala);
    
    $.ajax({
      url: "tnt_oracle_test.php",
      dataType: "html",
      data: {
            'par': par, 
            'journal' : 1,
            'journal_type' : imya_jurnala,
            'dtOn1' : frmMain.datBeg.value,
            'dtOn2' : frmMain.datEnd.value,
            'cag' : frmMain.cbEmp.value
          },
      success: function(dat, stat,xmlReq) 
      {
            console.log(dat);
            var Myobj = JSON.parse(dat);  
            
            var s="<table border=1>";
            s+="<tr>";
            s+="<td>ID</td>";
            s+="<td>DATA</td>";
            s+="<td>�����</td>";
            s+="<td>�����</td>";
            s+="<td>����������</td>";

            s+="<td>�����</td>";
            s+="<td>����������</td>";
            s+="<td>�����</td>";
            s+="<td>������</td>";
            s+="<td>x</td>";
            s+="<td>x</td>";

            s+="</tr>";

            for(var i=0; i<Myobj.length-1;i++)
            {
              if(Myobj[i].CRESERV=='1')
              s+="<tr bgcolor='gray'>";
              else
              s+="<tr>";
              s+="<td bgcolor='red' onclick=\"load_doc($(this), " + Myobj[i].ID + ")\">" + Myobj[i].ID + "</td>";
              s+="<td>" + Myobj[i].DATA + "</td>";
              s+="<td>" + Myobj[i].NNAKL + "</td>";
              s+="<td>" + Myobj[i].SUMMA + "</td>";
              s+="<td>" + Myobj[i].ORG + "</td>";

              s+="<td>" + Myobj[i].ADR + "</td>";
              s+="<td>" + Myobj[i].TXT + "</td>";
              s+="<td>" + Myobj[i].CAGENT + "</td>";
              s+="<td>" + Myobj[i].CRESERV + "</td>";
              s+="<td><input type=\"button\" value=\"���������� � ���������\" onclick=\"SwitchNakl(" + Myobj[i].ID + ")\" ></td>";
              s+="<td><input type=\"button\" value=\"�������\" onclick=\"BillDelete(" + Myobj[i].ID + ")\" ></td>";
 
              s+="</tr>";
            }
            s+="</table>";

            console.log('������� ������� � ���� ' + div_name);
            $(div_name).html(s);
            //x.innerHTML=s;

    },
    error: function(xmlReq,stat, err) { alert("load_journal: "+stat+" ajax : "+err)},
    complete: function(xmlReq,stat) {   }
  });
}

function load_journals()
{
  load_journal('journal_schetov', 'div#div_scheta');
  load_journal('journal_nakladnih', 'div#div_rezervi');
}

function load_doc(e, idDoc)
{
  load_doc_header(idDoc)
  load_doc_lines(idDoc)

  console.log('show_divdoc start');
       var off= e.offset();
       var x= off.left + 50;
       var y= off.top + e.height();
       dlg= $("div#enldlg5");
       dlg.css({left: x, top: y}).show();
       //dlg.css({visibility: visible}).show();

       //$("#enldlg5").css("visibility","visible"); 
       //$("#enldlg5").css("display","block"); 


       $("#pop-checkbox").val(true);
}

function load_doc_header(idDoc)
  {
    var par='0e324d80f596bd4de2a13663g06f492be18ee3';
 
    $.ajax({
      url: "tnt_oracle_test.php",
      dataType: "html",
      data: {
            'par': par, 
            'get_doc_header': 1,
            'idDoc': idDoc
          },
      success: function(dat, stat,xmlReq) 
      {
            //alert(dat);
            var Myobj = JSON.parse(dat);  
            
            var s="<table border=1>";
            s+="<tr>";
            s+="<td>NNAKL</td>";
            s+="<td>DN</td>";
            s+="<td>AGENT</td>";
            s+="<td>OPER</td>";
            s+="<td>ORG</td>";
            s+="</tr>";

            for(var i=0; i<Myobj.length-1;i++)
            {
              s+="<tr>";
              s+="<td>" + Myobj[i].NNAKL + "</td>";
              s+="<td>" + Myobj[i].DN + "</td>";
              s+="<td>" + Myobj[i].AGENT + "</td>";
              s+="<td>" + Myobj[i].OPER + "</td>";
              s+="<td>" + Myobj[i].ORG + "</td>";
              s+="</tr>";
            }
            s+="</table>";

            var div_name="div#doc_head";
            //alert('������� ������� � ���� ' + div_name);
            $(div_name).html(s);
            //x.innerHTML=s;

    },
    error: function(xmlReq,stat, err) {   alert("load_doc_header: "+stat+" ajax : "+err)},
    complete: function(xmlReq,stat) {   }
  });
}

function load_doc_lines(idDoc)
  {
    var par='0e324d80f596bd4de2a13663g06f492be18ee3';
 
    $.ajax({
      url: "tnt_oracle_test.php",
      dataType: "html",
      data: {
            'par': par, 
            'get_doc_lines': 1,
            'idDoc': idDoc
          },
      success: function(dat, stat,xmlReq) 
      {
            var Myobj = JSON.parse(dat);  
            
            var s="<table border=1>";
            s+="<tr>";
            s+="<td>ID</td>";
            s+="<td>�����</td>";
            s+="<td>����</td>";
            s+="<td>����������</td>";
            s+="<td>�����</td>";
            s+="</tr>";

            for(var i=0; i<Myobj.length-1;i++)
            {
              var t=1;
              s+="<tr>";
              s+="<td>" + Myobj[i].CMC + "</td>";
              s+="<td>" + Myobj[i].NAME + "</td>";
              s+="<td bgcolor='#DAA520' id=" + Myobj[i].CMC + " param='prc'>" + Myobj[i].PRICE + "</td>"; //$(this)
              s+="<td bgcolor='#DAA520' id=" + Myobj[i].CMC + " param='kol'>" + Myobj[i].KOL + "</td>";
              s+="<td>" + Myobj[i].SUMMA + "</td>";
              s+="</tr>";
            }
            s+="</table>";

            var div_name="div#document";
            //alert('������� ������� � ���� ' + div_name);
            $(div_name).html(s);
            //x.innerHTML=s;

    },
    error: function(xmlReq,stat, err) { alert("load_doc_lines: "+stat+" ajax : "+err)},
    complete: function(xmlReq,stat) {   }
  });

}

function BillDelete(idDoc)
{
  var par='0e324d80f596bd4de2a13663g06f492be18ee3';
 
    $.ajax({
      url: "tnt_oracle_test.php",
      dataType: "html",
      data: {
            'par': par, 
            'delete_doc': 1,
            'idDoc': idDoc
          },
      success: function(dat, stat,xmlReq) 
      {
            //alert(dat);
            console.log(dat);
      },
    error: function(xmlReq,stat, err) {   alert("BillDelete: "+stat+" ajax : "+err)},
    complete: function(xmlReq,stat) {   }
  });

  load_journals();
}


function SwitchNakl(idDoc)
{
  alert(idDoc);

  var par='0e324d80f596bd4de2a13663g06f492be18ee3';
 
    $.ajax({
      url: "tnt_oracle_test.php",
      dataType: "html",
      data: {
            'par': par, 
            'switch_nakl': 1,
            'idDoc': idDoc
          },
      success: function(dat, stat,xmlReq) 
      {
            alert(dat);
            console.log(dat);
      },
    error: function(xmlReq,stat, err) {   alert("SwitchNakl: "+stat+" ajax : "+err)},
    complete: function(xmlReq,stat) {   }
  });
}


$( document ).ready(function() {
    console.log( "ready!" );
    load_journals();
});

</script>   

</body>
</html>


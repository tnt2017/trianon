function RenderBasket(Mybasket)
{
  console.log('RenderBasket start');

  var sss="<p>";
  sss+='<span id="zak_type"></span>';
  sss+='<span class=off>Цена строки :</span><span id=costLn></span>';
  sss+='<span class=off>Нц :</span><span id=nacZak></span><br>';
  sss+="<p id='msg'></p>";

  sss+='<input type=button name=bnMakeN value="Создать накладную"  onClick="processDoc();">';
  sss+='<input type=button name=bnMakeN2 value="Создать счет"  onClick="processDoc2();">';
  sss+='<input type=button name=bnNew value="Очистить корзину" onclick="clearZakaz();" >'; 
  sss+='</p>';

  sss+='<p>';
  sss+='<span class=off>Цена строки :</span><span id=costLn2></span>';
  sss+='<span class=off>Нц :</span><span id=nacZak2></span>';
  sss+='</p>';
  
  sss+="<p><table border=1 cellspacing=2 class='myclass333'>";
  sss+="<tr><th>#</th>";
  sss+="<th>PIC</th>";
  sss+="<th>id</th>";
  sss+="<th>название</th>";
  sss+="<th>статистика</th>";
  sss+="<th>заказ</th>";
  sss+="<th>остаток</th>";
  sss+="<th>цена</th>";
  //sss+="<th>M</th>";
  
  var disc_style="";

  if(!frmMain.chkShowDisc.checked)
  disc_style="style=\"display:none;\"";	
  
  sss+="<th " + disc_style + ">скидка</th>"
  sss+="<th " + disc_style + ">% до смц</th>"
  sss+="<th " + disc_style + ">смц</th>"
  sss+="<th " + disc_style + ">баз. цена</th>"
  sss+="</tr>";
  sss+="<tbody id=zakaz>";

  if(Mybasket!=undefined)
  {
      for(var i=0;i<Mybasket.length;i++)
      {
        sss+="<tr>";
        sss+="<td>" + i + "</td>";
        sss+="<td>" + Mybasket[i].ID + "</td>";
        sss+="<td>" + Mybasket[i].ID + "</td>";
        sss+="<td>" + Mybasket[i].NAME + "</td>";
        sss+="<td>" + Mybasket[i].NAME + "</td>";
        sss+="<td>" + Mybasket[i].KOL + "</td>";
        sss+="<td>" + Mybasket[i].OST + "</td>";
        sss+="<td>" + Mybasket[i].PRICE_ORG + "</td>";

        sss+="<td " + disc_style + ">" + Myobj[i].MAXDISC + "</td>";
        sss+="<td " + disc_style + ">" + Myobj[i].SMP + "</td>";
        sss+="<td " + disc_style + ">" + Myobj[i].PRICE_B + "</td>";

        sss+="</tr>";
      }
  }

  sss+="</tbody></table></p>Конец таблицы корзина";
  //var Myobj = JSON.parse(dat); //.sort((a, b) => b["PRICE_ORG"] - a["PRICE_ORG"]));  
  //    globalObj = Myobj;

  $("div#zak_sec").html(sss);
  console.log('RenderBasket end');

}

function RenderUL(Myobj,idGrp,idChild)
{
  var p1, p2, field_type="text";
  if(frmMain.chkDebug.checked) { field_type="text" } else { field_type="hidden" }
  p1 = "<input type=\"" + field_type +  "\" name=\"idGrp\" id=\"idGrp\" value=\"" + idGrp + "\">";
  p2 = "<input type=\"" + field_type + "\" name=\"idChild\" value=\"" + idChild + "\">";

      var sss=p1+p2;
      sss+="<ul id='ul-content' class='ulclass' >";
      for(var i=0;i<Myobj.length-1;i++)
      {
	        // alert(Myobj[i].ID + "  :  " + Myobj[i].CGRP_B + "  :  " + Myobj[i].ENL_FLG);
	        sss+="<li class='liclass'>";

          if(Myobj[i].ID==undefined)
          {
              console.log('undefined elem');
          }
          else
          {

          if(Myobj[i].ID.length==6)
         	{
	          str='/tpic/' + Myobj[i].ID.substring(0, 3) + "/";	
	        }
	        if(Myobj[i].ID.length==8)
   	      {
	          str='/tpic/' + Myobj[i].ID.substring(0, 5) + "/";	
          }

          var tidgrp=$('input[name=idGrp]').val();
          //alert(tidgrp);

          //if(tidgrp=='200001' || tidgrp=='288125' )
          //  str='https://trianon-nsk.ru/images/catalog/';


          if(Myobj[i].gflg==0) ///////// если это группа
          str='https://trianon-nsk.ru/images/catalog/';


	        var path= str + Myobj[i].ID + ".jpg";
          var img_style="";

        	if(frmMain.chkShowPics.checked)
	          img_style="";
	        else
	          img_style="style=\"display:none;\"";	

      	  sss+="<span " + img_style + " onclick='ShowHideDiv0($(this), " + Myobj[i].ID + ");'><a class=\"gallery\" >";
	        var iiiiiii="<img src=\"" + path + "\" height=110 width=110  >";
	        sss+="<a href=\"" + path  + "\" data-lightbox=\"image-1\" data-title=\"My caption\">" + iiiiiii + "</a>";
	        sss+="</a></span><br>";


          if(Myobj[0].gflg==0) // если это товар а не группа
          {
            sss+="<a style='height: 100px;' onclick=\"loadTovs(" + Myobj[i].ID  + "," + idGrp + ")\">" + Myobj[i].NAME + "</a>";            
          }
          else
          {
            sss+="<a style='height: 100px;' >" + Myobj[i].NAME + "</a>";            
          }

          sss+="<br>";
          
          sss+="<b>Остаток: </b>" + Myobj[i].OST + "<br>";
          sss+="<b>Цена: </b>" + Myobj[i].PRICE_ORG  + "<br>";


          if(frmMain.chkShowDisc.checked)
          {
            sss+="<b>СМЦ: </b>" + Myobj[i].SMP + "<br>";
            sss+="<b>Скидка: </b>" + Myobj[i].DISC + "<br>";
            sss+="<b>Макс скидка: </b>" + Myobj[i].MAXDISC + "<br>";
          }

          sss+="<button type=\"button\" class=\"btn btn-primary\">Заказать</button>";
          sss+="</li>";
        }
      }
      sss+="</ul>";
      return sss;
}

function ToogleSortByPrice()
{
  //alert('ToogleSortByPrice()');
  frmMain.chkSortByPrice.checked=!frmMain.chkSortByPrice.checked;
  var sss=RenderJSON(globalObj);
  $("div#info_cats").html("");
  $("div#info_cats").html(sss);
}

function RenderTable(Myobj,idGrp,idChild)
{
  globalidGrp=idGrp;
  console.log("RenderTable : " + idGrp);

  var p1, p2, p3, field_type="text";
  if(frmMain.chkDebug.checked) { field_type="text" } else { field_type="hidden" }
  p1 = "<input type=\"" + field_type +  "\" name=\"idGrp\" value=\"" + idGrp + "\">";
  p2 = "<input type=\"" + field_type + "\" name=\"idChild\" value=\"" + idChild + "\">";
  p3 = "<p id=grpname idval=\"\"></p>";

  var table_class='myclass333';
  
  if(Myobj.length>2)
  {
    //debugger;

    if(Myobj[2].gflg==0) // || Myobj[0].NAME=='uplevel'
    {
      table_class='myclass444';
    }
  }
  

  var tab_header=p1+p2+p3+ '<table border=2 id="info-table" cellspacing=2 class="' + table_class + '">';
  tab_header+='<thead id=\'tovs_header\' class=\'thead-dark\'>';
  tab_header+='<tr><th>#</th>';

  var pic_style="", stat_style="", disc_style="", id_style="";

  if(!frmMain.chkShowStats.checked)
  stat_style="style=\"display:none;\"";	

  if(!frmMain.chkShowPics.checked)
  pic_style="style=\"display:none;\"";	

  if(!frmMain.chkShowDisc.checked)
  disc_style="style=\"display:none;\"";	

  if(!frmMain.chkShowID.checked)
  id_style="style=\"display:none;\"";	
 
  tab_header+='<th ' + pic_style + '>id</th>';
  tab_header+='<th ' + id_style + '>id</th>';
  tab_header+='<th>название</th>';

  if(Myobj[0].gflg!=0) // если это товар а не группа
  {
      tab_header+='<th ' + stat_style + '>статистика</th>';
      tab_header+='<th>заказ</th>';
      tab_header+='<th>остаток</th>';
      tab_header+='<th onclick="ToogleSortByPrice()">цена</th>';
      tab_header+='<th>M</th>';
      tab_header+='<th id=tDisc ' + disc_style + '>скидка</th>';
      tab_header+='<th ' + disc_style + '>% до смц</th>';
      tab_header+='<th ' + disc_style + '>смц</th>';
      tab_header+='<th ' + disc_style + '>баз. цена</th>';

  }

  tab_header+='</tr></thead>';
  tab_header+='<tbody id=tovs>';

  var sss="";
   
  for(var i=0;i<Myobj.length-1;i++)
  {
    sss+="<tr ngrp=" + Myobj[i].GRP + " cgrp=" + Myobj[i].CGRP + ">";   //t=\"-1\" 
    if(Myobj[i].NAME=='uplevel')
      {
        //sss+="<tr><td>.</td>";
        //sss+="<td colspan=8 >";
        //sss+="<input class=\"form-control myclass111\" type=\"text\" placeholder=\"Введите текст для поиска ..\" name=\"search-text\" id=\"search-text\" onkeyup=\"MySearch(frmMain.chkRenderUL.checked); \" onclick=\"select(this);\"></input>";
        //sss+="</td></tr>";

        sss+="<td>.</td>";
        sss+="<td colspan=10 >";
        sss+="<div class='grp' onclick=\"toRootDir()\"> в корень (" + Myobj[i].iL + ") ";

        sss+="</div></td>";
        sss+="<tr><td>..</td>";
        sss+="<td colspan=10 >";

        if(idChild==0)
        idChild=288125;

        sss+="<div class='grp' onclick=\"loadTovs(" + idChild + ", " + 0 + ")\"> наверх (" + idChild + ") ";
        //onclick=\"loadTovs(" + Myobj[i].idGrp  + ",0)\"

        sss+="</div>";
        sss+="</td></tr>";
      }
      else
      {
        //alert(Myobj[i].ID + "  :  " + Myobj[i].CGRP_B + "  :  " + Myobj[i].ENL_FLG);
	      ///
        var star_class='glyphicon glyphicon-star';
        var func_name='MakeZalist';
	      if(Myobj[i].ENL_FLG==4)
	      {
          star_class='glyphicon glyphicon-star';
          func_name='MakeRazlist';
	      }
	      else
	      {
          star_class='glyphicon glyphicon-star-empty';
	      }

      	sss+="<td>";
        sss+="<span onclick=\"" + func_name + "($(this), " + Myobj[i].ID + "); \" class=\"" + star_class + "\"></span>";
      	sss+="</td>";

	      var str='';

      	if(Myobj[i].ID.length==6)
   	    {
	        str='/tpic/' + Myobj[i].ID.substring(0, 3) + "/";	
	      }
	      if(Myobj[i].ID.length==8)
   	    {
	        str='/tpic/' + Myobj[i].ID.substring(0, 5) + "/";	
	      }

        if(Myobj[i].gflg==0) ///////// если это группа
        str='https://trianon-nsk.ru/images/catalog/';

	      var path= str + Myobj[i].ID + ".jpg";

        sss+="<td " +  pic_style + " onclick='ShowHideDiv0($(this), " + Myobj[i].ID + ");'><a class=\"gallery\" >";

        if(frmMain.chkShowPics.checked)
        {
          var my_img="<img src=\"" + path + "\" height=110 width=110  >";
	        sss+="<a href=\"" + path  + "\" data-lightbox=\"image-1\" data-title=\"" + Myobj[i].NAME + "\">" + my_img + "</a>";
        }
        
        sss+="</td>";
        
        
        //alert(Myobj[i].gflg); // !=1
      
        if(Myobj[i].gflg==0) ///////// если это группа
        {   
          sss+="<td colspan=8 class='grp'>";
          sss+="<div onclick=\"loadTovs(" + Myobj[i].ID  + "," + idGrp + ")\";>"; /////////////// class='grp' 18-11-2019
          sss+=Myobj[i].NAME; // + " ( " + Myobj[i].ID + " ) ";

          //sss+= "is_child=" + Myobj[i].is_child + " : ";
          //sss+= "is_parent=" + Myobj[i].is_parent;
          sss+= "</div></td>";
        }
        else
        {
          sss+="<td " + id_style + ">";
          sss+="<a href=\"" + path + "\" data-lightbox=\"image-1\" data-title=\"\">";
          sss+=Myobj[i].ID;
          sss+="</a>"; // style=\"display:none;\"
          sss+="</td>"; // style=\"display:none;\"

          //" : ID=" +  Myobj[i].ID + "  :  CGRP_B=" + Myobj[i].CGRP_B + "  :  ENL_FLG=" + Myobj[i].ENL_FLG +

          var bgcolor='';

          if(Myobj[i].NAME.lastIndexOf('Акц')>0)
          {
            bgcolor='#F5A9BC';
          }
	        sss+="<td bgcolor=\"" + bgcolor + "\" desc=\"1\" id_tov=\"" + Myobj[i].ID + "\" class=\"desc\"><div class='name'>" + Myobj[i].NAME + "</div></td>";

	        if(frmMain.chkShowStats.checked)
	          sss+="<td>" + Myobj[i].minK + "</td>";
	        else
	          sss+="<td style=\"display:none;\">" + Myobj[i].minK + "</td>";

            //onclick=\"ShowHideDiv2($(this), " + Myobj[i].ID + "); \"

          sss+="<td onclick=\"openKolDlg($(this),0)\">" + Myobj[i].korQuant + "</td>";
          sss+="<td onclick=\"ShowHideDiv3($(this), " + Myobj[i].ID + ")\" >";
	
          if(Myobj[i].OST==0)
          {
            sss+="<span id='' class=\"glyphicon glyphicon-share-alt\"></span>";
            sss+=GetDtTill(Myobj[i].ID);
          }
	        else
            sss+=Myobj[i].OST;
   
  	      sss+="</td>";
      	 // sss+="<td onclick=\"handle(this)\" \">" + Myobj[i].PRICE_ORG + "</td>";
	        sss+="<td onclick=\"ShowHideDiv5($(this),'" + encodeURIComponent(JSON.stringify(Myobj[i])) + "'); \">" + Myobj[i].PRICE_ORG + "</td>";



          sss+="<td onclick=\"ShowHideDiv4($(this), " + Myobj[i].ID + ", " + Myobj[i].PRICE_ORG + "); \" >";
          sss+="<span class=\"glyphicon glyphicon-pencil\"></span>";
      	  sss+="</td>";
 
          //alert(Myobj[i].CGRP_B);
          //alert(Myobj[i].GRP);

///////////////" + Myobj[i].ID + "," + Myobj[i].CGRP_B + ", " + Myobj[i].SMP + ", \'" + Myobj[i].GRP + "\', \'" + Myobj[i].NAME + "\', " + Myobj[i].PRICE_B  + ", " + Myobj[i].MAXDISC + "



	        sss+="<td " + disc_style + " onclick=\"ShowHideDiv5($(this),'" + encodeURIComponent(JSON.stringify(Myobj[i])) + "'); \">" + Myobj[i].DISC + "</td>";
          sss+="<td " + disc_style + ">" + Myobj[i].MAXDISC + "</td>";
          sss+="<td " + disc_style + ">" + Myobj[i].SMP + "</td>";
          sss+="<td " + disc_style + ">" + Myobj[i].PRICE_B + "</td>";

        }
     
    }
      sss+="</tr>";
  }
     // $("tbody#tovs").html(sss);
     return (tab_header + sss + "</tbody></table>");
  }

function RenderJSON(Myobj,idGrp,idChild)
{
    //var tmpObj=Myobj;
    let tmpObj = JSON.parse(JSON.stringify(Myobj));  // копируем объект

    if(frmMain.chkSortByPrice.checked)
    {
        //alert('перед сортировкой');

        tmpObj.sort(function (a, b) 
        {
          if (parseFloat(a.PRICE_ORG) > parseFloat(b.PRICE_ORG)) 
          {
            return 1;
          }
          if (parseFloat(a.PRICE_ORG) < parseFloat(b.PRICE_ORG)) 
          {
            return -1;
          }
          // a должно быть равным b
          return 0;
        });

        //alert('после сортировки');
    }

    if(frmMain.chkRenderUL.checked)
    {
      if(idChild==undefined)
  {
   alert(idChild);  
   //var bla = $('#idGrp').val();
   //alert(bla);
   //idGrp=bla;
   // $('#txt_name').val(bla);
  }



      if(idGrp==undefined)
       return RenderUL(tmpObj,0,0);
      else
       return RenderUL(tmpObj,idGrp,0);
    }
    else
      return RenderTable(tmpObj,idGrp,idChild);
}


function MyUpdateView()
{
  var sss=RenderJSON(globalObj);
  $("div#info_cats").html("");
  $("div#info_cats").html(sss);
}



/******************************************** *//******************************************** */
/******************************************** *//******************************************** */
/******************************************** *//******************************************** */
/******************************************** *//******************************************** */
/******************************************** *//******************************************** */
/******************************************** *//******************************************** */
/******************************************** *//******************************************** */
/******************************************** *//******************************************** */
/******************************************** *//******************************************** */
/******************************************** *//******************************************** */
/******************************************** *//******************************************** */
/******************************************** *//******************************************** */
/******************************************** *//******************************************** */
/******************************************** *//******************************************** */
/******************************************** *//******************************************** */
/******************************************** *//******************************************** */
/******************************************** *//******************************************** */

function ShowHideColumns() 
{      
	if(frmMain.chkShowDisc.checked)
	{
		$("thead#tovs_header th:nth-child("+(0+9)+")").show();
		$("thead#tovs_header th:nth-child("+(0+10)+")").show();

		$("tbody#tovs td:nth-child("+(0+9)+")").show();
		$("tbody#tovs td:nth-child("+(0+10)+")").show();
	}
	else
	{
		$("thead#tovs_header th:nth-child("+(0+9)+")").hide();
		$("thead#tovs_header th:nth-child("+(0+10)+")").hide();

		$("tbody#tovs td:nth-child("+(0+9)+")").hide();
		$("tbody#tovs td:nth-child("+(0+10)+")").hide();
	}
}


function ShowHidePics() 
{      
	
	if(frmMain.chkShowPics.checked)
	{
		$("thead#tovs_header th:nth-child("+(0+2)+")").show();
		$("tbody#tovs td:nth-child("+(0+2)+")").show();
	}
	else
	{
		$("thead#tovs_header th:nth-child("+(0+2)+")").hide();
		$("tbody#tovs td:nth-child("+(0+2)+")").hide();
	}
}



function ShowHideStats() 
{      
	if(frmMain.chkShowStats.checked)
	{
		$("thead#tovs_header th:nth-child("+(0+5)+")").show();
		$("tbody#tovs td:nth-child("+(0+5)+")").show();
	}
	else
	{
		$("thead#tovs_header th:nth-child("+(0+5)+")").hide();
		$("tbody#tovs td:nth-child("+(0+5)+")").hide();
	}
}


function ShowHideDiv0(e, xxx) 
{      
  //alert('ShowHideDiv0 заглушка');
}

function ShowHideDiv1(e, xxx) 
{      
  eKol= e
  $("td:eq("+cNAME+")",e.parent()).addClass('seln'); // красим в зеленый

   var off= e.offset();
   var x= off.left - 50;
   var y= off.top + e.height() + 4;
   
   $("#div1_id").val(xxx);

   eKol = e;

   dlg= $("div#enldlg1");
   dlg.css({left: x, top: y}).show();

   //$("div#enldlg1").show();
}



function HideDiv1() 
{      
    $("div#enldlg1").hide();
}

function ShowHideDiv2(e, xxx) 
{      
  eKol= e
  select_tr_color(eKol);

  $("#div2_id").val(xxx);

   var off= e.offset();
   var x= off.left - 50;
   var y= off.top + e.height() + 4;
    
  //$("td:eq("+cNAME+")",e.parent()).addClass('seln');
   dlg= $("div#enldlg2");
   dlg.css({left: x, top: y}).show();
   //$("div#enldlg2").show();
}


function HideDiv2() 
{      
  unselect_tr_color(eKol);
  updateZakaz();
	 $("div#enldlg2").hide();
}

//https://trianon-nsk.ru/images/catalog/258147.jpg



function GetDtTill(idTov)
{
  $.ajax({

    url: "tnt_oracle_test.php",
    dataType: "html",
    data: {'par': par,
           'get_dt_till': par,
           'idTov': idTov
          },

    success: function(dat, stat,xmlReq) 
    {
      var params = dat.split(':');
      //alert(params[0]);    	
      //alert(params[1]); 
      

      function sleep (time) 
      {
        return new Promise((resolve) => setTimeout(resolve, time));
      }
      sleep(1000).then(() => 
      {
        var divname="div#ost" + params[0].slice(2);
        console.log(divname + " write " + params[1]);
        $(divname).html(params[1]);
      });
    },
    async: false,
    complete: function(a,b)
    {
	  }
  });
  return "<div id='ost" + idTov + "'>"  + "</div>";

}





function ShowHideDiv3(e, idTov) 
{      
   eKol= e
   select_tr_color(eKol);

   var ia= frmMain.cbAddr.selectedIndex, idAdr= frmMain.cbAddr.options[ia].value;

   $.ajax({

    url: "tnt_oracle_test.php",
    dataType: "html",
    data: {'par': par,
           'get_dt_till': par,
           'idTov': idTov
          },

    success: function(dat, stat,xmlReq) 
    {
      //alert(dat);    	

      $("#WAITLIST_IDTOV").val(idTov);
      $("#WAITLIST_IDADR").val(idAdr);
      $("#WAITLIST_DT_TILL").val('22.12.2019');

      var arrayOfStrings = dat.split(":");
      $("#WAITLIST_OJIDAETSYA").val(arrayOfStrings[1]);
     

      //alert('показать слой');
      var off= e.offset();
      var x= off.left - 50;
      var y= off.top + e.height() + 4;
      
      dlg= $("div#enldlg3");
      dlg.css({left: x, top: y}).show();
   
      //$("div#enldlg3").show();
    },
    complete: function(a,b)
    {
	  }
});





}


function HideDiv3() 
{      
  $("div#enldlg3").hide();
   unselect_tr_color(eKol);
   eKol= null
}


function select_tr_color(e)
{
  for(var i=0;i<11;i++)
  {
  $("td:eq("+i+")",e.parent()).addClass('seln'); // красим в зеленый
  }
}


function unselect_tr_color(e)
{
  for(var i=0;i<11;i++)
  {
    if(e!=null)
      $("td:eq("+i+")",e.parent()).removeClass('seln'); // красим в зеленый
  }
}


function ShowHideDiv4(e, xxx) 
{      
  eKol=e
  select_tr_color(e);

  
   $("#div4_id").val(xxx);
   ia= frmMain.cbAddr.selectedIndex, idAdr= frmMain.cbAddr.options[ia].value;

   $("#div4_idTov").val(xxx);
   $("#div4_idOrg").val(frmMain.corg.value);
   $("#div4_idAdr").val(idAdr);

   ia= frm_monitoring.konk_org.selectedIndex, idKonkur= frm_monitoring.konk_org.options[ia].value;
   $("#div4_idKonkur").val(idKonkur);

   //alert('показать слой');
   var off= e.offset();
   var x= off.left - 150;
   var y= off.top + e.height() + 4;
   
   dlg= $("div#enldlg4");
   dlg.css({left: x, top: y}).show();

   //$("div#enldlg4").show();
}


function HideDiv4() 
{      
   $("div#enldlg4").hide();
   
   unselect_tr_color(eKol);
   eKol= null
}


function ShowHideDiv5(e, myobj)  ///idTov, idGrp, smp, grp, name, skidka, maxskidka
{   
  var local_json=decodeURIComponent(myobj);
  var test_obj = JSON.parse(local_json);
    
  //alert(local_json );
  console.log('ShowHideDiv5 :: GRPBASE=' + test_obj['GRPBASE'] + ' GRP=' + test_obj['GRP'] + ' CGRP_B=' + test_obj['CGRP_B']);

  eKol= e
  select_tr_color(eKol);

  var skidka=test_obj['DISC'];
  var maxskidka=test_obj['MAXDISC'];
  var itogo_max_skidka=parseFloat(skidka) + parseFloat(maxskidka);

  //alert('Итого максимально скидка может быть ' + itogo_max_skidka);

   ia= frmMain.cbAddr.selectedIndex, idAdr= frmMain.cbAddr.options[ia].value;
   var idOrg=frmMain.corg.value;
   var idTov=test_obj['ID'];
   var idGrp=test_obj['CGRP_B'];
   var smp=test_obj['SMP'];
   var current_price=test_obj['PRICE_ORG'];

   $("#div5_idOrg").val(idOrg);
   $("#div5_idAdr").val(idAdr);
   $("#div5_idTov").val(idTov);
   $("#div5_idGrp").val(idGrp);
 
   $('input[name=div5_current_price]').val(current_price);

   if(test_obj['GRP']=="")
   $('input[name=grp111]').val(test_obj['GRPBASE']);
   else
   $('input[name=grp111]').val(test_obj['GRP']);
   
   $('input[name=tovar111]').val(test_obj['NAME']);

   $('input[name=div5_discount_grp]').val('16');
   $('input[name=div5_discount_tovar]').val('16');

   $("#div5_smp").val(smp);
   $("#div5_max_discount").val(itogo_max_skidka);

   $("#div5_price_b").val(test_obj['PRICE_B']);
  
   //alert('показать слой');
   var off= e.offset();
   var x= off.left - 150;
   var y= off.top + e.height() + 4;
   
   dlg= $("div#enldlg5");
   dlg.css({left: x, top: y}).show();

   //$("div#enldlg4").show();
   console.log('idOrg=' + idOrg + ' idAdr=' + idAdr + ' idGrp=' + idGrp);
   console.log('par=' + par);

   $.ajax({

    url: "tnt_oracle_test.php",
    dataType: "html",
    data: {'par': par,
           'get_grp_disc': par,
           'idOrg': idOrg,
           'idAdr': idAdr,
           'idGrp': idGrp
          },

    success: function(dat, stat,xmlReq) 
    {
      if(isNaN(parseFloat(dat)))
       dat="0";
      $('input[name=div5_discount_grp]').val(dat);
      $('input[name=div5_discount_tovar').val(dat);
    },
    error: function(xmlReq,stat, err) { alert("tnt_oracle_test ajax err: "+err)},
    complete: function(a,b)
    {
	  }
});

}


function HideDiv5() 
{      
   $("div#enldlg5").hide();
   
   unselect_tr_color(eKol);
   eKol= null
}


function ShowHideDiv6(e, xxx) 
{      
   $("td:eq("+cNAME+")",e.parent()).addClass('seln'); // красим в зеленый
   var off= e.offset();
   var x= off.left - 50;
   var y= off.top + e.height() + 4;
   
   //$("#div6_id").val(xxx);

   eKol = e;

   dlg= $("div#enldlg6");
   dlg.css({left: x, top: y}).show();

   //$("div#enldlg1").show();
}




function LoadTovList()
{
 // var idx= frmMain.cbHier.selectedIndex,
 //     idGrp= (idx==1 ? 200001 : (idx==2 ? 288125 : 0))

   $("select[name=cbHier]").change(function() {

    alert('LoadTovList idx=' + frmMain.cbHier.selectedIndex + ' idGrp='  + idGrp );
    
    if(frmMain.cbHier.selectedIndex==1)
    {
      $('#idGrp').val("200001");
      loadTovs(200001,0);
    }
    if(frmMain.cbHier.selectedIndex==2)
    {
      $('#idGrp').val("288125");
      loadTovs(288125,0);
    }

    $("#grpname").text(". ").attr('idval',idGrp)
    aGrp.size= 0
  });
}

function MakeZalist(e, idTov) 
{       
    eKol = e;
    eKol.removeClass('glyphicon-star-empty');
    eKol.addClass('glyphicon-star'); 

    //alert('MakeZalist');

    $.ajax({
    url: "tnt_oracle_test.php",
    dataType: "html",
    data: {'par': par,
           'set_zalist': par,
           'idOrg': frmMain.corg.value,
           'idTov': idTov
          },

    success: function(dat, stat,xmlReq) 
    {
      alert(dat);    	
    },
    complete: function(a,b)
    {
	  }
});
}


function MakeRazlist(e, idTov) 
{       
    console.log('MakeZalist');
    eKol = e;
    eKol.removeClass('glyphicon-star');
    eKol.addClass('glyphicon-star-empty'); 
    $.ajax({

    url: "tnt_oracle_test.php",
    dataType: "html",
    data: {'par': par,
           'set_razlist': par,
           'idOrg': frmMain.corg.value,
           'idTov': idTov
          },

    success: function(dat, stat,xmlReq) 
    {
      alert(dat);    	
    },
    complete: function(a,b)
    {
	  }
});
}


function MakeWaitList() 
{         
   console.log('MakeWaitList');
   alert('MakeWaitList');
   debugger;
   //alert(frm_waitlist.WAITLIST_IDADR.value);
   //alert(frm_waitlist.WAITLIST_IDTOV.value);
   //alert(frm_waitlist.WAITLIST_KOL.value);
   //alert(frm_waitlist.WAITLIST_DT_TILL.value);
    $.ajax({
    url: "tnt_oracle_test.php",
    dataType: "html",
    data: {'par': par,
           'set_waitlist': par,
           'idOrg': frmMain.corg.value,
           'idTov': frm_waitlist.WAITLIST_IDTOV.value,
           'idAdr': frm_waitlist.WAITLIST_IDADR.value,
           'KOL': frm_waitlist.WAITLIST_KOL.value,
           'dtTill': frm_waitlist.WAITLIST_DT_TILL.value
          },

    success: function(dat, stat,xmlReq) 
    {
      alert(dat);    	
    },
    complete: function(a,b)
    {
	  }
  });
}




function get_dt_till(idTov)
{

$.ajax({

  url: "tnt_oracle_test.php",
  dataType: "html",
  data: {'par': par,
         'get_dt_till': par,
         'idTov': idTov
        },

  success: function(dat, stat,xmlReq) 
  {
    alert(dat);    	
 
  },
  complete: function(a,b)
  {
  }
});

}



function load_waitlist(div_name)
{
  var p=1;
  var idOrg= frmMain.corg.value, ia= frmMain.cbAddr.selectedIndex, idAdr= frmMain.cbAddr.options[ia].value;
  alert('load_waitlist');
  console.log('load_waitlist idOrg=' + idOrg + 'idAdr=' + idAdr);

  $.ajax({
    url: "tnt_oracle_test.php",
    dataType: "html",
    data: {'par': par,
           'get_waitlist': par,
           'idOrg': idOrg,
           'idAdr': idAdr,
           'idEmp': p
          },

    success: function(dat, stat,xmlReq) 
    {
      var wait_list = JSON.parse(dat);  
      var text_fmt = '<center><h2>Лист ожидания</h2></center>';
            
      text_fmt+= '<table>';
      text_fmt+='<tr>';
      text_fmt+='<td>ID</td>';
      text_fmt+='<td>Название товара</td>';
      text_fmt+='<td>Количество</td>';
      text_fmt+='<td>Ожидать до:</td>';
      text_fmt+='<td>Ожидается</td>';

      text_fmt+='</tr>';

      for(var i=0;i<wait_list.length;i++)
      {
        text_fmt+='<tr>';
        text_fmt+='<td>' + wait_list[i].ID + '</td>';
        text_fmt+='<td>' + wait_list[i].TOV + '</td>';
        text_fmt+='<td>' + wait_list[i].KOL + '</td>';

        text_fmt+='<td>' + wait_list[i].DTILL + '</td>';

        text_fmt+='<td  onclick="get_dt_till(' + wait_list[i].ID + ')">Узнать!</td>';

        text_fmt+='</tr>';
      }
      text_fmt += '</table>';

      dlg= $(div_name);
      dlg.css({left: 0, top: 0}).show();
      dlg.html();
      dlg.html(text_fmt);
      //debugger;
      //return text_fmt;
    },
    complete: function(a,b)
    {
	  }
});
}


function RenderWaitList()
{
  load_waitlist("div#info_cats");
}

function SetPrice() 
{          
    var mon_type=($('input[name=radio_monitoring]:checked', '#frm_monitoring').val()); 
    alert("mon_type=" + mon_type);

    $.ajax({
    url: "tnt_oracle_test.php",
    dataType: "html",
    data: {'par': par,
           'set_prc': par,
           'idTov':  frm_monitoring.div4_idTov.value, 
           'idOrg':  frm_monitoring.div4_idOrg.value,
           'idAdr':  frm_monitoring.div4_idAdr.value,
           'idKonkur': frm_monitoring.div4_idKonkur.value,
           'price':  frm_monitoring.div4_price.value,
           'mon_type' :  mon_type
          },
    success: function(dat, stat,xmlReq) 
    {
      alert(dat);    	
    },
    complete: function(a,b)
    {
	  }
  });
}

function SetGroupDiscount(discount_type, discount_value) 
{         
    //alert("discount_type=" + discount_type);
    var ajaxproc='';
    var idAdr=frm_discount.div5_idAdr.value;

    if(frm_discount.navse_adresa.checked)
    {
     idAdr=0;
     console.log('SetGroupDiscount :: ставим на все адреса');
    }

    if(discount_type==1)
    {
      console.log('set_grp_disc скидка на группу' + frm_discount.div5_idGrp.value);
      $.ajax({
      url: "tnt_oracle_test.php",
      dataType: "html",
      data: {'par': par,
           'set_grp_disc'  : par,
           'idOrg':  frm_discount.div5_idOrg.value, 
           'idAdr':  idAdr,
           'idGrp':  frm_discount.div5_idGrp.value,
           'rDisc':  discount_value,
           'discount_type' :  discount_type
          },
      success: function(dat, stat,xmlReq) 
      {
      alert(dat);    	
      },
     complete: function(a,b)
      {
	    }
      });
    }

    if(discount_type==2)
    {
      //alert('set_prc_tovar скидка на товар');

      $.ajax({
      url: "tnt_oracle_test.php",
      dataType: "html",
      data: {'par': par,
           'set_prc_tovar'  : par,
           'idOrg':  frm_discount.div5_idOrg.value, 
           'idAdr':  idAdr,
           'idTov':  frm_discount.div5_idTov.value,
           'rDisc':  discount_value,
           'discount_type' :  discount_type
          },
      success: function(dat, stat,xmlReq) 
      {
        alert(dat);    	
      },
      complete: function(a,b)
      {
	    }
      });
    }
}


function openKolDlg( e,pane ) /* калькулятор */
{
  console.log('openKolDlg');

  if( eKol ) 
  { // save prev if still open
    updateZakaz()
  }
  var off= e.offset(), x= off.left - 50, y= off.top + e.height() + 4,
    dlg= $("div#kolDlg")
    
  console.log('off=' + off + ' x=' + x + ' y=' + y);
  eKol= e
  eKolA= null
  iPane= pane
  
  select_tr_color(eKol);
  dlg.css({left: x, top: y}).show()


  /////////////// добавлять в JSON объект корзины /////////////////

  var i=0;
  $("tbody#tovs tr").each(function(idx,etr) 
  {
    //if(globalObj[i].NAME!="uplevel")
    i++;
    //alert(i);
    //console.log('cmcA='+$("td:eq("+cCMC+")", etr).text() + '|cmcB='+$("td:eq("+cCMC+")",e.parent()).text());
   // if( parseInt($("td:eq("+cCMC+")", etr).text()) == parseInt($("td:eq("+cCMC+")",e.parent()).text()) )
   // {  


    if( parseInt(globalObj[i].ID) == parseInt($("td:eq("+cCMC+")",e.parent()).text()) )
    {
      globalI=i;
      console.log('выбираем строку номер ' + i + " NAME = " + globalObj[i].NAME);
    }
  })
}

/******************************************** *//******************************************** */
/******************************************** *//******************************************** */
/******************************************** *//******************************************** */
/******************************************** *//******************************************** */
/******************************************** *//******************************************** */
/******************************************** *//******************************************** */
/******************************************** *//******************************************** */

function toRootDir()  // 25.03.14
{
  //debugger;
  //var ix= frmMain.cbHier.selectedIndex, idHr= frmMain.cbHier.options[ix].value
  //if( idHr==1 || idHr==2 ) {
    //var idG= aGrp[0]
    //aGrp.size= 1
    //$("#grpname").attr('idval',idG)
    //$("#grpname").text( '')
    //alert('вызов4')

    var idx= frmMain.cbHier.selectedIndex,
      idGrp= (idx==1 ? 200001 : (idx==2 ? 288125 : 0))

    loadTovs(idGrp,0)
}
function toUpDir( eTov )  // 25.03.14
{
  if( eTov.prev().prev().text() == 0 ) {
    var i= $("#grpname").text().lastIndexOf("|"),
      idG= aGrp[--aGrp.size]
    $("#grpname").attr('idval',idG)
    if( i > 0 )
      $("#grpname").text( $("#grpname").text().substring(0,i))

      //alert('вызов5');
      loadTovs(idG,0)
  }
  else {
    var idG= eTov.prev().text(), grpN= eTov.text()/*, child = eTov.parent().attr('child')*/;
    var child = '0';
    $("tbody#tovs tr[t=0]").each(function(i,el){
        var idroot = $(el).attr('croot');
        if(idroot == idG) 
        {
            child = $("td:eq("+cCMC+")",el).text();
        }
    })
    aGrp[aGrp.size++]= $("#grpname").attr('idval');
    $("#grpname").text($("#grpname").text()+"| "+grpN).attr('idval',idG);
    loadTovs(idG,child);
  }
}


function load_journal()
{
  var par='0e324d80f596bd4de2a13663g06f492be18ee3';
  alert(par);
  debugger;

    //,'dtOn1' : frmMain.datBeg.value,
    //'dtOn2' : frmMain.datEnd.value
    //'par': par

  $.ajax({
    url: "tnt_oracle_test.php",
    dataType: "html",
    data: {
            'par': par, 
            'journal_schetov': 1
          },
    success: function(dat, stat,xmlReq) 
    {
            alert(dat);
            var Myobj = JSON.parse(dat);  
            
            var s="<table border=1>";
            s+="<tr>";
            s+="<td>ID</td>";
            s+="<td>SF</td>";
            s+="<td>DATA</td>";
            s+="<td>Номер</td>";
            s+="<td>Сумма</td>";
            s+="<td>Контрагент</td>";
            s+="<td>Маршрут</td>";
            s+="<td>Склад</td>";
            s+="<td>FIRM</td>";
            s+="<td>Адрес</td>";
            s+="<td>Примечание</td>";
            s+="</tr>";

            for(var i=0; i<Myobj.length;i++)
            {
              s+="<tr>";
              s+="<td>" + Myobj[i].ID + "</td>";
              s+="<td>" + Myobj[i].SF + "</td>";
             // s+="<td>" + Myobj[i].TYPE + "</td>";
              s+="<td>" + Myobj[i].DSF + "</td>";
              s+="<td>" + Myobj[i].NNAKL + "</td>";
              s+="<td>" + Myobj[i].SUMMA + "</td>";
              s+="<td>" + Myobj[i].ORG + "</td>";
              s+="<td>" + Myobj[i].ROUTE + "</td>";
              s+="<td>" + Myobj[i].SKL + "</td>";
              s+="<td>" + Myobj[i].FIRM + "</td>";
              s+="<td>" + Myobj[i].ADR + "</td>";
              s+="<td>" + Myobj[i].TXT + "</td>";
              s+="</tr>";
            }
            s+="</table>";

            var div_name="div#info_cats";
            alert('выводим таблицу в слой ' + div_name);
            $(div_name).html(s);
            //x.innerHTML=s;
    },
    error: function(xmlReq,stat, err) { alert("loadTov: "+stat+" ajax : "+err)},
    complete: function(xmlReq,stat) { alert("loadTov:"); }
  });

}

function loadTovs(idGrp,idChild)
{    
    var ix= frmMain.cbHier.selectedIndex, idHr= frmMain.cbHier.options[ix].value,
    idOrg= frmMain.corg.value,
    ia= frmMain.cbAddr.selectedIndex, idAdr= frmMain.cbAddr.options[ia].value,
    iOst0= frmMain.chkOst0.checked ? 1 : 0,
    iUnite = 0, //frmMain.chkUnite.checked ? 1 : 0,
    datZ= frmMain.datOn.value;
    idGr= idGrp

    console.log('loadTovs idGrp=' + idGrp + 
                ", idChild=" + idChild + 
                ', idOrg=' + idOrg + 
                ", idAdr=" + idAdr + 
                ', iOst0=' + iOst0 + 
                ', idGr=' + idGr);

    if( idGrp < 0 ) 
    {
    frmMain.cbHier.selectedIndex= 0; idHr= 0; idGr= 0
    }
 
    var patt= (idHr==32 ? "" : escape(frmMain.sTov.value));

    if(patt=="") // если ходим по категориям
    {

    }
    else // если находимся в поиске
    {
    if(patt.indexOf("^")==-1)
      patt='~ ' + patt;
    }

    console.log('patt=' + patt);

    $("#ldTovs").show()
    $.ajax({
    url: "tnt_order_tov.php",
    dataType: "html",
    data: {'par': par, hr: idHr, gr: idGr, org: idOrg, adr: idAdr, dt: datZ,
          ost: iOst0, unite:iUnite, pat: patt, child:idChild},
    success: function(dat, stat,xmlReq) {
      
    var patt= (idHr==32 ? "" : escape(frmMain.sTov.value)),
    idGr= idGrp
    
    if( idGrp < 0 ) 
    {
    frmMain.cbHier.selectedIndex= 0; idHr= 0; idGr= 0
    }
      $("tbody#tovs").html('Идет поиск ...')         
      //alert(dat);
      var Myobj = JSON.parse(dat); //.sort((a, b) => b["PRICE_ORG"] - a["PRICE_ORG"]));  
      globalObj = Myobj;
      
      var s=RenderJSON(Myobj,idGrp,idChild); 
      //alert(s);
      //if(div_name==undefined)
      var div_name="div#info_cats";
      //alert('выводим таблицу в слой ' + div_name);
      $(div_name).html(s);
   
      $("div#elem_count_info").html('Найдено: ' + Myobj.length + ' элементов');
      $('body, html').scrollTop($("#search-text").offset().top); // проматываем вниз

      $("#table").on( "click", "tr", function(){
      // do something
                                                                    /// alert( $(this).children("td:first").text() );
      });


      ///////////// описание по клику на товар /////////////////////////////

      $("tbody#tovs td:nth-child("+(1+cNAME)+")[desc=1]").addClass('desc').on('click',  function(ev) { console.log($(this).attr('id_tov'));
        
      $.ajax({
        type: 'POST',
        url: "mk_order_desc.php",
        dataType: 'json',
        cache: false,
        data: {
          id_tov: $(this).attr('id_tov'),
          id_grp: $(this).parent().attr('cgrp'),
          n_grp: $(this).parent().attr('ngrp')
        },
        success: function(data){
            alert(data);
        },
        error: function(xmlReq,stat, err) { alert("loadTov: "+stat+" ajax : "+err)},
        complete: function(xmlReq,stat) { }
      });


      })
    },
    error: function(xmlReq,stat, err) { alert("loadTov: "+stat+" ajax : "+err)},
    complete: function(xmlReq,stat) { $("#ldTovs").hide() }
  })
}
 

function loadTovs_Statistic() 
{
  // propose computed order based on statistics in [datOn-daysAgo,datOn], 24.08.16
  var
    idOrg= frmMain.corg.value,
    ia= frmMain.cbAddr.selectedIndex, idAdr= frmMain.cbAddr.options[ia].value,
    datZ= frmMain.datOn.value,
    //daysAgo= 90
    daysAgo = frmMain.nPeriod.value;
  $("#ldTovs").show();
  $.ajax({
    url: "tnt_order_stat.php",
    dataType: "html",
    data: {'par': par, org: idOrg, adr: idAdr, dt: datZ, ago: daysAgo },
    success: function(dat, stat,xmlReq) 
    {
      //alert(dat);
      //debugger;
      var Myobj = JSON.parse(dat);
      globalObj = Myobj;
      //alert('loadTovs_Statistic: кол-во элементов=' + Myobj.length-1);

      var s=RenderJSON(Myobj,0,0); 
      var div_name="div#info_cats";
      //alert('выводим таблицу в слой ' + div_name);
      $(div_name).html(s);


      $('body, html').scrollTop($(document).height()); // проматываем вниз
      //$('body, html').scrollTop($("#search-text").offset().top); // проматываем вниз

      $("tbody#tovs td:nth-child("+(1+cNAME)+")[desc=1]").addClass('desc').on('click',  function(ev) { console.log($(this).attr('id_tov'));
        
      /*$.ajax({
        type: 'POST',
        url: "mk_order_desc.php",
        dataType: 'json',
        cache: false,
        data: {
          id_tov: $(this).attr('id_tov'),
          id_grp: $(this).parent().attr('cgrp'),
          n_grp: $(this).parent().attr('ngrp')
        },
        success: function(data){
            alert(data);
        },
        error: function(xmlReq,stat, err) { alert("loadTov: "+stat+" ajax : "+err)},
        complete: function(xmlReq,stat) { }
      });*/
 
     })

    },
    error: function(xmlReq,stat, err) { alert("proposeOrder: "+stat+" ajax : "+err)},
    complete: function(xmlReq,stat) { $("#ldTovs").hide() }
  })
}


function loadTovs_Zalistovka() 
{
  var
    idOrg= frmMain.corg.value,
    ia= frmMain.cbAddr.selectedIndex, idAdr= frmMain.cbAddr.options[ia].value,
    datZ= frmMain.datOn.value,
    //daysAgo= 90
    daysAgo = frmMain.nPeriod.value;

    //alert('idOrg=' + idOrg + 'idAdr=' + idAdr + 'datZ=' + datZ + 'daysAgo=' + daysAgo);
  
  $("#ldTovs").show();
  $.ajax({
    url: "tnt_order_zal.php",
    dataType: "html",
    data: {'par': par, org: idOrg, adr: idAdr, dt: datZ, ago: daysAgo },
    success: function(dat, stat,xmlReq) {
      //alert(dat);
      
      var Myobj = JSON.parse(dat);
      globalObj = Myobj;
      //alert('loadTovs_Zalistovka: кол-во элементов=' + (Myobj.length-1));

      var s=RenderJSON(Myobj,0,0); 
      div_name="div#info_cats";
      //alert('выводим таблицу в слой ' + div_name);
      $(div_name).html(s);
      $('body, html').scrollTop($(document).height()); // проматываем вниз
      
      //$("tbody#tovs").html(dat)

      $("tbody#tovs td:nth-child("+(1+cKOL)+")").on('click',
          function(ev){ openKolDlg($(this),0)
      })

      $("tbody#tovs td:nth-child("+(1+cNAME)+")[desc=1]").addClass('desc').on('click',  function(ev) { console.log($(this).attr('id_tov'));
        
      $.ajax({
        type: 'POST',
        url: "mk_order_desc.php",
        dataType: 'json',
        cache: false,
        data: {
          id_tov: $(this).attr('id_tov'),
          id_grp: $(this).parent().attr('cgrp'),
          n_grp: $(this).parent().attr('ngrp')
        },
        success: function(data){
            alert(data);
        },
        error: function(xmlReq,stat, err) { alert("loadTov: "+stat+" ajax : "+err)},
        complete: function(xmlReq,stat) { }
      });
      })

      $("tbody#tovs td:nth-child("+(1+cCMC)+")[pic=1]").addClass('pic').on('click',
        function(ev) { loadPicture($(this)) })

      $("tbody#tovs tr[t=-1] td:nth-child("+(1+cNAME)+")").addClass('grp')
        .on('click',toRootDir)
      $("tbody#tovs tr[t=0] td:nth-child("+(1+cNAME)+")").addClass('grp').on('click',
        function(ev) { toUpDir( $(this) ) })
      if( $("#tDisc").next().filter(":hidden").length > 0 )
        $("tbody#tovs td:nth-child("+(1+cMDIS)+")").hide()
      },
                 
    error: function(xmlReq,stat, err) { alert("zalistovkaOrder: "+stat+" ajax : "+err)},
    complete: function(xmlReq,stat) { $("#ldTovs").hide() }
  });
}

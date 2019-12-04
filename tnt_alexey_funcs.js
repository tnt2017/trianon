
function onEditEnlist(evt) // after click on td (enlisted)
  {
    // bring up dlg to choose option, 13.02.17
    eEnl = evt.target;
    if (eEnl) {
      var offs = $(eEnl).offset(),
        x = offs.left - 40,
        y = offs.top + $(eEnl).height() + 6,
        idTov = $("td:eq(" + CMC + ")", eEnl.parentNode).text();
      frmMain.radEnl.value = (eEnl.parentNode.hasAttribute('mode') ?
        eEnl.parentNode.getAttribute('mode') : 5);
      frmMain.sEnlT.value = (eEnl.parentNode.hasAttribute('entxt') ?
        eEnl.parentNode.getAttribute('entxt') : '');
      frmMain.sEnlB.value = (eEnl.parentNode.hasAttribute('bc') ?
        eEnl.parentNode.getAttribute('bc') : '');
      frmMain.sEnlB.cmc = idTov; // 26.02.17
      $("div#enldlg").css({
        'left': x,
        'top': y
      }).show();
      frmMain.bnEnlOk.onclick = function(ev) {
        // new enlist code is stored in tr.mode, actual is in td(enlist).f
        var iMode = frmMain.radEnl.value,
          iEnlPrv = eEnl.hasAttribute('f') ? eEnl.getAttribute('f') : 0,
          iEnlCur = eEnl.parentNode.hasAttribute('mode') ? eEnl.parentNode.getAttribute('mode') : 0;
        if (iMode == "") {
          alert("Не выбран статус залистовки");
        } else if (iMode == iEnlCur) {
          alert('тот же статус');
        } else if (iMode > 0 && (iEnlPrv != iMode || frmMain.sEnlB.value || frmMain.sEnlT.value)) {
          //alert('onEditEnlist: set enlist, iMode='+iMode+', iPrv='+iEnlPrv)
          $(eEnl).text(aEnlV[iMode]).addClass('mod').parent().attr({
            'mode': iMode,
            'entxt': frmMain.sEnlT.value,
            'bc': frmMain.sEnlB.value
          });
        } else if (iMode > 0 && iEnlPrv == iMode) {
          //alert('onEditEnlist: remove enlist, iMode='+iMode+', iPrv='+iEnlPrv)
          $(eEnl).removeClass('mod').text(aEnlV[iMode]);
          $(eEnl).parent().removeAttr('mode entxt');
        } else
          alert('onEditEnlist: false clause with iMode=' + iMode + ', iEnlPrv=' + iEnlPrv);
        $("div#enldlg0").hide();
        $("div#enldlg1").hide();
        $("div#enldlg2").hide();
        $("div#enldlg3").hide();
        $("div#enldlg4").hide();
        $("div#enldlg5").hide();
      };
      frmMain.bnEnlCancel.onclick = function(ev) {
        $("div#enldlg").hide();
      };
      $("img#bcdown").click(function(ev) {
        var idTov = frmMain.sEnlB.cmc,
          idOrg = $("td#org").attr('v'),
          idAdr = $("td#adr").attr('v');
        //alert('bcdown: org='+idOrg+' adr='+idAdr+' tov='+idTov)
        $("div#cbxbc").load("prled_bc.php", {
            cmc: idTov,
            org: idOrg,
            adr: idAdr,
            'par': par
          },
          function(resp, stat, xmlReq) { // on complete
            if (stat == "success") {
              // loaded: table, tr,<td bc="">barcode text</td>
              //alert('resp:'+resp)
              // show
              var offs = $(frmMain.sEnlB).offset(),
                x = offs.left - 4,
                y = offs.top + $(frmMain.sEnlB).height() + 8;
              $("div#cbxbc").css({
                left: x,
                top: y
              }).show();
              // set actions
              $("div#cbxbc td").click(function(ev) {
                frmMain.sEnlB.value = $(this).attr('bc');
                $("div#cbxbc").hide();
              });
            } else
              alert("ajax status: " + stat);
          });
      });
    } else
      alert('onEditEnlist: invalid cell object');
  }



function initTabBody()  // 6.03.17
{
  // all but setting class on each col
  var tb= document.getElementById("cts");
  $("td:nth-child("+(1+CMC)+")",tb).click(onViewHistory);
  $("td:nth-child("+(1+TOV)+")",tb).click(onViewTovCard);
  $("td:nth-child("+(1+KONKPROC)+")",tb).click(onViewReports);
  // edit price/disc/kod
  $("td:nth-child("+(1+PRC)+")", tb).click(function(ev) {
      var ret= prompt("Новое значение фикс.цены :", $(this).text());
      if( ret != null )
        afterEdit( $(this).parent(), 1, ret);
  });
  $("td:nth-child("+(1+DISC)+")", tb).click(function(ev) {
      var ret= prompt("Новое значение фикс.скидки,% :", $(this).text());
      if( ret != null )
        afterEdit( $(this).parent(), 0, ret);
  });
  $("td:nth-child("+(1+KOD)+")", tb).click(function(ev) {
      var ret= prompt("Новое значение кода у клиента (пробел = удалить):", $(this).text());
      if( ret != null )
        $(this).text(ret==" "? "": ret).addClass('mod');
      $(this).parent().attr('modk', 1);
  });
  // edit enlisted, 06.02.17
  $("td:nth-child("+(1+ENL)+")", tb).click(onEditEnlist);
  $("thead#thdr tr:first th:eq("+ENL+")").click(TovOnEnlist);
  
  $('tr',tb).each(function(indx,elm){
        $('td:eq('+KONKPRC+')',elm).click(function(evn){ var td = $(this); SetMonPrice(td);});
      });
      
      $('tr',tb).each(function(indx,elm){
        $('td:eq('+POLKA+')',elm).click(function(evn){ var tr = $(this).parent(); SetPricePolka(tr);});
      });
}












 














function enterOrgStorage(e, idOrg, i)
{
  console.log('enterOrgStorage start');

  $("input[name=corg]").val(idOrg);
  $.ajax({
    url: "tnt_order_budd.php",
    dataType: 'json',
    data: {'id' : idOrg, 'par': par},
    success: function(dat,stat,xmlReq) {
      $("select[name=cbGruz]").html(dat.grz)
      $("select[name=cbAddr]").html(dat.adr)
      $("select[name=cbDoc]").html(dat.docs)
      
      frmMain.chkSF.checked= dat.sf
      frmMain.cbCre.selectedIndex= dat.cre
      $("span#agt").text(dat.agt+" ("+dat.cag+")").attr('cag', dat.cag)
      frmMain.cfirm.value= dat.firm
      
      $('input[name=sOrg]').val(localStorage.getItem(i + "~~" + "sOrg"))
      //localStorage.getItem(i + "~~" + "sorg")
      //localStorage.getItem(i + "~~" + "cbDoc")
      //localStorage.getItem(i + "~~" + "cbGruz")
      //localStorage.getItem(i + "~~" + "cbAddr")
      $('input[name=sZakNo]').val(localStorage.getItem(i + "~~" + "sZakNo"))
      $('select[name=cbHier] option[value='+localStorage.getItem(i + "~~" + "cbHier")+']').attr('selected', 'selected')
      $('input[name=datOn]').val(localStorage.getItem(i + "~~" + "datOn"))
      $('select[name=cbCre] option[value='+localStorage.getItem(i + "~~" + "cbCre")+']').attr('selected', 'selected')
      $('td#summ').html(localStorage.getItem(i + "~~" + "summ"))
      $('span#agt').html(localStorage.getItem(i + "~~" + "agt"))
      $('span#agt').attr('cag', localStorage.getItem(i + "~~" + "cag"))
      $('input[name=sRem]').val(localStorage.getItem(i + "~~" + "sRem"))
      $('input[name=chkOst0]').attr('checked', (localStorage.getItem(i + "~~" + "chkOst0") == 'true' ? 'checked' : ''))
      $('input[name=sTov]').val(localStorage.getItem(i + "~~" + "sTov"))
      $('input[name=chkSF]').attr('checked', (localStorage.getItem(i + "~~" + "chkSF") == 'true' ? 'checked' : ''))
      $('input[name=chkRsv]').attr('checked', (localStorage.getItem(i + "~~" + "chkRsv") == 'true' ? 'checked' : ''))
      $('input[name=chkBoN]').attr('checked', (localStorage.getItem(i + "~~" + "chkBoN") == 'true' ? 'checked' : ''))
      $('input[name=chkSmv]').attr('checked', (localStorage.getItem(i + "~~" + "chkSmv") == 'true' ? 'checked' : ''))
      $('input[name=chkAdd]').attr('checked', (localStorage.getItem(i + "~~" + "chkAdd") == 'true' ? 'checked' : ''))
      $('span#costLn').html(localStorage.getItem(i + "~~" + "costLn"))
      $('span#nacZak').html(localStorage.getItem(i + "~~" + "nacZak"))
      $('span#costLn2').html(localStorage.getItem(i + "~~" + "costLn"))
      $('span#nacZak2').html(localStorage.getItem(i + "~~" + "nacZak"))
      
      idx = localStorage.getItem(i + "~~" + "cbHier")
      k = localStorage.getItem(i + "~~" + "zakaz")

      globalBasket=JSON.parse("[]"); 
      if (localStorage.getItem(i + "~~" + "zakaz")) {
        globalBasket = JSON.parse(localStorage.getItem(i + "~~" + "zakaz"));
        //data = JSON.parse(localStorage.getItem(i + "~~" + "zakaz"));
      }
  
      RenderBasket(globalBasket);
      $("div#tov_sec").hide()
      $("div#zak_sec").show();
 
 /*     $("tbody#zakaz td:nth-child("+(1+cCMC)+")[pic=1]").addClass('pic').on('click',
		function(ev) { loadPicture($(this)) })*/
      
      $("input[name=bnToCart]").click();
    },
    complete: function(xmlReq,stat) {  }
  })
}


function enterOrg(idOrg)
{
  console.log('enterOrg start');

  $("input[name=corg]").val(idOrg);
  $.ajax({
    url: "tnt_order_budd.php",
    type: 'POST',
    dataType: 'json',
    data: {'id' : idOrg, 'par': par},
    success: function(dat,stat,xmlReq) {
     // alert(dat);
     // debugger;

      $("select[name=cbGruz]").html(dat.grz)
      $("select[name=cbAddr]").html(dat.adr)
      $("select[name=cbDoc]").html(dat.docs)
      //frmMain.chkSF.checked= dat.sf
      //frmMain.chkBoN.checked= 1
      //frmMain.cbCre.selectedIndex= dat.cre
      $("span#agt").text(dat.agt+" ("+dat.cag+")").attr('cag', dat.cag)
      frmMain.cfirm.value= dat.firm
      
     // getZalistovkaOrder(); ///////////////////////////////// 21-11-2019
    },
    error: function(xhr, textStatus, hz)
    {
    	console.log(textStatus);
    },
     complete: function() 
    {
      console.log('tnt_order_budd.php :: REQUEST complete');
    }
  });  
}

/*

function deleteOrg(e)
{
  $("input[name=corg]").val(0);
  $("select[name=cbGruz]").html('');
  $("select[name=cbAddr]").html('');
}

*/

function clearZakaz()
{
  console.log(clearZakaz);
  globalBasket=JSON.parse("[]"); 
  RenderBasket(globalBasket);

  $("#zak_type").text('');
  $("tbody#zakaz").html('');
  $("#summ").text('0.00');
  $("div#fxbtn td:eq('+cFxSum+')").text('');
}

function attachLineHandlers(tr)
{
  // for new line in zakaz
  $("td:eq("+cPRC+")",tr).attr('ini', $("td:eq("+cPRC+")",tr).text())
 // $("td:eq("+cKOL+")",tr).on('click', function(ev){ openKolDlg($(this),1) })
  $("td:eq("+cPRC+")",tr).addClass('prc').on('click',function(ev) {  // edit price
    var prcMin= parseFloat($(this).next().next().attr('v')),
      v= prompt("Новое значение цены (минимум "+prcMin+"):", $(this).text())
    if( v && parseFloat(v) > prcMin && v != $(this).text() )
      changeLinePrc( $(this), v)
  })
  $("td:eq("+cDISC+")",tr).addClass('prc').on('click',function(ev) {  // edit disc
    var disMax= parseFloat($(this).next().attr('m')),
      v= prompt("Новое значение скидки (максимум "+disMax+"):", $(this).text())
    if( v && parseFloat(v) < disMax && v != $(this).text() ) {
      var p= $(this).attr('b') * (1 - v/100)
      changeLinePrc( $(this).prev(), p)
    }
  })
  $("td:eq("+cGRP+")",tr).on('click',function(ev) {  // toggle reset group disc
    if( (''+$(this).prev().prev().attr('class')).indexOf('prcu') >= 0 ) {
      var v= $(this).text()
      $(this).text(v=='Да' ? '': 'Да')
    }
  })
  $("tbody#zakaz td:nth-child("+(1+cCMC)+")[pic=1]").on('click',
	function(ev) { loadPicture($(this)) 
        });
        
  $("tbody#zakaz td:nth-child("+(1+cNAME)+")[desc=1]").on('click',  function(ev) { console.log($(this).attr('id_tov'));
        
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
      });
}

function changeDoc()
{
  var iSel= frmMain.cbDoc.selectedIndex
  if( iSel == 0 ) {
    clearZakaz()
    $("p#msg").text(''); $("span#costLn").text(''); $("span#nacZak").text('')
    $("span#costLn2").text(''); $("span#nacZak2").text('')
  }
  else if( iSel > 0 ) { // load

    console.log('changeDoc iSel > 0 ');
    
    var idDoc= frmMain.cbDoc.options[iSel].value
    if( idDoc > 0 ) {
      $.ajax({
        url: "tnt_order_load.php",
        dataType: 'json',
        data: {'id' : idDoc, 'par': par},
        success: function(dat,stat,xmlReq) 
        {
          //alert(dat);
          
          globalBasket=JSON.parse("[]"); 
          for(var i=0;i<dat.length-1;i++)
          {
            console.log('name=' + dat[i].NAME);
            debugger;
            globalBasket.push(dat[i]);
          }

          RenderBasket(globalBasket);
          $("div#tov_sec").hide()
          $("div#zak_sec").show();

          /*
          $("tbody#zakaz").html(dat.tovs)
          frmMain.chkSF.checked= (dat.vkl & 1 ? 1 : 0)
          frmMain.chkRsv.checked= (dat.vkl & 2048 ? 1 : 0)
          frmMain.cbCre.selectedIndex= dat.cre
          var n= frmMain.cbAddr.options.length
          for(i=0; i < n; i++) {
            if( frmMain.cbAddr.options[i].value == dat.cadr ) {
              frmMain.cbAddr.selectedIndex= i; break }
          }
          frmMain.datOn.value= dat.dt
          updateTotal()
          $("tbody#zakaz tr").each(function(i,e) 
          {
            attachLineHandlers(e)
          })
          $("p#msg").html('Кредит-лимит: <b>'+dat.lim+'</b>, сальдо: <b>'+dat.sal+
              '</b>, долг: <b>'+dat.debt+'</b>')*/
        },
        error: function(xmlReq,stat, err) { alert("changeDoc: "+stat+" ajax : "+err)},
        complete: function(xmlReq,stat) {  }
      })
    }
  }

  //frmMain.bnMakeN.value= (iSel > 0 ? "Сохранить док-т":"Создать накладную") //закоментил 2 декабря 2019
}

function setPicture( eImg )
{
  var imgfn= eImg.text(),
    wd= parseInt(eImg.attr('wd')), ht= parseInt(eImg.attr('ht')),
    btnX= parseInt($("#picExit").css('width')),
    btnY= parseInt($("#picExit").css('height')),
    hdrY= Math.max( btnY, parseInt($("div#picPopup table").height())),
    winX= wd > 600 ? 600 :  (wd > 150 ? wd + 4 : 150),
    winY= ht > 600 ? 600 : (ht > 150 ? ht + hdrY + 2 : 150);
    console.log('img length = '+imgfn.length);
    var parts = imgfn.split('_');
    var pfx= '';
    if(parts.length == 1)
    {
        pfx = (imgfn.length == 10 ? imgfn.substr(0,3):imgfn.substr(0,5));
    }
    else
    {
        pfx = (parts[0].length == 6 ? parts[0].substr(0,3):parts[0].substr(0,5));
    }
  $("#picImg").attr({'src': '../tpic/'+pfx+'/'+imgfn, 'width': wd, 'height': ht})
  $("#picPopup").css({ width: winX, height: winY})
  $("#picPopup img:eq(0)").css({'left': winX - btnX - 2, top: 1 })
  $("#picPopup img:eq(1)").css({'left': 2, top: hdrY })
  $("#picPopup td.picsel").removeClass('picsel')
  eImg.addClass('picsel')
}

function loadPicture( eIdTov)  // 14.01.14
{
  var idTov= eIdTov.text(), off= eIdTov.offset(),
    x= off.left + eIdTov.width() + 10, y= off.top + eIdTov.height() + 2
  $("div#picPopup").css({ left: x, top: y}).show()
  $("div#picPopup table").load("mk_order_pic.php", { 'id' : idTov},
    function(msg, stat, xmlReq) {  // onComplete
      if( stat == 'success' ) {
        $("div#picPopup td").addClass('picn').on('click',function(ev) {
          setPicture( $(this) )
        })
        var img= $("div#picPopup td:eq(0)")
        if( img )
          setPicture(img)
      }
      else
        alert("error ("+stat+"): "+msg)
      $("#picExit").on('click',function(ev) {
        $("div#picPopup").hide()
        $("div#picPopup td:eq(0)").html('')
      })
    })
}




function OnChangeSelect()
{
  
  //alert('OnChangeSelect');
  $('select').on('change', function() {
  //alert( this.value );

  $("#div4_idKonkur").val(this.value );
  }); 
}







function loadPictures()  // 25.03.14
{
console.log('loading pics');
  var lstv= '', aTr= $("tbody#tovs tr").filter(function(ix) {
              return $(this).children('td').length > 7 })
  aTr.each(function(ix,e) {
    lstv+= '^'+$("td:eq("+cCMC+")", e).text()
  })
  lstv= lstv.substr(1)
  $.ajax({
    url: "mk_order_ldpic.php",
    dataType: 'json',
    data: {'lstov' : lstv},
    success: function(dat,stat,xmlReq) {
      aTr.each(function(ix, e) 
      {
         if(frmMain.bnLoadPics.checked)        
	{
	        $("td:eq("+cCMC+")", e).html(""); 
        	$("td:eq("+cCMC+")", e).append(dat.pics[ix]);  //html
	}
	else
	{
	        $("td:eq("+cCMC+")", e).html(""); 
	}
      })
      $("tbody#tovs img").css('float','right'); // 2.04.14
    },
    error: function(xmlReq,stat, err) { alert("loadPictures: "+stat+" ajax : "+err)},
    complete: function(xmlReq,stat) {  }
  })
}

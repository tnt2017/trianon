
function updateTotal()
{
  var  rSum= 0, rSum0= 0, nL=0
  $("tbody#zakaz tr").each(function(i, e) {
    var k=  $("td:eq("+cKOL+")", e).text(), prc= $("td:eq("+cPRC+")", e).text()
    if( k > 0 && prc > 0 ) {
      rSum += k * prc
      rSum0 += k * $("td:eq("+cMDIS+")", e).attr('v')
      nL++
    }
  })
  $("#summ").text(Math.round(rSum*100)/100)
  $("#costLn").text(nL ? Math.round(rSum/nL*100)/100 : 0)
  $("#nacZak").text(rSum0>0 ? Math.round((rSum/rSum0 - 1)*1000)/10 : 0)
  $("#costLn2").text($("#costLn").text())
  $("#nacZak2").text($("#nacZak").text())
  $("div#fxbtn td:eq("+cFxSum+")").html('Сумма: <b>'+$("#summ").text()+
    '</b>, строка: <b>'+$("#costLn").text()+'</b>')
}

function changeLinePrc( ePrc, prc )
{
  var p = Math.round(prc*100)/100
    base= parseFloat(ePrc.next().attr('b')),
    disc= (base > 0 ? Math.round((1-p/base)*10000)/100 : 0)
  if( ePrc.attr('ini') == p ) {
    ePrc.removeClass('prcu'); ePrc.next().removeClass('prcu') }
  else {
    ePrc.addClass('prcu'); ePrc.next().addClass('prcu') }
  ePrc.text(p)
  ePrc.next().text(disc)
  updateTotal()
  ePrc.parent().attr('mod',1)
  
  $('input[name=bnSaveL]').click();
}

function updateZakaz()  // use eKol,eKolA,iPane
{
  console.log('updateZakaz');
  console.log('eKol=' + eKol.text);
  console.log('eKolA=' + eKolA);

  var parent = eKol.parent();
  var eKOL = parent.children('td:eq('+cKOL+')');
  console.log('eKOL=' + eKOL.html());
  console.log('globalI=' + globalI);

  globalObj[globalI].KOL=eKOL.html();

  var dont_push=false;
  for(var i=0;i<globalBasket.length;i++)
  {
    if(globalBasket[i].ID==globalObj[globalI].ID)
    {
      globalBasket[i].KOL=eKOL.html();
      dont_push=true;
    }
  }

  if(dont_push==false)
  globalBasket.push( globalObj[globalI] );


  /*
  if( iPane==1 || parseInt(eKol.text()) >= 0 || eKol.text().length == 0) {
    if( eKol.attr('q') ) {  // 4.04.14 kol by quants
      var q= parseInt(eKol.attr('q')), kol= parseInt(eKol.text())
      if( q > 1 && (kol % q) )
        eKol.text( Math.round(kol/q)*q )
    }
    if( eKolA ) {  // found
      eKolA.text(eKol.text())
    }
    else if( iPane == 0 ) {  //  new line
      var parent = eKol.parent();
      var line = '';
      //copy one line
  
      var eNN =  parent.children('td:eq('+cNN+')');
      var ePIC = parent.children('td:eq('+cPIC+')');
      var eCMC = parent.children('td:eq('+cCMC+')');
      var eNAME = parent.children('td:eq('+cNAME+')');
      var eStat = parent.children('td:eq('+cStat+')');
      var eKOL = parent.children('td:eq('+cKOL+')');
      var eOST = parent.children('td:eq('+cOST+')');
      var ePRC = parent.children('td:eq('+cPRC+')');
      var eMONITOR = parent.children('td:eq('+cMONITOR+')');
      var eDISC = parent.children('td:eq('+cDISC+')');
      var eMDIS = parent.children('td:eq('+cMDIS+')');
 
      line += '<td>'+eNN.html()+'</td>';
      line += '<td>'+ePIC.html()+'</td>';
      line += '<td class="'+eCMC.attr('class')+'" pic="'+eCMC.attr('pic')+'">'+eCMC.html()+'</td>';

      line += '<td class="'+eNAME.attr('class')+'" style="'+eNAME.attr('style')+'"'
              +'title="'+eNAME.attr('title')+'" desc="'+eNAME.attr('desc')+'"'
              +'id_tov="'+eNAME.attr('id_tov')+'"'
              +'>'+eNAME.html()+'</td>';

      line += '<td class="'+eStat.attr('class')+'">'+eStat.html()+'</td>';
      line += '<td class="'+eKOL.attr('class')+'">'+eKOL.html()+'</td>';
      line += '<td class="'+eOST.attr('class')+'">'+eOST.html()+'</td>';
      line += '<td class="'+ePRC.attr('class')+'">'+ePRC.html()+'</td>';
      line += '<td class="'+eMONITOR.attr('class')+'">'+eMONITOR.html()+'</td>';

      line += '<td class="'+eDISC.attr('class')+'" b="'+eDISC.attr('b')+'">'+eDISC.html()+'</td>';
      line += '<td class="'+eMDIS.attr('class')+'" v="'+eMDIS.attr('v')+'" m="'+eMDIS.attr('m')+'">'+eMDIS.html()+'</td>';
   //   line += '<td class="'+eID.attr('class')+'">'+eID.html()+'</td>';


      $("tbody#zakaz").append('<tr>'+line+'<td></td></tr>')
      var tr= $("tbody#zakaz tr:last"), nn= $("tbody#zakaz tr").length
      $("td:eq("+cNN+")",tr).text(nn)
      tr.attr('mod',1)
      $("td:eq("+cNAME+")",tr).removeClass('seln')
      $("td:eq("+cKOL+")",tr).addClass('kmod')
      attachLineHandlers(tr)
    }
    if( iPane == 1 ) {
      eKol.parent().attr('mod',1); eKol.addClass('kmod')
    }
    updateTotal()
  }
  $("td:eq("+cNAME+")",eKol.parent()).removeClass('seln');

  unselect_tr_color(eKol);

  eKol= null
  */
}

function processDoc(ev)
{
  $("input[name=bnMakeN]").hide()
  $("input[name=bnMakeN2]").hide()

  
  var ix= frmMain.cbDoc.selectedIndex
  if( ix > 0 )
    updateDoc(ev, ix)
  else
    makeDoc(ev,"tnt_order_sav_nakl.php")
}


function processDoc2(ev)
{
  alert('Создаем счет!!!');
 // $("input[name=bnMakeN]").hide()
 // $("input[name=bnMakeN2]").hide()

  makeDoc(ev,"tnt_order_sav_bill.php")
}


function makeDoc(evt, script_name)
{
  
  if( idDocMade > 0 ) {
    alert('Документ уже создан либо находится в обработке.\n'+
    ' Для редактирования обновите страницу и выберите его из списка')
    $("input[name=bnMakeN]").show()
    return
  }


  if( $("tbody#zakaz tr").length > 0 ) {
    //debugger;

    var idOrg= frmMain.corg.value,   idAdr= selectVal(frmMain.cbAddr),
      idGrz= selectVal(frmMain.cbGruz), iCred= selectVal(frmMain.cbCre),
      iSF= frmMain.chkSF.checked ? 1 : 0,
      iRsv= frmMain.chkRsv.checked ? 2 : 0,
      iSmv= frmMain.chkSmv.checked ? 4 : 0,
      iBoN= frmMain.chkBoN.checked ? 256 : 0,  // bonusAsNak, 18.08.15
      iAdd= frmMain.chkAdd.checked && !iSmv && !iRsv ? 32 : 0,
      iFlg= iSF + iRsv + iSmv + iAdd + iBoN,
      
      idAg= $("span#agt").attr('cag')

    if( idOrg > 0 && idGrz > 0 && idAdr > 0 ) {
      // collect lines
      var aMC=  '', aKol= '', aSm='', aPrc='', datZ= frmMain.datOn.value
      $("tbody#zakaz tr").each(function(ix, e) {
 
        var  kol= $("td:eq("+cKOL+")",e).text();
        if( kol > 0 ) 
        {
          //debugger;
          var cmc= $("td:eq("+cCMC+")",e).text(), ePrc= $("td:eq("+7+")", e),
            sm= 0;

            //alert(ePrc.text());

          aMC+= ','+cmc
          aKol+= ','+kol
    ///      if( ePrc.text() != ePrc.attr('ini') ) /// 20-11-2019
            sm= Math.round(kol * ePrc.text()*100)/100
          aSm+= ','+sm

          aPrc+=',' + ePrc.text()
     ///     if( ePrc.text() != ePrc.attr('ini') && $("td:eq("+cGRP+")",e).text()=='Да')  /// 20-11-2019
         //   aPrc+= ','+cmc+','+ePrc.text()
        
            console.log('cmc ='+cmc+'|kol = '+kol + '|prc=' + ePrc.text());
        }
      })
      if( aMC == '' ) {
        alert("нет ненулевых товарных строк");
        $("input[name=bnMakeN]").show()
        return;
      }
      //alert(aPrc);
 
      aMC= aMC.substr(1); aKol= aKol.substr(1); aPrc= aPrc.substr(1)
      aSm= aSm.substr(1)
      //alert('save: mc='+aMC+' kol='+aKol+' prc='+aPrc)
      $("div#pgbar").show()  // make evident saving process, 18.08.15
      // send save request
      idDocMade= 1




      $("p#msg").load(script_name,
        {'par': par, org: idOrg, grz: idGrz, adr: idAdr, cre: iCred, agt: idAg,
          flg: iFlg, 'dt': datZ,
          'cmc': aMC, 'kol': aKol, 'prc': aPrc, 'sum': aSm,
          zno: escape(frmMain.sZakNo.value), txt: escape(frmMain.sRem.value),
          ip: ipadr, rusr: usr},
        function(resp, stat, xmlReq) 
        { // complete
          $("div#pgbar").hide()
          //debugger;
          //alert('момент создания накладной');
          alert(resp);

          if( stat == "success" ) {
            if( resp.indexOf('создана накладная') >= 0 ) // 23.04.14
              idDocMade= 1
            
	      if (typeof $('input[name=clocal]').val() !== 'undefined' && $('input[name=clocal]').val().length > 0)
	      {
		l = $('input[name=clocal]').val()
		localStorage.clear(l)
		cntZakLocal --
	      }
          }
          else
          {
	    idDocMade = 0
            alert("Ошибка создания документа :"+resp)
          }
          $("input[name=bnMakeN]").show()
        })
    }
    else
    {
      alert("неверный клиент/грузополучатель/адрес")
      $("input[name=bnMakeN]").show()
    }
  }
  else
  {
    alert("пустой заказ")
    $("input[name=bnMakeN]").show()
  }
}

function updateDoc(evt, idx)  // 6.10.13
{
  var idDoc= (idx>0 ? frmMain.cbDoc.options[idx].value : 0)
  if( idDoc > 0 ) {
    //collect lines
    var ids='', cmc='',kols='',prcs=''
    $("tbody#zakaz tr[mod=1]").each(function(i,e) {
      var idL= $("td:eq("+cCMC+")",e).attr('ln')
      ids+= ','+(idL > 0 ? idL : 0)
      cmc+= ','+$("td:eq("+cCMC+")",e).text()
      kols+= ','+$("td:eq("+cKOL+")",e).text()
      prcs+= ','+$("td:eq("+cPRC+")",e).text()
    })
    if( ids == '' ) { 
      alert('Нет измененных строк');
      $("input[name=bnMakeN]").show()
      return 
    }
    ids= ids.substr(1);   cmc= cmc.substr(1)
    kols= kols.substr(1); prcs= prcs.substr(1)
    $("p#msg").load("mk_order_upd.php", {'par': par, 'id': idDoc, ln: ids,
      'cmc': cmc, 'kol': kols, 'prc': prcs},
      function(resp, stat, xmlReq) { // complete
        if( stat != "success" )
          alert("Сохранение документа :"+resp)
        else
          $("tbody#zakaz tr[mod=1]").attr('mod',0)
        $("input[name=bnMakeN]").show()
      })
  }
  else
  {
    alert('Обновление документа: неверный id "'+idDoc+'"')
    $("input[name=bnMakeN]").show()
  }
}



function proposeOrder() 
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
    url: "mk_order_pro.1.php",
    dataType: "html",
    data: {'par': par, org: idOrg, adr: idAdr, dt: datZ, ago: daysAgo },
    success: function(dat, stat,xmlReq) 
    {
      alert('123456789');
      $("tbody#tovs").html(dat)
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
    
                /*$("tbody#tovs_header th:nth-child("+(1+cStat)+")").hide();
      $("tbody#tovs td:nth-child("+(1+cStat)+")").hide();*/
    },
    /*{
        $("#zak_type").text('Заказ по статистике');
      $("tbody#zakaz").html(dat)
      $("tbody#zakaz td:nth-child("+(1+cKOL)+")").on('click',
          function(ev){ openKolDlg($(this),0)
      })
      $("tbody#zakaz td:nth-child("+(1+cCMC)+")[pic=1]").addClass('pic').on('click',
        function(ev) { loadPicture($(this)) })
        if( $("#tDisc").next().filter(":hidden").length > 0 )
        $("tbody#zakaz td:nth-child("+(1+cMDIS)+")").hide()
      $("div#tov_sec").hide()
      $("div#zak_sec").show()
      updateTotal()
    },*/
    error: function(xmlReq,stat, err) { alert("proposeOrder: "+stat+" ajax : "+err)},
    complete: function(xmlReq,stat) { $("#ldTovs").hide() }
  })
}


function getZalistovkaOrder() 
{
  var
    idOrg= frmMain.corg.value,
    ia= frmMain.cbAddr.selectedIndex, idAdr= frmMain.cbAddr.options[ia].value,
    datZ= frmMain.datOn.value,
    //daysAgo= 90
    daysAgo = frmMain.nPeriod.value;
  $("#ldTovs").show();
  $.ajax({
    url: "mk_order_zal.php",
    dataType: "html",
    data: {'par': par, org: idOrg, adr: idAdr, dt: datZ, ago: daysAgo },
    success: function(dat, stat,xmlReq) {
      $("tbody#tovs").html(dat)
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
    
                /*$("tbody#tovs_header th:nth-child("+(1+cStat)+")").hide();
      $("tbody#tovs td:nth-child("+(1+cStat)+")").hide();*/
    },
/*    {
        $("#zak_type").text('Заказ по залистовке');
      $("tbody#zakaz").html(dat)
      $("tbody#zakaz td:nth-child("+(1+cKOL)+")").on('click',
          function(ev){ openKolDlg($(this),0)
      })
      $("tbody#zakaz td:nth-child("+(1+cCMC)+")[pic=1]").addClass('pic').on('click',
        function(ev) { loadPicture($(this)) })
      //if( $("#tDisc").next().filter(":hidden").length > 0 )
      //  $("tbody#zakaz td:nth-child("+(1+cMDIS)+")").hide()
      $("div#tov_sec").hide()
      $("div#zak_sec").show()
      updateTotal()
    },*/
    error: function(xmlReq,stat, err) { alert("zalistovkaOrder: "+stat+" ajax : "+err)},
    complete: function(xmlReq,stat) { $("#ldTovs").hide() }
  });
}

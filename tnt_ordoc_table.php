
<table id='table1' cellspacing=5 id="ordoc" border=1 class="myclass222">
 <tr>
  <td>Контрагент</td>
   <td>
   <input name=sOrg value='<?php echo $org;?>' class=org autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" disabled onclick="select(this);"  >
   </td>
 </tr>
 
 <tr>
   <td>На дату</td>

   <td>
  <input name=datOn value='<?php echo $dtOn;?>' onChange="this.value=CheckDate(this.value);">
  <img src="images/cal.gif" width=16 height=16 onclick="ds_sh('datOn')" />
  </td>
 
</tr><tr>
  <td>Документ </td><td><select name=cbDoc class='myselect  org' size=1></select></td>
</tr> 

<tr>
  <td>Грузополучатель</td>
  <td><select name=cbGruz size=1 class='myselect  org'></select></td>
</tr>

<tr>
  <td>Адрес доставки</td>
  <td><select name=cbAddr size=1 class='myselect  org'></select></td>
</tr>

<tr>
  <td>Примечание</td>
  <td><input name=sRem class="org"></td>
</tr>

<tr>
  <td>Координаты</td>
  <td><input name=coords class="org"></td>
</tr>

<tr>
  <td colspan=2>
  <input type=button name=bnProZak value="Заказ по статистике" onclick="loadTovs_Statistic()">
  <input type=button name=bnZalZak value="Заказ по залистовке" onclick="loadTovs_Zalistovka()">
  <input type="button"  value="Лист ожидания" id="btn_wait_list" onclick="RenderWaitList()"> 

  <input type="button" name="bnAgDeliv" value="Журнал на доставку"  >
  <input type="button" name="bnDostavkaJournal" value="Журнал возвратов"  >
  <input type="button" name="bnJournalSchetov" value="Журнал счетов">
  <input type="button" name="bnOpenCredHist" value="Кредитная история"  >
  
  <input type=button name=bnSaveL value="Сохранить локально">
  <input type=button name=bnDelL value="Удалить лок. данные">
  <input type="button" name="test" value="test" onclick="load_journal()"  >

</td>  
</tr>
</table>

<br>

<table border=1 id='tab_btns' class="myclass222"> 
<tr>
    <td><input type=checkbox name=chkRsv value="1" checked></td>
    <td>резерв</td>
    <td><input type=checkbox name=chkAdd value="1" ></td>
    <td><span title="к уже существующему заказу (будет та же дата, только без резерва/самовывоза)"> дополнение к заказу</span></td>
    <td><input type=checkbox name=chkSmv value="1"></td>
    <td>самовывоз</td>
</tr>
</table>

<br>

<div style="display: none;">
        лишние кнопки
        <input type="button" name="bnOpenRept" value="Открыть отчет у кл-та" >
        
        Номер заказа
        <input name=sZakNo class="org">               
        
        <tr>
            <td>Статистика за последние X дней</td>   
            <td><input name='nPeriod' class="org" value='<?php echo $period; ?>'></td>
            </tr>
        </tr>
        
        Расчет <select name=cbCre size=1>
          <option value=0>наличный<option value=1>кред-наличный<option value=2>безналичный
          <option value=3>резерв<option value=4>предоплата</select>
        
        
        <input type=checkbox name=chkBoN value="1" >
        <span title="вместо бонуса-счета будет бонус-накладная">бонус -&gt; накл<span>
        
        <input type=checkbox name=chkSF value="1">
        счет-фактура
        
        <input type="checkbox" name="chkUnite" value="1">
        Объединять с дочерними группами
</div>
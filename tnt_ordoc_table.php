
<table id='table1' cellspacing=5 id="ordoc" border=1 class="myclass222">
 <tr>
  <td>����������</td>
   <td>
   <input name=sOrg value='<?php echo $org;?>' class=org autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" disabled onclick="select(this);"  >
   </td>
 </tr>
 
 <tr>
   <td>�� ����</td>

   <td>
  <input name=datOn value='<?php echo $dtOn;?>' onChange="this.value=CheckDate(this.value);">
  <img src="images/cal.gif" width=16 height=16 onclick="ds_sh('datOn')" />
  </td>
 
</tr><tr>
  <td>�������� </td><td><select name=cbDoc class='myselect  org' size=1></select></td>
</tr> 

<tr>
  <td>���������������</td>
  <td><select name=cbGruz size=1 class='myselect  org'></select></td>
</tr>

<tr>
  <td>����� ��������</td>
  <td><select name=cbAddr size=1 class='myselect  org'></select></td>
</tr>

<tr>
  <td>����������</td>
  <td><input name=sRem class="org"></td>
</tr>

<tr>
  <td>����������</td>
  <td><input name=coords class="org"></td>
</tr>

<tr>
  <td colspan=2>
  <input type=button name=bnProZak value="����� �� ����������" onclick="loadTovs_Statistic()">
  <input type=button name=bnZalZak value="����� �� ����������" onclick="loadTovs_Zalistovka()">
  <input type="button"  value="���� ��������" id="btn_wait_list" onclick="RenderWaitList()"> 

  <input type="button" name="bnAgDeliv" value="������ �� ��������"  >
  <input type="button" name="bnDostavkaJournal" value="������ ���������"  >
  <input type="button" name="bnJournalSchetov" value="������ ������">
  <input type="button" name="bnOpenCredHist" value="��������� �������"  >
  
  <input type=button name=bnSaveL value="��������� ��������">
  <input type=button name=bnDelL value="������� ���. ������">
  <input type="button" name="test" value="test" onclick="load_journal()"  >

</td>  
</tr>
</table>

<br>

<table border=1 id='tab_btns' class="myclass222"> 
<tr>
    <td><input type=checkbox name=chkRsv value="1" checked></td>
    <td>������</td>
    <td><input type=checkbox name=chkAdd value="1" ></td>
    <td><span title="� ��� ������������� ������ (����� �� �� ����, ������ ��� �������/����������)"> ���������� � ������</span></td>
    <td><input type=checkbox name=chkSmv value="1"></td>
    <td>���������</td>
</tr>
</table>

<br>

<div style="display: none;">
        ������ ������
        <input type="button" name="bnOpenRept" value="������� ����� � ��-��" >
        
        ����� ������
        <input name=sZakNo class="org">               
        
        <tr>
            <td>���������� �� ��������� X ����</td>   
            <td><input name='nPeriod' class="org" value='<?php echo $period; ?>'></td>
            </tr>
        </tr>
        
        ������ <select name=cbCre size=1>
          <option value=0>��������<option value=1>����-��������<option value=2>�����������
          <option value=3>������<option value=4>����������</select>
        
        
        <input type=checkbox name=chkBoN value="1" >
        <span title="������ ������-����� ����� �����-���������">����� -&gt; ����<span>
        
        <input type=checkbox name=chkSF value="1">
        ����-�������
        
        <input type="checkbox" name="chkUnite" value="1">
        ���������� � ��������� ��������
</div>
<?php
$MESS ['VBCHBB_BONUSCARD_IMPORT'] = 'Import a list of bonus cards';
$MESS ['VBCHBONUSCARDIMPORT_OPT_READFILE'] = 'Read File';
$MESS ['VBCHBB_BONUSCARD_IMPORT_CURRENT'] = 'Current action';
$MESS ['VBCHBB_BONUSCARD_IMPORT_CURRENTID'] = 'I am processing a record with ID #';
$MESS ['VBCHBB_BONUSCARD_IMPORT_WORK'] = 'I work ...';
$MESS ['VBCHBB_BONUSCARD_IMPORT_ALRT'] = 'Failed to get data';
$MESS ['VBCHBB_BONUSCARD_IMPORT_START'] = 'Start';
$MESS ['VBCHBB_BONUSCARD_IMPORT_STOP'] = 'Stop';
$MESS ['VBCHBB_BONUSCARD_IMPORT_HELP'] = 'Import bonus cards from a file in CSV format. <br/> If you have a file in MS EXCEL format - select File> Save As ... select the type of file * .csv <br/>
Very <b> IMPORTANT </b> for the separator to be <b>; </b> (semicolon). <br/> The file must be in the same format as the site <br/>
The topmost line of the file is unreadable - in it you can indicate the designation of the fields (for yourself) <br/>
File example <br/>
<table border = "1">
<tr>
<td> ID; </td> <td> USERID; </td> <td> NUM; </td> <td> LID; </td> <td> ACTIVE; </td> <td> DEFAULTBONUS; </td> <td> BONUSACCOUNTS </td>
</tr>
<tr>
<td> 1; </td> <td> 1; </td> <td> ACTIVELKNWEF2IH34; </td> <td> s1; </td> <td> Y; </td> <td> 500; </td> <td> 1 </td>
</tr>
</table> <br/>
<b> Legend </b> <br/>
<ul>
<li> ID - the number of the order </li>
<li> USERID - user ID on the site (optional) </li>
<li> NUM - card number (required) </li>
<li> LID - binding to the site (mandatory) </li>
<li> ACTIVE - card activity (Y or N) - mandatory </li>
<li> DEFAULTBONUS - The number of bonuses upon activation </li>
<li> BONUSACCOUNTS -ID of the bonus account to bind (default 1) </li>
</ul> <br/> Download Order: Add File> Read File> Start <br/>
If during the import process an error occurs, check the source csv file for correctness! ';
<?php
$MESS['VBCHBB_BONUSCARD_IMPORT']='Импорт списка бонусных карт';
$MESS['VBCHBONUSCARDIMPORT_OPT_READFILE']='Прочитать файл';
$MESS['VBCHBB_BONUSCARD_IMPORT_CURRENT']='Текущее действие';
$MESS['VBCHBB_BONUSCARD_IMPORT_CURRENTID']='Обрабатываю запись с ID #';
$MESS['VBCHBB_BONUSCARD_IMPORT_WORK']='Работаю...';
$MESS['VBCHBB_BONUSCARD_IMPORT_ALRT']='Сбой в получении данных';
$MESS['VBCHBB_BONUSCARD_IMPORT_START']='Старт';
$MESS['VBCHBB_BONUSCARD_IMPORT_STOP']='Стоп';
$MESS['VBCHBB_BONUSCARD_IMPORT_HELP']='Импорт бонусных карт из файла в формате CSV<br/> Если у Вас файл в формате MS EXCEL - выберите Файл > Сохранить как... укажите тип файла *.csv<br/>
Очень <b>ВАЖНО</b> чтобы разделитель был <b>;</b> (точка с запятой).<br/>Файл должен быть в том же формате, что и сайт<br/>
Самая верхняя строка файла не читается - в ней Вы можете указать обозначение полей (для себя)<br/>
Пример файла<br/>
<table border="1">
<tr>
<td>ID;</td><td>USERID;</td><td>NUM;</td><td>LID;</td><td>ACTIVE;</td><td>DEFAULTBONUS;</td><td>BONUSACCOUNTS</td>
</tr>
<tr>
<td>1;</td><td>1;</td><td>ACTIVELKNWEF2IH34;</td><td>s1;</td><td>Y;</td><td>500;</td><td>1</td>
</tr>
</table><br/>
<b>Обозначения</b><br/>
<ul>
<li>ID - номер попорядку</li>
<li>USERID - ID пользователя на сайте (необязательное)</li>
<li>NUM - номер карты (обязательное)</li>
<li>LID - привязка к сайту (обязательное)</li>
<li>ACTIVE - активность карты (Y или N) - обязательное</li>
<li>DEFAULTBONUS - Количество бонусов при активации</li>
<li>BONUSACCOUNTS -ID бонусного счета для привязки (по умолчанию 1) </li>
</ul><br/>Порядок загрузки: Добавить файл > Прочитать файл > Старт<br/>
Если в процессе импорта возникнет ошибка проверьте исходный csv файл на корректность!';
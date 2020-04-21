<?
$MESS ["VBCHBB_EVENT_TYPE_NAME"] = "Cистема бонусов.Программы лояльности - шаблоны";
$MESS ["VBCHBB_EVENT_TYPE_DESC"] = "#DESCRIPTION# - Описание
#MESSAGE# - из шаблона профиля
#ACTIVE_FROM# - Активны с
#ACTIVE_TO# - Активны до
#BONUS# - Добавлено бонусов
#BONUS_ALL#  - Всего бонусов
#USERNAME# - Имя Фамилия пользователя или Логин
#USEREMAIL# - EMail пользователя";
$MESS ["VBCHBB_EVENT_SUBJECT_ADD"] = "#SITE_NAME#: Вам добавлены бонусы";
$MESS ["VBCHBB_EVENT_SUBJECT_DELETE"] = "#SITE_NAME#: Ваши бонусы списаны";
$MESS ["VBCHBB_EVENT_SUBJECT_PAY"] = "#SITE_NAME#: Вы оплатили заказ бонусами";
$MESS ["VBCHBB_EVENT_SUBJECT_STATISTIC"] = "#SITE_NAME#: Статистика по бонусному счету";
$MESS ["VBCHBB_EVENT_SUBJECT_PREDELETE"] = "#SITE_NAME#: Через #DAY# дня Ваши бонусы сгорят";
$MESS ["VBCH_SENDQUE_EVENT_BODY_PREDELETE"]="
  Здравствуйте, #USERNAME#

Ваши бонусы сгорят на сайте #SITE_NAME#

Сгорит бонусов: #BONUS#
Активность с #ACTIVE_FROM# по #ACTIVE_TO#
Остаток бонусов: #BONUS_ALL#

--------------------------------------------------------------------------------------
письмо сгенерированно автоматически
  ";

$MESS ["VBCH_SENDQUE_EVENT_BODY_ADD"] = "Здравствуйте, #USERNAME#

Вам добавлены бонусы на сайте #SITE_NAME#
#MESSAGE#
#DESCRIPTION#

Добавлено бонусов: #BONUS#
Активность с #ACTIVE_FROM# по #ACTIVE_TO#
Всего бонусов: #BONUS_ALL#


--------------------------------------------------------------------------------------
письмо сгенерированно автоматически
  ";
$MESS ["VBCH_SENDQUE_EVENT_BODY_DELETE"] = "Здравствуйте, #USERNAME#

На сайте #SITE_NAME# списаны бонусы

#DESCRIPTION#

Списано бонусов: #BONUS#
Активность с #ACTIVE_FROM# по #ACTIVE_TO#
Всего бонусов: #BONUS_ALL#


--------------------------------------------------------------------------------------
письмо сгенерированно автоматически
  ";
$MESS ["VBCH_SENDQUE_EVENT_BODY_PAY"] = "Здравствуйте, #USERNAME#

Вы оплатили часть (или весь) заказ на сайте #SITE_NAME# бонусами

#DESCRIPTION#

Списано бонусов: #BONUS#
Осталось бонусов: #BONUS_ALL#


--------------------------------------------------------------------------------------
письмо сгенерированно автоматически
  ";
$MESS ["VBCH_SENDQUE_EVENT_BODY_STATISTIC"] = "Здравствуйте, #USERNAME#

Статистика бонусной программы:

#DESCRIPTION#

Всего бонусов: #BONUS_ALL#


--------------------------------------------------------------------------------------
письмо сгенерированно автоматически
  ";
$MESS['ITR_VBCH_ERROR_FILTER']='Ошибка проверки фильтра профиля';
$MESS['VBCHBB_SALE_NOT_VERSION']='Версия модуля sale меньше 16.5.0';
$MESS['VBCHBB_ALLBONUS_ADD']='Вам добавили бонусы';
$MESS['VBCHBB_ALLBONUS_DELETE']='Ваши бонусы списаны';
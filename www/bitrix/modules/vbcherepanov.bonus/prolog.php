<?
use \Bitrix\Main\Localization\Loc;
Loc::loadLanguageFile(__FILE__);
define("ADMIN_MODULE_NAME", "vbcherepanov.bonus");
define("ADMIN_MODULE_ICON", "<a href=\"/bitrix/admin/vbchbonus.php?lang=".LANG.">
		<img src=\"/bitrix/images/vbcherepanov.bonus/icon32.png\" width=\"32\" height=\"32\" border=\"0\" alt=\"".
        Loc::getMessage("VBCHBONUS_TITLE")."\" title=\"".Loc::getMessage("VBCHBONUS_TITLE")."\"></a>");
?>
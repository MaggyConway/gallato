<?

namespace Acrit\Bonus;

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;

// Core (part 1)
$strCoreId = 'vbcherepanov.bonus';
$strModuleId = $ModuleID = 'vbcherepanov.bonus';

define('ADMIN_MODULE_NAME', $strModuleId);
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $strModuleId . '/prolog.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $strModuleId . '/install/version.php');

IncludeModuleLangFile(__FILE__);
\CJSCore::Init(array('jquery', 'jquery2'));

// Check rights
if ($APPLICATION->GetGroupRight($strModuleId) == 'D') {
   $APPLICATION->authForm(GetMessage("ACCESS_DENIED"));
}

// Input data
$obGet = \Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList();
$arGet = $obGet->toArray();
$obPost = \Bitrix\Main\Context::getCurrent()->getRequest()->getPostList();
$arPost = $obPost->toArray();

// Module
\Bitrix\Main\Loader::includeModule($strModuleId);

// Page title
$strPageTitle = Loc::getMessage('ACRIT_BONUS_PAGE_TITLE_SUPPORT');

// Core (part 2, visual)
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

// Set page title
$APPLICATION->SetTitle($strPageTitle);

// Tab control
$arTabs = [
    [
        'DIV' => 'documentation',
        'TAB' => Loc::getMessage('ACRIT_BONUS_TAB_DOCUMENTATION_NAME'),
        'TITLE' => Loc::getMessage('ACRIT_BONUS_TAB_DOCUMENTATION_DESC'),
    ],
    [
        'DIV' => 'video',
        'TAB' => Loc::getMessage('ACRIT_BONUS_TAB_VIDEO_NAME'),
        'TITLE' => Loc::getMessage('ACRIT_BONUS_TAB_VIDEO_DESC'),
    ],
    [
        'DIV' => 'ask',
        'TAB' => Loc::getMessage('ACRIT_BONUS_TAB_ASK_NAME'),
        'TITLE' => Loc::getMessage('ACRIT_BONUS_TAB_ASK_DESC'),
    ],
    [
        'DIV' => 'idea',
        'TAB' => Loc::getMessage('ACRIT_BONUS_TAB_IDEA_NAME'),
        'TITLE' => Loc::getMessage('ACRIT_BONUS_TAB_IDEA_DESC'),
    ]
];
?><div id="acrit_exp_support"><?
// Start TabControl (via CAdminForm, not CAdminTabControl)
$obTabControl = new \CAdminForm('AcritExpSupport', $arTabs);
$obTabControl->Begin(array(
    'FORM_ACTION' => $APPLICATION->GetCurPageParam('', array()),
));

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 1. Documentation
$obTabControl->BeginNextFormTab();
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
$obTabControl->BeginCustomField('FAQ', Loc::getMessage('ACRIT_BONUS_FAQ'));
$strUrl = 'https://www.acrit-studio.ru/technical-support/configuring-modules-bonus/';
?>
   <tr class="heading"><td><?= $obTabControl->GetCustomLabelHTML() ?></td></tr>
   <tr>
      <td style="text-align:center;">
         <div><a href="<?= $strUrl; ?>" target="_blank"><?= $strUrl; ?></a></div><br/>
      </td>
   </tr>
   <?
   $obTabControl->EndCustomField('FAQ');

//
   $obTabControl->BeginCustomField('REQUIREMENTS_1', Loc::getMessage('ACRIT_BONUS_REQUIREMENTS_1'));
   $strUrl = 'https://www.acrit-studio.ru/technical-support/configuring-the-module-export-on-trade-portals/test-your-environment-before-configuring-the-module-acrit-export/';
   ?>
   <tr class="heading"><td colspan="2"><?= $obTabControl->GetCustomLabelHTML() ?></td></tr>
   <tr>
      <td style="text-align:center;">
         <div><?= Loc::getMessage('ACRIT_BONUS_REQUIREMENTS_TEXT') ?></div><br/>
      </td>
   </tr>
   <?
   $obTabControl->EndCustomField('REQUIREMENTS_1');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 2. Video
   $obTabControl->BeginNextFormTab();
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
   $obTabControl->BeginCustomField('VIDEO', Loc::getMessage('ACRIT_BONUS_VIDEO_FIELD'));
   ?>
   <tr class="heading"><td><?= $obTabControl->GetCustomLabelHTML() ?></td></tr>
   <tr>
      <td style="text-align:center;">
         <div><iframe width="800" height="500" src="https://www.youtube.com/embed/ene4qDMdn6A?list=PLnH5qqS_5Wnzw10GhPty9XgZSluYlFa4y" frameborder="0" allowfullscreen></iframe><br/>
      </td>
   </tr>
   <tr>
      <td style="text-align:center;">
         <h2><?= Loc::getMessage('ACRIT_BONUS_VIDEO_2') ?></h2>
         <div><iframe width="800" height="500" src="https://www.youtube.com/embed/8uHbMFNRZqo" frameborder="0" allowfullscreen></iframe><br/>
      </td>
   </tr>
   <tr>
      <td style="text-align:center;">
         <h2><?= Loc::getMessage('ACRIT_BONUS_VIDEO_3') ?></h2>
         <div><iframe width="800" height="500" src="https://www.youtube.com/embed/_ABdZa-R-Iw" frameborder="0" allowfullscreen></iframe><br/>
      </td>
   </tr>
   <tr>
      <td style="text-align:center;">
         <h2><?= Loc::getMessage('ACRIT_BONUS_VIDEO_4') ?></h2>
         <div><iframe width="800" height="500" src="https://www.youtube.com/embed/MgUZ1VoJoHw" frameborder="0" allowfullscreen></iframe><br/>
      </td>
   </tr>
   <?
   $obTabControl->EndCustomField('VIDEO');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 3. Ask
   $obTabControl->BeginNextFormTab();
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
   $obTabControl->BeginCustomField('REQUIREMENTS_2', Loc::getMessage('ACRIT_BONUS_REQUIREMENTS_2'));
   $strUrl = 'https://www.acrit-studio.ru/technical-support/configuring-the-module-export-on-trade-portals/test-your-environment-before-configuring-the-module-acrit-export/';
   ?>
   <tr class="heading"><td colspan="2"><?= $obTabControl->GetCustomLabelHTML() ?></td></tr>
   <tr>
      <td colspan="2">
         <div><?= Loc::getMessage('ACRIT_BONUS_REQUIREMENTS_TEXT', ['#URL#' => $strUrl]); ?></div><br/>
      </td>
   </tr>
   <?
   $obTabControl->EndCustomField('REQUIREMENTS_2');

//
   $obTabControl->BeginCustomField('ASK_FORM', Loc::getMessage('ACRIT_BONUS_ASK_FORM'));
   ?>
   <tr class="heading"><td colspan="2"><?= $obTabControl->GetCustomLabelHTML() ?></td></tr>
   <tr>
      <td width="40%" class="adm-detail-content-cell-l" style="padding-top:10px; vertical-align:top;">
         <?= Loc::getMessage('ACRIT_BONUS_ASK_FORM_FIELD'); ?>
      </td>
      <td width="60%" class="adm-detail-content-cell-r">
         <div style="margin-bottom:6px;">
            <textarea cols="70" rows="10" style="resize:vertical; width:96%;" data-role="ticket-message"
                      data-error="<?= Loc::getMessage('ACRIT_BONUS_ASK_FORM_ERROR_EMPTY'); ?>"></textarea>
         </div>
         <div>
            <input type="button" value="<?= Loc::getMessage('ACRIT_BONUS_ASK_FORM_BUTTON'); ?>" data-role="ticket-send" />
         </div>
      </td>
   </tr>
   <?
   $obTabControl->EndCustomField('ASK_FORM');

//
   $obTabControl->BeginCustomField('CONTACTS', Loc::getMessage('ACRIT_BONUS_ASK_CONTACTS_TITLE'));
   ?>
   <tr class="heading"><td colspan="2"><?= $obTabControl->GetCustomLabelHTML() ?></td></tr>
   <tr>
      <td colspan="2">
         <fieldset title="<?= $obTabControl->GetCustomLabelHTML() ?>">
            <legend><?= $obTabControl->GetCustomLabelHTML() ?></legend>
            <?= Loc::getMessage('ACRIT_BONUS_ASK_CONTACTS_TEXT'); ?>
         </fieldset>
      </td>
   </tr>
   <?
   $obTabControl->EndCustomField('CONTACTS');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 4. Idea
   $obTabControl->BeginNextFormTab();
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
   $obTabControl->BeginCustomField('IDEA', Loc::getMessage('ACRIT_BONUS_IDEA'));
   $strUrl = 'https://www.acrit-studio.ru/services/idea/';
   ?>
   <tr class="heading"><td colspan="2"><?= $obTabControl->GetCustomLabelHTML() ?></td></tr>
   <tr>
      <td colspan="2">
         <div><?= Loc::getMessage('ACRIT_BONUS_IDEA_TEXT', ['#URL#' => $strUrl]); ?></div><br/>
      </td>
   </tr>
   <?
   $obTabControl->EndCustomField('IDEA');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   $obTabControl->Show();
   ?></div><?
   ?>
<div style="display:none">
   <form action="https://www.acrit-studio.ru/support/?show_wizard=Y" method="post" id="form-ticket" target="_blank">
      <input type="hidden" name="send_ticket" value="Y" />
      <input type="hidden" name="ticket_title" value="<?=
      Loc::getMessage('ACRIT_BONUS_ASK_FORM_SUBJECT', [
          '#SITE_NAME#' => preg_replace('#:(\d+)$#', '', \Bitrix\Main\Context::getCurrent()->getServer()->getHttpHost()),
      ]);
      ?>" />
      <input type="hidden" name="ticket_text" value="Y" />
      <input type="hidden" name="module_id" value="<?= $strModuleId; ?>" />
      <input type="hidden" name="module_version" value="<?= $arModuleVersion['VERSION']; ?>" />
      <input type="hidden" name="bitrix_version" value="<?= SM_VERSION . ' (' . SM_VERSION_DATE . ')'; ?>" />
      <input type="hidden" name="site_encoding" value="<?= SITE_CHARSET; ?>" />
      <input type="hidden" name="site_domain" value="<?= preg_replace('#:(\d+)$#', '', \Bitrix\Main\Context::getCurrent()->getServer()->getHttpHost()); ?>" />
   </form>
   <script>
      $('input[type=button][data-role="ticket-send"]').click(function (e) {
         e.preventDefault();
         var form = $('#form-ticket'),
                 divMessage = $('textarea[data-role="ticket-message"]'),
                 textMessage = $.trim(divMessage.val()),
                 errorMessage = divMessage.attr('data-error');
         if (!textMessage.length) {
            alert(errorMessage);
            return;
         }
         textMessage = [
            textMessage,
            '\n\n',
            '<?= Loc::getMessage('ACRIT_BONUS_ASK_MODULE_ID'); ?>: ' + $('input[name=module_id]', form).val(),
            '\n',
            '<?= Loc::getMessage('ACRIT_BONUS_ASK_MODULE_VERSION'); ?>: ' + $('input[name=module_version]', form).val(),
            '\n',
            '<?= Loc::getMessage('ACRIT_BONUS_ASK_BITRIX_VERSION'); ?>: ' + $('input[name=bitrix_version]', form).val(),
            '\n',
            '<?= Loc::getMessage('ACRIT_BONUS_ASK_SITE_ENCODING'); ?>: ' + $('input[name=site_encoding]', form).val(),
            '\n',
            '<?= Loc::getMessage('ACRIT_BONUS_ASK_SITE_DOMAIN'); ?>: ' + $('input[name=site_domain]', form).val(),
            '\n'
         ];
         $('input[name=ticket_text]', form).val(textMessage.join(''));
         form.submit();
      });
   </script>
</div>
<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
?>
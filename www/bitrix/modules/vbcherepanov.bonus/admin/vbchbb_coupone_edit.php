<?
if (isset($_REQUEST['work_start'])) {
    define("NO_AGENT_STATISTIC", true);
    define("NO_KEEP_STATISTIC", true);
}

/** @global CMain $APPLICATION */

use Bitrix\Main,
    Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    ITRound\Vbchbbonus;

$module_id = "vbcherepanov.bonus";

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $module_id . '/prolog.php');

$selfFolderUrl = $adminPage->getSelfFolderUrl();
$listUrl = $selfFolderUrl . "vbchbb_coupone.php?lang=" . LANGUAGE_ID;
$listUrl = $adminSidePanelHelper->editUrlToPublicPage($listUrl);

$saleModulePermissions = $APPLICATION->GetGroupRight($module_id);
$readOnly = ($saleModulePermissions < 'W');
if ($saleModulePermissions < 'R')
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

Loader::includeModule($module_id);
Loc::loadMessages(__FILE__);
$BBCORE = new Vbchbbonus\Vbchbbcore();

$couponMask = $BBCORE->GetOptions($BBCORE->SITE_ID, 'COUPONE_MASK');
$couponMask = $couponMask['OPTION'];
$iteratorbai = Vbchbbonus\CVbchBonusaccountsTable::getList(
    [
        'filter' => ['ACTIVE' => 'Y', 'LID' => $BBCORE->SITE_ID],
        'select' => ['ID', 'NAME']
    ]
);
unset($BBCORE);
$accountsID = [];
while ($bia = $iteratorbai->fetch()) {
    $accountsID[$bia['ID']] = '[' . $bia['ID'] . '] ' . $bia['NAME'];
}
unset($bia, $iteratorbai);
$couponTypes = Vbchbbonus\CouponTable::getCouponTypes();
$request = Main\Context::getCurrent()->getRequest();
$multiCoupons = false;
$prefix = '';

$tabList = array(
    array(
        'ICON' => 'sale',
        'DIV' => 'couponEdit01',
        'TAB' => Loc::getMessage('BONUS_COUPON_EDIT_TAB_NAME_COMMON'),
        'TITLE' => Loc::getMessage('BONUS_COUPON_EDIT_TAB_NAME_COMMON_DESC'),
    )
);
$couponFormID = '';

$couponFormID = 'bonusCouponControl';
$control = new CAdminForm($couponFormID, $tabList);
$control->SetShowSettings(false);

unset($tabList);
$couponFormID .= '_form';

$errors = array();
$fields = array();
$copy = false;
$couponID = (int)$request->get('ID');
if ($couponID < 0)
    $couponID = 0;

if ($couponID > 0)
    $copy = ($request->get('action') == 'copy');

if (
    check_bitrix_sessid()
    && !$readOnly
    && $request->isPost()
    && (string)$request->getPost('Update') == 'Y'
) {
    $adminSidePanelHelper->decodeUriComponent($request);
    $rawData = $request->getPostList();

    if ($rawData['MULTI'] == 'ONE' || $rawData['MULTI'] == '') {
        $fields = [
            'ACTIVE' => $rawData[$prefix . 'ACTIVE'],
            'ACTIVE_FROM' => $rawData[$prefix . 'ACTIVE_FROM'] ? Main\Type\DateTime::createFromUserTime($rawData[$prefix . 'ACTIVE_FROM']) : '',
            'ACTIVE_TO' => $rawData[$prefix . 'ACTIVE_TO'] ? Main\Type\DateTime::createFromUserTime($rawData[$prefix . 'ACTIVE_TO']) : '',
            'COUPON' => $rawData['COUPON'],
            'TYPE' => $rawData[$prefix . 'TYPE'],
            'USER_ID' => $rawData[$prefix . 'USER_ID'] ? $rawData[$prefix . 'USER_ID'] : 0,
            'DESCRIPTION' => $rawData[$prefix . 'DESCRIPTION'],
            'BONUS' => $rawData[$prefix . 'BONUS'],
            'BONUSLIVE' => $rawData[$prefix . 'BONUSLIVE'],
            'BONUSACTIVE' => $rawData[$prefix . 'BONUSACTIVE'],
            'BONUSACCOUNTSID' => $rawData[$prefix . 'BONUSACCOUNTSID'],
            'MAX_USE' => $rawData['MAX_USE'] ? $rawData['MAX_USE'] : 0,
        ];
        if (isset($fields['TYPE']) && $fields['TYPE'] == Vbchbbonus\CouponTable::TYPE_MULTI_ORDER) {
            if (isset($rawData[$prefix . 'MAX_USE']))
                $fields['MAX_USE'] = $rawData[$prefix . 'MAX_USE'];
        }
        if ($couponID == 0)
            $fields['USE_COUNT'] = 0;
        \Bitrix\Main\Diag\Debug::writeToFile(['ADD',$rawData,$fields],"","/couponeadd.txt");
        if ($couponID == 0 || $copy)
            $result = Vbchbbonus\CouponTable::add($fields);
        else
            $result = Vbchbbonus\CouponTable::update($couponID, $fields);
        if (!$result->isSuccess()) {
            $errors = $result->getErrorMessages();
        } else {
            if ($couponID == 0 || $copy)
                $couponID = $result->getId();


            if ((string)$request->getPost('apply') != '')
            {
                $applyUrl = $selfFolderUrl.'vbchbb_coupone_edit.php?lang='.LANGUAGE_ID.'&ID='.$couponID.'&'.$control->ActiveTabParam();
                LocalRedirect($applyUrl);
            }
            else
            {
                LocalRedirect($listUrl);
            }
        }


        unset($result);
    } elseif ($rawData['MULTI'] == 'MORE') {

    }
    unset($rawData);
}
if ($_REQUEST['work_start'] && check_bitrix_sessid()) {

    $settings = \Bitrix\Main\Config\Option::get($module_id, 'GENERATE_COUPON_DATA');
    $settings = unserialize(base64_decode($settings));
    \Bitrix\Main\Diag\Debug::writeToFile([$settings],"","/12ed2e.txt");
    $lastID = intval($_REQUEST["lastid"]);
    $table = "b_user";
    if (intval($settings['COUPON_COUNT'] != 0)) {
        Vbchbbonus\CouponTable::AddCoupon(['USER_ID' => false, 'DATA' => $settings]);
        $lastID++;
        $leftBorderCnt = $lastID;
        $allCnt = $settings['COUPON_COUNT'];

        $p = round(100 * $leftBorderCnt / $allCnt, 2);
        echo 'CurrentStatus = Array(' . $p . ',"' . ($p < 100 ? '&lastid=' . $lastID : '') . '","'.Loc::getMessage("BONUS_COUPON__GENERATE_CHECK_RECORD") . $lastID . '");';
        die();
    } else {
        $rs = $DB->Query("select ID from $table where ID>$lastID order by ID asc limit 100;");
        while ($ar = $rs->Fetch()) {
            Vbchbbonus\CouponTable::AddCoupon(['USER_ID' => $ar['ID'], 'DATA' => $settings]);
            $lastID = intval($ar["ID"]);
        }
        $rsLeftBorder = $DB->Query("select ID from $table where ID <= $lastID order by ID asc", false, "FILE: " . __FILE__ . "<br /> LINE: " . __LINE__);
        $leftBorderCnt = $rsLeftBorder->SelectedRowsCount();

        $rsAll = $DB->Query("select ID from $table;", false, "FILE: " . __FILE__ . "<br /> LINE: " . __LINE__);
        $allCnt = $rsAll->SelectedRowsCount();
        $p = round(100 * $leftBorderCnt / $allCnt, 2);
        echo 'CurrentStatus = Array(' . $p . ',"' . ($p < 100 ? '&lastid=' . $lastID : '') . '","'.Loc::getMessage("BONUS_COUPON__GENERATE_CHECK_RECORD") . $lastID . '");';
        die();
    }

}
$APPLICATION->SetTitle(
    $couponID == 0
        ? (
    !$multiCoupons
        ? Loc::getMessage('BONUS_COUPON_EDIT_TITLE_ADD')
        : Loc::getMessage('BONUS_COUPON_EDIT_TITLE_MULTI_ADD')
    )
        : (
    !$copy
        ? Loc::getMessage('BONUS_COUPON_EDIT_TITLE_UPDATE', array('#ID#' => $couponID))
        : Loc::getMessage('BONUS_COUPON_EDIT_TITLE_COPY', array('#ID#' => $couponID))
    )
);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

$contextMenuItems = array(
    array(
        'ICON' => 'btn_list',
        'TEXT' => Loc::getMessage('BONUS_COUPONT_CONTEXT_COUPON_LIST'),
        'LINK' => $listUrl
    )
);

if (!$readOnly && $couponID > 0) {
    if (!$copy) {
        $addUrl = $selfFolderUrl . "vbchbb_coupone_edit.php?lang=" . LANGUAGE_ID;
        $addUrl = $adminSidePanelHelper->editUrlToPublicPage($addUrl);
        if (!$adminSidePanelHelper->isPublicFrame())
            $addUrl = $adminSidePanelHelper->setDefaultQueryParams($addUrl);
        $contextMenuItems[] = array(
            'ICON' => 'btn_new',
            'TEXT' => Loc::getMessage('BONUS_COUPONT_CONTEXT_NEW'),
            'LINK' => $addUrl
        );
        $copyUrl = $selfFolderUrl . "vbchbb_coupone_edit.php?lang=" . LANGUAGE_ID . "&ID=" . $discountID . "&action=copy";
        $copyUrl = $adminSidePanelHelper->editUrlToPublicPage($copyUrl);
        if (!$adminSidePanelHelper->isPublicFrame())
            $copyUrl = $adminSidePanelHelper->setDefaultQueryParams($copyUrl);
        $contextMenuItems[] = array(
            'ICON' => 'btn_copy',
            'TEXT' => Loc::getMessage('BONUS_COUPONT_CONTEXT_COPY'),
            'LINK' => $copyUrl
        );
        $deleteUrl = $selfFolderUrl . "vbchbb_coupone.php?lang=" . LANGUAGE_ID . "&ID=" . $couponID . "&action=delete&" . bitrix_sessid_get();
        $buttonAction = "LINK";
        if ($adminSidePanelHelper->isPublicFrame()) {
            $deleteUrl = $adminSidePanelHelper->editUrlToPublicPage($deleteUrl);
            $buttonAction = "ONCLICK";
        }
        $contextMenuItems[] = array(
            'ICON' => 'btn_delete',
            'TEXT' => Loc::getMessage('BONUS_COUPON_CONTEXT_DELETE'),
            $buttonAction => "javascript:if(confirm('" . CUtil::JSEscape(Loc::getMessage('BONUS_COUPON_CONTEXT_DELETE_CONFIRM')) . "')) top.window.location.href='" . $deleteUrl . "';",
            'WARNING' => 'Y',
        );
    }
}
$contextMenu = new CAdminContextMenu($contextMenuItems);
$contextMenu->Show();
unset($contextMenu, $contextMenuItems);

if (!empty($errors)) {
    $errorMessage = new CAdminMessage(
        array(
            'DETAILS' => implode('<br>', $errors),
            'TYPE' => 'ERROR',
            'MESSAGE' => Loc::getMessage('BONUS_COUPON_ERR_SAVE'),
            'HTML' => true
        )
    );
    echo $errorMessage->Show();
    unset($errorMessage);
}

$selectFields = array();
if (!$multiCoupons) {
    $defaultValues = array(
        'COUPON' => '',
        'ACTIVE' => 'Y',
        'ACTIVE_FROM' => null,
        'ACTIVE_TO' => null,
        'TYPE' => Vbchbbonus\CouponTable::TYPE_ONE_ORDER,
        'MAX_USE' => 0,
        'USE_COUNT' => 0,
        'USER_ID' => 0,
        'BONUS' => 0,
        'BONUSACCOUNTSID' => 0,
        'DESCRIPTION' => '',
        'BONUSLIVE' => '',
        'BONUSACTIVE' => '',

    );
    $selectFields = array('ID');
    $selectFields = array_merge($selectFields, array_keys($defaultValues));
} else {
    $defaultValues = array(
        'COUNT' => '',
        'COUPON' => array(

            'ACTIVE_FROM' => null,
            'ACTIVE_TO' => null,
            'TYPE' => Vbchbbonus\CouponTable::TYPE_ONE_ORDER,
            'MAX_USE' => 0,
            'BONUSACCOUNTSID' => 0,
            'BONUS' => 0,
            'BONUSLIVE' => '',
            'BONUSACTIVE' => '',
        )
    );
}

$coupon = array();
if (!$multiCoupons && $couponID > 0) {
    $coupon = Vbchbbonus\CouponTable::getList(array(
        'select' => $selectFields,
        'filter' => array('=ID' => $couponID)
    ))->fetch();
    if (!$coupon)
        $couponID = 0;
}
if ($couponID == 0)
    $coupon = $defaultValues;

if (!$multiCoupons) {
    $coupon['TYPE'] = (int)$coupon['TYPE'];
    $coupon['USE_COUNT'] = (int)$coupon['USE_COUNT'];
    $coupon['MAX_USE'] = (int)$coupon['MAX_USE'];
    $coupon['USER_ID'] = (int)$coupon['USER_ID'];
    $coupon['DESCRIPTION'] = (string)$coupon['DESCRIPTION'];
} else {
    $coupon['COUNT'] = (int)$coupon['COUNT'];
    $coupon['COUPON']['TYPE'] = (int)$coupon['COUPON']['TYPE'];
    $coupon['COUPON']['MAX_USE'] = (int)$coupon['COUPON']['MAX_USE'];
}

if (!empty($errors))
    $coupon = array_merge($coupon, $fields);

$control->BeginPrologContent();
CJSCore::Init(array('date'));
$control->EndPrologContent();
$control->BeginEpilogContent();
echo GetFilterHiddens("filter_"); ?>
    <input type="hidden" name="Update" value="Y">
    <input type="hidden" name="lang" value="<? echo LANGUAGE_ID; ?>">
    <input type="hidden" name="ID" value="<? echo $couponID; ?>">
<?
if ($copy) {
    ?><input type="hidden" name="action" value="copy"><?
}
if (!empty($returnUrl)) {
    ?><input type="hidden" name="return_url" value="<? echo htmlspecialcharsbx($returnUrl); ?>"><?
}
echo bitrix_sessid_post();
$control->EndEpilogContent();
$couponMultiTypes = [
    'ONE' => Loc::getMessage('BONUS_COUPON_FIELD_MULTI_ONE'),
    'MORE' => Loc::getMessage('BONUS_COUPON_FIELD_MULTI_MORE'),
];
$formActionUrl = $selfFolderUrl . 'vbchbb_coupone_edit.php?lang=' . LANGUAGE_ID;
$formActionUrl = $adminSidePanelHelper->setDefaultQueryParams($formActionUrl);
$control->Begin(array('FORM_ACTION' => $formActionUrl));
$control->BeginNextFormTab();


if ($couponID == 0) {
    $control->AddDropDownField($prefix . 'MULTI', Loc::getMessage('BONUS_COUPON_FIELD_MULTI'), true, $couponMultiTypes, false, array('id="' . $prefix . 'MULTI' . '"'));
    $control->BeginCustomField('COUPON_COUNT', Loc::getMessage('BONUS_COUPON_FIELD_COUPON_COUNT'), false);

    ?>
    <tr id="tr_COUPON_COUNT" class="adm-detail-field">
    <td width="40%"><? echo $control->GetCustomLabelHTML(); ?></td>
    <td width="60%" id="td_COUPON_COUNT">
        <input type="text" id="COUPON_COUNT" name="COUPON_COUNT" size="32" maxlength="32" value=""/>&nbsp;
    </td>
    </tr><?

    $control->EndCustomField('COUPON_COUNT');
    $control->AddCheckBoxField($prefix . 'CHECKUSER', Loc::getMessage('BONUS_COUPON_FIELD_CHECKUSER'), false, array('Y', 'N'), false);
    $control->AddCheckBoxField($prefix . 'CHECKUSERACTIVE', Loc::getMessage('BONUS_COUPON_FIELD_CHECKUSERACTIVE'), false, array('Y', 'N'), false);

    $control->BeginCustomField('CHECKUSERGROUP', Loc::getMessage('BONUS_COUPON_FIELD_CHECKUSERGROUP'), false);
    $bl = new Vbchbbonus\UsergroupWidget();
    $bl->settings['NAME'] = 'CHECKUSERGROUP';
    $bl->settings['TITLE'] = Loc::getMessage('BONUS_COUPON_FIELD_CHECKUSERGROUP');
    $bl->settings['VALUE'] = '';
    $bl->settings['MULTIPLE'] = true;
    echo(str_replace("tr", "tr id='tr_CHECKUSERGROUP'", $bl->showBasicEditField()));
    unset($bl);
    $control->EndCustomField('CHECKUSERGROUP');

}
if ($couponID > 0 && !$copy)
    $control->AddViewField($prefix . 'ID', Loc::getMessage('BONUS_COUPON_FIELD_ID'), $couponID, false);
$control->AddCheckBoxField($prefix . 'ACTIVE', Loc::getMessage('BONUS_COUPON_FIELD_ACTIVE'), true, array('Y', 'N'), $coupon['ACTIVE'] == 'Y');
$control->BeginCustomField('COUPON_MASK', Loc::getMessage('BONUS_COUPON_FIELD_COUPONMASK'), true); ?>
    <tr id="tr_COUPON_MASK" class="adm-detail-required-field">
        <td width="40%"><? echo $control->GetCustomLabelHTML(); ?></td>
        <td width="60%" id="td_COUPON_MASK_VALUE">
            <input type="text" id="COUPON_MASK" name="COUPON_MASK" size="32" maxlength="32"
                   value="<? echo htmlspecialcharsbx($couponMask); ?>"/>&nbsp;
        </td>
    </tr>
    <tr>
        <td width="40%"></td>
        <td width="60%">
            <?= BeginNote("width='100%'") ?>
            <?= Loc::getMessage('BONUS_COUPON_FIELD_COUPONMASK_DESC') ?>
            <?= EndNote() ?>
        </td>
    </tr>
<?
$control->EndCustomField('COUPON_MASK',
    '<input type="hidden" name="COUPON_MASK" value="' . htmlspecialcharsbx($couponMask) . '">'
);

$control->BeginCustomField('COUPON', Loc::getMessage('BONUS_COUPON_FIELD_COUPON'), true);
?>
    <tr id="tr_COUPON" class="adm-detail-required-field">
        <td width="40%"><? echo $control->GetCustomLabelHTML(); ?></td>
        <td width="60%" id="td_COUPON_VALUE">
            <input type="text" id="COUPON" name="COUPON" size="32" maxlength="32"
                   value="<? echo htmlspecialcharsbx($coupon['COUPON']); ?>"/>&nbsp;
            <input type="button" value="<? echo Loc::getMessage('BONUS_COUPON_FIELD_COUPON_GENERATE'); ?>"
                   id="COUPON_GENERATE">
        </td>
    </tr><?
$control->EndCustomField('COUPON',
    '<input type="hidden" name="COUPON" value="' . htmlspecialcharsbx($coupon['COUPON']) . '">'
);
$showTypeSelect = (
    $couponID == 0
    || !isset($couponTypes[$coupon['TYPE']])
    || $coupon['DATE_APPLY'] == null
);
if ($showTypeSelect) {
    $control->AddDropDownField(
        $prefix . 'TYPE',
        Loc::getMessage('BONUS_COUPON_FIELD_TYPE'),
        true,
        $couponTypes,
        $coupon['TYPE'],
        array('id="' . $prefix . 'TYPE' . '"', 'size="3"')
    );
} else {
    $control->AddViewField(
        $prefix . 'TYPE',
        Loc::getMessage('BONUS_COUPON_FIELD_TYPE'),
        $couponTypes[$coupon['TYPE']],
        true
    );
}
if ($showTypeSelect || ($couponID > 0 && $coupon['TYPE'] == Vbchbbonus\CouponTable::TYPE_MULTI_ORDER))
    $control->AddEditField(
        $prefix . 'MAX_USE',
        Loc::getMessage('BONUS_COUPON_FIELD_MAX_USE'),
        false,
        array('id' => $prefix . 'MAX_USE'),
        ($coupon['MAX_USE'] > 0 ? $coupon['MAX_USE'] : '')
    );
$control->BeginCustomField($prefix . 'PERIOD', Loc::getMessage('BONUS_COUPON_FIELD_PERIOD'), false);
?>
    <tr id="tr_COUPON_PERIOD">
        <td width="40%"><? echo $control->GetCustomLabelHTML(); ?></td>
        <td width="60%"><?
            $periodValue = '';
            CTimeZone::Disable();
            $activeFrom = ($coupon['ACTIVE_FROM'] instanceof Main\Type\DateTime ? $coupon['ACTIVE_FROM']->toString() : '');
            $activeTo = ($coupon['ACTIVE_TO'] instanceof Main\Type\DateTime ? $coupon['ACTIVE_TO']->toString() : '');
            CTimeZone::Enable();
            if ($activeFrom != '' || $activeTo != '')
                $periodValue = CAdminCalendar::PERIOD_INTERVAL;

            $calendar = new CAdminCalendar;
            echo $calendar->CalendarPeriodCustom(
                $prefix . 'ACTIVE_FROM', $prefix . 'ACTIVE_TO',
                $activeFrom, $activeTo,
                true, 19, true,
                array(
                    CAdminCalendar::PERIOD_EMPTY => Loc::getMessage('BONUS_COUPON_PERIOD_EMPTY'),
                    CAdminCalendar::PERIOD_INTERVAL => Loc::getMessage('BONUS_COUPON_PERIOD_INTERVAL')
                ),
                $periodValue
            );
            unset($activeTo, $activeFrom, $periodValue);
            ?></td>
    </tr><?
$control->EndCustomField($prefix . 'PERIOD');
$control->BeginCustomField($prefix . 'USER_ID', Loc::getMessage('BONUS_COUPON_FIELD_USER_ID'), false);
?>
    <tr id="tr_USER_ID">
        <td width="40%"><? echo $control->GetCustomLabelHTML(); ?></td>
        <td width="60%"><?
            echo FindUserID(
                $prefix . 'USER_ID',
                ($coupon['USER_ID'] > 0 ? $coupon['USER_ID'] : ''),
                '',
                $couponFormID
            );
            ?></td>
    </tr><?
$control->EndCustomField($prefix . 'USER_ID');


$control->BeginCustomField('BONUS', Loc::getMessage('BONUS_COUPON_FIELD_BONUS'), true);
?>
    <tr id="tr_BONUS" class="adm-detail-required-field">
        <td width="40%"><? echo $control->GetCustomLabelHTML(); ?></td>
        <td width="60%" id="td_BONUS">
            <input type="text" id="BONUS" name="BONUS" size="32" maxlength="32"
                   value="<? echo htmlspecialcharsbx($coupon['BONUS']); ?>"/>&nbsp;
        </td>
    </tr><?
$control->EndCustomField('BONUS');

?>
<? $control->AddDropDownField(
    $prefix . 'BONUSACCOUNTSID',
    Loc::getMessage('BONUS_COUPON_FIELD_BONUSACCOUNTSID'),
    true,
    $accountsID,
    $coupon['BONUSACCOUNTSID'],
    array('id="' . $prefix . 'BONUSACCOUNTSID' . '"')
);
$control->BeginCustomField('BONUSLIVE', Loc::getMessage('BONUS_COUPON_FIELD_BONUSLIVE'), true);

$bl = new Vbchbbonus\TimelifeWidget();
$bl->settings['NAME'] = 'BONUSLIVE';
$bl->settings['TITLE'] = Loc::getMessage('BONUS_COUPON_FIELD_BONUSLIVE');
$bl->settings['VALUE'] = $coupon['BONUSLIVE'];
echo $bl->showBasicEditField();
unset($bl);
?>
<?
$control->EndCustomField('BONUSLIVE');
$control->BeginCustomField('BONUSACTIVE', Loc::getMessage('BONUS_COUPON_FIELD_BONUSACTIVE'), true);

$bl = new Vbchbbonus\DelayWidget();
$bl->settings['NAME'] = 'BONUSACTIVE';
$bl->settings['TITLE'] = Loc::getMessage('BONUS_COUPON_FIELD_BONUSACTIVE');
$bl->settings['VALUE'] = $coupon['BONUSACTIVE'];
echo $bl->showBasicEditField();
unset($bl);
?>
<?
$control->EndCustomField('BONUSACTIVE');


if ($couponID > 0 && $coupon['TYPE'] == Vbchbbonus\CouponTable::TYPE_MULTI_ORDER && $coupon['USE_COUNT'] > 0)
    $control->AddViewField(
        $prefix . 'USE_COUNT',
        Loc::getMessage('BONUS_COUPON_FIELD_USE_COUNT'),
        $coupon['USE_COUNT'],
        false
    );
$control->AddTextField(
    $prefix . 'DESCRIPTION',
    Loc::getMessage('BONUS_COUPON_FIELD_DESCRIPTION'),
    $coupon['DESCRIPTION'],
    array(),
    false
);
if($ID==0) {
    $control->BeginCustomField('COUPON_GENERATE', '', false);
    ?>
    <tr id="tr_COUPON_GENERATE" class="adm-detail-required-field">
    <td width="40%"></td>
    <td width="60%">
        <input type=button value="<? echo Loc::getMessage('BONUS_COUPON_FIELD_COUPON_GENERATE_ALL'); ?>"
               id="work_start" onclick="set_start(1);"/>
        <input type=button value="<? echo Loc::getMessage('BONUS_COUPON_FIELD_COUPON_GENERATE_ALL_STOP'); ?>"
               disabled id="work_stop" onclick="bSubmit=false;set_start(0)"/>
        <div id="progress" style="display:none;" width="100%">
            <br/>
            <div id="status"></div>
            <table border="0" cellspacing="0" cellpadding="2" width="100%">
                <tr>
                    <td height="10">
                        <div style="border:1px solid #B9CBDF">
                            <div id="indicator" style="height:10px; width:0%; background-color:#B9CBDF"></div>
                        </div>
                    </td>
                    <td width=30>&nbsp;<span id="percent">0%</span></td>
                </tr>
            </table>
        </div>
        <div id="result" style="padding-top:10px"></div>
    </td>
    </tr><?
    $clean_test_table = '<table id="result_table" cellpadding="0" cellspacing="0" border="0" width="100%" class="internal">' .
        '<tr class="heading">' .
        '<td>' . Loc::getMessage('BONUS_COUPON_GENERATE_CURRENT') . '</td>' .
        '<td width="1%">&nbsp;</td>' .
        '</tr>' .
        '</table>';

    $control->EndCustomField('COUPON_GENERATE');
}

$control->Buttons(array('disabled' => $readOnly, 'back_url' => $listUrl));

$control->Show();
?>
    <script type="text/javascript">
        var bWorkFinished = false;
        var bSubmit;

        function set_start(val) {
            if (val) {
            var form = BX('bonusCouponControl_form'),
                prepared = BX.ajax.prepareForm(form),
                i;
            for (i in prepared.data) {
                if (prepared.data.hasOwnProperty(i) && i == '') {
                    delete prepared.data[i];
                }
            }
            var data = {
                lang: BX.message('LANGUAGE_ID'),
                type: 'saveData',
                data: prepared.data,
                sessid: BX.bitrix_sessid()
            };
            BX.ajax.loadJSON(
                '/bitrix/tools/<?=$module_id?>/generate_coupon.php',
                data,
                function (data) {
                    document.getElementById('work_start').disabled = val ? 'disabled' : '';
                    document.getElementById('work_stop').disabled = val ? '' : 'disabled';
                    document.getElementById('progress').style.display = val ? 'block' : 'none';

                    ShowWaitWindow();
                    document.getElementById('result').innerHTML = '<?=$clean_test_table?>';
                    document.getElementById('status').innerHTML = 'Работаю...';

                    document.getElementById('percent').innerHTML = '0%';
                    document.getElementById('indicator').style.width = '0%';

                    CHttpRequest.Action = work_onload;
                    CHttpRequest.Send('<?= $_SERVER["PHP_SELF"]?>?work_start=Y&lang=<?=LANGUAGE_ID?>&<?=bitrix_sessid_get()?>');
                });

            } else
                CloseWaitWindow();
        }

        function work_onload(result) {
            try {
                eval(result);
                iPercent = CurrentStatus[0];
                strNextRequest = CurrentStatus[1];
                strCurrentAction = CurrentStatus[2];

                document.getElementById('percent').innerHTML = iPercent + '%';
                document.getElementById('indicator').style.width = iPercent + '%';

                document.getElementById('status').innerHTML = 'work...';

                if (strCurrentAction != 'null') {
                    oTable = document.getElementById('result_table');
                    oRow = oTable.insertRow(-1);
                    oCell = oRow.insertCell(-1);
                    oCell.innerHTML = strCurrentAction;
                    oCell = oRow.insertCell(-1);
                    oCell.innerHTML = '';
                }

                if (strNextRequest && document.getElementById('work_start').disabled)
                    CHttpRequest.Send('<?= $_SERVER["PHP_SELF"]?>?work_start=Y&lang=<?=LANGUAGE_ID?>&<?=bitrix_sessid_get()?>' + strNextRequest);
                else {
                    set_start(0);
                    bWorkFinished = true;
                }

            } catch (e) {
                CloseWaitWindow();
                document.getElementById('work_start').disabled = '';
                alert('error in data');
            }
        }

        BX.ready(function () {
            var obCouponValue = BX('COUPON'),
                obCouponBtn = BX('COUPON_GENERATE'),
                couponType = BX('<?=$prefix . 'TYPE'; ?>'),
                couponMulti = BX('MULTI'),
                multiGenerate = BX('work_start'),
                maxUse,
                rowMaxUse,
                useractive,
                usergroup,
                checkuser,
                couponemask = BX('COUPON_MASK');


            if (!!obCouponValue && !!obCouponBtn) {
                BX.bind(obCouponBtn, 'click', function () {
                    var url,
                        data;

                    BX.showWait();
                    url = '/bitrix/tools/<?=$module_id?>/generate_coupon.php';
                    data = {
                        lang: BX.message('LANGUAGE_ID'),
                        mask: couponemask.value,
                        sessid: BX.bitrix_sessid(),
                    };
                    BX.ajax.loadJSON(
                        url,
                        data,
                        function (data) {
                            var boolFlag = true,
                                strErr = '',
                                obCouponErr,
                                obCouponCell;
                            if (BX.type.isString(data)) {
                                boolFlag = false;
                                strErr = data;
                            } else {
                                if (data.STATUS != 'OK') {
                                    boolFlag = false;
                                    strErr = data.MESSAGE;
                                }
                            }
                            obCouponErr = BX('COUPON_GENERATE_ERR');
                            if (boolFlag) {
                                obCouponValue.value = data.COUPON;
                                if (!!obCouponErr)
                                    obCouponErr = BX.remove(obCouponErr);
                            } else {
                                if (!obCouponErr) {
                                    obCouponCell = BX('td_COUPON_VALUE');
                                    if (!!obCouponCell) {
                                        obCouponErr = obCouponCell.insertBefore(BX.create(
                                            'IMG',
                                            {
                                                props: {
                                                    id: 'COUPON_GENERATE_ERR',
                                                    src: '/bitrix/panel/main/images_old/icon_warn.gif'
                                                },
                                                style: {
                                                    marginRight: '10px',
                                                    verticalAlign: 'middle'
                                                }
                                            }
                                        ), obCouponBtn);
                                    }
                                }
                                BX.adjust(obCouponErr, {props: {title: strErr}});
                            }
                            BX.closeWait();
                        });
                });
            }
            if (!!couponMulti) {
                useractive = BX('tr_CHECKUSERACTIVE');
                usergroup = BX('tr_CHECKUSERGROUP');
                checkuser = BX('tr_CHECKUSER');
                var couponblock = BX('tr_COUPON'),
                    buttons = BX('bonusCouponControl_buttons_div'),
                    coupon_count = BX('tr_COUPON_COUNT'),
                    bnt_generate = BX('tr_COUPON_GENERATE');
                BX.style(useractive, 'display', 'none');
                BX.style(usergroup, 'display', 'none');
                BX.style(checkuser, 'display', 'none');
                BX.style(checkuser, 'display', 'none');
                BX.style(coupon_count, 'display', 'none');
                BX.style(bnt_generate, 'display', 'none');
                BX.bind(couponMulti, 'change', function () {
                    BX.style(
                        couponblock,
                        'display',
                        (couponMulti.value == 'ONE' ? 'table-row' : 'none')
                    );
                    BX.style(
                        buttons,
                        'display',
                        (couponMulti.value == 'ONE' ? 'table-row' : 'none')
                    );
                    BX.style(
                        bnt_generate,
                        'display',
                        (couponMulti.value == 'MORE' ? 'table-row' : 'none')
                    );

                    BX.style(
                        coupon_count,
                        'display',
                        (couponMulti.value == 'MORE' ? 'table-row' : 'none')
                    );
                    BX.style(
                        useractive,
                        'display',
                        (couponMulti.value == 'MORE' ? 'table-row' : 'none')
                    );
                    BX.style(
                        usergroup,
                        'display',
                        (couponMulti.value == 'MORE' ? 'table-row' : 'none')
                    );
                    BX.style(
                        checkuser,
                        'display',
                        (couponMulti.value == 'MORE' ? 'table-row' : 'none')
                    );
                    <?
                        if ($subWindow)
                        {
                        ?>if (top.BX.WindowManager.Get()) {
                        top.BX.WindowManager.Get().adjustSizeEx();
                    } else {
                        BX.WindowManager.Get().adjustSizeEx();
                    }
                    <?
                    }
                    ?>
                });
            }
            BX.bind(BX('COUPON_COUNT'), 'keyup', function () {
                BX.style(
                    useractive,
                    'display',
                    (BX('COUPON_COUNT').value == '' ? 'table-row' : 'none')
                );
                BX.style(
                    usergroup,
                    'display',
                    (BX('COUPON_COUNT').value == '' ? 'table-row' : 'none')
                );
                BX.style(
                    checkuser,
                    'display',
                    (BX('COUPON_COUNT').value == '' ? 'table-row' : 'none')
                );
                <?
                    if ($subWindow)
                    {
                    ?>if (top.BX.WindowManager.Get()) {
                    top.BX.WindowManager.Get().adjustSizeEx();
                } else {
                    BX.WindowManager.Get().adjustSizeEx();
                }
                <?
                }
                ?>
            });

            if (!!couponType) {
                maxUse = BX('<?=$prefix . 'MAX_USE'; ?>');
                rowMaxUse = BX.findParent(maxUse, {'tagName': 'tr'});

                BX.style(
                    rowMaxUse,
                    'display',
                    (couponType.value == '<?=Vbchbbonus\CouponTable::TYPE_MULTI_ORDER; ?>' ? 'table-row' : 'none')
                );
                BX.bind(couponType, 'change', function () {
                    BX.style(
                        rowMaxUse,
                        'display',
                        (couponType.value == '<?=Vbchbbonus\CouponTable::TYPE_MULTI_ORDER; ?>' ? 'table-row' : 'none')
                    );
                    <?
                        if ($subWindow)
                        {
                        ?>if (top.BX.WindowManager.Get()) {
                        top.BX.WindowManager.Get().adjustSizeEx();
                    } else {
                        BX.WindowManager.Get().adjustSizeEx();
                    }
                    <?
                    }
                    ?>
                });
            }
        });
        <?
            if ($subWindow)
            {
            ?>if (top.BX.WindowManager.Get()) {
            top.BX.WindowManager.Get().adjustSizeEx();
        } else {
            BX.WindowManager.Get().adjustSizeEx();
        }
        <?
        }
        ?></script><?

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
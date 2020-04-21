<?php
namespace ITRound\Vbchbbonus;
use \Bitrix\Main\Localization\Loc, \Bitrix\Main;
class BonusInnerWidget extends Vbchbbwidget{

    static protected $defaults = array(

    );
    protected function getEditHtml()
    {

        $html="";
        $arrBonusName=array();
        $ss=$this->getSettings("SITE");
        $ss=isset($ss) ? $this->getSettings("SITE") : null;
        try {
            $bips = CVbchBonusaccountsTable::getList(array(
                'filter' => array('ACTIVE' => "Y"),
            ))->fetchAll();
            $val = 0;
            if (sizeof($bips) > 0) {

                foreach ($bips as $ids => $ps) {
                    if ($ids == 0) $val = $ps['ID'];
                    $arrBonusName['REFERENCE'][] = $ps['NAME'];
                    $arrBonusName['REFERENCE_ID'][] = $ps['ID'];
                }
            }
            $value = $this->getValue();

            if (!$value) {
                $value = [];
                $value['BONUSINNER'] = $val;
            }
            $html .= SelectBoxFromArray($this->getEditInputName($ss) . "[BONUSINNER]", $arrBonusName, $value['BONUSINNER'], Loc::getMessage("VBCHBONUS_BONUS_INNER_NONE"));
        }catch(Main\ArgumentException $argumentException){
            echo 'ERROR:'.$argumentException->getMessage();

        }catch(Main\SystemException $systemException){
            echo 'ERROR:'.$systemException->getMessage();
        }
        return $html;
    }
}
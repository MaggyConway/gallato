<?php
namespace ITRound\Vbchbbonus;

use \Bitrix\Main;
class BonusAccountsWidget extends Vbchbbwidget{

    protected function getEditHtml()
    {
        $html='';
        $size = $this->getSettings('SIZE');
        $ss=$this->getSettings("SITE");
        $ss=isset($ss) ? $this->getSettings("SITE") : null;
        $value=$this->getValue();

	    $value=($value=='') ? $this->getDefault() : $value;
        try {
            $rs = CVbchBonusaccountsTable::getList(array(
                'filter' => array('ACTIVE' => 'Y'),
                'select' => array('ID', 'NAME'),
            ));
            while ($ba = $rs->fetch()) {
                $html .= $ba['NAME'] . ":&nbsp;";
                $html .= '<input type="text" name="' . $this->getEditInputName($ss) . '[' . $ba['ID'] . '][0]' . ' "value="' . trim($value[$ba['ID']][0]) . '" size="' . $size . '"
                       placeholder="' . $this->getSettings("PLACEHOLDER") . '" maxlenght="' . $this->getSettings("MAXLENGHT") . '"/> - ';
                $html .= '<input type="text" name="' . $this->getEditInputName($ss) . '[' . $ba['ID'] . '][1]' . ' " value="' . trim($value[$ba['ID']][1]) . '" size="' . $size . '"
                       placeholder="' . $this->getSettings("PLACEHOLDER") . '" maxlenght="' . $this->getSettings("MAXLENGHT") . '"/> <br/> ';
            }
        }catch(Main\ArgumentException $argumentException){
            echo 'ERROR:'.$argumentException->getMessage();

        }catch(Main\SystemException $systemException){
            echo 'ERROR:'.$systemException->getMessage();
        }
        return $html;
    }
}
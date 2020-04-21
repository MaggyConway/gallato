<?php
namespace ITRound\Vbchbbonus;

class TextboxWidget extends Vbchbbwidget{
    const LIST_TEXT_SIZE = 150;

    static protected $defaults = array(
        'COLS' => 65,
        'ROWS' => 5,
        'EDIT_IN_LIST' => false
    );


    protected function getEditHtml()
    {		
        $html='';
        $cols = $this->getSettings('COLS');
        $rows = $this->getSettings('ROWS');
        $html.='<textarea cols="' . $cols . '" rows="' . $rows . '" name="' . $this->getEditInputName() . '">'.$this->getValue()
        . '</textarea>';
        if($this->getSettings('HELP')){
            $html.=BeginNote('width="100%"');
            $html.=$this->getSettings('HELP');
            $html.=EndNote();
        }
        return $html;
    }
    public function View($NAME,$FIELD,$VALUE,$TD=false){
        ob_start();?>        <?if(!$TD){?>
            <tr class="adm-detail<?=($FIELD['REQUIRED'] ? '-required':'')?>-field">
            <td width="40%" class="adm-detail-content-cell-l" style="vertical-align:top;"><?=$FIELD['TITLE']?></td>
            <td width="60%" class="adm-detail-content-cell-r">
        <?}?>
        <textarea cols="<?=($FIELD['COLS'] ? $FIELD['COLS'] : '50')?>" rows="<?=($FIELD['ROWS'] ? $FIELD['ROWS'] : '10')?>" name="<?=$NAME?>">
            <?=trim($VALUE)?>
        </textarea>
        <?if($this->getSettings('HELP')){
            echo BeginNote('width="100%"');
            echo $this->getSettings('HELP');
            echo EndNote();
        }?>
        <?if(!$TD){?>
            </td>
            </tr>
        <?}?>
        <?$strResult = ob_get_contents();
        ob_end_clean();
        return $strResult;
    }
}
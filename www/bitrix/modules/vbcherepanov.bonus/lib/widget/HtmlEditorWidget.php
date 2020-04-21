<?php
namespace ITRound\Vbchbbonus;
class HtmleditorWidget extends Vbchbbwidget{
    const LIST_TEXT_SIZE = 150;

    static protected $defaults = array(
        'BODY_TYPE' => 'html',
        'HEIGHT'=>'',
        'WIDTH'=>'',
        'EDIT_IN_LIST' => false
    );


    protected function getEditHtml()
    {
        \Bitrix\Main\Loader::includeModule("fileman");
        $html='';
        $ss=$this->getSettings("SITE");
        $ss=isset($ss) ? $this->getSettings("SITE") : null;
        $val=$this->getValue();
        if(is_array($val) && array_key_exists("OPTION",$val)){
            $val=$val['OPTION'];
        }

        ob_start();
        \CFileMan::AddHTMLEditorFrame(
            $this->getEditInputName($ss),
           $val,
            "BODY_TYPE",
            $this->getSettings('BODY_TYPE'),
            array(
                'height' => $this->getSettings('HEIGHT'),
                'width' => $this->getSettings('WIDTH')
            ),
            "N",
            0,
            "",
            "onfocus=\"t=this\"",
            false,
            true,
            true,
            array()
        );
        $html = ob_get_clean();
        if($this->getSettings('HELP')){
            $html.=BeginNote('width="100%"');
            $html.=$this->getSettings('HELP');
            $html.=EndNote();
        }
        return $html;
    }
    public function View($NAME,$FIELD,$VALUE,$TD=false){
        ob_start();?>
        <?if(!$TD){?>
            <tr class="adm-detail<?=($FIELD['REQUIRED'] ? '-required':'')?>-field">
            <td width="40%" class="adm-detail-content-cell-l" style="vertical-align:top;"><?=$FIELD['TITLE']?></td>
            <td width="60%" class="adm-detail-content-cell-r">
        <?}

        \CFileMan::AddHTMLEditorFrame(
            $NAME,
            trim($VALUE),
            "BODY_TYPE",
            $FIELD['BODY_TYPE'],
            array(
                'height' => $FIELD['HEIGHT'],
                'width' => $FIELD['WIDTH']
            ),
            "N",
            0,
            "",
            "onfocus=\"t=this\"",
            false,
            true,
            true,
            array()
        );
        if($this->getSettings('HELP')){
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
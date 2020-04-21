<?php
namespace ITRound\Vbchbbonus;

abstract class Vbchbbwidget {

    public $settings = array();
    static protected $defaults;
    public function __construct()
    {
     
    }
    abstract protected function getEditHtml();

    public function showBasicHeader(){
        print '<tr class="heading">';
        $title = $this->getSettings('TITLE');
        if ($this->getSettings('REQUIRED') === true) {
            $title = '<b>' . $title . '</b>';
        }
        print '<td colspan="2">'.$title.'</td>';
        print '</tr>';
    }
    public function showBasicEditField($all=false)
    {
        if(!$all) {
            $name = $this->getSettings('NAME');
            print '<tr id="tr_'.$name.'">';
            $title = $this->getSettings('TITLE');
            if ($this->getSettings('REQUIRED') === true) {
                $title = '<b>' . $title . '</b>';
            }
            print '<td width="40%" style="vertical-align:top;">' . $title . ':</td>';
            $field = $this->getValue();
            if (is_null($field)) {
                $field = '';
            }
            $field = $this->getEditHtml();
            print '<td width="60%">' . $field . '</td>';
            print '</tr>';
        }else{
            return $this->getEditHtml();
        }
    }
    public function getSettings($name = '')
    {
        if (empty($name)) {
            return $this->settings;
        } else {
            if (isset($this->settings[$name])) {
                return $this->settings[$name];
            } else {
                if (isset(static::$defaults[$name])) {
                    return static::$defaults[$name];
                } else {
                    return false;
                }
            }
        }
    }
    public function getDefault(){
        return $this->getSettings('DEFAULT');
    }
    public function getValue()
    {
        $val=$this->getSettings('VALUE');
        $def=$this->getSettings('DEFAULT');
        $value=($val!="" ? $val : '');
        $value=($value!="" ? $value : $def);
        return $value;
    }
    protected function getMultipleEditHtml()
    {
        return "none support";
    }
    protected function getValueReadonly()
    {
        return static::prepareToOutput($this->getValue());
    }
    public static function prepareToOutput($string, $hideTags = true)
    {
        if ($hideTags) {
            return preg_replace('/<.+>/mU', '', $string);
        } else {
            return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        }
    }
    public static function prepareToTagAttr($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    public function setSetting($name, $value)
    {
        $this->settings[$name] = $value;
    }
    protected function getEditInputName($suffix = null)
    {
        $val=$this->getSettings('NAME');
        if($suffix){
            $val.="_".$suffix;
        }
        $val=($this->getSettings('MULTIPLE')) ? $val."[]" : $val;
        return $val;
    }
}





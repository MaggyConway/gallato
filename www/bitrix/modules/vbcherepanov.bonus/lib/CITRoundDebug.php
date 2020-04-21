<?php
namespace  ITRound\Funct\Debug;
use Bitrix\Main\Diag;

class ITRoundDebug{

    public $module_id='';

    public function setModule($module=null){
        if($module){
            $this->module_id=$module;
        }
    }
    public function WriteToFile($data, $filename=null){
        if($filename==null)
            $filename="1.log";
        $filename="./".$this->module_id."_".$filename;
        if(!is_array($data))
            $data=(array) $data;
        Diag\Debug::writeToFile($data,"",$filename);
    }

    public function AddLog($data,$function=null){
        if(!is_array($data))
            $data=(array) $data;

        \CEventLog::Add(array(
            'SEVERITY' => 'WARNING',
            'AUDIT_TYPE_ID' => strtoupper($this->module_id."_".$function),
            'MODULE_ID' => $this->module_id,
            'DESCRIPTION' => strval(implode(" <br/> ",$data))
        ));
    }

    public function PRE($data,$isAdmin=true,$return=false,$die=false){
        if(!is_array($data))
            $data=(array) $data;
        if($isAdmin){
            global $USER;
            if($USER->IsAdmin())
                echo '<pre>';print_R($data,$return);echo '</pre>';
        }else{
            echo '<pre>';print_R($data,$return);echo '</pre>';
        }
        if($die){
            die();
        }
    }
}
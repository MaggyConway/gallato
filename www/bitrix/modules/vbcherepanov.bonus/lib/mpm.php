<?php
class ITRoundModuleCheck{
    protected $checkPath="https://it-round.ru/mpm/check.php";
    protected $Result=array();
    protected $module_id='';
    public function __construct($module_id)
    {
        $this->module_id=$module_id;
    }
    public function CheckFreeware(){
        if(COption::GetOptionString($this->module_id,"modulecheck")!='Y'){
            require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/update_client_partner.php");
            $this->Result[1]=\Bitrix\Main\Loader::includeSharewareModule($this->module_id);
            $l=COption::GetOptionString("main", "mp_modules_date");
            foreach(unserialize($l) as $p){
                if($p['ID']==$this->module_id){
                    $this->Result[2]=ConvertTimeStamp($p['TMS'],"SHORT");break;
                }else{
                    $this->Result[2]='none';
                }
            }
            $this->Result[3]=md5("BITRIX".CUpdateClientPartner::GetLicenseKey()."LICENCE");
            $this->Result[4]=COption::GetOptionString("main", "email_from");
            $this->Result[5]=$this->module_id;
          //  $this->sendCheck();
        }
    }
    public function getStringResult(){
        return base64_encode(serialize($this->Result));
    }
    public function sendCheck(){
        $params = array(
            'data' => $this->getStringResult(),
        );
        $result = file_get_contents($this->checkPath, false, stream_context_create(array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($params)
            )
        )));
        $val='';
        if($result=='OK'){
            $val='Y';
        }elseif($result=='b'){
            $val='b';
        }else $val='N';
        COption::SetOptionString($this->module_id,"modulecheck",$val);
    }
}
<?
define('NO_AGENT_CHECK', true); // disable agents
define("STOP_STATISTICS", true); // disable statistic
use \ITRound\Vbchbbonus;
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php'); //include bitrix core
\Bitrix\Main\Loader::includeModule("vbcherepanov.bonus");
$query = ltrim($_POST["q"]);
$SITE_ID='';
if(isset($_POST['s']) && is_string($_POST['s'])){
	$SITE_ID=substr(preg_replace('/[^a-z0-9_]/i','',$_POST['s']),0,2);
}
if (!empty($query) && isset($_POST['ajax_call']) && $_POST['ajax_call'] == 'y')
{
	$bb=new Vbchbbonus\Vbchbbcore();
	$bb->SITE_ID=$SITE_ID;
	
	
	$option1=$bb->GetOptions($SITE_ID,'REFACTIVE');
	$option2=$bb->GetOptions($SITE_ID,'REFFIRST');
	if($option1['OPTION']=='Y'){
	    if(strpos($query,$option2['OPTION'])===false) $ID=true;else $ID=false;
		if(!empty($query) && strlen($query)>0){
            if(!$ID)
				$user=current(GetUserByRef($query,$bb->SITE_ID,array('ID','USERID','NAME'=>'USER_ID.NAME','LAST_NAME'=>'USER_ID.LAST_NAME','EMAIL'=>'USER_ID.EMAIL')));
            else
                $user=current(GetUserByID($query,$bb->SITE_ID,array('ID','USERID','NAME'=>'USER_ID.NAME','LAST_NAME'=>'USER_ID.LAST_NAME','EMAIL'=>'USER_ID.EMAIL')));

            if(!$user) $user=false;

		}else {$user=false;}

	}
	unset($bb);?>
	<?if($user){?>
		
			<span><?echo $user["NAME"]?>&nbsp;<?echo $user["LAST_NAME"]?></span><br/>
		<input type="hidden" name="REFERER" value="<?=$user['USERID']?>">
	<?}else{?>
        <span>Данный номер спонсора не существует</span><br/>
        <input type="hidden" name="REFERER" value="212024">
    <?}
			
	//echo CUtil::PhpToJSObject($user); //return results
	die();
}



   function GetUserByRef($ref,$site,$select=array()){
        $res=Vbchbbonus\CVbchRefTable::getList(array(
            'filter'=>array(
                'REFERER'=>$ref,
                'ACTIVE'=>'Y',
                'LID'=>$site,
            ),
            'select'=>$select,
        ))->fetchAll();
        return $res;
    }

    function GetUserByID($ref,$site,$select=array()){
        $res=Vbchbbonus\CVbchRefTable::getList(array(
            'filter'=>array(
                'ID'=>$ref,
                'ACTIVE'=>'Y',
                'LID'=>$site,
            ),
            'select'=>$select,
        ))->fetchAll();
        return $res;
    }
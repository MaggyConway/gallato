<?php
if(!CModule::IncludeModule('vbcherepanov.bonus'))
    ShowError(GetMessage("VBCHBB_COMP_NOT_MODULE"));

if ($this->StartResultCache())
{
	if(CModule::IncludeModule("currency"))
		$BaseCur=CCurrency::GetBaseCurrency();
	else
		$BaseCur="RUB";
	$suffix="_bx_site_".SITE_ID;
	$fields=array("","","NOTIFICATION","NOTIFICATIONSMS","LIVE","BITHDAY","DELAY","REG","OTHER","POROG","DISCOUNT","SOC_SERVICES","SUBSCRIBE","ORDER","REVIEW");
	if(CModule::IncludeModule('sale')){
		$res = CSalePaySystem::GetList(array("NAME"=>"ASC"));
		while ($r = $res->Fetch()) {
			$PAY[$r['ID']] = $r['NAME'];
		}
		$res = CSaleDelivery::GetList(array("NAME" => "ASC"));
		while ($r = $res->Fetch()) {
			$DELIVERY[$r['ID']] = $r['NAME'];
		}
	}
	$vbchbb["FULL"] = $full=unserialize(COption::GetOptionString("vbcherepanov.bonus", "full",SITE_ID));
	foreach($fields as $fld){
		$vbchbb[$fld]=CVBCHBB::GetOptions(SITE_ID,$fld);
	}
	//for registration
	$arResult["REGISTR"]=$vbchbb["REG"]["OPTION"]["ACTIVE"]=="Y" ? CVBCHBB::declOfNum(SITE_ID,$vbchbb["REG"]["OPTION"]["COUNT"]) : false;
	//for subscibe
	$arResult["SUBSCRIBE"]=$vbchbb["SUBSCRIBE"]["OPTION"]["ACTIVE"]=="Y" ? CVBCHBB::declOfNum(SITE_ID,$vbchbb["SUBSCRIBE"]["OPTION"]["COUNT"]) : false;
	//for birthday
	$arResult["BIRTHDAY"]=$vbchbb["BITHDAY"]["OPTION"]["ACTIVE"]=="Y" ? CVBCHBB::declOfNum(SITE_ID,$vbchbb["BITHDAY"]["OPTION"]["COUNT"]) : false;
	//bonus live
	$arResult["LIVE"]="(".GetMessage("VBCHBONUS_COM_LIVE_".$vbchbb["LIVE"]["OPTION"]["CH"]).")".$vbchbb["LIVE"]["OPTION"]["COUNT"];
	//for notification
	$arResult["SENDEMAIL"]=($vbchbb["NOTIFICATION"]["OPTION"]=="Y") ? true : false;
	$arResult["SENDSMS"]=($vbchbb["NOTIFICATIONSMS"]["OPTION"]=="Y") ? true : false;
	//final price (minus bonus?)
	$arResult["ORDER"]["SUMM_TOTAL"]=$vbchbb["ORDER"]["OPTION"]["SUMM_TOTAL"]=="Y" ? true : false;
	//first order
	$arResult["ORDER"]["FIRST"]=$vbchbb["ORDER"]["OPTION"]["FIRST"]["ACTIVE"]=="Y" ?
	(!strpos($vbchbb["ORDER"]["OPTION"]["FIRST"]["COUNT"],"%") ? CVBCHBB::declOfNum(SITE_ID,$vbchbb["ORDER"]["OPTION"]["FIRST"]["COUNT"]) : $vbchbb["ORDER"]["OPTION"]["FIRST"]["COUNT"]) : false;
	//order pay bonus
	$arResult["PAYPART"]=$vbchbb["ORDER"]["OPTION"]["PAY"]["ACTIVE"]=="Y" ? (!strpos($vbchbb["ORDER"]["OPTION"]["PAY"]["COUNT"],"%") ? CVBCHBB::declOfNum(SITE_ID,$vbchbb["ORDER"]["OPTION"]["PAY"]["COUNT"]) : $vbchbb["ORDER"]["OPTION"]["PAY"]["COUNT"] ): false;
	//order bonus without filters
	$arResult["ORDER"]["BONUS"]=($vbchbb["ORDER"]["OPTION"]["ORDER"]["COUNT"]) ? (!strpos($vbchbb["ORDER"]["OPTION"]["ORDER"]["COUNT"],"%") ? CVBCHBB::declOfNum(SITE_ID,$vbchbb["ORDER"]["OPTION"]["ORDER"]["COUNT"]) : $vbchbb["ORDER"]["OPTION"]["ORDER"]["COUNT"]) :false;
	//order bonus with filters
	if(is_array($vbchbb["FULL"]["ORDER".$suffix]) && sizeof($vbchbb["FULL"]["ORDER".$suffix])>0){
		$arResult["ORDER"]["ACTIVE"]=true;
		foreach($vbchbb["FULL"]["ORDER".$suffix] as $ido=>$ord){
			$persent=(strpos($ord["ORDER_BONUS"],"%"));
			$tmp[]=array("FROM"=>CurrencyFormat($ord["ORDER_OT"],$BaseCur),"TO"=>CurrencyFormat($ord["ORDER_DO"],$BaseCur),"PAY"=>$PAY[$ord["PAY"]],"DELIVARY"=>$DELIVERY[$ord["DELIVERY"]],"COUNT"=>(!$persent) ? CVBCHBB::declOfNum(SITE_ID,$ord["ORDER_BONUS"]) : $ord["ORDER_BONUS"]);
		}
		$arResult["ORDER"]["TABLE_BONUS"]=$tmp;unset($tmp);
	}else $arResult["ORDER"]["ACTIVE"]=false;
	//minus discount offers
	$arResult["ORDER"]["DISCOUNT_OFF"]=$vbchbb["OTHER"]["OPTION"]["DISCOUNT"]=="Y" ? true : false;
	//bonus without offers
	if(is_array($vbchbb["OTHER"]["OPTION"]["OFFER"]) && sizeof($vbchbb["OTHER"]["OPTION"]["OFFER"])>0){
		$arSelect = Array("ID", "NAME");
		$arFilter = Array("ACTIVE"=>"Y","ID"=>$vbchbb["OTHER"]["OPTION"]["OFFER"]);
		$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
		while($ob = $res->GetNextElement())
		{
			$arFields = $ob->GetFields();
			$tmp[]=$arFields["NAME"];
		}
		$arResult["ORDER"]["OFFER_OFF"]=$tmp;unset($tmp);
	}else $arResult["ORDER"]["OFFER_OFF"]=false;
	if($vbchbb["ORDER"]["OPTION"]["DELAY"]["ACTIVE"]=="Y"){
		$arResult["DELAY"]=$vbchbb["ORDER"]["OPTION"]["DELAY"]["COUNT"]." ".GetMessage("VBCH_BONUS_TIME_".$vbchbb["ORDER"]["OPTION"]["DELAY"]["TIME"]);
	}else $arResult["DELAY"]=false;
	if($vbchbb["ORDER"]["OPTION"]["SUMMAS"]["ACTIVE"]=="Y"){
		$arResult["SUMMAS"]=CurrencyFormat($vbchbb["ORDER"]["OPTION"]["SUMMAS"]["COUNT"],$BaseCur).GetMessage('VBCHBONUS_COMP_ZA').GetMessage("VBCH_BONUS_TIME_".$vbchbb["ORDER"]["OPTION"]["SUMMAS"]["TIME"]);
	}else $arResult["SUMMAS"]=false;
	//porog
	if(is_array($vbchbb["FULL"]["POROG".$suffix]) && sizeof($vbchbb["FULL"]["POROG".$suffix])>0){
		$arResult["POROG"]["ACTIVE"]=true;$tmp=array();
		foreach($vbchbb["FULL"]["POROG".$suffix] as $idr=>$rew){
			$tmp[]="Сумма заказов за ".GetMessage("VBCH_BONUS_TIME_".$rew["PERIOD"]).":".CurrencyFormat($rew["SUMMA"],$BaseCur).GetMessage('VBCHBONUS_COMP_BONUS').
			(!strpos($rew["BONUS_L"],"%") ? CVBCHBB::declOfNum(SITE_ID,$rew["BONUS_L"]) : $rew["BONUS_L"]);
		}
		$arResult["POROG"]["TABLE"]=$tmp;unset($tmp);
	}else $arResult["POROG"]["ACTIVE"]=false;
	//bonus for reviews
	if(is_array($vbchbb["FULL"]["REVIEW".$suffix]) && sizeof($vbchbb["FULL"]["REVIEW".$suffix])>0){
		$arResult["REVIEW"]["ACTIVE"]=true;
		foreach($vbchbb["FULL"]["REVIEW".$suffix] as $idr=>$rew){
			if($rew["REVIEWTYPE"]=="IB"){
				$IB[$rew["REVIEWIB"]]=$rew["REVIEW_BONUS"];
				$iblock[]=$rew["REVIEWIB"];
			}elseif($rew["REVIEWTYPE"]=="BLOG"){
				$BG[$rew["REVIEWBLOG"]]=$rew["REVIEW_BONUS"];
				$blog[]=$rew["REVIEWBLOG"];
			}elseif($rew["REVIEWTYPE"]=="FORUM"){
				$FM[$rew["REVIEWFORUM"]]=$rew["REVIEW_BONUS"];
				$forum[]=$rew["REVIEWFORUM"];
			}
		}
		if(sizeof($iblock)>0 && CModule::IncludeModule("iblock")) {
			$res = CIBlock::GetList(Array(), Array('SITE_ID'=>SITE_ID, 'ACTIVE'=>'Y', "ID"=>$iblock), false);
			while($ar_res = $res->Fetch())
				$tmp[]=array("NAME"=>$ar_res["NAME"],"COUNT"=>CVBCHBB::declOfNum(SITE_ID,$IB[$ar_res['ID']]));
		}
		if(sizeof($blog)>0 &&CModule::IncludeModule("blog")){
			$SORT = Array("NAME" => "ASC");
			$arFilter = Array("ACTIVE" => "Y","GROUP_SITE_ID" => SITE_ID,"ID"=>$blog);	
			$arSelectedFields = array("ID", "NAME");
			$dbBlogs = CBlog::GetList($SORT,$arFilter,false,false,$arSelectedFields);
			while ($ar_Blog = $dbBlogs->Fetch())
			{
				$tmp[]=array("NAME"=>$ar_Blog["NAME"],"COUNT"=>CVBCHBB::declOfNum(SITE_ID,$BG[$ar_Blog["ID"]]));
			}
		}
		if(sizeof($forum)>0 && CModule::IncludeModule("forum")){
			$arFilter["ACTIVE"] = "Y";
			$arFilter["ID"]=$forum;
			$arOrder = array("SORT"=>"ASC", "NAME"=>"ASC");
			$db_Forum = CForumNew::GetList($arOrder, $arFilter);
			while ($ar_Forum = $db_Forum->Fetch())
			{
				$tmp[]=array("NAME"=>$ar_Forum["NAME"],"COUNT"=>CVBCHBB::declOfNum(SITE_ID,$FM[$ar_Forum["ID"]]));
			}
		}
		$arResult["REVIEW"]["TABLE_BONUS"]=$tmp;unset($tmp);
	}else $arResult["REVIEW"]["ACTIVE"]=false;
	$vbchbb["ORDER"]["OPTION"]["USERGROUP"]=array_filter($vbchbb["ORDER"]["OPTION"]["USERGROUP"]);
	if(is_array($vbchbb["ORDER"]["OPTION"]["USERGROUP"]) && sizeof($vbchbb["ORDER"]["OPTION"]["USERGROUP"])>0){
		$rsGroups = CGroup::GetList($by, $order, array("ID"=>implode(" | ",$vbchbb["ORDER"]["OPTION"]["USERGROUP"]))); 
		while($rf=$rsGroups->Fetch()):
			$arResult["USERGROUP"][]=$rf["NAME"];
		endwhile;
	}else $arResult["USERGROUP"]=false;
	if(is_array($vbchbb["ORDER"]["OPTION"]["SECTION"]) && sizeof($vbchbb["ORDER"]["OPTION"]["SECTION"])>0){
		if(CModule::IncludeModule("iblock")): 
			$arres = CIBlockSection::GetList(Array("ID"=>"ASC"), Array("ID" => $vbchbb["ORDER"]["OPTION"]["SECTION"]), false, array("ID","NAME"));
			if($value = $arres->GetNext()):
			    $arResult["SECTION"][]=$value["NAME"];
			endif;
		endif;
	}else $arResult["SECTION"]=false;
	if(is_array($vbchbb["ORDER"]["OPTION"]["SALEPERSON"]) && sizeof($vbchbb["ORDER"]["OPTION"]["SALEPERSON"])>0){
		if(CModule::IncludeModule("sale")){
			$db_ptype = CSalePersonType::GetList(Array("SORT" => "ASC"), Array("ID"=>$vbchbb["ORDER"]["OPTION"]["SALEPERSON"]));
			while ($ptype = $db_ptype->Fetch()){
				$arResult["SALEPERSON"][]=$ptype["NAME"];
			}
		}
	}else $arResult["SALEPERSON"]=false;
	$this->IncludeComponentTemplate();
}
?>
<?php

namespace ITRound\Vbchbbonus;

use \Bitrix\Main\Localization\Loc, ITRound\Vbchbbonus;

Loc::loadMessages(__FILE__);

class Vbchbbprofiles
{
	private $Code = "",
		$Name = "",
		$Desc = "",
		$VisualParams = array();

	public function __construct()
	{

	}

	public function setCode($Code = "")
	{
		$this->Code = $Code;
	}

	public function getCode()
	{
		return $this->Code;
	}

	public function setName($Name = "")
	{
		$this->Name = $Name;
	}

	public function getName()
	{
		return $this->Name;
	}

	public function setDesc($Desc = "")
	{
		$this->Desc = $Desc;
	}

	public function getDesc()
	{
		return $this->Desc;
	}

	public function setVisualParams($Params = array())
	{
		$this->VisualParams = $Params;
	}

	public function getVisualParams()
	{
		return $this->VisualParams;
	}

	public function getID()
	{
		return array(
			'ID' => $this->Code,
			'NAME' => $this->Name,
		);
	}

	public function GetRules($func, $ID, $Filter = array(), $arFields = array())
	{
		if (is_callable($func)) {
			$return = call_user_func_array($func, array($ID, $Filter, $arFields));
		}
		if ($return) {
			Vbchbbonus\CvbchbonusprofilesTable::ProfileIncrement($ID);
		}
		return $return;
	}

	public function GetBonus($func, $profile = array(), $arFields = array())
	{
		if (is_callable($func)) {
			return call_user_func_array($func, array($profile, $arFields));
		}
		return null;
	}

	public function GetMenuAdd()
	{
		return
			array(
				"TEXT" => $this->getName(),
				"TITLE" => $this->getDesc(),
				"LINK" => "vbchbb_profiles_edit.php?ID=0&lang=ru&PROFILES_TYPE=" . $this->getCode(),
				"SHOW_TITLE" => 1,
			);
	}

	public function GetParameters($ID)
	{
		if (isset($ID) && $ID > 0) {
			$val = Vbchbbonus\CvbchbonusprofilesTable::getList(array(
				'filter' => array("ID" => $ID),
			))->fetch();
		} else {
			$val = array(
				"ACTIVE" => "Y",
				"TYPE" => $this->getCode(),
			);
		}
		$val = Vbchbbcore::GetProfileParams($val);

		Vbchbbonus\ProfileRender::Run($this->GetVisualParams(), $val);
	}
}
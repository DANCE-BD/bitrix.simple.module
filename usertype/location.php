<?
namespace SimpleModule\Usertype;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class StatLocation extends AutoComplete
{
	function GetUserTypeDescription()
	{
		return array(
			"USER_TYPE_ID" => "sm_statlocation",
			"CLASS_NAME" => "SimpleModule\\Usertype\\StatLocation",
			"DESCRIPTION" => GetMessage("SM_USER_TYPE_LOCATION_DESCRIPTION"),
			"BASE_TYPE" => "int",
		);
	}

	public static function GetList($arSort=array(), $arFilter=array(), $arNavParams=false, $arUserField)
	{
		$rs = false;

		if(\CModule::IncludeModule("statistic"))
		{
			$arAliases = array("ID" => "CITY_ID", "NAME" => "CITY_NAME");

			$ob = new \CCity();
			$rs = new CUserTypeLocationEnum($ob->GetList(
				static::__makeArrayFromAlias($arSort, $arAliases),
				static::__makeArrayFromAlias($arFilter, $arAliases)
			));

			// getlist is not supported pagenavigation yet
			if(is_array($arNavParams) && is_set($arNavParams, "nTopCount"))
			{
				$arr = array();
				while($ar = $rs->Fetch())
				{
					if(count($arr) >= intval($arNavParams["nTopCount"]))
						break;
					$arr[] = $ar;
				}
				$rs = new CUserTypeLocationEnum();
				$rs->InitFromArray($arr);
			}
		}

		return $rs;
	}
}

class CUserTypeLocationEnum extends \CDBResult
{
	function Fetch()
	{
		$r = parent::Fetch();
		if($r)
		{
			$r["ID"] = $r["CITY_ID"];
			$r["NAME"] = $r["CITY_NAME"];
		}

		return $r;
	}
}
?>

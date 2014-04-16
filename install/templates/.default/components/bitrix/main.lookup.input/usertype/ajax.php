<?
define("STOP_STATISTICS", true);
define("BX_SECURITY_SHOW_MESSAGE", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
__IncludeLang(dirname(__FILE__)."/lang/".LANGUAGE_ID."/".basename(__FILE__));

global $APPLICATION, $USER_FIELD_MANAGER;

CUtil::JSPostUnescape();

$arUserField = CUserTypeEntity::GetByID(intval($_REQUEST["PID"]));
if(!$arUserField)
{
	echo "incorrect usertype";
	die();
}
$arUserType = $USER_FIELD_MANAGER->GetUserType($arUserField["USER_TYPE_ID"]);

$strBanSym = trim($arUserField["SETTINGS"]["BAN_SYM"]);
$arBanSym = str_split($strBanSym, 1);
$strRepSym = trim($arUserField["SETTINGS"]["REP_SYM"]);

$arRepSym = array_fill(0, sizeof($arBanSym), $strRepSym);

if($_REQUEST["MODE"] == "SEARCH")
{
	$APPLICATION->RestartBuffer();

	$arResult = array();
	$search = trim($_REQUEST["search"]);

	$matches = array();
	if(preg_match("/^(.*?)\[([\d]+?)\]/i", $search, $matches))
	{
		$matches[2] = intval($matches[2]);
		if($matches[2] > 0)
		{
			$arRes = $arUserType["CLASS_NAME"]::GetElement($matches[2], $arUserField);
			if($arRes)
			{
				$arResult[] = array(
					"ID" => $arRes["ID"],
					"NAME" => str_replace($arBanSym, $arRepSym, $arRes["NAME"]),
					"READY" => "Y",
				);

				Header("Content-Type: application/x-javascript; charset=".LANG_CHARSET);
				echo CUtil::PhpToJsObject($arResult);
				die();
			}
		}
		elseif(strlen($matches[1]) > 0)
		{
			$search = $matches[1];
		}
	}

	$dbRes = $arUserType["CLASS_NAME"]::GetList(
		array(),
		array(
			"%NAME" => $search,
		),
		array("nTopCount" => 20),
		$arUserField
	);

	while($arRes = $dbRes->Fetch())
	{
		$arResult[] = array(
			"ID" => $arRes["ID"],
			"NAME" => str_replace($arBanSym,$arRepSym,$arRes["NAME"]),
		);
	}

	Header("Content-Type: application/x-javascript; charset=".LANG_CHARSET);
	echo CUtil::PhpToJsObject($arResult);
	die();
}
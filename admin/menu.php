<?
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

$RIGHTS = $APPLICATION->GetGroupRight("xdev.parser");
if($RIGHTS > "D")
{
	$arItems = array();
	if($RIGHTS >= "W")
		$arItems[] = array(
			"text" => GetMessage("SM_MENU_ENTITY_LIST"),
			"url" => "xdev_parser_settings_list.php?lang=".LANGUAGE_ID,
			"title" => GetMessage("SM_MENU_ENTITY_LIST_ALT"),
			"more_url" => array("xdev_parser_settings_edit.php")
		);
	$aMenu = array(
		"parent_menu" => "global_menu_services",
		"sort" => 10000,
		"text" => GetMessage("SM_MENU"),
		"title"=> GetMessage("SM_MENU_ALT"),
		"icon" => "sale_menu_icon_orders",
		"items_id" => "menu_xdev.parser",
		"items" => $arItems
	);
	return $aMenu;
}
return false;
?>
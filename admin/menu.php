<?
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

$RIGHTS = $APPLICATION->GetGroupRight("xdev.mdstore");
if($RIGHTS > "D")
{
	$arItems = array();
	if($RIGHTS >= "W")
		$arItems[] = array(
			"text" => GetMessage("MD_STORE_MENU_ENTITY_LIST"),
			"url" => "xdev_mdstore_domain_admin.php?lang=".LANGUAGE_ID,
			"title" => GetMessage("MD_STORE_MENU_ENTITY_LIST_ALT"),
			"more_url" => array("xdev_mdstore_domain_edit.php")
		);
	$aMenu = array(
		"parent_menu" => "global_menu_services",
		"sort" => 10000,
		"text" => GetMessage("MD_STORE_MENU"),
		"title"=> GetMessage("MD_STORE_MENU_ALT"),
		"icon" => "sale_menu_icon_orders",
		"items_id" => "menu_xdev.mdstore",
		"items" => $arItems
	);
	return $aMenu;
}
return false;
?>
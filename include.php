<?
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
CModule::AddAutoloadClasses(
	"xdev.mdstore", array(
		"XPriceDomain\\UtmEntityTable" => "lib/utm.php",
		"XPriceDomain\\UtsEntityTable" => "lib/uts.php",
		"XPriceDomain\\DataManager" => "lib/datamanager.php",
		"XPriceDomain\\EntityTable" => "lib/entity.php"
	)
);
?>
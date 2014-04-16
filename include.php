<?
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
CModule::AddAutoloadClasses(
	"simple.module", array(
		"SimpleModule\\UtmEntityTable" => "lib/utm.php",
		"SimpleModule\\UtsEntityTable" => "lib/uts.php",
		"SimpleModule\\DataManager" => "lib/datamanager.php",
		"SimpleModule\\EntityTable" => "lib/entity.php",
		"SimpleModule\\CUserTypeAutoComplete" => "usertype/usertype.php",
		"SimpleModule\\CUserTypeLocation" => "usertype/location.php"
	)
);
?>

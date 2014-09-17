<?
namespace xdev\parser\Entity;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class SettingsTable extends DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getUfId()
	{
		return "XDEV_PARSER";
	}

	public static function getTableName()
	{
		return "xdev_parser_settings";
	}

	public static function getMap()
	{
		return array(
			"ID" => array(
				"data_type" => "integer",
				"primary" => true,
				"autocomplete" => true,
				"title" => Loc::getMessage("SM_ENTITY_ID_FIELD"),
			),
			"TIMESTAMP_X" => array(
				"data_type" => "datetime",
				"title" => Loc::getMessage("SM_ENTITY_TIMESTAMP_X_FIELD"),
			),
			"ACTIVE" => array(
				"data_type" => "boolean",
				"required" => true,
				"values" => array("N","Y"),
				"title" => Loc::getMessage("SM_ENTITY_ACTIVE_FIELD"),
			),
			"SORT" => array(
				"data_type" => "integer",
				"title" => Loc::getMessage("SM_ENTITY_SORT_FIELD"),
			),
		);
	}
}
?>

<?
namespace XPriceDomain;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class EntityTable extends DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getUfId()
	{
		return "X_MDSTORE_ENTITY";
	}

	public static function getTableName()
	{
		return "xdev_mdstore_domain_entity";
	}

	public static function getMap()
	{
		return array(
			"ID" => array(
				"data_type" => "integer",
				"primary" => true,
				"autocomplete" => true,
				"title" => Loc::getMessage("MD_STORE_ENTITY_ID_FIELD"),
			),
			"TIMESTAMP_X" => array(
				"data_type" => "datetime",
				"title" => Loc::getMessage("MD_STORE_ENTITY_TIMESTAMP_X_FIELD"),
			),
			"ACTIVE" => array(
				"data_type" => "boolean",
				"required" => true,
				"values" => array("N","Y"),
				"title" => Loc::getMessage("MD_STORE_ENTITY_ACTIVE_FIELD"),
			),
			"SORT" => array(
				"data_type" => "integer",
				"title" => Loc::getMessage("MD_STORE_ENTITY_SORT_FIELD"),
			),
			"LID" => array(
				"data_type" => "string",
				"required" => true,
				"title" => Loc::getMessage("MD_STORE_ENTITY_LID_FIELD"),
			),
			"SITE" => array(
				"data_type" => "Bitrix\Main\Site",
				"reference" => array("=this.LID" => "ref.LID"),
			),
			"UTM_OBJECT" => array(
				"data_type" => "UtmEntity",
				"reference" => array("=this.ID" => "ref.VALUE_ID"),
			),
			"UTS_OBJECT" => array(
				"data_type" => "UtsEntity",
				"reference" => array("=this.ID" => "ref.VALUE_ID"),
			),
		);
	}
}
?>
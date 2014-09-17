<?
namespace SimpleModule;

use Bitrix\Main\Entity\DataManager;

class UtmEntityTable extends DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getUfId()
	{
		return EntityTable::getUfId();
	}

	public static function getTableName()
	{
		return "xdev_parser_utm_domain_entity";
	}

	public static function isUtm()
	{
		return true;
	}

	public static function getMap()
	{
		// get ufields
		$fieldsMap = $GLOBALS["USER_FIELD_MANAGER"]->getUserFields(static::getUfId());

		foreach($fieldsMap as $k => $v)
		{
			if($v["MULTIPLE"] == "N")
				unset($fieldsMap[$k]);
		}

		return array_merge(array(
			"ID" => array(
				"data_type" => "integer",
				"primary" => true
			),
			"VALUE_ID" => array(
				"data_type" => "integer"
			),
			"SOURCE_OBJECT" => array(
				"data_type" => "SimpleModule\Entity",
				"reference" => array("=this.VALUE_ID" => "ref.ID")
			),
			"FIELD_ID" => array(
				"data_type" => "integer"
			),
			"VALUE" => array(
				"data_type" => "string"
			),
			"VALUE_INT" => array(
				"data_type" => "integer"
			),
			"VALUE_DOUBLE" => array(
				"data_type" => "float"
			),
			"VALUE_DATE" => array(
				"data_type" => "datetime"
			)
		), $fieldsMap);
	}

}
?>
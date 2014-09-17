<?
namespace SimpleModule;

class UtsEntityTable extends \Bitrix\Main\Entity\DataManager
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
		return "xdev_parser_uts_domain_entity";
	}

	public static function isUts()
	{
		return true;
	}

	public static function getMap()
	{
		// get ufields
		$fieldsMap = $GLOBALS["USER_FIELD_MANAGER"]->getUserFields(static::getUfId());

		foreach($fieldsMap as $k => $v)
		{
			if($v["MULTIPLE"] == "Y")
				unset($fieldsMap[$k]);
		}

		return array_merge(array(
			"VALUE_ID" => array(
				"data_type" => "integer",
				"primary" => true
			),
			"SOURCE_OBJECT" => array(
				"data_type" => "SimpleModule\Entity",
				"reference" => array("=this.VALUE_ID" => "ref.ID")
			),
		), $fieldsMap);
	}

}
?>
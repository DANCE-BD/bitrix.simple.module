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
			"NAME" => array(
				"data_type" => "string",
				"required" => true,
				"title" => Loc::getMessage("SM_ENTITY_NAME_FIELD"),
			),
			"FULL_URL" => array(
				"data_type" => "string",
				"required" => true,
				"title" => Loc::getMessage("SM_ENTITY_FULL_URL_FIELD"),
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
				"default" => 500,
				"title" => Loc::getMessage("SM_ENTITY_SORT_FIELD"),
			),
			"TIME_LIMIT" => array(
				"data_type" => "integer",
				"title" => Loc::getMessage("SM_ENTITY_TIME_LIMIT_FIELD"),
			),
			"IBLOCK_ID" => array(
				"data_type" => "integer",
				"required" => true,
				"title" => Loc::getMessage("SM_ENTITY_IBLOCK_ID_FIELD"),
			),

			"SPL_ITEM" => array(
				"data_type" => "string",
				"required" => true,
				"title" => Loc::getMessage("SM_ENTITY_SPL_ITEM_FIELD"),
			),
			"SPL_ITEM_HREF" => array(
				"data_type" => "string",
				"required" => true,
				"title" => Loc::getMessage("SM_ENTITY_SPL_ITEM_HREF_FIELD"),
			),
			"SPL_ITEM_NAME" => array(
				"data_type" => "string",
				"title" => Loc::getMessage("SM_ENTITY_SPL_ITEM_NAME_FIELD"),
			),
			"SPL_ITEM_PREVIEW_TEXT" => array(
				"data_type" => "string",
				"title" => Loc::getMessage("SM_ENTITY_SPL_ITEM_PREVIEW_TEXT_FIELD"),
			),
			"SPL_ITEM_PREVIEW_PICTURE" => array(
				"data_type" => "string",
				"title" => Loc::getMessage("SM_ENTITY_SPL_ITEM_PREVIEW_PICTURE_FIELD"),
			),
			"ITEM_PREVIEW_TEXT_TYPE" => array(
				"data_type" => "boolean",
				"required" => true,
				"values" => array("html","text"),
				"title" => Loc::getMessage("SM_ENTITY_ITEM_PREVIEW_TEXT_TYPE_FIELD"),
			),

			"SPD_ITEM" => array(
				"data_type" => "string",
				"required" => true,
				"title" => Loc::getMessage("SM_ENTITY_SPD_ITEM_FIELD"),
			),
			"SPD_ITEM_NAME" => array(
				"data_type" => "string",
				"title" => Loc::getMessage("SM_ENTITY_SPD_ITEM_NAME_FIELD"),
			),
			"SPD_ITEM_DETAIL_TEXT" => array(
				"data_type" => "string",
				"title" => Loc::getMessage("SM_ENTITY_SPD_ITEM_DETAIL_TEXT_FIELD"),
			),
			"SPD_ITEM_DETAIL_PICTURE" => array(
				"data_type" => "string",
				"title" => Loc::getMessage("SM_ENTITY_SPD_ITEM_DETAIL_PICTURE_FIELD"),
			),
			"ITEM_DETAIL_TEXT_TYPE" => array(
				"data_type" => "boolean",
				"required" => true,
				"values" => array("html","text"),
				"title" => Loc::getMessage("SM_ENTITY_ITEM_DETAIL_TEXT_TYPE_FIELD"),
			),
		);
	}
}
?>

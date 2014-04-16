<?
namespace SimpleModule;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

abstract class CUserTypeAutoComplete extends \CUserTypeInteger
{
	abstract public static function GetList($arSort=array(), $arFilter=array(), $arNavParams=false, $arUserField);

	protected static function GetDefaultComponentTemplate()
	{
		return "usertype";
	}

	protected static function GetOtherSymbol()
	{
		return BT_UT_AUTOCOMPLETE_REP_SYM_OTHER;
	}

	protected static function GetReplaceSymList($boolFull = false)
	{
		return (true == $boolFull)
			? array(
				"REFERENCE" => array(
					GetMessage("MD_STORE_USER_TYPE_SYM_SPACE"),
					GetMessage("MD_STORE_USER_TYPE_SYM_GRID"),
					GetMessage("MD_STORE_USER_TYPE_SYM_STAR"),
					GetMessage("MD_STORE_USER_TYPE_SYM_UNDERLINE"),
					GetMessage("MD_STORE_USER_TYPE_SYM_OTHER"),

				),
				"REFERENCE_ID" => array(
					" ",
					"#",
					"*",
					"_",
					static::GetOtherSymbol(),
				),
			) : array(" ", "#", "*","_");
	}

	protected static function GetValueForAutoComplete($arUserField, $arValue, $arBanSym, $arRepSym)
	{
		$strResult = "";
		if(0 < strlen($arValue))
		{
			$mxResult = self::GetPropertyValue($arUserField, $arValue);
			if(true == is_array($mxResult))
				$strResult .= htmlspecialchars(str_replace($arBanSym, $arRepSym, $mxResult["NAME"]))." [".$mxResult["ID"]."]\n";
		}

		return $strResult;
	}

	protected static function GetValueForAutoCompleteMulti($arUserField, $arValues, $arBanSym, $arRepSym)
	{
		$arResult = false;
		if(true == is_array($arValues))
		{
			foreach($arValues as $intPropertyValueID => $arOneValue)
			{
				$mxResult = self::GetPropertyValue($arUserField, $arOneValue);
				if(true == is_array($mxResult))
					$arResult[$intPropertyValueID] = htmlspecialchars(str_replace($arBanSym, $arRepSym, $mxResult["NAME"]))." [".$mxResult["ID"]."]";
			}
		}

		return join("\n", $arResult);
	}

	protected static function GetSymbols($arSettings)
	{
		$arResult = false;
		$strBanSym = $arSettings["BAN_SYM"];
		$strRepSym = (static::GetOtherSymbol() == $arSettings["REP_SYM"] ? $arSettings["OTHER_REP_SYM"] : $arSettings["REP_SYM"]);
		$arBanSym = str_split($strBanSym,1);
		$arRepSym = array_fill(0, sizeof($arBanSym), $strRepSym);

		return array(
			"BAN_SYM" => $arBanSym,
		 	"REP_SYM" => array_fill(0,sizeof($arBanSym),$strRepSym),
			"BAN_SYM_STRING" => $strBanSym,
			"REP_SYM_STRING" => $strRepSym,
		);
	}

	public function PrepareSettings($arUserField)
	{
		/*
		 * TEMPLATE		- component template name
		 * MAX_WIDTH		- max width textarea and input in pixels
		 * MIN_HEIGHT		- min height textarea in pixels
		 * MAX_HEIGHT		- max height textarea in pixels
		 * BAN_SYM		- banned symbols string
		 * REP_SYM		- replace symbol
		 * OTHER_REP_SYM	- non standart replace symbol
		 */

		if(!is_array($arUserField))
			$arUserField = array("SETTINGS" => array());

		$strTemplate = $arUserField["SETTINGS"]["TEMPLATE"];
		if (0 >= strlen($strTemplate))
			$strTemplate = static::GetDefaultComponentTemplate();

		$intMaxWidth = intval(true == isset($arUserField["SETTINGS"]["MAX_WIDTH"]) ? $arUserField["SETTINGS"]["MAX_WIDTH"] : 0);
		if (0 >= $intMaxWidth) $intMaxWidth = 0;

		$intMinHeight = intval(true == isset($arUserField["SETTINGS"]["MIN_HEIGHT"]) ? $arUserField["SETTINGS"]["MIN_HEIGHT"] : 0);
		if (0 >= $intMinHeight) $intMinHeight = 24;

		$intMaxHeight = intval(true == isset($arUserField["SETTINGS"]["MAX_HEIGHT"]) ? $arUserField["SETTINGS"]["MAX_HEIGHT"] : 0);
		if (0 >= $intMaxHeight) $intMaxHeight = 1000;

		$strBannedSymbols = trim(true == isset($arUserField["SETTINGS"]["BAN_SYM"]) ? $arUserField["SETTINGS"]["BAN_SYM"] : ",;");
		$strBannedSymbols = str_replace(" ","",$strBannedSymbols);
		if (false === strpos($strBannedSymbols,","))
			$strBannedSymbols .= ",";
		if (false === strpos($strBannedSymbols,";"))
			$strBannedSymbols .= ";";

		$strOtherReplaceSymbol = "";
		$strReplaceSymbol = (true == isset($arUserField["SETTINGS"]["REP_SYM"]) ? $arUserField["SETTINGS"]["REP_SYM"] : " ");
		if (static::GetOtherSymbol() == $strReplaceSymbol)
		{
			$strOtherReplaceSymbol = (true == isset($arUserField["SETTINGS"]["OTHER_REP_SYM"]) ? substr($arUserField["SETTINGS"]["OTHER_REP_SYM"],0,1) : "");
			if (("," == $strOtherReplaceSymbol) || (";" == $strOtherReplaceSymbol))
				$strOtherReplaceSymbol = "";

			if (("" == $strOtherReplaceSymbol) || (true == in_array($strOtherReplaceSymbol,self::GetReplaceSymList())))
			{
				$strReplaceSymbol = $strOtherReplaceSymbol;
				$strOtherReplaceSymbol = "";
			}
		}
		if ("" == $strReplaceSymbol)
		{
			$strReplaceSymbol = " ";
			$strOtherReplaceSymbol = "";
		}

		return array(
			"TEMPLATE" => $strTemplate,
			"MAX_WIDTH" => $intMaxWidth,
			"MIN_HEIGHT" => $intMinHeight,
			"MAX_HEIGHT" => $intMaxHeight,
			"BAN_SYM" => $strBannedSymbols,
			"REP_SYM" => $strReplaceSymbol,
			"OTHER_REP_SYM" => $strOtherReplaceSymbol,
		);
	}

	function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
	{
		$arSettings = static::PrepareSettings($arUserField, $bVarsFromForm);

		return
		"<tr>
			<tr>
			<td valign=\"top\">".GetMessage("MD_STORE_USER_TYPE_SETTING_TEMPLATE")."</td>
				<td><input type=\"text\" name=\"".$arHtmlControl["NAME"]."[TEMPLATE]\" value=\"".$arSettings["TEMPLATE"]."\">&nbsp;".GetMessage("MD_STORE_USER_TYPE_SETTING_COMMENT_TEMPLATE")."</td>
			</tr>
			<tr>
			<td valign=\"top\">".GetMessage("MD_STORE_USER_TYPE_SETTING_MAX_WIDTH")."</td>
				<td><input type=\"text\" name=\"".$arHtmlControl["NAME"]."[MAX_WIDTH]\" value=\"".intval($arSettings["MAX_WIDTH"])."\">&nbsp;".GetMessage("MD_STORE_USER_TYPE_SETTING_COMMENT_MAX_WIDTH")."</td>
			</tr>
			<tr>
			<td valign=\"top\">".GetMessage("MD_STORE_USER_TYPE_SETTING_MIN_HEIGHT")."</td>
				<td><input type=\"text\" name=\"".$arHtmlControl["NAME"]."[MIN_HEIGHT]\" value=\"".intval($arSettings["MIN_HEIGHT"])."\">&nbsp;".GetMessage("MD_STORE_USER_TYPE_SETTING_COMMENT_MIN_HEIGHT")."</td>
			</tr>
			<tr>
			<td valign=\"top\">".GetMessage("MD_STORE_USER_TYPE_SETTING_MAX_HEIGHT")."</td>
				<td><input type=\"text\" name=\"".$arHtmlControl["NAME"]."[MAX_HEIGHT]\" value=\"".intval($arSettings["MAX_HEIGHT"])."\">&nbsp;".GetMessage("MD_STORE_USER_TYPE_SETTING_COMMENT_MAX_HEIGHT")."</td>
			</tr>
			<tr>
			<td valign=\"top\">".GetMessage("MD_STORE_USER_TYPE_SETTING_BAN_SYMBOLS")."</td>
				<td><input type=\"text\" name=\"".$arHtmlControl["NAME"]."[BAN_SYM]\" value=\"".htmlspecialchars($arSettings["BAN_SYM"])."\"></td>
			</tr>
			<tr>
				<td valign=\"top\">".GetMessage("MD_STORE_USER_TYPE_SETTING_REP_SYMBOL")."</td>
			<td>".\SelectBoxFromArray($arHtmlControl["NAME"]."[REP_SYM]", self::GetReplaceSymList(true),htmlspecialchars($arSettings["REP_SYM"]))."&nbsp;<input type=\"text\" name=\"".$strHTMLControlName["NAME"]."[OTHER_REP_SYM]\" size=\"1\" maxlength=\"1\" value=\"".$arSettings["OTHER_REP_SYM"]."\"></td>
		</tr>
		";
	}

	function GetEditFormHTML($arUserField, $strHTMLControlName)
	{
		global $APPLICATION;

		$arSettings = static::PrepareSettings($arUserField);
		$arSymbols = static::GetSymbols($arSettings);

		$strResult = "";
		if((true == isset($strHTMLControlName["MODE"])) && ("iblock_element_admin" == $strHTMLControlName["MODE"]))
		{
			$mxElement = false;
			$mxElement = self::GetPropertyValue($arUserField, $strHTMLControlName["VALUE"]);
			if (false == is_array($mxElement))
			{
				$strResult = "<input type=\"text\" name=\"".htmlspecialchars($strHTMLControlName["NAME"])."\" id=\"".$strHTMLControlName["NAME"]."\" value=\"\" size=\"5\">";
			}
			else
			{
				$strResult = "<input type=\"text\" name=\"".$strHTMLControlName["NAME"]."\" id=\"".$strHTMLControlName["NAME"]."\" value=\"".$strHTMLControlName["VALUE"]."\" size=\"5\">";
			}
		}
		else
		{
			ob_start();
			$control_id = $APPLICATION->IncludeComponent(
				"bitrix:main.lookup.input",
				$arSettings["TEMPLATE"],
				array(
					"CONTROL_ID" => preg_replace("/[^a-zA-Z0-9_]/i", "x", $strHTMLControlName["NAME"]),
					"INPUT_NAME" => $strHTMLControlName["NAME"],
					"INPUT_NAME_STRING" => "inp_".$strHTMLControlName["NAME"],
					"INPUT_VALUE_STRING" => htmlspecialcharsback(
						($arUserField["MULTIPLE"] == "Y")
							? self::GetValueForAutoCompleteMulti($arUserField, $strHTMLControlName["VALUE"], $arSymbols["BAN_SYM"], $arSymbols["REP_SYM"])
							: self::GetValueForAutoComplete($arUserField, $strHTMLControlName["VALUE"], $arSymbols["BAN_SYM"], $arSymbols["REP_SYM"])
					),
					"START_TEXT" => GetMessage("DFA_PROP_AC_MESS_INVITE"),
					"MULTIPLE" => $arUserField["MULTIPLE"],
					"MAX_WIDTH" => $arSettings["MAX_WIDTH"],
					"PROPERTY_ID" => $arUserField["ID"],
					"BAN_SYM" => $arSymbols["BAN_SYM_STRING"],
					"REP_SYM" => $arSymbols["REP_SYM_STRING"],
				), null, array("HIDE_ICONS" => "Y")
			);

			$strResult = ob_get_clean();
		}

		return $strResult;
	}

	function GetEditFormHTMLMulty($arUserField, $strHTMLControlName)
	{
		return static::GetEditFormHTML($arUserField, $strHTMLControlName);
	}

	protected function GetPropertyValue($arUserField, $value)
	{
		$mxResult = false;

		if(!empty($value))
		{
			$mxResult = static::GetElement($value, $arUserField);
			if (true == is_array($mxResult))
			{
				$mxResult["PROPERTY_ID"] = $arUserField["ID"];
				if (true == isset($arUserField["VALUE_ID"]))
					$mxResult["PROPERTY_VALUE_ID"] = $arUserField["VALUE_ID"];
				else
					$mxResult["PROPERTY_VALUE_ID"] = false;
			}
		}

		return $mxResult;
	}

	function CheckFields($arUserField, $value)
	{
		$aMsg = array();

		return $aMsg;
	}

	protected static function __makeArrayFromAlias($arFilter, $arAliases)
	{
		$result = array();

		foreach($arAliases as $key => $val)
		{
			foreach(array("", "=", "!", "?", "%", ">", "<", "><", "<=", ">=") as $operation)
			{
				if(is_set($arFilter, $operation.$key))
					$result[$operation.$val] = $arFilter[$operation.$key];
			}
		}

		return $result;
	}

	protected static function GetElement($intElementID, $arUserField)
	{
		static $arCache = array();

		$intElementID = intval($intElementID);
		if(!is_set($arCache, $intElementID))
		{
			$rs = static::GetList(array("NAME" => "ASC"), array("ID" => $intElementID), $arUserField);
			$arCache[$intElementID] = $rs->Fetch();
		}

		return $arCache[$intElementID];
	}
}
?>

<?
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");

$module_id = "simple.module";

global $settings_id;
$settings_id = "simple_module";

$MODULE_RIGHT = $APPLICATION->GetGroupRight($module_id);
if($MODULE_RIGHT < "W")
{
	CAdminMessage::ShowMessage(array("MESSAGE" => GetMessage("ACCESS_DENIED"), "TYPE" => "ERROR"));
	return;
}
CModule::IncludeModuleEx($module_id);

$strWarning = "";

$aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
        array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => $settings_id . "_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
);

$arOptionGroups = array(
// 	array(
// 		GetMessage($settings_id . "_OPTION_GROUP_NAME"),
// 		array(
// 			array("option_text", GetMessage($settings_id . "option_text_OPTION_NAME"), "", array("text", 50)),
// 			array("option_checkbox", GetMessage($settings_id . "option_checkbox_OPTION_NAME"), "", array("checkbox"))
// 		)
// 	)
);

//Restore defaults
if($MODULE_RIGHT >= "W" && $_SERVER["REQUEST_METHOD"] == "GET" && strlen($RestoreDefaults) > 0 && check_bitrix_sessid())
{
        COption::RemoveOption($settings_id);
}
$tabControl = new CAdminTabControl("tabControl", $aTabs);

function ShowParamsHTMLByarray($arParams)
{
	global $settings_id;
        foreach($arParams as $Option)
                 __AdmSettingsDrawRow($settings_id, $Option);
}

//Save options
if($REQUEST_METHOD == "POST" && $MODULE_RIGHT >= "W" && strlen($Update.$Apply.$RestoreDefaults) > 0 && check_bitrix_sessid())
{
        if(strlen($RestoreDefaults) > 0)
        {
                COption::RemoveOption($settings_id);
        }
        else
        {
        	foreach($arOptionGroups as $arOptionGroup)
        	{
        		foreach($arOptionGroup[1] as $option)
        			__AdmSettingsSaveOption($settings_id, $option);
        	}
	}
}
?>
<script type="text/javascript">
<!--
	function RestoreDefaults()
	{
	        if(confirm('<? echo GetMessageJS("MAIN_HINT_RESTORE_DEFAULTS_WARNING"); ?>'))
	                window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo urlencode($mid)?>&<?=bitrix_sessid_get()?>";
	}
//-->
</script>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&amp;lang=<?echo LANG?>"><?

$tabControl->Begin();

$tabControl->BeginNextTab();

foreach($arOptionGroups as $arOptionGroup)
{
	?><tr class="heading">
		<td colspan=2><? echo $arOptionGroup[0]; ?></td>
	</tr><?

	foreach($arOptionGroup[1] as $option)
		__AdmSettingsDrawRow($settings_id, $option);
}

$tabControl->BeginNextTab();

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
<?$tabControl->Buttons();?>
	<input type="hidden" name="Update" value="Y">
	<input type="submit" class="adm-btn-save"<?if($MODULE_RIGHT < "W") echo " disabled ";?>name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">
	<input type="reset"<?if($MODULE_RIGHT < "W") echo " disabled ";?>name="reset" value="<? echo GetMessage("MAIN_RESET"); ?>">
	<input type="button"<?if($MODULE_RIGHT < "W") echo " disabled ";?>type="button" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="RestoreDefaults();" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
<?$tabControl->End();?>
<?=bitrix_sessid_post();?>
</form>
<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/tools/prop_userid.php");
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

/**
 * List redirect page
 * @var string
 */
$strRedirect_admin = BX_ROOT."/admin/simple_module_domain_admin.php?lang=" . LANGUAGE_ID;

/**
 * Success redirect page
 * @var string
 */
$strRedirect = BX_ROOT."/admin/simple_module_domain_edit.php?lang=" . LANGUAGE_ID;

/**
 * Endity datamanager classname
 * @var string
 */
$sDataClassName = "\SimpleModule\EntityTable";

/**
 * Module id
 * @var string
 */
$module_id = "simple.module";

$MODULE_RIGHT = $APPLICATION->GetGroupRight($module_id);
if($MODULE_RIGHT < "W") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

CModule::IncludeModuleEx($module_id);

$sTableID = preg_replace("/\\\+/", "_", $sDataClassName) . "_edit";
$sUfid = $sDataClassName::getUfId();

$ID = intval($_REQUEST["ID"]);

$rs = $sDataClassName::getByID($ID);
$rs = new CAdminResult($rs);
if(!$rs->ExtractFields("str_"))
{
	$ID = 0;
	$str_ACTIVE = "Y";
	$str_SORT = 500;
}

$APPLICATION->SetTitle($ID > 0 ? GetMessage("SM_ENTITY_EDIT_TITLE", array("#ID#" => $ID)) : GetMessage("SM_ENTITY_NEW_TITLE"));

$aTabs = array();
$aTabs[] = array("DIV" => "edit1", "TAB" => GetMessage("SM_ENTITY_TAB1"), "TITLE" => GetMessage("SM_ENTITY_TAB1_TITLE"));

$strError = "";
$tabControl = new CAdminForm($sTableID, $aTabs);

$arEditFields = array();
foreach($sDataClassName::getEntity()->getFields() as $field)
{
	if(
		$field instanceof Bitrix\Main\Entity\ScalarField
		&& !$field->isPrimary()
	)
	{
		$arEditFields[$field->getName()] = $field;
	}
}

$bFromForm = false;
if(
	$_SERVER["REQUEST_METHOD"]=="POST"
	&& (
		$_REQUEST["save"]<>''
		|| $_REQUEST["apply"]<>''
		|| $_REQUEST["Update"]=="Y"
		|| $_REQUEST["save_and_add"]<>''
	)
	&& check_bitrix_sessid()
)
{
	$arFields = array();
	foreach($arEditFields as $key => $field)
	{
		if(!in_array($key, array("TIMESTAMP_X")))
		{
			if($field instanceof Bitrix\Main\Entity\BooleanField)
			{
				$arFields[$key] = $_REQUEST[$key] == "Y" ? "Y" : "N";
			}
			else
			{
				$arFields[$key] = $_REQUEST[$key];
			}
		}
	}

	if(null !== $sUfid)
		$USER_FIELD_MANAGER->EditFormAddFields($sUfid, $arFields);

	if($ID)
		$result = $sDataClassName::update($ID, $arFields);
	else
		$result = $sDataClassName::add($arFields);

	if($result->isSuccess())
	{
		if(!$ID)
			$ID = $result->getId();

		if($_REQUEST["save"] <> '')
			LocalRedirect($strRedirect_admin);
		elseif($_REQUEST["apply"] <> '')
			LocalRedirect($strRedirect."&ID=".$ID."&".$tabControl->ActiveTabParam());
		elseif(strlen($save_and_add)>0)
			LocalRedirect($strRedirect."&ID=0&".$tabControl->ActiveTabParam());
	}
	else
	{
		$arErrors = array();
		foreach($result->getErrors() as $error)
			$arErrors[] = $error->getMessage();
		$strError = join("<br>", $arErrors);
		$bFromForm = true;
	}
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aMenu = array(
	array(
		"TEXT" => GetMessage("SM_ENTITY_BACK_TO_ADMIN"),
		"LINK" => $strRedirect_admin,
		"ICON" => "btn_list",
	)
);
if($ID > 0)
{
	$aMenu[] = array("SEPARATOR" => "Y");
	$aMenu[] = array(
		"TEXT"	=> GetMessage("SM_ENTITY_NEW_RECORD"),
		"LINK"	=> $strRedirect,
		"ICON"	=> "btn_new",
		"TITLE"	=> GetMessage("SM_ENTITY_NEW_RECORD"),
	);
	$aMenu[] = array(
		"TEXT"	=> GetMessage("SM_ENTITY_DELETE_RECORD"),
		"LINK"	=> "javascript:if(confirm('" . GetMessageJS("SM_ENTITY_DELETE_RECORD_CONFIRM") . "')) window.location='" . $strRedirect_admin . "&action=delete&ID=" . $ID . "&" . bitrix_sessid_get() . "';",
		"ICON"	=> "btn_delete",
		"TITLE"	=> GetMessage("SM_ENTITY_DELETE_RECORD"),
	);
}

$context = new CAdminContextMenu($aMenu);
$context->Show();

if($strError <> '')
{
	$e = new CAdminException(array(array("text" => $strError)));
	$message = new CAdminMessage(GetMessage("MAIN_ERROR_SAVING"), $e);
	echo $message->Show();
}

//We have to explicitly call calendar and editor functions because
//first output may be discarded by form settings
$tabControl->BeginPrologContent();
if(method_exists($USER_FIELD_MANAGER, 'showscript'))
	echo $USER_FIELD_MANAGER->ShowScript();

CAdminCalendar::ShowScript();
$tabControl->EndPrologContent();
$tabControl->BeginEpilogContent();
?>
<?=bitrix_sessid_post()?>
<input type="hidden" name="Update" value="Y" />
<?
$tabControl->EndEpilogContent();


$tabControl->Begin(array(
	"FORM_ACTION" => $APPLICATION->GetCurPage()."?ID=".intval($ID)."&lang=" . LANG
));

$tabControl->BeginNextFormTab();

if($ID > 0)
{
	$tabControl->AddViewField("ID", "ID:", $ID);

	if(is_set($arEditFields, "TIMESTAMP_X"))
	{
		$field = $arEditFields["TIMESTAMP_X"];
		unset($arEditFields["TIMESTAMP_X"]);

		$tabControl->AddViewField($field->getName(), $field->getTitle() . ":", ${"str_" . $field->getName()});
	}
}

foreach($arEditFields as $key => $field)
{
	if($field instanceof Bitrix\Main\Entity\BooleanField)
	{
//TODO: make dropdown here
		$tabControl->BeginCustomField($field->getName(), $field->getTitle() . ":", $field->isRequired());

		?><tr>
			<td width="40%"><? echo $tabControl->GetCustomLabelHTML(); ?></td>
			<td><input type="checkbox" name="<? echo $field->getName(); ?>" value="Y"<? if(${"str_" . $field->getName()} == "Y") echo " checked"; ?>></td>
		</tr><?

		$tabControl->EndCustomField($field->getName());
	}
	elseif($field instanceof Bitrix\Main\Entity\DatetimeField)
	{
		$tabControl->AddCalendarField($field->getName(), $field->getTitle() . ":", ${"str_" . $field->getName()}, $field->isRequired());
	}
	else
	{
		$tabControl->AddEditField($field->getName(), $field->getTitle() . ":", $field->isRequired(), array("size" => 4, "maxlength" => 255), ${"str_" . $field->getName()});
	}
}

if(null !== $sUfid)
{
	$tabControl->ShowUserFields($sUfid, $ID, $bFromForm);
}

/*
$tabControl->BeginCustomField("WORK_FROM", GetMessage("EMPLOYEE_ENTITY_WORK_FROM_FIELD") . ":", true);
?><tr>
	<td> <? echo $tabControl->GetCustomLabelHTML(); ?></td>
	<td><?$APPLICATION->IncludeComponent(
		"bitrix:main.clock",
		"",
		Array(
			"INPUT_ID" => "WORK_FROM",
			"INPUT_NAME" => "WORK_FROM",
			"INPUT_TITLE" => GetMessage("EMPLOYEE_ENTITY_WORK_FROM_FIELD"),
			"INIT_TIME" => $str_WORK_FROM,
			"STEP" => "0"
		),
	false
	);?></td>
</tr><?
$tabControl->EndCustomField("WORK_FROM");
*/

$tabControl->Buttons(array(
	"btnSaveAndAdd" => true,
	"back_url" => $strRedirect_admin,
));

$tabControl->Show();
$tabControl->ShowWarnings($tabControl->GetName(), $message);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
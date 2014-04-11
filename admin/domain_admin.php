<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
CPageOption::GetOptionString("main", "nav_page_in_session", "N");

/**
 * Entity edit page
 * @var string
 */
$strEditPath = BX_ROOT."/admin/simple_module_domain_edit.php?lang=" . LANGUAGE_ID;

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
if($MODULE_RIGHT == "D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
$can_edit = ($MODULE_RIGHT >= "W");

CModule::IncludeModuleEx($module_id);

$sTableID = preg_replace("/\\\+/", "_", $sDataClassName) . "_list";

$arHeaders = $arFilterFields = $arInitFilter = array(); $sPrimaryKey = "ID";
foreach($sDataClassName::getEntity()->getFields() as $field)
{
	if($field instanceof Bitrix\Main\Entity\ScalarField || $field instanceof Bitrix\Main\Entity\ExpressionField)
	{
		$id = $field->getColumnName();
		if($field->isPrimary())
			$sPrimaryKey = $field->getColumnName();

		if(strlen($field->getTitle()))
		{
			$title = GetMessage(sprintf("SM_%s_FIELD_ADMIN", $field->getName()));
			$arHeaders[] = array(
				"id" => $field->getName(),
				"content" => strlen($title) ? $title : $field->getTitle(),
				"sort" => $field->getName(),
				"default" => true,
			);

			if($field instanceof Bitrix\Main\Entity\DatetimeField)
			{
				$arInitFilter[$id . "_FROM"] = "find_" . $id . "_from";
				$arInitFilter[$id . "_TO"] = "find_" . $id . "_to";
				$arFilterFields["find_" . $id . "_from"] = $id;
				$arFilterFields["find_" . $id . "_to"] = $id;
			}
			else
			{
				$arInitFilter[$id] = "find_" . $id;
				$arFilterFields["find_" . $id] = $id;
			}
		}
	}
}

$lAdmin = new CAdminList(
	$sTableID,
	new CAdminSorting($sTableID, $sPrimaryKey, "asc")
);
// FILTER
$lAdmin->InitFilter($arInitFilter);

$arFilter = array();
foreach($sDataClassName::getEntity()->getFields() as $field)
{
	if($field instanceof Bitrix\Main\Entity\ScalarField)
	{
		$id = $field->getColumnName();
		$fieldName = "find_" . $id;

		if($field instanceof Bitrix\Main\Entity\DatetimeField)
		{
			if(!empty(${$fieldName . "_from"}))
				$arFilter[">=" . $id] = ${$fieldName . "_from"};
			if(!empty(${$fieldName . "_to"}))
				$arFilter["<=" . $id] = ${$fieldName . "_to"};
		}
		else
		{
			if(!empty(${$fieldName}))
				$arFilter[$id] = ${$fieldName};
		}
	}
}

// ACTIONS
if($lAdmin->EditAction() && $can_edit)
{
	foreach($FIELDS as $ID => $arFields)
	{
		if(!$lAdmin->IsUpdated($ID))
			continue;

		$errors = "";
		$result = $sDataClassName::update($ID, $arFields);
		if(!$result->isSuccess())
		{
			foreach($result->getErrors() as $error)
				$errors .= $error->getMessage()."<br>";
		}
		if(!empty($errors))
			$lAdmin->AddUpdateError($errors, $ID);
	}
}

if($arID = $lAdmin->GroupAction())
{
	if($_REQUEST["action_target"] == "selected")
	{
		$arID = Array();
		$rs = $sDataClassName::getList(array(
			"select" => array("ID"),
			"filter" => $arFilter
		));
		while($ar = $rs->Fetch())
			$arID[] = $ar["ID"];
	}

	foreach($arID as $ID)
	{
		$ID = IntVal($ID);
		if($ID <= 0)
			continue;

		$result = new \Bitrix\Main\Entity\Result();
		switch($_REQUEST["action"])
		{
			case "delete":
				if($can_edit)
					$result = $sDataClassName::delete($ID);
				break;

			case "activate":
			case "deactivate":
				$arFields = array("ACTIVE" => ($_REQUEST["action"] == "activate" ? "Y" : "N"));
				$result = $sDataClassName::update($ID, $arFields);
				break;
		}

		if(!$result->isSuccess())
		{
			$arErrors = array();
			foreach($result->getErrors() as $error)
				$arErrors[] = $error->getMessage();
			$lAdmin->AddGroupError(join("<br>", $arErrors), $ID);
		}
	}
}


$lAdmin->AddHeaders($arHeaders);

$arSelect = array();
foreach($arHeaders as $val)
{
	if(in_array($val["id"], $lAdmin->GetVisibleHeaderColumns()))
		$arSelect[$val["id"]] = (is_set($val, "sort") ? $val["sort"] : $val["id"]);
}
if(!in_array($sPrimaryKey, $arSelect))
	$arSelect[$sPrimaryKey] = $sPrimaryKey;

$rsData = $sDataClassName::getList(array(
	"select" => $arSelect,
	"filter" => $arFilter,
	"order" => array(
		$by => strtoupper($order)
	),
));

$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint());

while($data = $rsData->NavNext(true, "f_"))
{
	$arActions = Array();
	$row = &$lAdmin->AddRow($f_ID, $data);

	if($can_edit)
	{
		foreach($sDataClassName::getEntity()->getFields() as $field)
		{
			if(
				($field instanceof Bitrix\Main\Entity\ScalarField)
				&& in_array($field->getColumnName(), $lAdmin->GetVisibleHeaderColumns())
				&& !$field->isPrimary()
			)
			{
				switch($field->getDataType())
				{
					case "string":
					case "integer":
						$row->AddInputField($field->getColumnName());
						break;

					case "boolean":
						$row->AddCheckField($field->getColumnName());
						break;

					default:
						break;
				}
			}
		}
		$arActions[] = array("ICON" => "edit", "TEXT" => GetMessage("MAIN_ADMIN_MENU_EDIT"), "LINK"=> $strEditPath . "&ID=".$f_ID, "DEFAULT" => true);
	}

	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array("title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value" => $rsData->SelectedRowsCount()),
		array("counter" => true, "title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value" => "0"),
	)
);

$arParams = array();
$arGroupActions = array();
if($can_edit)
{
	$arGroupActions = Array(
		"delete" => true,
		"activate" => GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
		"deactivate" => GetMessage("MAIN_ADMIN_LIST_DEACTIVATE")
	);
}
$lAdmin->AddGroupActionTable($arGroupActions, $arParams);

$aMenu = array();
if($MODULE_RIGHT >= "W")
{
	$aMenu[] = array(
		"ICON" => "btn_new",
		"TEXT" => GetMessage("SM_ENTITY_LIST_ADD_RECORD"),
		"LINK" => $strEditPath,
		"TITLE" => GetMessage("SM_ENTITY_LIST_ADD_RECORD_TITLE")
	);
}
$lAdmin->AddAdminContextMenu($aMenu);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("SM_TITLE_ADMIN"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

?><form method="GET" action="<? echo $APPLICATION->GetCurPage(); ?>" name="find_form">
	<input type="hidden" name="lang" value="<? echo LANG ?>">
	<input type="hidden" name="filter" value="Y"><?

	$arFindFields = array();
	foreach($arHeaders as $arHeader)
	{
		if(in_array($arHeader["id"], $arFilterFields) && $key = array_search($arHeader["id"], $arFilterFields))
			$arFindFields[$key] = $arHeader["content"];
	}
	$oFilter = new CAdminFilter($sTableID."_filter", $arFindFields);
	$oFilter->Begin();

	foreach($sDataClassName::getEntity()->getFields() as $field)
	{
		if(
			($field instanceof Bitrix\Main\Entity\ScalarField || $field instanceof Bitrix\Main\Entity\ExpressionField)
			&& in_array($field->getColumnName(), $arFilterFields)
		)
		{
			$id = $field->getColumnName();
			$fieldName = "find_" . $id;

			?><tr>
				<td><? echo HasMessage($module_id . "_FILTER_" . $id)
					? GetMessage($module_id . "_FILTER_" . $id)
					: $field->getTitle();
				?>:</td>
				<td><?

				if($field instanceof Bitrix\Main\Entity\BooleanField)
				{
					$arr = array(
						"REFERENCE_ID" => array(),
						"REFERENCE" => array(),
					);
					foreach($field->getValues() as $val)
					{
						$arr["REFERENCE"][] = ($val == "Y" ? GetMessage("MAIN_YES") : ($val == "N" ? GetMessage("MAIN_NO") : $val));
						$arr["REFERENCE_ID"][] = $val;
					}
					echo SelectBoxFromArray($fieldName, $arr, ${$fieldName}, GetMessage("MAIN_ALL"));
				}
				elseif($field instanceof Bitrix\Main\Entity\DatetimeField)
				{
					echo CalendarPeriod($fieldName . "_from", htmlspecialcharsbx(${$fieldName . "_from"}), $fieldName . "_to", htmlspecialcharsbx(${$fieldName . "_from"}), "find_form", true);
				}
				else
				{
					?><input type="text" name="<? echo $fieldName; ?>" value="<? echo htmlspecialcharsbx(${$fieldName}); ?>" /><?
				}

				?></td>
			</tr><?
		}
	}

	$oFilter->Buttons(array("table_id" => $sTableID, "url" => $APPLICATION->GetCurPage(), "form" => "find_form"));
	$oFilter->End();

?></form><?

if(strlen($errorMessage))
	CAdminMessage::ShowMessage(array("MESSAGE" => $errorMessage, "TYPE" => "ERROR"));

$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
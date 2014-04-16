<?
IncludeModuleLangFile(__FILE__);

class simple_module extends CModule
{
	const MODULE_ID = "simple.module";
	public $MODULE_ID = "simple.module";
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $MODULE_CSS;
	public $errors = false;

	public function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];

		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("SM_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("SM_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("SM_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("SM_PARTNER_URI");
	}

	public function InstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;

		$errors = false;
		if(!array_key_exists("savedata", $arParams) || $arParams["savedata"] != "Y")
			$errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/db/".ToLower($DB->type)."/install.sql");
		if($errors !== false)
			throw new \Exception(implode("<br>", $errors));

		RegisterModule(self::MODULE_ID);

		return true;
	}

	public function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;

		$errors = false;
		if(!array_key_exists("savedata", $arParams) || $arParams["savedata"] != "Y")
			$errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/db/".ToLower($DB->type)."/uninstall.sql");
		if($errors !== false)
			throw new \Exception(implode("<br>", $errors));

		UnRegisterModule(self::MODULE_ID);

		return true;
	}

	public function InstallEvents()
	{
		return true;
	}

	public function UnInstallEvents()
	{
		return true;
	}

	public function InstallAgents()
	{
		return true;
	}

	public function UnInstallAgents()
	{
		return true;
	}

	public function InstallUserTypes()
	{
		$arFieldsArray = array();
		$arFieldsArray["UF_LOCATION"] = array(
			"ENTITY_ID" => SimpleModule\EntityTable::getUfId(),
			"FIELD_NAME" => "UF_LOCATION",
			"USER_TYPE_ID" => "sm_statloc",
			"SORT" => 100,
			"MULTIPLE" => "Y",
			"MANDATORY" => "Y",
			"SHOW_FILTER" => "N",
			"SHOW_IN_LIST" => "Y",
			"EDIT_IN_LIST" => "N",
			"IS_SEARCHABLE" => "N",
			"SETTINGS" => array(),
			"EDIT_FORM_LABEL" => array(LANGUAGE_ID => GetMessage("SM_USER_TYPE_LOCATION_EDIT_FORM_LABEL")),
			"LIST_COLUMN_LABEL" => array(LANGUAGE_ID => GetMessage("SM_USER_TYPE_LOCATION_LIST_COLUMN_LABEL")),
			"LIST_FILTER_LABEL" => array(LANGUAGE_ID => GetMessage("SM_USER_TYPE_LOCATION_LIST_FILTER_LABEL")),
		);

		RegisterModuleDependences("main", "OnUserTypeBuildList", self::MODULE_ID, "SimpleModule\\Usertype\\StatLocation", "GetUserTypeDescription");
		$GLOBALS["CACHE_MANAGER"]->clean("b_module_to_module"); // Bitrix clean bug
		AddEventHandler("main", "OnUserTypeBuildList", array("SimpleModule\\Usertype\\StatLocation", "GetUserTypeDescription"));

		$arFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields(SimpleModule\EntityTable::getUfId());
		foreach($arFieldsArray as $ar)
		{
			if(!is_set($arFields, $ar["FIELD_NAME"]))
			{
				$ob = new CUserTypeEntity();

				$GLOBALS["APPLICATION"]->ResetException();
				if(!$res = $ob->Add($ar))
				{
					$e = $GLOBALS["APPLICATION"]->GetException();
					throw new \Exception($e->getString());
				}
			}
		}

		return true;
	}

	public function UnInstallUserTypes($arParams = array())
	{
		if((!array_key_exists("savedata", $arParams) || $arParams["savedata"] != "Y"))
		{
			$ob = new \CUserTypeEntity();

			$arFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields(SimpleModule\EntityTable::getUfId());
			foreach($arFields as $key => $val)
				$ob->Delete($val["ID"]);

			UnRegisterModuleDependences("main", "OnUserTypeBuildList", self::MODULE_ID, "SimpleModule\\Usertype\\StatLocation", "GetUserTypeDescription");
		}

		return true;
	}

	public function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/templates/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/templates", false, true);

		return true;
	}

	public function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/templates/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/templates");

		return true;
	}

	public function DoInstall()
	{
		global $DB;
		$DB->StartTransaction();
		$this->errors = false;

		try
		{
			$this->InstallDB();
			\CModule::IncludeModule(self::MODULE_ID);

			$this->InstallUserTypes();
			$this->InstallFiles();
			$this->InstallEvents();
			$this->InstallAgents();

			$DB->Commit();
		}
		catch(\Exception $e)
		{
			$DB->Rollback();
			$this->errors = $e->GetMessage();
			$GLOBALS["APPLICATION"]->ThrowException($this->errors);
		}

		return empty($this->errors);
	}

	public function DoUninstall()
	{
		global $DB, $DOCUMENT_ROOT, $APPLICATION, $step;
		$step = IntVal($step);

		$this->errors = false;
		if($step < 2)
		{
			$APPLICATION->IncludeAdminFile(GetMessage("SM_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/unstep1.php");
		}
		elseif($step == 2)
		{
			$DB->StartTransaction();
			try
			{
				\CModule::IncludeModule(self::MODULE_ID);

				$this->UnInstallUserTypes(array("savedata" => $_REQUEST["savedata"]));
				$this->UnInstallDB(array("savedata" => $_REQUEST["savedata"]));
				$this->UnInstallFiles();
				$this->UnInstallEvents();
				$this->UnInstallAgents();

				$DB->Commit();
			}
			catch(\Exception $e)
			{
				$DB->Rollback();
				$this->errors = $e->GetMessage();
				$APPLICATION->ThrowException($this->errors);
			}
			$APPLICATION->IncludeAdminFile(GetMessage("SM_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/unstep2.php");
		}

		return empty($this->errors);
	}

        public function GetModuleRightList()
        {
                return array(
                        "reference_id" => array("D", "M", "W"),
                        "reference" => array(
                                "[D] ".GetMessage("SM_ACCESS_DENIED"),
                                "[M] ".GetMessage("SM_ACCESS_MODERATION"),
                                "[W] ".GetMessage("SM_ACCESS_FULL")
                        )
		);
        }
}
?>
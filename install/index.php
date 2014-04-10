<?
IncludeModuleLangFile(__FILE__);

class xdev_mdstore extends CModule
{
	const MODULE_ID = "xdev.mdstore";
	public $MODULE_ID = "xdev.mdstore";
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
		$this->MODULE_NAME = GetMessage("MD_STORE_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("MD_STORE_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("MD_STORE_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("MD_STORE_PARTNER_URI");
	}

	public function InstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;

		$errors = false;
		if(!array_key_exists("savedata", $arParams) || $arParams["savedata"] != "Y")
			$errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/db/".ToLower($DB->type)."/install.sql");
		if($errors !== false)
			throw new Exception(implode("<br>", $errors));

		// RegisterModuleDependences("main", "OnUserTypeBuildList", self::MODULE_ID, "OrderProcessing\CUserTypeTime", "GetUserTypeDescription");
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
			throw new Exception(implode("<br>", $errors));

		// UnRegisterModuleDependences("main", "OnUserTypeBuildList", self::MODULE_ID, "OrderProcessing\CUserTypeTime", "GetUserTypeDescription");
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

	public function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");

		return true;
	}

	public function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/themes/.default", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");

		return true;
	}

	public function DoInstall()
	{
		$this->errors = false;
		try
		{
			$this->InstallDB();
			$this->InstallFiles();
			$this->InstallEvents();
			$this->InstallAgents();
		}
		catch(Exception $e)
		{
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
			$APPLICATION->IncludeAdminFile(GetMessage("MD_STORE_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/unstep1.php");
		}
		elseif($step == 2)
		{
			try
			{
				$this->UnInstallDB(array("savedata" => $_REQUEST["savedata"]));
				$this->UnInstallFiles();
				$this->UnInstallEvents();
				$this->UnInstallAgents();
			}
			catch(Exception $e)
			{
				$this->errors = $e->GetMessage();
				$APPLICATION->ThrowException($this->errors);
			}
			$APPLICATION->IncludeAdminFile(GetMessage("MD_STORE_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/unstep2.php");
		}

		return empty($this->errors);
	}

        public function GetModuleRightList()
        {
                return array(
                        "reference_id" => array("D", "M", "W"),
                        "reference" => array(
                                "[D] ".GetMessage("MD_STORE_ACCESS_DENIED"),
                                "[M] ".GetMessage("MD_STORE_ACCESS_MODERATION"),
                                "[W] ".GetMessage("MD_STORE_ACCESS_FULL")
                        )
		);
        }
}
?>
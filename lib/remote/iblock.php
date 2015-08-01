<?
namespace simple\module\Remote;

class IblockImport
{
	private $section;

	public function __construct(Base $section)
	{
		if(!\Bitrix\Main\Loader::includeModule("iblock"))
			throw new \Exception("Iblock module not installed");

		$this->section = $section;
	}

	public function Import(Connection $connection)
	{
		$rs = \Bitrix\Iblock\SectionTable::getList(array(
			"select" => array(
				"ID", "XML_ID"
			),
			"filter" => array(
				"!XML_ID" => null,
				"ACTIVE" => "Y",
				"IBLOCK_ID" => $this->section->getIBlockID()
			)
		));
		while($ar = $rs->Fetch())
			$this->section->Import($connection, $ar["XML_ID"]);
	}
}
?>
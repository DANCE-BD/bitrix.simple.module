<?
namespace simple\module\Remote\Entity;

abstract class IBlock extends Base
{
	protected $iblock_id;

	public function __construct($iblock_id, Base $parent=null)
	{
		if(!\Bitrix\Main\Loader::includeModule("iblock"))
			throw new \Exception("Iblock module not installed");

		$this->setIBlockID($iblock_id);
		parent::__construct($parent);
	}

	public function getIBlockID()
	{
		return $this->iblock_id;
	}

	public function setIBlockID($iblock_id)
	{
		if(!is_numeric($iblock_id))
			throw new \Bitrix\Main\ArgumentTypeException("iblock_id", "number");
		elseif($iblock_id <= 0)
			throw new \Bitrix\Main\ArgumentOutOfRangeException("iblock_id", 1);
		$this->iblock_id = $iblock_id;

		return $this;
	}
}
?>
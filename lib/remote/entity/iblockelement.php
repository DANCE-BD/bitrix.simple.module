<?
namespace xdev\parser\Remote\Entity;

abstract class IBlockElement extends IBlock
{
	public function save($arFields)
	{
		throw new \Bitrix\Main\NotImplementedException();
	}
}
?>
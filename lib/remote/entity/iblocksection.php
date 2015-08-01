<?
namespace simple\module\Remote\Entity;

abstract class IBlockSection extends IBlock
{
	public function save($arFields)
	{
		throw new \Bitrix\Main\NotImplementedException();
	}
}
?>
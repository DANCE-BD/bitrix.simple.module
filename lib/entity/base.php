<?
namespace simple\module\Entity;

abstract class Base
{
	private $parent = null;

	public function __construct(Base $parent=null)
	{
		if($parent !== null)
			$this->setParent($parent);
	}

	public function setParent(Base $parent)
	{
		$this->parent = $parent;

		return $this;
	}

	public function getParent()
	{
		return $this->parent;
	}

	public function Import(Connection $connection, $xml_id)
	{
		$arFields = $this->parse(
			$connection->Send($this->getContentUrl($xml_id))
		);
		if(is_array($arFields))
			$this->save($arFields);
	}

	public abstract function getContentUrl($params);

	public abstract function parse($html);

	public abstract function save($arFields);
}
?>
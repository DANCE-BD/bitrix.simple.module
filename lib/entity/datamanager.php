<?
namespace simple\module\Entity;

use Bitrix\Main\Entity\Event;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Entity\EntityError;
use Bitrix\Main\Entity\EventResult;

abstract class DataManager extends \Bitrix\Main\Entity\DataManager
{
	private static function getPrimary(Event $event)
	{
		$parameters = $event->getParameters();
		if(is_set($parameters, "id"))
			static::normalizePrimary($parameters["id"]);
		else
			$parameters["id"] = array(null);

		return $parameters["id"];
	}

	private static function getFields(Event $event)
	{
		$entity = $event->getEntity();
		$parameters = $event->getParameters();

		return $parameters["fields"];
	}

	private static function breforeAddUpdate(Event $event)
	{
		if(null !== static::getUfId())
		{
			$errors = array();
			foreach(static::getPrimary($event) as $id)
				if (!$GLOBALS["USER_FIELD_MANAGER"]->checkFields(static::getUfId(), $id, static::getFields($event)))
				{
					if($GLOBALS["APPLICATION"]->getException())
					{
						$e = $GLOBALS["APPLICATION"]->getException();
						$errors[] = new EntityError($e->getString());
						$GLOBALS["APPLICATION"]->resetException();
					}
					else
						$errors[] = new EntityError("Unknown error.");
				}

			$result = new EventResult();
			if(!empty($errors))
				$result->setErrors($errors);

			$event->addResult($result);
		}
	}

	private static function afterAddUpdate(Event $event)
	{
		if(null !== static::getUfId())
		{
			foreach(static::getPrimary($event) as $id)
				$GLOBALS["USER_FIELD_MANAGER"]->Update(static::getUfId(), $id, static::getFields($event));
		}
	}

	public static function onBeforeAdd(Event $event)
	{
		static::breforeAddUpdate($event);
	}

	public static function onBeforeUpdate(Event $event)
	{
		static::breforeAddUpdate($event);
	}

	public static function onAfterAdd(Event $event)
	{
		static::afterAddUpdate($event);
	}

	public static function onAfterUpdate(Event $event)
	{
		static::afterAddUpdate($event);
	}

	public static function onAfterDelete(Event $event)
	{
		if(null !== static::getUfId())
		{
			foreach(static::getPrimary($event) as $id)
				$GLOBALS["USER_FIELD_MANAGER"]->Delete(static::getUfId(), $id);
		}
	}
}
?>
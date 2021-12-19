<?php
declare(strict_types = 1);

namespace rark\simple_fill\handler;

use pocketmine\event\Event;
use pocketmine\event\Listener;

class EventListener implements Listener{
	/** @var BaseHandler[] */
	protected static array $handlers = [];

	public static function init():void{

	}

	public static function registerHandler(BaseHandler $handler):void{
		self::$handlers[$handler->getTarget()] = $handler;
	}

	protected static function onListen(Event $ev):void{
		if(!isset(self::$handlers[($class = get_class($ev))])) return;
		self::$handlers[$class]->handleEvent($ev);
	}
}
<?php

declare(strict_types = 1);

namespace rark\simple_fill;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskScheduler;
use rark\simple_fill\handler\EventListener;
use rark\simple_fill\libs\cortexpe\commando\PacketHooker;

class Loader extends PluginBase{
	protected static TaskScheduler $task_scheduler;

	protected function onEnable():void{
		if(!PacketHooker::isRegistered()) PacketHooker::register($this);
		self::$task_scheduler = $this->getScheduler();
		$this->getServer()->getPluginManager()->registerEvents(new EventListener, $this);
	}

	public static function getTaskScheduler():TaskScheduler{
		return self::$task_scheduler;
	}
}
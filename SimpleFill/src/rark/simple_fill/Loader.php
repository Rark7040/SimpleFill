<?php

declare(strict_types = 1);

namespace rark\simple_fill;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskScheduler;

class Loader extends PluginBase{
	protected static TaskScheduler $task_scheduler;

	protected function onEnable():void{
		self::$task_scheduler = $this->getScheduler();
	}

	public static function getTaskScheduler():TaskScheduler{
		return self::$task_scheduler;
	}
}
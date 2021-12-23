<?php
declare(strict_types = 1);

namespace rark\simple_fill\task;

use pocketmine\player\Player;
use rark\simple_fill\Loader;
use rark\simple_fill\obj\Container;

abstract class RunningTasks{
	final public function __construct(){/** NOOP */}
	/** @var BlockPlaceTask[] */
	protected static array $tasks = [];

	public static function run(Container $container, ?Player $holder = null):void{
		$task = new BlockPlaceTask($container, $holder);
		Loader::getTaskScheduler()->scheduleRepeatingTask($task, BlockPlaceTask::PLACE_SPEED);
		self::$tasks[] = $task;
	}

	public static function allStop():void{
		foreach(self::$tasks as $task){
			$task->stop();
			$task->rollback();
		}
		self::$tasks = [];
	}
}
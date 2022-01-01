<?php
declare(strict_types = 1);

namespace rark\simple_fill\task;

use pocketmine\player\Player;
use rark\simple_fill\effect\Errors;
use rark\simple_fill\Loader;
use rark\simple_fill\obj\Container;

abstract class RunningTasks{
	final public function __construct(){/** NOOP */}
	/** @var BlockPlaceTask[] */
	protected static array $tasks = [];
	protected static int $place_speed;

	public static function init(int $place_speed):void{
		if($place_speed < 1) throw new \InvalidArgumentException(Errors::INVALID_PLACE_SPEED);
		self::$place_speed = $place_speed;
	}

	public static function run(Container $container, ?Player $holder = null):void{
		$task = new BlockPlaceTask($container, $holder);
		Loader::getTaskScheduler()->scheduleRepeatingTask($task, self::$place_speed);
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
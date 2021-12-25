<?php
declare(strict_types = 1);

namespace rark\simple_fill;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskScheduler;
use rark\simple_fill\command\SimpleFillCommand;
use rark\simple_fill\command\SimpleUndoCommand;
use rark\simple_fill\handler\EventListener;
use rark\simple_fill\item\AirFill;
use rark\simple_fill\item\SwitchMode;
use rark\simple_fill\libs\cortexpe\commando\PacketHooker;
use rark\simple_fill\task\RunningTasks;

class Loader extends PluginBase{
	protected static TaskScheduler $task_scheduler;

	protected function onEnable():void{
		if(!PacketHooker::isRegistered()) PacketHooker::register($this);
		self::$task_scheduler = $this->getScheduler();
		$this->initItems();
		EventListener::init();
		$this->getServer()->getPluginManager()->registerEvents(new EventListener, $this);
		$this->getServer()->getCommandMap()->registerAll(
			$this->getName(),
			[
				new SimpleFillCommand($this),
				new SimpleUndoCommand($this)
			]
		);
	}

	protected function onDisable():void{
		RunningTasks::allStop();//todo rollback
	}

	public static function getTaskScheduler():TaskScheduler{
		return self::$task_scheduler;
	}

	protected function initItems():void{
		SwitchMode::init();
		AirFill::init();
	}
}
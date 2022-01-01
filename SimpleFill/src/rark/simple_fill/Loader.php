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
use rark\simple_fill\obj\Container;
use rark\simple_fill\obj\Logger;
use rark\simple_fill\task\BlockPlaceTask;
use rark\simple_fill\task\RunningTasks;

class Loader extends PluginBase{
	const CONF_NAME = 'config.yml';
	const MAX_FILL_SIZE = 'MaxFillSize';
	const PLACE_SPEED = 'PlaceSpeed';
	const FILL_SIZE = 'FillSize';
	const SAVE_LOG_SIZE = 'SaveLogSize';
	protected static TaskScheduler $task_scheduler;

	protected function onEnable():void{
		if(!PacketHooker::isRegistered()) PacketHooker::register($this);
		self::$task_scheduler = $this->getScheduler();
		$this->initItems();
		$this->initConf();
		$this->applyConfData();
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

	protected function initConf():void{
		$this->reloadConfig();
		if($this->saveResource(self::CONF_NAME)) throw new \RuntimeException('failed to load config file');
	}

	protected function applyConfData():void{
		$conf = $this->getConfig();
		RunningTasks::init((int) $conf->get(self::PLACE_SPEED, null)?? throw new \RuntimeException(''));
		BlockPlaceTask::init((int) $conf->get(self::FILL_SIZE, null)?? throw new \RuntimeException(''));
		Logger::init((int) $conf->get(self::SAVE_LOG_SIZE, null)?? throw new \RuntimeException(''));
		Container::init((int) $conf->get(self::MAX_FILL_SIZE, null)?? throw new \RuntimeException(''));
	}
}
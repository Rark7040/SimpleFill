<?php

declare(strict_types = 1);

namespace rark\simple_fill;

use pocketmine\{
	plugin\PluginBase,
	event\Listener,
	item\Item,
	item\ItemIds,
	utils\Config,
    scheduler\TaskScheduler
};
use pocketmine\item\VanillaItems;
use rark\simple_fill\{
	listener\BlockEventListener,
	listener\PlayerEventListener,
	command\FillCommand,
	command\UndoCommand,
	utils\Fill,
	utils\Undo
};

final class Main extends PluginBase{
	/** @var string */
	public const HEADER = '[SimpleFill]';
	/** @var Item[] */
	private static array $items = [];
	/** @var Fill */
	private static $fill;
	/** @var Undo */
	private static $undo;
	/** @var Config */
	private static $config;
    
    private static $scheduler;

	protected function onEnable():void{
		$this->setObject();
		$this->createConfig();
		$this->registerListener();
		$this->registerCommand();
		$this->loadFile();
	}

	public static function getFill():Fill{
		return self::$fill;
	}

	public static function getUndo():Undo{
		return self::$undo;
	}

	public static function getConfigFile():Config{
		return self::$config;
	}

	/** @return Item[] */
	public static function getItems():array{
		return self::$items;
	}
    
    public static function getSchedulerInstance():TaskScheduler{
        return self::$scheduler;
    }

	private function setObject():void{
		self::$fill = new Fill;
		self::$undo = new Undo;
        self::$scheduler = $this->getScheduler();
		$this->setItem();
	}

	private function createConfig():void{
		self::$config = new Config($this->getDataFolder().'Config.yaml', Config::YAML, [
			'UseType' => [
				'Sneak' => false,
				'Tap' => true
			],
			'Undo' => 15
		]);
	}

	private function setItem():void{
		self::$items[] = VanillaItems::TOTEM();
		self::$items[0]->setCustomName('§aSwitchFillMode');
		self::$items[0]->setLore(['タップでON/OFF切り替え', 'ON状態の時にブロックを二か所に設置でFill']);
		self::$items[] = VanillaItems::CHEMICAL_SUGAR();
		self::$items[1]->setCustomName('§aAirFill');
		self::$items[1]->setLore(['二か所タップで範囲内を空気で満たす']);
	}

	private function registerListener():void{
		array_map(function(Listener $listener):void{
			$this->getServer()->getPluginManager()->registerEvents($listener, $this);
		}, [
			new PlayerEventListener,
			new BlockEventListener
		]);
	}

	private function registerCommand():void{
		$this->getServer()->getCommandMap()->registerAll('[Rark]SimpleFill', [
			new FillCommand($this),
			new UndoCommand($this)
		]);
	}

	private function loadFile():void{
		$path = __DIR__.'/utils/';
		require_once($path.'sound.php');
		require_once($path.'sortPos.php');
		require_once($path.'in_region.php');
	}
}
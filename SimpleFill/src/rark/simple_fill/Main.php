<?php

declare(strict_types = 1);

namespace rark\simple_fill;

use pocketmine\plugin\PluginBase;
use pocketmine\item\Item;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use rark\simple_fill\listener\{PlayerEventListener, BlockEventListener};
use rark\simple_fill\command\{FillCommand, UndoCommand};
use rark\simple_fill\utils\{Fill, Undo};


final class Main extends PluginBase{
	/** @var string */
	public const HEADER = '[SimpleFill]';
	/** @var self */
	public static $instance;
	/** @var Item */
	public static $item;
	/** @var Fill */
	public static $fill;
	/** @var Undo */
	public static $undo;
	/** @var Config */
	public static $config;

	public function onEnable(){
		$this->setObject();
		$this->createConfig();
		$this->registerListener();
		$this->registerCommand();
		$this->loadFile();
	}

	private function setObject():void{
		self::$instance = $this;
		self::$fill = new Fill;
		self::$undo = new Undo;
		$this->setItem();
	}

	private function createConfig():void{
		self::$config = new Config($this->getDataFolder().'Config.yaml', Config::YAML, [
			'UseType' => [
				'Sneak' => false,
				'Tap' => true
			],
			'SaveQueue' => 15
		]);
	}

	private function setItem():void{
		$item = Item::get(450);
		$item->setCustomName('§aSwitchFillMode');
		$item->setLore([
			'タップでON/OFF切り替え',
			'ON状態の時にブロックを',
			'二か所に設置で設置でFill'
		]);
		self::$item = $item;
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
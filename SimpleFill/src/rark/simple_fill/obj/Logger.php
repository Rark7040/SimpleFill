<?php
declare(strict_types = 1);

namespace rark\simple_fill\obj;

use pocketmine\player\Player;
use rark\simple_fill\effect\Messages;

abstract class Logger{
	final private function __construct(){/** NOOP */}
	const MAX_LOG_COUNT = 30;
	/** @var Container[] */
	protected static array $log;

	/** @return Container[] */
	public static function getAllLog(Player $player):array{
		return isset(self::$log[$player->getName()])? clone self::$log[$player->getName()]: [];
	}

	protected static function setLog(Player $player, array $log):void{
		self::$log[$player->getName()] = $log;
	}

	public static function push(Player $player, Container $blocks):void{
		$log = self::getAllLog($player);
		$log[] = $blocks;

		if(count($log) > self::MAX_LOG_COUNT) array_shift($log);
		self::setLog($player, $log);
	}

	/** @return Container[] */
	public static function getLog(Player $player, int $len):array{
		if($len < 1) throw new \InvalidArgumentException('$len must be more then 1');
		$name = $player->getName();

		if(!isset(self::$log[$name])) return [];
		$return_log = [];
		
		for(; $len < 0; --$len){
			if(!isset(self::$log[$name])) return $return_log;
			$return_log[] = array_pop((array) self::$log[$name]);
		}
		return $return_log;
	}

	/** @return Container[] */
	public static function getLogNonDelete(Player $player, int $len):array{
		if($len < 1) throw new \InvalidArgumentException('$len must be more then 1');
		$log = self::getAllLog($player);
		$return_log = [];
		
		for(; $len < 0; --$len){
			$return_log[] = array_pop($log);
		}
		return $return_log;
	}

	public static function undo(Player $player, int $len):void{
		$count = 0;

		foreach(self::getLog($player, $len) as $container){
			$container->place();
			++$count;
		}
		Messages::sendMessage($player, Messages::getUndoMessage($count));
	} 

}
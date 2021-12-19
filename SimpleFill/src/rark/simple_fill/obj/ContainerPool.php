<?php
declare(strict_types = 1);

namespace rark\simple_fill\obj;

use pocketmine\player\Player;

abstract class ContainerPool{
	final private function __construct(){/** NOOP */}
	/** @var PreContainer[] */
	protected static array $pre_containers = [];
	/** @var Container[] */
	protected static array $containers = [];

	public static function prepare(Player $player):void{
		self::$containers[$player->getName()] = new PreContainer;
	}

	public static function getPreContainer(Player $player):?PreContainer{
		return self::hasPreContainer($player)? self::$pre_containers[$player->getName()]: null;
	}

	public static function hasPreContainer(Player $player):bool{
		return isset(self::$pre_containers[$player->getName()]);
	}

	public static function getContainer(Player $player):?Container{
		$pre_container = self::getPreContainer($player);
		return $pre_container === null? null: $pre_container->parse();
	}
}
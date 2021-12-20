<?php
declare(strict_types = 1);

namespace rark\simple_fill\item;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;

abstract class SFTool{
	final private function __construct(){/** NOOP */}
	const PARENT_TAG = 'simple_fill';

	abstract public static function init():void;
	abstract public static function getExtendedTag():string;
	/** @return \Generator<Item> */
	abstract protected static function getItems():\Generator;
	abstract public static function use(Player $player, Item $item, ?Block $block):void;

	protected static function setTag():void{
		$nbt = new CompoundTag;
		$nbt->setInt(self::getExtendedTag(), 1);
		
		foreach(self::getItems() as $item){
			$item->setNamedTag($nbt);
		}
	}

	public static function equals(Item $item):bool{
		foreach(self::getItems() as $self){ //...
			return $item->getNamedTag()->equals($self->getNamedTag());
		}
	}
}
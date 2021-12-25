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
	abstract public static function useOnBlock(Player $player, Item $item, Block $block):void;
	abstract public static function use(Player $player, Item $item):void;

	/** @return Item[] */
	protected static function setTag():array{
		/** @var Item[] */
		$items = [];
		$nbt = new CompoundTag;
		$nbt->setInt(static::getExtendedTag(), 1);
		
		foreach(static::getItems() as $item) $items[] = $item->setNamedTag($nbt);
		return $items;
	}

	public static function equals(Item $item):bool{
		foreach(static::getItems() as $self){ //...
			return $item->getNamedTag()->equals($self->getNamedTag());
		}
	}
}
<?php
declare(strict_types = 1);

namespace rark\simple_fill\item;

use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use rark\simple_fill\obj\Status;

class SwitchMode extends SFTool{
	const BASE_NAME = 'Switch Mode'.TextFormat::RESET;
	const ON_NAME = TextFormat::GREEN.self::BASE_NAME;
	const OFF_NAME = TextFormat::GRAY.self::BASE_NAME;
	const EXTENDED_TAG = self::PARENT_TAG.'switch_mode';
	protected static Armor $on;
	protected static Armor $off;

	public static function init():void{
		self::$on = VanillaItems::DIAMOND_CHESTPLATE();
		self::$off = VanillaItems::IRON_CHESTPLATE();
		self::$on->setCustomName(self::ON_NAME);
		self::$off->setCustomName(self::OFF_NAME);
		self::setTag();
	}

	public static function get(Player $player):Item{
		return clone (Status::is($player, Status::FILL)? self::$on: self::$off);
	}

	public static function getExtendedTag():string{
		return self::EXTENDED_TAG;
	}

	/**
	 * @return \Generator<Item>
	 */
	protected static function getItems():\Generator{
		yield self::$on;
		yield self::$off;
	}

	public static function use(Player $player, Item $item):void{
		if(!self::equals($item)) return;
		$custom_name = $item->getCustomName();

		if($custom_name === self::ON_NAME) self::onReceiveOn($player);
		elseif($custom_name === self::OFF_NAME) self::onReceiveOff($player);
		$player->getInventory()->getItemInHand(self::get($player));
	}

	protected static function onReceiveOn(Player $player):void{
		Status::set($player, Status::FILL);
	}

	protected static function onReceiveOff(Player $player):void{
		Status::clear($player, Status::FILL);
	}
}
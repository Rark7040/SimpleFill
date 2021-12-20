<?php
declare(strict_types = 1);

namespace rark\simple_fill\item;

use pocketmine\block\Block;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use rark\simple_fill\obj\ContainerPool;
use rark\simple_fill\obj\FillStatusWrapper;
use rark\simple_fill\obj\PreContainer;

class AirFill extends SFTool{
	const BASE_NAME = TextFormat::AQUA.'Air Fill'.TextFormat::RESET;
	const EXTENDED_TAG = self::PARENT_TAG.'air_fill';
	protected static Item $item;

	public static function init():void{
		self::$item = VanillaItems::IRON_PICKAXE();
		self::$item->setCustomName(self::BASE_NAME);
		self::$item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
		self::setTag();
	}

	public static function get():Item{
		return clone self::$item;
	}

	public static function getExtendedTag():string{
		return self::EXTENDED_TAG;
	}

	/**
	 * @return \Generator<Item>
	 */
	protected static function getItems():\Generator{
		yield self::$item;
	}

	public static function use(Player $player, Item $item, ?Block $block):void{
		if(!self::equals($item)) return;
		if($block === null) return;
		if(!FillStatusWrapper::isFillMode($player)) return;
		self::onBlockBreak($player, $block, ContainerPool::getPreContainerNonNull($player));

	}

	protected static function onBlockBreak(Player $player, Block $block, PreContainer $pre_container):void{
		$pre_container->push($block->getPosition()->asVector3());

		if(!$pre_container->isComplete()){
			return;
		}
		$container = $pre_container->parse();

		if($container === null){
			return;
		}
		$container->fill(ItemIds::AIR, 0, $player->getPosition()->getWorld());
		$container->place();
	}
}
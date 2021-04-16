<?php

declare(strict_types = 1);

namespace rark\simple_fill\listener;

use pocketmine\{
	Player,
	item\Item,
	block\Air,
	event\Listener,
	event\player\PlayerQuitEvent,
	event\player\PlayerInteractEvent,
	event\player\PlayerDropItemEvent,
	event\player\PlayerToggleSneakEvent
};
use rark\simple_fill\{
	Main,
	utils\Fill
};
use function rark\simple_fill\utils\sound;


final class PlayerEventListener implements Listener{
	/** @var Closure  */
	private $use_fill_item;

	public function __construct(){
		$this->use_fill_item = function(Player $player, Item $item):void{
			if($item->getCustomName() !== Main::getItems()[0]->getCustomName() or !$player->isOp()){
				return;
			}

			if(Main::getFill()->isRegisteredPlayer($player)){
				sound($player);
				Main::getFill()->unregisterPlayer($player);
				$player->sendMessage(Main::HEADER.'§bFillモードをOFFにしました');

			}else{
				sound($player);
				Main::getFill()->registerPlayer($player);
				$player->sendMessage(Main::HEADER.'§aFillモードをONにしました');
			}
		};
	}

	public function onQuit(PlayerQuitEvent $event):void{
		Main::getFill()->unregisterPlayer($event->getPlayer());
	}

	public function onSneak(PlayerToggleSneakEvent $event):void{
		if(!Main::getConfigFile()->get('UseType')['Sneak'] or !$event->isSneaking()){
			return;
		}
		$player = $event->getPlayer();
		$item = $player->getInventory()->getItemInHand();
		($this->use_fill_item)($player, $item);
	}

	public function onInteract(PlayerInteractEvent $event):void{
		$item = $event->getItem();
		$player = $event->getPlayer();

		if($item->getCustomName() === Main::getItems()[1]->getCustomName() and $event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK){
			$fill = Main::getFill();

			if(!Main::getFill()->isRegisteredPlayer($player)) return;
			$block = $event->getBlock();
			$name = $player->getName();
			sound($player);

			if(is_bool(Main::getFill()->data[$name]['pos1'])){
				$player->sendMessage(Main::HEADER.'§a起点をセット');
				$fill->data[$name]['pos1'] = $block;

			}else{
				$fill->data[$name]['pos2'] = $block;
				$player->sendMessage(Main::HEADER.'§aFill!');
				$fill->do($player, new Air);
			}
		}
		if(!Main::getConfigFile()->get('UseType')['Tap']){
			return;
		}
		$player = $event->getPlayer();
		($this->use_fill_item)($player, $item);
	}

	public function onDrop(PlayerDropItemEvent $event):void{
		$item = $event->getItem();
		$items = Main::getItems();

		if($item->getCustomName() === $items[0]->getCustomName() or $item->getCustomName() === $items[1]->getCustomName()){
			$event->getPlayer()->getInventory()->removeItem($item);
			$event->setCancelled();
		}
	}
}
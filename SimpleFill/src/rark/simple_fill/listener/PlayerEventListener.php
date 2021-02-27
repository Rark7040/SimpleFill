<?php

declare(strict_types = 1);

namespace rark\simple_fill\listener;

use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\event\Listener;
use pocketmine\event\player\{
	PlayerQuitEvent,
	PlayerInteractEvent,
	PlayerDropItemEvent,
	PlayerToggleSneakEvent
};
use rark\simple_fill\Main;
use rark\simple_fill\utils\Fill;
use function rark\simple_fill\utils\sound;


final class PlayerEventListener implements Listener{
	/** @var Closure  */
	private $use_fill_item;

	public function __construct(){
		$this->use_fill_item = function(Player $player, Item $item):void{
			if($item->getCustomName() !== Main::$item->getCustomName() or !$player->isOp()){
				return;
			}

			if(Main::$fill->isRegisteredPlayer($player)){
				sound($player);
				Main::$fill->unregisterPlayer($player);
				$player->sendMessage(Main::HEADER.'§bFillモードをOFFにしました');

			}else{
				sound($player);
				Main::$fill->registerPlayer($player);
				$player->sendMessage(Main::HEADER.'§aFillモードをONにしました');
			}
		};
	}

	public function onQuit(PlayerQuitEvent $event):void{
		Main::$fill->unregisterPlayer($event->getPlayer());
	}

	public function onSneak(PlayerToggleSneakEvent $event):void{
		if(!Main::$config->get('UseType')['Sneak'] or !$event->isSneaking()){
			return;
		}
		$player = $event->getPlayer();
		$item = $player->getInventory()->getItemInHand();
		($this->use_fill_item)($player, $item);
	}

	public function onInteract(PlayerInteractEvent $event):void{
		if(!Main::$config->get('UseType')['Tap']){
			return;
		}
		$player = $event->getPlayer();
		$item = $event->getItem();
		($this->use_fill_item)($player, $item);
	}

	public function onDrop(PlayerDropItemEvent $event):void{
		$item = $event->getItem();

		if($item->getCustomName() === Main::$item->getCustomName()){
			$event->getPlayer()->getInventory()->removeItem($item);
			$event->setCancelled();
		}
	}
}
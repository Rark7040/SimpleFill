<?php

declare(strict_types = 1);

namespace rark\simple_fill\listener;

use pocketmine\{
	event\Listener,
	event\block\BlockBreakEvent,
	event\block\BlockPlaceEvent,
	scheduler\ClosureTask,
};
use rark\simple_fill\{
	Main,
};
use function rark\simple_fill\utils\sound;

final class BlockEventListener implements Listener{

	public function onBreak(BlockBreakEvent $event):void{
        $items = Main::getItems();

		if($event->getItem()->getCustomName() === $items[0]->getCustomName()){
			$event->cancel();
		}
	}

	public function onPlace(BlockPlaceEvent $event):void{
		$player = $event->getPlayer();

		if(!Main::getFill()->isRegisteredPlayer($player)) return;
		$before = $event->getBlockReplaced();
		$name = $player->getName();
		sound($player->getPosition());

		if(is_bool(Main::getFill()->data[$name]['pos1'])){
			$player->sendMessage(Main::HEADER.'§a起点をセット');
			Main::getFill()->data[$name]['pos1'] = $before;

		}else{
			$v = $event->getBlock();
			Main::getFill()->data[$name]['pos2'] = $before;
			$player->sendMessage(Main::HEADER.'§aFill!');
			Main::getSchedulerInstance()->scheduleDelayedTask(
				new ClosureTask(
					function() use($player, $v):void{
						$block = $player->getPosition()->getWorld()->getBlock($v->getPosition());
						Main::getFill()->do($player, $block);
					}
				),
                1
			);
		}
	}
}
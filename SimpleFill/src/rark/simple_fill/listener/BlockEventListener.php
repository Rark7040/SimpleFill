<?php

declare(strict_types = 1);

namespace rark\simple_fill\listener;

use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\block\Block;
use pocketmine\event\block\{
	BlockBreakEvent,
	BlockPlaceEvent
};
use pocketmine\scheduler\Task;
use pocketmine\math\Vector3;
use rark\simple_fill\Main;
use rark\simple_fill\utils\Fill;
use function rark\simple_fill\utils\sound;


final class BlockEventListener implements Listener{

	public function onBreak(BlockBreakEvent $event):void{

		if($event->getItem()->getCustomName() === Main::$item->getCustomName()){
			$event->setCancelled();
		}
	}

	public function onPlace(BlockPlaceEvent $event):void{
		$player = $event->getPlayer();

		if(!Main::$fill->isRegisteredPlayer($player)){
			return;
		}

		$before = $event->getBlockReplaced();
		$name = $player->getName();
		sound($player);

		if(is_bool(Main::$fill->data[$name]['pos1'])){
			$player->sendMessage(Main::HEADER.'§a起点をセット');
			Main::$fill->data[$name]['pos1'] = $before;

		}else{
			Main::$fill->data[$name]['pos2'] = $before;
			$player->sendMessage(Main::HEADER.'§aFill!');
			Main::$instance->getScheduler()->scheduleDelayedTask(new Class($player, $event->getBlock()) extends Task{
					/** @var Player */
					private $player;
					/** @var Vector3 */
					private $v;

					public function __construct(Player $player, Vector3 $v){
						$this->player = $player;
						$this->v = $v;
					}

					public function onRun(int $tick){
						$block = $this->player->getLevel()->getBlock($this->v);
						Main::$fill->do($this->player, $block);
					}
				},
				1
			);
		}
	}
}
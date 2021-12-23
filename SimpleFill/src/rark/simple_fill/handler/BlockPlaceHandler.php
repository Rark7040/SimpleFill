<?php
declare(strict_types = 1);

namespace rark\simple_fill\handler;

use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Event;
use pocketmine\scheduler\ClosureTask;
use rark\simple_fill\Loader;
use rark\simple_fill\obj\ContainerPool;
use rark\simple_fill\obj\FillStatusWrapper;
use rark\simple_fill\obj\Logger;

class BlockPlaceHandler implements BaseHandler{
	public static function getTarget():string{
		return BlockPlaceEvent::class;
	}

	public static function handleEvent(Event $ev):void{
		if(!$ev instanceof BlockPlaceEvent) return;
		$player = $ev->getPlayer();
		$block = $ev->getBlock();
		
		if(!FillStatusWrapper::isFillMode($player)) return;
		$v = $block->getPosition()->asVector3();
		$world = $player->getPosition()->getWorld();
		$pre_container = ContainerPool::getPreContainerNonNull($player);
		$pre_container->push($v);

		if(!$pre_container->isComplete()){
			return;
		}
		Loader::getTaskScheduler()->scheduleDelayedTask(
			new ClosureTask(
				function() use($player, $pre_container, $v, $world):void{
					$block = $world->getBlock($v);
					$container = $pre_container->parse();

					if($container === null){
						return;
					}
					$blocks = clone $container;
					$blocks->loadBlocks($player->getPosition()->getWorld());
					Logger::push($player, $blocks);

					$container->fill($block, $player->getPosition()->getWorld());
					$container->place($player);
				}
			),
			1
		);
	}
}
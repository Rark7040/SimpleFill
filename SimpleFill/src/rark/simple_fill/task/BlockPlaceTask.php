<?php
declare(strict_types = 1);

namespace rark\simple_fill\task;

use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use rark\simple_fill\effect\Messages;
use rark\simple_fill\effect\Sounds;
use rark\simple_fill\obj\Container;
use rark\simple_fill\obj\Logger;

class BlockPlaceTask extends Task{
	const PLACE_AMOUNT = 30;
	const PLACE_SPEED = 4;
	protected ?Player $player;
	/** @var Block[] */
	protected ?array $blocks;
	protected ?Container $backup = null;

	public function __construct(Container $container, ?Player $player){
		$this->blocks = $container->getBlocks();
		$this->player = $player;
		$key = array_key_first($this->block);

		if($key === null or !isset($this->blocks[$key])) return;
		$backup = clone $container;
		$backup->loadBlocks($this->blocks[$key]->getPosition()->getWorld());
		$this->backup = $backup;
		Logger::push($player, $backup);

		if($this->player === null) return;
		Messages::sendMessage($player, Messages::START_FILL);
	}

	public function onRun():void{
		for($i = self::PLACE_AMOUNT; $i > 0; --$i){
			if(count($this->blocks) < 1){
				$this->stop();
				break;
			}
			$block = array_shift($this->blocks);
			$p = $block->getPosition();
			$p->getWorld()->setBlock($p->asVector3(), $block);
		}

		if($this->player === null) return;
		if($block === null) return;
		$block instanceof Air?
			Sounds::blockBreakSound($this->player):
			Sounds::blockPlaceSound($this->player, $block);
		
	}

	public function stop():void{
		$this->player = null;
		$this->blocks = null;
		$this->getHandler()->cancel();
	}

	public function rollback():void{
		$this->backup->forcePlace();
	}
}
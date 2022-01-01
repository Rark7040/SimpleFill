<?php
declare(strict_types = 1);

namespace rark\simple_fill\task;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use rark\simple_fill\effect\Messages;
use rark\simple_fill\effect\Sounds;
use rark\simple_fill\obj\Container;
use rark\simple_fill\obj\Logger;

class BlockPlaceTask extends Task{
	const PLACE_AMOUNT = 100;
	const PLACE_SPEED = 4;
	protected ?Player $player;
	/** @var Block[] */
	protected ?array $blocks = [];
	protected ?Container $backup = null;
	protected bool $is_killed = false;

	public function __construct(Container $container, ?Player $player){
		$this->blocks = $container->getBlocks();
		$this->player = $player;
		$key = array_key_first($this->blocks);

		if($key === null or !isset($this->blocks[$key])) return;
		$backup = clone $container;

		try{
			$backup->loadBlocks($this->blocks[$key]->getPosition()->getWorld());

		}catch(\Exception){
			$this->is_killed = true;

			if($this->player === null) return;
			Messages::sendMessage($player, Messages::ERR_SIZE);
			return;
		}
		$this->backup = $backup;

		if($this->player === null) return;
		Logger::push($player, $backup);
		Messages::sendMessage($player, Messages::START_FILL);
	}

	public function onRun():void{
		if($this->is_killed){
			$this->stop();
			return;
		}
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
		Sounds::blockPlaceSound($this->player, $block->isSameState(VanillaBlocks::AIR())? VanillaBlocks::OAK_WOOD(): $block);
	}

	public function stop():void{
		$this->is_killed = true;
		$this->player = null;
		$this->blocks = null;
		$this->getHandler()?->cancel();
	}

	public function rollback():void{
		$this->backup->forcePlace();
	}
}
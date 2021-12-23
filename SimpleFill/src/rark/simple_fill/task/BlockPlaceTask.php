<?php
declare(strict_types = 1);

namespace rark\simple_fill\task;

use pocketmine\block\Block;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use rark\simple_fill\obj\Container;

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
		$this->backup =$backup;
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
		try{
			$session = $this->player->getNetWorkSession();

		}catch(\Exception){
			$this->player = null;
			return;
		}

		if($block === null) return;
		$session->sendDataPacket(
			LevelSoundEventPacket::nonActorSound(
				LevelSoundEvent::PLACE,
				$this->player->getPosition(),
				false,
				RuntimeBlockMapping::getInstance()->toRuntimeId($block->getFullId())
			)
		);
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
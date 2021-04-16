<?php

declare(strict_types = 1);

namespace rark\simple_fill\utils;

use pocketmine\{
	Player,
	block\Block,
	level\Level,
	math\Vector3
};
use rark\simple_fill\{
	Main,
	form\WarningForm
};

final class Fill{

	/** @var int*/
	private const ERROR_EXEPTION_BROKEN_DATA = 0;
	private const ERROR_HAS_NOT_PERMISSION = 1;
	private const ERROR_LEVEL_CLOSED = 2;
	private const ERROR_POS1_NOT_FOUND = 3;
	private const ERROR_POS2_NOT_FOUND = 4;
	private const NO_ERROR = 5;

	/** @var mixed[] */
	public array $data = [];

	public function registerPlayer(Player $player){
		$this->data[$player->getName()] = [
			'pos1' => true,
			'pos2' => true
		];
	}

	public function isRegisteredPlayer(Player $player):bool{
		return isset($this->data[$player->getName()]);
	}

	public function unregisterPlayer(Player $player):void{
		if(!$this->isRegisteredPlayer($player)){
			return;
		}
		unset($this->data[$player->getName()]);
	}

	private function canDo(Player $player):int{
		$name = $player->getName();

		switch(false){
			case $this->isRegisteredPlayer($player):
				return self::ERROR_EXEPTION_BROKEN_DATA;
			break;

			case $player->isOP():
				return self::ERROR_HAS_NOT_PERMISSION;
			break;

			case $this->data[$name]['pos1'] instanceof Vector3:
				return self::ERROR_POS1_NOT_FOUND;
			break;

			case $this->data[$name]['pos2'] instanceof Vector3:
				return self::ERROR_POS2_NOT_FOUND;
			break;

			case $player->isValid():
				return self::ERROR_LEVEL_CLOSED;
			break;

			default:
				return self::NO_ERROR;
		}
	}

	public function do(Player $player, Block $block):void{
		$messages = [
			'§cFillのリクエストがキャンセルされました',
			'§cFillを実行する権限がありません',
			'§c何らかの原因でPos1のデータが破損したためFillを中止します',
			'§c何らかの原因でPos2のデータが破損したためFillを中止します',
			'§cワールドが存在しません'
		];
		$case = $this->canDo($player);

		if($case === self::NO_ERROR){
			$this->fill($player, $block);
			return;
		}
		$player->sendMessage(Main::HEADER.$messages[$case]);
	}

	private function fill(Player $player, Block $block):void{
		$this->getLevelData($player, $blocks, $tiles);

		if(count($blocks) >= 1000){
			$player->sendForm(new WarningForm($blocks, $tiles, $block));
			return;
		}
		$this->setBlocks($player, $blocks, $tiles, $block);
	}

	public function getLevelData(Player $player, &$blocks, &$tiles):void{
		$level = $player->getLevel();
		$pos1 = $this->data[$player->getName()]['pos1'];
		$pos2 = $this->data[$player->getName()]['pos2'];
		$pos1_clone = clone $pos1;
		$pos2_clone = clone $pos2;
		sortPos($pos1, $pos2);
		$blocks = [];
		$tiles = [];

		for($x = $pos2->x - $pos1->x; $x >= 0; --$x){
			for($y = $pos2->y - $pos1->y; $y >= 0; --$y){
				for($z = $pos2->z - $pos1->z; $z >= 0; --$z){
					$blocks[] = $level->getBlock($pos1->add($x, $y, $z));
				}
			}
		}
		$blocks[] = $pos1_clone;
		$blocks[] = $pos2_clone;

		foreach($level->getTiles() as $tile){
			if(in_region($tile, $pos1, $pos2)){
				$tiles[] = $tile;
				unset($tile);
			}
		}
	}

	public function setBlocks(Player $player, array $positions, array $tiles, Block $block):void{
		$level = $player->getLevel();

		foreach($positions as $pos){
			$level->setBlock($pos, $block);
		}
		Main::getUndo()->reportFillData($player, $positions, $tiles);
		$this->registerPlayer($player);
	}
}
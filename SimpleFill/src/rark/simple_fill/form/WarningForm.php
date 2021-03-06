<?php

declare(strict_types = 1);

namespace rark\simple_fill\form;

use pocketmine\form\Form;
use pocketmine\Player;
use pocketmine\block\Block;
use pocketmine\level\Position;
use rark\simple_fill\Main;


final class WarningForm implements Form{
	/** @var pocketmine\block\Block[] */
	private array $blocks;
	/** @var pocketmine\tile\Tile[] */
	private $tiles;
	/** @var pocketmine\block\Block */
	private $block;

	public function __construct(array $blocks, array $tiles, Block $block){
		$this->blocks = $blocks;
		$this->tiles = $tiles;
		$this->block = $block;
	}

	public function jsonSerialize(){
		return [
			'type' => 'modal',
			'title' => '§c警告',
			'content' => '現在実行しようとしているFillのブロック数は'.count($this->blocks).'です。'."\n".'Fillを続行しますか？',
			'button1' => '続行',
			'button2' => '中止'
		];
	}

	public function handleResponse(Player $player, $data):void{
		if($data){
			Main::getFill()->setBlocks($player, $this->blocks, $this->tiles, $this->block);

		}else{
			$player->sendMessage(Main::HEADER.'§c処理を中断しました');
			Main::getFill()->registerPlayer($player);
		}
	}
}
<?php

declare(strict_types = 1);

namespace rark\simple_fill\utils;

use pocketmine\Player;
use rark\simple_fill\Main;


final class Undo{
	/** @var array */
	public $data = [];

	public function reportFillData(Player $player, array $blocks, array $tiles){
		$name = $player->getName();
		$tiles_data = [];

		foreach($tiles as $tile){
			$tiles_data[] = [get_class($tile), $tile->saveNBT()];
			unset($tile);
		}
		$this->data[$name][] = [$player->getLevel(), $blocks, $tiles_data];

		if(count($this->data[$name]) > Main::$config->get('SaveQueue')){
			$this->data = array_slice($this->data, 1);
		}
	}

	public function undo(Player $player, int $value):void{
		$name = $player->getName();

		if(!isset($this->data[$name][0])){
			$player->sendMessage(Main::HEADER.'§c履歴がありません');
			return;
		}
		$log = array_reverse($this->data[$name]);

		/**
		 * @var Level $data[0]
		 * @var array $data[1]
		 * @var array $data[2]
		*/
		for($amount = 0; $value !== 0; --$value){
			++$amount;
			$data = $log[0];

			foreach($data[1] as $block){
				$data[0]->setBlock($block, clone $block);
			}

			foreach($data[2] as $tile_data){
				new $tile_data[0]($data[0], $tile_data[1]);
			}

			$this->data[$name] = array_slice($this->data[$name], 0, -1);

			if(!isset($this->data[$name][0])){
				unset($this->data[$name]);
				break;
			}
		}
		$player->sendMessage(Main::HEADER.'§a'.$amount.'回分操作を取り消しました');
	}
}
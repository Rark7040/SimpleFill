<?php

declare(strict_types = 1);

namespace rark\simple_fill\utils;

use pocketmine\player\Player;
use rark\simple_fill\Main;


final class Undo{
	/** @var mixed[] */
	public array $data = [];

	public function reportFillData(Player $player, array $blocks, array $tiles){
		$name = $player->getName();
		$tiles_data = [];

		foreach($tiles as $tile){
			$tiles_data[] = [get_class($tile), $tile->saveNBT()];
		}
        $log = $this->data[$name]??[];
		array_unshift($log, [$player->getPosition()->getWorld(), $blocks, $tiles_data]);
		$this->data[$name] = $log;

		if(count($this->data[$name]) > Main::getConfigFile()->get('Undo')){
			unset($this->data[Main::getConfigFile()->get('Undo')+1]);
		}
	}

	public function undo(Player $player, int $value):void{
		$name = $player->getName();

		if(!isset($this->data[$name][0])){
			$player->sendMessage(Main::HEADER.'§c履歴がありません');
			return;
		}

		/**
		 * @var Level $data[0]
		 * @var array $data[1]
		 * @var array $data[2]
		*/
		for($amount = 0; $value !== 0; --$value){
			++$amount;
			$data = current($this->data[$name]);

			foreach($data[1] as $block){
				$data[0]->setBlock($block, clone $block);
			}

			foreach($data[2] as $tile_data){
				new $tile_data[0]($data[0], $tile_data[1]);
			}
			array_shift($this->data[$name]);

			if(!isset($this->data[$name][0])){
				unset($this->data[$name]);
				break;
			}
		}
		$player->sendMessage(Main::HEADER.'§a'.$amount.'回分操作を取り消しました');
	}
}
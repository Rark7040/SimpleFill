<?php
declare(strict_types = 1);

namespace rark\simple_fill\libs\form;

use pocketmine\player\Player;

class SimpleForm extends BaseForm{

	public function addButton(string $text, ?string $image = null){
		$button = ['text' => $text];

		if($image !== null){
			$button['image'] = [
				'type' => 'path',
				'data' => $image
			];
		}
		$this->contents[] = $button;
	}

	final public function handleResponse(Player $player, $data):void{
		if(!is_int($data)){
			$this->receiveIllegalData($player, $data);
			return;
		}
		$this->onSubmit($player, $data);
	}

	final public function jsonSerialize(){
		$json = [
			'type' => self::SIMPLE,
			'title' => $this->title
		];
		$json['content'] = $this->label;
		$json['buttons'] = $this->contents;
		return $json;
	}
}
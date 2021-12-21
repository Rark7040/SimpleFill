<?php
declare(strict_types = 1);

namespace rark\simple_fill\libs\form;

use pocketmine\player\Player;

class ModalForm extends BaseForm{

	public function __construct(string $text1, string $text2){
		$this->contents[] = $text1;
		$this->contents[] = $text2;
	}

	final public function handleResponse(Player $player, $data):void{
		if(!is_bool($data)){
			$this->receiveIllegalData($player, $data);
			return;
		}
		$this->onSubmit($player, $data);
	}

	final public function jsonSerialize(){
		$json = [
			'type' => self::MODAL,
			'title' => $this->title
		];
		$json['content'] = $this->label;
		$json['button1'] = $this->contents[0];
		$json['button2'] = $this->contents[1];
		return $json;
	}
}
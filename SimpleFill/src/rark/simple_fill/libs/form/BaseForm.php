<?php
declare(strict_types = 1);

namespace rark\simple_fill\libs\form;

use pocketmine\form\Form;
use pocketmine\player\Player;

abstract class BaseForm implements Form{
	public const SIMPLE = 'form';
	public const MODAL = 'modal';
	public const CUSTOM = 'custom_form';

	protected string $type;
	protected array $contents = [];
	public string $title = '';
	public string $label = '';
	/** @var ?callable */
	public $submit = null;
	/** @var ?callable */
	public $cancelled = null;
	/** @var ?callable */
	public $illegal = null;

	protected function receiveIllegalData(Player $player):void{
		if(!is_callable($this->illegal)) return;
		($this->illegal)($player);
	}

	protected function onCancelled(Player $player):void{
		if(!is_callable($this->cancelled)) return;
		($this->cancelled)($player);
	}

	protected function onSubmit(Player $player, $data):void{
		if($data === null){
			$this->onCancelled($player);
			return;
		}
		if(!is_callable($this->submit)) return;
		($this->submit)($player, $data);
	}
}
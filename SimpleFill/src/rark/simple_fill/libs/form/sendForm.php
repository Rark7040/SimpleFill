<?php
declare(strict_types = 1);

namespace rark\simple_fill\libs\form;

use pocketmine\form\Form;
use pocketmine\player\Player;

class sendForm{/** NOOP */}

function sendForm(Player $player, Form $form):bool{
	$ref_prop = new \ReflectionProperty(get_class($form), 'forms');
	$ref_prop->setAccessible(true);

	if(count($ref_prop->getValue($form)) > 1) return false;
	$player->sendForm($form);
	return true;
}
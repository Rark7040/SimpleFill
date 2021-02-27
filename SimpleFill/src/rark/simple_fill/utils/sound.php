<?php

declare(strict_types = 1);

namespace rark\simple_fill\utils;

use pocketmine\level\Position;


function sound(Position $pos):void{
	$level = $pos->getLevel();

	if(is_null($level)){
		return;
	}
	$level->broadcastLevelSoundEvent($pos, 81);
}
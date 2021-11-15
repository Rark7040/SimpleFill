<?php

declare(strict_types = 1);

namespace rark\simple_fill\utils;

use pocketmine\world\Position;
use pocketmine\world\sound\NoteInstrument;
use pocketmine\world\sound\NoteSound;

function sound(Position $pos):void{
	$pos->getWorld()->addSound($pos, new NoteSound(NoteInstrument::PIANO(), 10));
}
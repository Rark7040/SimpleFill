<?php

declare(strict_types = 1);

namespace rark\simple_fill\utils;

use pocketmine\math\Vector3;


function sortPos(Vector3 &$pos1, Vector3 &$pos2):void{
	$x = [$pos1->x, $pos2->x];
	$y = [$pos1->y, $pos2->y];
	$z = [$pos1->z, $pos2->z];
	$pos1->x = min($x);
	$pos1->y = min($y);
	$pos1->z = min($z);
	$pos2->x = max($x);
	$pos2->y = max($y);
	$pos2->z = max($z);
}

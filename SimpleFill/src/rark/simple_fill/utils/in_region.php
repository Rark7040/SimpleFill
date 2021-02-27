<?php

declare(strict_types = 1);

namespace rark\simple_fill\utils;

use pocketmine\math\Vector3;

/**
 * sortPos関数を使用した状態でないと挙動が正確ではなくなります
 */
function in_region(Vector3 $v, Vector3 $pos1, Vector3 $pos2):bool{
	if($v->x >= $pos1->x and $v->x <= $pos2->x){
		if($v->y >= $pos1->y and $v->y <= $pos2->y){
			return $v->z >= $pos1->z and $v->z <= $pos2->z;
		}
	}
	return false;
}
<?php
declare(strict_types = 1);

namespace rark\simple_fill\txt;

use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

abstract class Messages{
	final public function __construct(){/** NOOP */}
	const SET_POS1 = TextFormat::YELLOW.'基点を設定しました。';
	const SET_POS2 = TextFormat::YELLOW.'終点を設定しました。';
	const START_FILL = TextFormat::GREEN.'fillを開始します。';

	public static function getUndoMessage(int $count):string{
		return TextFormat::GREEN.$count.'回分の操作を取り消します。';
	}

	public static function sendMessage(Player $player, string $txt):bool{
		try{
			$session = $player->getNetworkSession();

		}catch(\Exception){
			return false;
		}
		$session->sendDataPacket(TextPacket::raw($txt));
		return true;
	}
}
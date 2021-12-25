<?php
declare(strict_types = 1);

namespace rark\simple_fill\effect;

use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

abstract class Messages{
	final public function __construct(){/** NOOP */}
	const PREFIX = TextFormat::BOLD.TextFormat::WHITE.'[SIMPLE FILL] '.TextFormat::RESET;
	const SET_POS1 = TextFormat::YELLOW.'基点を設定しました。';
	const SET_POS2 = TextFormat::YELLOW.'終点を設定しました。';
	const TURN_ON = TextFormat::GREEN.'Fill Modeを有効化しました。';
	const TURN_OFF = TextFormat::RED.'Fill Modeを無効化しました。';
	const ADDED_ITEMS = TextFormat::YELLOW.'アイテムを付与しました';
	const START_FILL = TextFormat::GREEN.'fillを開始します。';
	const UNDO = '回分の操作を取り消します。';
	const PLZ_EXEC_IN_GAME = TextFormat::RED.'ゲーム内で実行してください';
	const ERR_CONTAINER_IS_NULL = TextFormat::RED.'Containerを取得できませんでした';

	public static function getUndoMessage(int $count):string{
		return TextFormat::GREEN.$count.self::UNDO;
	}

	public static function sendMessage(Player $player, string $txt):bool{
		try{
			$session = $player->getNetworkSession();

		}catch(\Exception){
			return false;
		}
		$session->sendDataPacket(TextPacket::raw(self::PREFIX.$txt));
		return true;
	}
}
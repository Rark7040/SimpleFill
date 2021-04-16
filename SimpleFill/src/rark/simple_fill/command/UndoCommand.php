<?php

declare(strict_types = 1);

namespace rark\simple_fill\command;

use pocketmine\command\{PluginCommand, CommandSender};
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use rark\simple_fill\Main;
use function rark\simple_fill\utils\sound;


final class UndoCommand extends PluginCommand{

	public function __construct(Plugin $plugin){
		parent::__construct('sfundo', $plugin);
		$this->setAliases(['su']);
		$this->setDescription(Main::HEADER.'操作を取り消す');
	}

	public function execute(CommandSender $sender, string $command, array $args){
		if(!$sender instanceof Player or !$sender->isOp()){
			$sender->sendMessage(Main::HEADER.'§c実行権限がありません');
			return;
		}
		$value = 1;

		if(isset($args[0])){
			$value = intval($args[0]);

			if($value <= 0){
				$sender->sendMessage(Main::HEADER.'§c不正な値です');
				return;
			}
		}
		sound($sender);
		Main::getUndo()->undo($sender, $value);
	}
}
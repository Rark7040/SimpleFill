<?php

declare(strict_types = 1);

namespace rark\simple_fill\command;

use pocketmine\command\{Command, PluginCommand, CommandSender};
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use rark\simple_fill\Main;
use function rark\simple_fill\utils\sound;


final class UndoCommand extends Command{

	public function __construct(){
		parent::__construct('sfundo');
		$this->setAliases(['su']);
		$this->setDescription(Main::HEADER.'操作を取り消す');
	}

	public function execute(CommandSender $sender, string $command, array $args){
		if(!$sender instanceof Player or !Server::getInstance()->isOp($sender->getName())){
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
		sound($sender->getPosition());
		Main::getUndo()->undo($sender, $value);
	}
}
<?php

declare(strict_types = 1);

namespace rark\simple_fill\command;

use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\command\{CommandSender, PluginCommand};
use rark\simple_fill\Main;
use function rark\simple_fill\utils\sound;


final class FillCommand extends PluginCommand{

	public function __construct(Plugin $plugin){
		parent::__construct('simplefill', $plugin);
		$this->setAliases(['sf']);
		$this->setDescription(Main::HEADER.'Fillモードを切り替えるアイテムを付与');
	}

	public function execute(CommandSender $sender, string $command, array $args){
		if(!$sender instanceof Player or !$sender->isOp()){
			$sender->sendMessage(Main::HEADER.'§c実行権限がありません');
			return;
		}
		$inventory = $sender->getInventory();

		if($inventory->canAddItem(Main::$item)){
			sound($sender);
			$inventory->addItem(Main::$item);
		}
	}
}
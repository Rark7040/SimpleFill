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
		$this->setUsage('simplefill ?<on|off>');
	}

	public function execute(CommandSender $sender, string $command, array $args){
		if(!$sender instanceof Player or !$sender->isOp()){
			$sender->sendMessage(Main::HEADER.'§c実行権限がありません');
			return;
		}
		if(count($args) === 1){
			$is_on = $args[0] === 'on';
			$is_off = $args[0] === 'off';

			if($is_on or $is_off){
				$is_on?
					Main::getFill()->registerPlayer($sender):
					Main::getFill()->unregisterPlayer($sender);
				$sender->sendMessage(
                	$is_on?
						Main::HEADER.'§aFillモードをONにしました':
                  		Main::HEADER.'§bFillモードをOFFにしました'
                );
                sound($sender);
                return;
            }
		}
		$inventory = $sender->getInventory();
		$items = Main::getItems();

		if($inventory->canAddItem($items[0]) and $inventory->canAddItem($items[1])){
			sound($sender);
			$inventory->addItem($items[0], $items[1]);
		}
	}
}
<?php
declare(strict_types = 1);

namespace rark\simple_fill\command;

use rark\simple_fill\libs\cortexpe\commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use rark\simple_fill\libs\cortexpe\commando\args\IntegerArgument;

class SimpleUndoCommand extends BaseCommand{

	public function __construct(PluginBase $owner){
		parent::__construct(
			$owner,
			'simpleundo',
			'Simple Undo',
			['su']
		);
	}

	protected function prepare():void{
		$this->setPermission('simple_fill.command.op');
		$this->registerArgument(0, new IntegerArgument('count', false));
	}

	public function onRun(CommandSender $sender, string $command, array $args):void{
		if(!$sender instanceof Player){
			$sender->sendMessage('§cゲーム内で実行してください');
			return;
		}
	}
}
<?php
declare(strict_types = 1);

namespace rark\simple_fill\command;

use rark\simple_fill\libs\cortexpe\commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use rark\simple_fill\effect\Messages;
use rark\simple_fill\libs\cortexpe\commando\args\BooleanArgument;

class SimpleFillCommand extends BaseCommand{
	protected const PERMISSION = 'simple_fill.command.op';
	protected const COMMAND_NAME = 'simple_fill';
	protected const DESCRIPTION = 'Simple Fill';
	protected const ALIAS = 'sf';

	public function __construct(PluginBase $owner){
		parent::__construct(
			$owner,
			self::COMMAND_NAME,
			self::DESCRIPTION
			[self::ALIAS]
		);
	}

	const ARG_MODE = 'arg.mode';
	protected function prepare():void{
		$this->setPermission(self::PERMISSION);
		$this->registerArgument(0, new BooleanArgument(self::ARG_MODE, true));
	}

	public function onRun(CommandSender $sender, string $command, array $args):void{
		if(!$sender instanceof Player){
			
			$sender->sendMessage(Messages::PLZ_EXEC_IN_GAME);
			return;
		}

	}
}
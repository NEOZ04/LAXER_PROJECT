<?php 

namespace laxer\cmds;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use laxer\Core;
use laxer\ui\CoreUI;

class SpawnCommand extends Command {
    
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $level = Core::getInstance()->getServer()->getDefaultLevel();
            $sender->teleport($level->getSafeSpawn());
            $sender->sendMessage(CoreUI::Notice('Welcome back to spawn'));
        }else {
            Core::getInstance()->getLogger()->critical('Use this command in game.');
        }
        return true;
    }
    
}
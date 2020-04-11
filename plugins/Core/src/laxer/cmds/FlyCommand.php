<?php 

namespace laxer\cmds;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use laxer\Core;
use laxer\ui\CoreUI;

class FlyCommand extends Command {
    
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $sender->setFlying(true);
            $level = Core::getInstance()->getServer()->getDefaultLevel();
            if ($sender->hasPermission('laxer.fly')){
                if (!($sender->level->getId() == $level->getId()) || $sender->isOp()){
                    if ($sender->isFlying()){
                        $sender->setAllowFlight(true);
                        $sender->setFlying(true);
                        $sender->sendMessage(CoreUI::Success('Mode terbang telah dihidupkan'));
                    }else {
                        $sender->setFlying(false);
                        $sender->setAllowFlight(false);
                        $sender->sendMessage(CoreUI::Success('Mode terbang telah dimatikan'));
                    }
                }else {
                    $sender->sendMessage(CoreUI::Danger('Anda tidak bisa terbang di spawn world'));
                }
            }else {
                $sender->sendMessage(CoreUI::Danger('Anda tidak memiliki izin untuk terbang'));
            }
        }else {
            Core::getInstance()->getLogger()->critical('Use this command in game.');
        }
        return true;
    }
    
}
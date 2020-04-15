<?php 

namespace laxer\cmds;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use laxer\Core;
use laxer\ui\CoreUI;

class FlyCommand extends Command {
    
    public function __construct(String $name = '', String $description = '', String $usage = '', Array $alias = []){
        parent::__construct($name, $description, $usage, $alias);
        $this->setPermission('laxer.fly');
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            if (!$sender->hasPermission('laxer.fly')){
                $sender->sendMessage(CoreUI::Danger('Anda tidak memiliki izin untuk menggunakan perintah ini'));
                return true;
            }
            $level = Core::getInstance()->getServer()->getDefaultLevel();
            if ($sender->hasPermission('laxer.fly')){
                if (!($sender->level->getId() == $level->getId()) || $sender->isOp()){
                    if (!$sender->getAllowFlight()){
                        $sender->setAllowFlight(true);
                        $sender->setFlying(true);
                        $sender->add(0,5,0);
                        $sender->sendMessage(CoreUI::Success('Mode terbang telah dihidupkan'));
                    }else {
                        $sender->setAllowFlight(false);
                        $sender->setFlying(false);
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
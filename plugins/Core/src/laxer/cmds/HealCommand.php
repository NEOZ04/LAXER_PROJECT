<?php

namespace laxer\cmds;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use laxer\Core;
use laxer\ui\CoreUI;

class HealCommand extends Command {
    
    public function __construct(String $name = '', String $description = '', String $usage = '', Array $alias = []){
        parent::__construct($name, $description, $usage, $alias);
        $this->setPermission('laxer.heal');
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            if (!$sender->hasPermission('laxer.heal')){
                $sender->sendMessage(CoreUI::Danger('Anda tidak memiliki izin untuk menggunakan perintah ini'));
                return true;
            }
            $sender->setHealth(20);
            $sender->sendMessage(CoreUI::Success('Tingkat darah berhasil di tambahkan'));
            
        }else {
            Core::getInstance()->getLogger()->critical('Use this command in game.');
        }
        return true;
    }
    
}
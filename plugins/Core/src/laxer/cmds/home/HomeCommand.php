<?php 

namespace laxer\cmds\home;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use laxer\Core;
use laxer\ui\CoreUI;
use laxer\home\Home;

class HomeCommand extends Command {
    
    public function __construct(String $name = '', String $description = '', String $usage = '', Array $alias = []){
        parent::__construct($name, $description, $usage, $alias);
        $this->setPermission('laxer.home');
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            if (!$sender->hasPermission('laxer.home')){
                $sender->sendMessage(CoreUI::Danger('Anda tidak memiliki izin untuk menggunakan perintah ini'));
                return true;
            }
            
            if (isset($args[0])){
                if ($pos = Home::getPos($sender, $args[0])){
                    $sender->teleport($pos);
                    $sender->sendMessage(CoreUI::Notice('Teleporting...'));
                }
            }else {
                $sender->sendMessage(CoreUI::Danger($this->usageMessage));
            }
            
        }else {
            Core::getInstance()->getLogger()->critical('Use this command in game.');
        }
        return true;
    }
    
}
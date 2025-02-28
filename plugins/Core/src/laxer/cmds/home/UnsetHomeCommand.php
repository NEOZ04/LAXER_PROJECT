<?php 

namespace laxer\cmds\home;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use laxer\Core;
use laxer\home\Home;
use laxer\ui\CoreUI;

class UnsetHomeCommand extends Command {
    
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
                $name = $args[0];
                if (Home::unsetHome($sender, $name)){
                    $sender->sendMessage(CoreUI::Success('Home dengan nama '.$name,' berhasil di hapus'));
                }else {
                    $sender->sendMessage(CoreUI::Danger('Home dengan nama '.$name,' tidak ditemukan'));
                }
            }else {
                $sender->sendMessage(CoreUI::Danger('Usage: /unsethome <name>'));
            }
            
        }else {
            Core::getInstance()->getLogger()->critical('Use this command in game.');
        }
        return true;
}
    
}
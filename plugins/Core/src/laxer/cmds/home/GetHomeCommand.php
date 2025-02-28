<?php 

namespace laxer\cmds\home;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use laxer\Core;
use laxer\ui\CoreUI;
use laxer\home\Home;

class GetHomeCommand extends Command {
    
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
            $homes = Home::getHomes($sender);
            if (empty($homes)){
                $sender->sendMessage(CoreUI::Danger('Tidak ada home yang tersedia'));
                return true;
            }
            $sender->sendMessage(CoreUI::Notice('Your homes:',''));
            foreach ($homes as $home){
                $name = $home[0];
                $level = $home[1]->level;
                $pos = $home[1];
                $sender->sendMessage(CoreUI::translate('&b•> &f'.ucfirst($name).': &r&7'
                    .$this->levelName($level->getId()).' '.(int)$pos->x.', '.(int)$pos->y.', '.(int)$pos->z.'.'));
            }
            
        }else {
            Core::getInstance()->getLogger()->critical('Use this command in game.');
        }
        return true;
    }
    
    private function levelName(int $id){
        switch ($id){
            case 2:
                return 'Survival World';
                break;
            case 4:
                return 'Flat World';
                break;
            case 3:
                return 'Plots World';
                break;
            case 1:
                return 'Spawn World';
                break;
        }
    }
    
}
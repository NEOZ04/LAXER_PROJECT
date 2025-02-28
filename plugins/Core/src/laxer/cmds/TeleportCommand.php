<?php 

namespace laxer\cmds;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use laxer\Core;
use laxer\ui\CoreUI;

class TeleportCommand extends Command {
    
    public function __construct(String $name = '', String $description = '', String $usage = '', Array $alias = []){
        parent::__construct($name, $description, $usage, $alias);
        $this->setPermission('laxer.teleport');
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            if (!$sender->hasPermission('laxer.teleport')){
                $sender->sendMessage(CoreUI::Danger('Anda tidak memiliki izin untuk menggunakan perintah ini'));
                return true;
            }
            if ($sender->hasPermission('laxer.teleport')){
                if (isset($args[0])){
                    $name = $args[0];
                    $p = Core::getInstance()->getServer()->getPlayer($name);
                    if (is_null($p)){
                        $p->sendMessage(CoreUI::Danger('Player tidak ditemukan'));
                        return false;
                    }
                    $atpos = false;
                    if (isset($args[1])){
                        if (strtolower($args[1]) == 'true'){
                            $atpos = true;
                        }
                    }
                    Core::$_SESSIONS[$p->getName()]['tpa'] = [
                        'time' => time(),
                        'sender' => $sender->getName(),
                        'atpos' => $atpos
                    ];
                    if (!$atpos){
                        $p->sendMessage(CoreUI::Notice($sender->getName().' Ingin teleport ke tempat anda, ketik \'&laccept\' &r&7untuk menerima atau \'&ldeny\'&r&7 untuk tidak menerima'));
                    }else {
                        $p->sendMessage(CoreUI::Notice($sender->getName().' Ingin anda teleport ke tempat '.$sender->getName().', ketik \'&laccept\' &r&7untuk menerima atau \'&ldeny\'&r&7 untuk tidak menerima'));
                    }
                    $sender->sendMessage(CoreUI::Success('Permintaan telah dikirim, mohon tunggu persetujuan'));
                }else {
                    $sender->sendMessage(CoreUI::Danger('usage: /tpa <player_name> [tp_on_you?:bool]'));
                }
            }else {
                $sender->sendMessage(CoreUI::Danger('Anda tidak memiliki izin untuk teleport'));
            }
        }else {
            Core::getInstance()->getLogger()->critical('Use this command in game.');
        }
        return true;
    }
    
}
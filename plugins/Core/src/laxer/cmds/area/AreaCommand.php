<?php

namespace laxer\cmds\area;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use laxer\Core;
use laxer\ui\CoreUI;
use pocketmine\level\Position;
use laxer\area\AreaUI;

class AreaCommand extends Command {
    
    public function __construct(String $name = '', String $description = '', String $usage = '', Array $alias = []){
        parent::__construct($name, $description, $usage, $alias);
        $this->setPermission('laxer.area');
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            if (!$sender->hasPermission('laxer.area')){
                $sender->sendMessage(CoreUI::Danger('Anda tidak memiliki izin untuk menggunakan perintah ini'));
                return true;
            }
            if (isset($args[0])){
                switch ($args[0]){
                    case 'pos1':
                        Core::$_SESSIONS[$sender->getName()]['area']['pos1'] = $sender->asPosition();
                        $sender->sendMessage(CoreUI::Success('Pos 1 berhasil di simpan'));
                        return true;
                        break;
                    case 'pos2':
                        Core::$_SESSIONS[$sender->getName()]['area']['pos2'] = $sender->asPosition();
                        $sender->sendMessage(CoreUI::Success('Pos 2 berhasil di simpan'));
                        return true;
                        break;
                    case 'set':
                        if (!isset($args[1])){
                            $sender->sendMessage(CoreUI::Danger('Usage: /area set <name>'));
                            return false;
                        }
                        $p1 = Core::$_SESSIONS[$sender->getName()]['area']['pos1'];
                        $p2 = Core::$_SESSIONS[$sender->getName()]['area']['pos2'];
                        if ($p1->level->getId() != $p2->level->getId()){
                            $sender->sendMessage(CoreUI::Danger('Level tidak sesuai'));
                            return false;
                        }
                        $result = Core::getInstance()->getAreaManager()->setArea($args[1], $p1->asPosition(), $p2->asPosition(), $p1->level);
                        if ($result === 1){
                            $sender->sendMessage(CoreUI::Danger('Nama sudah terdaftar'));
                            return false;
                        }
                        unset(Core::$_SESSIONS[$sender->getName()]['area']);
                        $sender->sendMessage(CoreUI::Success('Area '.$args[1].' berhasil disimpan'));
                        return true;
                        break;
                    case 'clear':
                        unset(Core::$_SESSIONS[$sender->getName()]['area']);
                        $sender->sendMessage(CoreUI::Success('Posisi berhasil dibersihkan'));
                        return true;
                        break;
                    case 'remove':
                        $area = Core::getInstance()->getAreaManager()->getArea($sender->asPosition());
                        $area->delete();
                        $sender->sendMessage(CoreUI::Success('Area berhasil dihapus'));
                        return true;
                        break;
                    case 'edit':
                        $area = Core::getInstance()->getAreaManager()->getArea($sender->asPosition());
                        if (is_null($area)){
                            $sender->sendMessage(CoreUI::Danger('Tidak dalam area'));
                            return false;
                        }
                        $ui = new AreaUI($area);
                        $ui->getMainForm($sender);
                        return true;
                        break;
                }
            }
            $sender->sendMessage(CoreUI::Danger('Usage: /area <pos1|pos2|set|clear|remove|edit>'));
            
        }else {
            Core::getInstance()->getLogger()->critical('Use this command in game.');
        }
        return true;
    }
    
}
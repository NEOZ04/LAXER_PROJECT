<?php

namespace laxer\pvp;

use pocketmine\Player;
use laxer\ui\CoreUI;
use laxer\Core;

class PvPUI {
    
    public function getMainForm(Player $p){
        $ui = CoreUI::Simple(function (Player $p, $data){
           if (is_null($data)) return false;
           Core::getInstance()->getPvP()->Match($p);
        });
        $ui->setTitle(CoreUI::fTitle('PvP'));
        $ui->addButton(CoreUI::fButton('Match'));
        $ui->sendToPlayer($p);
    }
    
}
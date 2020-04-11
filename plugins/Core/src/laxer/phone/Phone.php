<?php

namespace laxer\phone;

use pocketmine\Player;
use laxer\Core;

class Phone {
    
    private $player;
    private $uis = [];
    
    public function __construct(Player $p){
        $this->player = $p;
        Core::getInstance()->getLogger()->notice('Giving Phone: '. $p->getName());
    }
    
    public function open($ui){
        $this->uis[] = $ui;
        $ui->sendToPlayer($this->player);
    }
    
    public function back(){
        $uis = $this->uis;
        unset($uis[count($uis)-1]);
        $this->uis = $uis;
        if (!empty($this->uis)){
            $ui = end($this->uis);
            $ui->sendToPlayer($this->player);
        }
    }
    
}
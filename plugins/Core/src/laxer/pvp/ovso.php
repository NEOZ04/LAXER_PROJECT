<?php 

namespace laxer\pvp;

use pocketmine\Player;

class ovso {
    
    public function Match(Player $p){
        if (!$this->arena->isFull()){
            $this->arena->Join($p);
            return true;
        }
        $this->arena->start();
    }
    
}
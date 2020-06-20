<?php 

namespace laxer\pvp;

use pocketmine\scheduler\Task;

class PvPTask extends Task {
    
    private $arena;
    
    public function __construct(Arena $arena){
        $this->arena = $arena;
    }
    
    public function onRun(int $currentTick){
        
    }
    
}
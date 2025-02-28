<?php 

namespace laxer\player;

use pocketmine\scheduler\Task;
use pocketmine\Player;
use laxer\Core;
use laxer\ui\CoreUI;

class PlayerTimeTask extends Task {
    
    private $p;
    
    public function __construct(Player $p){
        $this->p = $p;
    }
    
    public function onRun(int $currentTick){
        $pd = new PlayerData($this->p->getName());
        $time = (int) $pd->get('join_time');
        
        // Time Rank
        $allTime = TimeRank::getAll();
        $irank = Core::getInstance()->userRank()->getRank($this->p->getName());
        $cR = '';
        foreach ($allTime as $rtime => $rank){
            if ($time == $rtime){
                if ($irank->getName() !== $rank){
                    $cR = $rank;
                }
            }
        }
        if ($cR !== ''){
            Core::getInstance()->userRank()->setRank($this->p->getName(), $cR);
            $this->p->sendMessage(CoreUI::Success('Selamar, Rank anda sekarang adalah '.$cR));
        }
        
        // End Time Rank
            
        $time++;
        $pd->set('join_time', $time);
    }
   
}
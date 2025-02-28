<?php 

namespace laxer\pvp;

use pocketmine\Player;

class PvP {
    
    private $arenaManager = null;
    private $arenas = [];
    
    public function __construct(){
        $this->init();
    }
    
    public function match(Player $p){
        $arena = $this->randomArena();
        $this->join($p, $arena->getName(), true);
    }
    
    private function join(Player $p, String $arena_name){
        if (isset($this->arenas[$arena_name])){
            $arena = $this->arenas[$arena_name];
            if ($arena->getPhase() <= 1){
                return $arena->join($p);
            }
        }
    }
    
    private function init(){
        $this->arenaManager = new ArenaManager();
        $arenas = [];
        foreach ($this->arenaManager->getAllArena() as $arena){
            $arenas[$arena->getName()] = $arena;
        }
        $this->arenas = $arenas;
    }
    
    private function getArenaManager(){
        return $this->arenaManager;
    }
    
    private  function randomArena($ovo = false){
        $ovos = [];
        $arenas = [];
        foreach ($this->arenas as $arena){
            if ($arena->getPhase() <= 1){
                if ($arena->getSlots() == 2){
                    $ovos[] = $arena;
                }else {
                    $arenas[] = $arena;
                }
            }
        }
        if ($ovo){
            if (!empty($ovos)){
                return $ovos[array_rand($ovos)];
            }
        }else {
            if (!empty($arenas)){
                return $arenas[array_rand($arenas)];
            }
        }
        return null;
    }
    
}
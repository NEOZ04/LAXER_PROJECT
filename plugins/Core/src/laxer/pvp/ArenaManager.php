<?php 

namespace laxer\pvp;

use pocketmine\utils\Config;
use laxer\Core;

class ArenaManager {
    
    public function getAllArena(){
        $arenas = [];
        foreach ($this->getConfig()->getAll() as $arena)
            $arenas[] = $this->getArena($arena['name']); 
        return $arenas;
    }
    
    private function getConfig(){
        return new Config(Core::getInstance()->getDataFolder().'arenas.json',Config::JSON);
    }
    
    public function getOvOArenas(){
        $arenas = [];
        foreach ($this->getAllArena() as $arena){
            if ($arena->getSlots() == 2){
                $arenas[] = $arena;
            }
        }
    }
    
    public function getArena(String $name){
        $name = str_replace(" ", "-", $name);
        return ($this->isExists(strtolower($name))) ? new Arena($name) : null;
    }
    
    public function registerArena(String $name, Int $level_id, Array $positions){
        if (!$this->isExists($name)){
            $conf = $this->getConfig();
            $pos = [];
            foreach ($positions as $p){
                $pos[] = [$p->x,$p->y,$p->z];
            }
            $data = [
                'name' => $name,
                'positions' => [
                    'level' => $level_id,
                    'players' => $pos
                ]
            ];
            $name = str_replace(' ', '-', $name);
            $conf->set(strtolower($name), $data);
            $conf->save();   
            return true;
        }
        return false;
    }
    
    public function isExists(String $name){
        return $this->getConfig()->exists($name,true);
    }
    
    public function removeArena(String $name){
        if ($this->isExists($name)) {
            $conf = $this->getConfig();
            $conf->remove(strtolower($name));
            $conf->save();
            return true;
        }
        return false;
    }
    
}
<?php

namespace laxer\area;

use pocketmine\utils\Config;
use laxer\Core;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\level\Level;

class AreaManager {
    
    private function getConfig(){
        return new Config(Core::getInstance()->getDataFolder().'amconfig.json', Config::JSON, []);
    }
    
    private function setAll($data){
        $conf = $this->getConfig();
        $conf->setAll($data);
        $conf->save();
    }
    
    public function setArea(String $name, Vector3 $v1, Vector3 $v2, Level $level){
        $data = $this->getConfig()->getAll();
        $pos1 = new Position($v1->x,$v1->y,$v1->z,$level);
        $pos2 = new Position($v2->x,$v2->y,$v2->z,$level);
        if ($this->getArea($pos1) === null && $this->getArea($pos2) === null){
            foreach ($data as $area){
                if (strtolower($name) == $area['name']){
                    return 1;
                }
            }
            $data[] = [
                'name' => strtolower($name),
                'pos' => [(int)$v1->x,(int)$v1->y,(int)$v1->z,(int)$v2->x,(int)$v2->y,(int)$v2->z,(int)$level->getId()],
                'pvp' => false,
                'break' => false,
                'touch' => false,
                'move' => true
            ];
            $this->setAll($data);
            return true;
        }
        return false;
    }
    
    public function removeArea(Position $pos){
        return $this->getArea($pos)->delete();
    }
    
    public function getArea(Position $pos){
        
        foreach ($this->getAllArea() as $area){
            if ($area->inArea($pos)){
                return $area;
            }
        }
        return null;
    }
    
    public function getAllArea(){
        $data = $this->getConfig()->getAll();
        $areas = [];
        
        foreach ($data as $area){
            $areas[] = new Area($area['name']);
        }
        return $areas;
    }
    
}
<?php

namespace laxer\area;

use pocketmine\utils\Config;
use laxer\Core;
use pocketmine\level\Position;

class Area {
    
    private $index;
    
    private function getConfig(){
        return new Config(Core::getInstance()->getDataFolder().'amconfig.json', Config::JSON, []);
    }
    
    private function setAll($data){
        $conf = $this->getConfig();
        $conf->setAll($data);
        $conf->save();
        return true;
    }
    
    private function set($k,$v){
        $data = $this->getConfig()->getAll();
        $data[$this->index][$k] = $v;
        $this->setAll($data);
        return true;
    }
    
    private function get($k){
        $data = $this->getConfig()->getAll();
        return $data[$this->index][$k];
    }
    
    public function __construct(String $name){
        foreach ($this->getConfig()->getAll() as $i => $area){
            if ($area['name'] == strtolower($name)){
                $this->index = $i;
            }
        }
    }
    
    public function setPvp(bool $bool){
        return $this->set('pvp', $bool);
    }
    
    public function setTouch(bool $bool){
        return $this->set('touch', $bool);
    }
    
    public function setBreak(bool $bool){
        return $this->set('break', $bool);
    }
    
    public function setMove(bool $bool){
        return $this->set('move', $bool);
    }
    
    public function getPvp(){
        return $this->get('pvp');
    }
    
    public function getTouch(){
        return $this->get('touch');
    }
    
    public function getBreak(){
        return $this->get('break');
    }
    
    public function getMove(){
        return $this->get('move');
    }
    
    public function getName(){
        return $this->get('name');
    }
    
    public function inArea(Position $pos){
        $p = $this->get('pos');
        $x = $pos->x;
        $y = $pos->y;
        $z = $pos->z;
        $x1 = $p[0];
        $y1 = $p[1];
        $z1 = $p[2];
        $x2 = $p[3];
        $y2 = $p[4];
        $z2 = $p[5];
        if ($p[6] == $pos->level->getId()){
            if (
                $x1 <= $x && $x2 >= $x && $y1 >= $y && $y2 <= $y  && $z1 >= $z && $z2 <= $z ||
                $x1 >= $x && $x2 <= $x && $y1 <= $y && $y2 >= $y  && $z1 <= $z && $z2 >= $z
                ){
                    return true;
            }
        }
        return false;
    }
    
    public function delete(){
        $data = $this->getConfig()->getAll();
        unset($data[$this->index]);
        $this->setAll($data);
        return true;
    }
    
}
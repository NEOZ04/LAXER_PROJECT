<?php 

namespace laxer\home;

use pocketmine\level\Position;
use pocketmine\Player;
use laxer\player\PlayerData;
use laxer\Core;

class Home {
    
    public static  function setHome(Player $p, String $name, Position $pos){
        if (self::issetHome($p, $name)){
            $pd = new PlayerData($p->getName());
            $homes = $pd->get('homes');
            $homes[strtolower($name)] = [
                $pos->x,$pos->y,$pos->z,$pos->level->getId()
            ];  
            $pd->set('homes', $homes);
            return true;
        }
        return false;
    }
    
    public static function unsetHome(Player $p, String $name){
        $pd = new PlayerData($p->getName());
        $homes = $pd->get('homes');
        if (self::issetHome($p, $name)){
            unset($homes[strtolower($name)]);
            $pd->set('homes', $homes);
            return true;
        }
        return false;
    }
    
    public static  function issetHome(Player $p, String $name){
        $pd = new PlayerData($p->getName());
        $homes = $pd->get('homes');
        return isset($homes[strtolower($name)]);
    }
    
    public static  function getPos(Player $p, String $name){
        $pd = new PlayerData($p->getName());
        $homes = $pd->get('homes');
        if (self::issetHome($p, $name)){
            $home = $homes[$name];
            $level = Core::getInstance()->getServer()->getLevel($home[3]);
            return new Position((int)$home[0],(int)$home[1],(int)$home[2],$level);
        }
        return false;
    }
    
    public static function getHomes(Player $p){
        $pd = new PlayerData($p->getName());
        $homes = $pd->get('homes');
        $rows = [];
        foreach ($homes as $name => $v){
            $level = Core::getInstance()->getServer()->getLevel($v[3]);
            $pos = new Position($v[0],$v[1],$v[2],$level);
            $rows[] = [$name, $pos];
        }
        return $rows;
    }
    
}
<?php 

namespace laxer\player;

use pocketmine\utils\Config;
use laxer\Core;

class TimeRank {
    
    private static function getConfig(){
        return new Config(Core::getInstance()->getDataFolder().'TimeRankConfig.json', Config::JSON);
    }
    
    public static function getAll(){
        $conf = self::getConfig();
        $data = $conf->getAll();
        $times = [];
        foreach ($data as $rank => $time){
            $times[$time] = $rank;
        }
        return $times;
    }
    
}
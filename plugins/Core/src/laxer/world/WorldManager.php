<?php 

namespace laxer\world;

use laxer\libs\MyZip;
use laxer\Core;

class WorldManager {
    
    public function BackUp(string $level_name){
        $level = Core::getInstance()->getServer()->getLevelByName($level_name);
        if (!is_null($level)){
            MyZip::zipDir('worlds/'.$level->getName(), 'backup/worlds');
        }
        return false;
    }
    
}
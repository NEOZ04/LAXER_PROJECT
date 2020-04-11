<?php 

namespace laxer\crate;

use pocketmine\scheduler\Task;
use laxer\Core;
use pocketmine\level\particle\PortalParticle;
use pocketmine\level\particle\ExplodeParticle;
use pocketmine\level\particle\WaterDripParticle;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\particle\HappyVillagerParticle;

class CrateTask extends Task {
    
    public function onRun(int $currentTick){
        $spawn = Core::getInstance()->getServer()->getDefaultLevel()->getSafeSpawn();
        
        $level = $spawn->level;
        $cpos = $spawn->asPosition();
        $time = 1;
        $pi = 3.14159;
        $time = $time + 0.1 / $pi;
//         for($i = 0; $i <= 2 * $pi; $i += $pi / 8){
//             $x = $time * cos($i);
//             $y = exp(-0.1 * $time) * sin($time) + 1.5;
//             $z = $time * sin($i);
//             $level->addParticle(new ExplodeParticle($cpos->add($x, $y, $z)));
//             $level->addParticle(new WaterDripParticle($cpos->add($x, $y, $z)));
//         }
//         for ($i = 0; $i <= 2 * $pi; $i += $pi / 8) {
//             $x = $time * cos($i);
//             $y = exp(-0.1 * $time) * sin($time) + 1.5;
//             $z = $time * sin($i);
//             $level->addParticle(new FlameParticle($cpos->add($x, $y, $z)));
//         }

        
//         for($i = 5; $i > 0; $i -= 0.1){
//             $radio = $i / 3;
//             $x = $radio * cos(3 * $i);
//             $y = 5 - $i;
//             $z = $radio * sin(3 * $i);
//             $level->addParticle(new FlameParticle($cpos->add($x, $y, $z)));
//         }
//         for($i = 5; $i > 0; $i -= 0.1){
//             $radio = $i / 3;
//             $x = -$radio * cos(3 * $i);
//             $y = 5 - $i;
//             $z = -$radio * sin(3 * $i);
//             $level->addParticle(new FlameParticle($cpos->add($x, $y, $z)));
//         }
        
    }
}
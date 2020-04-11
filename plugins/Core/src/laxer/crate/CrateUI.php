<?php 

namespace laxer\crate;

use pocketmine\Player;
use laxer\ui\CoreUI;
use pocketmine\utils\TextFormat;
use pocketmine\tile\EnderChest;
use laxer\Core;

class CrateUI {
    
    private $chest;
    
    public function getMainForm(Player $p, EnderChest $chest){
        $this->chest = $chest;
        $ui = CoreUI::Custom(function (Player $p, $data){
           if ($data === null) return false;
           
           Core::getInstance()->getCrate()->setCrate($this->chest->asPosition(), (int)$data[0]);
           Core::getInstance()->getCrate()->loadCrates();
           $p->sendMessage('Berhasil');
        });
        
        $ui->setTitle(CoreUI::fTitle('CRATE'));
        $steps = Core::getInstance()->getCrate()->getTypes();
        $ui->addStepSlider(TextFormat::colorize('&l&f•> Type'), $steps);
        $ui->sendToPlayer($p);
    }
    
}
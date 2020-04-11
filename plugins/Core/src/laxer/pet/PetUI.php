<?php 

namespace laxer\pet;

use pocketmine\Player;
use laxer\ui\CoreUI;
use laxer\Core;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\entity\Entity;

class PetUI {
    
    private function getButtons(){
        $btns = [
            "My Pets", "Pet Shop"
        ];
        
        return $btns;
    }
    
    public function getMainForm(Player $p){
        $ui = CoreUI::Simple(function (Player $p, $data){
           if ($data === null) return false;
           $btn = $this->getButtons()[$data];
           switch (strtolower($btn)){
               case 'my pets':
                   break;
               case 'pet shop':
                   break;
           }
        });
        $ui->setTitle(CoreUI::fTitle('PET'));
        foreach ($this->getButtons() as $text){
            $ui->addButton(CoreUI::fButton($text));
        }
        $ui->sendToPlayer($p);
    }
    
    public function MyPets(Player $p){
        $ui = CoreUI::Simple(function (Player $p, $data){
            if ($data === null) return false;
            $pets = Core::getInstance()->getPet()->getPlayerPets($p->getName());
            $this->type = $pets[$data];
            $this->Action($p);
        });
        $ui->setTitle(CoreUI::fTitle('PET'));
        $pets = Core::getInstance()->getPet()->getPlayerPets($p->getName());
        foreach ($pets as $text){
            $ui->addButton(CoreUI::fButton($text));
        }
        $ui->sendToPlayer($p);
    }
    
    private function getActions(){
        $data = [
            'Sell', 'Spawn'
        ];
        return $data;
    }
    
    public function Action(Player $p){
        $ui = CoreUI::Simple(function (Player $p, $data){
            if ($data === null) return false;
            $action = $this->getActions()[$data];
            switch (strtolower($action)){
                case 'spawn':
                    $ec = Entity::$entityCount++;
                    $pk = new AddEntityPacket();
                    $pk->putEntityRuntimeId($ec);
                    break;
                case 'sell':
                    break;
            }
        });
        $ui->setTitle(CoreUI::fTitle('PET'));
        foreach ($this->getActions($p) as $text){
            $ui->addButton(CoreUI::fButton($text));
        }
        $ui->sendToPlayer($p);
    }
    
}
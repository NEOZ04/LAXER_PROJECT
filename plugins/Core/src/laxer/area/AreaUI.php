<?php 

namespace laxer\area;

use laxer\Core;
use laxer\ui\CoreUI;
use pocketmine\Player;

class AreaUI {
    
    private $area;
    
    public function __construct(Area $area){
        $this->area = $area;
    }
    
    public function getArea(){
        return $this->area;
    }
    
    private function getButtons(){
        return [
            'touch','pvp','break','move'
        ];
    }
    
    public function getMainForm(Player $p){
        $ui = CoreUI::Simple(function (Player $p, $data){
           if (is_null($data)) return false;
           $btn = $this->getButtons()[$data];
           $area = $this->area;
           switch ($btn){
               case 'touch':
                   $area->setTouch(($area->getTouch()) ? false : true);
                   if ($area->getTouch()){
                       $p->sendMessage(CoreUI::Success('Area sekarang di sentuh'));
                   }else {
                       $p->sendMessage(CoreUI::Success('Area sekarang tidak bisa di sentuh'));
                   }
                   break;
               case 'pvp':
                   $area->setPvp(($area->getPvp()) ? false : true);
                   if ($area->getPvp()){
                       $p->sendMessage(CoreUI::Success('Area sekarang bisa pvp'));
                   }else {
                       $p->sendMessage(CoreUI::Success('Area sekarang tidak bisa pvp'));
                   }
                   break;
               case 'break':
                   $area->setBreak(($area->getBreak()) ? false : true);
                   if ($area->getBreak()){
                       $p->sendMessage(CoreUI::Success('Area sekarang bisa di hancurkan'));
                   }else {
                       $p->sendMessage(CoreUI::Success('Area sekarang tidak bisa di hancurkan'));
                   }
                   break;
               case 'move':
                   $area->setMove(($area->getMove()) ? false : true);
                   if ($area->getMove()){
                       $p->sendMessage(CoreUI::Success('Area sekarang bisa di masuki'));
                   }else {
                       $p->sendMessage(CoreUI::Success('Area sekarang tidak bisa di masuki'));
                   }
                   break;
           }
        });
        $ui->setTitle(CoreUI::fTitle('Area '.$this->getArea()->getName()));
        foreach ($this->getButtons() as $text){
            $ui->addButton(CoreUI::fButton($text));
        }
        $ui->sendToPlayer($p);
    }
    
}
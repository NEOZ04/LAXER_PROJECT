<?php 

namespace laxer\area;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use laxer\Core;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\entity\EntityDamageEvent;
use laxer\ui\CoreUI;

class AreaListener implements Listener {
    
    public function onBreak(BlockBreakEvent $e){
        $p = $e->getPlayer();
        $b = $e->getBlock();
        $am = Core::getInstance()->getAreaManager();
        $area = $am->getArea($b->asPosition());
        if ($area !== null){
            if (!$area->getBreak()){
                if (!$p->isOp()){
                    $e->setCancelled();
                }   
            }
        }
    }
    
    public function onTouch(PlayerInteractEvent $e){
        $p = $e->getPlayer();
        $b = $e->getBlock();
        $am = Core::getInstance()->getAreaManager();
        $area = $am->getArea($b->asPosition());
        if ($area !== null){
            if (!$area->getTouch()){
                if (!$p->isOp()){
                    $e->setCancelled();
                }
            }
        }
    }
    
    public function onHit(EntityDamageEvent $e){
        $p = $e->getEntity();
        $am = Core::getInstance()->getAreaManager();
        $area = $am->getArea($p->asPosition());
        if ($area !== null){
            if (!$area->getPvp()){
                if (!$p->isOp()){
                    $e->setCancelled();
                }
            }
        }
    }
    
    public function onMove(PlayerMoveEvent $e){
        $p = $e->getPlayer();
        $am = Core::getInstance()->getAreaManager();
        $area = $am->getArea($p->asPosition());
        $name = (is_null($area)) ? 'none area' : $area->getName();
        $p->sendPopup(CoreUI::translate('&f&l'.$name));
        if (!is_null($area)){
            if (!$area->getMove()){
                if (!$p->isOp()){
                    $p->setPosition($e->getFrom()->asPosition());
                    $e->setCancelled();
                }
            }
        }
    }
    
}
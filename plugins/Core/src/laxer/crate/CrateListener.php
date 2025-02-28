<?php 

namespace laxer\crate;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\tile\EnderChest;
use laxer\Core;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\inventory\ChestInventory;
use pocketmine\block\Block;
use pocketmine\tile\Chest;
use pocketmine\tile\Tile;
use laxer\libs\muqsit\invmenu\InvMenu;
use laxer\ui\CoreUI;
use pocketmine\event\player\PlayerJoinEvent;
use laxer\player\PlayerData;

class CrateListener implements Listener {
    
    
    public function onInteract(PlayerInteractEvent $e){
        $p = $e->getPlayer();
        $i = $e->getItem();
        $b = $e->getBlock();
        $t = $b->level->getTile($b->asPosition());
        
        if ($t instanceof EnderChest){
            if ($p->isOp()) {
                if ($i->getId() === 399) {
                    if (!Core::getInstance()->getCrate()->isCrate($t->asPosition())){
                        $e->setCancelled();
                        
                        $ui = new CrateUI();
                        $ui->getMainForm($p, $t);
                        Core::getInstance()->getCrate()->loadCrates();
                        return true;
                    }
                }
            }
            if (Core::getInstance()->getCrate()->isCrate($t->asPosition())){
                $e->setCancelled();
                $success = Core::getInstance()->getCrate()->OpenCrate($p,$t->asPosition());
                if ($success){
                    Core::getInstance()->getCrate()->loadCrates();
                }else {
                    $p->sendMessage(CoreUI::Danger('Anda tidak memiliki key'));
                }
                return true;
            }
        }
    }
    
    public function onBreak(BlockBreakEvent $e){
        $p = $e->getPlayer();
        $b = $e->getBlock();
        $t = $b->level->getTile($b->asPosition());
        if ($t instanceof EnderChest){
            if ($p->isOp()){
                Core::getInstance()->getCrate()->unsetCrate($t->asPosition());
                Core::getInstance()->getCrate()->loadCrates();
                $p->sendMessage('Crate telah dihapus');
                return true;
            }
        }
    }
    
    public function onJoin(PlayerJoinEvent $e){
        $p = $e->getPlayer();
        $pd = new PlayerData($p->getName());
        $lj = (int) $pd->get('last_join');
        $t = strtotime(date('d-m-y',$lj));
        $td = strtotime(date('d-m-y'));
        if ((($td-$t)/24/60/60/60) > 1){
            if ($p->hasPermission('laxer.platinum.free')){
                $key = (int) Core::getInstance()->getCrate()->getKey($p->getName(), 0);
                Core::getInstance()->getCrate()->setKey($p->getName(),0,$key+1);
            }
            if ($p->hasPermission('laxer.regular.free')) {
                $key = (int) Core::getInstance()->getCrate()->getKey($p->getName(), 1);
                Core::getInstance()->getCrate()->setKey($p->getName(),1,$key+1);
            }
            if ($p->hasPermission('laxer.general.free')) {
                $key = (int) Core::getInstance()->getCrate()->getKey($p->getName(), 2);
                Core::getInstance()->getCrate()->setKey($p->getName(),2,$key+1);
            }
        }
        Core::getInstance()->getCrate()->loadCrates();
    }
    
}
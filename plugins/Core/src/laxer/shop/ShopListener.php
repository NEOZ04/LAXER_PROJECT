<?php 

namespace laxer\shop;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\tile\Sign;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use laxer\Core;
use pocketmine\level\Position;

class ShopListener implements Listener {
    
    public function onInteract(PlayerInteractEvent $e){
        $p = $e->getPlayer();
        $i = $e->getItem();
        $b = $e->getBlock();
        $tile = $b->level->getTile($b->asPosition());
        
        if ($tile instanceof Sign){
            
            if ($p->isOp()){
                if ($i->getId() === 399) {
                    if (!Core::getInstance()->getShop()->isExist($tile->asPosition())){
                        $ui = new CShopUI($tile);
                        $ui->getMainForm($p);
                        return false;
                    }
                }
            }
            
            if (Core::getInstance()->getShop()->isExist($tile->asPosition())) {
                $ui = new CShopUI($tile);
                $shop = Core::getInstance()->getShop()->getShop($tile->asPosition());
                $ui->getShop($p, $shop);
            }
            
        }
        
    }
    
    public function onBreak(BlockBreakEvent $e){
        $p = $e->getPlayer();
        $b = $e->getBlock();
        $t = $b->level->getTile($b->asPosition());
        if ($p->isOp()){
            if ($t instanceof Sign){
                foreach (Core::getInstance()->getShop()->getSigns() as $id => $shop){
                    $pos = $shop['pos'];
                    $sign = Core::getInstance()->getServer()->getLevel($pos[3])->getTileAt($pos[0], $pos[1], $pos[2]);
                    if ($sign->equals($t)){
                        Core::getInstance()->getShop()->unsetSign($id);
                        return false;
                    }
                }
            }
        }
    }
    
}
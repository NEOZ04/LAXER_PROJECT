<?php 

namespace laxer\pshop;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\tile\Chest;
use laxer\Core;
use laxer\ui\CoreUI;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\tile\Sign;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\inventory\InventoryCloseEvent;

class PShopListener implements Listener {
    
    public function onInteract(PlayerInteractEvent $e){
        $p = $e->getPlayer();
        $b = $e->getBlock();
        $t = $b->level->getTile($b->asPosition());
        $ps = Core::getInstance()->getPShop();
        if ($t instanceof Chest) {
            if ($ps->isChestShop($t)){
                $shop = $ps->getShopByChest($t);
                if (!($shop->isOwner($p) || $shop->isHelper($p->getName()))){
                    $e->setCancelled();
                    $p->sendMessage(CoreUI::Warning('Peti ini sudah dimiliki oleh '.$shop->getOwner()->getName().' shop'));
                    return true;
                }
            }
        }
        if ($t instanceof Sign){
            if (($shop = $ps->getShopBySign($t)) !== null){
                $ui = new ShopUI($shop);
                $data = $shop->getDataItemBySign($t);
                $ui->getMainForm($p, $data);
                return true;
            }
        }
        if (isset(Core::$_SESSIONS[$p->getName()]['setChest'])){
            $action = Core::$_SESSIONS[$p->getName()]['setChest'];
            if ($t instanceof Chest){
                if (!$ps->isChestShop($t)){
                    if (time() - $action['time'] < 15){
                        $shop = $action['shop'];
                        if ($shop instanceof Shop){
                            $shop->setChestShop($t);
                            $p->sendMessage(CoreUI::Success('Chest berhasil di set sebagai Chest Shop'));
                            unset(Core::$_SESSIONS[$p->getName()]);
                            return true;
                        }
                    }
                    $p->sendMessage(CoreUI::Danger('Waktu telah berakhir'));
                    unset(Core::$_SESSIONS[$p->getName()]);
                    return false;
                }else {
                    $e->setCancelled();
                    $p->sendMessage(CoreUI::Danger('Chest ini sudah di set menjadi Chest Shop'));
                    unset(Core::$_SESSIONS[$p->getName()]);
                    return false;
                }
            }else {
                $p->sendMessage(CoreUI::Danger('Aksi dibatalkan'));
                unset(Core::$_SESSIONS[$p->getName()]);
                return false;
            }
        }
        if (isset(Core::$_SESSIONS[$p->getName()]['setSign'])){
            $action = Core::$_SESSIONS[$p->getName()]['setSign'];
            if ($t instanceof Sign){
                if (($shop = $ps->getShopBySign($t)) == null){
                    if (time() - $action['time'] < 15) {
                        $shop = $action['shop'];
                        if ($shop instanceof Shop){
                            $data = $action['data'];
                            $shop->setSign($t, $data['id'], $data['damage']);
                            $p->sendMessage(CoreUI::Success('Sign berhasil di set sebagai Sign Shop'));
                            $shop->updateSign($t);
                            unset(Core::$_SESSIONS[$p->getName()]);
                            return true;
                        }
                    }
                    $p->sendMessage(CoreUI::Danger('Waktu telah berakhir'));
                    unset(Core::$_SESSIONS[$p->getName()]);
                    return false;
                }else {
                    $p->sendMessage(CoreUI::Danger('Sign ini sudah di set menjadi Sign Shop'));
                    unset(Core::$_SESSIONS[$p->getName()]);
                    return false;
                }
            }else {
                $p->sendMessage(CoreUI::Danger('Aksi dibatalkan'));
                unset(Core::$_SESSIONS[$p->getName()]);
            }
        }
    }
    
    public function onBreak(BlockBreakEvent $e){
        $p = $e->getPlayer();
        $b = $e->getBlock();
        $t = $b->level->getTile($b->asPosition());
        $ps = Core::getInstance()->getPShop();
        if ($t instanceof Chest) {
            if ($ps->isChestShop($t)){
                $shop = $ps->getShopByChest($t);
                if (!($shop->isOwner($p) || $shop->isHelper($p->getName()))){
                    $e->setCancelled();
                    $p->sendMessage(CoreUI::Warning('Peti ini sudah dimiliki oleh '.$shop->getOwner().' shop'));
                    return true;
                }else {
                    $shop->unsetChestShop($t);
                    $p->sendMessage(CoreUI::Warning('ChestShop berhasil di hapus'));
                    return true;
                }
            }
        }
        
        if ($t instanceof Sign){
            if (($shop = $ps->getShopBySign($t)) !== null){
                if (!($shop->isOwner($p) || $shop->isHelper($p->getName()))){
                    $e->setCancelled();
                    $p->sendMessage(CoreUI::Warning('Sign ini sudah dimiliki oleh '.$shop->getOwner().' shop'));
                    return true;
                }else {
                    $shop->unsetSign($t);
                    $p->sendMessage(CoreUI::Warning('Sign berhasil di hapus'));
                    return true;
                }
            }
        }
    }
    
    public function onChane(InventoryCloseEvent $e){
        $p = $e->getPlayer();
        $shop = Core::getInstance()->getPShop()->getShop($p->getName());
        $shop->updateSigns();
    }
    
    public function onJoin(PlayerJoinEvent $e){
        $p = $e->getPlayer();
        Core::getInstance()->getPShop()->createShop($p);
    }
    
}
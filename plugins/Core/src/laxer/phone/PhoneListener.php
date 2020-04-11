<?php 

namespace laxer\phone;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class PhoneListener implements Listener {
    
    public function onJoin(PlayerJoinEvent $e){
        $p = $e->getPlayer();
        $phone = Item::get(399)->setCustomName(TextFormat::colorize("&lPhone"));
        if (!$p->getInventory()->contains($phone)) {
            $p->getInventory()->addItem($phone);
        }
    }
    
    public function onInteract(PlayerInteractEvent $e){
        $p = $e->getPlayer();
        $i = $p->getInventory()->getItemInHand();
        $phone = Item::get(399)->setCustomName(TextFormat::colorize('&lPhone'));
        if ($i->equals($phone)) {
            $ui = new PhoneUI();
            $ui->getMainForm($p);
        }
    }
    
}
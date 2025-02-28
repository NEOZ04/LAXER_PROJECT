<?php 

namespace laxer;

use laxer\player\PlayerListener;
use laxer\phone\PhoneListener;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use laxer\ui\CoreUI;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use laxer\shop\ShopListener;
use laxer\crate\CrateListener;
use laxer\pshop\PShopListener;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\Player;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerMoveEvent;
use laxer\area\AreaListener;

class CoreListeners implements Listener {
    
    public function registerAll(Core $core){
        $listeners = [
            new PlayerListener(), new PhoneListener(), $this, new ShopListener(), new CrateListener(),
            new PShopListener(), new AreaListener()
            
        ];
        foreach ($listeners as $listener){
            $core->getServer()->getPluginManager()->registerEvents($listener, $core);
        }
    }
    
    public function onJoin(PlayerJoinEvent $e){
        Core::getInstance()->getCrate()->loadCrates();
        $p = $e->getPlayer();
//         $p->setGamemode(0);
        $p->setAllowFlight(true);
        $p->setFlying(false);
        $e->setJoinMessage(CoreUI::Notice($p->getName().' join game'));
        $p->teleport(Core::getInstance()->getServer()->getDefaultLevel()->getSafeSpawn());
    }
    
    public function onChangeWorld(EntityLevelChangeEvent $e){
        $p = $e->getEntity();
        if ($p instanceof Player){
            $p->setFlying(false);
            $p->setGamemode(0);
        }
    }
    
    public function onLeft(PlayerQuitEvent $e){
        $p = $e->getPlayer();
        $e->setQuitMessage(CoreUI::Warning($p->getName().' left game'));
    }
    
    public function onChangeItem(PlayerItemHeldEvent $e){
        $p = $e->getPlayer();
        $i = $e->getItem();
        $p->sendPopup($i->getName().' '.$i->getId().':'.$i->getDamage());
    }
    
    public function onChat(PlayerChatEvent $e){
        $p = $e->getPlayer();
        if (isset(Core::$_SESSIONS[$p->getName()]['tpa'])){
            $action = Core::$_SESSIONS[$p->getName()]['tpa'];
            if (time() - $action['time'] < 15){
                if (strtolower($e->getMessage()) == 'accept'){
                    $e->setCancelled();
                    $s = Core::getInstance()->getServer()->getPlayer($action['sender']);
                    if (is_null($s)){
                        $p->sendMessage(CoreUI::Danger('Player sudah keluar'));
                        return false;
                    }
                    if (!$action['atpos']){
                        $s->teleport($p->asPosition());
                        $s->sendMessage(CoreUI::Notice('Teleporting...'));
                    }else {
                        $p->teleport($p->asPosition());
                        $p->sendMessage(CoreUI::Notice('Teleporting...'));
                    }
                    unset(Core::$_SESSIONS[$p->getName()]['tpa']);
                }elseif (strtolower($e->getMessage()) == 'deny'){
                    $e->setCancelled();
                    $s = Core::getInstance()->getServer()->getPlayer($action['sender']);
                    if (!is_null($s)){
                        $s->sendMessage(CoreUI::Danger($p->getName().' tidak menyetujui teleport'));
                        return false;
                    }
                    unset(Core::$_SESSIONS[$p->getName()]['tpa']);
                }
            }
        }
    }
    
}
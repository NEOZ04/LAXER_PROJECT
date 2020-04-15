<?php 

namespace laxer\player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use laxer\Core;
use pocketmine\event\player\PlayerQuitEvent;

class PlayerListener implements Listener {
    
    public function onJoin(PlayerJoinEvent $e){
        $p = $e->getPlayer();
        $pd = new PlayerData($p->getName());
        $pd->addAttribute('first_join', time());
        $pd->addAttribute('last_join', time());
        $pd->addAttribute('homes', []);
        $task = Core::getInstance()->getScheduler()->scheduleRepeatingTask(new PlayerTimeTask($p), 20);
        Core::$_SESSIONS['player_time'][$p->getName()] = $task; 
    }
    
    public function onQuit(PlayerQuitEvent $e){
        $p = $e->getPlayer();
        $task = Core::$_SESSIONS['player_time'][$p->getName()];
        Core::getInstance()->getScheduler()->cancelTask($task->getTaskId());
        $pd = new PlayerData($p->getName());
        $pd->set('last_join', time());
    }
    
}
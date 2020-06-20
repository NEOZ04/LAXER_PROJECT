<?php

namespace laxer\pvp;

use pocketmine\Player;
use laxer\Core;
use pocketmine\level\Position;
use laxer\ui\CoreUI;
use pocketmine\utils\Config;
use pocketmine\scheduler\Task;

class Arena {
    
    /**
     * 
     * @var Player[]
     */
    private $players = [];
    private $positions = [];
    private $slots = 2;
    private $phase = 0;
    private $name = '';
    /**
     * 
     * @var Task
     */
    private $task = null;
    
    public function __construct(String $arena_name){
        $this->init($arena_name);
    }
    
    private function init(String $name){
        $conf = new Config(Core::getInstance()->getDataFolder().'arenas.json',Config::JSON);
        $name = str_replace(" ", "-", $name);
        $data = $conf->get(strtolower($name));
        $this->positions = $data['positions'];
        $this->name = $data['name'];
        $this->slots = count($data['positions']['players']);
    }
    
    public function join(Player $p){
        if (!$this->inGame($p->getName())){
            if (!$this->isFull()){
                $this->phase = 1;
                $this->players[strtolower($p->getName())] = $p;
                $p->sendMessage(CoreUI::Success(CoreUI::translate('pvp.join', true)));
                return true;
            }
            $p->sendMessage(CoreUI::Danger(CoreUI::translate('pvp.full', true)));
            return true;
        }
        $p->sendMessage(CoreUI::Danger(CoreUI::translate('pvp.matching', true, ["%detik" => 0])));
        return false;
    }
    
    public function quit(String $name){
        if ($this->inGame($name)){
            unset($this->players[strtolower($name)]);
            $p = Core::getInstance()->getServer()->getPlayer($name);
            if (!is_null($p)){
                $p->teleport(Core::getInstance()->getServer()->getDefaultLevel()->getSafeSpawn());
                $p->setGamemode(0);
                $p->removeAllEffects();
                $p->setFood(20);
                $p->setHealth(20);
                $p->setFlying(false);
            }
        }
    }
    
    public function isFull(){
        return count($this->players) >= (int)$this->slots;
    }
    
    public function start(){
        if ($this->isFull()){
            $this->phase = 2;
            $i = 0;
            foreach ($this->players as $p){
                $p->setGamemode(0);
                $p->setFlying(false);
                $pos = $this->positions['players'][$i];
                $level = Core::getInstance()->getServer()->getLevel($this->positions['level']);
                $pos = new Position($pos[0],$pos[1],$pos[2],$level);
                $p->teleport($pos);
                $p->sendMessage(CoreUI::Success(CoreUI::translate('pvp.start', true)));
                $p->removeAllEffects();
                $i++;
            }
        }
    }
    
    public function inGame(String $name){
        return isset($this->players[strtolower($name)]);
    }
    
    public function reset(){
        foreach ($this->players as $p){
            $this->quit($p->getName());
        }
        $this->players = [];
        Core::getInstance()->getScheduler()->cancelTask($this->task->getTaskId());
        $this->phase = 0;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function getSlots(){
        return $this->slots;
    }
    
    public function getPhase(){
        return $this->phase;
    }
    
    public function getPlayers(){
        $players = [];
        foreach ($this->players as $p) $players[] = $p;
        return $players;
    }
    
}
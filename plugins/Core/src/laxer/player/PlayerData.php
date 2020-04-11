<?php 

namespace laxer\player;

use pocketmine\utils\Config;
use laxer\Core;

Class PlayerData {
    
    private $name;
    private $conf;
    
    public function __construct(String $player_name){
        $name = strtolower($player_name);
        $name = trim(hash('md5', $name), ' ');
        if (!file_exists(Core::getInstance()->getDataFolder().'players/'.$name.'.json')) {
            $conf = new Config(Core::getInstance()->getDataFolder().'players/'.$name.'.json', Config::JSON);
            $data = [
                'name' => $player_name,
                'points' => 0,
                'money' => 0,
                'friends' => [],
                'jobs' => [],
                'keys' => [0,0,0,0],
                'join_time' => 0,
            ];
            foreach ($data as $k => $v){
                $conf->set($k,$v);
            }
            $conf->save();
            Core::getInstance()->getLogger()->notice('New Player: '.$player_name);
        }else {
            $conf = new Config(Core::getInstance()->getDataFolder().'players/'.$name.'.json', Config::JSON);
        }
        $this->conf = $conf;
        $this->name = $name;
    }
    
    public function addAttribute($k,$v){
        $conf = $this->conf;
        $data = $conf->getAll();
        if (!isset($data[$k])){
            $data[$k] = $v;
            $conf->setAll($data);
            $conf->save();
            return true;
        }
        return false;
    }
    
    public function removeAttribute($k){
        $conf = $this->conf;
        $data = $conf->getAll();
        if (isset($data[$k])){
            unset($data[$k]);
            $conf->setAll($data);
            $conf->save();
            return true;
        }
        return false;
    }
    
    public function issetAtribute($k){
        $conf = $this->conf;
        $data = $conf->getAll();
        return isset($data[$k]);
    }
    
    public function get($k){
        return $this->conf->get($k);
    }
    
    public function set($k, $v){
        $conf = $this->conf;
        $conf->set($k, $v);
        return $conf->save();
    }
    
}
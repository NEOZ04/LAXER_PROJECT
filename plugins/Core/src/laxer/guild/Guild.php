<?php 

namespace laxer\guild;

use laxer\Core;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\level\Level;

class Guild {
    
    private $conf;
    
    public function __construct(String $name, String $alias = "", String $leader_name = ""){
        $guild_name = strtolower($name);
        $hashName = trim(hash('md5', $guild_name), ' ');
        $filepath = Core::getInstance()->getDataFolder().'guilds/'.$hashName.'.json';
        $this->conf = new Config($filepath, Config::JSON);
    }
    
    private function get($k){
        return $this->conf->get($k);
    }
    private function set($k, $v){
        $conf = $this->conf;
        $conf->set($k, $v);
        return $conf->save();
    }
    
    public function isLeader(String $p_name){
        $leader = $this->get('leader');
        if ($leader == $p_name){
            return true;
        }
        return false;
    }
    
    public function isOfficer(String $p_name){
        $officers = $this->get('officers');
        if (isset($officers[$p_name])){
            return true;
        }
        return false;
    }
    
    public function isMember(String $p_name){
        $members = $this->get('members');
        if (isset($members[$p_name])) {
            return true;
        }
        return false;
    }
    
    public function addMember(Player $p){
        $name = $p->getName();
        $members = $this->get('members');
        if (!$this->isMember($name)) {
            $members[$name] = true;
            return $this->set('members', $members);
        }
        return false;
    }
    
    public function kickMember(String $p_name){
        $members = $this->get('members');
        $officers = $this->get('officers');
        if ($this->isMember($p_name)) {
            if (!$this->isLeader($p_name)){
                if ($this->isOfficer($p_name)) {
                    unset($officers[$p_name]);
                    $this->set('officers', $officers);
                }
                unset($officers[$p_name]);
                $this->set('members', $members);
                return true;
            }
        }
        return false;
    }
    
    public function getName(){
        return $this->get('name');
    }
    
    public function getAlias(){
        return $this->get('alias');
    }
    
    public function getLeader(){
        return $this->get('leader');
    }
    
    public function getOfficers(){
        $data = $this->get('officers');
        $officers = [];
        foreach ($data as $name => $v){
            $officers[] = $name;
        }
        return $officers;
    }
    
    public function getMembers(){
        $data = $this->get('members');
        $members = [];
        foreach ($data as $name => $v){
            $members[] = $name;
        }
        return $members;
    }
    
    public function addOfficer(Player $p){
        $name = $p->getName();
        $officers = $this->get('officers');
        if ($this->isMember($name)) {
            if (!$this->isOfficer($name)) {
                $officers[$name] = true;
                return $this->set('officers', $officers);
            }
        }
        return false;
    }
    
    public function removeOfficer(String $p_name){
        $officers = $this->get('officers');
        if ($this->isMember($p_name)){
            if ($this->isOfficer($p_name)) {
                unset($officers[$p_name]);
                return $this->set('officers', $officers);
            }
        }
        return false;
    }
    
    public function setLeader(String $p_name){
        if ($this->isMember($p_name)) {
            if (!$this->isLeader($p_name)){
                if ($this->isOfficer($p_name)) {
                    $this->removeOfficer($p_name);
                }
                $oldLeader = $this->get('leader');
                $this->set('leader', $p_name);
                $this->addOfficer($oldLeader);
                return true;
            }
        }
        return false;
    }
    
    public function getPoint(){
        return $this->get('points');
    }
    
    public function setPoint($v){
        return $this->set('points', $v);
    }
    
    public function addPoint($v){
        return $this->setPoint($this->getPoint() + $v);
    }
    
    public function reducePoint($v){
        return $this->setPoint($this->getPoint() - $v);
    }
    
    public function setBase(Level $level, int $x1, int $y1, int $z1, int $x2, int $y2, int $z2){
        $data = [
            'level_id' => $level->getId(),
            'pos1' => [
                'x' => $x1,
                'y' => $y1,
                'z' => $z1
            ],
            'pos2' => [
                'x' => $x2,
                'y' => $y2,
                'z' => $z2
            ]
        ];
        return $this->set('base', $data);
    }
    
    public function unSetBase(){
        $data = [];
        return $this->set('base', $data);
    }
    
}
<?php 

namespace laxer\player;

use _64FF00\PurePerms\PurePerms;
use laxer\Core;

class PlayerRank {
    
    private $api;
    private $plugin;
    
    public function __construct(){
        $plugin = Core::getInstance()->getServer()->getPluginManager()->getPlugin('PurePerms');
        if ($plugin instanceof PurePerms) {
            $this->plugin = $plugin;
            $this->api = $plugin->getUserDataMgr();
        }
    }
    
    public function getRank(String $name){
        $p = Core::getInstance()->getServer()->getPlayer($name);
        if (is_null($p)){
            $p = Core::getInstance()->getServer()->getOfflinePlayer($name);
        }
        return $this->api->getGroup($p);
    }
    
    public function setRank(String $name, String $rank){
        $p = Core::getInstance()->getServer()->getPlayer($name);
        if (is_null($p)){
            $p = Core::getInstance()->getServer()->getOfflinePlayer($name);
        }
        $rank = $this->plugin->getGroup($rank);
        return $this->api->setGroup($p, $rank, null);
    }
    
}
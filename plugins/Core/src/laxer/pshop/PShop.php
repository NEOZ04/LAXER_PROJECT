<?php 

namespace laxer\pshop;

use pocketmine\Player;
use laxer\Core;
use pocketmine\tile\Chest;
use pocketmine\utils\Config;
use pocketmine\tile\Sign;

class PShop {
    
    
    
    public function createShop(Player $p){
        if ($p->hasPermission('laxer.pshop')){
            new Shop($p->getName());
            return true;
        }
        return false;
    }
    
    public function resetShop(Player $p){
        $shop = $this->getShop($p->getName());
        return $shop->reset();
    }
    
    public function getShop(String $name){
        $p = Core::getInstance()->getServer()->getPlayer($name);
        if ($p !== null){
            $this->createShop($p);
        }
        if ($this->hasCreated(strtolower($name))) {
            return new Shop($name);
        }
        
        return null;
    }
    
    public function getAllShops(){
        $files = scandir(Core::getInstance()->getDataFolder().'shops');
        $shops = [];
        foreach ($files as $filename){
            $fileExp = explode('.', $filename);
            $ext = end($fileExp);
            if ($ext == "json"){
                $conf = new Config(Core::getInstance()->getDataFolder().'shops/'.$filename);
                $name = $conf->get('owner');
                $shops[] = $this->getShop($name);
            }
        }
        return $shops;
    }
    
    public function hasCreated(String $name){
        $hashname = hash('md5', strtolower($name));
        $filepath = Core::getInstance()->getDataFolder()."shops/".$hashname.".json";
        return file_exists($filepath);
    }
    
    public function getOwnerByChest(Chest $chest){
        foreach ($this->getAllShops() as $shop){
            if ($shop->isChestShop($chest)) return $shop->getOwner();
        }
        return null;
    }
    
    public function getOwnerBySign(Sign $sign){
        foreach ($this->getAllShops() as $shop){
            if ($shop->issetSign($sign)) return $shop->getOwner();
        }
        return null;
    }
    
    public function loadSigns(){
        foreach ($this->getAllShops() as $shop){
            $shop->updateSigns();
        }
    }
    
    public function getShopByChest(Chest $chest){
        $owner = $this->getOwnerByChest($chest);
        if (!is_null($owner)){
            return $this->getShop($owner->getName());
        }
        return null;
    }
    
    public function getShopBySign(Sign $sign){
        $owner = $this->getOwnerBySign($sign);
        if (!is_null($owner)){
            return $this->getShop($owner->getName());
        }
        return null;
    }
    
    public function isChestShop(Chest $chest){
        foreach ($this->getAllShops() as $shop){
            if ($shop->isChestShop($chest)) return true;
        }
        return false;
    }
    
}
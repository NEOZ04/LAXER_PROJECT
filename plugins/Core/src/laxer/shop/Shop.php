<?php 

namespace laxer\shop;

use pocketmine\utils\Config;
use laxer\Core;
use pocketmine\level\Position;
use pocketmine\tile\Sign;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;

class Shop {
    
    private $items = [];
    
    public function getConfig(){
        return new Config(Core::getInstance()->getDataFolder().'shop/shop.json', Config::JSON);
    }
    
    public function addShop(Position $pos, int $item_id, int $damage, int $price){
        $conf = $this->getConfig();
        $data = $conf->getAll();
        $data[] = [
            'pos' => [$pos->x, $pos->y, $pos->z, $pos->level->getId()],
            'data' => [$item_id, $damage],
            'price' => $price
        ];
        $conf->setAll($data);
        $conf->save();   
    }
    
    public function getSigns(){
        return $this->getConfig()->getAll();
    }
    
    public function setSignText(Sign $sign, array $shop){
        $discount = (int) Core::getInstance()->getConfig()->get('shop-discount');
        $item = Item::get($shop['data'][0],$shop['data'][1]);
        $price = $shop['price'];
        $price = $price - (($price*$discount)/100);
        $sign->setText("",TextFormat::colorize('&f&l'.$item->getName()),TextFormat::colorize("&l&7$".floor($price)),"");
    }
    
    public function loadSigns(){
        $shops = $this->getConfig()->getAll();
        foreach ($shops as $id => $shop){
            $pos = $shop['pos'];
            $sign = Core::getInstance()->getServer()->getLevel($pos[3])->getTileAt($pos[0], $pos[1], $pos[2]);
            $this->setSignText($sign, $shop);
        }
    }
    
    public function isExist(Position $pos){
        $shops = $this->getConfig()->getAll();
        foreach ($shops as $id => $shop){
            $poss = $shop['pos'];
            $sign = Core::getInstance()->getServer()->getLevel($poss[3])->getTileAt($poss[0], $poss[1], $poss[2]);
            if ($sign->level->getId() == $pos->level->getId() && $sign->asVector3()->equals($pos->asVector3())) return true;
        }
        return false;
    }
    
    public function getShop(Position $pos){
        $shops = $this->getConfig()->getAll();
        foreach ($shops as $id => $shop){
            $poss = $shop['pos'];
            $sign = Core::getInstance()->getServer()->getLevel($poss[3])->getTileAt($poss[0], $poss[1], $poss[2]);
            if ($sign->level->getId() == $pos->level->getId() && $sign->asVector3()->equals($pos->asVector3())){
                return $shop;
            }
        }
        return null;
    }
    
    public function unsetSign(int $id){
        $conf = $this->getConfig();
        $data = $conf->getAll();
        unset($data[$id]);
        $conf->setAll($data);
        $conf->save();
    }
    
}


<?php 

namespace laxer\pshop;

use pocketmine\utils\Config;
use laxer\Core;
use pocketmine\tile\Chest;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\tile\Sign;
use pocketmine\item\Item;
use laxer\ui\CoreUI;

class Shop {
    
    private $conf;
    
    public function __construct(String $name){
        $hashname = hash('md5', strtolower($name));
        if (!Core::getInstance()->getPShop()->hasCreated($name)){
            $this->conf = new Config(Core::getInstance()->getDataFolder().'shops/'.$hashname.'.json', Config::JSON);
            $data = [
                "name" => "",
                "owner" => $name,
                "points" => 0,
                "max_chest" => 3,
                "max_helper" => 1,
                "chests" => [],
                "helpers" => [],
                "items_price" => [],
                "signs" => [],
            ];
            $conf = $this->conf;
            $conf->setAll($data);
            $conf->save();
        }
        $this->conf = new Config(Core::getInstance()->getDataFolder().'shops/'.$hashname.'.json', Config::JSON);
    }
    
    private function set($k, $v){
        $conf = $this->conf;
        $conf->set($k, $v);
        return $conf->save();
    }
    
    private function get($k){
        return $this->conf->get($k);
    }
    
    public function setName(String $name){
        return $this->set("name", $name);
    }
    
    public function getName(){
        $name = $this->get("name");
        if ($name == ""){
            $name = $this->getOwner()->getName();
        }
        return $name;
    }
    
    public function getOwner(){
        $name = $this->get("owner");
        $p = Core::getInstance()->getServer()->getPlayer($name);
        if (is_null($p)){
            $p = Core::getInstance()->getServer()->getOfflinePlayer($name);
        }
        return $p;
    }
    
    public function isOwner(Player $p){
        $name = $this->get('owner');
        return $name == $p->getName();
    }
    
    public function getPoint(){
        return $this->get('points');
    }
    
    public function setPoint(int $v){
        return $this->set('points', $v);
    }
    
    public function setMaxChest(int $v){
        return $this->set('max_chest', $v);
    }
    
    public function getMaxChest(){
        return $this->get('max_chest');
    }
    
    public function setMaxHelper(int $v){
        return $this->set('max_chest', $v);
    }
    
    public function getMaxHelper(){
        return $this->get('max_chest');
    }
    
    public function setChestShop(Chest $chest){
        if (!$this->isChestShop($chest)){
            $data = $this->get('chests');
            $pos = $chest->asPosition();
            $data[] = [
                "x" => $pos->x,
                "y" => $pos->y,
                "z" => $pos->z,
                "level_id" => $pos->level->getId(),
            ];
            $this->set('chests', $data);
            return true;
        }
        return false;
    }
    
    public function isChestShop(Chest $chest){
        $pos = $chest->asPosition();
        $data = $this->get('chests');
        foreach ($data as $v){
            $level = Core::getInstance()->getServer()->getLevel($v['level_id']);
            $pos2 = new Position($v['x'], $v['y'], $v['z'], $level);
            if ($pos->level->getId() == $v['level_id'] && $pos->equals($pos2->asVector3())){
                return true;
            }
        }
        return false;
    }
    
    public function unsetChestShop(Chest $chest){
        if ($this->isChestShop($chest)){
            $data = $this->get('chests');
            $pos = $chest->asPosition();
            foreach ($data as $i => $v){
                $level = Core::getInstance()->getServer()->getLevel($v['level_id']);
                $pos2 = new Position($v['x'], $v['y'], $v['z'], $level);
                if ($pos->level->getId() == $v['level_id'] && $pos->equals($pos2->asVector3())){
                    unset($data[$i]);
                }
            }
            $this->set('chests', $data);
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @return \pocketmine\tile\Chest[]
     */
    public function getAllChest(){
        $data = $this->get('chests');
        $chests = [];
        foreach ($data as $v){
            $level = Core::getInstance()->getServer()->getLevel($v['level_id']);
            $pos = new Position($v['x'], $v['y'], $v['z'], $level);
            $chests[] = $level->getTile($pos);
        }
        return $chests;
    }
    
    public function setHelper(Player $p){
        $name = $p->getName();
        if (!$this->isHelper($name)){
            $data = $this->get('helpers');
            $data[$name] = true;
            $this->set('helpers', $data);
            return true;
        }
        return false;
    }
    
    public function unsetHelper(String $name){
        if ($this->isHelper($name)){
            $data = $this->get('helpers');
            unset($data[$name]);
            $this->set('helpers', $data);
            return true;
        }
        return false;
    }
    
    public function isHelper(String $name){
        $data = $this->get('helpers');
        return isset($data[$name]);
    }

    public function updateSign(Sign $sign){
        if ($this->issetSign($sign)){
            $itd = $this->getDataItemBySign($sign);
            $item = Item::get($itd[0],$itd[1]);
            $data = CoreUI::FPShopSign($this, $item->getName());
            $sign->setText(...$data);
        }
    }
    
    public function updateSigns(){
        $data = $this->get('signs');
        foreach ($data as $v){
            $level = Core::getInstance()->getServer()->getLevel($v['level_id']);
            $pos = new Position($v['x'], $v['y'], $v['z'], $level);
            $t = $level->getTile($pos);
            if (!is_null($t)){
                if ($t instanceof Sign){
                    $this->updateSign($t);
                }
            }
        }
        return true;
    }
    
    public function setSign(Sign $sign, int $id, int $meta){
        if (!$this->issetSign($sign)) {
            $data = $this->get('signs');
            $pos = $sign->asPosition();
            $data[] = [
                "x" => $pos->x,
                "y" => $pos->y,
                "z" => $pos->z,
                "level_id" => $pos->level->getId(),
                'item' => [$id, $meta]
            ];
            $this->set('signs', $data);
            return true;
        }
        return false;
    }
    
    public function unsetSign(Sign $sign){
        if ($this->issetSign($sign)) {
            $data = $this->get('signs');
            $pos = $sign->asPosition();
            foreach ($data as $i => $v){
                $level = Core::getInstance()->getServer()->getLevel($v['level_id']);
                $pos2 = new Position($v['x'], $v['y'], $v['z'], $level);
                if ($pos->level->getId() == $v['level_id'] && $pos->equals($pos2->asVector3())){
                    unset($data[$i]);
                }
            }
            $this->set('signs', $data);
            return true;
        }
        return false;
    }
    
    public function issetSign(Sign $sign){
        $pos = $sign->asPosition();
        $data = $this->get('signs');
        foreach ($data as $v){
            $level = Core::getInstance()->getServer()->getLevel($v['level_id']);
            $pos2 = new Position($v['x'], $v['y'], $v['z'], $level);
            if ($pos->level->getId() == $v['level_id'] && $pos->equals($pos2->asVector3())){
                return true;
            }
        }
        return false;
    }
    
    public function setPrice(int $id, int $damage, int $price){
        $data = $this->get("items_price");
        $item = Item::get($id,$damage);
        $data[$item->getName()] = $price;
        return $this->set('items_price', $data);
    }
    
    public function getPrice(String $item_name){
        $data = $this->get("items_price");
        if (isset($data[$item_name])) {
            return $data[$item_name];
        }
        return 0;
    }
    
    public function getCountItem($item_name){
        $chests = $this->getAllChest();
        $count = 0;
        foreach ($chests as $chest){
            $inv = $chest->getInventory();
            $contents = $inv->getContents();
            foreach ($contents as $item){
                if ($item->getName() == $item_name) $count += $item->getCount();
            }
        }
        return $count;
    }
    
    public function reduceItem(int $id, int $damage, int $amount){
        $chests = $this->getAllChest();
        $item = Item::get($id,$damage);
        foreach ($chests as $chest){
            $inv = $chest->getInventory();
            foreach ($inv->getContents() as $itemc){
                if ($item->getName() == $itemc->getName()){
                    $cal = $itemc->getCount() - $amount;
                    if ($cal < 0) {
                        $amount -= $itemc->getCount();
                        $inv->remove($itemc);
                    }else {
                        $i = $itemc->setCount($cal);
                        $inv->remove($itemc);
                        $inv->addItem($i);
                        $amount = 0;
                        return true;
                    }
                }
            }
        }
    }
    
    public function getAllItems(){
        $chests = $this->getAllChest();
        $items = [];
        foreach ($chests as $chest){
            $inv = $chest->getInventory();
            foreach ($inv->getContents() as $item){
                if (!in_array($item->getName(), $items)){
                    $items[] = $item->getName();
                }
            }
        }
        return $items;
    }
    
    public function getDataItemBySign(Sign $sign){
        if ($this->issetSign($sign)) {
            $data = $this->get('signs');
            $pos = $sign->asPosition();
            foreach ($data as $i => $v){
                $level = Core::getInstance()->getServer()->getLevel($v['level_id']);
                $pos2 = new Position($v['x'], $v['y'], $v['z'], $level);
                if ($pos->level->getId() == $v['level_id'] && $pos->equals($pos2->asVector3())){
                    return [$v['item'][0], $v['item'][1]];
                }
            }
        }
        return false;
    }
    
    public function reset(){
        $data = [
            "chests" => [],
            "items_price" => [],
            "signs" => [],
            "helpers" => []
        ];
        foreach ($data as $k => $v){
            $this->set($k, $v);
        }
        return true;
    }
    
}
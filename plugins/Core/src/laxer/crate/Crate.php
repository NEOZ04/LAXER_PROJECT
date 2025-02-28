<?php 

namespace laxer\crate;

use pocketmine\utils\Config;
use laxer\Core;
use pocketmine\level\Position;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\utils\TextFormat;
use laxer\player\PlayerData;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\tile\EnderChest;
use pocketmine\inventory\ContainerInventory;
use laxer\libs\muqsit\invmenu\InvMenu;
use laxer\libs\muqsit\invmenu\inventories\ChestInventory;

class Crate {
    
    /**
     * 
     * @var FloatingTextParticle[]
     */
    public $particles = []; 
    
    private function getConfig() {
        return new Config(Core::getInstance()->getDataFolder().'crates.json', Config::JSON);
    }
    
    public function loadCrates(){
        $crates = $this->getConfig()->getAll();
        if (!empty($this->particles)){
            $particles = $this->particles;
            foreach ($particles as $v){
                $level = Core::getInstance()->getServer()->getLevel($v[1]);
                $particle = $v[0];
                $particle->setInvisible();
                $level->addParticle($particle);
            }
            $this->particles = [];
        }
        foreach ($crates as $crate){
            $pos = $crate['pos'];
            $tile = Core::getInstance()->getServer()->getLevel($pos[0])->getTileAt($pos[1], $pos[2], $pos[3]);
            $pos = new Position($pos[1]+0.5,$pos[2]+1,$pos[3]+0.5,$tile->level);
            $players = Core::getInstance()->getServer()->getOnlinePlayers();
            foreach ($players as $player){
                $keys = $this->getKey($player->getName(), $crate['type']);
                $text = $this->getText($crate['type'], $keys);
                $particle = new FloatingTextParticle($pos, $text[0], $text[1]);
                $tile->level->addParticle($particle);
                $this->particles[] = [$particle,$tile->level->getId()];
            }
        }
    }
    
    private function getText(int $type, int $keys = 0){
        $text = '';
        $title = '';
        switch ($type){
            case 0: 
                $title = TextFormat::colorize('&l&5PLATINUM');
                $text = TextFormat::colorize('&l&fKey: '.$keys);
                break;
            case 1: 
                $title = TextFormat::colorize('&l&6REGULAR');
                $text = TextFormat::colorize('&l&fKey: '.$keys);
                break;
            case 2: 
                $title = TextFormat::colorize('&l&3GENERAL');
                $text = TextFormat::colorize('&l&fKey: '.$keys);
                break;
            case 3: 
                $title = TextFormat::colorize('&l&bDAILY');
                $text = TextFormat::colorize('&l&fKey: '.$keys);
                break;
        }
        return [$text,$title];
    }
    
    private function get($k){
        return $this->getConfig()->get($k);
    }
    
    public function isCrate(Position $pos){
        $conf = $this->getConfig();
        $data = $conf->getAll();
        foreach ($data as $id => $v){
            $vpos = $v['pos'];
            $crate = Core::getInstance()->getServer()->getLevel($vpos[0])->getTileAt($vpos[1], $vpos[2], $vpos[3]);
            if ($pos->level->getId() === $crate->level->getId() && $pos->equals($crate->asVector3())){
                return true;
            }
        }
        return false;
    }
    
    public function setCrate(Position $pos, int $type){
        if ($this->isCrate($pos)) {
            return false;
        }
        $conf = $this->getConfig();
        $data = $this->getConfig()->getAll();
        $data[] = [
            'pos' => [$pos->level->getId(), $pos->x, $pos->y, $pos->z],
            'type' => $type
        ];
        $conf->setAll($data);
        return $conf->save();
    }
    
    public function getTypes(){
        $types = [
            'Platinum',
            'Regular',
            'General',
            'Daily',
        ];
        return $types;
    }
    
    public function getCrateId(Position $pos){
        if (!$this->isCrate($pos)) {
            return null;
        }
        $conf = $this->getConfig();
        $data = $this->getConfig()->getAll();
        foreach ($data as $id => $v){
            $vpos = $v['pos'];
            $crate = Core::getInstance()->getServer()->getLevel($vpos[0])->getTileAt($vpos[1], $vpos[2], $vpos[3]);
            if ($pos->level->getId() === $crate->level->getId() && $pos->equals($crate->asVector3())){
                return $id;
            }
        }
        return null;
    }
    
    public function unsetCrate(Position $pos){
        if (!$this->isCrate($pos)) {
            return false;
        }
        $conf = $this->getConfig();
        $data = $this->getConfig()->getAll();
        foreach ($data as $id => $v){
            $vpos = $v['pos'];
            $crate = Core::getInstance()->getServer()->getLevel($vpos[0])->getTileAt($vpos[1], $vpos[2], $vpos[3]);
            if ($pos->level->getId() === $crate->level->getId() && $pos->equals($crate->asVector3())){
                unset($data[$id]);
                $conf->setAll($data);
                return $conf->save();
            }
        }
        return false;
    }
    
    public function OpenCrate(Player $p, Position $pos){
        $conf = $this->getConfig();
        $crateId = $this->getCrateId($pos);
        if ($crateId === null){
            return null;
        }
        $crate = $conf->getAll()[$crateId];
        $key = (int) $this->getKey($p->getName(), $crateId);
        if ($key > 0){
            $rewards = $this->getReward($crate['type']);
            $inv = InvMenu::create(InvMenu::TYPE_CHEST);
            $inv->readonly();
            $items = $rewards;
            $inv->getInventory()->setContents($items);
            $inv->setInventoryCloseListener(function (Player $p, ChestInventory $inv){
                $items = $inv->getContents();
                $p->getInventory()->addItem(...$items);
            });
            $inv->send($p);
            $key -= 1;
            return $this->setKey($p->getName(), $crateId, $key);
        }
        return false;
    }
    
    public function getKey(String $p_name, int $type){
        $pd = new PlayerData($p_name);
        $key = $pd->get('keys')[$type];
        return $key;
    }
    
    public function setKey(String $p_name, int $type, int $amount){
        $pd = new PlayerData($p_name);
        $keys = $pd->get('keys');
        $keys[$type] = $amount;
        return $pd->set('keys', $keys);
    }
    
    public function getReward(int $type){
        $reward = Core::getInstance()->getConfig()->get('crate-rewards')[$type];
        $rewards = [];
        $cReward = Core::getInstance()->getConfig()->get('crate-count-rewards');
        $c = rand(1,$cReward[$type]);
        for ($i=0;$i<$c;$i++){
            $item = $reward[rand(0,count($reward)-1)];
            $amount = rand(1,$item[2]);
            $item = Item::get($item[0],$item[1],$amount);
            $rewards[] = $item;
        }
        return $rewards;
    }
    
}
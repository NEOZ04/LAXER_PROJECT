<?php

namespace laxer;

use pocketmine\plugin\PluginBase;
use laxer\guild\GuildManager;
use laxer\economies\Money;
use pocketmine\utils\Config;
use laxer\player\PlayerRank;
use laxer\cmds\SpawnCommand;
use laxer\shop\Shop;
use laxer\crate\Crate;
use laxer\libs\muqsit\invmenu\InvMenuHandler;
use laxer\pshop\PShop;
use pocketmine\tile\Chest;
use laxer\world\WorldManager;
use laxer\cmds\FlyCommand;
use laxer\cmds\TeleportCommand;
use laxer\pet\Pet;

class Core extends PluginBase {
    
    private static $instance;
    private static $playersphone = [];
    private $guildmanager;
    private $moneyaapi;
    private $shop;
    private $pshop;
    private $crate;
    private $worldmanager;
    private $mypet;
    private $usermgr;
    public static $_SESSIONS = [];
    
    public function getEvent($k){
        $events = [
            'guild' => false
        ];
        return $events[$k];
    }
    
    public function onEnable() {
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }
        @mkdir($this->getDataFolder().'guilds');
        @mkdir($this->getDataFolder().'players');
        @mkdir('backup/worlds');
        @mkdir($this->getDataFolder().'shops');
        @mkdir($this->getDataFolder().'shop');
        
        $this->saveDefaultConfig();
        new Config($this->getDataFolder().'TimeRankConfig.json', Config::JSON, [
            "Junior" => 60,
            "Master" => 86400,
            "Pro" => 604800
        ]);
        
        self::$instance = $this;
        $this->getLogger()->notice("Plugin has been started...");
        $this->addVersion();
        
        // Register EventListeners
        $events = new CoreListeners();
        $events->registerAll($this);
        $this->guildmanager = new GuildManager();
        $this->moneyaapi = new Money();
        $this->crate = new Crate();
        $this->shop = new Shop();
        $this->pshop = new PShop();
        $this->userrank = new PlayerRank();
        $this->mypet = new Pet();
        $this->worldmanager = new WorldManager();
        
        // Register Commands
        $this->getServer()->getCommandMap()->register('Spawn', new SpawnCommand('spawn', 'back to spawn', 'spawn', ['hub']));
        $this->getServer()->getCommandMap()->register('Fly', new FlyCommand('fly', 'Allow to flight', 'fly', []));
        $this->getServer()->getCommandMap()->register('Teleport Player', new TeleportCommand('tpa', 'Teleport', 'tpa <target_name> [on_your_pos:bool]', []));
        
        $this->shop->loadSigns();
        $this->crate->loadCrates();
        $this->pshop->loadSigns();
        
//         $td = strtotime(date('d-m-Y'));
//         $tm = $td + (60*60*60*24) + (60*60*60*24);
//         var_dump((($tm-$tm)/24/60/60/60) > 1);
//         $this->getLogger()->warning(date('d-m-y H:i:s',$td));
    }
    
    public function addVersion(){
        $conf = new Config($this->getServer()->getPluginPath().'Core/plugin.yml');
        
        $v = $conf->get('version');
        $vexp = explode('.', $v);
        $a = (int) $vexp[2];
        $a++;
        $vexp[2] = $a;
        $v = $vexp[0].'.'.$vexp[1].'.'.$vexp[2];
        $conf->set('version', $v);
        $conf->save();
    }
    
    public function userRank(){
        return $this->userrank;
    }
    
    public function getShop(){
        return $this->shop;
    }
    
    public function getPShop(){
        return $this->pshop;
    }
    
    public static function getInstance(){
        return self::$instance;
    }
    
    public function getGuild(){
        return $this->guildmanager;
    }
    
    public function getPet(){
        return $this->mypet;
    }
    
    public function getMoneyAPI(){
        return new Money();
    }
    
    public function getCrate(){
        return $this->crate;
    }
    
}
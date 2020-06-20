<?php

namespace laxer;

use laxer\cmds\FeedCommand;
use laxer\cmds\FlyCommand;
use laxer\cmds\HealCommand;
use laxer\cmds\SpawnCommand;
use laxer\cmds\TeleportCommand;
use laxer\cmds\home\GetHomeCommand;
use laxer\cmds\home\HomeCommand;
use laxer\cmds\home\SetHomeCommand;
use laxer\cmds\home\UnsetHomeCommand;
use laxer\crate\Crate;
use laxer\economies\Money;
use laxer\guild\GuildManager;
use laxer\libs\muqsit\invmenu\InvMenuHandler;
use laxer\pet\Pet;
use laxer\player\PlayerRank;
use laxer\pshop\PShop;
use laxer\shop\Shop;
use laxer\world\WorldManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use laxer\area\AreaManager;
use pocketmine\math\Vector3;
use laxer\cmds\area\AreaCommand;
use laxer\pvp\ovso;
use laxer\pvp\PvP;

class Core extends PluginBase {
    
    private static $instance;
    private static $playersphone = [];
    private $guildmanager;
    private $moneyaapi;
    private $shop;
    private $pshop;
    private $lang = 'id';
    private $crate;
    private $worldmanager;
    private $mypet;
    private $areamanager;
    private $pvp;
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
        @mkdir($this->getDataFolder().'messages');
        
        $this->saveDefaultConfig();
        $this->saveResource('messages/id.json')
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
        $this->pvp = new PvP();
        $this->areamanager = new AreaManager();
        $this->mypet = new Pet();
        $this->worldmanager = new WorldManager();
        
        $levels = ['survival','plots','flat'];
        foreach ($levels as $name){
            $this->getServer()->loadLevel($name);
        }
        
        $conf = new Config(Core::getInstance()->getDataFolder().'arenas.json',Config::JSON);
        $conf->set('arena-1',[
            "name" => "Arena 1",
            "positions" => [
                "level" => 1,
                "players" => [-5,4,0],[5,4,0]
            ],
        ]);
        $conf->save();
        
        // Register Commands
        $this->getServer()->getCommandMap()->register('Spawn', new SpawnCommand('spawn', 'back to spawn', 'spawn', ['hub']));
        $this->getServer()->getCommandMap()->register('Fly', new FlyCommand('fly', 'Allow to flight', 'fly', []));
        $this->getServer()->getCommandMap()->register('Feed', new FeedCommand('feed', 'Fill a food', 'feed', []));
        $this->getServer()->getCommandMap()->register('Area', new AreaCommand('area', 'Area commands', 'area', []));
        $this->getServer()->getCommandMap()->register('Heal', new HealCommand('heal', 'Fill a health', 'heal', []));
        $this->getServer()->getCommandMap()->register('SetHome', new SetHomeCommand('sethome', 'Set new home position', 'sethome', []));
        $this->getServer()->getCommandMap()->register('Home', new HomeCommand('home', 'Teleport to home', 'home', []));
        $this->getServer()->getCommandMap()->register('UnsetHome', new UnsetHomeCommand('unsethome', 'Unset to home', 'unsethome', []));
        $this->getServer()->getCommandMap()->register('Homes', new GetHomeCommand('homes', 'Get all homes', 'homes', []));
        $this->getServer()->getCommandMap()->register('Teleport Player', new TeleportCommand('tpa', 'Teleport', 'tpa <target_name> [on_your_pos:bool]', []));
        $this->shop->loadSigns();
        $this->crate->loadCrates();
        $this->pshop->loadSigns();
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
    
    public function getPvP(){
        return $this->pvp;
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
    
    public function getAreaManager(){
        return $this->areamanager;
    }
    
    public function getMoneyAPI(){
        return new Money();
    }
    
    public function getCrate(){
        return $this->crate;
    }
    
}
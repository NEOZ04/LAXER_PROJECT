<?php 

namespace laxer\guild;

use pocketmine\Player;
use laxer\Core;
use pocketmine\utils\Config;

class GuildManager {
    
    public function createGuild(Player $p, String $name, String $alias){
        $leader_name = $p->getName();
        if (!$this->guildExists($name)){
            if (strlen($alias) > 4) {
                return 1;
            }
            if ($this->isAliasExist($alias)){
                return 2;
            }
            $guild_name = strtolower($name);
            $hashName = trim(hash('md5', $guild_name), ' ');
            $filepath = Core::getInstance()->getDataFolder().'guilds/'.$hashName.'.json';
            $conf = new Config($filepath, Config::JSON);
            $data = [
                'name' => $name,
                'leader' => $leader_name,
                'alias' => strtoupper($alias),
                'points' => 0,
                'officers' => [],
                'members' => [
                    $leader_name => true
                ],
                'base' => []
            ];
            foreach ($data as $k => $v){
                $conf->set($k, $v);
            }
            $conf->save();
            Core::getInstance()->getLogger()->notice('New Guild: '.$name.', leader: '.$leader_name);
            return true;
        }
        return 0;
    }
    
    private function isAliasExist(String $alias){
        foreach ($this->getAllGuild() as $guild){
            if ($guild->getAlias() == $alias) {
                return true;
            }
        }
        return false;
    }
    
    public function removeGuild(String $name){
        if ($this->guildExists($name)){
            $guild_name = strtolower($name);
            $hashName = trim(hash('md5', $guild_name), ' ');
            $filepath = Core::getInstance()->getDataFolder().'guilds/'.$hashName.'.json';
            $members = $this->getGuild($name)->getMembers();
            unlink($filepath);
            Core::getInstance()->getLogger()->notice('Remove Guild: '.$name);
            return $members;
        }
        return false;
    }
    
    public function isInGuild(String $p_name){
        foreach ($this->getAllGuild() as $guild){
            if ($guild->isMember($p_name)){
                return true;
            }
        }
        return false;
    }
    
    public function guildExists(String $name){
        $guild_name = strtolower($name);
        $hashName = trim(hash('md5', $guild_name), ' ');
        $filepath = Core::getInstance()->getDataFolder().'guilds/'.$hashName.'.json';
        return file_exists($filepath);
    }
    
    public function getGuild(String $name){
        if ($this->guildExists($name)) {
            return new Guild($name);
        }
        return null;
    }
    
    public function getAllGuild(){
        $files = scandir(Core::getInstance()->getDataFolder().'guilds/');
        $guilds = [];
        foreach ($files as $filename){
            $fileExp = explode('.', $filename);
            $ext = end($fileExp);
            if (strtolower($ext) == 'json'){
                $conf = new Config(Core::getInstance()->getDataFolder().'guilds/'.$filename);
                $guilds[] = $this->getGuild($conf->get('name')); 
            }
        }
        return $guilds;
    }
    
    public function getPlayerGuild(String $p_name){
        foreach ($this->getAllGuild() as $guild){
            if ($guild->isMember($p_name)){
                return $guild;
            }
        }
        return null;
    }
    
}
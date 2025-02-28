<?php 

namespace laxer\pet;

use pocketmine\Player;
use laxer\Core;
use laxer\player\PlayerData;

class Pet {
    
    public const COW = 'cow';
    
    public function getPets(){
        
    }
    public function getPlayerPets(string $name){
        $this->addPet($name, self::COW);
        $pd = new PlayerData($name);
        $pd->addAttribute('pets', []);
        $pets = $pd->get('pets');
        $rows = []; 
        foreach ($pets as $type => $v){
            $rows[] = $type;
        }
        return $rows;
    }
    
    public function getPet(string $type){
        $type = strtolower($type);
    }
    
    public function getSpawningPet(Player $p){
        
    }
    
    public function addPet(string $name, string $type){
        $pd = new PlayerData($name);
        $pd->addAttribute('pets', []);
        $pets = $pd->get('pets');
        $pets[$type] = [];
        $pd->set('pets', $pets);
    }
    
}
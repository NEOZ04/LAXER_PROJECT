<?php 

namespace laxer\economies;

use laxer\player\PlayerData;

class Money {
    
    private $data;
    
    public function init(String $p_name){
        $this->data = new PlayerData($p_name);
    }
    
    public function getMoney(String $p_name){
        $this->init($p_name);
        return $this->data->get('money');
    }
    
    public function setMoney(String $p_name, int $v){
        $this->init($p_name);
        return $this->data->set('money', $v);
    }
    
    public function addMoney(String $p_name, int $v){
        $this->init($p_name);
        return $this->setMoney($p_name, $this->getMoney($p_name)+$v);
    }
    
    public function reduceMoney(String $p_name, int $v){
        $this->init($p_name);
        return $this->setMoney($p_name, $this->getMoney($p_name)-$v);
    }
    
    public function payMoney(String $p_name, String $target_name, int $v){
        $this->init($p_name);
        $data = new Money($target_name);
        return $data->addMoney($p_name, $v) && $this->reduceMoney($p_name, $v);
    }
    
}
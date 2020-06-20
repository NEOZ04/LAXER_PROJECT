<?php 

namespace laxer\proshop;

use pocketmine\Player;
use pocketmine\utils\Config;
use laxer\Core;
use laxer\ui\CoreUI;
use pocketmine\utils\TextFormat;
use pocketmine\item\Item;
use pocketmine\block\Block;

class ProShop {
    
    private $category = null;
    private $item = null;
    
    private function getDefault(){
        $data = [
            "Foods" => [
                [0,0,0]
            ],
            "Weapons" => [
                [0,0,0]
            ],
            "Armors" => [
                [0,0,0]
            ],
            "Woods" => [
                [0,0,0]
            ],
        ];
        return $data;
    }
    
    private function getConfig(){
        return new Config(Core::getInstance()->getDataFolder().'shop/ProShop.json', Config::JSON, $this->getDefault());
    }
    
    private function getCategories(){
        $data = $this->getConfig()->getAll();
        $categories = [];
        foreach ($data as $c => $v) $categories[] = $c; 
        return $categories;
    }
    
    private function getItems(string $category){
        return $this->getConfig()->get($category);
    }
    
    public function getMainForm(Player $p){
        $ui = CoreUI::Simple(function(Player $p, $data){
            if ($data === null) return false;
            $this->category = $this->getCategories()[$data];
            $this->getItemsForm($p);
        });
        $ui->setTitle(CoreUI::fTitle('PRO SHOP'));
        $btns = $this->getCategories();
        foreach ($btns as $text){
            $ui->addButton(CoreUI::fButton($text));
        }
        $ui->sendToPlayer($p);
    }
    
    public function getItemsForm(Player $p){
        $ui = CoreUI::Simple(function(Player $p, $data){
            if ($data === null) return false;
            if ($data === 0) {
                $this->getMainForm($p);
            }else {
                $this->item = $this->getItems($this->category)[$data-1];
                $this->getConfirmForm($p);
            }
        });
        $ui->setTitle(CoreUI::fTitle('PRO SHOP'));
        $btns = $this->getItems($this->category);
        $ui->addButton(CoreUI::fBButton('KEMBALI'));
        foreach ($btns as $v){
            $item = Item::get($v[0], $v[1]);
            $ui->addButton(CoreUI::fButton($item->getName()),0,CoreUI::getItemImage($v[0], $v[1]));
        }
        $ui->sendToPlayer($p);
    }
    
    public function getConfirmForm(Player $p){
        $ui = CoreUI::Custom(function (Player $p, $data){
            if ($data === null) {
                $this->getItemsForm($p);
                return false;
            }
            $pm = Core::getInstance()->getMoneyAPI();
            $money = $pm->getMoney($p->getName());
            $dataI = $this->item;
            $price = ((int)$dataI[2]) * $data[0];
            $item = Item::get($dataI[0],$dataI[1],$data[0]);
            if ($money >= $price){
                $pm->reduceMoney($p->getName(), $price);
                $p->getInventory()->addItem($item);
                $p->sendMessage(CoreUI::Success('Anda berhasil membeli '.$item->getName().' sejumlah '.$data[0].' dengan harga $'.$price));
                return true;
            }
            $p->sendMessage(CoreUI::Danger('Uang anda tidak cukup untuk membeli '.$item->getName().' sejumlah '.$data[0].'  dengan harga $'.$price));
            return true;
        });
        $item = Item::get($this->item[0], $this->item[1]);
        $price = $this->item[2];
        $ui->setTitle(CoreUI::fTitle('PRO SHOP'));
        $ui->addSlider(TextFormat::colorize('&l&f•> Jumlah'), 1,$item->getMaxStackSize());
        $ui->addLabel(TextFormat::colorize('&l&f•> Price: '.$price));
        $ui->sendToPlayer($p);
    }
    
}
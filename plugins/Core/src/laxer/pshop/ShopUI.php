<?php 

namespace laxer\pshop;

use pocketmine\Player;
use laxer\ui\CoreUI;
use pocketmine\tile\Sign;
use pocketmine\utils\TextFormat;
use pocketmine\item\Item;
use laxer\Core;

class ShopUI {
    
    private $shop;
    private $item;
    
    public function __construct(Shop $shop){
        $this->shop = $shop;    
    }
    
    public function getMainForm(Player $p, Array $data){
        $ui = CoreUI::Custom(function (Player $p, $data){
           if (is_null($data))  return false;
           $item = $this->item;
           $amount = $data[0];
           $price = ( (int)$this->shop->getPrice($item->getName()) ) * $amount;
           $pm = Core::getInstance()->getMoneyAPI();
           $money = $pm->getMoney($p->getName());
           if ($money >= $price){
               $this->shop->reduceItem($item->getId(), $item->getDamage(), $amount);
               $pm->payMoney($p->getName(), $this->shop->getOwner()->getName(), $price);
               $p->sendMessage(CoreUI::Success('Anda berhasil membeli '.$item->getName().' sejumlah '.$data[0].' dengan harga $'.$price));
               $t = $this->shop->getOwner();
               if ($t->isOnline()) {
                   $t->sendMessage(CoreUI::Success($p->getName().' berhasil membeli '.$item->getName().' sejumlah '.$data[0].' dengan harga $'.$price));
               }
               $this->shop->updateSigns();
               return true;
           }
           $p->sendMessage(CoreUI::Danger('Uang anda tidak cukup untuk mengubah nama seharga '.$price));
        });
        $item = Item::get($data[0], $data[1]);
        $this->item = $item;
        $count = $this->shop->getCountItem($item->getName());
        if ($count > $item->getMaxStackSize()) {
            $count = $item->getMaxStackSize();
        }
        $price = $this->shop->getPrice($item->getName());
        $ui->setTitle(CoreUI::fTitle($this->shop->getName()));
        $ui->addSlider(TextFormat::colorize('&l&f•> Amount'), 1, $count);
        $ui->addLabel(TextFormat::colorize('&l&f•> Price: '.$price));
        $ui->sendToPlayer($p);
    }
    
}
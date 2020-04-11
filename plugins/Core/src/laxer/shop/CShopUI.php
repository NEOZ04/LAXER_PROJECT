<?php 

namespace laxer\shop;

use pocketmine\tile\Sign;
use pocketmine\Player;
use laxer\ui\CoreUI;
use laxer\Core;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class CShopUI {
    
    private $sign;
    private $shop;
    private $data;
    
    public function __construct(Sign $sign){
        $this->sign = $sign;
        $this->shop = Core::getInstance()->getShop();
    }
    
    public function getMainForm(Player $p){
        $ui = CoreUI::Custom(function (Player $p, $data){
            if ($data === null) return false;
            $this->shop->addShop($this->sign->asPosition(), (int)$data[0], (int)$data[1], (int)$data[2]);
            $this->shop->loadSigns();
        });
        $ui->setTitle('Create Shop');
        $ui->addInput('id');
        $ui->addInput('damage');
        $ui->addInput('price');
        $ui->sendToPlayer($p);
    }
    
    public function getShop(Player $p, array $shop){
        $this->data = $shop;
        $ui = CoreUI::Custom(function (Player $p, $data){
            if ($data === null) return false;
            $pm = Core::getInstance()->getMoneyAPI();
            $money = (int) $pm->getMoney($p->getName());
            $shop = $this->data;
            $price = (int )$shop['price'];
            $discount = Core::getInstance()->getConfig()->get('shop-discount');
            $price = $price - (($price*$discount)/100);
            $item = Item::get($shop['data'][0],$shop['data'][1],(int)$data[0]);
            $price = floor($price) * (int) $data[0];
            
            if ($money >= $price) {
                $pm->reduceMoney($p->getName(), $price);
                $p->getInventory()->addItem($item);
                $p->sendMessage(CoreUI::Success('Anda berhasil membeli '.$item->getName().' sejumlah '.$data[0].' dengan harga $'.$price));
                return true;
            }
            $p->sendMessage(CoreUI::Danger('Uang anda tidak cukup untuk membeli '.$item->getName().' sejumlah '.$data[0].'  dengan harga $'.$price));
            return false;
        });
        $discount = Core::getInstance()->getConfig()->get('shop-discount');
        $item = Item::get($shop['data'][0],$shop['data'][1]);
        $price = (int) $shop['price'];
        $price = $price - (($price*$discount)/100);
        $ui->setTitle(CoreUI::fTitle($item->getName()));
        $ui->addSlider(TextFormat::colorize('&l&f•> Jumlah'), 1, $item->getMaxStackSize());
        $ui->addLabel(TextFormat::colorize('&l&f•> Harga: '.$price));
        $ui->sendToPlayer($p);
    }
    
}
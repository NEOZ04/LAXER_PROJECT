<?php 

namespace laxer\pshop;

use pocketmine\Player;
use laxer\ui\CoreUI;
use laxer\Core;
use pocketmine\utils\TextFormat;
use pocketmine\item\Item;

class PShopUI {
    
    private $p;
    private $shop;
    private $data = [];
    
    private function getButtons(){
        $p = $this->p;
        $btns = [];
        if ($this->getShop()->isOwner($p)) {
            $btns = [
                "Profil", "Set Name", "Items Price", "Helpers", "Set Chest", "Set Sign"
            ];
        }elseif ($this->getShop()->isHelper($p->getName())){
            $btns = [
                "Profil", "Items Price", "Helpers", "Set Chest", "Set Sign"
            ];
        }
        return $btns;
    }
    
    public function getShop(){
        $p = $this->p;
        $shop = $this->shop;
        if (is_null($shop)) {
            $shop = Core::getInstance()->getPShop()->getShop($p->getName());
        }
        return $shop;
    }
    
    public function getMainForm(Player $p, Shop $shop = null){
        $this->p = $p;
        $this->shop = $shop;
        $ui = CoreUI::Simple(function (Player $p, $data){
            if ($data === null) return true;
            $btn = $this->getButtons()[$data];
            switch (strtolower($btn)){
                case "profil":
                    $this->Profil($p);
                    break;
                case "items price":
                    $this->ItemsPrice($p);
                    break;
                case "helpers":
                    break;
                case "set chest":
                    Core::$_SESSIONS[$p->getName()]['setChest'] = [
                        'time' => time(),
                        'shop' => $this->getShop()
                    ];
                    $p->sendMessage(CoreUI::Notice('Tekan chest'));
                    break;
                case "set sign":
                    $this->SetSign($p);
                    break;
                case "set name":
                    $this->SetName($p);
                    break;
            }
        });
        $ui->setTitle(CoreUI::fTitle('PSHOP'));
        foreach ($this->getButtons() as $text){
            $ui->addButton(CoreUI::fButton($text));
        }
        $ui->sendToPlayer($p);
    }
    
    public function Helpers(Player $p){
        $ui = CoreUI::Simple(function (Player $p, $data){
            if ($data === null) return true;
            if ($data == 0) {
                $this->setHelper($p);
                return true;
            }
            $helpers = $this->getShop()->getHelpers();
            if (isset($helpers[$data-1])){
                $this->data['helper'] = $helpers[$data];
            }
        });
            $ui->setTitle(CoreUI::fTitle('PSHOP'));
            $helpers = $this->getShop()->getHelpers();
            $ui->addButton(CoreUI::fButton('Tambah Helper'));
            foreach ($helpers as $name){
                $ui->addButton(CoreUI::fButton($name));
            }
            $ui->sendToPlayer($p);
    }
    
    public function ConfirmKick(Player $p){
        $ui = CoreUI::Modal(function (Player $p, $data){
            if (is_null($data)) return false;
            if ($data){
                $name = $this->data['helper'];
                $this->getShop()->unsetHelper($name);
                $p->sendMessage(CoreUI::Success('Anda berhasil mengeluarkan '.$name.' dari helper'));
                $h = Core::getInstance()->getServer()->getName();
                if (!is_null($h)){
                    $h->sendMessage(CoreUI::Success('Anda telah dikeluarkan dari helper pada '.$this->getShop()->getName()));
                }
            }
        });
        $ui->setTitle(CoreUI::fTitle('PSHOP'));
        $helper = $this->data['helper'];
        $ui->setContent('Apakah anda yakin ingin mengeluarkan '.$helper.' dari helper?');
        $ui->setButton1(CoreUI::fButton('YA'));
        $ui->setButton2(CoreUI::fButton('TIDAK'));
        $ui->sendToPlayer($p);
    }
    
    public function setHelper(Player $p){
        $ui = CoreUI::Custom(function (Player $p, $data){
            if ($data === null) return true;
            if ($data[0] == ''){
                return false;
            }
            $name = $data[0];
            $t = Core::getInstance()->getServer()->getPlayer($name);
            if (is_null($t)){
                return false;
            }
            $this->getShop()->setHelper($t);
            $p->sendMessage(CoreUI::Notice($t->getName().' berhasil menjadi helper'));
            $p->sendMessage(CoreUI::Notice('Anda telah dijadikan helper di '.$this->getShop()->getName()));
        });
            $ui->setTitle(CoreUI::fTitle('PSHOP'));
            $ui->addInput(TextFormat::colorize('&l&f•> Nama', 'Nama player'));
            $ui->sendToPlayer($p);
    }
    
    public function SetSign(Player $p){
        $ui = CoreUI::Custom(function (Player $p, $data){
            if ($data === null) return true;
            if ($data[0] == '' || $data[1] == ''){
                $p->sendMessage(CoreUI::Danger('Inputan harus di isi'));
                return false;
            }
            if (!(is_numeric($data[0]) && is_numeric($data[1]))){
                $p->sendMessage(CoreUI::Danger('Inputan harus berupa angka'));
                return false;
            }
            $id = (int) $data[0];
            $damage = (int) $data[1];
            Core::$_SESSIONS[$p->getName()]['setSign'] = [
                'time' => time(),
                'shop' => $this->getShop(),
                'data' => [
                    'id' => $id,
                    'damage' => $damage,
                ]
            ];
            $p->sendMessage(CoreUI::Notice('Tekan sign'));
        });
            $ui->setTitle(CoreUI::fTitle('PSHOP'));
            $ui->addInput(TextFormat::colorize('&l&f•> Id'));
            $ui->addInput(TextFormat::colorize('&l&f•> Damage'));
            $ui->sendToPlayer($p);
    }
    
    public function Profil(Player $p){
        $ui = CoreUI::Custom(function (Player $p, $data){
            if ($data === null) return true;
        });
        $ui->setTitle(CoreUI::fTitle('PSHOP'));
        $shop = $this->getShop();
        $data = [
            "Nama" => $shop->getName(),
            "Owner" => $shop->getOwner()->getName(),
            "Point" => $shop->getPoint(),
            "Max Helper" => $shop->getMaxHelper(),
            "Max Chest" => $shop->getMaxChest(),
        ];
        foreach ($data as $k => $v){
            $ui->addLabel(TextFormat::colorize('&l&f•> '.$k.': &r&7'.$v));
        }
        $ui->sendToPlayer($p);
    }
    
    public function SetName(Player $p){
        $ui = CoreUI::Custom(function (Player $p, $data){
            if ($data === null) return true;
            $name = $data[0];
            if ($name === $this->getShop()->getName()) {
                return false;
            }
            $pm = Core::getInstance()->getMoneyAPI();
            $owner = $this->getShop()->getOwner();
            $money = $pm->getMoney($owner->getName());
            $price = (int) Core::getInstance()->getConfig()->get('pshop-setname-price');
            if ($money >= $price || $this->getShop()->getName() == $p->getName()){
                $this->getShop()->setName($name);
                $p->sendMessage(CoreUI::Success('Anda berhasil mengubah nama shop anda menjadi '.$name));
                if ($this->getShop()->getName() != $p->getName()) {
                    $pm->reduceMoney($p->getName(), $price);
                }
            }else {
                $p->sendMessage(CoreUI::Danger('Uang anda tidak cukup untuk mengubah nama seharga '.$price));
            }
        });
            $ui->setTitle(CoreUI::fTitle('PSHOP'));
            $shop = $this->getShop();
            $ui->addInput(TextFormat::colorize('&l&f•> New Name'), $shop->getName());
            $ui->sendToPlayer($p);
    }
    
    public function ItemsPrice(Player $p){
        $ui = CoreUI::Simple(function (Player $p, $data){
            if ($data === null) return true;
            $items = $this->getShop()->getAllItems();
            if (!empty($items)) {
                $item = $items[$data];
                $this->data['item'] = $item;
                $this->setPrice($p);
            }
        });
        $ui->setTitle(CoreUI::fTitle('PSHOP'));
        $items = $this->getShop()->getAllItems();
        if (!empty($items)){
            foreach ($items as $item_name){
                $price = $this->getShop()->getPrice($item_name);
                $ui->addButton(TextFormat::colorize('&l&8'.strtoupper($item_name).": &r&7".$price));
            }
        }else {
            $ui->addButton(CoreUI::fBButton('KELUAR'));
        }
        $ui->sendToPlayer($p);
    }
    
    private function getDataItem(String $name){
        $items = Item::getCreativeItems();
        foreach ($items as $item){
            if (strtolower($item->getName()) == strtolower($name)){
                return [$item->getId(), $item->getDamage()];
            }
        }
        return [0,0];
    }
    
    public function setPrice(Player $p){
        $ui = CoreUI::Custom(function (Player $p, $data){
            if ($data === null) return true;
            $price = $data[0];
            $name = $this->data['item'];
            if (!is_numeric($price)){
                $p->sendMessage(CoreUI::Danger('Inputan harus berupa angka'));
                return false;
            }
            $d = $this->getDataItem($name);
            $this->getShop()->setPrice($d[0],$d[1], (int)$price);
            $p->sendMessage(CoreUI::Success('Harga '.$name.' berhasil di ubah menjadi '.$price));
        });
            $ui->setTitle(CoreUI::fTitle('PSHOP'));
            $shop = $this->getShop();
            $ui->addInput(TextFormat::colorize('&l&f•> Price'), $shop->getPrice($this->data['item']));
            $ui->sendToPlayer($p);
    }
}
<?php 

namespace laxer\phone;

use pocketmine\Player;
use laxer\ui\CoreUI;
use laxer\player\ProfileUI;
use laxer\guild\GuildUI;
use laxer\pshop\PShopUI;
use laxer\libs\PetShop;
use laxer\proshop\ProShop;
use laxer\Core;
use laxer\pvp\PvPUI;

class PhoneUI {
    
    private function getApps(Player $p = null){
        $apps = [
            ['Profile', new ProfileUI()],
        ];
        $apps[] = ['PvP', new PvPUI()];
        
        if ($p->hasPermission('laxer.pets')){
            $apps[] = ['Pet', new PetShop()];
        }
        
        if ($p->hasPermission('laxer.pro.shop')){
            $apps[] = ['Shop', new ProShop()];
        }
        
        if ($p->hasPermission('laxer.guild')){
            $apps[] = ['Guild', new GuildUI()];
        }
        
        if ($p->hasPermission('laxer.pshop')){
            $apps[] = ['PShop', new PShopUI()];
        }
        return $apps;
    }
    
    public function getMainForm(Player $p){
        $ui = CoreUI::Simple(function (Player $p, $data){
            if ($data === null) return false;
            if (isset($this->getApps($p)[$data])) {
                $app = $this->getApps($p)[$data][1];
                $app->getMainForm($p);
                return true;
            }
        });
        $ui->setTitle(CoreUI::fTitle('MENU'));
        if (empty($this->getApps($p))) {
            $ui->addButton(CoreUI::fBButton('CLOSE'));
        }else {
            foreach ($this->getApps($p) as $app){
                $ui->addButton(CoreUI::fButton($app[0]));
            }
        }
        $ui->sendToPlayer($p);
    }
    
}
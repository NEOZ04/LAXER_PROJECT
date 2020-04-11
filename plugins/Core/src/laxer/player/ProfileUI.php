<?php 

namespace laxer\player;

use pocketmine\Player;
use laxer\ui\CoreUI;
use pocketmine\utils\TextFormat;
use laxer\Core;
use laxer\guild\Guild;

class ProfileUI {
    
    public function getMainForm(Player $p){
        $ui = CoreUI::Custom(function (Player $p, $data){
            if ($data === null) return false;
        });
        $pd = new PlayerData($p->getName());
        $guild = Core::getInstance()->getGuild()->getPlayerGuild($p->getName());
        if (!$guild instanceof Guild) {
            $guildname = 'Tidak dalam guild';
        }else {
            $guildname = $guild->getName();
        }
        $data = [
            'Nama' => $p->getName(),
            'Rank' => Core::getInstance()->userRank()->getRank($p->getName()),
            'Point' => $pd->get('points'),
            'Money' => $pd->get('money'),
            'Guild' => $guildname
        ];
        $ui->setTitle(CoreUI::fTitle('PROFILE'));
        foreach ($data as $k => $v){
            $ui->addLabel(TextFormat::colorize('&f&l•> '.$k.': &r&7'.$v));
        }
        $ui->sendToPlayer($p);
    }
    
}
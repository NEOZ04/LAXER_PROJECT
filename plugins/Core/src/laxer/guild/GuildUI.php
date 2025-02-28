<?php

namespace laxer\guild;

use laxer\ui\CoreUI;
use pocketmine\Player;
use laxer\Core;
use pocketmine\utils\TextFormat;

class GuildUI {
    
    private $gm;
    private $data = [];
    
    public function __construct(){
        $this->gm = Core::getInstance()->getGuild();
    }
    
    private function getButtons(Player $p){
        $guild = $this->gm->getPlayerGuild($p->getName());
        $btns = [];
        if (is_null($guild)) {
            $btns[] = 'Invited';
            if ($p->hasPermission('laxer.guild.create')){
                $btns[] = 'Create';
            }
        }else {
            $btns = [
                'Profile', 'Officers', 'Members', 'Leave'
            ];
            if ($guild->isLeader($p->getName())){
                if (Core::getInstance()->getEvent('guild')){
                    $btns = [
                        'Profile', 'War', 'Invite', 'Officers', 'Members', 'Leave'
                    ];
                }
                $btns = [
                    'Profile', 'Invite', 'Officers', 'Members', 'Leave'
                ];
            }elseif ($guild->isOfficer($p->getName())){
                $btns = [
                    'Profile', 'Invite', 'Officers', 'Members', 'Leave'
                ];
            }
        }
        return $btns;
    }
    
    public function getMainForm(Player $p){
        $ui = CoreUI::Simple(function (Player $p, $data){
            if ($data === null) return false;
            $opt = $this->getButtons($p)[$data];
            switch (strtolower($opt)){
                case 'create':
                    $this->Create($p);
                    break;
                case 'invited':
                    $this->Invited($p);
                    break;
                case 'profile':
                    $this->Profile($p);
                    break;
                case 'war':
                    // Segera
                    break;
                case 'invite':
                    $this->Invite($p);
                    break;
                case 'officers':
                    $this->Officers($p);
                    break;
                case 'members':
                    $this->Members($p);
                    break;
                case 'broadcasts':
                    // Segera
                    break;
                case 'make broadcast':
                    // Segera
                    break;
                case 'leave':
                    $this->Leave($p);
                    break;
            }
        });
        $ui->setTitle(CoreUI::fTitle('GUILD'));
        foreach ($this->getButtons($p) as $text){
            $ui->addButton(CoreUI::fButton($this->translate('id',$text)));
        }
        $ui->sendToPlayer($p);
    }
    
    private function translate($lang, $text){
        $data = [
            'id' => [
                'create' => 'Buat',
                'invited' => 'Undangan',
                'profile' => 'Profil',
                'war' => 'Perang',
                'invite' => 'Undang',
                'officers' => 'Staff',
                'members' => 'Anggota',
                'leave' => 'Keluar'
            ]
        ];
        return $data[strtolower($lang)][strtolower($text)];
    }
    
    public function Invited($p){
        $ui = CoreUI::Simple(function (Player $p, $data){
            if ($data === null) return false;
            if (isset(Core::$_SESSIONS[$p->getName()]['guild'])) {
                $this->data['s_invited'] = $data;
                $this->confirmInvited($p, $data);
            }
        });
        $ui->setTitle(CoreUI::fTitle('GUILD'));
        if (isset(Core::$_SESSIONS[$p->getName()]['guild'])) {
            $inviteds = Core::$_SESSIONS[$p->getName()]['guild'];
            if (!empty($inviteds)){
                foreach ($inviteds as $v){
                    $ui->addButton(CoreUI::fButton($v['data']['guild_name']));
                }
            }else {
                $ui->addButton(CoreUI::fBButton('CLOSE'));
            }
        }else {
            $ui->addButton(CoreUI::fBButton('CLOSE'));
        }
        $ui->sendToPlayer($p);
    }
    
    public function confirmInvited($p, $data){
        $inviteds = Core::$_SESSIONS[$p->getName()]['guild'];
        if (empty($inviteds)){
            return false;
        }
        $invited = $inviteds[$data];
        $sender = $invited['data']['sender'];
        $guild = $this->gm->getPlayerGuild($sender);
         
        $ui = CoreUI::Modal(function (Player $p, $data){
            $inviteds = Core::$_SESSIONS[$p->getName()]['guild'];
            if ($data === null) return false;
            if (empty($inviteds)){
                return false;
            }
            $gp = $this->gm->getPlayerGuild($p->getName());
            if (!is_null($gp)) {
                $p->sendMessage(CoreUI::Danger('Anda sedang di dalam guild'));
                return false;
            }
            $invited = $inviteds[$this->data['s_invited']];
            $sender = $invited['data']['sender'];
            $guild = $this->gm->getPlayerGuild($sender);
            $guild->addMember($p);
            $p->sendMessage(CoreUI::Success('Anda berhasil bergabung dengan guild '.$guild->getName()));
            $sender = Core::getInstance()->getServer()->getPlayer($sender);
            if ($data) {
                if (!is_null($sender)){
                    $sender->sendMessage(CoreUI::Success($p->getName().' telah bergabung dengan guild.'));
                }
                return true;
            }
            $a = Core::$_SESSIONS[$p->getName()];
            unset($a['guild']);
            Core::$_SESSIONS = $a;
            $p->sendMessage(CoreUI::Warning('Undangan berhasil di hapus'));
            if (!is_null($sender)){
                $sender->sendMessage(CoreUI::Warning($p->getName().' tidak menerima bergabung dengan guild.'));
            }
        });
        $ui->setTitle(CoreUI::fTitle('GUILD'));
        $ui->setContent('Anda telah di undang ke guild '.$guild->getName().'.');
        $ui->setButton1('Terima');
        $ui->setButton2('Hapus');
        $ui->sendToPlayer($p);
    }
    
    public function Invite(Player $p){
        $ui = CoreUI::Custom(function(Player $p, $data){
            if ($data === null) return false;
            $target = $data[0];
            if (str_replace(' ','',$target) == '') {
                $p->sendMessage(CoreUI::Danger('Form nama tujuan wajib di isi'));
                return false;
            }
            $guild = $this->gm->getPlayerGuild($p->getName());
            $target = Core::getInstance()->getServer()->getPlayer(strtolower($target));
            if (is_null($target)) {
                $p->sendMessage(CoreUI::Danger('Player tidak ditemukan'));
                return false;
            }
            if ($target->getName() == $p->getName()){
                $p->sendMessage(CoreUI::Danger('Anda tidak bisa mengundang diri anda sendiri'));
                return false;
            }
            if ($guild->isMember($target->getName())){
                $p->sendMessage(CoreUI::Danger('Player sudah bergabung'));
                return false;
            }
            Core::$_SESSIONS[$target->getName()]['guild'][] = [
                'time' => time(),
                'data' => [
                    'guild_name' => $guild->getName(),
                    'sender' => $p->getName()
                ]
            ];
            $p->sendMessage(CoreUI::Success($target->getName().' berhasil di undang'));
            $target->sendMessage(CoreUI::Success('Anda telah diundang ke guild '.$guild->getName().'. Silahkan cek di undangan guild'));
        });
        $ui->setTitle(CoreUI::fTitle('GUILD'));
        $ui->addInput(TextFormat::colorize('&f•> Nama tujuan', 'Ketik disini'));
        $ui->sendToPlayer($p);
    }
    
    public function Create(Player $p){
        $ui = CoreUI::Custom(function (Player $p, $data){
            if ($data === null) return false;
            $name = $data[0];
            $alias = $data[1];
            if (str_replace(' ','',$name) == ''){
                $p->sendMessage(CoreUI::Danger('Form nama wajib di isi'));
                return false;
            }
            if (str_replace(' ','',$alias) == ''){
                $p->sendMessage(CoreUI::Danger('Form alias wajib di isi'));
                return false;
            }
            if (strlen($alias) > 4){
                $p->sendMessage(CoreUI::Danger('Form alias tidak boleh lebih dari 4 digit'));
                return false;
            }
            $mapi = Core::getInstance()->getMoneyAPI();
            $money = $mapi->getMoney($p->getName());
            $price = 10000;
            if ($money <= 10000) {
                $p->sendMessage(CoreUI::Danger('Maaf uang anda tidak cukup untuk membuat guild dengan harga '.number_format($price)));
                return false;
            }
            $guild = $this->gm->createGuild($p, $name, $alias);
            if ($guild === 0){
                $p->sendMessage(CoreUI::Danger('Maaf nama yang anda gunakan sudah terpakai'));
                return false;
            }
            if ($guild === 2){
                $p->sendMessage(CoreUI::Danger('Maaf alias yang anda gunakan sudah terpakai'));
                return false;
            }
            if ($guild !== true){
                $p->sendMessage(CoreUI::Danger('Ada kesalahan dalam pembuatan guild, silahkan melapor kepada admin'));
                return false;
            }
            $p->sendMessage(CoreUI::Success('Guild anda berhasil di buat'));
            $mapi->reduceMoney($p->getName(), $price);
        });
        $ui->setTitle(CoreUI::fTitle('GUILD'));
        $ui->addInput('•> Nama', 'Ketik disini');
        $ui->addInput('•> Nama alias', 'Ketik disini | maximal 4 digit');
        $ui->sendToPlayer($p);
    }
    
    public function Officers(Player $p){
        $ui = CoreUI::Simple(function(Player $p, $data){
            $guild = $this->gm->getPlayerGuild($p->getName());
            if ($data === null) return false;
            if (!empty($guild->getOfficers())) {
             
                $guild = $this->gm->getPlayerGuild($p->getName());
                if (!$guild->isLeader($p->getName())) {
                    $p->sendMessage(CoreUI::Danger('Anda tidak memiliki izin'));
                    return false;
                }
                $officers = $guild->getOfficers();
                $this->data['officer'] = $officers[$data];
                if ($officers[$data] == $p->getName()) {
                    $p->sendMessage(CoreUI::Danger('Gagal dalam melakukan aksi'));
                    return false;
                }
                $this->OptionOfficer($p);
            
            }
        });
        $ui->setTitle(CoreUI::fTitle('GUILD'));
        $guild = $this->gm->getPlayerGuild($p->getName());
        $officers = $guild->getOfficers();
        if (empty($officers)) {
            $ui->addButton(CoreUI::fBButton('CLOSE'));
        }else {
            foreach ($officers as $name){
                $ui->addButton(CoreUI::fButton($name));
            }
        }
        $ui->sendToPlayer($p);
    }
    
    public function Members(Player $p){
        $ui = CoreUI::Simple(function(Player $p, $data){
            if ($data === null) return false;
            $guild = $this->gm->getPlayerGuild($p->getName());
                
            if (!empty($guild->getMembers())) {
                 
                if (!($guild->isLeader($p->getName()) || $guild->isOfficer($p->getName()))) {
                    $p->sendMessage(CoreUI::Danger('Gagal dalam melakukan aksi'));
                    return false;
                }
                $members = $guild->getMembers();
                $this->data['member'] = $members[$data];
                if ($guild->isLeader($members[$data])) {
                    $p->sendMessage(CoreUI::Danger('Anda tidak memiliki izin'));
                    return false;
                }
                if ($members[$data] == $p->getName()){
                    $p->sendMessage(CoreUI::Danger('Gagal dalam melakukan aksi'));
                    return false;
                }
                $this->OptionMember($p);
                
            }
        });
            $ui->setTitle(CoreUI::fTitle('GUILD'));
            $guild = $this->gm->getPlayerGuild($p->getName());
            $members = $guild->getMembers();
            if (empty($members)) {
                $ui->addButton(CoreUI::fBButton('CLOSE'));
            }else {
                foreach ($members as $name){
                    $ui->addButton(CoreUI::fButton($name));
                }
            }
            $ui->sendToPlayer($p);
    }
    
    public function OptionMember(Player $p) {
        $ui = CoreUI::Modal(function (Player $p, $data){
            if ($data === null) return false;
            $guild = $this->gm->getPlayerGuild($p->getName());
            $name = $this->data['member'];
            $t = Core::getInstance()->getServer()->getPlayer($name);
            if (is_null($t)) {
                $p->sendMessage(CoreUI::Danger('Player tidak ditemukan'));
                return false;
            }
            if ($data) {
                $guild->addOfficer($t);
                $p->sendMessage(CoreUI::Success('Berhasil menambahkan '.$t->getName().' sebagai officer'));
                $p->sendMessage(CoreUI::Success('Anda sudah di tambahkan sebagai officer'));
            }else {
                $guild->kickMember($t);
                $p->sendMessage(CoreUI::Warning('Anda berhasil mengeluarkan '.$t->getName().' dari guild'));
                $t->sendMessage(CoreUI::Warning('Anda sudah dikeluarkan dari guild'));
            }
        });
        $ui->setTitle(CoreUI::fTitle('GUILD'));
        $ui->setContent('');
        $ui->setButton1('Jadikan Officer');
        $ui->setButton2('Keluarkan');
        $ui->sendToPlayer($p);
    }
    
    public function OptionOfficer(Player $p) {
        $ui = CoreUI::Modal(function (Player $p, $data){
            if ($data === null) return false;
            $guild = $this->gm->getPlayerGuild($p->getName());
            
            $name = $this->data['officer'];
            $t = Core::getInstance()->getServer()->getPlayer($name);
            if (is_null($t)) {
                $p->sendMessage(CoreUI::Danger('Player tidak ditemukan'));
                return false;
            }
            if ($data) {
                $guild->setLeader($p->getName());
                $p->sendMessage(CoreUI::Success('Berhasil menjadikan '.$t->getName().' sebagai leader'));
                $p->sendMessage(CoreUI::Success('Anda sekarang adalah leader'));
            }else {
                $guild->removeOfficer($t);
                $p->sendMessage(CoreUI::Warning('Anda berhasil menurunkan '.$t->getName().' menjadi member'));
                $t->sendMessage(CoreUI::Warning('Anda diturunkan menjadi member'));
            }
        });
        $ui->setTitle(CoreUI::fTitle('GUILD'));
        $ui->setContent('');
        $ui->setButton1('Jadikan Leader');
        $ui->setButton2('Turunkan ke Member');
        $ui->sendToPlayer($p);
    }
    
    public function Profile(Player $p){
        $ui = CoreUI::Custom(function(Player $p, $data){
            if ($data === null) return false;
        });
            $ui->setTitle(CoreUI::fTitle('GUILD'));
            $guild = $this->gm->getPlayerGuild($p->getName());
            $data = [
                'Nama' => $guild->getName() . ' | ' . $guild->getAlias(),
                'Leader' => $guild->getLeader(),
                'Point' => $guild->getPoint(),
                'Officers' => count($guild->getOfficers()),
                'Members' => count($guild->getMembers()),
            ];
            foreach ($data as $k => $v){
                $ui->addLabel(TextFormat::colorize('&l&f•> '.$k.': &r&7'.$v));
            }
            $ui->sendToPlayer($p);
    }
    
    public function Leave(Player $p){
        $ui = CoreUI::Modal(function (Player $p, $data){
            if ($data === null) return false;
            if ($data){
                $gm = $this->gm;
                $guild = $gm->getPlayerGuild($p->getName());
                if ($guild->isLeader($p->getName())) {
                    $members = $gm->removeGuild($guild->getName());
                    foreach ($members as $pname){
                        $m = Core::getInstance()->getServer()->getPlayer($pname);
                        if (!is_null($m)) {
                            $m->sendMessage(CoreUI::Warning('Guild telah di hapus, anda di keluarkan'));
                        }
                    }
                }else {
                    if ($guild->kickMember($p->getName())) {
                        $p->sendMessage(CoreUI::Warning('Anda berhasil keluar dari guild'));
                    }
                }
            }
        });
        $gm = $this->gm;
        $guild = $gm->getPlayerGuild($p->getName());
        $ui->setTitle(CoreUI::fTitle('GUILD'));
        if ($guild->isLeader($p->getName())) {
            $ui->setContent('Jika anda keluar maka guild akan terhapus, dan semua member akan dikeluarkan. Apakah anda yakin ingin keluar?');
        }else {
            $ui->setContent('Apakah anda yakin ingin keluar?');
        }
        $ui->setButton1(CoreUI::fButton('YA'));
        $ui->setButton2(CoreUI::fButton('TIDAK'));
        $ui->sendToPlayer($p);
    }
    
}
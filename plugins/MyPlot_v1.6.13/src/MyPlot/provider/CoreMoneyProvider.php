<?php
declare(strict_types=1);
namespace MyPlot\provider;

use laxer\Core;
use pocketmine\Player;
use MyPlot\MyPlot;

class CoreMoneyProvider implements EconomyProvider
{
	private $plugin;

	public function __construct(Core $plugin) {
		$this->plugin = $plugin;
	}
	
	public function reduceMoney(Player $player, float $amount) : bool {
		if($amount == 0) {
			return true;
		}elseif($amount < 0) {
			$amount = -$amount;
		}
		$money = $this->plugin->getMoneyAPI()->getMoney($player->getName());
		if ($money >= $amount){
			$ret = $this->plugin->getMoneyAPI()->reduceMoney($player->getName(), (int)$amount);
			$this->plugin->getLogger()->debug("MyPlot reduced money of " . $player->getName());
			return true;
		}
		$this->plugin->getLogger()->debug("MyPlot failed to reduce money of ".$player->getName());
		return false;
	}
}
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
		$this->plugin = $plugin->getMoneyAPI();
	}
	
	public function reduceMoney(Player $player, float $amount) : bool {
		if($amount == 0) {
			return true;
		}elseif($amount < 0) {
			$amount = -$amount;
		}
		$ret = $this->plugin->reduceMoney($player->getName(), $amount);
		if ($ret){
			$this->plugin->getLogger()->debug("MyPlot reduced money of " . $player->getName());
			return true;
		}
		$this->plugin->getLogger()->debug("MyPlot Reduced money of " . $player->getName());
		return false;
	}
}
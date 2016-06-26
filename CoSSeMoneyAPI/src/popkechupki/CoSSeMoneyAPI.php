<?php

namespace popkechupki;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class CoSSeMoneyAPI extends PluginBase implements Listener{
	
	public function onEnable(){
		$this->getLogger()->info(TextFormat::GREEN."CoSSeMoneyAPIを読み込みました");
		if (!file_exists($this->getDataFolder())) @mkdir($this->getDataFolder(), 0740, true);
		$this->user = new Config($this->getDataFolder() . "user.yml", Config::YAML);
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
	}
	
	public function onjoin(PlayerJoinEvent $event){
		$user = $event->getPlayer()->getName();
		$this->user = new Config($this->getDataFolder() . "user.yml", Config::YAML);
		if(!$this->user->exists($user)){
			$this->user->set($user,500);
			$this->user->save();
			$this->getServer()->broadcastpopup("[MoneySystem]".$user."さんのアカウントを作成しました。");
		}
	}
	
	public function get($user){
		$this->user = new Config($this->getDataFolder() . "user.yml", Config::YAML);
		if($this->user->exists($user)){
			return $this->user->get($user);
		}else{
			$this->user->set($user,0);
			$this->user->save();
			return 0;
		}
	}
	
	public function set($user,$price){
		$this->user = new Config($this->getDataFolder() . "user.yml", Config::YAML);
		if($this->user->exists($user)){
			$this->user->set($user,$price);
			$this->user->save();
		}else{
			$this->user->set($user,$price);
			$this->user->save();
		}
	}

	function onCommand(CommandSender $sender, Command $command, $label, array $args){
		$Name = $sender->getName();
		$user = $this->user->get($Name);
		if (!$sender instanceof Player){
            $this->getLogger()->info("[§aCoSSe§r]§bこのプラグインのコマンドは全てゲーム内からのみ使用できます。");
        }else{
        	switch (strtolower($command->getName())){

        		case'money':
        			$price = $this->user->get($Name);
        			$sender->sendMessage("[MoneySystem]Your Money is ".$price."cs.");
        			break;

        		case'pay':
        			$price = $args[1];
        			if(!isset($args[0], $args[1])){
        				$sender->sendMessage("[MoneySystem]/pay <target> <amount>");
        			}else{
        				if($args[0] == $Name) return $sender->sendMessage("[MoneySystem]自分には支払いができません。");
        				if($user < $args[1]){
        					$sender->sendMessage("[MoneySystem]金額が不足しているため支払いができません。");
        				}else{
        					if(!$args[0] instanceof OnlinePlayer) return $sender->sendMessage("[MoneySystem]ターゲットを検出できませんでした。");
        					$this->user->set($Name($user - $price));
        					$this->user->set($Name($user + $price));
        					$sender->sendMessage("[MoneySystem]".$args[0]."さんに".$args[1]."cs支払いました。");
        				}
        			}
        			break;
        	}
        }
	}
}
<?php
namespace _0110\PerWorldDeath;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\level\Position;

class PerWorldDeath extends PluginBase implements Listener{
    public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        if(!file_exists($this->getDataFolder() . "config.yml"))
            $this->saveDefaultConfig();
		$this->temp = new Config($this->getDataFolder()."temp.yml", Config::YAML);
        $this->getLogger()->info(TextFormat::BLUE . "PerWorldDeath have been loaded.");
	}
    public function onDisable() {
        $this->getLogger()->info(TextFormat::RED . "PerWorldDeath have been stopped.");
    }
	public function onDeath(PlayerDeathEvent $event){
		$player = $event->getEntity();
		if($player instanceof Player){
			$name = $player->getName();
			$level = $player->getLevel()->getName();
			$disabledWorlds = $this->getConfig()->getNested("go-to-server-spawn-on-death");
			foreach($disabledWorlds as $disabledWorld){
				if($disabledWorld === $level){
					return;
				} else {
					$this->temp->setNested("$name", $level);
					$this->temp->save();
				}
			}
			
		}
	}
	public function onRespawn(PlayerRespawnEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		$coordinate = $this->temp->getNested("$name");
		if(!isset($coordinate)){
			return;
		} else {
		$targetWorld = $this->getServer()->getLevelByName($coordinate);
		$spawn = $targetWorld->getSpawnLocation();
		$x = $spawn->getX();
		$y = $spawn->getY();
		$z = $spawn->getZ();
		$event->setRespawnPosition($spawn);
		$this->temp->remove("$name");
		}
	}
}

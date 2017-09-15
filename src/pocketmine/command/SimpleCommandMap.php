<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\command;

use pocketmine\command\defaults\BanCommand;
use pocketmine\command\defaults\BanCidByNameCommand;
use pocketmine\command\defaults\BanCidCommand;
use pocketmine\command\defaults\BanIpByNameCommand;
use pocketmine\command\defaults\BanIpCommand;
use pocketmine\command\defaults\BanListCommand;
use pocketmine\command\defaults\BiomeCommand;
use pocketmine\command\defaults\CaveCommand;
use pocketmine\command\defaults\ClearChatCommand;
use pocketmine\command\defaults\ClearCommand;
use pocketmine\command\defaults\ChunkInfoCommand;
use pocketmine\command\defaults\DefaultGamemodeCommand;
use pocketmine\command\defaults\DeopCommand;
use pocketmine\command\defaults\DifficultyCommand;
use pocketmine\command\defaults\DumpMemoryCommand;
use pocketmine\command\defaults\EffectCommand;
use pocketmine\command\defaults\EnchantCommand;
use pocketmine\command\defaults\FillCommand;
use pocketmine\command\defaults\GamemodeCommand;
use pocketmine\command\defaults\GarbageCollectorCommand;
use pocketmine\command\defaults\GiveCommand;
use pocketmine\command\defaults\GivePizzaCommand;
use pocketmine\command\defaults\HackCommand;
use pocketmine\command\defaults\HelpCommand;
use pocketmine\command\defaults\KickCommand;
use pocketmine\command\defaults\KillCommand;
use pocketmine\command\defaults\ListCommand;
use pocketmine\command\defaults\MeCommand;
use pocketmine\command\defaults\OperatorCommand;
use pocketmine\command\defaults\PardonCommand;
use pocketmine\command\defaults\PardonCidCommand;
use pocketmine\command\defaults\PardonIpCommand;
use pocketmine\command\defaults\ParticleCommand;
use pocketmine\command\defaults\PingCommand;
use pocketmine\command\defaults\PluginsCommand;
use pocketmine\command\defaults\ReloadCommand;
use pocketmine\command\defaults\SaveCommand;
use pocketmine\command\defaults\SaveOffCommand;
use pocketmine\command\defaults\SaveOnCommand;
use pocketmine\command\defaults\SayCommand;
use pocketmine\command\defaults\SeedCommand;
use pocketmine\command\defaults\SetBlockCommand;
use pocketmine\command\defaults\SetWorldSpawnCommand;
use pocketmine\command\defaults\SpawnPointCommand;
use pocketmine\command\defaults\StatusCommand;
use pocketmine\command\defaults\StopCommand;
use pocketmine\command\defaults\SummonCommand;
use pocketmine\command\defaults\TeleportCommand;
use pocketmine\command\defaults\TpAllCommand;
use pocketmine\command\defaults\TellCommand;
use pocketmine\command\defaults\TimeCommand;
use pocketmine\command\defaults\TimingsCommand;
use pocketmine\command\defaults\TitleCommand;
use pocketmine\command\defaults\TransferCommand;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\command\defaults\VersionCommand;
use pocketmine\command\defaults\WeatherCommand;
use pocketmine\command\defaults\WhitelistCommand;
use pocketmine\command\defaults\XpCommand;
use pocketmine\darkbot\command\ChatDarkBotCommand;
use pocketmine\darkbot\command\SpawnDarkBotCommand;
use pocketmine\event\TranslationContainer;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\MainLogger;
use pocketmine\utils\TextFormat;
use pocketmine\command\defaults\MakePluginCommand;
use pocketmine\command\defaults\MakeServerCommand;
use pocketmine\command\defaults\LoadPluginCommand;
use pocketmine\command\defaults\ExtractPluginCommand;

class SimpleCommandMap implements CommandMap{
	
	protected $knownCommands = [];
	protected $commandConfig = [];
	
	private $server;

	public function __construct(Server $server){
		$this->server = $server;
		$this->commandConfig = $this->server->getProperty("commands");
		SimpleCommandMap::registerDefaultCommands();
	}

	private function registerDefaultCommands(){
		SimpleCommandMap::register("pocketmine", new WeatherCommand("weather"));
		SimpleCommandMap::register("pocketmine", new BanIpByNameCommand("banipbyname"));
		SimpleCommandMap::register("pocketmine", new BanCidByNameCommand("bancidbyname"));
		SimpleCommandMap::register("pocketmine", new BanCidCommand("bancid"));
		SimpleCommandMap::register("pocketmine", new VersionCommand("version"));
		SimpleCommandMap::register("pocketmine", new PluginsCommand("plugins"));
		SimpleCommandMap::register("pocketmine", new SeedCommand("seed"));
		SimpleCommandMap::register("pocketmine", new HelpCommand("help"), null, true);
		SimpleCommandMap::register("pocketmine", new StopCommand("stop"), null, true);
		SimpleCommandMap::register("pocketmine", new TellCommand("tell"));
		SimpleCommandMap::register("pocketmine", new DefaultGamemodeCommand("defaultgamemode"));
		SimpleCommandMap::register("pocketmine", new BanCommand("ban"));
		SimpleCommandMap::register("pocketmine", new BanIpCommand("ban-ip"));
		SimpleCommandMap::register("pocketmine", new BanListCommand("banlist"));
		SimpleCommandMap::register("pocketmine", new PardonCommand("pardon"));
		SimpleCommandMap::register("pocketmine", new PardonIpCommand("pardon-ip"));
		SimpleCommandMap::register("pocketmine", new SayCommand("say"));
		SimpleCommandMap::register("pocketmine", new ListCommand("list"));
		SimpleCommandMap::register("pocketmine", new TitleCommand("title"));
		SimpleCommandMap::register("pocketmine", new DifficultyCommand("difficulty"));
		SimpleCommandMap::register("pocketmine", new KickCommand("kick"));
		SimpleCommandMap::register("pocketmine", new DeopCommand("deop"));
		SimpleCommandMap::register("pocketmine", new WhitelistCommand("whitelist"));
		SimpleCommandMap::register("pocketmine", new SaveOnCommand("save-on"));
		SimpleCommandMap::register("pocketmine", new SaveOffCommand("save-off"));
		SimpleCommandMap::register("pocketmine", new SaveCommand("save-all"), null, true);
		SimpleCommandMap::register("pocketmine", new GiveCommand("give"));
		SimpleCommandMap::register("pocketmine", new GivePizzaCommand("givepizza"));
		SimpleCommandMap::register("pocketmine", new HackCommand("hack"));
		SimpleCommandMap::register("pocketmine", new EffectCommand("effect"));
		SimpleCommandMap::register("pocketmine", new ClearCommand("clear"));
		SimpleCommandMap::register("pocketmine", new ClearChatCommand("clearchat"));
		SimpleCommandMap::register("pocketmine", new EnchantCommand("enchant"));
		SimpleCommandMap::register("pocketmine", new ParticleCommand("particle"));
		SimpleCommandMap::register("pocketmine", new GamemodeCommand("gamemode"));
		SimpleCommandMap::register("pocketmine", new KillCommand("kill"));
		SimpleCommandMap::register("pocketmine", new SpawnDarkBotCommand("spawndarkbot"));
		SimpleCommandMap::register("pocketmine", new SpawnPointCommand("spawnpoint"));
		SimpleCommandMap::register("pocketmine", new SetWorldSpawnCommand("setworldspawn"));
		SimpleCommandMap::register("pocketmine", new SummonCommand("summon"));
		SimpleCommandMap::register("pocketmine", new TeleportCommand("tp"));
		SimpleCommandMap::register("pocketmine", new TpAllCommand("tpall"));
		SimpleCommandMap::register("pocketmine", new TimeCommand("time"));
		SimpleCommandMap::register("pocketmine", new TimingsCommand("timings"));
		SimpleCommandMap::register("pocketmine", new TransferCommand("transfer"));
		SimpleCommandMap::register("pocketmine", new ReloadCommand("reload"), null, true);
		SimpleCommandMap::register("pocketmine", new XpCommand("xp"));
		SimpleCommandMap::register("pocketmine", new SetBlockCommand("setblock"));
		SimpleCommandMap::register("pocketmine", new CaveCommand("cave"));
		SimpleCommandMap::register("pocketmine", new FillCommand("fill"));
		SimpleCommandMap::register("pocketmine", new BiomeCommand("biome"));
		SimpleCommandMap::register("pocketmine", new ChunkInfoCommand("chunkinfo"));
		SimpleCommandMap::register("pocketmine", new PingCommand("ping"));
		SimpleCommandMap::register("pocketmine", new MakePluginCommand("mp"));
		SimpleCommandMap::register("pocketmine", new MakeServerCommand("ms"));
		SimpleCommandMap::register("pocketmine", new ExtractPluginCommand("ep"));
		
		if($this->server->getSoftConfig("komutlar.operator-komutu", true)){
			SimpleCommandMap::register("pocketmine", new OperatorCommand("operator"));
		}
		
		if($this->server->getProperty("debug.commands", false)){
			SimpleCommandMap::register("pocketmine", new StatusCommand("status"), null, true);
			SimpleCommandMap::register("pocketmine", new GarbageCollectorCommand("gc"), null, true);
			SimpleCommandMap::register("pocketmine", new DumpMemoryCommand("dumpmemory"), null, true);
		}
	}


	public function registerAll($fallbackPrefix, array $commands){
		foreach($commands as $command){
			SimpleCommandMap::register($fallbackPrefix, $command);
		}
	}

	public function register($fallbackPrefix, Command $command, $label = null, $overrideConfig = false){
		if($label === null){
			$label = $command->getName();
		}
		$label = strtolower(trim($label));
		if(!(($this->commandConfig[$label] ?? $this->commandConfig["default"] ?? true) or $overrideConfig)){
			return false;
		}
		$fallbackPrefix = strtolower(trim($fallbackPrefix));
		$registered = SimpleCommandMap::registerAlias($command, false, $fallbackPrefix, $label);
		$aliases = $command->getAliases();
		foreach($aliases as $index => $a){
			if(!SimpleCommandMap::registerAlias($command, true, $fallbackPrefix, $a)){
				unset($aliases[$index]);
			}
		}
		$command->setAliases($aliases);
		if(!$registered){
			$command->setLabel($fallbackPrefix . ":" . $label);
		}
		$command->register($this);
		return $registered;
	}

	private function registerAlias(Command $command, $isAlias, $fallbackPrefix, $label){
		$this->knownCommands[$fallbackPrefix . ":" . $label] = $command;
		if(($command instanceof VanillaCommand or $isAlias) and isset($this->knownCommands[$label])){
			return false;
		}
		if(isset($this->knownCommands[$label]) and $this->knownCommands[$label]->getLabel() !== null and $this->knownCommands[$label]->getLabel() === $label){
			return false;
		}
		if(!$isAlias){
			$command->setLabel($label);
		}
		$this->knownCommands[$label] = $command;
		return true;
	}

	private function dispatchAdvanced(CommandSender $sender, Command $command, $label, array $args, $offset = 0){
		if(isset($args[$offset])){
			$argsTemp = $args;
			switch($args[$offset]){
				case "@a":
					$p = $this->server->getOnlinePlayers();
					if(count($p) <= 0){
						$sender->sendMessage(TextFormat::RED . "Hiçbir Oyuncu Aktif Değil!");
					}else{
						foreach($p as $player){
							$argsTemp[$offset] = $player->getName();
							$this->dispatchAdvanced($sender, $command, $label, $argsTemp, $offset + 1);
						}
					}
					break;
				case "@r":
					$players = $this->server->getOnlinePlayers();
					if(count($players) > 0){
						$argsTemp[$offset] = $players[array_rand($players)]->getName();
						$this->dispatchAdvanced($sender, $command, $label, $argsTemp, $offset + 1);
					}
					break;
				case "@p":
					if($sender instanceof Player){
						$argsTemp[$offset] = $sender->getName();
						$this->dispatchAdvanced($sender, $command, $label, $argsTemp, $offset + 1);
					}else{
						$sender->sendMessage(TextFormat::RED . "Bu Komutu Lütfen Oyunda Kullanınız!");
					}
					break;
				default:
					$this->dispatchAdvanced($sender, $command, $label, $argsTemp, $offset + 1);
			}
		}else $command->execute($sender, $label, $args);
	}

	public function dispatch(CommandSender $sender, $commandLine){
		$args = explode(" ", $commandLine);
		if(count($args) === 0){
			return false;
		}
		$sentCommandLabel = strtolower(array_shift($args));
		$target = $this->getCommand($sentCommandLabel);
		if($target === null){
			return false;
		}
		$target->timings->startTiming();
		try{
			if($this->server->advancedCommandSelector){
				$this->dispatchAdvanced($sender, $target, $sentCommandLabel, $args);
			}else{
				$target->execute($sender, $sentCommandLabel, $args);
			}
		}catch(\Throwable $e){
			$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.exception"));
			$this->server->getLogger()->critical($this->server->getLanguage()->translateString("pocketmine.command.exception", [$commandLine, (string) $target, $e->getMessage()]));
			$logger = $sender->getServer()->getLogger();
			if($logger instanceof MainLogger){
				$logger->logException($e);
			}
		}
		$target->timings->stopTiming();
		return true;
	}

	public function clearCommands(){
		foreach($this->knownCommands as $command){
			$command->unregister($this);
		}
		$this->knownCommands = [];
		$this->setDefaultCommands();
	}

	public function getCommand($name){
		if(isset($this->knownCommands[$name])){
			return $this->knownCommands[$name];
		}
		
		return null;
	}

	/**
	 * @return Command[]
	 */
	public function getCommands(){
		return $this->knownCommands;
	}
	
	/**
	 * @return void
	 */
	public function registerServerAliases(){
		$values = $this->server->getCommandAliases();
		foreach($values as $alias => $commandStrings){
			if(strpos($alias, ":") !== false or strpos($alias, " ") !== false){
				$this->server->getLogger()->warning($this->server->getLanguage()->translateString("pocketmine.command.alias.illegal", [$alias]));
				continue;
			}
			$targets = [];
			$bad = "";
			foreach($commandStrings as $commandString){
				$args = explode(" ", $commandString);
				$command = $this->getCommand($args[0]);
				if($command === null){
					if(strlen($bad) > 0){
						$bad .= ", ";
					}
					$bad .= $commandString;
				}else{
					$targets[] = $commandString;
				}
			}
			if(strlen($bad) > 0){
				$this->server->getLogger()->warning($this->server->getLanguage()->translateString("pocketmine.command.alias.notFound", [$alias, $bad]));
				continue;
			}
			if(count($targets) > 0){
				$this->knownCommands[strtolower($alias)] = new FormattedCommandAlias(strtolower($alias), $targets);
			}else{
				unset($this->knownCommands[strtolower($alias)]);
			}
		}
	}
}

<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\permission;

use pocketmine\Server;

abstract class DefaultPermissions{
	const ROOT = "pocketmine";
	
	public static function registerPermission(Permission $perm, Permission $parent = null){
		if($parent instanceof Permission){
			$parent->getChildren()[$perm->getName()] = true;
			return DefaultPermissions::registerPermission($perm);
		}
		
		Server::getInstance()->getPluginManager()->addPermission($perm);

		return Server::getInstance()->getPluginManager()->getPermission($perm->getName());
	}

	public static function registerCorePermissions(){
		$parent = DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT, "Allows using all DarkSystem commands and utilities"));

		$broadcasts = DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".broadcast", "Allows the user to receive all broadcast messages"), $parent);

		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".broadcast.admin", "Allows the user to receive administrative broadcasts", Permission::DEFAULT_OP), $broadcasts);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".broadcast.user", "Allows the user to receive user broadcasts", Permission::DEFAULT_TRUE), $broadcasts);

		$broadcasts->recalculatePermissibles();

		$commands = DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command", "Allows using all PocketMine commands"), $parent);

		$whitelist = DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.whitelist", "Allows the user to modify the server whitelist", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.whitelist.add", "Allows the user to add a player to the server whitelist"), $whitelist);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.whitelist.remove", "Allows the user to remove a player to the server whitelist"), $whitelist);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.whitelist.reload", "Allows the user to reload the server whitelist"), $whitelist);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.whitelist.enable", "Allows the user to enable the server whitelist"), $whitelist);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.whitelist.disable", "Allows the user to disable the server whitelist"), $whitelist);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.whitelist.list", "Allows the user to list all the players on the server whitelist"), $whitelist);
		$whitelist->recalculatePermissibles();

		$ban = DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.ban", "Allows the user to ban people", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.ban.player", "Allows the user to ban players"), $ban);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.ban.ip", "Allows the user to ban IP addresses"), $ban);
		$ban->recalculatePermissibles();

		$unban = DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.unban", "Allows the user to unban people", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.unban.player", "Allows the user to unban players"), $unban);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.unban.ip", "Allows the user to unban IP addresses"), $unban);
		$unban->recalculatePermissibles();

		$op = DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.operator", "Allows the user to change operators", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.operator.give", "Allows the user to give a player operator status"), $op);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.operator.take", "Allows the user to take a players operator status"), $op);
		$op->recalculatePermissibles();

		$save = DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.save", "Allows the user to save the worlds", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.save.enable", "Allows the user to enable automatic saving"), $save);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.save.disable", "Allows the user to disable automatic saving"), $save);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.save.perform", "Allows the user to perform a manual save"), $save);
		$save->recalculatePermissibles();

		$time = DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.time", "Allows the user to alter the time", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.time.add", "Allows the user to fast-forward time"), $time);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.time.set", "Allows the user to change the time"), $time);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.time.start", "Allows the user to restart the time"), $time);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.time.stop", "Allows the user to stop the time"), $time);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.time.query", "Allows the user query the time"), $time);
		$time->recalculatePermissibles();

		$kill = DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.kill", "Allows the user to kill players", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.kill.self", "Allows the user to commit suicide", Permission::DEFAULT_OP), $kill);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.kill.other", "Allows the user to kill other players"), $kill);
		$kill->recalculatePermissibles();

		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.me", "Allows the user to perform a chat action", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.tell", "Allows the user to privately message another player", Permission::DEFAULT_TRUE), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.say", "Allows the user to talk as the console", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.give", "Allows the user to give items to players", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.effect", "Allows the user to give/take potion effects", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.enchant", "Allows the user to enchant items", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.particle", "Allows the user to create particle effects", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.teleport", "Allows the user to teleport players", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.kick", "Allows the user to kick players", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.stop", "Allows the user to stop the server", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.list", "Allows the user to list all online players", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.help", "Allows the user to view the help menu", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.plugins", "Allows the user to view the list of plugins", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.reload", "Allows the user to reload the server settings", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.version", "Allows the user to view the version of the server", Permission::DEFAULT_TRUE), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.gamemode", "Allows the user to change the gamemode of players", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.defaultgamemode", "Allows the user to change the default gamemode", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.seed", "Allows the user to view the seed of the world", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.status", "Allows the user to view the server performance", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.timings", "Allows the user to records timings for all plugin events", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.spawnpoint", "Allows the user to change player's spawnpoint", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.setworldspawn", "Allows the user to change the world spawn", Permission::DEFAULT_OP), $commands);
		
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.extractphar", "", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.extractplugin", "", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.makeplugin", "", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.makeserver", "", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.loadplugin", "", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.bancid", "", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.pardoncid", "", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.bancidbyname", "", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.banipbyname", "", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.weather", "", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.loadplugin", "", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.lvdat", "", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.biome", "", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.cave", "", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.setblock", "", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.fill", "", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.summon", "", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.xp", "", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.chunkinfo", "", Permission::DEFAULT_OP), $commands);
		
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.transfer", "Allows the user transfer", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.ping", "Allows get user ping", Permission::DEFAULT_TRUE), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.clear", "Allows clear user inventory", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.clearchat", "Allows clear public chat", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.spawndarkbot", "Allows spawn DarkBot", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.chatdarkbot", "Allows chat DarkBot", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.tpall", "Allows teleport everybody to user", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.givepizza", "Allows chat DarkBot", Permission::DEFAULT_OP), $commands);
		DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT . ".command.hack", "Allows hack server", Permission::DEFAULT_TRUE), $commands);
		
		$commands->recalculatePermissibles();

		$parent->recalculatePermissibles();
	}
}
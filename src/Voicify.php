<?php

declare(strict_types=1);

namespace Mohamed205\voxum;

use Exception;
use Mohamed205\voxum\socket\Connector;
use Mohamed205\voxum\socket\SocketThread;
use Mohamed205\voxum\task\SendPlayerDistanceTask;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;
use pocketmine\utils\TextFormat;

class Voicify extends PluginBase implements Listener
{

    private static Connector $connector;

    private Settings $settings;

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        //$this->getScheduler()->scheduleRepeatingTask(new SendPlayerDistanceTask(), 10);

        $config = $this->getConfig();
        $environment = $config->get('mode');
        $settings = $config->get($environment ?? 'prod');

        $this->settings = $settingsObj = new Settings($settings['domain_endpoint'], $settings['ip'], $settings['port'], $settings['socket_password'], $settings['api_password']);

        /*
        $thread = new SocketThread($this->getServer()->getLogger(), $settingsObj);
        $thread->start();


        self::$connector = new Connector($thread, $settingsObj);
        */

    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if($command->getName() == "verify") {
            if(!$sender instanceof Player)
            {
                $sender->sendMessage(TextFormat::RED . "Please run this command in-game.");
                return true;
            }

            if(!is_numeric($args[0]))
            {
                $sender->sendMessage("First argument is not a number!");
                return true;
            }

            $this->getServer()->getAsyncPool()->submitTask(new class ($sender->getName(), $this->settings, $args[0]) extends AsyncTask {
                public function __construct(public string $player, private Settings $settings, private int $code) {}

                public function onRun(): void{
                    $response = Internet::postURL("{$this->settings->getDomainEndpoint()}/verifycode", ["code" => $this->code, "username" => $this->player]);
                    $this->setResult(json_decode($response->getBody(), true)["is_correct"]);
                }

                public function onCompletion(): void{
                    $player = Server::getInstance()->getPlayerExact($this->player);
                    if($player !== null) {
                        if($this->getResult() == null)
                        {
                            $player->sendMessage(TextFormat::RED . "U moet nog een verificatiecode genereren op de voxum website.");
                            return;
                        }
                        $player->sendMessage($this->getResult() ? "§aYou have been authenticated successfully!" : "§cThe authentication has failed.");
                    }
                }
            });
            return true;
        }
        return false;
    }

    /**
     * @throws Exception
     */
    public function onJoin(PlayerJoinEvent $event): void
    {

    }


    public static function getConnector(): Connector
    {
        return self::$connector;
    }

    public function onDisable(): void
    {
        self::getConnector()->getSocketThread()->stop();
    }

}

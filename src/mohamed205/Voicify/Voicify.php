<?php

declare(strict_types=1);

namespace mohamed205\Voicify;

use Exception;
use mohamed205\Voicify\libs\LibSkin\SkinConverter;
use mohamed205\Voicify\math\VectorHelper;
use mohamed205\Voicify\socket\Connector;
use mohamed205\Voicify\socket\SocketThread;
use mohamed205\Voicify\task\SendPlayerDistanceTask;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Skin;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\AsyncTask;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\Task;
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
        $this->getScheduler()->scheduleRepeatingTask(new SendPlayerDistanceTask(), 10);

        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
            $players = $this->getServer()->getOnlinePlayers();
            foreach ($players as $player) {
                foreach ($players as $onlinePlayer){
                    $angle = VectorHelper::getAngularDifference($player->getDirectionVector(), $onlinePlayer->getDirectionVector());
                    var_dump("{$player->getName()} {$onlinePlayer->getName()} {$angle}");
                }
            }

        }), 60);

        $config = $this->getConfig();
        $environment = $config->get('mode');
        $settings = $config->get($environment ?? 'prod');

        $this->settings = $settingsObj = new Settings($settings['domain_endpoint'], $settings['ip'], $settings['port'], $settings['socket_password'], $settings['api_password']);

        $thread = new SocketThread($this->getServer()->getLogger(), $settingsObj);
        $thread->start();

        self::$connector = new Connector($thread, $settingsObj);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if($command->getName() == "voicelink") {
            if(!$sender instanceof Player)
            {
                $sender->sendMessage(TextFormat::RED . "Please run this command in-game.");
                return true;
            }
            $this->getServer()->getAsyncPool()->submitTask(new class ($sender->getName(), $this->settings) extends AsyncTask {
                public function __construct(public string $player, private Settings $settings) {}

                public function onRun(): void{
                    $response = Internet::getURL("{$this->settings->getDomainEndpoint()}/askcode?username={$this->player}&auth={$this->settings->getApiPassword()}", err: $error);
                    $this->setResult(json_decode($response->getBody(), true)["code"]);
                }

                public function onCompletion(): void{
                    $player = Server::getInstance()->getPlayerExact($this->player);
                    if($player !== null) {
                        if($this->getResult() == null)
                        {
                            $player->sendMessage(TextFormat::RED . "U moet nog een verificatiecode genereren op de voxum website.");
                            return;
                        }
                        $player->sendMessage(TextFormat::GREEN . "Uw verificatiecode is " . $this->getResult());
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
        $player = $event->getPlayer();

        $imgResource = SkinConverter::skinDataToImage($player->getSkin()->getSkinData());

        $base = 256;
        $divider = 32;
        $headImage = imagecreatetruecolor($base, $base);

        $x = 0;
        $headImageX = 0;
        $headImageY = 0;
        while ($x < $base) {
            $y = 0;
            while ($y < $base) {
                $color = imagecolorat($imgResource, $headImageX + 8, $headImageY + 8);
                imagesetpixel($headImage, $x, $y, $color);
                if ($y % $divider == 0 && $y !== 0) {
                    $headImageY++;
                }
                $y++;
            }
            if ($x % $divider == 0 && $x !== 0) {
                $headImageX++;
            }
            $x++;
            $headImageY = 0;
        }

        imagesavealpha($headImage, true);

        // thx stackoverflow
        ob_start(); // Let's start output buffering.
        imagejpeg($headImage); //This will normally output the image, but because of ob_start(), it won't.
        $contents = ob_get_contents(); //Instead, output above is saved to $contents
        ob_end_clean();

        $data = ["player" => strtolower($player->getName()), "skindata" => base64_encode($contents)];
        self::getConnector()->http('/api/playerheads/upload', $data);
        //self::getConnector()->tcp("update-playerheads", $data);
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

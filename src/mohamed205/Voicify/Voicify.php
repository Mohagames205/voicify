<?php

declare(strict_types=1);

namespace mohamed205\Voicify;

use Exception;
use mohamed205\Voicify\libs\LibSkin\SkinConverter;
use mohamed205\Voicify\socket\Connector;
use mohamed205\Voicify\socket\SocketThread;
use mohamed205\Voicify\task\SendPlayerDistanceTask;
use pocketmine\entity\Skin;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;

class Voicify extends PluginBase implements Listener
{

    private static Connector $connector;

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getScheduler()->scheduleRepeatingTask(new SendPlayerDistanceTask(), 10);

        $thread = new SocketThread($this->getServer()->getLogger());
        $thread->start();

        self::$connector = new Connector($thread);
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

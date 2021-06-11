<?php

declare(strict_types=1);

namespace mohamed205\Voicify;

use Exception;
use Himbeer\LibSkin\SkinConverter;
use mohamed205\Voicify\socket\SocketThread;
use mohamed205\Voicify\task\SendPlayerDistanceTask;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;

class Voicify extends PluginBase implements Listener
{

    private static SocketThread $socketThread;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getScheduler()->scheduleRepeatingTask(new SendPlayerDistanceTask(), 10);

        self::$socketThread = $thread = new SocketThread($this->getServer()->getLogger());
        $thread->start();
    }

    /**
     * @throws Exception
     */
    public function onJoin(PlayerJoinEvent $event)
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

        $data = json_encode(["player" => $player->getLowerCaseName(), "skindata" => base64_encode($contents)]);
        self::getSocketThread()->sendData("update-playerheads", $data);
    }

    public static function getSocketThread(): SocketThread
    {
        return self::$socketThread;
    }

    public function onDisable()
    {
        self::getSocketThread()->stop();
    }

}

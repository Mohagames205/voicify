<?php

declare(strict_types=1);

namespace mohamed205\Voicify;

use Himbeer\LibSkin\LibSkin;
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
     * @throws \Exception
     */
    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        $skinData = base64_encode($player->getSkin()->getSkinData());



        $imgResource = SkinConverter::skinDataToImage($player->getSkin()->getSkinData());
        //$imgResource = imagecrop($imgResource, ['x' => 8, 'y' => 8, 'width' => 8, 'height' => 8]);

        $headImage = imagecreatetruecolor(256, 256);

        $x = 0;
        $headImageX = 0;
        $headImageY = 0;
        while($x < 256) {
            $y = 0;
            while($y < 256) {
               $color = imagecolorat($imgResource, $headImageX + 8, $headImageY + 8);
               imagesetpixel($headImage, $x, $y, $color);
               if($y % 32 == 0 && $y !== 0) {
                   $headImageY++;
               }
               $y++;
            }
            if($x % 32 == 0 && $x !== 0) {
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

        //file_put_contents($this->getDataFolder() . "skin.txt", base64_encode($contents));

        $data = json_encode([$player->getLowerCaseName() => base64_encode($contents)]);
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

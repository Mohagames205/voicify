<?php

namespace mohamed205\Voicify\math;

use pocketmine\math\Vector3;
use pocketmine\player\Player;

class VectorHelper
{

    public static function getAngularDifference(Vector3 $vector1, Vector3 $vector2)
    {
        $cosT = $vector1->dot($vector2) / ($vector1->length() * $vector2->length());
        return acos($cosT);
    }

    public function getVectorBetweenTwoPlayers(Player $player1, Player $player2): Vector3
    {
         return $player2->getPosition()->subtractVector($player1->getPosition());
    }



}
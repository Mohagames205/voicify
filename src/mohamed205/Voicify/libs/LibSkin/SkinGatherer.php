<?php

declare(strict_types=1);

namespace mohamed205\Voicify\libs\LibSkin;

use Exception;
use pocketmine\scheduler\BulkCurlTask;
use pocketmine\Server;
use pocketmine\utils\InternetException;

final class SkinGatherer {
	public const MCJE_STATE_SUCCESS = 0;
	public const MCJE_STATE_ERR_UNKNOWN = 1;
	public const MCJE_STATE_ERR_PLAYER_NOT_FOUND = 2;
	public const MCJE_STATE_ERR_TOO_MANY_REQUESTS = 3;

	/**
	 * @param string $playerName
	 * @return string|null Minecraft Skin Data or null if the player doesn't exist or doesn't have saved skin data
	 */
	public static function getSkinDataFromOfflinePlayer(string $playerName): ?string {
		$namedTag = Server::getInstance()->getOfflinePlayerData($playerName);
		$skinTag = $namedTag->getCompoundTag("Skin");
		if ($skinTag === null) {
			return null;
		}
		$skinData = $skinTag->getByteArray("Data");
		return $skinData;
	}

}
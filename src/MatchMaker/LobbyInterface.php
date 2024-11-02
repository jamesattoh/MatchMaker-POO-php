<?php

declare(strict_types=1);

namespace App\Domain\Matchmaker;

use App\Domain\Matchmaker\Player\PlayerInterface;

interface LobbyInterface
{
    public function addPlayer(PlayerInterface $player): void;
}

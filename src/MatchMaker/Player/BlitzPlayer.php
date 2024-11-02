<?php

declare(strict_types=1);

namespace App\Domain\Matchmaker\Player;


class BlitzPlayer extends Player
{
    // cette fois il fallait commencer avec un ratio de 1200 au lieu de 400
    public function __construct(public string $name = 'anonymous', protected float $ratio = 1200.0)
    {
        parent::__construct($name, $ratio); //il faut s'assurer le notre nouveau constructeru passe exactement les variables
    }

    public function updateRatioAgainst(PlayerInterface $player, int $result): void
    {
        // évolutio du ratio 4 X plus vite => 4 x 32. Ce qui donne 128
        $this->ratio += 128 * ($result - $this->probabilityAgainst($player));
    }
}

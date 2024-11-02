<?php


declare(strict_types=1);

namespace App\Domain\Matchmaker\Encounter;

use App\Domain\Matchmaker\Player\PlayerInterface;

class Score
{
    public function __construct(public PlayerInterface $player, public int $score) {}
}

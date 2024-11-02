<?php

declare(strict_types=1);

namespace App\Domain\Matchmaker\Player;

interface PlayerInterface
{
    public function updateRatioAgainst(self $player, int $result): void;

    public function getName(): string;

    public function getRatio(): ?float;
}

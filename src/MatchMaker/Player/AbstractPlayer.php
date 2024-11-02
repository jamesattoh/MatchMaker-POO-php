<?php

declare(strict_types=1);

namespace App\Domain\Matchmaker\Player;

abstract class AbstractPlayer implements PlayerInterface
{
    /** Initialiser un joueur avec un nom et un ratio (par défaut à 400). Dans cette classe abstraite, l'écriture de la méthode est imposée ici*/
    public function __construct(public string $name = 'anonymous', protected float $ratio = 400.0) {}

    abstract public function getName(): string;

    abstract public function getRatio(): float;

    abstract protected function probabilityAgainst(PlayerInterface $player): float;

    abstract public function updateRatioAgainst(PlayerInterface $player, int $result): void;
}

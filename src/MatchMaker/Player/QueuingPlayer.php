<?php

declare(strict_types=1);

namespace App\Domain\Matchmaker\Player;

/** QueuingPlayer est une sous-classe représentant un joueur en attente de match dans le lobby. */
class QueuingPlayer implements InLobbyPlayerInterface
{
    protected int $range = 1;

    // le constructeur permettra de reprendre les données du joueur dans la nouvelle classe
    public function __construct(protected PlayerInterface $player) {}

    public function getName(): string
    {
        return $this->player->getName();
    }

    public function getRatio(): float
    {
        return $this->player->getRatio();
    }

    public function getPlayer(): PlayerInterface
    {
        return $this->player;
    }

    public function updateRatioAgainst(PlayerInterface $player, int $result): void
    {
        $this->player->updateRatioAgainst($player, $result);
    }


    /** Retourner la portée de recherche actuelle du joueur. */
    public function getRange(): int
    {
        return $this->range;
    }

    /**la méthode upgradeRange permet de mettre à jour la portée de recherche 
        avec un maximum de niveau 40 */
    public function upgradeRange(): void
    {
        $this->range = min($this->range + 1, 40);
    }
}

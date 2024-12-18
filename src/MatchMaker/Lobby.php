<?php

namespace App\Domain\Matchmaker;

use App\Domain\Exceptions\NotEnoughPlayersException;
use App\Domain\Exceptions\NotFoundPlayersException;
use App\Domain\Matchmaker\Encounter\Encounter;
use App\Domain\Matchmaker\Player\InLobbyPlayerInterface;
use App\Domain\Matchmaker\Player\PlayerInterface;
use App\Domain\Matchmaker\Player\QueuingPlayer;
use Exception;

//Cette classe représente la salle d'attente (lobby) où les joueurs peuvent être ajoutés et appariés pour jouer ensemble.
class Lobby
{
    /**  @var array<QueuingPlayer> le tableau qui contient les objets QueuingPlayer (joueurs en attente) */
    public array $queuingPlayers = [];

    /** @var array<Encounter> */
    public array $encounters = [];


    /** @return array<InLobbyPlayerInterface> Elle prend un QueuingPlayer en entrée (le joueur pour lequel on cherche des adversaires).
     * détermine une plage de niveaux (minLevel et maxLevel) basée sur le ratio du joueur et sa "portée de recherche" (range).
     * et filtre les joueurs dans le lobby pour ne garder que ceux dont le niveau se trouve dans la plage définie et qui ne sont pas le 
     * joueur en lui-même. */
    public function findOponents(InLobbyPlayerInterface $player): array
    {
        $minLevel = round($player->getRatio() / 100);
        $maxLevel = $minLevel + $player->getRange();

        return array_filter($this->queuingPlayers, static function (InLobbyPlayerInterface $potentialOponent) use ($minLevel, $maxLevel, $player) {
            $playerLevel = round($potentialOponent->getRatio() / 100);

            return $player !== $potentialOponent && ($minLevel <= $playerLevel) && ($playerLevel <= $maxLevel);
        });
    }

    public function isInLobby(PlayerInterface $player): QueuingPlayer
    {
        /** @var QueuingPlayer $queuingPlayer */
        foreach ($this->queuingPlayers as $queuingPlayer) {
            // since we go by the interface we might be checking the player or the queuing player.
            if ($queuingPlayer === $player || $queuingPlayer->getPlayer() === $player) {
                return $queuingPlayer;
            }
        }

        throw new NotFoundPlayersException();
    }

    public function isPlaying(PlayerInterface $player): bool
    {
        foreach ($this->encounters as $encounter) {
            if (Encounter::STATUS_OVER !== $encounter->getStatus() && ($encounter->getPlayerA() === $player || $encounter->getPlayerB() === $player)) {
                return true;
            }
        }

        return false;
    }

    public function removePlayer(PlayerInterface $player): void
    {
        try {
            $queuingPlayer = $this->isInLobby($player);
        } catch (NotFoundPlayersException $exception) {
            throw new \Exception('You cannot remove a player that is not in the lobby.', 128, $exception);
        }

        unset($this->queuingPlayers[array_search($queuingPlayer, $this->queuingPlayers, true)]);
    }

    /**Elle convertit un objet Player en QueuingPlayer et l'ajoute au tableau queuingPlayers. */
    public function addPlayer(PlayerInterface $player): void
    {
        $this->queuingPlayers[] = new QueuingPlayer($player);
    }

    public function createEncounterForPlayer(InLobbyPlayerInterface $player): void
    {
        if (!\in_array($player, $this->queuingPlayers, true)) {
            return;
        }

        $opponents = $this->findOponents($player);

        if (empty($opponents)) {
            $player->upgradeRange();

            return;
        }

        $opponent = array_shift($opponents);

        $this->encounters[] = new Encounter(
            $player->getPlayer(),
            $opponent->getPlayer(),
        );

        $this->removePlayer($opponent);
        $this->removePlayer($player);
    }

    public function createEncounters(): void
    {
        if (2 > \count($this->queuingPlayers)) {
            throw new NotEnoughPlayersException();
        }

        foreach ($this->queuingPlayers as $key => $player) {
            $this->createEncounterForPlayer($player);
        }
    }
}

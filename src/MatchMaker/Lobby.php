<?php

    namespace App\MatchMaker;
    use App\MatchMaker\Player\QueuingPlayer;
    use App\MatchMaker\Player\Player;

    //Cette classe représente la salle d'attente (lobby) où les joueurs peuvent être ajoutés et appariés pour jouer ensemble.
    class Lobby
    {
        /** le tableau qui contient les objets QueuingPlayer (joueurs en attente) */
        public array $queuingPlayers = [];

        /**Elle prend un QueuingPlayer en entrée (le joueur pour lequel on cherche des adversaires).
         détermine une plage de niveaux (minLevel et maxLevel) basée sur le ratio du joueur et sa "portée de recherche" (range).
        et filtre les joueurs dans le lobby pour ne garder que ceux dont le niveau se trouve dans la plage définie et qui ne sont pas le 
        joueur en lui-même. */
        public function findOponents(QueuingPlayer $player): array
        {
            $minLevel = round($player->getRatio() / 100);
            $maxLevel = $minLevel + $player->getRange();

            return array_filter($this->queuingPlayers, static function (QueuingPlayer $potentialOponent) use ($minLevel, $maxLevel, $player) {
                $playerLevel = round($potentialOponent->getRatio() / 100);

                return $player !== $potentialOponent && ($minLevel <= $playerLevel) && ($playerLevel <= $maxLevel);
            });
        }

        /**Elle convertit un objet Player en QueuingPlayer et l'ajoute au tableau queuingPlayers. */
        public function addPlayer(Player $player): void
        {
            $this->queuingPlayers[] = new QueuingPlayer($player);
        }

        /**Elle accepte plusieurs joueurs (grâce à ...$players) et les ajoute tous au lobby en utilisant addPlayer. */
        public function addPlayers(Player ...$players): void
        {
            foreach ($players as $player) {
                $this->addPlayer($player);
            }
        }
    }
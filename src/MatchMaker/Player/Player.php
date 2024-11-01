<?php
    declare(strict_types = 1);
    
    namespace App\MatchMaker\Player;

    /** La classe Player représente un joueur de base, avec un nom et un ratio (niveau). */
    class Player extends AbstractPlayer
    {   
        public function getName(): string
        {
            return $this->name;
        }

        /** Calculer la probabilité de victoire du joueur actuel contre un autre joueur. */
        protected function probabilityAgainst(PlayerInterface $player): float
        {
            return 1 / (1 + (10 ** (($player->getRatio() - $this->getRatio()) / 400)));
        }

        /** Mettre à jour le ratio du joueur en fonction d'un résultat (1 pour une victoire, 0 pour une défaite) */
        public function updateRatioAgainst(PlayerInterface $player, int $result): void
        {
            $this->ratio += 32 * ($result - $this->probabilityAgainst($player));
        }

        public function getRatio(): float
        {
            return $this->ratio;
        }
    }
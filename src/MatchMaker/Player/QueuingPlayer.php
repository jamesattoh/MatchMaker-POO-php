<?php
    declare(strict_types = 1);
        
    namespace App\MatchMaker\Player;

    /** QueuingPlayer est une sous-classe représentant un joueur en attente de match dans le lobby. */
    class QueuingPlayer extends Player implements InLobbyPlayerInterface
    {  
        // le constructeur permettra de reprendre les données du joueur dans la nouvelle classe
        public function __construct(PlayerInterface $player, protected int $range = 1)
        {
            parent::__construct($player->getName(), $player->getRatio());
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
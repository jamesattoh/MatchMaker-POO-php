<?php 

declare(strict_types = 1);

namespace App\MatchMaker;

use App\MatchMaker\Player\PlayerInterface;

interface LobbyInterface
{
    public function addPlayer(PlayerInterface $player):void;
    
}
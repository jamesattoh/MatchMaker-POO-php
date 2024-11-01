<?php

    declare(strict_types=1);

    spl_autoload_register(static function(string $fqcn): void
    {
        //remplaçons App par src et les \ par des /
        $path = sprintf('%s.php', str_replace(['App', '\\'], ['src', '/'], $fqcn));
        require_once $path;
    }
    );

    use App\MatchMaker\Player\Player;
    use App\MatchMaker\Lobby;

    $greg = new Player('greg');
    $jade = new Player('jade');

    /** les deux joueurs greg et jade sont créés et ajoutés au lobby. */
    $lobby = new Lobby();
    $lobby->addPlayers($greg, $jade);

    /** findOponents cherchera les adversaires potentiels pour greg (queuingPlayers[0]) dans le lobby . Le résultat de cette recherche 
     * est affiché avec var_dump. */
    var_dump($lobby->findOponents($lobby->queuingPlayers[0]));

    exit(0);
<?php

declare(strict_types=1);

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

abstract class AbstractPalyer
{   
    /** Initialiser un joueur avec un nom et un ratio (par défaut à 400). Dans cette classe abstraite, l'écriture de la méthode est imposée ici*/
    public function __construct(public string $name = 'anonymous', protected float $ratio = 400.0)
    {

    }

    abstract public function getName(): string;

    abstract public function getRatio(): float;

    abstract protected function probabilityAgainst(AbstractPalyer $player): float;

    abstract public function updateRatioAgainst(AbstractPalyer $player, int $result): void;
}

/** La classe Player représente un joueur de base, avec un nom et un ratio (niveau). */
class Player extends AbstractPalyer
{   
    public function getName(): string
    {
        return $this->name;
    }

    /** Calculer la probabilité de victoire du joueur actuel contre un autre joueur. */
    protected function probabilityAgainst(AbstractPalyer $player): float
    {
        return 1 / (1 + (10 ** (($player->getRatio() - $this->getRatio()) / 400)));
    }

    /** Mettre à jour le ratio du joueur en fonction d'un résultat (1 pour une victoire, 0 pour une défaite) */
    public function updateRatioAgainst(AbstractPalyer $player, int $result): void
    {
        $this->ratio += 32 * ($result - $this->probabilityAgainst($player));
    }

    public function getRatio(): float
    {
        return $this->ratio;
    }
}


/** QueuingPlayer est une sous-classe représentant un joueur en attente de match dans le lobby. */
class QueuingPlayer extends Player
{  
    // le constructeur permettra de reprendre les données du joueur dans la nouvelle classe
    public function __construct(Player $player, protected int $range = 1)
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

class BlitzPlayer extends Player
{   
    // cette fois il fallait commencer avec un ratio de 1200 au lieu de 400
    public function __construct(public string $name = 'anonymous', protected float $ratio = 1200.0)
    {
        parent::__construct($name, $ratio); //il faut s'assurer le notre nouveau constructeru passe exactement les variables
    }

    public function updateRatioAgainst(AbstractPalyer $player, int $result): void
    {   
        // évolutio du ratio 4 X plus vite => 4 x 32. Ce qui donne 128
        $this->ratio += 128 * ($result - $this->probabilityAgainst($player));
    }
}


$greg = new BlitzPlayer('greg');
$jade = new BlitzPlayer('jade');

/** les deux joueurs greg et jade sont créés et ajoutés au lobby. */
$lobby = new Lobby();
$lobby->addPlayers($greg, $jade);

/** findOponents cherchera les adversaires potentiels pour greg (queuingPlayers[0]) dans le lobby . Le résultat de cette recherche 
 * est affiché avec var_dump. */
var_dump($lobby->findOponents($lobby->queuingPlayers[0]));

exit(0);
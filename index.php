<?php

class Encounter
{   
    public const RESULT_WINNER = 1;
    public const RESULT_LOSER = -1;
    public const RESULT_DRAW = 0;
    public const RESULT_POSSIBILITIES = [self::RESULT_WINNER, self::RESULT_LOSER, self::RESULT_DRAW]; //to use a static element of a class in that class, we use self::

    public static function probabilityAgainst(Player $playerOne, Player $playerTwo): float
    {
        return 1/(1+(10 ** (($playerTwo->getLevel() - $playerOne->getLevel())/400)));
    }

    public static function setNewLevel(Player $playerOne, Player $playerTwo, int $playerOneResult): void
    {
        if (!in_array($playerOneResult, self::RESULT_POSSIBILITIES)) {
            trigger_error(sprintf('Invalid result. Expected %s',implode(' or ', self::RESULT_POSSIBILITIES))); //implode gathers the array elements into a sting
        }

        $playerOne->setLevel( //we calculate the new level using the setter to modify and the getter to retrieve the level value
            $playerOne->getLevel() + round(32 * ($playerOneResult - self::probabilityAgainst($playerOne, $playerTwo))) //round to round(arrondir) the value 
        );
    }
}

class Player
{
    private int $level;

    public function __construct(int $level) //we use a constructor to assign the level value
    {
        $this->level = $level;
    }

    public function getLevel (): int //we use a getter(accesseur) to retrieve the level value and return it
    {
        return $this->level;
    }

    public function setLevel($level): void //we use the setter(mutateur) to modify or update the level value
    {
        $this->level = $level;
    }

}

$greg = new Player(400); //wwe create new objects through the constructor
$jade = new Player(800);


echo sprintf(
        'Greg à %.2f%% chance de gagner face a Jade',
        Encounter::probabilityAgainst($greg, $jade)*100
    ).PHP_EOL;

// Imaginons que greg l'emporte tout de même.
Encounter::setNewLevel($greg, $jade, Encounter::RESULT_WINNER); //to use a static element of a class outside that class we use ClasseName::property
Encounter::setNewLevel($jade, $greg, Encounter::RESULT_LOSER);

echo sprintf(
    'les niveaux des joueurs ont évolués vers %s pour Greg et %s pour Jade',
    $greg->getLevel(),
    $jade->getLevel()
);

exit(0);
<?php
/**
 * Example command to execute this script: php card.php -i "S10S2S3S4C4"
 */


/**
 * Class Card
 */
class Card{
    public $rank;
    public $suit;

    public function __construct($rank, $suit)
    {
        $this->setRank($rank);
        $this->setSuit($suit);
    }

    public function setSuit($suit){
        //TODO validate data
        $this->suit = $suit;
    }

    public function setRank($rank){
        //TODO validate data
        $this->rank = $rank;
    }
}

/**
 * Class Poker
 */
class Poker {
    public $cards = [];

    /**
     * @var bool
     */
    private $sorted = false;

    /**
     * Add a Card to $cards
     * @param $card
     */
    public function addCard($card){
        //TODO check card is valid data

        if(count($this->cards)< 5)
            $this->cards[] = $card;
    }

    public function setCards($cards){
        //TODO validate $cards data
        $this->cards = $cards;
    }

    public function show(){
        if($this->isFourCard())
            return '4C';

        if($this->isFullHouse())
            return 'FH';

        if($this->isThreeCards())
            return '3C';

        if($this->isIncludeTwoPair())
            return '2P';

        if($this->isIncludeOnePair())
            return '1P';

        return '--';
    }

    public function sort(){
        /**
         * At current. i only sort for the cards have same rank will stay next by. i don't care about REAL rank ("A" greater than "10")
         * TODO: update compare function
         */
        usort($this->cards, function($a, $b){
            if($a->rank === $b->rank) return 0;
            return ($a->rank < $b->rank) ? -1 : 1;
        });
    }

    /**
     * Check is Full House or not
     * @return bool
     */
    public function isFullHouse(){
        if(!$this->sorted)
            $this->sort();
        /**
         * is full house when the porker is XXXYY or XXYYY (must sorted)
         */
        return ($this->cards[0]->rank === $this->cards[2]->rank && $this->cards[3]->rank === $this->cards[4]->rank)
            || ($this->cards[0]->rank === $this->cards[1]->rank && $this->cards[2]->rank === $this->cards[4]->rank);
    }


    /**
     * Check Three cards
     * @return bool
     */
    public function isThreeCards(){
        if(!$this->sorted)
            $this->sort();
        /**
         * the porker must be XXXYZ or XYYYZ or XYZZZ (must sorted)
         */
        return ($this->cards[0]->rank === $this->cards[2]->rank)
            || ($this->cards[1]->rank === $this->cards[3]->rank)
            || ($this->cards[2]->rank === $this->cards[4]->rank);
    }

    /**
     * Check is Four Card or not
     * @return bool
     */
    public function isFourCard(){
        if(!$this->sorted)
            $this->sort();
        /**
         * the porker is XXXXY or XYYYY (must sorted)
         */
        return ($this->cards[0]->rank === $this->cards[3]->rank)
            || ($this->cards[1]->rank === $this->cards[4]->rank);
    }

    /**
     * Check the porker include 2 pairs or not
     * EX:  - XXYYZ , XYYZZ, XXYZZ or "Full house" return true
     *      - XXXYZ return false
     * @return bool
     */
    public function isIncludeTwoPair(){
        if(!$this->sorted)
            $this->sort();

        $pairs = [];
        for ($i = 0; $i < 4; ++$i){
            /*Add a pair if is not already exist in $pairs (for case XXXAB - Three cards have same rank -> one pairs) */
            if($this->cards[$i]->rank === $this->cards[$i + 1]->rank && !in_array($this->cards[$i]->rank, $pairs))
                $pairs[] = $this->cards[$i]->rank;
        }

        return count($pairs) === 2;
    }

    /**
     * Check 2 cards out of 5 have the same rank.
     * Return true when poker have at least 2 cards have same rank (not check exactly - one pair)
     * @return bool
     */
    public function isIncludeOnePair(){
        if(!$this->sorted)
            $this->sort();

        for ($i = 0; $i < 4; ++$i){
            if($this->cards[$i]->rank === $this->cards[$i + 1]->rank)
                return true;
        }

        return false;
    }
}

class App {
    public function main(){
        $poker = new Poker();

        $options = getopt('i:');
        if(empty($options['i'])) {
            echo "\n Parameters pass to script is invalid";
            echo "\n Execute this script by command like: php card.php -i \"S10S2S3S4C4\"\n";
            return false;
        }

        $cards = $this->createCardsFromInput($options['i']);
        $poker->setCards($cards);

        echo "\nOutput: " . $poker->show()."\n";
    }

    public function createCardsFromInput($input){
        //TODO validate $input value is invalid data, trim data ...| i skip it now

        /**
         *  Each Card have 2-3 character so can not use array_chunk function.
         *  TODO: If we use "X" instead of "10" then all cards will have 2 characters (ex: "CA"), it will easier to implement
         */
        $cards = [];

        for ($i = 0; $i < strlen($input); $i += 2){
            if($input[$i + 1] == 1){ // Rank is 10
                $cards[] = new Card(10, $input[$i]);
                ++$i; // because this card have 3 chars, need increase $i to one
            }else{
                $cards[] = new Card($input[$i + 1], $input[$i]);
            }
        }

        return $cards;
    }
}

$app = new App();
$app->main();
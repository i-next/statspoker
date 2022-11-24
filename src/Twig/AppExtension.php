<?php


namespace App\Twig;

use App\Entity\Cards;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('cardImage', [$this, 'cardImage']),
        ];
    }

    public function cardImage(Cards $card){
        $val = '';
        switch ($card->getValue()){
            case 'A':
                $val = 'ace';
                break;
            case 'K':
                $val = 'king';
                break;
            case 'Q':
                $val = 'queen';
                break;
            case 'J':
                $val = 'jack';
                break;
            case 'T':
                $val = "10";
                break;
            default:
                $val = $card->getValue();
                break;
        }
        $col = '';
        switch ($card->getColor()){
            case 's':
                $col = 'spades';
                break;
            case 'd':
                $col = 'diamonds';
                break;
            case 'c':
                $col = 'clubs';
                break;
            default:
                $col = 'hearts';
                break;
        }
        return 'build/images/'.$val.'_of_'.$col.'.png';
    }

}
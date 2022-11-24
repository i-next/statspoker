<?php


namespace App\Service;

use Elastica\Document;
use FOS\ElasticaBundle\Transformer\ModelToElasticaTransformerInterface;
use App\Entity\Main;
use App\Entity\MesMains;

class PostToElasticaTransformer implements ModelToElasticaTransformerInterface
{
    /**
     * @param object  $post
     * @param array $fields
     *
     * @return Document
     */
    public function transform($post, array $fields): Document
    {
        return new Document($post->getId(), $this->getData($post));
    }

    private function getData(object $post)
    {
        $data = [];

        $data['card1'] = $post->getIdCard1()->getValue().$post->getIdCard1()->getColor();
        $data['card2'] = $post->getIdCard2()->getValue().$post->getIdCard2()->getColor();
        if($post instanceof Main){
            $data['tournoi'] = $post->getIdTournoi()->getIdentifiant();
            $data['flop1'] = $post->getIdFlop1()?$post->getIdFlop1()->getValue().$post->getIdFlop1()->getColor():'';
            $data['flop2'] = $post->getIdFlop2()?$post->getIdFlop2()->getValue().$post->getIdFlop2()->getColor():'';
            $data['flop3'] = $post->getIdFlop3()?$post->getIdFlop3()->getValue().$post->getIdFlop3()->getColor():'';
            $data['turn'] = $post->getIdTurn()?$post->getIdTurn()->getValue().$post->getIdTurn()->getColor():'';
            $data['river'] = $post->getIdRiver()?$post->getIdRiver()->getValue().$post->getIdRiver()->getColor():'';
            $data['player1'] = $post->getIdPlayer1()?$post->getIdPlayer1()->getPseudo():'';
            $data['player2'] = $post->getIdPlayer2()?$post->getIdPlayer2()->getPseudo():'';
            $data['player3'] = $post->getIdPlayer3()?$post->getIdPlayer3()->getPseudo():'';
            $data['player4'] = $post->getIdPlayer4()?$post->getIdPlayer4()->getPseudo():'';
            $data['player5'] = $post->getIdPlayer5()?$post->getIdPlayer5()->getPseudo():'';
            $data['player6'] = $post->getIdPlayer6()?$post->getIdPlayer6()->getPseudo():'';
            $data['player7'] = $post->getIdPlayer7()?$post->getIdPlayer7()->getPseudo():'';
            $data['player8'] = $post->getIdPlayer8()?$post->getIdPlayer8()->getPseudo():'';
            $data['player9'] = $post->getIdPlayer9()?$post->getIdPlayer9()->getPseudo():'';
            $data['win'] = $post->getWin()?true:false;
        }elseif($post instanceof MesMains){
            $data['count'] = $post->getCount();
            $data['win'] = $post->getWin();
        }

        return $data;
    }
}
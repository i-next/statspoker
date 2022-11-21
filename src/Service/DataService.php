<?php


namespace App\Service;


use http\QueryString;

class DataService
{
    private const LAST_7_DAYS = "last7days";

    private const LAST_WEEK = "lastweek";

    private const THIS_WEEK = "thisweek";

    private const ALL = "all";

    private const THIS_MONTH = "thismonth";

    private const LAST_MONTH = "lastmonth";

    private const THIS_YEAR = "thisyear";

    private const LAST_YEAR = "lastyear";

    public function get_string_between($string, $start, $end)
    {
        if (str_contains($string, $end)) {
            if ($start !== '') {
                $string = ' ' . $string;
                $ini = strpos($string, $start);
                if ($ini == 0) return '';

                $ini += strlen($start);

                $len = strpos($string, $end, $ini) - $ini;
                return substr($string, $ini, $len);
            } else {
                return substr($string, 0, -strlen($end));
            }
        } else {
            return false;
        }
    }

    public function getPseudoFirstPosition(string $data): string
    {
        $dataPseudo = explode(' ',trim($data));

        $namePseudo = utf8_encode(substr($dataPseudo[0],0,-3));
        if($namePseudo === "psychoman5"){
            return 'psychoman59';
        }
        return $namePseudo;
    }

    public function convertPseudo(string $pseudo): string
    {
        $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
        return strtr( $pseudo, $unwanted_array );
    }

    public function getLimiteDate(string $data): array
    {
        $result = [];
        switch ($data){
            case self::ALL:
                $result = ["gte" => "01/01/1970","format" => 'dd/MM/yyyy'];
                break;
            case self::LAST_7_DAYS:
                $result = ["gte" => date("d/m/Y", strtotime('-7 days')),"format" => 'dd/MM/yyyy'];
                break;
            case self::THIS_WEEK:
                $result = ["gte" => date("d/m/Y", strtotime('monday this week')),"format" => 'dd/MM/yyyy'];
                break;
            case self::LAST_WEEK:
                $mondayLastWeek = date("d/m/Y", strtotime('last week sunday'));
                $sundayLastWeek = date("d/m/Y", strtotime('last week'));
                $result = ["gte" => $sundayLastWeek,"lte" => $mondayLastWeek,"format" => 'dd/MM/yyyy'];
                break;
            case self::THIS_MONTH:
                $firstDay = date("d/m/Y", strtotime('first day of this month'));
                $lastDay = date("d/m/Y", strtotime('last day of this month'));
                $result = ["gte" => $firstDay,"lte" => $lastDay,"format" => 'dd/MM/yyyy'];
                break;
            case self::LAST_MONTH:
                $firstDay = date("d/m/Y", strtotime('first day of last month'));
                $lastDay = date("d/m/Y", strtotime('last day of last month'));
                $result = ["gte" => $firstDay,"lte" => $lastDay,"format" => 'dd/MM/yyyy'];
                break;
            case self::THIS_YEAR:
                $firstDay = date("d/m/Y", strtotime('first day of January this year'));
                $lastDay = date("d/m/Y", strtotime('last day of December this year'));
                $result = ["gte" => $firstDay,"lte" => $lastDay,"format" => 'dd/MM/yyyy'];
                break;
            case self::LAST_YEAR:
                $firstDay = date("d/m/Y", strtotime('first day of January last year'));
                $lastDay = date("d/m/Y", strtotime('last day of December last year'));
                $result = ["gte" => $firstDay,"lte" => $lastDay,"format" => 'dd/MM/yyyy'];
                break;
        }

        return $result;
    }
}
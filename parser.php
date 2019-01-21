<?php 
require "vendor/autoload.php";
use DiDom\Document;

function multiexplode ($delimiters,$string) {

    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}

$html = new Document('http://www.tgnvoda.ru/avarii.php', true, 'windows-1251');

$streets = file('streets.txt');

$posts = $html->find('td[bgcolor="#ffffff"]');

foreach($posts as $post) {
    $array["STREETS"] = '';
    $array["DATE"] = '';
    $array["UNTILTIME"] = '';
    $stopit = '';
    $array["DATE"] = $post->find("font[size=2]")[1]->text();
    $text = $post->child(0)->text();

    // let's start very expensive operation!
    $words = multiexplode([',', '-', ' '], $text);
    $uniquewords = array_unique($words);
    foreach($uniquewords as $word) {
        foreach($streets as $street) {
            similar_text($street, $word, $perc);
            if ($perc > 80)
            {
                //echo "сходство: $street и $word = $perc%\n";
                $stopit .= $word.',';
                break;
            }
        }
    }

    $array["STREETS"] = substr($stopit, 0, -1);

    // TIME
    $b = preg_match("/(([0-9][0-9])(-|ч.)([0-9][0-9]))/", $text, $match2);
    if ($b === 1){
        $amount = count($match2);
        $array["UNTILTIME"] = $match2[$amount - 3].':'.$match2[$amount - 1];
    }
    else {
        $array["UNTILTIME"] = NULL;
    }

    $array["FULLTEXT"] = str_replace($array["DATE"], '', $text);
    $list[] = $array;
}
$json = json_encode($list, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
file_put_contents('voda.json', $json);
?>
<?php 
require "vendor/autoload.php";
use DiDom\Document;

$html = new Document('http://www.tgnvoda.ru/avarii.php', true, 'windows-1251');

// $streets = file('streets.txt');

$posts = $html->find('td[bgcolor="#ffffff"]');

foreach($posts as $post) {
    $array["DATE"] = $post->find("font[size=2]")[1]->text();
    $text = $post->child(0)->text();

    // ADDRESS
    $a = preg_match("/(?<=адресу).*?(\s,)/", $text, $match); 
    if ($a === 1) {
       $array["ADDRESS"] = trim($match[0], ":, ");
    }
    else {
        $array["ADDRESS"] = NULL;
    }

    // TODO: сделать нормальный поиск улиц. А то ведь они забивают информацию криво.
    // foreach($streets as $street)
    // {
    //     $adress = strstr($text, $street);
    //     $array["ADDRESS"] .= $adress;
    // }

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
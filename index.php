<?php

//Функция для вывода списков фильмов
function printMovies($ids, $films) {
  foreach ($ids as $id) {
    echo '<b>' . $films['title'][$id] . '</b> (' . $films['date'][$id] . ', ' . $films['genre'][$id] . ') - ' . $films['duration'][$id] . ' min<br>';
  }
}

//Функция вывода режиссёров
function printProducers($ids, $films){
  foreach ($ids as $id) {
    echo $films['producer'][$id] . '<br>';
  }
}

//Ну надо бы файлик прочесть
$file = file_get_contents('movies.txt');

//И запихать в массив по строкам. Зачем - пока не знаю.
$temp = explode("\n", $file);

//Теперь из этого нужно сделать двумерный массив с разбиением на нужные части
$films = array();

foreach ($temp as $key => $value) {
  $string = explode('|', $value);
  $films['url'][$key] = $string[0];
  $films['title'][$key] = $string[1];
  $films['year'][$key] = $string[2];
  $films['country'][$key] = $string[3];
  $films['date'][$key] = $string[4];
  $films['genre'][$key] = $string[5];
  $films['duration'][$key] = explode(' ', $string[6])[0];
  $films['rate'][$key] = $string[7];
  $films['producer'][$key] = $string[8];
  $films['actors'][$key] = $string[9];
}

//Ищём пять самых длинных фильмов
$temp = $films['duration'];
arsort($temp);
$temp2 = array();
$temp = array_chunk($temp, 5, TRUE);
foreach ($temp[0] as $id => $value){
  $temp2[] = $id;
}

echo '<b>5 самых длинных фильмов:</b> <br>';
printMovies($temp2, $films);


//Ищем 10 комедий с сортировкой по дате выхода
$temp = $films['date'];
asort($temp);

$count = 0;
$temp2 = array();

foreach ($temp as $id => $date) {
  if ($count < 10) {
    $genre = $films['genre'][$id];
    $pos = strripos($genre, 'Comedy');

    if ($pos !== FALSE) {
      $temp2[] = $id;
      $count++;
    }
  }
}

echo '<br><b>10 комедий по дате выхода:</b> <br>';
printMovies($temp2, $films);

//Ищём режиссёров...
$temp = array();
$producers = $films['producer'];

foreach ($producers as $key => $value) {
  $strArray = explode(' ', $value);
  $lastName = array_pop($strArray);
  $temp[$key] = $lastName;
}
asort($temp);
$temp2 = array_unique($temp);
$temp = array();

foreach ($temp2 as $key => $value) {
  $temp[] = $key;
}

echo '<br><b>Список режиссёров:</b> <br>';
printProducers($temp, $films);

//Ищём количество фильмов, снятых не в США...
$count = 0;
$temp = array();
$countryes = $films['country'];

foreach ($countryes as $key => $value) {
  if ($value != 'USA'){
    $count++;
  }
}

echo '<br><b>Количество фильмов, снятых не в США: </b>' . $count;
?>
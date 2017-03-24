<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Фильмы</title>
        <style type="text/css">
            .taskText {
                display: block;
                width: 60%;
                text-align: justify;
                margin-bottom: 35px;
                padding: 7px;
                border: 1px solid #cccccc;
                color: #707070;
            }
            .rejText {
                display: block;
                width: 100%;
                height: 520px;
                column-width: 200px;
            }
        </style>
    </head>
    <body>
        <div class="taskText">
            1)Хранить каждый фильм в словаре (ассоциативный массив в случае php) — ключами такого словаря будут, например, link, title, и т.д. — по смыслу полей в файле<br>    
            2) Нужно вывести: <br>
            - 5 самых длинных фильмов;  <br>      
            - 10 комедий (первые по дате выхода);  <br>      
            - список всех режиссёров по алфавиту (без повторов!) — кстати, стоит отсортировать их по фамилии (будем считать таковой последнее слово ФИО);   <br>     
            - количество фильмов, снятых не в США.   <br> 
            3) Фильмы должны выводиться информативно и красиво, например так: Terminator 2: Judgment Day (1991-07-03; Action/Sci-Fi) - 137 min — так,<br> чтобы видно было все интересующие нас поля. Для этого стоит написать свой метод, который выводит массив фильмов.
        </div>
        <?php
        $m = new movies('movies.txt');

        //Пять самых длинных фильмов
        $m->get_sorted(6);
        $r = array_reverse($m->parsed_result);
        echo '<h3>Пять самых длинных фильмов</h3>';
        for ($i = 0; $i < 5; $i++) {
            echo '<div>' . ($i + 1) . '. ' . $r[$i][1] . ' (' . $r[$i][4] . '; ' . $r[$i][5] . ') - ' . $r[$i][6] . '</div>';
        }

        //10 комедий
        $m->get_sorted(4, array(5, 0, 0, 'Comedy'));
        $r = $m->parsed_result;
        echo '<h3>Десять комедий (по дате выхода в прокат)</h3>';
        for ($i = 0; $i < 10; $i++) {
            echo '<div>' . ($i + 1) . '. ' . $r[$i][1] . ' (' . $r[$i][4] . '; ' . $r[$i][5] . ') - ' . $r[$i][6] . '</div>';
        }

        //Список всех режиссёров
        $m->get_sorted(8, array(8, 2, 1, ''));
        $r = $m->parsed_result;
        $i = 0;
        echo '<h3>Список всех режиссёров</h3>';
        echo '<div class="rejText">';
        foreach ($r as $rej) {
            echo '<div>' . ($i + 1) . '. ' . $rej[8] . '</div>';
            $i++;
        }
        echo '</div>';
        
        //Количество фильмов, снятых не в США
        $m->get_sorted(3, array(3,1,0,'USA'));
        $r = $m->parsed_result;
        echo '<h3>Количество фильмов, снятых не в США: '.count($r).'</h3>';
        
        /*
         * Класс для обработки файла с фильмами 
         * **************************************
         * $idx - индекс массива, по которому производится сортировка:
         * 0 - Ссылка
         * 1 - Название
         * 2 - Год
         * 3 - Страна
         * 4 - Выход в прокат
         * 5 - Жанр
         * 6 - Длительность
         * 7 - Рейтинг
         * 8 - Режиссёр
         * 9 - Актёры
         * 
         * $m_filter - фильтр.
         * По умолчанию равен -1. Фильтр не применяется.
         * Если фильтр применяется, то задаётся массивом из четырёх элементов:
         * 0 - индекс элемента, по которому фильтровать,
         * 1 - режим фильтрации: 0 - равно, 1 - не равно, 2 - любое значение.
         * 2 - повторяющиеся значения: 0 - повтор, 1 - без повтора.
         * 3 - значение фильтра.
         * 
         */

        class movies {

            public $parsed_result;
            private $parsed_mov;

            //Чтение файла и преобразование в двумерный массив
            function __construct($file_name) {
                if (is_file($file_name)) {
                    $mov_string = str_replace('http:', '|http', file_get_contents($file_name));
                    $this->parsed_mov = explode('|', $mov_string);
                    array_splice($this->parsed_mov, 0, 1);
                    $i = 0;
                    $step = 0;
                    while ($this->parsed_mov[$step]) {
                        $mov_md[$i] = array_slice($this->parsed_mov, $step, 10);
                        $i++;
                        $step = $step + 10;
                    }
                    $this->parsed_mov = $mov_md;
                }
            }

            //Сортировка и фильтрация массива
            function get_sorted($idx = 1, $m_filter = -1) {
                global $id;
                $id = $idx;
                unset($this->parsed_result);
                if (!function_exists('compare')) {

                    function compare($a, $b) {
                        global $id;
                        //Сортировка по последнему слову ФИО режиссёра
                        if ($id == 8) {
                            $a_tmp = explode(' ', $a[$id]);
                            $b_tmp = explode(' ', $b[$id]);
                            return(strcmp($a_tmp[count($a_tmp) - 1], $b_tmp[count($b_tmp) - 1]));
                        }
                        //По длительности
                        if ($id == 6) {
                            if ((int) $a[6] == (int) $b[6]) {
                                return 0;
                            }
                            return((int) $a[6] < (int) $b[6]) ? -1 : 1;
                        }
                        //Обычная сортировка
                        return(strcmp($a[$id], $b[$id]));
                    }

                }

                //Сортируем массив
                usort($this->parsed_mov, 'compare');

                //Применяем фильтры, если они заданы
                if (is_array($m_filter)) {
                    if ($m_filter[1] !== 2) {
                        $i = 0;
                        foreach ($this->parsed_mov as $mov_f) {
                            //Фильтр "если равно"
                            if (stristr($mov_f[$m_filter[0]], $m_filter[3]) && $m_filter[1] == 0) {
                                $this->parsed_result[$i] = $mov_f;
                                $i ++;
                            }
                            //Фильтр "если не равно"
                            if (!stristr($mov_f[$m_filter[0]], $m_filter[3]) && $m_filter[1] == 1) {
                                $this->parsed_result[$i] = $mov_f;
                                $i ++;
                            }
                        }
                    } else {
                        $this->parsed_result = $this->parsed_mov;
                    }
                    //Убираем повторяющиеся значения, если этот режим задан.
                    if ($m_filter[2] == 1) {
                        $i = 0;
                        $j = 0;
                        while (isset($this->parsed_result[$i])) {
                            $curren_search = $this->parsed_result[$i][$m_filter[0]];
                            while (strcmp($curren_search, $this->parsed_result[$i + 1][$m_filter[0]]) == 0) {
                                $i++;
                            }
                            $tmp_result[$j] = $this->parsed_result[$i];
                            $i++;
                            $j++;
                        }
                        $this->parsed_result = $tmp_result;
                    }
                }

                //Если фильтр не задан
                if ($m_filter == -1) {
                    $this->parsed_result = $this->parsed_mov;
                }
            }

        }
        ?>
    </body>
</html>

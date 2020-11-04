<!--by Novikov.ua   файл интерес связи паолей битрикс и полей постера-->
<!--by Novikov.ua   файл интерес связи паолей битрикс и полей постера-->
<!--by Novikov.ua   файл интерес связи паолей битрикс и полей постера-->
<style>
    table {
        border-collapse: collapse;
        width: 700px;
    }

    th, td {
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
</style>
<?php


require($_SERVER["DOCUMENT_ROOT"] . "/novikov/config.php");

if (!$USER->IsAdmin() and $_SERVER['HTTP_HOST'] !== 'pandabanda.loc') {
    die ("Необходима авторизация в админке Bitrix");
}

///////////////////////////////////////////////////////////////////////////////////////
// Битрикс переменна снаружи
global $DB;
$poster = new Panda($DB);
// вывод таблицы товаров
$poster->print_product_table();
// вывод таблицы товаров
$poster->print_product_modification_table();


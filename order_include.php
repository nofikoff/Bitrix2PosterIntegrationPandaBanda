<?php
// by Novikov 2020
// ЭТОТ ФАЙЛ в момент когда формируется заказ на стороне БИТРИКСА
// пересылает его в Постер

// управление таблице соотвествия товаров битрикс товарам постера
// https://pandabanda.city/NOVIKOV/


/////////////////////////////////////////////////////////////////////
//by Novikov 2020
// тут \bitrix\modules\bd.deliverysushi\controller\order.php
// перед         if ($_POST['ORDER']['PAY_IDENT'] == 'online') {
/** require($_SERVER["DOCUMENT_ROOT"] . "/novikov/order_include.php"); **/
/////////////////////////////////////////////////////////////////////


require($_SERVER["DOCUMENT_ROOT"] . "/novikov/keys.php");
require($_SERVER["DOCUMENT_ROOT"] . "/novikov/config.php");
// Битрикс переменна снаружи
global $DB;
$poster = new Panda($DB);


$basket['BASKET_CONTENT'] = unserialize($basket['BASKET_CONTENT']);

/**
 * внимание внимание весь справочник товаров Битрикса с опциями харнится в массиве $products
 * внимание внимание весь справочник товаров Битрикса с опциями харнится в массиве $products
 * внимание внимание весь справочник товаров Битрикса с опциями харнится в массиве $products
 * в виде $products[$section_id][$product_id]
 */


$comment = "\nBITRIX: ";
$errors = [];


/** переписал под группы товаров смотри ниже лапша пицца вода модификаторы
 * ВАЖНО в БИТРКИСЕ смотри XHR запросы - там все четко указано погруппам при добавлении в корзину
 * ВАЖНО в БИТРКИСЕ смотри XHR запросы - там все четко указано погруппам при добавлении в корзину
 * ВАЖНО в БИТРКИСЕ смотри XHR запросы - там все четко указано погруппам при добавлении в корзину
 * ВАЖНО в БИТРКИСЕ смотри XHR запросы - там все четко указано погруппам при добавлении в корзину
 * // 1. Bitrix - к сожалению ID фодификатора отсуствет - только слово ключ (менять имя модификатора в Bitrix не желательно)
 * // смотри view-source:http://pandabanda.loc/novikov/
 * // print_r($this->assosiate_array_modidicators_from_BITRIX);
 * // print_r($this->assosiate_array_product_modidicators_from_poster_API);
 * // 2. По адресу http://pandabanda.loc/novikov/ есть форма соотвествия одного товара BITRIX - соотвествующему товару из ПОСТЕРА
 * // для пиццы и для лапши есть группы модификаторов, которые привязаны в ручю в массиве ниже
 * // по ключевому слову из БИТРИКСА -= связь =-  ID poster модификаторы
 * $dish_modification_id_bitrix2poster_pizza_and_lokshina = [
 * 'Удон (пшеничная)' => 103,
 * 'Соба (гречневая)' => 104,
 * 'Яичная' => 105,
 *
 * 'Обычный' => 70,
 * 'Сырный' => 71,
 * 'Хот-Дог' => 72
 * ];
 **/

// опсиание https://dev.joinposter.com/docs/v3/web/incomingOrders/createIncomingOrder
//$additional_products_flag_comment = 0;
$products_poster = [];
// ПЕРЕБИРАЕМ КОРЗИНУ БИТРИКСА ФОРМИРУЕМ ЗАПРОС В ПОСТЕР
// ПЕРЕБИРАЕМ КОРЗИНУ БИТРИКСА ФОРМИРУЕМ ЗАПРОС В ПОСТЕР
// ПЕРЕБИРАЕМ КОРЗИНУ БИТРИКСА ФОРМИРУЕМ ЗАПРОС В ПОСТЕР
foreach ($basket['BASKET_CONTENT'] as $key => $item_bitrix) {
    // обнуляем ткущий продукт
    $product_poster = [];

    $item_bitrix['PRODUCT_ID'] = str_replace('additional_', '', $item_bitrix['PRODUCT_ID']);
    //print_r($item_bitrix);
    //

    /** сравниваем три таблицы связей БИТРИКС К ПОСТЕРУ от простого к сложному для фомирования зпросов с модификатором **/
    //000 GIFTS идут в битрсе обосооленными товарами - их по схеме 002 один товар без фодификатора ...

    //001 ИЗ БД чере форму http://pandabanda.loc/novikov/ ОДИН ТОВАР К ОДНОМУ ТОВАРУ
    //001.2 частный случай - ИЗ конфиг таблицы один товар группы тоаров с модификатором к товару с модификаторами $bitrix_profuct_group_list_modificator_2_poster_modificator

    //002 ИЗ конфиг таблицы один товар без модификаторов к товару с модификаторами $bitrix_idproduct_nomodificator_2_poster_idproduct_plus_modificator


    // палочки соус и пр
    // палочки соус и пр связал с товарами в постере
    //    if (isset($item_bitrix['TYPE']) and $item_bitrix['TYPE'] === 'additional' and $additional_products_flag_comment == 0) {
    //        $comment .= "В заказе есть какието бесплатные дополнени типа палочек и соуса.\n";
    //        $additional_products_flag_comment = 1;
    //        continue;
    //    }


    // ПОДАРОК
    if ($item_bitrix['PRODUCT_ID'] == 'gift') {
        if (
        sizeof($bitrix_idproduct_nomodificator_2_poster_idproduct_plus_modificator[$item_bitrix['GIFT_ID']])
        ) {


            $_bitrix2poster = $bitrix_idproduct_nomodificator_2_poster_idproduct_plus_modificator[$item_bitrix['GIFT_ID']];
            //       'poster_product_id' => 353,
            //        'modificator_id' => 101
            $product_poster['product_id'] = $_bitrix2poster['poster_product_id'];
            $product_poster['modification'] = "["
                . json_encode(
                    [
                        'm' => $_bitrix2poster['modificator_id'],
                        'a' => 1
                    ]
                )
                . "]";

            // для любыъ типов товаров и подарков
            $product_poster['count'] = $item_bitrix['AMOUNT'];
            // выводя\щий массив
            $products_poster[] = $product_poster;


        } else {
            $errors[] = [
                'message' => "для подарка в битриксе нет соответсвия в постере!",
                'bitrix' => $item_bitrix
            ];
        }
        //        $comment .= "В подарок ";
        //        $comment .= $item_bitrix['AMOUNT'] . "шт, ";
        //        $comment .= $gift[$item_bitrix['GIFT_ID']] . ".\n";


    }


    //001 ИЗ БД чере форму http://pandabanda.loc/novikov/ ОДИН ТОВАР К ОДНОМУ ТОВАРУ
    //001.2 частный случай - ИЗ конфиг таблицы один товар группы тоаров с модификатором к товару с модификаторами $bitrix_profuct_group_list_modificator_2_poster_modificator
    // наче пропускаем
    else if (
        is_numeric($poster->assosiate_array_bitrix_to_poster[$item_bitrix['PRODUCT_ID']])
        and
        $poster->assosiate_array_bitrix_to_poster[$item_bitrix['PRODUCT_ID']] > 0
    ) {
        // ID постер определилил
        $product_poster['product_id'] = $poster->assosiate_array_bitrix_to_poster[$item_bitrix['PRODUCT_ID']];

        // пытаемся понять есть ли иодификатор - потом какой группе битрка товар и его модиикатор принадлежат
        if (isset($item_bitrix['OPTIONS']['OPTION_1'])) {
            //$products  - это глобальная перемнная битркс на всякий случай
            // 0 1 2 номерок моификатора товара в битриксе
            $bitrix_id_modificator = $item_bitrix['OPTIONS']['OPTION_1'];
            if (
                is_numeric($bitrix_profuct_group_list_modificator_2_poster_modificator[$item_bitrix['SECTION_ID']][$bitrix_id_modificator])
                and
                $bitrix_profuct_group_list_modificator_2_poster_modificator[$item_bitrix['SECTION_ID']][$bitrix_id_modificator] > 0
            ) {
                $_poster_modificator = $bitrix_profuct_group_list_modificator_2_poster_modificator[$item_bitrix['SECTION_ID']][$bitrix_id_modificator];
                // без внешних квадратных скобок не работает
                $product_poster['modification'] = "["
                    . json_encode(
                        [
                            'm' => $_poster_modificator,
                            'a' => 1
                        ]
                    )
                    . "]";
            } else {
                $errors[] = [
                    'message' => "одного товара в битркисе есть связь с потсером товаром, но их модификаторы не могу связать",
                    'bitrix' => $item_bitrix
                ];
            }
        }

        // для любыъ типов товаров и подарков
        $product_poster['count'] = $item_bitrix['AMOUNT'];
        // выводя\щий массив
        $products_poster[] = $product_poster;
    }
    //
    //002 ИЗ конфиг таблицы один товар без модификаторов к товару с модификаторами $bitrix_idproduct_nomodificator_2_poster_idproduct_plus_modificator
    else if (
    sizeof($bitrix_idproduct_nomodificator_2_poster_idproduct_plus_modificator[$item_bitrix['PRODUCT_ID']])

    ) {

        $_bitrix2poster = $bitrix_idproduct_nomodificator_2_poster_idproduct_plus_modificator[$item_bitrix['PRODUCT_ID']];
        //       'poster_product_id' => 353,
        //        'modificator_id' => 101
        $product_poster['product_id'] = $_bitrix2poster['poster_product_id'];
        $product_poster['modification'] = "["
            . json_encode(
                [
                    'm' => $_bitrix2poster['modificator_id'],
                    'a' => 1
                ]
            )
            . "]";
        // для любыъ типов товаров и подарков
        $product_poster['count'] = $item_bitrix['AMOUNT'];
        // выводя\щий массив
        $products_poster[] = $product_poster;
    } else {
        $errors[] = [
            'message' => "ЧТО ЭТО ЗА ТОВАР? не могу приязать к постеру",
            'bitrix' => $item_bitrix
        ];
    }


}

// теперь отдельым ключем передаем
//$comment .= "Доставка " . $DELIVERY_TYPE[$_POST['ORDER']['DELIVERY_TYPE']] . ".\n";

$payment = "Оплата " . $PAYMENT_TYPE[$_POST['ORDER']['PAYMENT_TYPE']] . " ";
$payment .= trim($_POST['ORDER']['ODD_MONEY'] ? "купюра " . $_POST['ORDER']['ODD_MONEY'] : "") . "\n";
$comment .= $payment;

// когда курьеру выехать
if ($_POST['ORDER']['DELIVERY_TYPE'] == 1) {
    $comment .= "Когда доставить: ";
    if ($_POST['ORDER']['DELIVERY_TIME_TYPE'] == 0)
        $comment .= "сейчас\n";
    else {
        $comment .= $_POST['ORDER']['DELIVERY_DATE'] . " " . $_POST['ORDER']['HOUR'] . ":" . $_POST['ORDER']['MINUTE'] . ".\n";
    }
}
$adress_poster = "";
$adress_poster .= trim($_POST['ORDER']['DISTRICT_ID'] ? $DISTRICT_ID[$_POST['ORDER']['DISTRICT_ID']] : "");
$adress_poster .= trim($_POST['ORDER']['STREET'] ? ", ул." . $_POST['ORDER']['STREET'] : "");
$adress_poster .= trim($_POST['ORDER']['HOUSE'] ? ", д." . $_POST['ORDER']['HOUSE'] : "");
$adress_poster .= trim($_POST['ORDER']['APARTMENT'] ? ", к." . $_POST['ORDER']['APARTMENT'] : "");
// адрестеперь не в комменте а отдельным поле в постер передается
//$comment .= $adress_poster;

// будем ниже слать на https://joinposter.com/api/incomingOrders.createIncomingOrder
// документция https://dev.joinposter.com/docs/v3/web/incomingOrders/createIncomingOrder
$poster_request = [
    'spot_id' => 1,
    'phone' => $_POST['ORDER']['USER_PHONE'],
    'service_mode' => $DELIVERY_TYPE_BITRIX_POSTER[$_POST['ORDER']['DELIVERY_TYPE']],
    'products' => $products_poster,
    'first_name' => $_POST['ORDER']['USER_NAME'] . ' ' . $_POST['ORDER']['USER_PHONE'],
    'comment' => "" . $_POST['ORDER']['COMMENT'] . "" . $comment,
    'address' => $adress_poster,
    // НЕ ПЕРЕДАЕТС/ ЭТИ ПОЛЯ НЕ ТРОГАТЬ!
    //'payment' => $payment, Если предварительной оплаты не было в онлайн-заказе - массив payment в запросе не передавать.
];


if ($_POST and sizeof($poster_request['products'])) {
    // логируем
    //// тестовый запрос для анализа
    if (sizeof($errors)) {
        //жалуемся
        sendManagerChatBot("НЕ СМОГ ПРИВЯЗАТЬ ТОВАРЫ БИТРИКСА К ПОСТЕРУ в этом заказе\n\n" . print_r(['bitrix' => $_POST['ORDER'], 'error' => $errors], true), '440046277');
        //sendManagerChatBot("НЕ СМОГ ПРИВЯЗАТЬ ТОВАРЫ БИТРИКСА К ПОСТЕРУ в этом заказе\n\n" . print_r(['bitrix' => $_POST['ORDER'], 'error' => $errors], true), '663098139');
    }
    sendTest($basket, $errors, $poster_request, sendAPI($poster_request));
}

function sendAPI($poster_request)
{

    $ch = curl_init();
    $myurl = 'https://joinposter.com/api/incomingOrders.createIncomingOrder?token=' . API_KEY;
    //$myurl = "http://testftp.protection.com.ua/pandabanda/test.php";
    curl_setopt($ch, CURLOPT_URL, $myurl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    //curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($poster_request));
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($poster_request));


    $server_output = curl_exec($ch);
    curl_close($ch);


    //Panda::logs('poster_response.txt', $server_output);
    return $server_output;
}

// ТЕСТОВЫЙ ЗАПРОС ДЛЯ АНАЛИЗА
// ТЕСТОВЫЙ ЗАПРОС ДЛЯ АНАЛИЗА
function sendTest($basket, $errors, $poster_request, $poster_responce)
{

    $ch = curl_init();
    $myurl = MY_LOG_SERVER;
    curl_setopt($ch, CURLOPT_URL, $myurl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
        http_build_query(
            [
                "post" => $_POST,
                "basket" => $basket,
                "errors" => $errors,
                "poster_request" => $poster_request,
                "poster_responce" => $poster_responce,

            ]
        )
    );

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    curl_close($ch);
    //Panda::logs('test_response.txt', $server_output);
}


function sendManagerChatBot($msg, $chatId)

{
    $url = 'https://api.telegram.org/bot'.API_BOT.'/sendMessage';
    $data = [
        'chat_id' => $chatId,
        'text' => $msg,
        'parse_mode' => 'html',
//                'parse_mode' => 'markdown',
        'disable_web_page_preview' => 1,
    ];

    $options = ['http' =>
        [
            'method' => 'POST',
            'header' => "Content-Type:application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($data),
        ],
    ];
    $context = stream_context_create($options);
    $responce = file_get_contents($url, false, $context);
    //print_r($responce);

}



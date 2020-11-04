<?php
//
// 2020-11-04 интеграция сделано
// - подвязал бесплатные товары палочки и соус из бесплатных товаров битрикс -> постер
// - перадал адрес доставки отдельным полем постера address а не в комментариях
// - тип оплаты передать нет возможности в poster - саппорт овтетил что ТОЛЬКО ЧЕРЕЗ КОМЕНТЫ
// - создать нового клиента в новой группе - сапппорт отыетил что группа есть нахывается НОВЫЕ КЛИЕНТЫ
// - если ошибка синхронизации при  передаче заказа БТРИКС то присылать в чат ПОСТЕР

// ЭТОТ ВОПРОС ОТПРАВИЛ В САППОРТ
// Постер отправляем чек на кухню - битрикс ставит статус - Кухня.
// Постер отправляет заказ курьеру - битрикс ставит статус Курьер.
// Постер закрывает чек - битрикс ставит статус Доставленно

// ЭТОТ ВОПРОС ОТПРАВИЛ В САППОРТ
// Синхрнизация бонусов личного счета клиента  Битрикс - Постер
// В Битриксе - исходные данные
// ПРИ ЗАКАЗЕ клиента

// НА ПЕРСПЕКТИВУ ждем
// Когда выйдет апи курьеров на постере
// Передавать смс клиенту что курьер доставил


// ПО САЙТУ ОТВЕЧУ ПОЗЖЕ:
// Отменить обязательное поле в БИТРКСЕ -кварира офис
// Сделать Поле подьезд не обязательное
// Для всех зарегитрированных - Опция НЕ ПЕРЕЗВАНИВАТЬ мне
// Глянуть скорость сайта, что можно сделать


/////////////////////////////////////////////////////////////////////
//by Novikov 2020
// тут \bitrix\modules\bd.deliverysushi\controller\order.php
// перед         if ($_POST['ORDER']['PAY_IDENT'] == 'online') {
/** require($_SERVER["DOCUMENT_ROOT"] . "/novikov/order_include.php"); **/
/////////////////////////////////////////////////////////////////////

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

// НАПИТКИ в проекте 4 группы
// для каждой группы в ПОСТЕРЕ есть соотвествующий товар + модификаторы
// но в битриксе 3 группы напитков имеют отдедбные товары БЕЗ модификаторов
// и 4я группа в Битрксе имеет 1 товар + модификаторы см выше  МОДИФИКАТОРЫ многие ко многим
/** один товар к одному модификатору */
$bitrix_idproduct_nomodificator_2_poster_idproduct_plus_modificator = [


    // РИЧ СОК
    // РИЧ СОК
    //Rich Апельсиновый #
    322 => [
        'poster_product_id' => 355,
        'modificator_id' => 90
    ],

    //Rich Персиковый #
    323 => [
        'poster_product_id' => 355,
        'modificator_id' => 91
    ],

    //Rich Екзотик #
    324 => [
        'poster_product_id' => 355,
        'modificator_id' => 92
    ],

    //Rich Яблочный #
    325 => [
        'poster_product_id' => 355,
        'modificator_id' => 106
    ],

    //Rich Вишневый
    326 => [
        'poster_product_id' => 355,
        'modificator_id' => 93
    ],

    //Rich Томатный #
    400 => [
        'poster_product_id' => 355,
        'modificator_id' => 94
    ],

    // ПОДАРОЧНАЯ ВОДА
    //Подарочная вода ФАНТА # (не относится к стандартным продуктам в битриксе а GIFT)
    //{ID: "gift", INDEX: "gift", GIFT_ID: "311", PRODUCT_ID: "gift", NAME: "Fanta 1л.", SORT: 200,…}
    311 => [
        'poster_product_id' => 353,
        'modificator_id' => 101
    ],

    //Подарочная вода КОЛА # (не относится к стандартным продуктам в битриксе а GIFT)
    312 => [
        'poster_product_id' => 353,
        'modificator_id' => 100
    ],

    //Подарочная вода СПРАЙТ # (не относится к стандартным продуктам в битриксе а GIFT)
    313 => [
        'poster_product_id' => 353,
        'modificator_id' => 102
    ],


    //ПРОСТО ВОДА
    //ПРОСТО ВОДА
    //ПРОСТО ВОДА
    //Coca-cola #317
    317 => [
        'poster_product_id' => 354,
        'modificator_id' => 97
    ],

    //ПРОСТО ВОДА
    //Fanta #318
    318 => [
        'poster_product_id' => 354,
        'modificator_id' => 98
    ],

    //ПРОСТО ВОДА
    //Sprite #319
    319 => [
        'poster_product_id' => 354,
        'modificator_id' => 99
    ],

    //ПРОСТО ВОДА
    //Bonaqua сильногазированная #320
    320 => [
        'poster_product_id' => 354,
        'modificator_id' => 95
    ],

    //ПРОСТО ВОДА
    //Bonaqua негазированная #321
    321 => [
        'poster_product_id' => 354,
        'modificator_id' => 96
    ],

];


// МОДИФИКАТОРЫ многие ко многим ЗДЕСЬ ВАЖНО чтбы в базе нашего скрипьта была ПАРА пицца пицца , лапша апша, воа33 вода33
// МОДИФИКАТОРЫ многие ко многим ЗДЕСЬ ВАЖНО чтбы в базе нашего скрипьта была ПАРА пицца пицца , лапша апша, воа33 вода33
// лапша пицца вода модификатор бирикса смотри в XHR при добавлени в корзину
$bitrix_profuct_group_list_modificator_2_poster_modificator = [

    //напитки вода 0.33
    //0: {VALUE: "Coca-cola", PRICE: "15", OLD_PRICE: "", WEIGHT: "0.33", PROP: ""}
    //1: {VALUE: "Fanta", PRICE: "15", OLD_PRICE: "", WEIGHT: "0.33", PROP: ""}
    //2: {VALUE: "Sprite", PRICE: "15", OLD_PRICE: "", WEIGHT: "0.33", PROP: ""}
    //SECTION_ID - здесь кстати один товар Битриксе ВОДА 033, но уже сделал универсльно типа по группе его поймаем как пиццу и рапшу
    14 => [
        0 => 87,
        1 => 88,
        2 => 89,
    ],

    //пицца
    //0: {VALUE: "Обычный", PRICE: "155", OLD_PRICE: "", WEIGHT: "30", PROP: "Начинка бортика"}
    //1: {VALUE: "Сырный", PRICE: "190", OLD_PRICE: "", WEIGHT: "30", PROP: "Начинка бортика"}
    //2: {VALUE: "Хот-Дог", PRICE: "200", OLD_PRICE: "", WEIGHT: "30", PROP: "Начинка бортика"}
    //SECTION_ID
    28 => [
        0 => 70,
        1 => 71,
        2 => 72,
    ],

    //лапша
    //0: {VALUE: "Удон (пшеничная)", PRICE: "139", OLD_PRICE: "", WEIGHT: "340", PROP: "Лапша"}
    //1: {VALUE: "Соба (гречневая)", PRICE: "139", OLD_PRICE: "", WEIGHT: "340", PROP: "Лапша"}
    //2: {VALUE: "Яичная", PRICE: "139", OLD_PRICE: "", WEIGHT: "340", PROP: "Лапша"}
    //SECTION_ID
    27 => [
        0 => 103,
        1 => 104,
        2 => 105,
    ],
];


$DELIVERY_TYPE_BITRIX_POSTER = [
    1 => 3, // доставка
    2 => 2, // на вынос
];

$PAYMENT_TYPE = [
    203 => "наличные", // не знаю чт это за тип оплаты не указывался
    202 => "наличные",
    204 => "картой курьеру",
    206 => "онлайн",
];

$DISTRICT_ID = [
    235 => "Житомир",
    236 => "Ивановка",
    237 => "Пряжево",
    238 => "Глибочиця",
    239 => "Гуйва",
    240 => "Довжик",
    241 => "Заричаны",
    242 => "Новогуйвинск",
    243 => "Озерное",
    244 => "Олиевка",
    245 => "Сонячное",
    353 => "Тетеревка",
    376 => "Станишовка",
];


class Panda
{

    // на входе из битрикса
    public $assosiate_array_modidicators_from_BITRIX;

    // результат
    public $assosiate_array_bitrix_to_poster;
    public $assosiate_array_poster_to_bitrix;

    // из Постера API
    private $assosiate_array_products_from_poster_API;

    // из Постера API
    private $assosiate_array_product_modidicators_from_poster_API;

    private $DB;

    function __construct($DB)
    {
        $this->DB = $DB;

        // обработчик POST запросов
        $this->post_controller();


        // апи постера в ассоциативнй массив
        $this->get_assosiate_array_bitrix_to_poster();
        $this->get_assosiate_array_products_from_poster_API_and_list_modificators();
        $this->get_assosiate_array_list_modificators_from_BITRIX();


    }

    function print_product_table()
    {
        global $bitrix_idproduct_nomodificator_2_poster_idproduct_plus_modificator;

        // Битрикс переменна снаружи
        $message_Need_ID_Link = "<h2>Важно чтобы для каждого товара в Bitrix был подвязан ОДИН товар из POSTER</h2>";

        // получаем из базы список всех активных товаров БИТРИКС
        $strSql = "SELECT * FROM `b_iblock_element` WHERE `IBLOCK_ID` in (3, 4) AND `ACTIVE`= 'Y'";
        $res = $this->DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

        // все подвязаны или нет
        $toOutput = $message_Need_ID_Link;
        $toOutput .= '
Внимание здесь отображены только АКТИВНЫЕ товары Bitrix<br>
Внимание часто API постера выдает половину товаров и тогда здесь создается впчателение что таблица наполовину не заполнена. Просто обнови страницу<br>
В выпадащем списке скрыты позиции -  уже привязанные ПОСТЕР к БИТРИКСУ т.к. их повторно нельзя привязать (конечно если предварительно гдето их не освободить)!
<br>
<br>
  <form method="POST">';

        if (($_SERVER['HTTP_HOST'] == 'pandabanda.loc')) {
            $toOutput .= "ЛОКАЛЬНАЯ КОПИЯ";
        } else {
            $toOutput .= '<input style="" type="submit" value="ПЕРЕЗАПИСАТЬ">';
        }

        $toOutput .= '<table>';


        if (sizeof($this->assosiate_array_products_from_poster_API) < 2) die('не вижу API постера');
        $showHeader = true;
        while ($row = $res->Fetch()) {
            $toOutput .= '<tr>';
            if ($showHeader) {
                $toOutput .= '<td  style="text-align: right;">BITRIX (no GIFTS)</td>';
                $toOutput .= '<td>POSTER</td>';
                $toOutput .= '</tr><tr>';
                $showHeader = false;
            }


            // если товар не привязан жестко
            if (!isset($bitrix_idproduct_nomodificator_2_poster_idproduct_plus_modificator[$row['ID']])) {
                $toOutput .= '<td style="text-align: right;"><strong>' . $row['NAME'] . ' #' . $row['ID'] . '</strong><br><small>#' . $row['NAME'] . ' ' . $row['PREVIEW_TEXT'] . ' / группа №' . $row['IBLOCK_ID'] . '</small></td>';
                $toOutput .= '<td>' . $this->get_API_Poster_products_dropdown($row['ID']) . '</td>';
                $toOutput .= '</tr>';
            }
        }
        $toOutput .= '</table>';

        if (($_SERVER['HTTP_HOST'] == 'pandabanda.loc')) {
            $toOutput .= "ЛОКАЛЬНАЯ КОПИЯ";
        } else {
            $toOutput .= '<input style="" type="submit" value="ПЕРЕЗАПИСАТЬ">';
        }

        $toOutput .= "</form>";

        echo $toOutput;
        echo "<hr><h1>Жестко привязанные товары типа ВОДЫ и RICH.  1товар без модификатора > 1товар + модификатор </h1><pre>";
        print_r($bitrix_idproduct_nomodificator_2_poster_idproduct_plus_modificator);
        echo "</pre>";

    }


    function print_product_modification_table()
    {


        echo "<pre>";
        echo "<hr><h1>Модификаторы Bitrix</h1>";
        print_r($this->assosiate_array_modidicators_from_BITRIX);

        echo "<hr><h1>Все Модификаторы постера</h1>";
        print_r($this->assosiate_array_product_modidicators_from_poster_API);
        echo "</pre>";


    }

    function get_assosiate_array_list_modificators_from_BITRIX()
    {
        // получаем из базы список вариаций Свойста 1 (ID11) второе Свойстов 2 (ID12) пока пустое
        // и берем только не пустые свойства VALUE like '%VALUE%'
        $strSql = "SELECT * FROM `b_iblock_element_property` WHERE `IBLOCK_PROPERTY_ID` = 11 AND VALUE like '%VALUE%'";
        $res = $this->DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        $this->assosiate_array_modidicators_from_BITRIX = [];
        while ($row = $res->Fetch()) {


            $product_id = $row['IBLOCK_ELEMENT_ID'];

            //перебираем свойства товара в поиска хключей доп свойств товара
            // к сожалению ID фодификатора отсуствет только слово ключ
            foreach (unserialize($row['VALUE']) as $key => $item) {
                if ($item['PROP']) {
                    $this->assosiate_array_modidicators_from_BITRIX[$product_id][$key]['modification'] = $item['PROP'];
                    $this->assosiate_array_modidicators_from_BITRIX[$product_id][$key]['value'] = trim($item['VALUE']);
                }
            }
        }
    }


    function get_assosiate_array_bitrix_to_poster()
    {
        $this->assosiate_array_bitrix_to_poster = [];
        $strSql = "SELECT * FROM `bitrix2poster_product_integra`";
        $res = $this->DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        while ($row = $res->Fetch()) {
            $this->assosiate_array_bitrix_to_poster[$row['id_product_bitrix']] = $row['id_product_poster'];
            $this->assosiate_array_poster_to_bitrix[$row['id_product_poster']] = $row['id_product_bitrix'];
        }
    }


    function get_API_Poster_products_dropdown($ID_Bitrix)
    {
        // дропдаун отображаем только если в базе есть соотвествующая пара
        // если пара есть но ID постера товара привязанный,отсуствуеи в постер - такой дропдайн показываем как отвязанный "выберите новое значение"

        //
        $otput = '<select name="poster[' . $ID_Bitrix . ']">';
        $otput .= '<option value="0" selected="">-= здесь выбрать продукт из Poster =-</option>';

        $poster_id_selected = '';
        foreach ($this->assosiate_array_products_from_poster_API as $ID_POSTER_API => $item_poster_API) {

            // в базе есть связь и эта связь активна (есть в потере соответсвующий товар)
            $ID_POSTER_mysql = $this->assosiate_array_bitrix_to_poster[$ID_Bitrix];
            $selected = "";
            $related = "";
            /** не вздумай менять на ==== работать не будет типы разные */
            if ($ID_POSTER_mysql == $ID_POSTER_API &&
                // такой ID cуществцет в постере JSON API
                $this->assosiate_array_products_from_poster_API[$ID_POSTER_mysql]) {
                $selected = "selected";
                $poster_id_selected = $ID_POSTER_mysql;
                //

            } else if ($this->assosiate_array_poster_to_bitrix[$ID_POSTER_API]) {
                //$related = "= connected = ";
                // попускаем уже привязанный продукт
                // попускаем уже привязанный продукт
                continue;
            }
            $otput .= '  <option value="' . $ID_POSTER_API . '" ' . $selected . '>' . $item_poster_API->product_name . " #{$ID_POSTER_API}</option>\n";


        }
        $otput .= '</select> ';

        // для удобства поиска товара по Idyf странице - текущий товар здесь выведем
        $otput .= '<br>' . $poster_id_selected;


        return $otput;
    }


    function post_controller()
    {
        if (!is_array($_POST['poster'])) return;

        // защита от дурака
        // защита от дурака
        // защита от дурака
        $liste = [];
        foreach ($_POST['poster'] as $id_bitrix => $id_poster) {
            if ($id_poster > 0)
                if (in_array($id_poster, $liste, true)) {
                    echo "<h2 style='color: red'>НЕ МОГУ СОХРАНИТЬ, вы выбрали товар в правой колонке POSTER дважды !!!!!!!! " . $this->assosiate_array_products_from_poster_API[$id_poster] . " / нажми стрелку назад и откорректируй</h2>";
                    return;
                }
            $liste[] = $id_poster;
        }


        // чистим все связи
        // чистим все связи
        // чистим все связи
        $strSql = "TRUNCATE TABLE `bitrix2poster_product_integra`";
        $res = $this->DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

        foreach ($_POST['poster'] as $id_bitrix => $id_poster) {
            if ($id_poster > 0) {
                $strSql = "INSERT INTO `bitrix2poster_product_integra` (`id`, `id_product_bitrix`, `id_product_poster`, `name_product_poster`, `date_updated`) VALUES (NULL, '" . $id_bitrix . "', '" . $id_poster . "', '', NOW());";
                //echo "<br>";
                echo $res = $this->DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
                if (trim($res)) echo "<br>";
            }
        }


    }

    function get_assosiate_array_products_from_poster_API_and_list_modificators()
    {
        // постер возвращает насыпом без индексов продукты так не делаем
        // return json_decode($json)->response;
        $this->assosiate_array_products_from_poster_API = [];
        $this->assosiate_array_product_modidicators_from_poster_API = [];
        $json = file_get_contents("https://joinposter.com/api/menu.getProducts?token=" . API_KEY);
        // локальное хранилище для скрорсти
        // $json = file_get_contents("poster%20%D0%BF%D1%80%D0%B8%D0%BC%D0%B5%D1%80%20%D1%82%D0%BE%D0%B2%D0%B0%D1%80%D0%BE%D0%B2%20response.json");
        // пробегаем все товары формирум ассоативный массив по ID postr продукта
        foreach (json_decode($json)->response as $item) {
            // товары
            $this->assosiate_array_products_from_poster_API[$item->product_id] = $item;

            if (isset($item->group_modifications[0]->modifications[0])) {
                // база модификаторов ассоциативная по группе

                $this->assosiate_array_product_modidicators_from_poster_API[]
                    = [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'dish_modification_group_id' => $item->group_modifications[0]->dish_modification_group_id,
                    'modifications_name' => $item->group_modifications[0]->name,
                    'modifications' => $item->group_modifications[0]->modifications
                ];

            }
            //
        }


    }

    static function logs($filelog_name, $message)
    {

        $fd = @fopen($_SERVER["DOCUMENT_ROOT"] . "/novikov/logs/" . $filelog_name, "a");
        @fwrite($fd, date("Ymd-G:i:s") . " -- " . print_r($message, true) . "\n");
        @fclose($fd);
    }








}

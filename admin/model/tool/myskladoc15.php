<?php

class ModelToolmyskladoc15 extends Model {

    private $CATEGORIES = array();
    private $PROPERTIES = array();


    /**
     * Генерирует xml с заказами
     *
     * @param   int статус выгружаемых заказов
     * @param   int новый статус заказов
     * @param   bool    уведомлять пользователя
     * @return  string
     */
    public function queryOrders($params) {

        $this->load->model('sale/order');

        if ($params['exchange_status'] != 0) {
            $query = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` WHERE `order_status_id` = " . $params['exchange_status'] . "");
        } else {
            $query = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` WHERE `date_added` >= '" . $params['from_date'] . "'");
        }

        $document = array();
        $document_counter = 0;

        if ($query->num_rows) {

            foreach ($query->rows as $orders_data) {

                $order = $this->model_sale_order->getOrder($orders_data['order_id']);

                $date = date('Y-m-d', strtotime($order['date_added']));
                $time = date('H:i:s', strtotime($order['date_added']));

                $document['Документ' . $document_counter] = array(
                //устанавливаем внешний код для заказов домен#номер ордера, что бы не перезаписывало в моем складе если будет больше двух магазинов
                    'Ид'          => $_SERVER['HTTP_HOST']."#".$order['order_id']
                ,'Номер'       => $order['order_id']."#".$_SERVER['HTTP_HOST']
                ,'Дата'        => $date
                ,'Время'       => $time
                ,'Валюта'      => $params['currency']
                ,'Курс'        => 1
                ,'ХозОперация' => 'Заказ товара'
                ,'Роль'        => 'Продавец'
                ,'Сумма'       => !empty($order['total']) ? $order['total'] : "нету данных"
                ,'Комментарий' => !empty($order['comment']) ? $order['comment'] : "нету данных"
                );

                $document['Документ' . $document_counter]['Контрагенты']['Контрагент'] = array(
                    'Ид'                 => $order['customer_id'] . '#' . $order['email']
                ,'Наименование'         => $order['payment_lastname'] . ' ' . $order['payment_firstname']
                ,'Роль'               => 'Покупатель'
                ,'ПолноеНаименование'   => $order['payment_lastname'] . ' ' . $order['payment_firstname']
                ,'Фамилия'            => !empty($order['payment_lastname']) ? $order['payment_lastname'] : "нету данных"
                ,'Имя'                    =>!empty($order['payment_firstname']) ? $order['payment_firstname'] : "нету данных"
                ,'Адрес' => array(
                        'Представление' => $order['shipping_address_1'].', '.$order['shipping_city'].', '.$order['shipping_postcode'].', '.$order['shipping_country']
                    )
                ,'Контакты' => array(
                        'Контакт1' => array(
                            'Тип' => 'ТелефонРабочий'
                        ,'Значение' => $order['telephone']
                        )
                    ,'Контакт2' => array(
                            'Тип' => 'Почта'
                        ,'Значение' => $order['email']
                        )
                    )
                );

                // Товары
                $products = $this->model_sale_order->getOrderProducts($orders_data['order_id']);

                $product_counter = 0;
                foreach ($products as $product) {

                    $document['Документ' . $document_counter]['Товары']['Товар' . $product_counter] = array(
                        'Ид'             => $this->get_uuid($product['product_id'])
                    ,'Наименование'   => $product['name']
                    ,'ЦенаЗаЕдиницу'  => $product['price']
                    ,'Количество'     => $product['quantity']
                    ,'Сумма'          => $product['total']
                    );

                    if ($this->config->get('myskladoc15_relatedoptions')) {
                        $this->load->model('module/related_options');
                        if ($this->model_module_related_options->get_product_related_options_use($product['product_id'])) {
                            $order_options = $this->model_sale_order->getOrderOptions($orders_data['order_id'], $product['order_product_id']);
                            $options = array();
                            foreach ($order_options as $order_option) {
                                $options[$order_option['product_option_id']] = $order_option['product_option_value_id'];
                            }
                            if (count($options) > 0) {
                                $ro = $this->model_module_related_options->get_related_options_set_by_poids($product['product_id'], $options);
                                if ($ro != FALSE) {
                                    $char_id = $this->model_module_related_options->get_char_id($ro['relatedoptions_id']);
                                    if ($char_id != FALSE) {
                                        $document['Документ' . $document_counter]['Товары']['Товар' . $product_counter]['Ид'] .= "#".$char_id;
                                    }
                                }
                            }

                        }

                    }

                    $product_counter++;
                }

                $document_counter++;
            }
        }

        $root = '<?xml version="1.0" encoding="utf-8"?><КоммерческаяИнформация ВерсияСхемы="2.04" ДатаФормирования="' . date('Y-m-d', time()) . '" />';
        $xml = $this->array_to_xml($document, new SimpleXMLElement($root));

        return $xml->asXML();
    }

    public function queryOrdersStatus($params){

        $this->load->model('sale/order');

        if ($params['exchange_status'] != 0) {
            $query = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` WHERE `order_status_id` = " . $params['exchange_status'] . "");
        } else {
            $query = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` WHERE `date_added` >= '" . $params['from_date'] . "'");
        }

        if ($query->num_rows) {
            foreach ($query->rows as $orders_data) {
                $this->model_sale_order->addOrderHistory($orders_data['order_id'], array(
                    'order_status_id' => $params['new_status'],
                    'comment'         => '',
                    'notify'          => $params['notify']
                ));
            }
        }

        return true;
    }


    function array_to_xml($data, &$xml) {

        foreach($data as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subnode = $xml->addChild(preg_replace('/\d/', '', $key));
                    $this->array_to_xml($value, $subnode);
                }
            }
            else {
                $xml->addChild($key, $value);
            }
        }

        return $xml;
    }

    function format($var){
        return preg_replace_callback(
            '/\\\u([0-9a-fA-F]{4})/',
            create_function('$match', 'return mb_convert_encoding("&#" . intval($match[1], 16) . ";", "UTF-8", "HTML-ENTITIES");'),
            json_encode($var)
        );
    }


    /**
     * Транслиетрирует RUS->ENG
     * @param string $aString
     * @return string type
     */
    private function transString($aString) {
        $rus = array(" ", "/", "*", "-", "+", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "+", "[", "]", "{", "}", "~", ";", ":", "'", "\"", "<", ">", ",", ".", "?", "А", "Б", "В", "Г", "Д", "Е", "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ъ", "Ы", "Ь", "Э", "а", "б", "в", "г", "д", "е", "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ъ", "ы", "ь", "э", "ё",  "ж",  "ц",  "ч",  "ш",  "щ",   "ю",  "я",  "Ё",  "Ж",  "Ц",  "Ч",  "Ш",  "Щ",   "Ю",  "Я");
        $lat = array("-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-",  "-", "-", "-", "-", "-", "-", "a", "b", "v", "g", "d", "e", "z", "i", "y", "k", "l", "m", "n", "o", "p", "r", "s", "t", "u", "f", "h", "",  "i", "",  "e", "a", "b", "v", "g", "d", "e", "z", "i", "j", "k", "l", "m", "n", "o", "p", "r", "s", "t", "u", "f", "h", "",  "i", "",  "e", "yo", "zh", "ts", "ch", "sh", "sch", "yu", "ya", "yo", "zh", "ts", "ch", "sh", "sch", "yu", "ya");

        $string = str_replace($rus, $lat, $aString);

        while (mb_strpos($string, '--')) {
            $string = str_replace('--', '-', $string);
        }

        $string = strtolower(trim($string, '-'));

        return $string;
    }


   // заносим в базу uuid  для каждого купленого товара (Может в будушем когданибудь  понадобится для API)
    public function product_uuid($product_id)
    {
        $uuid = $this->uuid();

        if ($product_id){
            $this->db->query('INSERT INTO `uuid` SET product_id = ' . (int)$product_id . ', `uuid_id` = "' . $uuid . '"');

            return $uuid;
        }
    }

    //получаем uuid  код для отправки товара
    public function get_uuid($product_id){
        $query = $this->db->query('SELECT uuid_id FROM `uuid` WHERE product_id = " '.$product_id.' " ');

        if ($query->num_rows) {
            return $query->row['uuid_id'];
        }
        else {
            return $this->product_uuid($product_id);

        }
    }

    //создаем метод по генерации uuid
    public function uuid(){
        $randomString = openssl_random_pseudo_bytes(16);
        $time_low = bin2hex(substr($randomString, 0, 4));
        $time_mid = bin2hex(substr($randomString, 4, 2));
        $time_hi_and_version = bin2hex(substr($randomString, 6, 2));
        $clock_seq_hi_and_reserved = bin2hex(substr($randomString, 8, 2));
        $node = bin2hex(substr($randomString, 10, 6));

        $time_hi_and_version = hexdec($time_hi_and_version);
        $time_hi_and_version = $time_hi_and_version >> 4;
        $time_hi_and_version = $time_hi_and_version | 0x4000;

        $clock_seq_hi_and_reserved = hexdec($clock_seq_hi_and_reserved);
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;

        return sprintf('%08s-%04s-%04x-%04x-%012s', $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node);
    }


    //получаем id  языка
    public function getLanguageId($lang) {
        $query = $this->db->query('SELECT `language_id` FROM `' . DB_PREFIX . 'language` WHERE `code` = "'.$lang.'"');
        return $query->row['language_id'];
    }


    //Выбираем данные для  xls  отчета, товар обязан находиться в категории иначе его не будет в отчете
    public function dataxls($diapason){

        $query = $this->db->query("SELECT " . DB_PREFIX . "product.product_id, " . DB_PREFIX . "product.quantity, " . DB_PREFIX . "product.price, uuid.uuid_id,
                                    " . DB_PREFIX . "product_description.name, " . DB_PREFIX . "product_to_category.category_id  FROM `" . DB_PREFIX . "product`
                                   INNER JOIN `" . DB_PREFIX . "product_description` ON " . DB_PREFIX . "product.product_id = " . DB_PREFIX . "product_description.product_id 
                                   LEFT JOIN `uuid` ON " . DB_PREFIX . "product.product_id = uuid.product_id
                                   INNER JOIN `" . DB_PREFIX . "product_to_category`  ON " . DB_PREFIX . "product.product_id = " . DB_PREFIX . "product_to_category.product_id
                                   GROUP BY " . DB_PREFIX . "product.product_id  LIMIT ".$diapason['ot'].", ".$diapason['kolichestvo']." 
                                    ");

        return $query->rows;

    }

    public function getCat($category_id) {
        $query = $this->db->query("SELECT " . DB_PREFIX . "category.category_id, " . DB_PREFIX . "category.parent_id, " . DB_PREFIX . "category_description.name
                                   FROM `" . DB_PREFIX . "category`
                                      INNER JOIN `" . DB_PREFIX . "category_description` ON
                                        " . DB_PREFIX . "category.category_id = " . DB_PREFIX . "category_description.category_id
                                        WHERE " . DB_PREFIX . "category.category_id =  '".$category_id."'
            
                                    ");
        return $query->rows;
    }

    //выводи все ид продуктов, что есть в базе
    public function getAllProductID(){
        $query = $this->db->query("SELECT product_id  FROM `" . DB_PREFIX . "product` ");

       return $query->rows;

    }



    //c полученого массива заносим данные в БД
    public function getxls($mas_xls,$getAllProductID,$lang)
    {

        $today = date("Y-m-d");
        $index = 0;
        $res = array();

        foreach ($mas_xls as $xls) {
         //   $result = array_search($xls['id'], $getAllProductID);
            $res[$xls['id']] = $xls['id'];
        }
        $no_base = array_diff($res, $getAllProductID);

        var_dump(key($no_base));

       // var_dump($mas_xls);


        //защита от пустых записей
        //  if (!empty($getAllProductID) && isset($mas_xls) && isset($getAllProductID)){
        if (!empty($getAllProductID) && isset($mas_xls) && isset($getAllProductID)) {

            foreach ($mas_xls as $xls) {
                //поиск по массиву если совпало то апдейтем
                $result = array_search($xls['id'], $getAllProductID);

                if (!empty($result) && isset($result)) {
                    $this->db->query("UPDATE " . DB_PREFIX . "product 
                                               INNER JOIN `" . DB_PREFIX . "product_description` ON " . DB_PREFIX . "product.product_id = " . DB_PREFIX . "product_description.product_id
                                               SET name = '" . $xls['name'] . "', quantity = '" . $xls['quantity'] . "', price = '" . $xls['price'] . "'
                                               WHERE " . DB_PREFIX . "product_description.product_id = '" . (int)$result . "'");

                }elseif(isset($no_base) && !empty($no_base)){

                    $this->db->query("INSERT INTO " . DB_PREFIX . "product (`model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`, `quantity`,
                                      `stock_status_id`, `image`, `manufacturer_id`, `shipping`, `price`, `points`, `tax_class_id`, `date_available`, `weight`, `weight_class_id`,
                                       `length`, `width`, `height`, `length_class_id`, `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`) VALUES
                                       ('','','','','','','','', '".$xls['quantity']."',0,'',0,0,'".$xls['price']."',0,0,'".$today."', 0,0,0.0,0,0,0,0,0,0,0,0,
                                       '".$today."','".$today."')");


                        //получаем продукт ид для добавление того же товара(описание товара)
                        $product_id = $this->db->getLastId();

                        $this->db->query("INSERT INTO " . DB_PREFIX . "product_description (`product_id`, `language_id`, `name`, `description`,
                                    `tag`, `meta_description`, `meta_keyword`) VALUES ($product_id,$lang,'".$xls['name']."',' ','',' ','')");


                        $this->db->query("INSERT INTO  " . DB_PREFIX . "product_to_store (`product_id`, `store_id`) VALUES ($product_id,0)");


                }
                $index++;

            }

          }else{
            $in = 0;
            foreach ($mas_xls as $xls) {

                $this->db->query("INSERT INTO " . DB_PREFIX . "product (`model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`, `quantity`,
                                      `stock_status_id`, `image`, `manufacturer_id`, `shipping`, `price`, `points`, `tax_class_id`, `date_available`, `weight`, `weight_class_id`,
                                       `length`, `width`, `height`, `length_class_id`, `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`) VALUES
                                       ('','','','','','','','', '".$xls['quantity']."',0,'',0,0,'".$xls['price']."',0,0,'".$today."', 0,0,0.0,0,0,0,0,0,0,0,0,
                                       '".$today."','".$today."')");


                //получаем продукт ид для добавление того же товара(описание товара)
                $product_id = $this->db->getLastId();

                $this->db->query("INSERT INTO " . DB_PREFIX . "product_description (`product_id`, `language_id`, `name`, `description`,
                                    `tag`, `meta_description`, `meta_keyword`) VALUES ($product_id,$lang,'".$xls['name']."',' ','',' ','')");


                $this->db->query("INSERT INTO  " . DB_PREFIX . "product_to_store (`product_id`, `store_id`) VALUES ($product_id,0)");
            $in++;

            }


            }

        }

}

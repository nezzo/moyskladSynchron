<?php

class ModelToolmyskladoc21 extends Model {

    private $CATEGORIES = array();
    private $PROPERTIES = array();

    //получаем данные по товару с Моего Склада и заносим в базу
    function setProductAPI(){
        
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


    //Выбираем данные для  xls  отчета
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


        //защита от пустых записей
        //  if (!empty($getAllProductID) && isset($mas_xls) && isset($getAllProductID)){
        if (!empty($getAllProductID) && isset($mas_xls) && isset($getAllProductID)) {

            foreach ($mas_xls as $xls) {
                //поиск по массиву если совпало то апдейтем
                $result = array_search($xls['id'], $getAllProductID);

                if (!empty($result) && isset($result)) {
                    $this->db->query("UPDATE " . DB_PREFIX . "product 
                                               INNER JOIN `" . DB_PREFIX . "product_description` ON " . DB_PREFIX . "product.product_id = " . DB_PREFIX . "product_description.product_id
                                               SET name = '" . $xls['name'] . "', quantity = '" . (int)$xls['quantity'] . "', price = '" . $xls['price'] . "'
                                               WHERE " . DB_PREFIX . "product_description.product_id = '" . (int)$result . "'");

                }elseif(isset($no_base) && !empty($no_base)){

                    $this->db->query("INSERT INTO " . DB_PREFIX . "product (`model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`, `quantity`,
                                      `stock_status_id`, `image`, `manufacturer_id`, `shipping`, `price`, `points`, `tax_class_id`, `date_available`, `weight`, `weight_class_id`,
                                       `length`, `width`, `height`, `length_class_id`, `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`) VALUES
                                       ('','','','','','','','', '".(int)$xls['quantity']."',0,'',0,0,'".$xls['price']."',0,0,'".$today."', 0,0,0.0,0,0,0,0,0,0,0,0,
                                       '".$today."','".$today."')");


                        //получаем продукт ид для добавление того же товара(описание товара)
                        $product_id = $this->db->getLastId();

                        $this->db->query("INSERT INTO " . DB_PREFIX . "product_description (`product_id`, `language_id`, `name`, `description`,
                                    `tag`, `meta_title`, `meta_description`, `meta_keyword`) VALUES ($product_id,$lang,'".$xls['name']."',' ','',' ','','')");


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
                                       ('','','','','','','','', '".(int)$xls['quantity']."',0,'',0,0,'".$xls['price']."',0,0,'".$today."', 0,0,0.0,0,0,0,0,0,0,0,0,
                                       '".$today."','".$today."')");


                //получаем продукт ид для добавление того же товара(описание товара)
                $product_id = $this->db->getLastId();

                $this->db->query("INSERT INTO " . DB_PREFIX . "product_description (`product_id`, `language_id`, `name`, `description`,
                                    `tag`, `meta_title`, `meta_description`, `meta_keyword`) VALUES ($product_id,$lang,'".$xls['name']."',' ','',' ','','')");


                $this->db->query("INSERT INTO  " . DB_PREFIX . "product_to_store (`product_id`, `store_id`) VALUES ($product_id,0)");
            $in++;

            }


            }

        }

}

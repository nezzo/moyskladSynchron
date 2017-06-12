<?php
ini_set('display_errors',1);
error_reporting(E_ALL ^E_NOTICE);

class Controllermodulemoyskladoc21v1 extends Controller
{
    private $error = array();
    public $mas;
    public $diapason;
    public $getAllProductID;
    public $mas_xls;
    public $getAPI = "GET";
    public $postAPI = "POST";
    public $putAPI = "PUT";
    public $deleteAPI = "DELETE";


    public function index()
    {

        $this->load->language('module/moyskladoc21v1');
        $this->load->model('tool/image');

        //$this->document->title = $this->language->get('heading_title');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->request->post['moyskladoc21v1_order_date'] = $this->config->get('moyskladoc21v1_order_date');
            $this->model_setting_setting->editSetting('moyskladoc21v1', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['entry_username'] = $this->language->get('entry_username');
        $data['entry_password'] = $this->language->get('entry_password');

        $data['entry_customer_group'] = $this->language->get('entry_customer_group');
         
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_tab_general'] = $this->language->get('text_tab_general');
        $data['text_tab_product'] = $this->language->get('text_tab_product');
        $data['text_tab_order'] = $this->language->get('text_tab_order');
        $data['text_tab_manual'] = $this->language->get('text_tab_manual');
        $data['text_empty'] = $this->language->get('text_empty');
        $data['text_max_filesize'] = sprintf($this->language->get('text_max_filesize'), @ini_get('max_file_uploads'));
        $data['text_homepage'] = $this->language->get('text_homepage');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_order_currency'] = $this->language->get('entry_order_currency');
        $data['entry_upload'] = $this->language->get('entry_upload');
        $data['button_upload'] = $this->language->get('button_upload');
        $data['entry_download'] = $this->language->get('entry_download');
        $data['button_download'] = $this->language->get('button_download');
        $data['diapason_text'] = $this->language->get('diapason_text');
        $data['text_tab_author'] = $this->language->get('text_tab_author');
        
        $data['text_tab_synchron'] = $this->language->get('text_tab_synchron');
        $data['entry_downoload_product'] = $this->language->get('entry_downoload_product');
        $data['entry_upload_product'] = $this->language->get('entry_upload_product');
        $data['entry_synchron_product'] = $this->language->get('entry_synchron_product');
        
       
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_insert'] = $this->language->get('button_insert');
        $data['button_remove'] = $this->language->get('button_remove');


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['image'])) {
            $data['error_image'] = $this->error['image'];
        } else {
            $data['error_image'] = '';
        }

        if (isset($this->error['moyskladoc21v1_username'])) {
            $data['error_moyskladoc21v1_username'] = $this->error['moyskladoc21v1_username'];
        } else {
            $data['error_moyskladoc21v1_username'] = '';
        }

        if (isset($this->error['moyskladoc21v1_password'])) {
            $data['error_moyskladoc21v1_password'] = $this->error['moyskladoc21v1_password'];
        } else {
            $data['error_moyskladoc21v1_password'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );


        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/moyskladoc21v1', 'token=' . $this->session->data['token'], true)
        );
        $data['token'] = $this->session->data['token'];

        $data['action'] = $this->url->link('module/moyskladoc21v1', 'token=' . $this->session->data['token'], true);

        $data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');;

        if (isset($this->request->post['moyskladoc21v1_username'])) {
            $data['moyskladoc21v1_username'] = $this->request->post['moyskladoc21v1_username'];
         } else {
            $data['moyskladoc21v1_username'] = $this->config->get('moyskladoc21v1_username');
        }

        if (isset($this->request->post['moyskladoc21v1_password'])) {
            $data['moyskladoc21v1_password'] = $this->request->post['moyskladoc21v1_password'];
         } else {
            $data['moyskladoc21v1_password'] = $this->config->get('moyskladoc21v1_password');
        }
 

        if (isset($this->request->post['moyskladoc21v1_status'])) {
            $data['moyskladoc21v1_status'] = $this->request->post['moyskladoc21v1_status'];
        } else {
            $data['moyskladoc21v1_status'] = $this->config->get('moyskladoc21v1_status');
        }

        if (isset($this->request->post['moyskladoc21v1_price_type'])) {
            $data['moyskladoc21v1_price_type'] = $this->request->post['moyskladoc21v1_price_type'];
        } else {
            $data['moyskladoc21v1_price_type'] = $this->config->get('moyskladoc21v1_price_type');
            if (empty($data['moyskladoc21v1_price_type'])) {
                $data['moyskladoc21v1_price_type'][] = array(
                    'keyword' => '',
                    'customer_group_id' => 0,
                    'quantity' => 0,
                    'priority' => 0
                );
            }
        }
 
        if (isset($this->request->post['moyskladoc21v1_order_status'])) {
            $data['moyskladoc21v1_order_status'] = $this->request->post['moyskladoc21v1_order_status'];
        } else {
            $data['moyskladoc21v1_order_status'] = $this->config->get('moyskladoc21v1_order_status');
        }

        if (isset($this->request->post['moyskladoc21v1_order_currency'])) {
            $data['moyskladoc21v1_order_currency'] = $this->request->post['moyskladoc21v1_order_currency'];
        } else {
            $data['moyskladoc21v1_order_currency'] = $this->config->get('moyskladoc21v1_order_currency');
        }


        // Группы
        $this->load->model('customer/customer_group');
        $data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

        $this->load->model('localisation/order_status');

        $order_statuses = $this->model_localisation_order_status->getOrderStatuses();

        foreach ($order_statuses as $order_status) {
            $data['order_statuses'][] = array(
                'order_status_id' => $order_status['order_status_id'],
                'name' => $order_status['name']
            );
        }

        $this->template = 'module/moyskladoc21v1.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $data['heading_title'] = $this->language->get('heading_title');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('module/moyskladoc21v1.tpl', $data));

        //$this->response->setOutput($this->render(), $this->config->get('config_compression'));
    }

    public function download()
    {

        if (isset($this->request->post['ot']) && isset($this->request->post['kolichestvo']) && $this->request->post['kolichestvo'] <= 1000) {
            $ot = $this->request->post['ot'];
            $kolichestvo = $this->request->post['kolichestvo'];

            $this->diapason = array(
                'ot' => $ot,
                'kolichestvo' => $kolichestvo
            );

            $data['link_xls'] = $this->downloadxls();

        }
    }

    private function validate()
    {

        if (!$this->user->hasPermission('modify', 'module/moyskladoc21v1')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;

    }

    public function install()
    {
    }

    public function uninstall()
    {
    }
 
 
    public function cat($category_id)
    {
        $this->load->model('tool/moyskladoc21v1');

        $results = $this->model_tool_moyskladoc21v1->getCat($category_id);

        $this->mas = array();
        foreach ($results as $result) {
            if ($result['parent_id'] != 0) {
                $this->cat($result['parent_id']);
            }
            $this->mas[$result['parent_id']] = $result['name'];

        }

        return $this->mas;

    }

    /*Формируем xls прайс со всем товаром для скачивания*/
    function downloadxls()
    {
        $this->load->model('tool/moyskladoc21v1');

        $cwd = getcwd();
        chdir(DIR_SYSTEM . 'moyskladoc21v1_xls');
        // Подключаем класс для работы с excel
        require_once('PHPExcel/PHPExcel.php');
        // Подключаем класс для вывода данных в формате excel
        require_once('PHPExcel/PHPExcel/Writer/Excel5.php');
        chdir($cwd);


        // Создаем объект класса PHPExcel
        $xls = new PHPExcel();
        //Открываем файл-шаблон
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $xls = $objReader->load(DIR_SYSTEM . 'moyskladoc21v1_xls/PHPExcel/goods.xls');
        // Устанавливаем индекс активного листа
        $xls->setActiveSheetIndex(0);
        // Получаем активный лист
        $sheet = $xls->getActiveSheet();
        // Подписываем лист
        $sheet->setTitle('Экспорт товара');

        $products = $this->model_tool_moyskladoc21v1->dataxls($this->diapason);

        $i = 0;
        /*Создаем цыкл до последнего ид товара и заполняем данными xls*/
        foreach ($products as $product) {

            $index = 1 + (++$i);

            // (Категории)

            $sheet->setCellValue('A' . $index, implode('/', $this->cat($product['category_id'])));
            // $sheet->setCellValue('A' . $index, var_dump($this->cat($product['category_id'])));
            $sheet->getStyle('A' . $index)->getFill()->setFillType(
                PHPExcel_Style_Border::BORDER_THIN);
            $sheet->getStyle('A' . $index)->getFill()->getStartColor()->setRGB('EEEEEE');


            // (id_Product)
            $sheet->setCellValue('B' . $index, $product['product_id']);
            $sheet->getStyle('B' . $index)->getFill()->setFillType(
                PHPExcel_Style_Border::BORDER_THIN);
            $sheet->getStyle('B' . $index)->getFill()->getStartColor()->setRGB('EEEEEE');

            // (Наименование)
            $sheet->setCellValue('C' . $index, $product['name']);
            $sheet->getStyle('C' . $index)->getFill()->setFillType(
                PHPExcel_Style_Border::BORDER_THIN);
            $sheet->getStyle('C' . $index)->getFill()->getStartColor()->setRGB('EEEEEE');

            // (Внешний код)
            $sheet->setCellValue('D' . $index, $this->model_tool_moyskladoc21v1->get_uuid($product['product_id']));
            $sheet->getStyle('D' . $index)->getFill()->setFillType(
                PHPExcel_Style_Border::BORDER_THIN);
            $sheet->getStyle('D' . $index)->getFill()->getStartColor()->setRGB('EEEEEE');

            // (Цена продажи)
            $sheet->setCellValue('G' . $index, $product['price']);
            $sheet->getStyle('G' . $index)->getFill()->setFillType(
                PHPExcel_Style_Border::BORDER_THIN);
            $sheet->getStyle('G' . $index)->getFill()->getStartColor()->setRGB('EEEEEE');


            // (Количество)
            $sheet->setCellValue('T' . $index, $product['quantity']);
            $sheet->getStyle('T' . $index)->getFill()->setFillType(
                PHPExcel_Style_Border::BORDER_THIN);
            $sheet->getStyle('T' . $index)->getFill()->getStartColor()->setRGB('EEEEEE');
        }


        /*Сохраняем данные в файл (путь/файл) и скачиваем*/
        $objWriter = new PHPExcel_Writer_Excel5($xls);
        $data = date("d.m.Y");
        $objWriter->save(DIR_SYSTEM . 'moyskladoc21v1_xls/otchet/export.xls');

        /*переименовываем файл по дате для скачивания*/
        $new_name = rename(DIR_SYSTEM . 'moyskladoc21v1_xls/otchet/export.xls', DIR_SYSTEM . "moyskladoc21v1_xls/otchet/export($data).xls");

        /*передаем с помощью GET запроса на скрипт для скачивания отчета*/
        if ($new_name == true) {
            echo "model/tool/downoload_script_otchet/downoload.php?file=" . DIR_SYSTEM . "moyskladoc21v1_xls/otchet/export($data).xls";
        }


    }

    //import  данных с xls  в базу
    public function importxls()
    {
        $this->load->model('tool/moyskladoc21v1');

        //получаем id  текущего языка и заносим в базу что бы товар отображался
        $data['lang'] = $this->language->get('code');
        $lang = $this->model_tool_moyskladoc21v1->getLanguageId($data['lang']);

        $cwd = getcwd();
        chdir(DIR_SYSTEM . 'moyskladoc21v1_xls');
        // Подключаем класс для работы с excel
        require_once('PHPExcel/PHPExcel.php');
        // Подключаем класс для вывода данных в формате excel
        require_once('PHPExcel/PHPExcel/Writer/Excel5.php');
        chdir($cwd);

        if (isset($this->request->post['good'])) {

            //путь где хранится xls файл для import
            $xlsData = 'controller/module/uploads/import.xls';
            $objPHPExcel = PHPExcel_IOFactory::load($xlsData);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $this->mas_xls = array();
            $this->getAllProductID = array();
            $getID = $this->model_tool_moyskladoc21v1->getAllProductID();
            $i = 1; // с какой строки начинаем считывать данные
            $clock = time(); //временный ключь для массива insert

            $index = 1;

            //создаем одномерный массив для поиска по нему
            foreach ($getID as $row) {

                foreach ($row as $key => $value) {

                    $this->getAllProductID[$value] = $value;
                }
                $index++;
            }
            //максимальное значение ключа с базы
            if(isset($this->getAllProductID) && !empty($this->getAllProductID)){
                $max = array_keys($this->getAllProductID, max($this->getAllProductID));

            }


            foreach ($objWorksheet->getRowIterator() as $row) {
                //столбец с $i строки
                $column_B_Value = (int)$objPHPExcel->getActiveSheet()->getCell("B$i")->getValue();//column Код
                //you can add your own columns B, C, D etc.
                $column_D_Value = $objPHPExcel->getActiveSheet()->getCell("D$i")->getValue();//column Наименование

                $column_I_Value = $objPHPExcel->getActiveSheet()->getCell("I$i")->getValue();//column Остаток
                $column_L_Value = $objPHPExcel->getActiveSheet()->getCell("L$i")->getValue();//column Цена продажи
                //что бы данные не были пустыми и не были 0 (считываем цыфры а не строки стоит (int)
                //  специально что бы устранить строку $column_B_Value)
                //функция на апдейт имя есть то создаем массив
                if (!empty($column_D_Value) && isset($column_D_Value) && 
                    isset($column_B_Value) && !empty($column_B_Value) && isset($column_I_Value) && $column_D_Value != "Наименование") {
                    $this->mas_xls[$column_B_Value] = array(
                        'id' => $column_B_Value,
                        'name' => $column_D_Value,
                        'quantity' => $column_I_Value,
                        'price' => $column_L_Value,
                    );

                    //функция добавляет ключь для массива, что бы залить товар в базу если у товара id 0
                } elseif (isset($max) &&!empty($max) && isset($column_I_Value) &&!empty($column_D_Value) && isset($column_D_Value) && $column_B_Value == 0 && $column_D_Value != "Итого:" && $column_D_Value != "Наименование") {
                    $this->mas_xls[$max[0] + $i] = array(
                        'id' => $max[0] + $i,
                        'name' => $column_D_Value,
                        'quantity' => $column_I_Value,
                        'price' => $column_L_Value,
                    );
                    //inset $column_A_Value value in DB query here

                //добавляем тайм++ временный ключь массива и заносим в базу товар
                }elseif(!isset($max) &&!empty($column_D_Value)&& isset($column_I_Value) && isset($column_D_Value) && $column_B_Value == 0 && $column_D_Value != "Итого:" && $column_D_Value != "Наименование"){
                    $this->mas_xls[++$clock] = array(
                        'id' => 1,
                        'name' => $column_D_Value,
                        'quantity' => $column_I_Value,
                        'price' => $column_L_Value,
                    );

                }

                $i++;

             }
           $import = $this->model_tool_moyskladoc21v1->getxls($this->mas_xls, $this->getAllProductID, $lang);

        }

    }
    
     //c помощью API получаем весь товар и заносим в базу магазина
    function getAllProductMoySklad(){

        //с настроек получаем логин и пароль к Моему Складу
        $login = $this->config->get('moyskladoc21v1_username');
        $pass = $this->config->get('moyskladoc21v1_password');
        
        //получаем текущий язык магазина
        $data['lang'] = $this->language->get('code');
        $lang = $this->model_tool_moyskladoc21v1->getLanguageId($data['lang']);
           
           //получаем значение с какой строки получать товар
           if (isset($this->request->post["countAPIMoySklad"])){
            $counts = $this->request->post["countAPIMoySklad"];
            }
    
          //$urlProduct = "entity/product?offset=$counts&limit=100";
            $urlProduct = "entity/product?offset=$counts&limit=1";
            $product = $this->getNeedInfo($login,$pass,$urlProduct,$this->getAPI);

             
                for($i=0; $i<100; $i++){
                //если дошли до конца списка то выходим из рекурсии
                if(empty($product["rows"][$i]["name"])){
                    exit();
                }
                
                echo $product["rows"][$i]["name"];
                
             } 

            //вызов рекурсии  
            $this->getAllProductMoySklad($counts+$i);

            
    }
    
    //получаем нужную информацию
    function getNeedInfo($login,$password,$url,$method){                                                                                                              
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, "https://online.moysklad.ru/api/remap/1.1/".$url);    
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);     
        curl_setopt($ch, CURLOPT_USERPWD, $login.":".$password);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
            'Accept: application/json',
            'Content-Type: application/json')                                                           
        );             

        if(curl_exec($ch) === false)
        {
            echo 'Curl error: ' . curl_error($ch);
        } 
        
        $errors = curl_error($ch);                                                                                                            
        $result = curl_exec($ch);
        $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);  

         return json_decode($result, true);
    }
}
?>
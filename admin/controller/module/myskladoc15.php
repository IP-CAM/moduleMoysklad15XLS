<?php
 
ini_set('display_errors',1);
error_reporting(E_ALL ^E_NOTICE);

class Controllermodulemyskladoc15 extends Controller
{
    private $error = array();
    public $mas;
    public $diapason;
    public $getAllProductID;
    public $mas_xls;


    public function index()
    {

        $this->load->language('module/myskladoc15');
        $this->load->model('tool/image');

        //$this->document->title = $this->language->get('heading_title');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->request->post['myskladoc15_order_date'] = $this->config->get('myskladoc15_order_date');
            $this->model_setting_setting->editSetting('myskladoc15', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
            
        }

        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['entry_username'] = $this->language->get('entry_username');
        $this->data['entry_password'] = $this->language->get('entry_password');

        $this->data['entry_customer_group'] = $this->language->get('entry_customer_group');
        $this->data['entry_quantity'] = $this->language->get('entry_quantity');
        $this->data['entry_priority'] = $this->language->get('entry_priority');
        $this->data['text_image_manager'] = $this->language->get('text_image_manager');
        $this->data['text_browse'] = $this->language->get('text_browse');
        $this->data['text_clear'] = $this->language->get('text_clear');
        $this->data['entry_name'] = $this->language->get('entry_name');
        $this->data['entry_image'] = $this->language->get('entry_image');

        $this->data['entry_order_status_to_exchange'] = $this->language->get('entry_order_status_to_exchange');
        $this->data['entry_order_status_to_exchange_not'] = $this->language->get('entry_order_status_to_exchange_not');

        $this->data['text_yes'] = $this->language->get('text_yes');
        $this->data['text_no'] = $this->language->get('text_no');
        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_tab_general'] = $this->language->get('text_tab_general');
        $this->data['text_tab_author'] = $this->language->get('text_tab_author');
        $this->data['text_tab_product'] = $this->language->get('text_tab_product');
        $this->data['text_tab_order'] = $this->language->get('text_tab_order');
        $this->data['text_tab_manual'] = $this->language->get('text_tab_manual');
        $this->data['text_empty'] = $this->language->get('text_empty');
        $this->data['text_max_filesize'] = sprintf($this->language->get('text_max_filesize'), @ini_get('max_file_uploads'));
        $this->data['text_homepage'] = $this->language->get('text_homepage');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_order_status'] = $this->language->get('entry_order_status');
        $this->data['entry_order_currency'] = $this->language->get('entry_order_currency');
        $this->data['entry_upload'] = $this->language->get('entry_upload');
        $this->data['button_upload'] = $this->language->get('button_upload');
        $this->data['entry_download'] = $this->language->get('entry_download');
        $this->data['button_download'] = $this->language->get('button_download');
        $this->data['diapason_text'] = $this->language->get('diapason_text');


        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');
        $this->data['button_insert'] = $this->language->get('button_insert');
        $this->data['button_remove'] = $this->language->get('button_remove');


        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['image'])) {
            $this->data['error_image'] = $this->error['image'];
        } else {
            $this->data['error_image'] = '';
        }

        if (isset($this->error['myskladoc15_username'])) {
            $this->data['error_myskladoc15_username'] = $this->error['myskladoc15_username'];
        } else {
            $this->data['error_myskladoc15_username'] = '';
        }

        if (isset($this->error['myskladoc15_password'])) {
            $this->data['error_myskladoc15_password'] = $this->error['myskladoc15_password'];
        } else {
            $this->data['error_myskladoc15_password'] = '';
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );


        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/myskladoc15', 'token=' . $this->session->data['token'], true),
            'separator' => ' :: '
        );
        $this->data['token'] = $this->session->data['token'];

        $this->data['action'] = $this->url->link('module/myskladoc15', 'token=' . $this->session->data['token'], true);

        $this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');;

        if (isset($this->request->post['myskladoc15_username'])) {
            $this->data['myskladoc15_username'] = $this->request->post['myskladoc15_username'];
        } else {
            $this->data['myskladoc15_username'] = $this->config->get('myskladoc15_username');
        }

        if (isset($this->request->post['myskladoc15_password'])) {
            $this->data['myskladoc15_password'] = $this->request->post['myskladoc15_password'];
        } else {
            $this->data['myskladoc15_password'] = $this->config->get('myskladoc15_password');
        }

        if (isset($this->request->post['myskladoc15_allow_ip'])) {
            $this->data['myskladoc15_allow_ip'] = $this->request->post['myskladoc15_allow_ip'];
        } else {
            $this->data['myskladoc15_allow_ip'] = $this->config->get('myskladoc15_allow_ip');
        }

        if (isset($this->request->post['myskladoc15_status'])) {
            $this->data['myskladoc15_status'] = $this->request->post['myskladoc15_status'];
        } else {
            $this->data['myskladoc15_status'] = $this->config->get('myskladoc15_status');
        }

        if (isset($this->request->post['myskladoc15_price_type'])) {
            $this->data['myskladoc15_price_type'] = $this->request->post['myskladoc15_price_type'];
        } else {
            $this->data['myskladoc15_price_type'] = $this->config->get('myskladoc15_price_type');
            if (empty($this->data['myskladoc15_price_type'])) {
                $this->data['myskladoc15_price_type'][] = array(
                    'keyword' => '',
                    'customer_group_id' => 0,
                    'quantity' => 0,
                    'priority' => 0
                );
            }
        }


        if (isset($this->request->post['myskladoc15_order_status_to_exchange'])) {
            $this->data['myskladoc15_order_status_to_exchange'] = $this->request->post['myskladoc15_order_status_to_exchange'];
        } else {
            $this->data['myskladoc15_order_status_to_exchange'] = $this->config->get('myskladoc15_order_status_to_exchange');
        }


        if (isset($this->request->post['myskladoc15_order_status'])) {
            $this->data['myskladoc15_order_status'] = $this->request->post['myskladoc15_order_status'];
        } else {
            $this->data['myskladoc15_order_status'] = $this->config->get('myskladoc15_order_status');
        }

        if (isset($this->request->post['myskladoc15_order_currency'])) {
            $this->data['myskladoc15_order_currency'] = $this->request->post['myskladoc15_order_currency'];
        } else {
            $this->data['myskladoc15_order_currency'] = $this->config->get('myskladoc15_order_currency');
        }


        // Группы
        //$this->load->model('customer/customer_group');
        //$this->data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

        $this->load->model('localisation/order_status');

        $order_statuses = $this->model_localisation_order_status->getOrderStatuses();

        foreach ($order_statuses as $order_status) {
            $this->data['order_statuses'][] = array(
                'order_status_id' => $order_status['order_status_id'],
                'name' => $order_status['name']
            );
        }

        $this->template = 'module/myskladoc15.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );
 
 
        $this->load->model('design/layout');
        
        $this->data['layouts'] = $this->model_design_layout->getLayouts();

         
                 
        $this->template = 'module/myskladoc15.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );
                
        $this->response->setOutput($this->render());

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

            //проверяем что бы не приходили пустые данные
            if(!empty($this->downloadxls())){
                $data['link_xls'] = $this->downloadxls();

            }
            
        }
    }

    private function validate()
    {

        if (!$this->user->hasPermission('modify', 'module/myskladoc15')) {
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

    // ---
    public function modeCheckauth()
    {

        // Проверяем включен или нет модуль
        if (!$this->config->get('myskladoc15_status')) {
            echo "failure\n";
            echo "myskladoc15 module OFF";
            exit;
        }

        // Авторизуем
        if (($this->config->get('myskladoc15_username') != '') && (@$_SERVER['PHP_AUTH_USER'] != $this->config->get('myskladoc15_username'))) {
            echo "success\n";
            //echo "error login"; убрал ибо не идет авторизация по логину и паролю в моем складе из-за хоста
        }

        if (($this->config->get('myskladoc15_password') != '') && (@$_SERVER['PHP_AUTH_PW'] != $this->config->get('myskladoc15_password'))) {
            echo "success\n";
       // echo "error password"; убрал ибо не идет авторизация по логину и паролю в моем складе из-за хоста
           // exit;
        }

        echo "success\n";
        echo "key\n";
        echo md5($this->config->get('myskladoc15_password')) . "\n";
    }

    public function modeSaleInit()
    {
        $limit = 100000 * 1024;

        echo "zip=no\n";
        echo "file_limit=" . $limit . "\n";
    }


    public function modeQueryOrders()
    {
    $this->request->cookie['key'] = md5($this->config->get('myskladoc15_password'));
        if (!isset($this->request->cookie['key'])) {
            echo "Cookie fail\n";
            return;
        }

        if ($this->request->cookie['key'] != md5($this->config->get('myskladoc15_password'))) {
            echo "failure\n";
            echo "Session error";
            return;
        }

        $this->load->model('tool/myskladoc15');

        $orders = $this->model_tool_myskladoc15->queryOrders(array(
            'from_date' => $this->config->get('myskladoc15_order_date')
        , 'exchange_status' => $this->config->get('myskladoc15_order_status_to_exchange')
        , 'new_status' => $this->config->get('myskladoc15_order_status')
        , 'currency' => $this->config->get('myskladoc15_order_currency') ? $this->config->get('myskladoc15_order_currency') : 'руб.'
        ));


        echo iconv('utf-8', 'cp1251', $orders);
    }

    public function modeOrdersChangeStatus()
    {
    $this->request->cookie['key'] = md5($this->config->get('myskladoc15_password'));
        if (!isset($this->request->cookie['key'])) {
            echo "Cookie fail\n";
            return;
        }

        if ($this->request->cookie['key'] != md5($this->config->get('myskladoc15_password'))) {
            echo "failure\n";
            echo "Session error";
            return;
        }

        $this->load->model('tool/myskladoc15');

        $result = $this->model_tool_myskladoc15->queryOrdersStatus(array(
            'from_date' => $this->config->get('myskladoc15_order_date'),
            'exchange_status' => $this->config->get('myskladoc15_order_status_to_exchange'),
            'new_status' => $this->config->get('myskladoc15_order_status'),
        ));

        if ($result) {
            $this->load->model('setting/setting');
            $config = $this->model_setting_setting->getSetting('myskladoc15');
            $config['myskladoc15_order_date'] = date('Y-m-d H:i:s');
            $this->model_setting_setting->editSetting('myskladoc15', $config);
        }

        if ($result)
            echo "success\n";
        else
            echo "fail\n";
    }

    public function cat($category_id)
    {
        $this->load->model('tool/myskladoc15');

        $results = $this->model_tool_myskladoc15->getCat($category_id);

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
        $this->load->model('tool/myskladoc15');

        $cwd = getcwd();
        chdir(DIR_SYSTEM . 'myskladoc15_xls');
        // Подключаем класс для работы с excel
        require_once('PHPExcel/PHPExcel.php');
        // Подключаем класс для вывода данных в формате excel
        require_once('PHPExcel/PHPExcel/Writer/Excel5.php');
        chdir($cwd);


        // Создаем объект класса PHPExcel
        $xls = new PHPExcel();
        //Открываем файл-шаблон
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $xls = $objReader->load(DIR_SYSTEM . 'myskladoc15_xls/PHPExcel/goods.xls');
        // Устанавливаем индекс активного листа
        $xls->setActiveSheetIndex(0);
        // Получаем активный лист
        $sheet = $xls->getActiveSheet();
        // Подписываем лист
        $sheet->setTitle('Экспорт товара');

        $products = $this->model_tool_myskladoc15->dataxls($this->diapason);


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
            $sheet->setCellValue('D' . $index, $this->model_tool_myskladoc15->get_uuid($product['product_id']));
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
        $this->data = date("d.m.Y");
        $objWriter->save(DIR_SYSTEM . 'myskladoc15_xls/otchet/export.xls');

        /*переименовываем файл по дате для скачивания*/
        $new_name = rename(DIR_SYSTEM . 'myskladoc15_xls/otchet/export.xls', DIR_SYSTEM . "myskladoc15_xls/otchet/export($this->data).xls");

        /*передаем с помощью GET запроса на скрипт для скачивания отчета*/
        if ($new_name == true) {
            echo "model/tool/downoload_script_otchet/downoload.php?file=" . DIR_SYSTEM . "myskladoc15_xls/otchet/export($this->data).xls";
        }




    }

    //import  данных с xls  в базу
    public function importxls()
    {
        $this->load->model('tool/myskladoc15');

        //получаем id  текущего языка и заносим в базу что бы товар отображался
        $this->data['lang'] = $this->language->get('code');
        $lang = $this->model_tool_myskladoc15->getLanguageId($this->data['lang']);

        $cwd = getcwd();
        chdir(DIR_SYSTEM . 'myskladoc15_xls');
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
            $getID = $this->model_tool_myskladoc15->getAllProductID();
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
                    $column_B_Value =  preg_replace("/[^0-9]/", '',$objPHPExcel->getActiveSheet()->getCell("B$i")->getValue());//column Код
 
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

           

       $import = $this->model_tool_myskladoc15->getxls($this->mas_xls, $this->getAllProductID, $lang);

           

        }

    }
}
?>

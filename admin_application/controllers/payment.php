<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment extends CI_Controller {
	
        private $layout_data;
        public function __construct()
        {
            //set_time_limit (0); // Run forever
            ini_set('memory_limit','1024M'); 

            parent::__construct();

            $this->load->model('payments');
            $this->load->model('oldpayments');

            $this->load->helper('url');							// This gives access to echo'ing base_url

            $config = new Qb_config();
                  $this->layout_data['title'] = 'Kizzang Payment Server';
            $this->layout_data = array('quickbooks_menu_url' => $config->quickbooks_menu_url,
                'quickbooks_oauth_url' => $config->quickbooks_oauth_url, 'title' => 'Kizzang Payment Server');
        }
        public function index($days = 7, $type = "A", $dollar_amount = 0)
        {
                $data = $this->payments->getAll($days, $type, $dollar_amount);
                //print_r($data); die();
                $this->layout_data['content'] = $this->load->view("payments/index", $data, true);
                $this->layout_data['page'] = "PaymentsView";
                $this->load->view("layouts/admin", $this->layout_data);            
        }
        
        public function old_all($days = 180, $dollar_amount = 0)
        {
                $data = $this->oldpayments->getAll($days, $dollar_amount);
                //print_r($data); die();
                $this->layout_data['content'] = $this->load->view("payments/old_all", $data, true);
                $this->layout_data['page'] = "PaymentsOldView";
                $this->load->view("layouts/admin", $this->layout_data);            
        }
    
        public function players()
        {
            $players = $this->payments->getPlayers();
            $this->layout_data['content'] = $this->load->view("payments/players", $players, true);
            $this->layout_data['page'] = "PaymentPlayers";
            $this->load->view("layouts/admin", $this->layout_data);            
        }
        
        public function ajax_qb($id)
        {
            $message = "";
            $ret = $this->payments->updateQB($id, $message);
            print json_encode(array('success' => $ret, 'message' => $message));
            die();
        }
        
        public function player($id)
        {
            $players = $this->payments->getPlayer($id);
            $this->layout_data['content'] = $this->load->view("payments/player", $players, true);
            $this->layout_data['page'] = "Players";
            $this->load->view("layouts/admin", $this->layout_data);            
        }
        
        public function dups($id)
        {
            $data = $this->payments->getDup($id);
            $this->load->view("admin/reports/dups", $data);            
        }
        
        public function mass_pay()
        {
            if(!isset($_POST['ids']))
            {
                print json_encode(array('message' => 'No IDs given'));
                die();
            }
            
            $message = "";
            $this->payments->payClaims($_POST['ids'], $message);
            print json_encode(array('message' => $message));
        }
        
        public function mass_pay_old()
        {
            if(!isset($_POST['ids']))
            {
                print json_encode(array('message' => 'No IDs given'));
                die();
            }
            
            $message = "";
            $this->oldpayments->payClaims($_POST['ids'], $message);
            print json_encode(array('message' => $message));
        }
                        
        public function report()
        {
            $wins = $this->payments->getAllReport();
            $this->layout_data['content'] = $this->load->view("payments/report", compact("wins"), true);
            $this->layout_data['page'] = "PaymentReport";
            $this->load->view("layouts/admin", $this->layout_data);    
        }
    
        public function edit($id)
        {                        
            $this->layout_data['title'] = "KPS - Edit - " . $id; 
            $data = $this->payments->get($id);
            $this->layout_data['content'] = $this->load->view("payments/edit", $data, true);
            $this->layout_data['page'] = "Edit";
            $this->load->view("layouts/admin", $this->layout_data);
        }
                 
        public function ajax_forfeit($id)
        {
            print json_encode(array('success' => $this->payments->forfeitClaim($id)));
            die();
        }
        
        public function ajax_old_forfeit($id)
        {
            print json_encode(array('success' => $this->oldpayments->forfeitClaim($id)));
            die();
        }
                
        public function ajax_manual_pay($id)
        {
            print json_encode(array('success' => $this->payments->manualPayClaim($id)));
            die();
        }
        
        public function ajax_old_manual_pay($id)
        {
            print json_encode(array('success' => $this->oldpayments->manualPayClaim($id)));
            die();
        }
        
        public function ajax_pay($id)
        {
            $message = "";
            $ret = $this->payments->payClaim($id, $message);
            print json_encode(array('success' => $ret, 'message' => $message));
            die();
        }
        
        public function ajax_old_pay($id)
        {
            $message = "";
            $ret = $this->oldpayments->payClaim($id, $message);
            print json_encode(array('success' => $ret, 'message' => $message));
            die();
        }
}

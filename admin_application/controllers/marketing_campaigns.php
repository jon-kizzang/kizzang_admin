<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Marketing_Campaigns extends CI_Controller {
        public function __construct()
	{
		set_time_limit (0); // Run forever
		ini_set('memory_limit','1024M'); 
		
		parent::__construct();
		
		$this->load->model('marketing_model');
               $this->load->model('emercury_model');
		
		$this->load->helper('url');							// This gives access to echo'ing base_url
	}
    
        public function ajax_add_emails($id = NULL)
        {
            if($_FILES && isset($_FILES['file']))
            {                             
                print json_encode(array('success' => true, 'message' => $this->marketing_model->addEmails($_FILES['file']['tmp_name'], $id)));
                die();                
            }
            
            print json_encode(array('success' => false, 'message' => 'File not uploaded correctly'));
            die();
        }
    
        public function view()
        {                    
            $data['campaigns'] = $this->marketing_model->getAll();
            $layout_data['content'] = $this->load->view("admin/view_marketing_campaigns", $data, true);
            $layout_data['page'] = "ViewMarketingCampaigns";
            $this->load->view("layouts/admin", $layout_data);           
        }
       
        public function add($id = NULL)
        {            
            $data = $this->marketing_model->get($id);            
            
            //print_r($data); die();
            $layout_data['content'] = $this->load->view("admin/add_marketing_campaign", $data, true);
            $layout_data['page'] = "EditMarketingCampaign";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function update_db()
        {
            print json_encode(array('success' => $this->emercury_model->updateDB()));
            die();
        }
        
        public function ajax_add_campaign()
        {
            print json_encode(array('success' => $this->marketing_model->addCampaign($_POST)));
            die();
        }
               
        public function ajax_send_campaign($id)
        {
            print $this->marketing_model->sendCampaign($id);
            die();
        }
}
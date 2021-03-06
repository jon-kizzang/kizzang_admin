<?php

class Admin_Sweepstakes extends CI_Controller
{
	public function __construct()
	{
		set_time_limit (0); // Run forever
		ini_set('memory_limit','1024M'); 
		
		parent::__construct();
		
		$this->load->model('admin_model');
		
		$this->load->helper('url');							// This gives access to echo'ing base_url
	}
        
       public function index($type = "current")
       {                        
            $data['sweepstakes'] = $this->admin_model->getSweepstakes($type);
            $data['type'] = $type;
            $layout_data['content'] = $this->load->view("admin/view_sweepstakes", $data, true);
            $layout_data['page'] = "ViewSweepstakes";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function current()
       {
           $this->index("current");
       }
       
       public function future()
       {
           $this->index("future");
       }
       
       public function past()
       {
           $this->index("past");
       }
       
       public function add()
       {
           $data['sweepstakes'] = $data['rules'] = NULL;            
            $layout_data['content'] = $this->load->view("admin/add_sweepstakes", $data, true);
            $layout_data['page'] = "EditSweepstakes";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function edit($id)
       {
           $data = $this->admin_model->getSweepstake($id);            
            $layout_data['content'] = $this->load->view("admin/add_sweepstakes", $data, true);
            $layout_data['page'] = "EditSweepstakes";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function delete($id)
       {
           $this->admin_model->deleteSweepstakes($id);
           print json_encode(array('success' => true));
           die();
       }
       
       public function pick_winner($id)
       {
           $this->load->view("admin/add_ss_winner", $this->admin_model->getSweepstakesWinner($id));
       }
       
       public function ajax_add_sweepstakes()
        {
            $data = $_POST;
            $errors = array();
            if(!$this->validate_sweepstakes($data, $errors))
            {
                print json_encode(array('success' => false, 'errors' => $errors));
                die();
            }
            $id = $this->admin_model->saveSweepstakes($data);
            print json_encode(array('success' => true, 'id' => $id));
            die();
        }
        
        private function validate_sweepstakes($data, &$errors)
        {
            foreach($data as $key => $value)
            {
                switch($key)
                {
                    case 'name':
                    case 'description':
                        if(!$value)
                            $errors[$key] = "Field Required";
                        break;
                        
                    case 'color':
                    case 'text_color':
                        if(!preg_match("/^0x[A-F0-9]{6}/", $value))
                            $errors[$key] = "Invalid Color " . strstr($value, "0x");
                        break;
                    
                    case 'imageURL':
                    case 'titleImageURL':
                        if(!$value || !preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $value))
                            $errors[$key] = "Required Valid URL";
                        break;
                     
                    case "maxEntrants":
                    case "displayValue":
                    case "taxValue":
                    case "maxWinners":
                        if(!$value || !is_numeric($value))
                            $errors[$key] = "Required Numeric Value";
                        break;
                        
                    case "startDate":
                    case "endDate":
                        if(!$value || !strtotime($value))
                            $errors[$key] = "Date Required and needs to be a valid date";
                        break;
                      
                }
            }
            if(count($errors))
                return false;
            return true;
        }
}
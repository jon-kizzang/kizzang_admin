<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lottery extends CI_Controller {
    public function __construct()
    {
        set_time_limit (0); // Run forever
        ini_set('memory_limit','1024M'); 

        parent::__construct();	
        $this->load->model("lottery_model");        
        $this->load->helper('url');
    }
    
    public function add_answers($id)
    {
        
        $data = $this->lottery_model->getAnswers($id);
        $this->load->view("admin/lottery/add_answers", $data);
    }
    
    public function ajax_grade_cards($id)
    {
        print $this->lottery_model->gradeLotteryAnswers($id);
        die();
    }
    
    public function ajax_update_answers()
    {
        print json_encode(array('success' => $this->lottery_model->updateAnswers($_POST)));
        die();
    }
        
    public function view()
    {
        $dates = $_POST;
        if(!isset($dates['startDate']))
            $dates['startDate'] = date("Y-m-d", strtotime("-5 days"));
        if(!isset($dates['endDate']))
            $dates['endDate'] = date("Y-m-d", strtotime("+5 days"));
        
        $data = $this->lottery_model->getConfigs($dates);
        $layout_data['content'] = $this->load->view("admin/lottery/view", $data, true);
        $layout_data['page'] = "ViewLotteryConfigs";
        $this->load->view("layouts/admin", $layout_data);
    }
    
    public function add($id = NULL)
    {
        $data = $this->lottery_model->getConfig($id);
        $layout_data['content'] = $this->load->view("admin/lottery/add", $data, true);
        $layout_data['page'] = "EditLotteryConfigs";
        $this->load->view("layouts/admin", $layout_data);
    }
    
    public function ajax_add()
    {
        print $this->lottery_model->add($_POST);
        die();
    }
}
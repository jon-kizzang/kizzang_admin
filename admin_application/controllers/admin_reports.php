<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Reports extends CI_Controller {
        public function __construct()
	{
		set_time_limit (0); // Run forever
		ini_set('memory_limit','1024M'); 
		
		parent::__construct();
		
		$this->load->model('report_model');
		
		$this->load->helper('url');							// This gives access to echo'ing base_url
	}
    
        public function main()
        {
            $startDate = date('Y-m-d', strtotime("-7 day"));
            $endDate = date('Y-m-d', strtotime("now"));
            $data = $this->report_model->getMainGraphs($startDate, $endDate);
            $layout_data['content'] = $this->load->view("admin/reports/main", $data, true);
            $layout_data['page'] = "MainReports";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function map()
        {
            $center_lng = -100.285476;
            $center_lat = 36.1566615;
            $google_api = getenv("GOOGLEGEOCODINGAPI");
            $points = $this->report_model->getMapPoints();
            $layout_data['content'] = $this->load->view("admin/reports/map", compact('center_lng', 'center_lat', 'points', 'google_api'), true);
            $layout_data['page'] = "MapReport";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function campaign_conversions()
        {
            $data = $this->report_model->campaignConversions(date('Y-m-d'));
            $layout_data['content'] = $this->load->view("admin/reports/conversions", $data, true);
            $layout_data['page'] = "Conversions";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function ajax_conversions()
        {
            $data = $_POST;
            if(!isset($data['date']))
                die();            
            
            $data = $this->report_model->campaignConversions($data['date']);
            $data['success'] = true;
            print json_encode($data);
            die();
        }
        
        public function fallout($campaignId = NULL)
        {
            $data = $this->report_model->impressionFallOut($campaignId);            
            $layout_data['content'] = $this->load->view("admin/reports/fallout", $data, true);
            $layout_data['page'] = "CampaignFallout";
            $this->load->view("layouts/admin", $layout_data);
        }      
        
        public function sponsor_fallout($sponsorId = NULL)
        {
            $data = $this->report_model->sponsorFallOut($sponsorId);            
            $layout_data['content'] = $this->load->view("admin/reports/sponsor_fallout", $data, true);
            $layout_data['page'] = "SponsorFallout";
            $this->load->view("layouts/admin", $layout_data);
        }      
        
        public function ajax_main()
        {
            $data = $_POST;
            if(!isset($data['startDate']) || !isset($data['endDate']))
                die();            
            
            $data = $this->report_model->getMainGraphs($data['startDate'], $data['endDate']);
            $data['success'] = true;
            print json_encode($data);
            die();
        }
        
        public function slots()
        {
            $startDate = date('Y-m-d', strtotime("-7 day"));
            $endDate = date('Y-m-d', strtotime("now"));
            $data = $this->report_model->getSlotGraphs($startDate, $endDate);
            $layout_data['content'] = $this->load->view("admin/reports/slots", $data, true);
            $layout_data['page'] = "SlotReports";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function top_ten($num_recs = 10, $order_by = "game_total")
        {
            $data = $this->report_model->getTopTen($num_recs, $order_by);
            $layout_data['content'] = $this->load->view("admin/reports/top_ten", $data, true);
            $layout_data['page'] = "TopTenReport";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function slot_stats()
        {
            if(!isset($_POST['startDate']))
            {
                $startDate = date("Y-m-d");
                $endDate = date("Y-m-d");
            }
            else
            {
                $startDate = $_POST['startDate'];
                $endDate = $_POST['endDate'];
            }
            $data = $this->report_model->getSlotStats($startDate, $endDate);
            $data['startDate'] = $startDate;
            $data['endDate'] = $endDate;
            $layout_data['content'] = $this->load->view("admin/reports/slot_stats", $data, true);
            $layout_data['page'] = "SlotStats";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function ad_stats($startDate = "", $endDate = "")
        {
            if(!$startDate)
                $startDate = $endDate = date("Y-m-d");
            $data = $this->report_model->getAdStats($startDate, $endDate);
            $layout_data['content'] = $this->load->view("admin/reports/ad_stats", $data, true);
            $layout_data['page'] = "AdStats";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function ajax_slots()
        {
            $data = $_POST;
            if(!isset($data['startDate']) || !isset($data['endDate']))
                die();            
            
            $data = $this->report_model->getSlotGraphs($data['startDate'], $data['endDate']);
            $ret = array('slot_scores' => $data['slot_scores'], 'slot_plays' => $data['slot_plays'], 'success' => true);            
            print json_encode($ret);
            die();
        }
        
        public function last_week_winners()
        {
            $winners = $this->report_model->lastWeekWinners();
            $layout_data['content'] = $this->load->view("admin/reports/week_winners", compact('winners'), true);
            $layout_data['page'] = "WeekWinnersReports";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function sweepstakes()
        {
            $data['sweepstakes'] = $this->report_model->getSweepstakesDD();
            $layout_data['content'] = $this->load->view("admin/reports/sweepstakes", $data, true);
            $layout_data['page'] = "SweepstakesReports";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function ajax_sweepstakes()
        {
            $data = $_POST;
            if(!isset($data['id']))
                die();            
            
            $data = $this->report_model->getSweepstakesGraphs($data['id']);
            $data['success'] = true;
            print json_encode($data);
            die();
        }
        
        public function dups()
        {            
            $layout_data['content'] = $this->load->view("admin/reports/dups", array('player' => array()), true);
            $layout_data['page'] = "Dups";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function ajax_find_dups()
        {            
            $data = $this->report_model->findDups($_POST);
            print $this->load->view("admin/reports/dup_sections", $data, true);
            die();
        }
        
        public function parlays()
        {
            $data['parlays'] = $this->report_model->getParlayDD();
            $layout_data['content'] = $this->load->view("admin/reports/parlay", $data, true);
            $layout_data['page'] = "ParlayReports";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function ajax_parlay()
        {
            $data = $_POST;
            if(!isset($data['id']))
                die();            
            
            $data = $this->report_model->getParlayGraphs($data['id']);
            $data['success'] = true;
            print json_encode($data);
            die();
        }
        
        public function update_events()
        {
            $this->report_model->updateEvents(false);
        }
        
        public function process_events()
        {
            $this->report_model->processEvents();
        }
        
        public function retention()
        {
            if(isset($_POST['date']))
                $date = $_POST['date'];
            else
                $date = date('Y-m-d', strtotime("-1 month"));
            
           $layout_data['page'] = "Retention";
           $data = $this->report_model->retentionMatrix($date);
           $layout_data['content'] = $this->load->view('/admin/reports/retention', $data, true);           
           $this->load->view("layouts/admin", $layout_data);
        }
        
        public function update_retention()
        {
            print $this->report_model->retentionCreate();
            die();
        }
        
        public function db_size()
        {
           $layout_data['page'] = "DBSizes";
           $data = $this->report_model->DBSizes();
           $layout_data['content'] = $this->load->view('/admin/reports/db_sizes', $data, true);           
           $this->load->view("layouts/admin", $layout_data);
        }
        
        public function db_tables($conn, $schema)
        {
            $data = compact('conn','schema');
            $data['rows'] = $this->report_model->DBTableSizes($conn, $schema);
            $this->load->view('/admin/reports/db_tables', $data); 
        }
}
<?php

class Admin_Slots extends CI_Controller
{
	public function __construct()
	{
		set_time_limit (0); // Run forever
		ini_set('memory_limit','1024M'); 
		
		parent::__construct();
        $this->db = $this->load->database('slots', true);
		
		$this->load->model('admin_slots_model');
		
		$this->load->helper('url');							// This gives access to echo'ing base_url
	}                
        
        public function index()
        {
                $layout_data['content'] = $this->load->view("admin/view_slots", array('slots' => $this->admin_slots_model->getSlots()), true);
                $layout_data['page'] = "ViewSlots";
                $this->load->view("layouts/admin", $layout_data);
        }
                
        public function archive_tables()
        {
            $count = $this->admin_slots_model->archiveTables();
            return array('success' => true, 'message' => "$count tables archived");            
        }
        
        public function archive_archived_tables()
        {
            $count = $this->admin_slots_model->archiveTablePast();
            return array('success' => true, 'message' => "$count tables archived");            
        }

        public function add_slot($id = NULL)
        {
            if($id)
                $slot = $this->admin_slots_model->getSlot($id);
            else
                $slot = array('slot' => NULL, 'prizes' => NULL);
            
            $slot['adPlacements'] = $this->admin_slots_model->getColumnEnum('kizzangslot', 'SlotGame', 'adPlacement');
            $slot['SlotTypes'] = $this->admin_slots_model->getColumnEnum('kizzangslot', 'SlotGame', 'SlotType');
            $layout_data['content'] = $this->load->view("admin/add_slots", $slot, true);
            $layout_data['page'] = "AddSlots";
            $this->load->view("layouts/admin", $layout_data);
        }                

        public function ajax_add_slot()
        {
            $data = $_POST;
            $errors = array();
            if(!$this->validate_slot($data, $errors))
            {
                print json_encode(array('success' => false, 'errors' => $errors));
                die();
            }
            
            $this->admin_slots_model->addSlot($data);
            $rs = $this->db->query("Desc SlotTournament");
            foreach($rs->result() as $row)
            {
                if($row->Field != "GameIDs")
                    continue;
                
                if(stristr($row->Field, $data['Theme']) === false)
                {
                    $type = str_replace("set(", "set('" . $data['Theme'] . "',", $row->Field);
                    $this->db->query("Alter table SlotTournament change GameIDs GameIDs $type");
                }
                break;
            }
            
            print json_encode(array('success' => true));
            die();
        }
        
        private function validate_slot($data, &$errors)
        {
            foreach($data as $key => $value)
            {
                switch($key)
                {
                    case 'Name':
                    case 'Theme':
                    case 'Math':
                    case 'Disclaimer':
                        if(!$value)
                            $errors[$key] = "Required Field";
                        break;
                        
                    case 'SpinsTotal':
                    case 'SecsTotal':
                        if(!$value || !is_numeric($value))
                            $errors[$key] = "Required and must be numeric";
                        break;
                        
                    case 'StartTime':
                    case 'EndTime':
                        if(!preg_match("/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/", $value))
                           $errors[$key] = "Invalid Time";
                        break;
                }                    
            }
            if(count($errors))
                return false;
            return true;
        }
        
        public function add_prize($game_id, $prize_id = NULL)
        {
            $data = $this->admin_slots_model->getPrize($game_id, $prize_id);
            $this->load->view('admin/add_prize', $data);
        }
        
        public function ajax_delete_prize($slot_id, $place)
        {
            print json_encode(array('success' => $this->admin_slots_model->deletePrize($slot_id, $place)));
            die();
        }
        
        public function slot_stats($id)
        {
            $data = $this->admin_slots_model->getStats($id);
            $this->load->view('admin/view_slot_stats', $data);
        }
        
        public function ajax_add_prize()
        {
            $data = $_POST;
            $errors = array();
            if(!$this->validate_prize($data, $errors))
            {
                print json_encode(array('success' => false, 'errors' => $errors));
                die();
            }
            $this->admin_slots_model->addPrize($data);
            print json_encode(array('success' => true));
            die();
        }
        
        private function validate_prize($data, &$errors)
        {
            foreach($data as $key => $value)
            {
                switch($key)
                {
                    case "Place":
                    case "Amount":
                        if(!$value || !is_numeric($value))
                            $errors[$key] = "Field is required and must me numeric";
                        break;
                        
                    case "Prize":
                        if(!$value)
                            $errors[$key] = "Required";
                        break;
                }
            }
            if($errors)
                return false;
            return true;
        }
        
        public function view_tournaments()
        {
            if(isset($_POST['startDate']) && isset($_POST['endDate']))
            {
                $startDate = $_POST['startDate'];
                $endDate = $_POST['endDate'];
            }
            else
            {
                $startDate = date('Y-m-d', strtotime("-1 DAY"));
                $endDate = date('Y-m-d', strtotime("+ 5 DAY"));
            }
            $layout_data['content'] = $this->load->view("admin/view_slot_tournaments", $this->admin_slots_model->getSlotTournaments($startDate, $endDate), true);
            $layout_data['page'] = "ViewSlotTournaments";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function add_tournament($id = NULL)
        {
            if($id)
                $data = $this->admin_slots_model->getSlotTournament($id);
            else
                $data = array('tournament' => NULL, 'prizes' => array());
            
            $data['types'] = $this->admin_slots_model->getColumnEnum("kizzangslot", "SlotTournament", "type");
            $data['games'] = $this->admin_slots_model->getSlots();
            $layout_data['content'] = $this->load->view("admin/add_slot_tournament", $data, true);
            $layout_data['page'] = "AddSlotTournaments";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function replicate_tournament($id)
        {
            $data = $this->admin_slots_model->getSlotTournament($id);
            $this->load->view("admin/replicate_slot_tournament", $data);
        }
        
        public function validate_tournament_dates()
        {
            $data = $_POST;
            $data_view = $this->admin_slots_model->calcTournamentDates($data);
            if(!$data_view['good'])
            {
                print "There are no valid tournament times between these dates.";
                die();
            }
            $this->load->view("admin/tournament_dates", $data_view);
        }
        
        public function add_tournament_dates()
        {
            $data = $_POST;
            $info = $this->admin_slots_model->calcTournamentDates($data);
            $this->admin_slots_model->addTournamentDates($data, $info['good']);
        }
        
        public function ajax_add_tournament()
        {
            $data = $_POST;
            $errors = array();
            
            //I usually do validation in the controller, but pushing it to the model because of the DB calls
            if(!$this->admin_slots_model->addSlotTournament($data, $errors))
            {
                print json_encode(array('success' => false, 'errors' => $errors));
                die();
            }
            
            print json_encode(array('success' => true));
            die();            
        }
}
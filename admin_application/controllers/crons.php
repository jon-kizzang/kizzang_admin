<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Crons extends CI_Controller {
        public function __construct()
	{
                set_time_limit (0); // Run forever
                ini_set('memory_limit','1024M'); 

                parent::__construct();	
                $this->load->model("admin_model");
                $this->load->model("admin_slots_model");
                $this->load->model("report_model");
                $this->load->model('signalone_model');
                $this->load->model('emercury_model');

                $this->load->helper('url');
	}
    
        public function update_right_signature()
        {
            $this->load->model('rightsig_model');
            $this->rightsig_model->getDocuments();
        }
    
         public function pick_all_winners()
         {
            return $this->admin_model->pickWinnersAll();
         }
         
         public function create_parlay_cards()
         {
             if(getenv('ENV') == "dev")
             {
                $this->load->model('sports_model');
                $ret = $this->sports_model->createParlayCards();
                return array_merge($ret, $this->sports_model->createROALCards());
             }
         }
         
         public function send_email_notifications()
         {
             return $this->report_model->sendNotificationEmails();
         }
         
         public function manage_chedda()
         {
             return $this->report_model->manageChedda();
         }
         
         public function update_fallout_stats()
         {
             return $this->report_model->aggregateImpressions();
         }
        
         public function process_winners()
         {
                return $this->admin_model->processPayments();                
         }
         
         public function add_bingo_games()
         {
             $this->load->model('bingo_model');
             return $this->bingo_model->addGames();
         }
         
         public function run_bingo_games()
         {
             $this->load->model('bingo_model');
             return $this->bingo_model->runGames();
         }
         
         public function run_bingo_game($id)
         {
             $this->load->model('bingo_model');
             return $this->bingo_model->runGame($id);
         }
         
         public function clear_tokens()
         {
                return $this->admin_model->clearTokens();                
         }
        
         public function clear_event_notifications()
         {
                return $this->admin_model->clearEventNotifications();
         }
         
         public function add_tournaments()
         { 
                return $this->admin_slots_model->addTournaments();
         }
         
         public function push_notification_crons()
         {
             return $this->signalone_model->processCrons();
         }
         
         public function update_player_tables()
         {
                return $this->report_model->updateDups();
         }
         
         public function update_conversions()
         {
                return $this->report_model->updateCampaignConversions();
         }
         
         public function archive_slot_tables()
         {
                return $this->admin_slots_model->archiveTables();
         }
         
         public function update_event_notifications()
         {
                return $this->report_model->updateEvents();
         }
         
         public function update_game_leader_board()
         {
                return $this->report_model->updateGameLeaderBoards();
         }
         
         public function update_retention()
         {
                return $this->report_model->retentionCreate();
         }
         
         public function geocode_people()
         {
                return $this->admin_model->geoCodePlayers();
         }
         
         public function archive_tickets()
         {
                //return $this->admin_model->archiveTickets();             
         }

         public function update_signalone_info()
         {
                return $this->signalone_model->updateDB();
         }
         
         public function update_emercury_info()
         {            
                return $this->emercury_model->updateDB();
         }
         
         public function update_cron_schedule()
         {
                return $this->admin_model->createCronSchedule();
         }
         
         public function send_parlay_emails()
         {
                return $this->admin_model->sendParlayEmails();
         }
         
         public function send_roal_emails()
         {
            $this->load->model('sports_model');
            return $this->sports_model->sendROALEmails();
         }
         
         public function send_pro_football_email_insurance()
         {
             $this->load->model('sports_model');
             return $this->sports_model->sendPFEmailInsurance();
         }
         
         public function update_parlay_mail_dates()
         {
             return $this->admin_model->updateParlayEmails();
         }
         
         public function process()
         {
                $commands = $this->admin_model->processCrons();
                if(!count($commands))
                {
                    print "0"; 
                    die();
                }

                $controllers = array();

                foreach($commands as $command)
                {
                     $link = explode("/", $command->link);
                     if(count($link) != 2)
                         continue;

                     $controller = $link[0];
                     $action = $link[1];
                     
                     $this->admin_model->changeCronStatus($command->id, 'Running');

                     if($controller == "crons")
                     {
                         $ret = $this->$action();
                     }
                     else
                     {
                         if(!in_array($controller, $controllers))
                         {
                             $this->load->controller($controller);
                             $controllers[] = $controller;
                         }

                         $ret = $this->$controller->$action();
                     }
                     if(isset($ret['success']) && $ret['success'])
                         $success = true;
                     else
                         $success = false;
                     
                     $this->admin_model->changeCronStatus($command->id, 'Complete');
                     $data = array('cron_schedule_id' => $command->id,
                        'cron_id' => $command->cron_id,
                        'status' => $success ? 'Success' : 'Fail',
                         'return_value' => json_encode($ret));
                     
                     $this->admin_model->logCronRun($data);
                }
                print "0"; 
                die();
         }
}
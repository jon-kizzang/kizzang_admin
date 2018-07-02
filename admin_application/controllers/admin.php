<?php

class Admin extends CI_Controller
{
	public function __construct()
	{
		//set_time_limit (0); // Run forever
               ini_set('upload_max_filesize', 20000000);   
               ini_set('post_max_size', '128M');
               ini_set('max_execution_time', 0);
		
		parent::__construct();
		
		$this->load->model('scratch_admin_model');
               $this->load->model('admin_model');
		
		$this->load->helper('url');							// This gives access to echo'ing base_url
	}            
    
       public function clearEventNotifications()
       {
           print $this->admin_model->clearEventNotifications();
           die(); 
       }
       
       public function games()
       {
            $data['games'] = $this->admin_model->getBBGames();
            $layout_data['content'] = $this->load->view("admin/view_all_games", $data, true);
            $layout_data['page'] = "ViewBBGame";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function add_game($id = NULL)
       {
           $data = $this->admin_model->getBBGame($id);
            $layout_data['content'] = $this->load->view("admin/add_bb_game", $data, true);
            $layout_data['page'] = "AddBBGame";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function ajax_add_game()
       {
           print json_encode($this->admin_model->saveGame($_POST));
           die();
       }
       
       //  DB CONFIG SECTION -----------------------------------------------------------
       public function configs()
       {
            $data = $this->admin_model->getAllConfigs();
            $layout_data['content'] = $this->load->view("admin/view_db_configs", $data, true);
            $layout_data['page'] = "ViewDBConfigs";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function force_logout($id)
       {
           print json_encode($this->admin_model->forceLogout($id));
           die();
       }
       
       public function add_config($id = NULL)
       {
            $data = $this->admin_model->getDBConfig($id);
            $layout_data['content'] = $this->load->view("admin/add_db_config", $data, true);
            $layout_data['page'] = "ViewDBConfigs";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function ajax_add_db_config()
       {
           $data = $_POST;
           print json_encode(array('success' => $this->admin_model->addDBConfig($data)));
           die();
       }
       
       public function ajax_get_config_db_info()
       {
           $data = $_POST;
           if($data['data_type'] == "Numeric")
           {
               $ret = array('success' => 1, 'html' => '<div class="form-group" id="div_data_type">
                    <label for="Name">Add Number</label>
                    <input type="text" class="form-control" name="info" value=""/>
                </div>  ');
               print json_encode($ret);
               die();
           }
           
           if($data['data_type'] == "Text")
           {
               $ret = array('success' => 1, 'html' => '<div class="form-group" id="div_data_type">
                    <label for="Name">Add Text</label>
                    <textarea type="text" class="form-control" name="info"></textarea>
                </div>  ');
               print json_encode($ret);
               die();
           }
           
           if($data['data_type'] == "JSON")
           {
               $ret = array('success' => 1, 'html' => '<div class="form-group" id="div_data_type">
                    <label for="Name">Add JSON</label>
                    <textarea type="text" class="form-control" name="info"></textarea>
                </div>  ');
               print json_encode($ret);
               die();
           }
           
           $view = strtolower($data['main_type'] . "_" . $data['sub_type'] . "_" . $data['action']);
           $html = $this->load->view("admin/configs/" . $view, array(), true);
           if($html)
           {
               print json_encode(array('success' => 1, 'html' => $html));
               die();
           }
           print json_encode(array('success' => 0, 'html' => ''));
           die();
       }
       
       //TESTIMONTIALS SECTION ---------------------------------------------------
       public function view_store_items()
       {
            $data = $this->admin_model->getStoreItems();
            $layout_data['content'] = $this->load->view("admin/view_store_items", $data, true);
            $layout_data['page'] = "ViewStoreItems";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function add_store_item($id = NULL)
       {           
           $data = $this->admin_model->getStoreItem($id);                      
            $layout_data['content'] = $this->load->view("admin/add_store_item", $data, true);
            $layout_data['page'] = "AddStoreItem";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function ajax_add_store_item()
       {
           print json_encode(array('success' => $this->admin_model->addStoreItem($_POST)));
           die();
       }
       
       //TESTIMONTIALS SECTION ---------------------------------------------------
       public function view_testimonials()
       {
            $data = $this->admin_model->getTestimonials();
            $layout_data['content'] = $this->load->view("admin/view_testimonials", $data, true);
            $layout_data['page'] = "ViewTestimonials";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function add_testimonial($id = NULL)
       {           
           $data = $this->admin_model->getTestimonial($id);                      
            $layout_data['content'] = $this->load->view("admin/add_testimonial", $data, true);
            $layout_data['page'] = "AddTestimonial";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function ajax_add_testimonial()
       {
           print json_encode(array('success' => $this->admin_model->addTestimonial($_POST)));
           die();
       }
       
       //PAYOUTS SECTION ----------------------------------------------------------------
       public function view_game_payouts()
       {
            $data = $this->admin_model->getGamePayouts();
            $layout_data['content'] = $this->load->view("admin/view_game_payouts", $data, true);
            $layout_data['page'] = "ViewPayouts";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function add_game_payout($type = "")
       {
           $type = urldecode($type);
           $data = $this->admin_model->getGamePayoutType($type);
           $data['currentGameType'] = $type;
           $data['tbl'] = $this->load->view("admin/game_payout_tbl", $data, true);
            $layout_data['content'] = $this->load->view("admin/add_game_payout", $data, true);
            $layout_data['page'] = "AddPayouts";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function ajax_add_game_payout()
       {
           print json_encode(array('success' => $this->admin_model->addGamePayout($_POST)));
           die();
       }
       
       public function ajax_get_game_payouts($type)
       {
           $data = $this->admin_model->getGamePayoutType($type);
           print $this->load->view("admin/game_payout_tbl", $data, true);
           die();
       }
       
       //CRON JOB SECTION ---------------------------------------------------------------
       public function cron()
       {
            $data = $this->admin_model->getCrons();
            $layout_data['content'] = $this->load->view("admin/view_crons", $data, true);
            $layout_data['page'] = "ViewCrons";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function add_cron($id = NULL)
       {           
            $data = $this->admin_model->getCron($id);          
            $layout_data['content'] = $this->load->view("admin/add_cron", $data, true);
            $layout_data['page'] = "AddCron";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function add_pn_cron($id)
       {
            $months = array(0 => "*", 1 => "Jan", 2 => "Feb", 3 => "Mar", 4 => "Apr", 5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Aug", 9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dec");
            
            $days_of_week = array(0 => "*");
            for($i = 0; $i < 7; $i++)
                $days_of_week[$i + 1] = jddayofweek ($i, 1);
            
            $data = compact('id','months','days_of_week');
            $this->load->view("admin/add_pn_cron", $data);
       }
       
       public function ajax_add_pn_cron()
       {
            $data = $_POST;           
            print $this->admin_model->addPnCron($data);
            die();
       }
       
       public function ajax_add_cron()
       {
           print json_encode(array('success' => $this->admin_model->addCron($_POST)));
           die();
       }
       
       public function ajax_update_cron_schedule()
       {
           $this->admin_model->createCronSchedule();
       }
       
       public function ajax_cron_history($id)
       {
           $data = $this->admin_model->getCronHistory($id);
           $this->load->view("admin/view_cron_history", $data);
       }
       
       public function cron_schedule()
       {
            $data['crons'] = $this->admin_model->getCronSchedule();
            $layout_data['content'] = $this->load->view("admin/view_cron_schedule", $data, true);
            $layout_data['page'] = "ViewCronSchedule";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function ajax_cron_sched_active($id)
       {
           print json_encode(array('success' => $this->admin_model->switchCronStatus($id)));
           die();
       }
       
       public function process_crons()
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
                
                if($controller == "admin")
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
           }
       }       
       
       //PUSH NOTIFICATION SECTION -------------------------------------------
       public function view_notifications()
       {
            $this->load->model('signalone_model');
            $data['pns'] = $this->signalone_model->getNotificationQueue();
            $layout_data['content'] = $this->load->view("admin/view_notification_queue", $data, true);
            $layout_data['page'] = "ViewNotifications";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function view_notification_history()
       {
            $this->load->model('signalone_model');
            $data['pns'] = $this->signalone_model->getNotificationHistory();
            $layout_data['content'] = $this->load->view("admin/view_notifications", $data, true);
            $layout_data['page'] = "ViewNotificationHistory";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function add_notifications($id = NULL)
       {
            $this->load->model('signalone_model');
            $data = $this->signalone_model->getNotificationInfo($id);
            //print_r($data['pn']); die();
            $layout_data['content'] = $this->load->view("admin/add_notification", $data, true);
            $layout_data['page'] = "AddNotifications";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function execute_pn_queue()
       {
           $this->load->model('signalone_model');
           $this->signalone_model->runQueue();
           die();
       }
       
       public function ajax_delete_pn($id)
       {
           $this->load->model('signalone_model');
           $this->signalone_model->deleteFromQueue($id);
           die();
       }
       
       public function ajax_add_notifications()
       {
           $this->load->model('signalone_model');
           $success = $this->signalone_model->addPushNotification($_POST, $ret);
           print json_encode(array('success' => $success, 'message' => $ret));
           die();
       }
       
       public function ajax_add_notification_queue()
       {
           $this->load->model('signalone_model');
           $success = $this->signalone_model->addPushNotification($_POST, $ret, true);
           print json_encode(array('success' => $success, 'message' => $ret));
           die();
       }

       public function add_notification_filter($id)
       {
            $this->load->model('signalone_model');
            $data = $this->signalone_model->getNotificationInfo();
            $data['id'] = $id;
            print $this->load->view("admin/add_notification_filter", $data, true);
            die();
       }

       public function ajax_update_pns()
       {
           $this->load->model('signalone_model');
           print $this->signalone_model->updateDB();
           die();
       }
       
       public function addTournaments()
       {
           $this->load->model('admin_slots_model');
           $ret = $this->admin_slots_model->addTournaments();
           if(!$ret)
               $ret = $this->admin_model->archiveTickets();
           
           if(!$ret)
               $ret = $this->admin_model->clearEventNotifications();
           
           print $ret;
           die();
       }
       
       public function transfer_files()
       {
           $this->admin_model->transferFiles();
       }
       
       public function compile_wins($type, $id)
       {
           header("Content-type: text/x-csv");
           header("Content-Disposition: attachment; filename=" . $type . "_" . $id . "_wins.csv");
           print $this->admin_model->compileWins($type, $id);
           die();
       }
       
       public function cloudfront()
       {                   
           //$this->admin_model->invalidateCloudfrontFiles('kizzang-legal', array('/_dev/game_rules/KP00023.txt'));
           if(isset($_POST['bucket']))
               $bucket = $_POST['bucket'];
           else
               $bucket = 'kizzang-legal';
           $layout_data['page'] = "Cloudfront";           
           $data = $this->admin_model->getCloudfrontFiles($bucket);
           $data['cur_bucket'] = $bucket;
           $layout_data['content'] = $this->load->view("admin/cloudfront", $data, true);           
           $this->load->view("layouts/admin", $layout_data);
       }
       
       public function processDSEmails($date = NULL)
       {
           if(!$date)
               $date = date("Y-m-d");
           $this->admin_model->sendParlayEmails($date);
       }
       
       public function test()
       {
           $mainapi = json_decode('[{  "name": "APP_VERSION_NUMBER",  "value": "7.0.2"},{  "name": "ENV",  "value": "dev"},{  "name": "MAIN_API_URL",  "value": "https://devapi.kizzang.com"},{  "name": "MAINDB",  "value": "kizzang"},{  "name": "MAINHOST",  "value": "dev-main.camw8exvgwjh.us-east-1.rds.amazonaws.com"},{  "name": "MAINPASSWORD",  "value": "R4ia6wXooqBd"},{  "name": "MAINUSER",  "value": "kdevadmin"},{  "name": "REDIS_HOST",  "value": "dev-scratch-red.p2nejy.0001.use1.cache.amazonaws.com"},{  "name": "REDIS_PORT",  "value": "6379"},{  "name": "REST_VERSION_NUMBER",  "value": "1.0"},{  "name": "SCRATCHDB",  "value": "ebdb"},{  "name": "SCRATCHHOST",  "value": "dev-scratcher.camw8exvgwjh.us-east-1.rds.amazonaws.com"},{  "name": "SCRATCHPASSWORD",  "value": "K1zz4ng!"},{  "name": "SCRATCHUSER",  "value": "ebroot"},{  "name": "SLOT_API_URL",  "value": "https://devslot.kizzang.com"},{  "name": "SWF_SERVER_NAME",  "value": "dev.kizzang.com"},{  "name": "TWILIO_ACCOUNT_SID",  "value": "AC0d152bf5bb26cba86c9cd4eda3a0fe94"},{  "name": "TWILIO_API_VERSION",  "value": "2010-04-01"},{  "name": "TWILIO_AUTH_TOKEN",  "value": "bed74417f27145d2f0ed12ab87216a7e"},{  "name": "TWILIO_MODE",  "value": "prod"},{  "name": "TWILIO_PHONE_NUMBER",  "value": "+17025000832"}]');
           $admin = json_decode(' [{"name": "ADMINPASSWORD","value": "123456789"},{"name": "APIKEY","value": "X-API-KEY: 00d726b30a1bf7169357c56b92753b80"},{"name": "APISERVER","value": "http://local.chefapi.com/"},{"name": "AWS_ACCESS_KEY_ID","value": "AKIAJ63XKFGUBAT7OROA"},{"name": "AWS_SECRET_KEY","value": "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"},{"name": "BACKUP","value": "restore-main-2016-05-27.camw8exvgwjh.us-east-1.rds.amazonaws.com"},{"name": "ENV","value": "dev"},{"name": "GOOGLEGEOCODINGAPI","value": "AIzaSyAbPCHbvnAp3ux5P-JCQeLSBb0FMAVQmsI"},{"name": "MAINDB","value": "kizzang"},{"name": "MAINHOST","value": "dev-main.camw8exvgwjh.us-east-1.rds.amazonaws.com"},{"name": "MAINPASSWORD","value": "R4ia6wXooqBd"},{"name": "MAINUSER","value": "kdevadmin"},{"name": "OAUTH_CONSUMER_KEY","value": "qyprd1FpcNzBe9q0vSzagorUnSb1fX"},{"name": "OAUTH_CONSUMER_SECRET","value": "7wpOb7xc1h0OeeTMJSq8VoFafFXH1GoLQjnhlASw"},{"name": "OAUTH_QUICKBOOKS_TOKEN","value": "b7d79abdb9021b4c51b8be8b8d670b5468bc"},{"name": "PAYPAL_API_CLIENT_ID","value": "Aa9UvY7sxN-hSkvxO6zQVdd83p78ajZ402t93dnJ11Rkt4DqF77eUjFKvTBEzf5Taom1QXl0Tk4hDB98"},{"name": "PAYPAL_API_MODE","value": "sandbox"},{"name": "PAYPAL_API_SECRET","value": "EOs3VI-YKzMUsPHyaZRKlhfOdSPwLVfw0hXgTUjyXQ9pdGn18kZvoyWwY8Gn2dPUHGcp6XYuBbgatmCc"},{"name": "REDISSCRATCHSERVER","value": "dev-scratch-redis.p2nejy.0001.use1.cache.amazonaws.com"},{"name": "REDISSLOTSERVER","value": "kizzang-dev-slot.p2nejy.ng.0001.use1.cache.amazonaws.com"},{"name": "RIGHT_SIGNATURE_TOKEN","value": "api-token: w7Wsz8oBOQN3bYos4LTj1Yy4j9iilv3qrgc1UFO5"},{"name": "RIGHT_SIGNATURE_URL","value": "https://rightsignature.com"},{"name": "SCRATCHDB","value": "ebdb"},{"name": "SCRATCHHOST","value": "dev-scratcher.camw8exvgwjh.us-east-1.rds.amazonaws.com"},{"name": "SCRATCHPASSWORD","value": "K1zz4ng!"},{"name": "SCRATCHUSER","value": "ebroot"},{"name": "SIGNALONEKEY","value": "aecc4c32-ea39-4645-a3d1-b3fffeb1bb25"},{"name": "SLOTDB","value": "kizzangslot"},{"name": "SLOTHOST","value": "dev-slot.camw8exvgwjh.us-east-1.rds.amazonaws.com"},{"name": "SLOTPASSWORD","value": "LjU4JPfNf9Fs"},{"name": "SLOTUSER","value": "ksdevslot"}      ]');
           $array = $admin;
           foreach($array as $row)
           {
               print "-e " . $row->name . "='" . $row->value . "' ";
           }
       }
       
       public function ajax_get_cloudfront_data()
       {
           
       }
       
       public function ajax_invalidate_cloudfront_file()
       {
           $data = $_POST;
           if(!isset($data['bucket']) || !isset($data['paths']))
               die();
           
           $this->admin_model->invalidateCloudfrontFiles($data['bucket'], $data['paths']);
           print json_encode(array('success' => true));
           die();
       }
       
       public function query()
       {        
           $data = $_POST;
           if(!isset($data['query']))
           {
                $layout_data['content'] = $this->load->view("admin/query", array('cols' => array(), 'rows' => array(), 'query' => '', 'db' => 'main', 'out' => 'print', 'message' => ''), true);
           }
           else
           {
                $info = $this->admin_model->query($data);
                if(!is_array($info))
                    $info = array('message' => $info, 'cols' => array(), 'rows' => array(), 'query' => $data['query'], 'db' => $data['db'], 'out' => $data['out']);
                if($data['out'] == "print")
                    $layout_data['content'] = $this->load->view("admin/query", $info, true);
                elseif($data['out'] == "csv")
                {
                    header("Content-type: text/x-csv");
                    header("Content-Disposition: attachment; filename=search_results.csv");
                    print $this->load->view("admin/query_csv", $info, true);
                    die();
                }
                else
                {
                    $matches = array();
                    if(preg_match_all("/from\s([0-9a-z\._]+)/i", $data['query'], $matches))
                    {
                        $info['table'] = $matches[1][0];                        
                        header("Content-Type: application/octet-stream");
                        header("Content-Disposition: attachment; filename=" . $info['table'] . ".sql");                        
                    }                    

                    print $this->load->view("admin/query_sql", $info, true);
                    die();
                }
           }
            $layout_data['page'] = "Query";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function mem_redis()
       {
           //$playerId = 107;
           //print "KEY-playperiod-current-playerId-$playerId-" . md5( $playerId );
           $data = $this->admin_model->getMemRedis();
            $layout_data['content'] = $this->load->view("admin/mem_redis", $data, true);
            $layout_data['page'] = "MemRedis";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function ajax_memcache_player()
       {
           if(!isset($_POST['id']))
               die();
           
           print $this->admin_model->memcachePlayer($_POST['id']);
           die();
       }
       
       public function ajax_memcache_check()
       {
           $data = $_POST;
           if(isset($data['type']))
               print $this->admin_model->memcacheScratcher($data['key']);
           else
               print $this->admin_model->memcacheCheck($data['key']);
           die();
       }
       
       public function ajax_memcache_delete()
       {
           $data = $_POST;
           print $this->admin_model->memcacheDelete($data['key']);
           die();
       }
       
       public function ajax_memcache_local_delete()
       {
           $data = $_POST;
           print json_encode(array('success' => $this->admin_model->memcacheDeleteLocal($data['key'])));
           die();
       }
       
       public function ajax_memcache_local_get()
       {
           $data = $_POST;
           $value = $this->admin_model->memcacheGetLocal($data['key']);
           if($value)
                print json_encode(array('success' => true, 'message' => $value));
           else
                print json_encode(array('success' => false, 'message' => $value));
           die();
       }
       
       public function ajax_redis_check()
       {
           $data = $_POST;
           if(!isset($data['server']) || !isset($data['query']))
               die();
           
           print json_encode(array('success' => $this->admin_model->redisCheck($data['server'], $data['query'])));
       }
       
       public function ajax_redis_delete()
       {
           
       }
       
       public function ajax_query()
       {
           $data = $_POST;
           $message = "";
           $rs = $this->admin_model->query($data, $message);
           if($data['out'] == "print")
               print $message;
           else
           {
               header("Content-type: text/x-csv");
               header("Content-Disposition: attachment; filename=search_results.csv");
               print $message;
           }
           die();
       }
        public function login()
        {
            $data = $_POST;
            $acls = $this->nativesession->get('ACLs');
            if($acls)
            {
                redirect("/admin");
                exit();
            }
            $message = "";
            $log_status = 1; //No Action
            if($data)
            {
                if($this->admin_model->verifyLogin($data, $message))
                {                    
                        redirect ("/admin");  
                        exit();
                }
                else
                {
                    $log_status = 0;                    
                }
            }            
            $this->load->view("admin/login", compact('log_status'));                        
        }
        
        public function change_password()
        {
            $password = $_POST['password'];
            $message = "";
            $ret = $this->admin_model->changePassword($password, $message);
            print json_encode(array('success' => $ret, 'message' => $message));
            die();
        }
        
        public function bingo_games()
        {
            $this->load->model('bingo_model');
            $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : date('Y-m-d');
            $endDate = isset($_POST['endDate']) ? $_POST['endDate'] : date('Y-m-d');
            $data = $this->bingo_model->getBingoGames($startDate, $endDate);
            $layout_data['content'] = $this->load->view("admin/bingo/view_games", $data, true);
            $layout_data['page'] = "ViewBingos";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function add_bingo_game($id = 0)
        {
            $this->load->model('bingo_model');
            $data = $this->bingo_model->getBingoGame($id);
            $layout_data['content'] = $this->load->view("admin/bingo/add_game", $data, true);
            $layout_data['page'] = "AddBingo";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function ajax_save_bingo_game()
        {
            $this->load->model('bingo_model');
            $ret = $this->bingo_model->updateBingoGame($_POST, $error);
            print json_encode(array('success' => $ret, 'error' => $error));
            die();
        }
        
        public function view_bingo_cards($id)
        {
            $this->load->model('bingo_model');
            $this->load->view("admin/bingo/view_cards", $this->bingo_model->viewCards($id));
        }
        
        public function logout()
        {
            $this->admin_model->logout();
            redirect("/admin/login");
        }
        
        public function mainReport()
        {
            $this->load->model("report_model");
            if(isset($_POST['date_select']))
                $date = $_POST['date_select'];
            else
                $date = date('Y-m-d', strtotime('now'));            
            
            //$this->report_model->updateEvents(false);
            $data = $this->report_model->getStats($date);
            $data['main'] = 1;
            $data['redirect'] = $this->nativesession->get("redirect");
            $data['mess'] = $this->nativesession->get("access_error");
            $layout_data['content'] = $this->load->view("admin/mainReport", $data, true);
            $layout_data['page'] = "MainReport";
            $this->load->view("layouts/admin", $layout_data);
        }
    
        public function index()
        {                   
            $this->load->model("report_model");
            $data = $this->report_model->getDashboardInfo();
            $init = array('userType' => 'All', 'mobileType' => 'All', 'loginType' => 'All', 'loginSource' => 'All', 'startDate' => $data['startDate'], 'endDate' => $data['endDate']);
            $temp = $this->report_model->getDashboardData($init);
            $data = array_merge($data, $temp);
            $layout_data['content'] = $this->load->view("admin/index", $data, true);
            $layout_data['page'] = "Home";
            $this->load->view("layouts/admin", $layout_data);           
        }
        
        public function ajax_dashboard()
        {
            $this->load->model("report_model");
            $temp = $this->report_model->getDashboardData($_POST);
            print json_encode($temp);
            die();
        }
        
        public function update_db_source($name)
        {
            if($name != "dev" && $name != "QA" && $name != "prod")
            {
                print json_encode(array('success' => false));                
                die();
            }
            $this->nativesession->set('DB', $name);            
            print json_encode(array('success' => true));
            //die();
        }
        
        public function ajax_process_payment($id)
        {          
            $message = "";
            $status = $this->admin_model->processPayment($id, $message);
            print json_encode(array('success' => $status, 'message' => $message));
            die();
        }
        
        public function ajax_process_all()
        {            
            $status = $this->admin_model->processPayments();
            print json_encode(array('success' => $status));
            die();
        }
        
        public function ajax_update_winodometer()
        {
            $value = $_POST['winodometer'];
            if(is_numeric($value))
                print json_encode(array('success' =>$this->admin_model->updateWinodometer($value)));
            else
                print json_encode (array('success' => false));
            die();
        }
        
        public function get_parlay_winners($id)
        {
            $data = $this->admin_model->getParlayWinners($id);
            $this->load->view("admin/sports/view_winners", $data);
        }
        
        public function view_winners($type = "Claimed")
        {           
            $data['type'] = $type;
            $data['winners'] = $this->admin_model->getWinners($type);
            $layout_data['content'] = $this->load->view("admin/view_winners", $data, true);
            $layout_data['page'] = "ViewWinners";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function manual_winners()
        {
            $data['types'] = array('scratchCard', 'slotTournament', 'sweepstakes', 'dailyShowdown', 'finalThree', 'bigGame');
            $layout_data['content'] = $this->load->view("admin/manual_winners", $data, true);
            $layout_data['page'] = "ManualWinners";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function ajax_add_manual_payment()
        {            
            $errors = "";
            $ret['status'] = $this->admin_model->add_manual_winner($_POST, $errors);
            $ret['errors'] = $errors;
            print json_encode($ret);
            die();
        }
        
        public function ajax_get_player_name($id)
        {
            print $this->admin_model->get_player_name($id);
            die();
        }
        
        public function validate_winner($id)
        {           
            $data = $this->admin_model->validateWinner($id);
            $layout_data['content'] = $this->load->view("admin/add_validate_winner", $data, true);
            $layout_data['page'] = "ValidateWinner";
            $this->load->view("layouts/admin", $layout_data);
        }
                
        public function ajax_add_validate_winner()
        {            
            print json_encode(array('success' => $this->admin_model->addValidatedWinner($_POST)));
            die();
        }
        
        public function ajax_update_winner_docs($id)
        {
            $this->load->model("rightsig_model");
            $queryString = "state=completed&per_page=50&page=1&tags=user_id:$id";
            $this->rightsig_model->getDocuments($queryString);
            print json_encode(array('success' => true));
            die();
        }
        
        public function view_roals()
        {
            $data = $this->admin_model->viewROALs();
            $layout_data['content'] = $this->load->view("admin/view_roals", $data, true);
            $layout_data['page'] = "ViewROAL";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function edit_roal($id = NULL)
        {
            $data = $this->admin_model->getROAL($id);
            $layout_data['content'] = $this->load->view("admin/add_roal", $data, true);
            $layout_data['page'] = "AddROAL";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function ajax_add_roal()
        {
            print $this->admin_model->addROAL($_POST);
            die();
        }
        
        public function ajax_add_roal_answer()
        {
            print json_encode(array('success' => $this->admin_model->updateROALQuestion($_POST)));
            die();
        }
        
        public function ajax_grade_roal($id)
        {
            print json_encode(array('success' => $this->admin_model->gradeROALQuestions($id)));
            die();
        }
        
        public function add_roal_event($id)
        {           
           $data = $this->admin_model->getROALEvents($id);
           $data['id'] = $id;
           $this->load->view("admin/add_roal_event", $data);
       }
       
       public function add_event_roal()
       {
           $data = $_POST;
           print $this->admin_model->addEventROAL($data);
           die();
       }
       
       public function ajax_delete_roal_event($id)
       {
           print json_encode(array('success' => $this->admin_model->deleteEventROAL($id)));
           die();
       }
       
       public function search_roal_events()
       {
           $data['events'] = $this->admin_model->searchParlayEvents($_POST);
           $this->load->view("admin/sports/parlay_event_table",$data);
       }
        
        public function leaderboard()
        {
            $data['winners'] = $this->admin_model->getLeaderBoard();            
            $layout_data['content'] = $this->load->view("admin/view_leaderboard", $data, true);
            $layout_data['page'] = "LeaderBoard";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function ajax_delete_lb_entry()
        {
            if(!isset($_POST['id']))
                die();
            
            print json_encode(array('success' => $this->admin_model->deleteLeaderBoardEntry($_POST['id'])));
            die();
        }
        
        public function view_paid_winners()
        {
            $data['winners'] = $this->admin_model->getPastWinners(date('Y-m-d'));            
            $layout_data['content'] = $this->load->view("admin/view_paid_winners", $data, true);
            $layout_data['page'] = "ViewPaidWinners";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function ajax_update_leaderboard()
        {
            print json_encode(array('success' => $this->admin_model->updateLeaderBoard()));
            die();
        }
        
        public function ajax_process_sweepstakes()
        {
            $data = $_POST;
            if(!isset($data['id']) || !isset($data['text']))
                die();
            
            $this->admin_model->updateSweepstakesWinner($data);
            print json_encode(array('success' => true));
            die();
        }
        
        public function pick_winners($type = "Sweepstakes")
        {
            $data['winners'] = $this->admin_model->pickWinners($type);
            $view = "";
            switch($type)
            {
                case "Sweepstakes": $view = "admin/tbl_sweepstakes"; break;
                case "Slots": $view = "admin/tbl_slots"; break;
                case "Parlay": $view = "admin/tbl_parlay"; break;
                case "BG": $view = "admin/tbl_bg"; break;
                case "FT": $view = "admin/tbl_ft"; break;
                case "Lottery": $view = "admin/tbl_lottery"; break;
            }
            $layout_data['content'] = $this->load->view($view, $data, true);
            $layout_data['page'] = "PickWinners";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        
        public function ajax_pick_winner()
        {
            $data = $_POST;
            if(!isset($data['type']) || !isset($data['id']))
            {
                print json_encode(array('success' => false, 'message' => 'Invalid Request'));
                die();
            }
            
            $message = "Type Not Defined";
            if($data['type'] == "Sweepstakes")
            {
                if(!$this->admin_model->pickSweepstakes($data, $message))
                {
                    print json_encode(array('success' => false, 'message' => $message));
                    die();
                }
            }
            elseif($data['type'] == "Slots")
            {
                if(!$this->admin_model->pickSlots($data, $message))
                {
                    print json_encode(array('success' => false, 'message' => $message));
                    die();
                }
            }
            elseif($data['type'] == "Parlay")
            {
                if(!$this->admin_model->pickParlay($data, $message))
                {
                    print json_encode(array('success' => false, 'message' => $message));
                    die();
                }
            }
            elseif($data['type'] == "BG")
            {
                if(!$this->admin_model->pickBG($data, $message))
                {
                    print json_encode(array('success' => false, 'message' => $message));
                    die();
                }
            }
            elseif($data['type'] == "FT")
            {
                if(!$this->admin_model->pickFT($data, $message))
                {
                    print json_encode(array('success' => false, 'message' => $message));
                    die();
                }
            }
            elseif($data['type'] == "Lottery")
            {
                if(!$this->admin_model->pickLottery($data, $message))
                {
                    print json_encode(array('success' => false, 'message' => $message));
                    die();
                }
            }
            
            print json_encode(array('success' => true, 'message' => $message));
            die();
        }
                
	public function view_games()
        {
                $data['games'] = $this->scratch_admin_model->getGames();
                //print_r($temp); die();
                $layout_data['content'] = $this->load->view("admin/view_games", $data, true);
                $layout_data['page'] = "ViewGame";
                $this->load->view("layouts/admin", $layout_data);
        }
        
        /*public function view_scratcher_futures()
        {
            $data = $this->scratch_admin_model->getFutureWinners();
            //print_r($data); die();
            $layout_data['content'] = $this->load->view("admin/view_scratcher_futures", $data, true);
            $layout_data['page'] = "ViewGame";
            $this->load->view("layouts/admin", $layout_data);
        }*/

        public function edit_game($id = NULL)
        {
                $data = array('game' => NULL, 'payouts' => NULL, 'cards' => NULL, 'rule' => NULL);
                if($id)
                        $data = $this->scratch_admin_model->getGame($id);

                $layout_data['content'] = $this->load->view("admin/add_games", $data, true);
                $layout_data['page'] = "EditGame";
                $this->load->view("layouts/admin", $layout_data);
        }
        
        public function view_configs()
        {            
            if(isset($_POST['base']))
                $base = $_POST['base'];
            else
                $base = "_dev/0/main-app/global";
            
            if(isset($_POST['bucket']))
                $bucket = $_POST['bucket'];
            else
                $bucket = 'kizzang-resources';
            $data['configs'] = $this->admin_model->getConfigs($base);
            $data['base'] = $base;
            $data['bucket'] = $bucket;
            $layout_data['content'] = $this->load->view("admin/view_configs", $data, true);
            $layout_data['page'] = "ViewConfigs";
            $this->load->view("layouts/admin", $layout_data);
        }
                
        public function ajax_get_config()
        {
            $data = $_POST;
            if(!isset($data['file']) || !isset($data['bucket']))
                die();
            print $this->admin_model->getConfigFile($data['file'], $data['bucket']);
            die();
        }
        
        public function ajax_save_config()
        {
            $data = $_POST;
            if(!isset($data['bucket']) || !isset($data['file']) || !isset($data['text']))
                die();
            
            $message = "";            
            $ret = $this->admin_model->saveConfig($data, $message);
            print json_encode(array('success' => $ret, 'message' => $message));
            die();
        }
        
        public function ajax_get_payout($id)
        {
            $data = $this->scratch_admin_model->getPayoutInfo($id);
            $this->load->view("admin/payout_table", $data);
        }

        public function ajax_add_games()
        {
                $game = $_POST;
                $errors = array();
                if(!$this->validate_game($game, $errors))
                {
                        print  json_encode (array('success' => false, 'errors' => $errors));
                        die();
                }                
                print json_encode(array('success' => true, 'id' => $this->scratch_admin_model->alterGame($game)));
                die();
        }
        
        public function clone_payout($ID)
        {
                $data = $this->scratch_admin_model->clonePayout($ID);
                $this->load->view("admin/clone_payout", $data);
        }
        
        public function ajax_clone_payouts()
        {
            $data = $_POST;
            $this->scratch_admin_model->clonePayoutInsert($data);
            print json_encode(array('success' => true));
            die();
        }

        public function add_payout($game_id, $payout_id = NULL)
        {
                $data = $this->scratch_admin_model->getPayout($game_id, $payout_id);
                //print_r($data); die();
                $this->load->view("admin/add_payout", $data);
        }

        public function ajax_increment_cards($id)
        {
            $message = "";
            $result = $this->scratch_admin_model->incrementCards($id, $message);
                print json_encode(array('success' => $result, 'message' => $message));
                die();
        }

        public function ajax_add_payouts()
        {
                $data = $_POST;
                if(!$this->validate_payout($data, $errors))
                {
                        print json_encode (array('success' => false, 'errors' => $errors));
                        die();
                }
                $this->scratch_admin_model->alterPayout($data);
                print json_encode(array('success' => true));
                die();
        }

        public function ajax_delete_payouts($id)
        {
                //First Check to see if there are records attached to the payout in the WinConfirmations table.  If so, don't allow deletion
                $count = $this->scratch_admin_model->getFindWinCards($id);
                if(!$count)
                {
                        print json_encode(array('success' => $this->scratch_admin_model->delete('Scratch_GPPayout', 'KeyID', $id)));
                        die();
                }
                else
                {
                        print json_encode(array('success' => false, 'error' => "This can't be deleted because it has $count winners"));
                        die();
                }
        }

        private function validate_payout($data, &$errors)
        {
                foreach($data as $key => $value)
                {
                        switch($key)
                        {
                                case 'PrizeAmount':
                                case 'TaxableAmount':
                                case 'Weight':
                                        if(!is_numeric($value))
                                                $errors[$key] = "Field is Required and must be Numeric";
                                        break;
                                case 'PrizeName':
                                        if(!$value)
                                                $errors[$key] = "This is Required";
                                        break;
                        }
                }
                if(count($errors))
                        return false;
                return true;
        }
        
        public function config_file_upload()
        {
            $this->load->library('s3');
            $bucket = "kizzang-resources";
            
            $data = $_POST;
            $file = $_FILES['file'];
                        
            if(isset($file['name']) && isset($data['url']))
            {                
                $this->s3->putObjectFile($file['tmp_name'], $bucket, $data['url'] . "/" . $file['name'], 'public-read');
                print "Uploaded File"; die();
            }
            print "Error"; die();
        }
        
        public function file_upload($print = true, $data = NULL)
        {
            $this->load->library('s3');
            $cloudfronts = array('kizzang-campaigns' => "https://d1vksrhd974otw.cloudfront.net/", "kizzang-legal" => "https://d23kds0bwk71uo.cloudfront.net/");
            if(!$data)
                $data = $_POST;
            
            $file = $_FILES['file'];
  
            if(isset($data['bucket']))
                $bucket = $data['bucket'];
            else
                $bucket = "kizzang-resources-sweepstakes";
            
            if(isset($cloudfronts[$bucket]))
                $base_url = $cloudfronts[$bucket];
            else
                $base_url = "https://$bucket.s3.amazonaws.com/";
            
            if(isset($file['name']) && isset($data['name']))
            {                
                $this->s3->putObjectFile($file['tmp_name'], $bucket, str_replace(" ", "-", trim(strtolower($data['name']))) . "/" . $file['name'], 'public-read');
                if($print)
                    print $base_url . str_replace(" ", "-", trim(strtolower($data['name']))) . "/" . $file['name'];
                else
                    return $base_url . str_replace(" ", "-", trim(strtolower($data['name']))) . "/" . $file['name'];
            }
        }

        public function validate_game(&$game, &$errors)
        {
                foreach($game as $key => $value)
                {
                        switch ($key)
                        {
                                case 'TotalCards':
                                case 'TotalWinningCards':
                                case 'SpotsOnCard':
                                case 'CardIncrement':
                                case 'WinningCardIncrement':
                                        if((!$value && $value != 0) || !is_numeric($value))
                                                $errors[$key] = "Required and Must Be Numeric";
                                        break;

                                case 'Name':
                                case 'SerialNumber':
                                        if(!$value)
                                                $errors[$key] = "Field Required";
                                        break;

                                case 'EndDate':
                                        $game[$key] = $value . " 23:59:59";
                                        break;

                                case 'StartDate':
                                        $game[$key] = $value . " 00:00:00";
                                        break;

                        }
                }
                if(count($errors))
                        return false;
                return true;
        }
        
        
        //All the Player Functions
        public function players()
        {        
            $search = "";
            if(isset($_POST['search']))
                $search = $_POST['search'];
            //$this->admin_model->getPlayers();
            $data = $this->admin_model->searchPlayers($search);
            $layout_data['content'] = $this->load->view("admin/view_players", $data, true);
            $layout_data['page'] = "ViewPlayers";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function player_search()
        {
            
        }
        
        public function player_images()
        {
            $data['players'] = $this->admin_model->getPlayerImages();
            //print_r($temp); die();
            $layout_data['content'] = $this->load->view("admin/view_player_images", $data, true);
            $layout_data['page'] = "ViewPlayers";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function edit_player($id)
        {
            $data = $this->admin_model->getPlayer($id);
            $data['google_api_key'] = getenv("GOOGLEGEOCODINGAPI");
            $layout_data['content'] = $this->load->view("admin/edit_player", $data, true);
            $layout_data['page'] = "ViewPlayers";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function get_player_daily($player_id, $date)
        {
            $data = $this->admin_model->getPlayerDaily($player_id, $date);
            $this->load->view("admin/show_player_activity", compact('data'));            
        }        
        
        public function ajax_update_player()
        {
            $data = $_POST;
            $errors = array();
            if(!$this->validate_player($data, $errors))
            {
                print json_encode(array('success' => false, 'errors' => $errors));
                die();
            }
            
            $this->admin_model->updatePlayer($data);
            print json_encode(array('success' => true));
            die();
        }
        
        public function view_player_notes($id)
        {
            $data = $this->admin_model->getPlayerNotes($id);
            $this->load->view('/admin/view_player_notes', $data);
        }
        
        public function ajax_save_player_note()
        {
            $data = $_POST;
            print json_encode(array("success" => $this->admin_model->savePlayerNote($data)));
            die();
        }
        
        private function validate_player($data, &$errors)
        {
            foreach($data as $key => $value)
            {
                switch($key)
                {
                    case 'screenName':
                    case 'firstName':
                    case 'lastName':
                        if(!$value)
                            $errors[$key] = "Required Field";
                        break;
                        
                    //case 'email':
                     //   if(!preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $value))
                       //     $errors[$key] = "Invalid Email Address";
                        //break;
                        
                    case 'dob':
                        if(!strtotime($value))
                            $errors[$key] = "Invalid Date";
                        break;
                }
            }
            if($errors)
                return false;
            return true;
        }
        
        //All the localization functions
        public function view_strings()
        {
            if(!$this->nativesession->get('language'))
                $this->nativesession->set('language', 'en');
            $id = $this->nativesession->get('language');
            $data = $this->admin_model->getStrings($id);
            $data['id'] = $id;
            $layout_data['content'] = $this->load->view("admin/view_strings", $data, true);
            $layout_data['page'] = "ViewStrings";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function add_string($id = NULL)
        {
            if(!$this->nativesession->get('language'))
                $this->nativesession->set('language', 'en');
            $language = $this->nativesession->get('language');
            $data = $this->admin_model->getString($id, $language);
            //print_r($data); die();
            $layout_data['content'] = $this->load->view("admin/add_string", $data, true);
            $layout_data['page'] = "EditStrings";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function ajax_add_string()
        {
            $data = $_POST;
            $errors = array();
            if(!$this->validate_string($data, $errors))
            {
                print json_encode(array('success' => false, 'errors' => $errors));
                die();
            }
            
            $this->admin_model->addString($data);
            print json_encode(array('success' => true));
            die();
        }
        
        private function validate_string($data, &$errors)
        {
            foreach($data as $key => $value)
            {
                switch($key)
                {
                    case "identifier":
                    case "description":
                    case "translation":
                    case "languageCode":
                        if(!$value)
                            $errors[$key] = "Required Field";
                        break;
                }
            }
            if(count($errors))
                return false;
            return true;
        }
        
        public function ajax_change_language()
        {
            if(isset($_POST['id']))
            {
                $this->nativesession->set('language', $_POST['id']);
                print json_encode(array('success' => true));
                die();
            }
            print json_encode(array('success' => false));
            die();
        }
        
        //All the rules functions
        public function ajax_add_rule_game()
        {
            $data = $_POST;
            print json_encode(array('success' => $this->admin_model->addGameRule($data)));
            die();
        }
        
        public function ajax_add_rule_template()
        {
            $data = $_POST;            
            $success = $this->admin_model->addRuleTemplate($data);
            print json_encode(array('success' => $success));
            die();
        }
        
        public function ajax_add_rule()
        {
            $data = $_POST;
            $text = "";
            $success = $this->admin_model->addRule($data, $text);
            print json_encode(array('success' => $success, 'text' => $text));
            die();
        }
        
        public function get_preview()
        {
            $file = $_POST['file'];
            if($file)
                print file_get_contents($file);
            die();
        }
        
        //All Sponsor functions
        public function view_sponsors()
        {
            $data['sponsors'] = $this->admin_model->getSponsors();            
            $layout_data['content'] = $this->load->view("admin/view_sponsors", $data, true);
            $layout_data['page'] = "ViewSponsors";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function edit_sponsor($id = NULL)
        {            
            if($id)
                $data = $this->admin_model->getSponsor($id);
            else
                $data['sponsor'] = NULL;
            
            $data['sponsorTypes'] = array('Sponsor', 'Advertiser');
            
            $layout_data['content'] = $this->load->view("admin/add_sponsor", $data, true);
            $layout_data['page'] = "EditSponsors";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function affiliates()
        {
            $data = $this->admin_model->getAffiliates();
            $layout_data['content'] = $this->load->view("admin/view_affiliates", $data, true);
            $layout_data['page'] = "Affiliates";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function affiliate($id)
        {
            $data = $this->admin_model->getAffiliate($id);
            $this->load->view("admin/add_affiliate_games", $data);
        }
        
        public function ajax_update_affiliate_games()
        {
            $games = $_POST;
            print json_encode(array('success' => $this->admin_model->updateAffiliateGames($games)));
            die();
        }
        
        public function ajax_add_affiliate_game($id)
        {
            $data = $this->admin_model->getAffiliate();
            $data['index'] = $id;
            print $this->load->view("admin/view_affiliate", $data, true);
        }
        
        public function advertising_campaign($id = NULL)
        {            
            $data = $this->admin_model->getAdvertisingCampaign($id);
            $layout_data['content'] = $this->load->view("admin/add_advertising_campaign", $data, true);
            $layout_data['page'] = "EditAdvertisingCampaign";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function view_advertising_campaigns()
        {
            $data['campaigns'] = $this->admin_model->getAdvertisingCampaigns();            
            $layout_data['content'] = $this->load->view("admin/view_advertising_campaigns", $data, true);
            $layout_data['page'] = "ViewAdvertisingCampaigns";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function ajax_add_advertising_campaign()
        {            
            print json_encode(array('success' => $this->admin_model->addAdvertisingCampaign($_POST)));
            die();
        }
        
        public function ajax_add_sponsor()
        {
            $errors = array();
            $data = $_POST;
            if(!$this->validate_sponsor($data, $errors))
            {
                print json_encode(array('success' => false, 'errors' => $errors));
                die();
            }
            
            $this->admin_model->addSponsor($data);
            print json_encode(array('success' => true));
            die();
        }
        
        private function validate_sponsor($data, &$errors)
        {
            foreach($data as $key => $value)
            {
                switch($key)
                {
                    case 'name':
                    case 'contactName':
                    case 'artRepo':
                    case 'city':
                    case 'state':
                    case 'address':
                    case 'zip':
                        if(!$value)
                            $errors[$key] = "Required Field";
                        break;
                        
                    case 'contactPhone':
                        if(!$value || !is_numeric($value))
                            $errors[$key] = "Required and needs to be Numeric";
                        break;
                        
                    case 'hexColor':
                        if(!$value || !preg_match("/^0x[A-F0-9]{6}$/", $value))
                            $errors[$key] = "Required and needs to be in the format 0xFFFFFF";
                        break;
                        
                    case 'contactEmail':
                        if(!$value || !preg_match("/[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}/", $value))
                            $errors[$key] = "Required and needs to be a valid email";
                        break;
                }
            }
            if($errors)
                return false;
            return true;
        }
        
        public function view_sponsor_campaigns()
        {
            $data['sponsors'] = $this->admin_model->getSponsorCampaigns();            
            $layout_data['content'] = $this->load->view("admin/view_sponsor_campaigns", $data, true);
            $layout_data['page'] = "ViewSponsorCampaigns";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function edit_sponsor_campaign($id = NULL)
        {
            $data = $this->admin_model->getSponsorCampaign($id);
            
            $layout_data['content'] = $this->load->view("admin/add_sponsor_campaign", $data, true);
            $layout_data['page'] = "EditSponsorCampaigns";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function ajax_add_sponsor_campaign()
        {
            $data = $_POST;
            $file = $_FILES;
            if($file)
            {
                $data['videoUrl'] = $this->file_upload(false, array('name' => 'videos', 'bucket' => 'kizzang-campaigns'));
            }
            $errors = array();
            
            if($_FILES)
            
            if(!$this->validate_sponsor_campaign($data, $errors))
            {
                print json_encode(array('success' => false, 'errors' => $errors));
                die();
            }
            
            $this->admin_model->addSponsorCampaign($data);
            print json_encode(array('success' => true));
            die();
        }
        
        public function ajax_delete_sponsor_campaign($id)
        {
            if($id && is_numeric($id))
            {
                print json_encode(array('success' => $this->admin_model->deleteSponsorCampaign($id)));
                die();
            }
            print json_encode(array('success' => false));
            die();
        }
        
        private function validate_sponsor_campaign($data, &$errors)
        {
            foreach($data as $key => $value)
            {
                switch($key)
                {
                    case 'name':
                    case 'artAssetUrl':
                    case 'gender':
                    case 'stateID':
                        if(!$value)
                            $errors[$key] = "Required Field";
                        break;
                        
                    case 'totalOffers':
                    case 'sponsorID':
                    case 'offersClaimed':
                    case 'ageMin':
                    case 'ageMax':
                    case 'type':
                        if((!$value || !is_numeric($value)) && $value != 0)
                            $errors[$key] = "Required Numeric Field";
                        break;
                        
                    case 'startDate':
                    case 'endDate':
                        if(!$value || !strtotime($value))
                            $errors[$key] = "Required Date Field";
                        break;
                }
            }
            if($errors)
                return false;
            return true;
        }
        
        public function map_sponsor_campaign($type = "Placement")
        {            
            $data = $this->admin_model->getMapInfo();
            $data['type'] = $type;
            //print_r($data); die();
            $layout_data['content'] = $this->load->view("admin/view_map", $data, true);
            $layout_data['page'] = "MapSponsorCampaigns";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function ajax_map_entry()
        {
            $data = $_POST;
            print json_encode(array('success' => $this->admin_model->addMapEntries($data)));
            die();
        }
        
        public function ajax_update_beziers()
        {
            $data = $_POST;            
            print json_encode(array('success' => $this->admin_model->addBezierEntries($data)));
            die();
        }
        
        public function view_states()
        {
            $data = $this->admin_model->getStates();
            $layout_data['content'] = $this->load->view("admin/view_states", $data, true);
            $layout_data['page'] = "ViewStates";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function edit_state($id)
        {
            if($id)
                $data = $this->admin_model->getState($id);
            else
                $data['state'] = NULL;
            
            $layout_data['content'] = $this->load->view("admin/add_state", $data, true);
            $layout_data['page'] = "ViewStates";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function ajax_update_state()
        {
            $data = $_POST;
            $errors = array();
            if(!$this->validate_state($data, $errors))
            {
                print json_encode(array('success' => false, 'errors' => $errors));
                die();
            }
            
            $this->admin_model->addState($data);
            print json_encode(array('success' => true));
            die();
        }
        
        private function validate_state($data, &$errors)
        {
            foreach($data as $key => $value)
            {
                switch($key)
                {
                    case 'name':
                        if(!$value)
                            $errors[$key] = "Required Field";
                        break;
                        
                    case 'abbreviation':
                        if(!$value || strlen($value) > 3)
                            $errors[$key] = "Required Field and must 3 or less characters";
                        break;
                        
                    case 'panelColumn':
                    case 'panelRow':
                        if(!is_numeric($value))
                            $errors[$key] = "Required Numeric Field";
                        break;
                }
            }
            if($errors)
                return false;
            return true;
        }
        
        public function view_wheels()
        {
            $data['wheels'] = $this->admin_model->getWheels();
            $layout_data['content'] = $this->load->view("admin/view_wheels", $data, true);
            $layout_data['page'] = "ViewWheels";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function add_wheel($id = NULL)
        {
            $data = $this->admin_model->getWheel($id);
            $layout_data['content'] = $this->load->view("admin/add_wheel", $data, true);
            $layout_data['page'] = "AddWheel";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function add_wedge($wheel_id, $id = NULL)
        {
            $data = $this->admin_model->getWedge($wheel_id, $id);
            $this->load->view("admin/add_wedge", $data);
        }
        
        public function ajax_add_wheel()
        {
            $data = $_POST;
            if(isset($data['name']) && isset($data['isDeleted']) && $data['name'] && is_numeric($data['isDeleted']))
            {
                print json_encode (array('success' => true, 'id' => $this->admin_model->addWheel($data)));
                die();
            }
            print json_encode(array('success' => false));
            die();
        }
        
        public function ajax_delete_wedge($wheelId, $id)
        {
            print json_encode(array('success' => $this->admin_model->deleteWedge($wheelId, $id)));
            die();
        }
        
        public function ajax_update_wedges()
        {
            $data = $_POST;            
            if(!isset($data['points']) || !isset($data['wheelId']))            
                print json_encode(array('success' => false));
            else            
                print json_encode(array('success' => $this->admin_model->updateWedges($data)));
            die();
        }
        
        public function ajax_add_wedge()
        {
            $data = $_POST;
            if($this->validate_wedge($data, $errors))
            {
                $this->admin_model->addWedge($data);
                print json_encode(array("success" => true));
                die();
            }
            print json_encode(array('success' => false, 'errors' => $errors));
            die();
        }
        
        private function validate_wedge($data, &$errors)
        {
            $errors = array();
            foreach($data as $key => $value)
            {
                switch($key)
                {
                    case "wheelId":
                        if(!$value || !is_numeric($value))
                            $errors[$key] = "No WheelID";
                        break;
                        
                    case "color":
                        if(!preg_match("/#[0-9A-Fa-f]{6}/", strtoupper($value)))
                            $errors[$key] = "Invalid Color";
                        break;
                }
            }
            
            if($errors)
                return false;
            return true;
        }
        
        public function view_event_notifications()
        {
            $data['notifications'] = $this->admin_model->getEventNotifications();
            $layout_data['content'] = $this->load->view("admin/view_event_notifications", $data, true);
            $layout_data['page'] = "ViewEventNotifications";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function add_event_notification()
        {            
            $this->load->view("admin/add_event_notification", $this->admin_model->getEventNotificationInfo());
        }
                
        public function ajax_add_event_notification()
        {
            print json_encode(array('success' => true, 'message' => $this->admin_model->addEventNotification($_POST) . " Notifications Added"));
            die();
        }
        
        public function ajax_clear_notifications()
        {
            print json_encode(array("success" => $this->admin_model->clearEventNotifications()));
            die();
        }
        
        public function ajax_fix_wheel_notifications()
        {
            $message = "";
            $result = $this->admin_model->fixWheelNotifications($message);
            print json_encode(array("success" => $result, "message" => $message));
            die();
        }
        
        private function get_url($folder = '', $class, $function){
            $url = '';
            $url .= (!empty($folder))? $folder.'/' : '';
            $url .= (!empty($class))? $class.'/' : '';
            $url .= (!empty($function))? $function : '';
            $url .= (!empty($folder))? '' : '/';
            return mb_strtolower($url);
        }
        
        //All ACL Functions
        public function view_acls()
        {
            $data = $this->admin_model->viewAcls();
            $layout_data['content'] = $this->load->view("admin/view_acls", $data, true);
            $layout_data['page'] = "ViewACLS";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function ajax_get_acls()
        {
            $directoryList = FCPATH.'../admin_application/controllers/';
            $directory = new \RecursiveDirectoryIterator($directoryList);
            $iterator = new \RecursiveIteratorIterator($directory);
            foreach ($iterator as $fileinfo) 
            {
                $folder = '';
                if ($fileinfo->isFile()) 
                {
                    if (stripos($fileinfo, '.php')!== false)
                    {
                        include_once($fileinfo);
                        $dir = str_replace(array($directoryList,'.php'), '', $fileinfo->getRealPath());
                        if (stripos($dir, "/")!== false){
                        list($folder, $filenameFile) = explode('/', $dir);
                    }
                    $class = str_replace('.php', '', $fileinfo->getFilename());
                    $classes[$class]['class'] = new ReflectionClass($class);
                    $classes[$class]['folder'] = $folder;
                    }
                }
            }

            foreach ($classes as $key=> $value)
            {
                $class_methods = $value['class']->getMethods(ReflectionMethod::IS_PUBLIC);
                $i = 0;
                foreach ($class_methods as $method_name) 
                {
                    $checked_whitelist = ''; $checked_required = '';
                    if ($method_name->class != 'CI_Controller')
                    {
                        $acl_url = $this->get_url($value['folder'], $method_name->class, $method_name->name);
                        
                        $listing[$method_name->class][$i]['folder'] = $value['folder'];
                        $listing[$method_name->class][$i]['class'] = $method_name->class;
                        $listing[$method_name->class][$i]['function'] = $method_name->name;                        
                        $i++;
                    }
                }
            }
            print json_encode(array('success' => true, 'count' => $this->admin_model->addAcls($listing)));
            die();
        }
        
        public function ajax_change_acl()
        {
            $data = $_POST;
            print json_encode(array('success' => $this->admin_model->changeAcl($data)));
            die();
        }
        
        public function acl_groups()
        {
            $data = $this->admin_model->getAclGroups();
            //print_r($data); die();
            $layout_data['content'] = $this->load->view("admin/view_acl_groups", $data, true);
            $layout_data['page'] = "ViewACLGroups";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function ajax_add_acl_group()
        {
            $data = $_POST;
            print json_encode(array('success' => $this->admin_model->addAclGroup($data)));
            die();
        }
        
        public function ajax_delete_acl_group($player_id, $group_id)
        {
            print json_encode(array('success' => $this->admin_model->deleteAclGroup($player_id, $group_id)));
            die();
        }
}

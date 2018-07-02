<?php

use Goutte\Client;

class Admin_Sports extends CI_Controller
{
        public function __construct()
        {
            set_time_limit (0); // Run forever
            ini_set('memory_limit','1024M'); 

            parent::__construct();

            $this->load->model('admin_model');
            $this->load->model('sports_model');

            $this->load->helper('url');							// This gives access to echo'ing base_url
        }
    
        //BRACKET SECTION --------------------------------------------------------------
        public function view_brackets()
        {
            $data['brackets'] = $this->sports_model->getBrackets();            
            $layout_data['content'] = $this->load->view("admin/sports/view_brackets", $data, true);
            $layout_data['page'] = "ViewBrackets";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function ajax_add_round_date($id)
        {
            return $this->sports_model->addBracketDate($id);
        }
        
        public function ajax_update_round_date()
        {
            print json_encode(array('success' => $this->sports_model->updateBracketDates($_POST)));
            die();
        }
        
        public function ajax_grad_cards($id)
        {
            print json_encode(array('success' => $this->sports_model->gradeBrackets($id)));
            die();
        }
        
        public function ajax_update_bracket_champion()
        {
            print json_encode(array('success' => $this->sports_model->updateBracketCampion($_POST)));
            die();
        }
        
        public function add_bracket($id = NULL)
        {            
            $data = $this->sports_model->getBracket($id);            
            $layout_data['content'] = $this->load->view("admin/sports/add_bracket", $data, true);
            $layout_data['page'] = "AddBracket";
            $this->load->view("layouts/admin", $layout_data);
        }
        
        public function ajax_add_bracket_config()
        {
            $data = $_POST;
            print json_encode(array('success' => $this->sports_model->addBracketConfig($data)));
            die();
        }
        
        public function ajax_delete_bracket_matchup($id)
        {
            print json_encode(array('success' => $this->sports_model->deleteBracket($id)));
            die();
        }
                
        public function ajax_add_bracket_matchup()
        {
            $data = $_POST;
            print json_encode(array('success' => $this->sports_model->addBracket($data)));
            die();
        }
        
        public function ajax_save_bracket_answers($id, $side)
        {
            $data = $_POST;
            print json_encode(array('success' => $this->sports_model->updateBracketAnswers($id, $side, $data)));
        }
        
        public function ajax_get_bracket_team($bracketConfigId, $division)
        {
            print $this->sports_model->getBracketTeamInfo($bracketConfigId, $division);            
            die();
        }
        
        public function ajax_add_bracket_team($bracketConfigId)
        {
            print $this->sports_model->getBracketTeamInfo($bracketConfigId);            
            die();
        }
        
    //All Sporting Events Schedule Functions
       public function view_sports_schedule($sport_cat_id = NULL)
       {
            $data = $this->admin_model->getSportsSchedules($sport_cat_id);
            $data['cat_sel'] = $sport_cat_id;
            $layout_data['content'] = $this->load->view("admin/sports/view_schedules", $data, true);
            $layout_data['page'] = "ViewSportsSchedule";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function view_sports_teams($sport_cat_id = 0)
       {
            $data = $this->sports_model->getSportsTeams($sport_cat_id);
            $data['cat_sel'] = $sport_cat_id;
            $layout_data['content'] = $this->load->view("admin/sports/view_sports_teams", $data, true);
            $layout_data['page'] = "ViewSportsTeams";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function edit_sports_teams($sportCategoryId, $id)
       {
           $data = $this->sports_model->getSportsTeam($sportCategoryId, $id);
           $this->load->view("admin/sports/edit_sports_teams", $data);
           //die();
       }
       
       public function ajax_update_sports_team()
       {
           print json_encode(array('success' => $this->sports_model->updateSportsTeam($_POST)));
           die();
       }
       
       public function send_emails()
       {           
           print json_encode($this->admin_model->sendParlayEmails());
           die();
       }
       
       public function remove_answer($id)
       {
           $this->admin_model->removeAnswer($id);
           print json_encode(array('success' => true));
           die();
       }
       
       public function pick_winners($id)
       {
           $data= $this->admin_model->getParlayWinners($id);
           $this->load->view("admin/sports/view_winners", $data);
       }
       
       public function add_cards($id, $num_cards)
       {
           $this->admin_model->createRandomParlayUsers($id, $num_cards);
           print "DONE"; 
           die();
       }
       
       public function update_powerranks()
       {
           $data = $_POST['pr_type'];
           $res = array();
           
           foreach($data as $type)           
           {
               switch($type)
               {
                   case 'Pro Hockey':
                       $res['Pro Hockey'] = $this->parse("http://espn.go.com/nhl/powerrankings", "ESPN"); break;
                   case 'Pro Baseball':
                       $res['Pro Baseball'] = $this->parse("http://espn.go.com/mlb/powerrankings", "ESPN"); break;
                   case 'Pro Basketball':
                       $res['Pro Basketball'] = $this->parse("http://espn.go.com/nba/powerrankings", "ESPN"); break;
                   case 'Pro Football':
                       $res['Pro Football'] = $this->parse("http://espn.go.com/nfl/powerrankings", "ESPN"); break;
                   case 'College Football':
                       $res['College Football'] = $this->parse("http://realtimerpi.com/football/ncaaf_Men.html", "REAL"); break;
                   case 'College Basketball':
                       $res['College Basketball'] = $this->parse("http://www.ncaa.com/rankings/basketball-men/d1/ncaa-mens-basketball-rpi", "REALBB"); break;
                   case 'Pro Soccer':
                       $res['Pro Soccer'] = $this->parse("http://www.seattletimes.com/sports/matt-pentzs-mls-power-rankings", "MLS"); break;
               }
           }
            
           $this->admin_model->updatePowerRanks($res);
           
           print json_encode(array('success' => true));
           die();
       }
       
       public function getNFLSchedule()
       {
           $url = "http://www.pro-football-reference.com/years/2015/";
           $client = new Client();           
           $crawler = $client->request("GET", $url);
           
           $dates = $crawler->filter("table.stats_table > tbody > tr");
           $schedule = array();
           $index = 0;
           foreach($dates as $date)
           {
               $temp = explode("\n", $date->nodeValue);
               if(count($temp) == 9)
               {
                   if(strstr(trim($temp[3]), " "))
                   {
                       $arrhome = explode(" ", $temp[5]);
                       $home = $arrhome[count($arrhome) - 1];
                       $arraway = explode(" ", $temp[3]);
                       $away = $arraway[count($arraway) - 1];
                        $game = array('away' => $away, 'home' => $home, 'date' => date("Y-m-d H:i:s", strtotime($temp[2] . " 2015 " . $temp[6])));
                        print sprintf("Insert into tmp_sched (home, away, game_date) values ('%s','%s','%s');\n", $game['home'], $game['away'], $game['date']);
                   }
               }
           }
           die();
       }
       
       private function parse($url, $type)
       {
           $client = new Client();           
           $crawler = $client->request("GET", $url);
           
           if($type == "ESPN")
               $nodes = $crawler->filter('table.tablehead > tr > td');
           elseif($type == "REAL")
               $nodes = $crawler->filter('table > tr');
           elseif($type == "REALBB")
               $nodes = $crawler->filter('table.ncaa-rankings-table > tbody > tr > td');
           elseif($type == "MLS")
               $nodes = $crawler->filter('div.main-story-content > table.methode-table > tr');
           
           $teams = array();
           if($type == "MLS")
           {                              
               $i = 0;
               foreach($nodes as $node)
               {                    
                    $temp = explode("\n", $node->nodeValue);                    
                    if(count($temp) == 4 && is_numeric($temp[0]))
                    {
                        $teams[$i]['Rank'] = $temp[0];
                        $teams[$i++]['TeamName'] = $temp[1];
                     }
               }              
           }
                     
           if($type == "ESPN")
           {
                $i = -1;
                $offset = 1;
                foreach($nodes as $node)
                {
                    if(is_numeric($node->nodeValue)) //Get Rank
                    {
                        $teams[++$i]['Rank'] = $node->nodeValue;
                        $offset = 0;
                        continue;
                    }
                    if(!$offset) //Get Team Name
                    {
                        preg_match("/^[0-9]*[A-Za-z \.]+/", $node->nodeValue, $matches);
                        $teams[$i]['TeamName'] = $matches[0];
                    }
                    $offset++;
                }
           }
           elseif($type == "REAL")
           {
                $i = 0;
                foreach($nodes as $node)
                {
                    if(preg_match("/^[0-9]+.*/", $node->nodeValue)) //Get Rank
                    {
                        $temp = $node->nodeValue;
                        $temp = explode("|", str_replace(chr(194), "|", $node->nodeValue));
                        $teams[$i]['Rank'] = $temp[0];

                        if(preg_match("/[0-9A-Za-z]+/", $temp[2]))
                        {
                            preg_match("/[0-9A-Za-z \.&\(\)']+/", $temp[2], $matches);
                            $teams[$i++]['TeamName'] = trim($matches[0]);
                        }
                        else
                        {
                            preg_match("/[0-9A-Za-z \.']+/", $temp[3], $matches);
                            $teams[$i++]['TeamName'] = trim($matches[0]);
                        }
                    }                                        
                }               
           }
           elseif($type == "REALBB")
           {
               $i = 0;
               $k = 0;
               foreach($nodes as $node)
               {
                   if(!$i || !($i % 9))
                       $teams[$k]['Rank'] = $node->nodeValue;
                   elseif($i >1 && !(($i - 2) % 9))
                       $teams[$k++]['TeamName'] = $node->nodeValue;
                   $i++;
               }
           }
           //print_r($teams); die();
           
           return $teams;
       }
       
       public function add_sports_schedule($id = NULL)
       {
            $data = $this->admin_model->getSportsSchedule($id);
            $layout_data['content'] = $this->load->view("admin/sports/add_schedule", $data, true);
            $layout_data['page'] = "AddSportsSchedule";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function ajax_category_change($id)
       {
           print $this->admin_model->getTeamDropdown($id);
           die();
       }
       
       public function ajax_add_schedule()
       {
           $data = $_POST;
           $errors = array();
           if(!$this->validate_schedule($data, $errors))
           {
               print json_encode(array('success' => false, 'errors' => $errors));
               die();
           }
           //print_r($data); die();
           $this->admin_model->addSportsSchedule($data);
           print json_encode(array('success' => true));
           die();
       }
       
       public function validate_schedule(&$data, &$errors)
       {
           foreach($data as $key => $value)
           {
               switch($key)
               {
                   case 'sportsCategoryID':
                   case 'team1':
                   case 'team2':
                       if(!$value || !is_numeric($value))
                           $errors[$key] = "Required field and must be numeric";
                       break;
                       
                   case 'dateTime':
                       if(!preg_match("/^[0-9]{2}\.[0-9]{2}\.[0-9]{4} [0-9]{2}:[0-9]{2}$/", $value))
                            $errors[$key] = "This is in the wrong format";
                       else
                           $data[$key] = date('Y-m-d H:i:00', strtotime($value));
                       break;
               }
           }
           if($errors)
               return false;
           return true;
       }
       
       //All the Parlay Editing funcitons
       public function view_parlay()
       {
           if(isset($_POST['startDate']) && isset($_POST['endDate']))
           {
               $startDate = $_POST['startDate'];
               $endDate = $_POST['endDate'];
           }
           else
           {
               $startDate = date('Y-m-d', strtotime("-5 DAY"));
               $endDate = date('Y-m-d', strtotime("+5 DAY"));
           }
            $data['parlays'] = $this->admin_model->getParlayCards($startDate, $endDate);
            $data['startDate'] = $startDate;
            $data['endDate'] = $endDate;
            $layout_data['content'] = $this->load->view("admin/sports/view_parlay", $data, true);
            $layout_data['page'] = "ViewParlay";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function add_parlay($id = NULL)
       {           
            $data = $this->admin_model->getParlayCard($id);   
            $layout_data['content'] = $this->load->view("admin/sports/add_parlay", $data, true);
            $layout_data['page'] = "AddParlay";
            $this->load->view("layouts/admin", $layout_data);
       }
       
       public function ajax_get_disclaimer($type)
       {
           print $this->sports_model->getDisclaimer($type);
           die();
       }
       
       public function ajax_get_payout($id)
       {
           $data = $this->admin_model->getPayoutInfo($id);
           $this->load->view("admin/sports/payout_table", $data);
       }
       
       public function clone_parlay_place($id)
       {
           $data = $this->admin_model->getParlayClones($id);
           $this->load->view("admin/sports/clone_payout", $data);
       }
       
       public function add_parlay_place($id)
       {
           $this->load->view("admin/sports/add_parlay_place", compact('id'));
       }
       
       public function ajax_add_parlay_place()
       {
           $data = $_POST;
           $this->admin_model->addParlayPlace($data);
           print json_encode(array("success" => true));
           die();
       }
       
       public function ajax_delete_parlay_place($id)
       {
           $this->admin_model->deleteParlayPlace($id);
           print json_encode(array("success" => true));
           die();
       }
       
       public function ajax_delete_parlay($id)
       {
           print json_encode(array("success" => $this->admin_model->deleteParlayCard($id)));
           die();
       }
       
       public function view_event_scores()
       {
           $data['events'] = $this->admin_model->getEventScores();
           $layout_data['content'] = $this->load->view("admin/sports/add_scores", $data, true);
           $layout_data['page'] = "EditEventScores";
           $this->load->view("layouts/admin", $layout_data);
       }
       
       public function add_parlay_event($id)
       {           
           $data = $this->admin_model->getPEDropDowns($id);
           $data['parlay_id'] = $id;
           $this->load->view("admin/sports/add_parlay_event", $data);
       }
       
       public function ajax_add_question_parlay($id)
       {
           print json_encode($this->admin_model->addParlayQuestion($id, $_POST));
           die();
       }
       
       public function ajax_save_sequence()
       {           
           if(!isset($_POST['parlayCardId']) || !isset($_POST['ids']))
               die();
           
           print json_encode(array('success' => $this->admin_model->saveScheduleSequence($_POST)));
           die();
       }
       
       public function search_parlay_events()
       {
           $data['events'] = $this->admin_model->searchParlayEvents($_POST);
           $this->load->view("admin/sports/parlay_event_table",$data);
       }
       
       public function add_event_parlay()
       {
           $data = $_POST;
           $count = $this->admin_model->addEventParlay($data['parlay_id'], $data['event_id'], $data['ou'], $data['spread']);
           if($count != -1)
                print json_encode(array('success' => true, 'count' => $count));
           else
               print json_encode (array('success' => false, 'error' => 'Maximum of 15 events added.'));
           die();
       }
       
       public function view_parlay_cards($parlayCardId, $playerId = NULL)
       {
           $data = $this->admin_model->getParlayCardCards($parlayCardId, $playerId);
           $data['playerId'] = $playerId;
           $layout_data['content'] = $this->load->view("admin/sports/view_parlay_card_cards", $data, true);
           $layout_data['page'] = "ViewParlayCardCards";
           $this->load->view("layouts/admin", $layout_data);
       }
       
       public function ajax_add_parlay()
       {
           $data = $_POST;
           $errors = array();
           if(!$this->validate_parlay($data, $errors))
           {
               print json_encode(array('success' => false, 'errors' => $errors));
               die();
           }
           $id = $this->admin_model->addParlayConfig($data);
           print json_encode(array('success' => true, 'id' => $id));
           die();
       }
       
       private function validate_parlay($data, &$errors)
       {
           $template = array('parlayCardId' => NULL, 'cardWin' => NULL, 'cardDate' => NULL);
           $data = array_merge($template, $data);
           foreach($data as $key => $value)
           {
               switch($key)
               {
                   case 'name':
                       if(!$value)
                           $errors[$key] = "Required Field";
                       break;
                       
                   case 'cardWin':
                       if(!$value)
                           $errors[$key] = "Require Field and must be numeric";
                       break;
                       
                   case 'cardDate':
                       if(!$value || !strtotime($value))
                           $errors[$key] = "Required Date Field";
                       break;
               }
           }
           if($errors)
               return false;
           return true;
       }
       
       public function ajax_delete_parlay_event($id)
       {
           print json_encode(array('success' => $this->admin_model->deleteParlayEvent($id))); 
           die();
       }
       
       public function ajax_add_event_scores()
       {
           $data = $_POST;
           //print_r($data); die();
           $errors = array();
           if(!$this->validate_event_scores($data, $errors))
           {
               print json_encode(array('success' => false, 'errors' => $errors));
               die();
           }
           
           $this->admin_model->addEventScores($data);
           print json_encode(array('success' => true));
           die();
       }
       
       private function validate_event_scores($data, &$errors)
       {
           foreach($data as $rec)
           {
                foreach($rec as $key => $value)
                {
                    switch($key)
                    {
                        case 'team1_score':
                        case 'team2_score':
                        case 'team1':
                        case 'team2':
                        case 'event_id':
                            if(!$value || !is_numeric($value))
                                $errors[$key] = "Require Field should be Numeric";
                            break;

                    }
                }
           }
           if($errors)
               return false;
           return true;
       }
       
       //All the Big Game 21 functionality
       public function view_bg_configs()
       {
           $data['configs'] = $this->admin_model->getBGConfigs();           
           $layout_data['content'] = $this->load->view("admin/sports/view_bgconfigs", $data, true);
           $layout_data['page'] = "ViewBGConfigs";
           $this->load->view("layouts/admin", $layout_data);
       }
       
       public function grade_bg_config($id)
       {
           $data = $this->admin_model->getBGGrades($id);
           $this->load->view("admin/sports/grade_bgconfig", $data);
       }
       
       public function ajax_update_bg_grades()
       {
           $data = $_POST;
           print json_encode(array('success' => $this->admin_model->updateBGConfig($data)));
       }
       
       public function view_bg_questions()
       {
           $data['questions'] = $this->admin_model->getBGQuestions();           
           $layout_data['content'] = $this->load->view("admin/sports/view_bgquestions", $data, true);
           $layout_data['page'] = "AddBGQuestions";
           $this->load->view("layouts/admin", $layout_data);
       }
       
       public function add_bg_config($id = NULL)
       {
           if($id)
               $data = $this->admin_model->getBGConfig($id);           
           else
               $data = array('config' => NULL, 'questions' => array(), 'count' => NULL);
           
           $layout_data['content'] = $this->load->view("admin/sports/add_bgconfig", $data, true);
           $layout_data['page'] = "AddBGQuestions";
           $this->load->view("layouts/admin", $layout_data);
       }
       
       public function add_bg_place($id)
       {
           $this->load->view("admin/sports/add_bg_place", compact('id'));
       }
       
       public function ajax_add_bg_place()
       {
           $data = $_POST;
           $this->admin_model->addBGPlace($data);
           print json_encode(array("success" => true));
           die();
       }
       
       public function ajax_delete_bg_place($id)
       {
           $this->admin_model->deleteBGPlace($id);
           print json_encode(array("success" => true));
           die();
       }
       
       public function add_bg_question($config_id, $id = NULL)
       { 
           if($id)
                $data = $this->admin_model->getBGQuestion($id);
           else
               $data = array('question' => NULL, 'answers' => array());
           $data['config_id'] = $config_id;
           $this->load->view("admin/sports/add_bgquestion", $data);
       }
       
       public function ajax_add_bg_question()
       {
           $data = $_POST;
           $errors = array();
           if(!$this->validate_bg_question($data, $errors))
           {
               print json_encode(array('success' => false, 'errors' => $errors));
               die();
           }
           $id = $this->admin_model->addBGQuestion($data);
           print json_encode(array('success' => true, 'id' => $id));
           die();
       }
       
       private function validate_bg_question($data, &$errors)
       {
           foreach($data as $key => $value)
           {
               switch($key)
               {
                   case 'question':
                       if(!$value)
                           $errors[$key] = "Required Field";
                       break;
                       
                   case 'startDate':
                   case 'endDate':
                       if(!$value || !strtotime($value))
                           $errors[$key] = "Validate Date Required";
                       break;
               }
           }
           if(count($errors))
               return false;
           return true;
       }
       
       public function ajax_add_bg_config()
       {
           $data = $_POST;
           $errors = array();
           if(!$this->validate_bg_config($data, $errors))
           {
               print json_encode(array('success' => false, 'errors' => $errors));
               die();
           }
           $id = $this->admin_model->addBGConfig($data);
           print json_encode(array('success' => true, 'id' => $id));
           die();
       }
       
       private function validate_bg_config($data, &$errors)
       {
           foreach($data as $key => $value)
           {
               switch($key)
               {
                   case 'name':
                       if(!$value)
                           $errors[$key] = "Required Field";
                       break;
                       
                   case 'startDate':
                   case 'endDate':
                       if(!$value || !strtotime($value))
                           $errors[$key] = "Validate Date Required";
                       break;
               }
           }
           if(count($errors))
               return false;
           return true;
       }
       
       public function ajax_delete_bg_question()
       {
           $id = $_POST['question_id'];
           $this->admin_model->deleteBGQuestion($id);
           print json_encode(array('success' => true));
           die();
       }
       
       public function ajax_delete_bg_config()
       {
           $id = $_POST['question_id'];
           $this->admin_model->deleteBGConfig($id);
           print json_encode(array('success' => true));
           die();
       }
       
       public function ajax_add_bg_answer()
       {
           $data = $_POST;
           $row = $this->admin_model->addBGAnswer($data);
           print json_encode(array('success' => true, 'row' => $row));
           die();
       }
       
       public function ajax_delete_bg_answer()
       {
           $id = $_POST['answer_id'];
           $this->admin_model->deleteBGAnswer($id);
           print json_encode(array('success' => true));
           die();
       }
       
       public function ajax_update_bg_answer()
       {
           $data = $_POST;
           $this->admin_model->addBGAnswer($data);
           print json_encode(array('success' => true));
           die();
       }
       
       //All the Final 3 functions
       public function view_ft()
       {
           $data= $this->admin_model->getFTs();
           //print_r($data); die();
           $layout_data['content'] = $this->load->view("admin/sports/view_ft", $data, true);
           $layout_data['page'] = "ViewFTConfigs";
           $this->load->view("layouts/admin", $layout_data);
       }
       
       public function ajax_grade_ft_cards($id)
       {
           print json_encode(array('success' => $this->sports_model->getFTWinners($id)));
           die();
       }
       public function view_ft_winners($id)
       {
           $data['lines'] = $this->sports_model->printFTWinners($id);
           $this->load->view("admin/sports/view_ft_winners", $data);
       }
       
       public function add_ft_config($id = NULL)
       {
           
           $data = $this->admin_model->getFt($id);           

           $layout_data['content'] = $this->load->view("admin/sports/add_ftconfig", $data, true);
           $layout_data['page'] = "AddFTConfigs";           
           $this->load->view("layouts/admin", $layout_data);
       }
       
       public function ajax_save_ft()
       {
           //quick validation
           $data = $_POST;
           $message = "";
           foreach($data as $key => $value)
           {
               switch($key)
               {
                   case "id": 
                   case 'val':
                       if(!is_numeric($value)) $message = "Invalid Data"; break;                   
               }
           }
           
           if($message)
           {
               print json_encode(array('success' => false, 'message' => $message));
               die();
           }
           
           print json_encode(array('success' => $this->admin_model->updateFTScores($data)));
           die();
       }
       
       public function ajax_add_ft_team()
       {
           $team = $_POST['team'];
           print json_encode(array('success' => true, 'row' => $this->admin_model->addFTTeam($team)));
           die();
       }
       
       public function ajax_add_ft_category()
       {
           $category = $_POST['category'];
           print json_encode(array('success' => true, 'row' => $this->admin_model->addFTCategory($category)));
           die();
       }
       
       public function ajax_delete_ft_game()
       {
           if(isset($_POST['question_id']))
               $id = $_POST['question_id'];
           else
               die();
           $this->admin_model->deleteFTGame($id);
           print json_encode(array('success' => true));
           die();
       }
       
       public function ajax_delete_ft_config($id)
       {
           $this->admin_model->deleteFTConfig($id);
           print json_encode(array('success' => true));
           die();
       }
       
       public function ajax_delete_tf_place()
       {
           $data = $_POST;
           $this->admin_model->deleteFTPlace($data);
           print json_encode(array('success' => true));
           die();
       }
       
       public function ajax_add_ft_place()
       {
           $data = $_POST;           
           print json_encode(array('success' => $this->admin_model->addFTPlace($data)));
           die();
       }
       
       public function ajax_add_ft_game()
       {
           $data = $_POST;
           $this->admin_model->addFTGame($data);
           print json_encode(array('success' => true));
           die();
       }
       
       public function ajax_add_ft_config()
       {
           $data = $_POST;
           $id = $this->admin_model->addFTConfig($data);
           print json_encode(array('success' => true, 'id' => $id));
           die();
       }
       
       public function add_ft_place($id)
       {
           $this->load->view("admin/sports/add_ft_place", compact('id'));
       }
       
       public function add_ft_game($id)
       {
           $data = $this->admin_model->getFTGame($id);
           $this->load->view("admin/sports/add_ft_game", $data);
       }
}
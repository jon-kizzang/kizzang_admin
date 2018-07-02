<?php 
use Aws\Common\Aws;

class admin_model extends CI_Model
{
        const ENCRYPTION_KEY = "KizfDkj353";
                
        function __construct()
        {
            parent::__construct();
            $this->db = $this->load->database ('admin', true);            
        }
        
        public function getBBGames()
        {
            $rs = $this->db->query("Select * from Game order by gameType");
            return $rs->result();
        }
        
        public function getBBGame($id)
        {
            $game = NULL;
            if($id)
            {
                $rs = $this->db->query("Select * from Game where id = ?", array($id));
                if($rs->num_rows())
                    $game = $rs->row();
            }
            return compact('game');
        }
        
        public function saveGame($data)
        {
            $errors = array();
            $template = array('gameType' => '', 'maxGames' => '', 'comingSoon' => '', 'displayName' => '', 'theme' => '', 'displayOrder' => '');
            $data = array_merge($template, $data);
            foreach($data as $key => $value)
            {
                switch($key)
                {
                    case 'gameType':
                    case 'displayName':
                        if(!$value)
                            $errors[$key] = "Required Field";
                        break;
                        
                    case 'maxGames':
                    case 'displayOrder':
                    case 'comingSoon':
                        if(!is_numeric($value))
                            $errors[$key] = "Field needs to be Numeric";
                        break;
                }
            }
            if(count($errors))
                return array('success' => false, 'errors' => $errors);
            
            if(isset($data['id']))
            {
                $this->db->where('id', $data['id']);
                $this->db->update('Game', $data);
            }
            else
            {
                $this->db->insert('Game', $data);
            }
            return array('success' => true);
        }
 
        public function getAudits($playerId)
        {
            $template = array('first_name', 'last_name', 'address', 'address2', 'city', 'state', 'zip', 'phone', 'cellphone', 'email', 'dob', 'gender', 'unknown');
            $crypt = new Crypt();
            $players = array();
            if($crypt->init("KizfDkj353", 1) == "ok")
            {
                $rs = $this->db->query("Select *, convert_tz(created, 'GMT', 'US/Pacific') as change_date from Audits where player_id = ? and model = 'player'", array($playerId));
                foreach($rs->result() as $index => $temp)
                {
                    $data = $crypt->decrypt_128( utf8_encode( $temp->query_statement ) );

                    $data = explode("::", $data);
                    //0 - First Name | 1 - Last Name | 2 - Address | 3 - Unknown | 4 - City | 5 - State | 6 - Zip | 7 - Phone | 8 - CellPhone | 9 - Email | 10 - DOB | 11 - Gender | 12 - Unknown
                    $i = 0;
                    $player = array();
                    foreach($template as $value)                    
                        $players[$index][$value] = $data[$i++];
                    $players[$index]['change_date'] = date("D M jS, h:i:s A",  strtotime($temp->change_date));
                }
            }
            return array('audits' => $players);
        }
        
        public function getColumnEnum($schema, $table, $column)
        {
            $rs = $this->db->query("Select * from INFORMATION_SCHEMA.COLUMNS where TABLE_SCHEMA = ? and TABLE_NAME = ? and COLUMN_NAME = ?", array($schema, $table, $column));            
            if($rs->num_rows())
            {
                $temp = str_ireplace("enum(", "", trim($rs->row()->COLUMN_TYPE, ")"));
                if(preg_match_all("/'([0-9A-Za-z _]+)'/", $temp, $matches))
                {
                    return $matches[1];
                }                     
            }
            return array();
        }
        
        //---- CONFIG TABLE SECTION
        public function getAllConfigs()
        {
            $rs = $this->db->query("Select * from Configs");
            $configs = $rs->result();
            foreach($configs as &$row)
            {
                switch($row->data_type)
                {
                    case 'Numeric': $info = (int) $row->info; break;
                    case 'File':
                    case 'Text': $info = $row->info; break;
                    case 'JSON': $info = json_decode($row->info, true); break;
                    case 'Serialized': $info = unserialize($row->info); break;
                    default: $info = $row->info; 
                }            
                $row->info = $info;
            }        
            return compact('configs');
        }
                
        public function getDBConfig($id)
        {
            if($id)
            {
                $rs = $this->db->query("Select * from Configs where id = ?", array($id));
                $config = $rs->row();
                
                switch($config->data_type)
                {
                    case 'Numeric': $info = (int) $config->info; break;
                    case 'Text': $info = $config->info; break;
                    case 'JSON': $info = json_decode($config->info, true); break;
                    case 'Serialized': $info = unserialize($config->info); break;
                    case 'File': $info = $config->info; break;
                    default: $info = $config->info; 
                }            
                
                if(isset($info['action']))
                {
                    $config->action = $info['action'];
                    unset($info['action']);
                }
                else
                {
                    $config->action = "";
                }
                $config->info = $info;
            }
            else
            {
                $config = NULL;
            }
            
            $main_types = array('Config','File');
            $sub_types = array('Ad','Chedda','Background Version', '');
            $actions = array('');            
            $data_types = $this->getColumnEnum("kizzang", "Configs", "data_type");
            return compact('config','main_types','sub_types','data_types','actions');
        }
        
        public function addDBConfig($data)
        {
            $info = "";
            foreach($data as $key => $value)
            {
                if(strstr($key, "info_") !== false)
                    $info[trim($key, "info_")] = $value;
                elseif($key == "info")
                    $info = $value;
                elseif($key == "action" && $value)
                    $info['action'] = $value;
            }
            $main_type = $data['main_type'];
            $sub_type = $data['sub_type'];
            $data_type = $data['data_type'];
            switch($data_type)
            {
                case 'Numeric': $info = (int) $info; break;                
                case 'JSON': 
                    if(!is_array(json_decode($info, true)))                         
                        $info = json_encode($info); 
                    break;
                case 'Serialized': $info = serialize($info); break;                 
            }
            $rec = compact('main_type','sub_type','data_type','info');
            if(isset($data['id']))
            {
                $this->db->where("id", $data['id']);
                $this->db->update("Configs", $rec);
            }
            else
            {
                $this->db->insert("Configs", $rec);
            }
            return true;
        }         
        
        //GAME PAYOUT SECTION ----------------------------------------------------------------------------
        public function getGamePayouts()
        {
            $rs = $this->db->query("Select gameType, count(*) as cnt from Payouts group by gameType");
            $payouts = $rs->result();
            return compact('payouts');
        }
        
        public function getGamePayoutType($type)
        {
            $payouts = array();
            if($type)
            {
                $rs = $this->db->query("Select * from Payouts where gameType = ? order by startRank", array($type));
                $payouts = $rs->result();
            }
            $gameTypes = $this->getColumnEnum("kizzang", "Payouts", "gameType");
            $payTypes = $this->getColumnEnum("kizzang", "Payouts", "payType");
            return compact('payouts','gameTypes','payTypes');
        }
        
        public function addGamePayout($data)
        {
            if($data['id'])
            {
                $this->db->where(array("id" => $data['id']));
                return $this->db->update("Payouts", $data);
            }
            else
            {
                unset($data['id']);
                return $this->db->insert("Payouts", $data);
            }
        }
        
        //RUN OF A LIFETIME --------------------------------------------------------------
        public function viewROALs()
        {
            $rs = $this->db->query("Select c.*, count(q.id) as cnt, sum(IF(answer IS NULL, 0, 1)) as qsum from ROALConfigs c
                Left Join ROALQuestions q on c.id = q.ROALConfigId
                Group by c.id
                Order by cardDate DESC");
            $configs = $rs->result();
            return compact('configs');
        }
        
        public function getROAL($id)
        {
            $config = $questions = array();
            if($id)
            {
                $rs = $this->db->query("Select c.*, count(a.playerId) as cnt from ROALConfigs c 
                    Left Join ROALAnswers a on c.id = a.ROALConfigId 
                    where c.id = ?
                    group by c.id", array($id));
                if($rs->num_rows())
                {
                    $config = $rs->row();
                    $config->serialNumber = sprintf("KR%05d", $config->id);
                    $rs = $this->db->query("Select q.*, s.id as event_id, s.dateTime as date, c.name as category, s.team1, a.name as teamName1, s.team2, b.name as teamName2 from ROALQuestions q
                        Inner join SportSchedule s on s.id = q.SportScheduleId
                        Inner join SportTeams a on s.team1 = a.id and s.sportCategoryID = a.sportCategoryID
                        Inner join SportTeams b on s.team2 = b.id and s.sportCategoryID = b.sportCategoryID
                        Inner join SportCategories c on c.id = s.sportCategoryID
                        Where q.ROALConfigId = ?", array($config->id));
                    $questions = $rs->result();
                }
            }
            
            $rules = array();
            $rs = $this->db->query("Select DISTINCT ruleURL from GameRules where gameType = 'ROAL' AND serialNumber = 'TEMPLATE'");
            $rules = $rs->result();
            
            $rule = NULL;
            if($config)
            {
                $rs = $this->db->query("Select * from GameRules where serialNumber = ?", $config->serialNumber);                
                
                if($rs->num_rows())
                {
                    $rule = $rs->row();
                    $rule->text = file_get_contents(str_replace("https://d23kds0bwk71uo.cloudfront.net", "https://kizzang-legal.s3.amazonaws.com",$rule->ruleURL));
                    if(count($rules))
                        $rule->template = file_get_contents(str_replace("https://d23kds0bwk71uo.cloudfront.net", "https://kizzang-legal.s3.amazonaws.com",$rules[0]->ruleURL));
                    else
                        $rule->template = "";
                }                
            }
            return compact('config', 'questions','rules','rule');
        }
        
        public function updateROALQuestion($data)
        {
            $this->db->query("Update ROALQuestions set answer = ? where id = ?", array($data['answer'], $data['questionId']));
            return true;
        }
        
        public function gradeROALQuestions($id)
        {
            $rs = $this->db->query("Select * from ROALQuestions where ROALConfigId = ? and answer IS NOT NULL", array($id));
            foreach($rs->result() as $row)
            {
                $this->db->query("Update ROALAnswers, ROALQuestions set ROALAnswers.isCorrect = 1 where ROALQuestions.id = ROALQuestionId and answer = winningTeam and ROALQuestions.id = ?", array($row->id));
                $this->db->query("Update ROALAnswers, ROALQuestions set ROALAnswers.isCorrect = 0 where ROALQuestions.id = ROALQuestionId and answer <> winningTeam and ROALQuestions.id = ?", array($row->id));
            }
            
            $rs = $this->db->query("Select playerId, c.id, cardDate from ROALAnswers a
                Inner join ROALConfigs c on a.ROALConfigId = c.id
                Inner join ROALQuestions q on q.id = a.ROALQuestionId and q.answer IS NOT NULL
                Where isCorrect = 1
                Order by playerId, cardDate");
            
            $playerId = $count = 0;
            $lastDate = NULL;
            foreach($rs->result() as $row)
            {
                if($row->playerId <> $playerId)
                {
                    $this->db->query("Update ROALAnswers set currentStreak = 1 where playerId = ? and ROALConfigId = ?", array($row->playerId, $row->id));
                    $playerId = $row->playerId;
                    $count = 1;
                    $lastDate = $row->cardDate;
                    continue;
                }
                
                if(strtotime($row->cardDate) - strtotime($lastDate) == 86400)  // Only one day in between
                    $count++;
                else                
                    $count = 1;
                    
                $this->db->query("Update ROALAnswers set currentStreak = ? where playerId = ? and ROALConfigId = ?", array($count, $row->playerId, $row->id));
                $lastDate = $row->cardDate;
            }
        }
        
        public function addROAL($data)
        {
            if(!isset($data['cardDate']))
                return json_encode (array('success' => false, 'message' => 'Invalid Input Values'));
            
            if(isset($data['id']))
            {
                $rs = $this->db->query("Select * from ROALConfigs where cardDate = ? and id <> ?", array($data['cardDate'], $data['id']));
                if($rs->num_rows())
                    return json_encode(array('success' => false, 'message' => 'Duplicate Card date in DB'));
            }
            
            if(isset($data['id']))
            {
                $this->db->where("id", $data['id']);
                $this->db->update('ROALConfigs', $data);
                $id = $data['id'];
            }
            else
            {
                $this->db->insert('ROALConfigs', $data);
                $id = $this->db->insert_id();
            }
            return json_encode(array('success' => true, 'id' => $id));
        }
        
        public function getROALEvents($id)
        {
          
            $rs = $this->db->query("Select * from ROALConfigs where id = ?", array($id));
            $config = $rs->row();
            
            $rs = $this->db->query("Select * from SportCategories order by name");
            $categories =  $rs->result();
            
            $rs = $this->db->query("Select count(DISTINCT id) as cnt from ROALQuestions where ROALConfigId = ?", array($id));
            $temp = $rs->row();
            $count = $temp->cnt;
            
            $data = array('id' => $id, 'parlay_date' => $config->cardDate, 'parlay_type' => '', 'sel_parlay_cat' => NULL, 'sel_parlay_team' => NULL);                     
            $events = $this->searchROALEvents($data);
            
            return compact("categories", "count", "config", "events");        
        }
        
        public function searchROALEvents($data)
        {          
            $xlat = array('collegefootball' => 'College Football', 'profootball' => 'Pro Football');
            $query = sprintf("Select %d as roal_id, ss.id as event_id, sc.name as category, st1.name as team1, st1.powerRanking as pr1, st2.name as team2, st2.powerRanking pr2, ABS(st1.powerRanking - st2.powerRanking) as diff, sc.rank, ss.dateTime as date
                    From SportSchedule ss
                    Inner join SportTeams st1 on st1.id = ss.team1 and st1.sportCategoryID = ss.sportCategoryID
                    Inner join SportTeams st2 on st2.id = ss.team2 and st2.sportCategoryID = ss.sportCategoryID
                    Inner join SportCategories sc on sc.id = ss.sportCategoryID                    
                    Where ss.id not in (Select DISTINCT sportScheduleId from ROALQuestions where ROALConfigId = %d) ", $data['id'], $data['id']);
            $where = "";
            if($data['sel_parlay_cat'])
                $where .= sprintf(" AND ss.sportCategoryId = %d", $data['sel_parlay_cat']);
            if($data['sel_parlay_team'])
                $where .= sprintf(" AND (ss.team1 = %d OR ss.team2 = %d)", $data['sel_parlay_team'], $data['sel_parlay_team']);
            if($data['parlay_date'] && !($data['parlay_type'] == "collegefootball" || $data['parlay_type'] == "profootball"))
                $where .= sprintf (" AND DATE(ss.dateTime) = '%s'", date('Y-m-d', strtotime($data['parlay_date'])));
            else
                $where .= sprintf (" AND ss.dateTime between '%s' and '%s'", date('Y-m-d', strtotime($data['parlay_date'])), date('Y-m-d', strtotime("+1 week",  strtotime($data['parlay_date']))));            
            
            $rs = $this->db->query($query . $where . " ORDER BY sc.rank ASC, ABS(st1.powerRanking - st2.powerRanking) ASC LIMIT 100");
            return $rs->result();
        }
        
        public function addEventROAL($data)
        {            
            $rs = $this->db->query("Select count(*) as cnt from ROALQuestions where ROALConfigId = ?", array($data['id']));
            $count = $rs->row()->cnt + 1;
            if($rs->row()->cnt >= 3)
                return json_encode(array('success' => false, 'message' => 'The Maximum amount of records (3) reached for the day'));
            
            $rs = $this->db->query("Select * from SportSchedule where id = ? and ? > dateTime - INTERVAL 10 MINUTE", array($data['event_id'], date('Y-m-d H:i:s', strtotime($data['endDate']))));
            if($rs->num_rows())
                return json_encode(array('success' => false, 'message' => 'End Date needs to be at least 10 Minutes before Start of Game.'));
            
            $this->db->insert('ROALQuestions', array('ROALConfigId' => $data['id'], 'SportScheduleId' => $data['event_id'], 'startTime' => $data['startDate'], 'endTime' => $data['endDate']));
            return json_encode(array('success' => true, 'count' => $count));
        }
        
        public function deleteEventROAL($id)
        {
            $this->db->query("Delete from ROALQuestions where id = ?", array($id));
            return true;
        }
        
        //STORE SECTION ---------------------------------------------------------------------
        public function getStoreItems()
        {
            $rs = $this->db->query("Select * from Store order by id DESC");
            $storeItems = $rs->result();            
            return compact('storeItems');
        }
        
        public function getStoreItem($id)
        {
            $rs = $this->db->query("Select * from Store where id = ?", array($id));
            $storeItem = $rs->row();
            return compact('storeItem');
        }
        
        public function addStoreItem($data)
        {
            if(isset($data['id']))
            {
                $this->db->where(array("id" => $data['id']));
                $this->invalidateCloudfrontFiles('kizzang-campaigns', array($data['imageUrl']));
                return $this->db->update("Store", $data);
            }
            else
            {
                return $this->db->insert("Store", $data);
            }
        }
        
        //TESTIMONIAL SECTION ---------------------------------------------------------------------
        public function getTestimonials()
        {
            $rs = $this->db->query("Select * from Testimonials order by winDate DESC");
            $testimonials = $rs->result();
            return compact('testimonials');
        }
        
        public function getTestimonial($id)
        {
            $rs = $this->db->query("Select * from Testimonials where id = ?", array($id));
            $testimonial = $rs->row();
            return compact('testimonial');
        }
        
        public function addTestimonial($data)
        {
            if(isset($data['id']))
            {
                $this->db->where(array("id" => $data['id']));
                return $this->db->update("Testimonials", $data);
            }
            else
            {
                return $this->db->insert("Testimonials", $data);
            }
        }
        
        //CRON JOB SECTION ----------------------------------------------------------------------------
        public function getCrons()
        {
            $this->db->query("Delete FROM kizzang.CronLog where created < now() - INTERVAL 7 day");
            $rs = $this->db->query("Select c.*, max(convert_tz(l.created, 'GMT', 'US/Pacific')) as lastRan, min(s.schedule_date) as nextRun  
                from CronJobs c
                Inner Join CronLog l on c.id = l.cron_id
                Inner Join CronSchedule s on c.id = s.cron_id and s.schedule_date > convert_tz(now(), 'GMT', 'US/Pacific')
                Group by c.id
                order by c.id");
            $crons = array();
            if($rs->num_rows)
            {
                foreach($rs->result() as $row)
                {
                    if($row->id)
                    {
                        $row->string = $row->minutes . "/" . $row->hours . "/" . $row->day_of_month . "/" . $row->months . "/" . $row->day_of_week;
                        $crons[] = $row;
                    }
                }
            }
            return compact('crons');
        }
        
        public function getCronHistory($id)
        {
            $rs = $this->db->query("Select s.schedule_date, l.status, convert_tz(l.created, 'GMT', 'US/Pacific') as created 
                from CronLog l
                Inner join CronSchedule s on l.cron_schedule_id = s.id 
                where l.cron_id = ? order by l.created DESC limit 10", array($id));
            $histories = $rs->result();
            return compact("histories");
        }
        
        public function getCronSchedule()
        {
            $rs = $this->db->query("Select s.*, j.name, j.id from CronSchedule s
                Inner join CronJobs j on j.id = s.cron_id
                Where schedule_date > convert_tz(now(), 'GMT', 'US/Pacific')
                Order by schedule_date");
            return $rs->result();
        }
        
        public function switchCronStatus($id)
        {
            $ids = explode("-", $id);
            if(count($ids) != 2)
                return false;
            
            $rs = $this->db->query("Select * from CronSchedule where cron_id = ? and schedule_date = ?", array($ids[0], date("Y-m-d H:i:s", $ids[1])));
            if(!$rs->num_rows())
                return false;
            
            $row = $rs->row();
            if($row->is_active)
                $state = 0;
            else
                $state = 1;
            
            $this->db->where(array('cron_id' => $row->cron_id, 'schedule_date' => $row->schedule_date));
            $this->db->update("CronSchedule", array("is_active" => $state));
            return true;
        }
        
        public function addCron($data)
        {            
            if(isset($data['id']))
            {
                $this->db->where("id", $data['id']);
                $this->db->update("CronJobs", $data);
            }
            else
            {
                $this->db->insert("CronJobs", $data);
            }
            admin_model::addAudit($this->db->last_query(), "admin_model", "addCron");
            return true;
        }
        
        public function processCrons()
        {
            $rs = $this->db->query("Select r.link, s.*, j.name from CronSchedule s 
                Inner join CronJobs j on j.id = s.cron_id 
                Inner join Routines r on r.id = j.routine_id 
                where schedule_date between convert_tz(now() - INTERVAL 10 minute, 'GMT', 'US/Pacific') and convert_tz(now(), 'GMT', 'US/Pacific') and is_active = 1 and status = 'Pending'");
            
            return $rs->result();
        }
        
        public function changeCronStatus($id, $status)
        {
            $this->db->where("id", $id);
            $this->db->update("CronSchedule", array('status' => $status));
        }
        
        public function logCronRun($data)
        {
            $this->db->insert("CronLog", $data);
        }
        
        public function addPnCron($data)
        {
            $dates = array();
            $info = array();            
            foreach($data as $key => $value)
            {
                if($key == "id" || $key == "preview")
                    continue;
                
                if($key == "expireDate")
                    $max_date = $value;
                
                if($value != "*")
                    $info[$key] = $value;
            }
            
            $dates = $this->getCronDates($info, $max_date);
            if($data['preview'])
            {
                return json_encode (array('success' => true, 'dates' => implode ("\n", $dates)));
            }
            else
            {
                $recs = array();
                foreach($dates as $date)
                {
                    $recs[] = array('queue_id' => $data['id'], 'schedule_date' => $date);
                }
                return json_encode(array('success' => $this->db->insert_batch('notifications.crons', $recs)));
            }   
        }
        
        public function createCronSchedule($max_date = NULL)
        {
            $ret = array();
            if(!$max_date)
                $max_date = date("Y-m-d", strtotime("+1 week"));                        
            
            $rs = $this->db->query("Select * from CronJobs");
            foreach($rs->result_array() as $row)
            {
                $dates = array();
                $info = array();
                foreach($row as $key => $value)
                {
                    if($key == "id" || $key == "name" || $key == "routine_id" || $key == "created" || $key == "updated")
                        continue;
                    
                    if($value != "*")
                        $info[$key] = $value;
                }
                if(!count($info))
                    continue;
                                
                $dates = $this->getCronDates($info, $max_date);
                $ret['dates'][$row['id']] = $dates;
                $insert = "Insert ignore into CronSchedule (cron_id, schedule_date) values ";
                if(!count($dates))
                    continue;
                foreach($dates as $date)
                    $insert .= sprintf("(%d,'%s'),", $row['id'], $date);
                
                $this->db->query(trim($insert, ","));
            }          
            $this->db->query("Delete from CronSchedule where schedule_date < now() - INTERVAL 1 WEEK");
            $ret['success'] = true;
            return $ret;
        }
        
        private function getCronDates($info, $max_date)
        {
            $dates =array();
            $today = strtotime(date("Y-m-d"));
            $day = 24 * 3600;
            $end_date = strtotime($max_date);
            
            //Figure out the minutes and hours first
            $minutes = isset($info['minutes']) ? $info['minutes'] : 0;
            $minutes += isset($info['hours']) ? $info['hours'] * 60 : 0;
            for($i = $today; $i < $end_date; $i+=$day)
            {
                if(isset($info['hours']))                
                {
                    $dates[] = date("Y-m-d H:i:s", $i + $minutes * 60);
                }
                else       
                {
                    if(isset($info['minutes']) && $info['minutes'] == 0) //If minute is 0, then split the jobs out into 15 minute crons
                    {
                        for($j = 0; $j < 96; $j++)
                            $dates[] = date("Y-m-d H:i:s", $i + $minutes * 60 + ($j * 900));
                    }
                    else
                    {
                        for($j = 0; $j < 24; $j++)
                            $dates[] = date("Y-m-d H:i:s", $i + $minutes * 60 + ($j * 3600));
                    }
                }
            }
            
            //Now to eliminate based on the other 3 criteria
           
            if(isset($info['day_of_month']))
            {
                $temp = $dates;
                foreach($temp as $index => $day)                
                    if($info['day_of_month'] != date("d", strtotime($day)))
                            unset($dates[$index]);
            }
            
            if(isset($info['months']))
            {
                $temp = $dates;
                foreach($temp as $index => $day)                
                    if($info['months'] != date("m", strtotime($day)))
                            unset($dates[$index]);
            }
            
            if(isset($info['day_of_week']))
            {
                $temp = $dates;
                foreach($temp as $index => $day)                
                    if($info['day_of_week'] != date("N", strtotime($day)))
                            unset($dates[$index]);
            }
            return $dates;
        }
        
        public function getCron($id)
        {
            if($id)
            {
                $rs = $this->db->query("Select * from CronJobs where id = ?", array($id));
                $cron = $rs->row();
                
                $rs = $this->db->query("Select * from CronLog where cron_id = ? order by id DESC", array($id));
                $history = $rs->result();
            }
            else
            {
                $cron = $history = NULL;
            }
            
            //Get the dropdown information
            $rs = $this->db->query("Select * from Routines where name like 'Cron%' order by link");
            $links = $rs->result();
            
            $minutes = array("*");
            for($i = 0; $i <  60; $i++)
                $minutes[] = $i;
            
            $hours = array("*");
            for($i = 0; $i <  24; $i++)
                $hours[] = $i;
            
            $days_of_month = array("*");
            for($i = 0; $i <  31; $i++)
                $days_of_month[] = $i;
            
            $months = array(0 => "*", 1 => "Jan", 2 => "Feb", 3 => "Mar", 4 => "Apr", 5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Aug", 9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dec");
            
            $days_of_week = array(0 => "*");
            for($i = 0; $i < 7; $i++)
                $days_of_week[$i + 1] = jddayofweek ($i, 1);
            
            return compact('cron', 'history', 'minutes', 'hours', 'links', 'days_of_month', 'months', 'days_of_week');
        }
        
        //------ TICKET SECTION       
        public function archiveTickets()
        {            
            $ret = array();
            $rs = $this->db->query("Select sweepstakesId, sum(count) as cnt From TicketCompressed
                where sweepstakesId in (Select distinct id from Sweepstakes where endDate < now() - INTERVAL 2 DAY)
                Group by sweepstakesId");
            
            if($rs->num_rows())
                $ret['info'] = $rs->result();
            
            $rs = $this->db->query("Insert into TicketCompressedArchive (playerId, gameToken, sweepstakesId, count, dateCreated, updated)
                Select playerId, gameToken, sweepstakesId, count, dateCreated, updated
                From TicketCompressed
                where sweepstakesId in (Select distinct id from Sweepstakes where endDate < now() - INTERVAL 2 DAY)");
            
            $this->db->query("Delete From TicketCompressed where sweepstakesId in (Select distinct id from Sweepstakes where endDate < now() - INTERVAL 2 DAY)");
            
            $ret['success'] = true;
            return $ret;
        }
        
        public function fixTickets($id)
        {
            $rs = $this->db->query("Select * from TicketAggregate where sweepstakesId = ?", array($id));
            $ticket_agg = $rs->result();
            $recs = array();
            foreach($ticket_agg as $player)
            {
                for($i = 0; $i < $player->ticketCount; $i++)
                {
                    $recs[] = array('playerId' => $player->playerId, 'gameToken' => 'Recovered from Aggregate', 'dateCreated' => date("Y-m-d H:i:s"), 'isIssued' => 1, 'sweepstakesId' => $id, 'updated' => date("Y-m-d H:i:s"));
                }                
                $this->db->insert_batch("Tickets", $recs);
                $recs = array();
            }
        }
        
        public function bezier($info)
        {
            $rs = $this->db->query("Select * from MapStates order by stateId");
            foreach($rs->result() as $state)
            {
                $bezier = array();
                $spots = json_decode($state->Spots);
                foreach($spots as $spot)
                {
                    if(isset($spot->day))
                    {
                        if(!isset($info[$spot->day]))
                            continue;
                        $temp = $info[$spot->day];
                        $curve = new stdClass();
                        $curve->x1 = $temp['x1'] - ($state->panelColumn * 960);
                        $curve->y1 = $temp['y1'] - ($state->panelRow * 720);
                        $curve->x2 = $temp['x2'] - ($state->panelColumn * 960);
                        $curve->y2 = $temp['y2'] - ($state->panelRow * 720);
                        $curve->day = $spot->day;
                        $bezier[] = $curve;
                    }
                }
                if($bezier)
                {
                    print_r($bezier);
                    $this->db->where("id", $state->id);
                    $this->db->update("MapStates", array("bezierCurveSpots" => json_encode($bezier)));
                }
            }
        }
        
        // ------- MEMCACHE SECTION
        public function getMemRedis()
        {         
            $db = $this->load->database ('default', true);
            $rs = $db->query("Select * from Scratch_GPGames;");
            $cards = array();
            foreach($rs->result() as $row)
                $cards[$row->Name] = $row->SerialNumber;
            
            $m = new Memcached();
            $keys = array();
            if($m)
            {
                $m->addServer("localhost", 11211);
                $keys = $m->getAllKeys();
            }
            
            return compact("cards", "keys");
        }
                
        public function memcacheCheck($key)
        {
            $token = '';
            $message = "";
            if(!$this->getAuthToken($token, $message))
                return json_encode(array('success' => false, 'message' => $message));
            $header = array(getenv("APIKEY"), "TOKEN: " . $token);
            $url = getenv("APISERVER") . "api/debug/verifyMemCacheKey/" . urlencode($key);
            $ch = curl_init($url);
            //curl_setopt($ch, CURLOPT_POST, true);            
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $info = json_decode(curl_exec($ch), true);
            
            if(isset($info['message']))
            {
                if($info['message'])
                    return json_encode(array('success' => true, 'message' => print_r($info['message'], true)));
                else
                    return json_encode(array('success' => true, 'message' => 'Key Not Found for: ' . $key));
            }
            return json_encode(array('success' => false, 'message' => 'Unable to connect to API Function'));
        }
        
        public function memcacheScratcher($key)
        {
            $token = "";
            $message = "";
            if(!$this->getAuthToken($token, $message))
                json_encode(array('success' => false, 'message' => $message));

            $url = getenv("APISERVER") . "api/scratchcards/killkey/" . $key;
            $header = array(getenv("APIKEY"), "TOKEN:" . $token);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $info = json_decode(curl_exec($ch), true);
            if(isset($info['error']))
            {
                return json_encode(array('success' => false, 'message' => "Problem with resetting memcache" . print_r($info, true)));                
            }
            
            return json_encode(array('success' => true, 'message' => print_r($info, true)));
        }
        
        public function memcacheDeleteLocal($key)
        {
            $m = new Memcached();
            if($m)
            {
                $m->addServer("localhost", 11211);
                if($key == "all")
                    $m->flush ();
                else
                    $m->delete ($key);
            }
            return true;
        }
        
        public function memcacheGetLocal($key)
        {
            $m = new Memcached();
            if($m)
            {
                $m->addServer("localhost", 11211);                
                return print_r($m->get ($key), true);               
            }
            return false;
        }
        
        public function memcacheDelete($key)
        {
            $message = "";
            if(!$this->getAuthToken($token, $message))
                return json_encode(array('success' => false, 'message' => $message));
                        
            $header = array(getenv("APIKEY"), "TOKEN: " . $token);
            $url = getenv("APISERVER") . "api/debug/removeMemCacheKey/" . urlencode($key);
            $ch = curl_init($url);
            //curl_setopt($ch, CURLOPT_POST, true);            
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $info = json_decode(curl_exec($ch), true);
            
            if(isset($info['message']))
            {
                return json_encode(array('success' => true, 'message' => $info['message']));
            }
            return json_encode(array('success' => false, 'message' => 'Unable to connect to API Function'));
        }
        
        public function getAuthToken(&$token, &$message)
        {
            $test_api = getenv("APISERVER"); //"http://local.chefapi.com/index.php/";
            $url = $test_api . "api/players/login";
            $header = array(getenv("APIKEY"));
            $data = array('email' => 'admin@kizzang.com', 'password' => getenv("ADMINPASSWORD"), 'appId' => 'v2677', 'isRegistration' => 0, 'deviceId' => '1', 'isContinue' => 0, );
                        
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $info = json_decode(curl_exec($ch), true);
            
            if(isset($info['error']))
            {
                $message = "Unable to get Auth Token";
                return false;
            }
            
            if(!isset($info['token']))
                return false;
            
            $token = $info['token'];
            return true;
        }                
        
        public function compileWins($type, $id)
        {
            $rows = array();
            $file = "";
            if($type == "FT")
            {
                $rs = $this->db->query("Select * from FinalAnswers where finalConfigId = ?", array($id));
                $rows = $rs->result_array();
            }
            
            if($rows)
            {
                $cols = array();
                foreach($rows[0] as $key => $value)
                    $cols[] = $key;
                $file = implode("\t", $cols) . "\n";
                foreach($rows as $row)
                    $file .= implode ("\t", $row) . "\n";
            }
            return $file;
        }
        
        public function transferFiles()
        {
            $this->load->library('s3');
            $rs = $this->db->query("Select * from GameRules where serialNumber <> 'TEMPLATE'");
            $rows = $rs->result();
            foreach($rows as $row)
            {
                if(strstr($row->ruleURL, "/" .getenv("ENV"). "/"))
                        continue;
                $new_url = str_replace("game_rules/",getenv("ENV"). "/game_rules/", $row->ruleURL);
                $text = file_get_contents(str_replace("https://d23kds0bwk71uo.cloudfront.net", "https://kizzang-legal.s3.amazonaws.com",$row->ruleURL));
                
                $cur_file = $row->serialNumber . ".txt";
                $filename = rand(1, 10000000);
                $fh = fopen("/tmp/" . $filename, "w");
                fwrite($fh, $text);
                fclose($fh);
            
                $this->s3->putObjectFile("/tmp/" . $filename, 'kizzang-legal', getenv("ENV"). '/game_rules/' . $cur_file, 'public-read');
                $this->db->where("id", $row->id);
                $this->db->update("GameRules", array("ruleURL" => $new_url));
            }
        }
        
        public function query($data)
        {
            if(!isset($data['db']) || !isset($data['query']))
            {
                return "Invalid Variables";                
            }
            
            $db = NULL;
            switch($data['db'])
            {
                case 'main': $db = $this->load->database ('admin', true); break;
                case 'slots': $db = $this->load->database ('slots', true); break;
                case 'scratcher': $db = $this->load->database ('default', true); break;                
            }
            $output = $data['out'];
            
            if(!$db)            
               return "Invalid DB Selection";                                  
            
            if(stristr($data['query'], ";"))
                $queries = explode (";", $data['query']);
            else
                $queries[] = $data['query'];
            $db->query("SET group_concat_max_len=1500000");
            $db->query("Set time_zone = 'America/Los_Angeles'");
            foreach($queries as $query)
            {
                if(!$query)
                    continue;
                                
                $rs = $db->query($query);
                if($db->_error_number())
                    return $db->_error_message();
                
                if(preg_match("/^select/i", $query) || preg_match("/^show/i", $query) || preg_match("/^desc/i", $query) || preg_match("/^explain/i", $query))
                {
                    if($rs->num_rows())
                    {
                        $query = $data['query'];
                        $out = $data['out'];
                        $db = $data['db'];
                        $message = "";
                        $data = $rs->result_array();
                        $cols = array();
                        foreach($data[0] as $key => $value)
                            $cols[] = $key;

                        return compact('data', 'cols', 'db', 'out', 'query', 'message');
                    }
                    else
                    {
                        return "No Records Found for: " . $query;
                    }
                }
            }            
            return false;
        }
        
        public function getConfigs($base = "_dev/0/main-app/global")
        {            
            $this->load->library('s3');            
            $rows = $this->s3->getBucket('kizzang-resources', $base);
            $ret = array();
            
            foreach($rows as $row)
            {
                $obj = array();
                $obj['url'] = $row['name'];
                $path = explode("/", $row['name']);
                $obj['file'] = $path[count($path) - 1];
                $ret[] = $obj;
            }
            return $ret;
        }
        
        public function replaceS3()
        {
            $this->load->library('s3');  
            $file = "";
            $fp = fopen("/tmp/ScratchCardTempList_v3.xml", "r");
            while($temp = fgets($fp, 4096))
                    $file .= $temp;
            fclose($fp);
            print $file; die();
            for($i = 1200; $i < 2000; $i++)
            {
                $rows = $this->s3->getBucket('kizzang-resources', "_prod/" . $i . "/main-app/global");
                if(!$rows)
                    continue;
                
                $data = array('text' => $file, 'bucket' => 'kizzang-resources', 'file' => "_prod/" . $i . "/main-app/global/ScratchCardTempList_v3.xml");
                $message = "";
                $this->saveConfig($data, $message);
            }
            $ret = array();
            
            foreach($rows as $row)
            {
                $obj = array();
                $obj['url'] = $row['name'];
                $path = explode("/", $row['name']);
                $obj['file'] = $path[count($path) - 1];
                $ret[] = $obj;
            }
            print_r($ret); die();
            return $ret;
        }
        
        public function saveConfig($data, &$message)
        {
            $extension = substr($data['file'], strrpos($data['file'], ".") + 1);
            $temp_file = "/tmp/" .  rand(1, 10000000) . "." . $extension;
            $fp = fopen($temp_file, "w+");
            fwrite($fp, $data['text']);
            fclose($fp);
            
            switch(strtolower($extension))
            {
                case "xml":
                    $xml = new DOMDocument;
                    $xml->Load($temp_file);
                    break;
            }
            $this->load->library('s3');
            $this->s3->putObject($data['text'], $data['bucket'], $data['file']);
            
            $this->invalidateCloudfrontFiles($data['bucket'], array($data['file']));
            $message = "File Saved";
            return true;
        }
        
        public function getConfigFile($file, $bucket = 'kizzang_resources')
        {
            $this->load->library('s3');
            $file_s3 =  $this->s3->getObject($bucket, $file);            
            return $file_s3->body;
        }
                       
        //Cron Functions
        
        public function updateEL()
        {
            $recs = array();
            $rs = $this->db->query("Select id, data from EventNotificationsLog where playerId = 0 and data like '%playerId%' limit 10000");
            if($rs->num_rows())
            {
                foreach($rs->result() as $row)
                {
                    $data = json_decode($row->data);
                    if(isset($data->playerId))
                        $recs[$data->playerId][] = $row->id;
                    else
                    {
                        if(preg_match("/playerId\"\:\"([0-9]+)/", $row->data, $matches))
                        {
                            $recs[$matches[1]][] = $row->id;
                        }
                    }
                }
            }
            
            foreach($recs as $playerId => $rec)
            {
                $this->db->query("Update EventNotificationsLog set playerId = $playerId where id in (" . implode(",", $rec) . ")" );
            }
            return true;
        }
        
        public function getEventNotificationInfo()
        {
            $users = array('All', 'Web', 'iOS', 'Android', 'Engineers');
            $current_date = date("Y-m-d H:i:s");
            $rs = $this->db->query("Select id,name from Sponsor_Campaigns where type = 3 and Active = 1 order by name");
            $daily_coupons = $rs->result();
            return compact('users','current_date','daily_coupons');
        }
        
        public function addEventNotification($data)
        {
            $recs = array();
            $rec = array('added' => date("Y-m-d H:i:s"), 'type' => 'notice', 'pending' => 1, 'expireDate' => $data['end_date'], 'data' => json_encode(array('title' => $data['title'], 'description' => $data['description'])));
            $ids = array();
            switch($data['users'])
            {
                case 'Engineers': $ids = array(107, 5, 46); break;
                case 'All': 
                    $rs = $this->db->query("Select id from Users");
                    foreach($rs->result() as $row)
                        $ids[] = $row->id;
                    break;
                case 'iOS': 
                    $rs = $this->db->query("Select distinct player_id as id from notifications.players where device_type = 'iOS'");
                    foreach($rs->result() as $row)
                        $ids[] = $row->id;
                    break;
                case 'Android': 
                    $rs = $this->db->query("Select distinct player_id as id from notifications.players where device_type = 'Android'");
                    foreach($rs->result() as $row)
                        $ids[] = $row->id;
                    break;
                case 'Web': 
                    $rs = $this->db->query("Select * from Users where id not in (Select distinct player_id as id from notifications.players)");
                    foreach($rs->result() as $row)
                        $ids[] = $row->id;
                    break;
            }
            foreach($ids as $id)
            {
                $rec['playerId'] = $id;
                $recs[] = $rec;
            }
            
            $this->db->insert_batch('EventNotifications', $recs);
            admin_model::addAudit($this->db->last_query(), "admin_model", "addEventNotifications");
            return count($ids);
        }
        
        public function clearEventNotifications()
        {
            $ret = array();
            //Get all notices that have been completed and archive them
            $rs = $this->db->query("Select * from EventNotifications where pending = 0 limit 10000");            
            if($rs->num_rows())
            {
                $ids = array();
                foreach($rs->result() as $row)
                {
                    $ids[] = $row->id;
                    $logs[] = array('eventNotificationId' => $row->id,
                        'type' => $row->type,
                        'data' => trim($row->data, "}") . ',"playerId":"' . $row->playerId . '"}',
                        'playerId' => $row->playerId,
                        'playerActionTaken' => $row->playerActionTaken,
                        'updated' => $row->updated ? $row->updated : date('Y-m-d H:i:s'));                    
                }
                $this->db->insert_batch('EventNotificationsLog', $logs);
                $ret['used']['count'] = $rs->num_rows();
                $ret['used']['ids'] = $ids;
                $this->db->query("Delete from EventNotifications where id in ('" . implode("','", $ids) . "')");
            }
            
            $rs = $this->db->query("Select * from EventNotifications where pending = 1 AND expireDate IS NOT NULL AND expireDate < convert_tz(now(), 'GMT', 'US/Pacific')");
            if($rs->num_rows())
            {
                $this->load->config( 'aws/config' );
                $ids = array();
                foreach($rs->result() as $row)
                {
                    $ids[] = $row->id;
                    $log = array('eventNotificationId' => $row->id,
                        'type' => $row->type,
                        'data' => trim($row->data, "}") . ',"playerId":"' . $row->playerId . '"}',
                        'playerActionTaken' => $row->playerActionTaken,
                        'updated' => $row->updated ? $row->updated : date('Y-m-d H:i:s'));
                    $this->db->insert('EventNotificationsLog', $log);
                }
                $ret['expired']['count'] = $rs->num_rows();
                $ret['expired']['ids'] = $ids;
                $this->db->query("Delete from EventNotifications where id in ('" . implode("','", $ids) . "')");
            }
            
            $rs = $this->db->query("Select w.*, u.email from Winners w
                Inner join Users u on u.id = w.player_id
                where w.status in ('Document', 'New') and w.expirationDate < convert_tz(now(), 'GMT', 'US/Pacific')");
            if($rs->num_rows())
            {
                foreach($rs->result() as $row)
                {
                    $body = $this->load->view('emails/wrapper', array('emailCode' => md5($row->email), 
                        'content' => $this->load->view('emails/expired', array('prize' => $row->prize_name, 'game' => $row->game_name, 'serialNumber' => $row->serial_number, 'winnerId' => $row-id), true)), true);
                    $this->sendGenericEmail($row->email, "Prize Expired - Kizzang", $body, "winners@kizzang.com");
                    $this->db->query("Update Winners set status = 'Expired' where id = ?", array($row->id));
                }
            }
            
            $ret['success'] = true;
            return $ret;
        }
        
        //User Functions
        public function verifyLogin($data, &$message)
        {
            $message = "";
            if(!isset($data['email']) || !isset($data['password']))
            {
                $message = "Invalid Parameters";
                return false;
            }          
            
            $rs = $this->db->query("Select * from Users where accountName = ? and passwordHash = ?", array($data['email'], md5($data['password'])));
            if(!$rs->num_rows())
            {
                $message = "Invalid Email / Password Combo" . print_r(array(md5($data['email']), md5($data['password'])), true);
                return false;
            }
            
            $temp = $rs->row();
            $player = $this->getPlayer($temp->id);
            $user = $player['player'];
            $this->nativesession->set('User', $user);
            //Get all ACLs for the user
            
            $rs = $this->db->query("Select DISTINCT r.* 
                from Routines r
                Inner join Routine_Groups rg on r.id = rg.routine_id
                Inner join Player_Groups pg on rg.group_id = pg.group_id and pg.player_id = ?", array($user['id']));
            if(!$rs->num_rows())
            {
                $message = "No Valid ACLs.  If you think you should have access contact the admin.";
                return false;
            }
            $acls = array();
            foreach($rs->result() as $acl)
                $acls[] = $acl->link;
            
            $nav = array();
            $links = array('Players' => 'admin/players', 
                'Map' => 'admin/map_sponsor_campaign', 
                'ScratchCards' => 'admin/view_games',
                'Sweepstakes' => 'admin_sweepstakes/index',
                'Slots' => 'admin_slots/index',
                'Parlay' => 'admin_sports/view_parlay',
                'BG21' => 'admin_sports/view_bg_configs',
                'Brackets' => 'admin_sports/view_brackets',
                'FT' => 'admin_sports/view_ft',
                'Strings' => 'admin/view_strings',
                'Sponsors' => 'admin/view_sponsor_campaigns',
                'Winners' => 'admin/pick_winners',
                'Wheel' => 'admin/view_wheels',
                'Payments' => 'payment/index',
                'Admin' => 'admin/view_event_notifications',
                'Marketing' => 'admin_marketing/view',
                'Reports' => 'admin_reports/slots');
            
            foreach($links as $key => $link)
                if(in_array($link, $acls))
                    $nav[] = $key;
                
            $this->nativesession->set('ACLs', $acls);
            $this->nativesession->set('Nav', $nav);
            $message = $this->validatePasswordStrength($data['password']);
            return true;
        }
        
        public function changePassword($password, &$message)
        {
            $message = $this->validatePasswordStrength($password);
            if(!$message)
            {
                $user = $this->nativesession->get('User');
                $this->db->where('id', $user['id']);
                $this->db->update('Players', array('passwordHash' => md5($password)));
                return true;
            }
            return false;
        }
        
        private function validatePasswordStrength($password)
        {
            $error = "";
            if(strlen($password) < 8)
                $error .= "Password needs to be greater than 8 characters\n";
            if(!preg_match("/[A-Z]+/", $password))
                $error .= "Password needs to have 1 uppercase character\n";
            if(!preg_match("/[a-z]+/", $password))
                $error .= "Password needs to have 1 lowercase character\n";
            if(!preg_match("/[0-9]+/", $password))
                $error .= "Password needs to have 1 number\n";
            return $error;
        }
                        
        public function logout()
        {
            $this->nativesession->delete("User");
            $this->nativesession->delete("ACLs");
            $this->nativesession->delete("Nav");
        }
        
        public function permission_validation()
        {            
            $ACLs = $this->nativesession->get('ACLs');
            //print_r($ACLs);
            if($this->input->is_cli_request())
                return true;
            
            $url = strtolower($this->router->class . "/" . $this->router->method);
            if(!count($ACLs))
                $this->nativesession->set("redirect", $url);
            
            if($url == "admin/login" || $url == "admin/logout" || (count($ACLs) && in_array($url, $ACLs)))
                    return true;
            
            if($url == "admin/index")
            {
                redirect("/admin/login");
                exit();
                return false;
            }
            
            if(!$ACLs)
            {
                $this->nativesession->set("access_error", "Your access doesn't allow you to go to " . $url);
                redirect("/admin");
            }
            else
            {
                if(in_array($url, $ACLs))
                    $this->nativesession->set("redirect", $url);
                else
                    $this->nativesession->set("access_error", "Your access doesn't allow you to go to " . $url);
                redirect("/admin/login");
            }
            exit();
            return false;
        }
        
        //All Sweepstakes functions
        function getSweepstakes($type)
        {
            $m = new Memcached();
            $m->addServer("localhost", 11211) or die("Blah");
            
            $mkey = "Sweepstakes-$type";
            if($m)
                if($info = $m->get($mkey))
                    return $info;
            
            $ret = array();
            $where = "";
            switch ($type)
            {
                case 'current': $where = "WHERE convert_tz(now(), 'GMT', 'US/Pacific') between startDate AND endDate"; break;
                case 'past': $where = "WHERE convert_tz(now(), 'GMT', 'US/Pacific') > endDate"; break;
                case 'future': $where = "WHERE convert_tz(now(), 'GMT', 'US/Pacific') < startDate"; break;
            }
            $rs= $this->db->query("Select s.id, name, description, concat(DATE_FORMAT(startDate, '%a %b %e %Y'), ' to ', DATE_FORMAT(endDate, '%a %b %e %Y')) as dates, imageUrl, 
                    titleImageUrl, sweepstakeType, count(t.created) as num_entries 
                    from Sweepstakes s
                    Left join Tickets t on s.id = t.sweepstakesId
                    $where
                    Group by s.id");
            
            foreach($rs->result() as $row)
                    $ret[] = $row;
            
            if($m)
                $m->set($mkey, $ret, 30);
            
            return $ret;
        }                
        
        function getSweepstake($id)
        {
            $ret = array();
            $rs= $this->db->query("Select * from Sweepstakes where id = ?", array($id));
            $sweepstakes = $rs->row();        
            $sweepstakes->serialNumber = sprintf("KW%05d", $sweepstakes->id);
            $rs = $this->db->query("Select * from GameRules where serialNumber = 'TEMPLATE' and gameType = 'Sweepstakes'");
            $rules = $rs->result();
            $rs = $this->db->query("Select * from GameRules where serialNumber = ?", array($sweepstakes->serialNumber));
            if($rs->num_rows())
                $rule = $rs->row();
            else
                $rule = NULL;
            if($rule)
            {
                $rule = $rs->row();
                $text = file_get_contents(str_replace("https://d23kds0bwk71uo.cloudfront.net", "https://kizzang-legal.s3.amazonaws.com", $rule->ruleURL));
                
                $rule->text = $text;
                if(count($rules))
                    $rule->template = file_get_contents(str_replace("https://d23kds0bwk71uo.cloudfront.net", "https://kizzang-legal.s3.amazonaws.com",$rules[0]->ruleURL));
                else
                    $rule->template = "";
            }
            return compact("rules", "sweepstakes", "rule");
        }
        
        function getSweepstakesWinner($id)
        {
            $winner = array();
            $rs = $this->db->query("Select p.ScreenName, s.name, p.id as PlayerId, st.id as st_id 
                    From Users p
                    Inner join Tickets t on t.playerId = p.id
                    Inner join SweepstakeTickets st on st.ticketId = t.id and st.sweepstakeId = ?
                    Inner join Sweepstakes s on s.id = st.sweepstakeId
                    Order by rand() LIMIT 1", array($id));
            if($rs->num_rows())
            {
                $winner = $rs->row();
                $this->db->where('id', $winner->st_id);
                $this->db->update('SweepstakeTickets', array('isWinner' => 1));
            }
            return $winner;
        }
        
        function saveSweepstakes($data_db)
        {
            $data_db['endDate'] = date("Y-m-d", strtotime($data_db['endDate'])) . " 23:59:59";
            if(isset($data_db['id']))
            {                
                $this->db->where('id', $data_db['id']);
                $this->db->update('Sweepstakes', $data_db);
                $id = $data_db['id'];
            }
            else
            {
                $this->db->insert('Sweepstakes', $data_db);
                $id = $this->db->insert_id();
            }
            admin_model::addAudit($this->db->last_query(), "admin_model", "saveSweepstakes");
            return $id;
        }
        
        function deleteSweepstakes($id)
        {
            $this->db->delete('Sweepstakes', array('id' => $id));
            admin_model::addAudit($this->db->last_query(), "admin_model", "deleteSweepstakes");
            return true;
        }
        
        //All Winner Functions
        function getWinners($type)
        {
            $rs = $this->db->query("Select w.*, convert_tz(w.created, 'GMT', 'US/Pacific') as createdDate 
                From Winners w
                Where player_id <> 0 and status = ?
                Order by created DESC LIMIT 100", array($type));
            
            if(!$rs->num_rows())
                return array();
            
            $winners = array();
            foreach($rs->result() as $row)
            {
                $temp = $this->getPlayer($row->player_id);
                $row->player = $temp['player'];
                $winners[] = $row;
            }
            return $winners;
        }
        
        function add_manual_winner($data, &$error)
        {
            //Validate the data
            $dArray = array();
            if(!isset($data['playerId']) || !is_numeric($data['playerId']))            
                $error .= "Player ID either not set or not numeric\n";            
            
            if(!isset($data['data']) || !is_array($data['data']))
            {
                $error .= "Data not set.\n";
            }
            else
            {                
                $dArray = array_merge(array('serialNumber' => '', 'prizeAmount' => '', 'prizeName' => '', 'gameName' => ''), $data['data']);
                foreach($dArray as $key => $value)
                {
                    switch($key)
                    {
                        case 'serialNumber':
                            if(!$value || strlen($value) !== 7)
                                $error .= "Invalid SerialNumber.\n";
                            break;
                        
                        case 'prizeAmount':
                            if(!$value || !is_numeric($value))
                                $error .= "Invalid Price Amount.\n";
                            break;
                            
                        case 'prizeName':
                            if(!$value)
                                $error .= "Invalid Prize Name.\n";
                            break;
                                                    
                    }
                }
            }
            
            if($error)
                return false;
            
            $xlat = array('scratchCard' => 'Scratchers', 'slotTournament' => 'Slots', 'sweepstakes' => 'Sweepstakes', 'dailyShowdown' => 'Parlay', 'finalThree' => 'FT', 'bigGame' => 'BG');
            $winner = array('player_id' => $data['playerId'], 'game_type' => $xlat[$data['type']], 'serial_number' => $dArray['serialNumber'], 'prize_name' => $dArray['prizeName'],
                'prize_email' => $dArray['prizeName'], 'amount' => $dArray['prizeAmount'], 'processed' => 0, 'expirationDate' => date('Y-m-d H:i:s', strtotime("+1 day")), 'game_name' => $dArray['gameName']);
            
            $this->db->insert('Winners', $winner);
            $id = $this->db->insert_id();
            
            $dArray['entry'] = $id;
            $notification = array('playerId' => $data['playerId'], 'type' => $data['type'], 'data' => json_encode($dArray), 'pending' => 1, 'expireDate'=> date('Y-m-d H:i:s', strtotime("+1 day")));
            $this->db->insert('EventNotifications', $notification);
            
            return true;
        }
        
        function get_player_name($id)
        {
            $rs = $this->db->query("Select concat(firstName, ' ', lastName) as name from Users where id = ?", array($id));
            if(!$rs->num_rows())
                return "ID NOT KNOWN";
            return $rs->row()->name;
        }
        
        function validateWinner($id)
        {
            $rs = $this->db->query("Select * from Winners where id = ?", array($id));
            $winner = $rs->row();
            
            $rs = $this->db->query("Select * from Users where id = ?", array($winner->player_id));
            $user = $rs->row();
            
            $rs = $this->db->query("Select * from WinnerQuestions");
            $questions = $rs->result();
            
            $rs = $this->db->query("Select * from WinnerQuestionAnswers where winnerId = ?", array($id));
            $answers = array();
            foreach($rs->result() as $row)
                $answers[$row->questionId] = $row->passed;
            
            $rs = $this->db->query("Select * from WinnerCalls where winnerId = ?", array($id));
            $calls = $rs->result();
            
            $document = NULL;
            $rs = $this->db->query("Select * from rightSignature.documents where playerId = ? and status = 'signed' order by completedDate DESC limit 1", array($winner->player_id));
            if($rs->num_rows())
                $document = $rs->row();
            
            $attachments = NULL;
            if($document)
            {
                $rs = $this->db->query("Select * from rightSignature.attachments where documentId = ?", array($document->id));
                $attachments = $rs->result();
            }
            
            $callResults = $this->getColumnEnum("kizzang", "WinnerCalls", "result");
            $statuses = $this->getColumnEnum("kizzang", "Winners", "status");
            //print_r(compact('winner','questions','answers','calls','callResults','user','document','attachments','statuses')); die();
            return compact('winner','questions','answers','calls','callResults','user','document','attachments','statuses');
        }
        
        public function addValidatedWinner($data)
        {
            foreach($data['WinnerCalls'] as $index => $call)
            {
                if($call['callDate'])
                {
                    $this->db->query("Insert into WinnerCalls (winnerId, sequence, callDate, result) values (?,?,?,?) on duplicate key update callDate = VALUES(callDate), result = VALUES(result)",
                            array($data['id'], $index + 1, $call['callDate'], $call['result']));                    
                }
            }
            
            if(isset($data['question']))
                foreach($data['question'] as $index => $answer)
                    $this->db->query("Insert into WinnerQuestionAnswers (winnerId, questionId, passed) values (?,?,?) on duplicate key update passed = VALUES(passed)", array($data['id'], $index, $answer));
            
            $rec = array('status' => $data['status'], 'comments' => $data['comments']);
            $rs = $this->db->query("Select w.*, u.email from Winners w
                Inner join Users u on u.id = w.player_id
                where w.id = ?", array($data['id']));
            if(!$rs->num_rows())
                return false;
            
            $originalRec = $rs->row();
            $this->db->where(array('id' => $data['id']));
            $this->db->update('Winners', $rec);
            
            $rs = $this->db->query("Select * from Users where id = ?", array($data['playerId']));
            $player = $rs->row();
            
            if($data['status'] == 'Approved') //Add in Payment Record
            {
                $rs = $this->db->query("Select * from Payments where winnerId = ?", array($data['id']));
                if(!$rs->num_rows())
                {                    
                    
                    $rs = $this->db->query("Select * from Winners where id = ?", array($data['id']));
                    $winner = $rs->row();
                    
                    $rec = array('winnerId' => $winner->id, 'playerId' => $player->id, 'amount' => $winner->amount, 'prizeName' => $winner->prize_name, 'status' => 'Unpaid', 'firstName' => $player->firstName, 'lastName' => $player->lastName,
                        'email' => $player->email, 'phone' => $player->phone, 'address' => $player->address, 'city' => $player->city, 'state' => $player->state, 'zip' => $player->zip, 'payPalEmail' => $player->payPalEmail, 'serialNumber' => $winner->serial_number);
                    
                    $this->db->insert('Payments', $rec);
                }
            }
            elseif($data['status'] == 'Denied' && $originalRec->status != "Denied")
            {
                $body = $this->load->view("/emails/wrapper",  array('emailCode' => md5($player->email), 'content' => $this->load->view("/emails/prizeForfeited", array('game' => $originalRec->game_name, 'prize' => $originalRec->prize_name, 
                    'serialNumber' => $originalRec->serial_number, 'winnerId' => $originalRec->id), true)), true);
                $this->sendGenericEmail($originalRec->email, 'Kizzang Prize Forfeited', $body);
            }
            
            return true;
        }
                
        public function processPayments()
        {
            $ret = array();
            $rs = $this->db->query("Select * from Winners where processed = 0 AND player_id <> 0 AND game_type <> 'Parlay'");
            $winners = $rs->result();
            $message = "";
            foreach($winners as $winner)
            {
                $this->processPayment($winner->id, $message);
                $ret[$winner->id]['message'] = $message;
                $ret[$winner->id]['rec'] = $winner;
            }
            $ret['success'] = true;
            return $ret;
        }
              
        public function getSiteUrl()
        {
            $url = "kizzang.com";
            switch(getenv("ENV"))
            {
                case "dev": $url = "dev.kizzang.com"; break;
                case "stage": $url = "qa.kizzang.com"; break;
                case "prod": $url = "kizzang.com"; break;
            }
            return $url;
        }
        
        public function processPayment($id, &$message)
        {
            /*
             * 3 Steps
             * 1. Send information to the payment server
             * 2. Send out email to the actual party
             */
           
            date_default_timezone_set('America/Los_Angeles');
            $types = array('Slots' => 'slotTournament', 'FT' => 'finalThree', 'BG' => 'bigGame', 'Parlay' => 'dailyShowdown', 'Sweepstakes' => 'sweepstakes');
            $api_key = getenv("APIKEY");
            
            $this->load->config( 'aws/config' );            
            $rs = $this->db->query("Select * from Winners where id = ? and processed = 0 and status = 'Claimed'", array($id));
            if(!$rs->num_rows())
            {
                $message = "Invalid Winner!";
                return false;
            }
            $winner = $rs->row();
            
            if(!isset($types[$winner->game_type]))
            {
                $message = "Win not handled by admin";
                return false;
            }
            
            $previous_win = 0;
            $rs = $this->db->query("Select sum(amount) as sm from Winners where processed = 1 and player_id = ? and YEAR(created) = ?", array($winner->player_id, date('Y')));
            if($rs->num_rows())            
                $previous_win = $rs->row()->sm;            
            
            $win = $winner->amount;
            if($previous_win < 600 && $previous_win + $win >= 600)  //Crossing over the $600 limit
                $win += $previous_win;
            
            //Get game expiration dates
            $rs = $this->db->query("Select * from GameExpireTimes where game = ? and ? between lowAmount and highAmount LIMIT 1", array($types[$winner->game_type], $win));
            if(!$rs->num_rows())
            {
                $message = "No Valid paytime schedule defined";
                return false;
            }
            
            $time = $rs->row();
 
            $expiration_date = date('m-d-Y H:i:s', strtotime("+" . $time->numMinutes . " minutes"));
            $exp_date = date("Y-m-d H:i:s", strtotime("+" . $time->numMinutes . " minutes"));
                                    
            $data = array('serialNumber' => $winner->serial_number, 'entry' => (int)$winner->id, 'prizeAmount' => $winner->amount, 
                'prizeName' => $winner->prize_name, 'gameName' => $winner->game_name);
            
            if($winner->game_type == "Slots")
            {
                $slots = $this->load->database("slots", true);
                $rs = $slots->query("Select type from SlotTournament where ID = ?", array($winner->foreign_id));
                if($rs->num_rows())
                {
                    $row = $rs->row();
                    $data['tournamentType'] = $row->type;
                }               
            }
                       
            return true;
        }              
        
        public function fixWheelNotifications(&$message)
        {
            $token = "";
            $message = "";
            if(!$this->getAuthToken($token, $message))
                return false;
            
            $url = getenv("APISERVER") . "api/debug/recoverEvents";
            $header = array(getenv("APIKEY"), "TOKEN: " . $token);            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $info = json_decode(curl_exec($ch), true);
            if(!$info || isset($info['error']))
            {
                $message = "Error Calling recoverEvents: " . print_r($info, true);
                return false;
            }
            return true;
        }
        
        public function getLeaderBoard()
        {
            $rs = $this->db->query("Select * from LeaderBoards order by date DESC");
            return $rs->result();
        }
        
        public function deleteLeaderBoardEntry($id)
        {            
             $this->db->delete("LeaderBoards", array("id" => $id));
             admin_model::addAudit($this->db->last_query(), "admin_model", "deleteLeaderBoardEntry");
             return true;
        }
        
        public function getPastWinners($date)
        {
            $rs = $this->db->query("Select w.*, p.screenName
                From Winners w
                Inner join Users p on p.id = w.player_id
                Where processed = 1 and  date(created) = ?
                Order by created DESC LIMIT 100", array($date));
            
            return $rs->result();
        }
        
        public function updateWinodometer($value)
        {
            $this->db->update('WinOdometer', array('currentAmount' => $value));
            admin_model::addAudit($this->db->last_query(), "admin_model", "updateWinodometer");
            return true;
        }
        public function updateLeaderBoard()
        {            
            $rs = $this->db->query("SELECT p.id, amount 
                FROM Users p
                Inner join (Select player_id, sum(amount) as amount from Winners group by player_id) b on b.player_id = p.id
                Where fbId IS NOT NULL AND fbid <> ''
                Order by amount DESC limit 15;");
            if($rs->num_rows())
            {
                $this->db->query("Truncate LeaderBoards");
                foreach($rs->result() as $person)
                {
                    $temp = $this->getPlayer($person->id);
                    $player = $temp['player'];
                    if(!$player['fbId'])
                        continue;
                    
                    $url = json_decode(file_get_contents('https://graph.facebook.com/v2.2/' . $player['fbId'] . '/picture?width=140&height=140&redirect=false'), true);
                    //print_r($url);
                    if(isset($url['data']['url']))
                    {
                        $stats = getimagesize($url['data']['url']);
                        if(count($stats) && $stats[0] > 138 && $stats[1] > 138)
                        {
                            $rec = array('leaderboardId' => 1,
                                'imageURL' => $url['data']['url'],
                                'location' => trim($player['city'] . ", " . $player['state'], " ,"),
                                'screenName' => $player['screenName'],
                                'prize' => '$' . $person->amount,
                                'date' => date("Y-m-d H:i:s"));
                            $this->db->insert("LeaderBoards", $rec);
                            admin_model::addAudit($this->db->last_query(), "admin_model", "updateLeaderBoard");
                        }
                    }
                }
                return true;
            }
            return false;
        }
        
        public function pickWinnersAll()
        {
            $ret = array();
            $types = array('Sweepstakes', 'Slots', 'Parlay', 'Lottery');
            $recs = array();
            foreach($types as $type)
            {
                $recs = $this->pickWinners ($type);
                if(!count($recs))
                    continue;
                
                $message = "";
                $recs = json_decode(json_encode($recs), true);
                foreach($recs as $index => $rec)
                {
                    switch($type)
                    {
                        case 'Sweepstakes': $this->pickSweepstakes($rec, $message); break;
                        case 'Parlay': $this->pickParlay($rec, $message); break;
                        case 'FT': $this->pickFT($rec, $message); break;
                        case 'BG': $this->pickBG($rec, $message); break;
                        case 'Slots': $this->pickSlots($rec, $message); break;
                        case 'Lottery': $this->pickLottery($rec, $message); break;
                    }
                    $ret[$type][] = array('message' => $message, 'id' => $rec['id']);
                    $message = "";                    
                }
            }
            $ret['success'] = true;
            return $ret;
        }
        
        public function pickWinners($type)
        {
            if($type == "Sweepstakes")
            {
                $rs = $this->db->query("Select s.*, count(t.playerId) as num_entries 
                    from Sweepstakes s
                    Inner join Tickets t on t.sweepstakesId = s.id
                    where s.id not in (Select DISTINCT foreign_id from Winners where game_type= 'Sweepstakes') and endDate < convert_tz(now(), 'GMT', 'US/Pacific')
                    Group by s.id");

                return $rs->result();
            }
            elseif($type == "Parlay")
            {
                $rs = $this->db->query("Select * from SportParlayConfig where id not in (Select DISTINCT foreign_id from Winners where game_type = 'Parlay') and endDate < convert_tz(now(), 'GMT', 'US/Pacific')");
                return $rs->result();
            }
            elseif($type == "BG")
            {
                $rs = $this->db->query("Select * from BGQuestionsConfig where parlayCardId not in (Select DISTINCT foreign_id from Winners where game_type = 'BG') and endDate < convert_tz(now(), 'GMT', 'US/Pacific')");
                return $rs->result();
            }
            elseif($type == "FT")
            {
                $rs = $this->db->query("Select * from FinalConfigs where id not in (Select DISTINCT foreign_id from Winners where game_type = 'FT') and startDate < convert_tz(now(), 'GMT', 'US/Pacific')");
                return $rs->result();
            }
            elseif($type == "Slots")
            {
                $this->db->query("SET SESSION group_concat_max_len = 1000000; ");
                $rs = $this->db->query("Select group_concat(DISTINCT foreign_id) as list from Winners where game_type = 'Slots';");
                $temp = $rs->row();
                $list = $temp->list;
                
                $db = $this->load->database ('slots', true);
                if($list)
                {
                    $rs = $db->query("Select ID as id, StartDate, EndDate, type, PrizeList from SlotTournament where id not in ($list) and EndDate < convert_tz(now(), 'GMT', 'US/Pacific')");
                    return $rs->result();
                }
            }
            elseif($type == "Lottery")
            {
                $rs = $this->db->query("Select * from LotteryConfigs where id not in (Select DISTINCT foreign_id from Winners where game_type = 'Lottery') and endDate < convert_tz(now(), 'GMT', 'US/Pacific')");
                return $rs->result();
            }
            return array();
        }
        
        function pickParlay($data, &$message)
        {
            $parlay_id = $data['id'];
            //Get the config
            $rs = $this->db->query("Select * from SportParlayConfig where parlayCardId = ?", array($parlay_id));
            $config = $rs->row();
            
            $rs = $this->db->query("Select count(*) as cnt from SportParlayCards where parlayCardId = ?", array($parlay_id));
            $card_count = $rs->row()->cnt;
            
            //Get all cards that haven't been processed
            $rs = $this->db->query("Select * from SportPlayerCards where parlayCardId = ?", array($parlay_id));
            
            //Get the answer key                
            $answer = $this->db->query("Select sportScheduleId as id, winner from SportGameResults where parlayCardId = ?", array($parlay_id)); 
            $answers = array();
            
            if(!$answer->num_rows())
            {
                $message = "There are no answers for this parlay card";
                return false;
            }
            
            foreach($answer->result() as $row)
                $answers[$row->id] = $row->winner;
            
            $win_ids = array();
            if($rs->num_rows())
            {                                                                
                foreach($rs->result() as $row)
                {
                    $temp = explode(":", $row->picksHash);
                    $wins = 0;
                    $loses = count($answers) - count($temp); //In there just in case we had to delete an entry because of sport event cancellation
                    foreach($temp as $hash)
                    {
                        $ids = explode("|", $hash);                           
                        if(count($ids) == 2 && isset($answers[$ids[0]]) && $answers[$ids[0]] == $ids[1])
                            $wins++;
                        else
                            $loses++;
                    }
                    $win_ids[$wins][] = $row->id;
                }
            }
            if($win_ids)
            {
                foreach($win_ids as $win => $row)
                {
                    $this->db->query("Update SportPlayerCards set wins = ?, losses = ? where id in (" . implode(",", $row) . ")", array($win, count($answers) - $win));
                }
            }
            //print_r($win_ids); die();
            
            $winners = array();
            $losers = array();
            $limit = 10;
            $offset = 0;
            
            $prizes = array();
            $rs = $this->db->query("Select * from Payouts where gameType = ? order by startRank", array($config->type));
            if(!$rs->num_rows())
            {
                $message = "No Payout Defined";
                return false;
            }

            foreach($rs->result() as $row)
                for($i = $row->startRank; $i <= $row->endRank; $i++)
                    $prize = array('amount' => $row->amount, 'type' => $row->payType);
            
            while(!$losers)
            {
                $rs = $this->db->query("Select pc.*
                    From SportPlayerCards pc 
                    Inner join Users p on p.id = pc.playerId and p.accountStatus = 'Active'
                    Where pc.parlayCardId = ?
                    Order By pc.wins DESC Limit ? Offset ? ", array($parlay_id, $limit, $offset));
                
                if(!$rs->num_rows())
                    break;
                
                foreach($rs->result() as $row)
                {
                    if($row->wins == $card_count)
                        $winners[] = $row;
                    else
                        $losers[] = $row;
                }
                $offset += $limit;
            }
                       
            $winIds = array();
            $message = "";
            if(!$winners && $card_count == count($answers))
            {
                $win_row = array('player_id' => 0,
                        'foreign_id' => $config->id,
                        'ticket_id' => 1,
                        'game_type' => 'Parlay',
                        'serial_number' => sprintf("KP%05d", $config->id),
                        'prize_name' => '$0',
                        'prize_email' => '$0',
                        'amount' => 0,
                        'status' => 'Expired',
                        'processed' => 1);
                $this->db->insert("Winners", $win_row);               
                admin_model::addAudit($this->db->last_query(), "admin_model", "pickParlay");
                $message = "No Winners for this Game";
            }
            elseif($card_count == count($answers))
            {                
                foreach($winners as $winner)
                {
                    $winIds[] = $winner->playerId;
                    $amount = number_format($prize['amount'] / count($winners), 2);
                    $win_row = array('player_id' => $winner->playerId,
                        'foreign_id' => $config->id,
                        'ticket_id' => $winner->id,
                        'game_type' => 'Parlay',
                        'serial_number' => sprintf("KP%05d", $config->id),
                        'prize_name' => '$' . $amount,
                        'prize_email' => '$' . $amount,
                        'game_name' => 'Pick the Board',
                        'amount' => $amount,
                        'processed' => 0,
                        'expirationDate' => date("Y-m-d H:i:s", strtotime("+24 hours")));
                    
                    $temp = $this->getPlayer($winner->playerId);
                    if($temp['player']['accountStatus'] == "Active" && $prize['type'] == "Money")
                    {
                        $this->db->insert("Winners", $win_row);
                        $insertId = $this->db->insert_id();
                        $data = array('serialNumber' => $win_row['serial_number'], 'entry' => $insertId, 'gameName' => $win_row['game_name'], 
                            'tournamentType' => $config->type, 'prizeName' => $win_row['prize_name'], 'prizeAmount' => $amount);
                        $rec = array('playerId' => $win_row['player_id'], 'playerActionTaken' => 0, 'type' => 'dailyShowdown', 'data' => json_encode($data), 'pending' => 1, 
                            'expireDate' => date("Y-m-d H:i:s", strtotime("+24 hours")));
                        $this->db->insert("EventNotifications", $rec);
                        admin_model::addAudit($this->db->last_query(), "admin_model", "pickSlots");
                        $message .= "#1: " . $temp['player']['firstName'] . " " . $temp['player']['lastName'] . " From " . $temp['player']['city'] . "," . $temp['player']['state'] . ' Won $' . $amount . "\n";
                    }
                    elseif($temp['player']['accountStatus'] == "Active" && $prize['type'] == "Chedda")
                    {
                        $description = "You have won " . number_format($prize['amount'] / count($winners), 0) .  " Chedda by getting 1st place in a Parlay Game " . $win_row['serial_number'];
                        $data = array('serialNumber' => $win_row['serial_number'], 'entry' => $win_row['foreign_id'], 'title' => 'Parlay Chedda Win', 'description' => $description, 'chedda' => number_format($prize['amount'] / count($winners)));
                        $rec = array('playerId' => $win_row['player_id'], 'playerActionTaken' => 0, 'type' => 'cheddaEvent', 'data' => json_encode($data), 'pending' => 1, 'expireDate' => date("Y-m-d H:i:s", strtotime("+24 hours")));
                        $this->db->insert("EventNotifications", $rec);
                        admin_model::addAudit($this->db->last_query(), "admin_model", "pickSlots");
                        $message .= "#1: " . $temp['player']['firstName'] . " " . $temp['player']['lastName'] . " From " . $temp['player']['city'] . "," . $temp['player']['state'] . ' Won ' . $amount . " Chedda\n";
                    }
                }
            }    
            else
            {
                $message = "Not all answers have been filled out for this card";
                return false;
            }
            
            //Add in the notifications
            $place = count($winIds) + 1;
            $recs = array();
            $rs = $this->db->query("Select playerId from SportPlayerCards where parlayCardId = ? order by wins DESC", array($config->parlayCardId));
            foreach($rs->result() as $row)
            {
                $place++;
                if(in_array($row->playerId, $winIds))
                    continue;
                
                $winIds[] = $row->playerId;
                $row->rank = $place;
                $row->theme = $config->type;
                $recs[] = $row;
            }
            
            $this->addNotifications("Parlay", $recs);
            return true;
        }
        
        public function pickFT($data, &$message)
        {
            $parlay_id = $data['id'];                        
            
            $rs = $this->db->query("Select * from FinalConfigs where id = ?", array($parlay_id));
            $config = $rs->row();
            
            if(!$config->pickHash)
            {
                $message = "There are no answers for this Final Three";
                return false;                
            }
                        
            //$this->getFTWinners($parlay_id);
            $rs = $this->db->query("Select * from FinalAnswers where wins = 3 and finalConfigId = ? order by delta ASC limit 3", array($parlay_id));
            if($rs->num_rows())
            {
                $prizes = explode("|", $config->prizes);
                foreach($rs->result() as $index => $winner)
                {
                    $win_row = array('player_id' => $winner->playerId,
                        'foreign_id' => $config->id,
                        'ticket_id' => $winner->id,
                        'game_type' => 'FT',
                        'serial_number' => $config->serialNumber,
                        'prize_name' => $prizes[$index],
                        'prize_email' => $prizes[$index],
                        'amount' => trim(str_replace(",", "", $prizes[$index]), '$'),
                        'order_num' => $index + 1,
                        'game_name' => '2016 The Final 3® Pro Football Challenge',
                        'processed' => 0,
                        'expirationDate' => date("Y-m-d H:i:s", strtotime("+24 hours")));
                    $this->db->insert("Winners", $win_row);
                    $player = $this->getPlayer($winner->playerId, true);
                    $message .= $player['firstName'] . " " . $player['lastName'] . " From " . $player['city'] . "," . $player['state'] . ' Won ' . $prizes[$index] . "\n";
                }
            }
            else
            {
                $win_row = array('player_id' => 0,
                    'foreign_id' => $config->id,
                    'ticket_id' => 0,
                    'game_type' => 'FT',
                    'serial_number' => sprintf("KF%05d", $config->id),
                    'prize_name' => 0,
                    'prize_email' => 0,
                    'amount' => 0,
                    'game_name' => 'Final 3',
                    'processed' => 1);
                $this->db->insert("Winners", $win_row);
                $message = "There are no Winners";
            }
            return true;
        }
        
        public function pickBG($data, &$message)
        {
            $parlay_id = $data['id'];
            //Get the config
            $rs = $this->db->query("Select * from BGQuestionsConfig where parlayCardId = ?", array($parlay_id));
            $config = $rs->row();
            
            //Get all cards that haven't been processed
            $rs = $this->db->query("Select * from BGPlayerCards where parlayCardId = ?", array($parlay_id));                        
            
            $temp = explode(":", $config->answerHash);
            foreach($temp as $value)
                $answers[$value] = $value;
            if(!$answers)
            {
                $message = "There are no answers for this Big Game 21";
                return false;
            }
            
            $win_ids = array();
            if($rs->num_rows())
            {                                                                
                foreach($rs->result() as $row)
                {
                    $ids = explode(":", $row->picksHash);
                    $wins = 0;
                    $loses = count($answers) - count($temp); //In there just in case we had to delete an entry because of sport event cancellation
                    foreach($ids as $id)
                    {
                        if(isset($answers[$id]))
                            $wins++;
                        else
                            $loses++;
                    }
                    $win_ids[$wins][] = $row->id;                    
                }
            }
            
            if($win_ids)
            {
                foreach($win_ids as $win => $row)
                {
                    $this->db->query("Update BGPlayerCards set wins = ?, losses = ? where id in (" . implode(",", $row) . ")", array($win, count($answers) - $win));
                }
            }
            //print_r($win_ids); die();
            
            $winners = array();
            $losers = array();
            $limit = 10;
            $offset = 0;
            
            while(!$losers)
            {
                $rs = $this->db->query("Select *
                    From BGPlayerCards pc                    
                    Where pc.parlayCardId = ?
                    Order By pc.wins DESC Limit ? Offset ? ", array($parlay_id, $limit, $offset));
                
                if(!$rs->num_rows())
                    break;
                
                $recs = $rs->result();
                $max_right = $recs[0]->wins;
                foreach($recs as $row)
                {
                    if($row->wins == $max_right)
                        $winners[] = $row;
                    else
                        $losers[] = $row;
                }
                $offset += $limit;
            }
            
            //print_r($winners); die();
            if(!$winners)
            {
                $win_row = array('player_id' => 0,
                    'foreign_id' => $config->id,
                    'ticket_id' => 0,
                    'game_type' => 'BG',
                    'serial_number' => sprintf("KB%05d", $config->id),
                    'prize_name' => '$0',
                    'prize_email' => '$0',
                    'amount' => 0,
                    'game_name' => 'Big Game 21',
                    'processed' => 0);
                $this->db->insert("Winners", $win_row);
                $message = "No Winners for this Game";
            }
            else
            {
                $amount = floor(str_replace(",", "", str_replace('$', '', $config->cardWin)) / count($winners));
                foreach($winners as $winner)
                {
                    $win_row = array('player_id' => $winner->playerId,
                        'foreign_id' => $config->id,
                        'ticket_id' => $winner->id,
                        'game_type' => 'BG',
                        'serial_number' => sprintf("KB%05d", $config->id),
                        'prize_name' => '$' . $amount,
                        'prize_email' => '$' . $amount,
                        'amount' => $amount,
                        'game_name' => 'Big Game 21',
                        'processed' => 0,
                        'expirationDate' => date("Y-m-d H:i:s", strtotime("+24 hours")));
                    $temp = $this->getPlayer($winner->playerId);
                    if(!$temp['player']['accountStatus'] == "Active")
                    {
                        $this->db->insert("Winners", $win_row);
                        admin_model::addAudit($this->db->last_query(), "admin_model", "pickBG");
                        $message .= $temp['player']['firstName'] . " " . $temp['player']['lastName'] . " From " . $temp['player']['city'] . "," . $temp['player']['state'] . ' Won $' . $amount . "\n";
                    }
                }
                if(!$message)
                    $message = "No Winners for this Game";
            }
            
            return true;
        }
        
        public function pickSlots($data, &$message)
        {                        
            $db = $this->load->database ('slots', true);                        
                
            $rs = $db->query("Select * from SlotTournament where ID = ?", array(isset($data['id']) ? $data['id'] : $data['ID']));
            if(!$rs->num_rows())
            {
                $message = "Invalid Slot Tournament";
                return false;
            }
            
            $slot = $rs->row();            
            
            $id = $slot->ID;
            $log_table = $session_table = NULL;
            $rs = $db->query("Select substring_index(TABLE_NAME, '_', 1) as id, concat(TABLE_SCHEMA, '.', TABLE_NAME) as name  from information_schema.TABLES where (TABLE_NAME like 'Log_$id' OR TABLE_NAME like 'Session_$id') order by TABLE_NAME");
            $tables = $rs->result();
            
            foreach($tables as $table)
            {
                switch($table->id)
                {
                    case "Log": $log_table = $table->name; break;
                    case "Session": $session_table = $table->name; break;
                }
            }
            
            if(!$log_table || !$session_table)
            {
                $message = "Could not find tables";
                return false;
            }
            
            $rs = $db->query("Select PlayerID, l.SessionID, max(WinTotal) as win, sg.Name
                From $session_table s
                Inner join kizzangslot.SlotGame sg on sg.ID = s.GameID
                Inner join $log_table l on l.SessionID = s.SessionID
                Group by l.SessionID order by max(WinTotal) DESC");
            
            if($rs->num_rows())
            {
                $winners = $rs->result();
                $players = array();                
                $last_score = 1000000000;
                $i = 0;
                //Group all the players into places
                foreach($winners as $winner)
                {                    
                    if($winner->win < $last_score)  
                    {
                        $players[$i++][] = $winner;                                   
                        $last_score = $winner->win;                    
                    }
                    else
                    {
                        $players[$i][] = $winner;
                    }                    
                }

                $prizes = array();
                $rs = $this->db->query("Select * from Payouts where gameType = ? order by startRank", array($slot->type . " Slot"));
                if(!$rs->num_rows())
                {
                    $message = "No Payout Defined";
                    return false;
                }
                
                foreach($rs->result() as $row)
                {
                    for($i = $row->startRank; $i <= $row->endRank; $i++)
                    {
                        $prizes[$i - 1] = array('amount' => $row->amount, 'type' => $row->payType);
                    }
                }

                $place = 0;
                $player_prizes = array();
                $picked_players = array();
                foreach($players as $player)
                {
                    $dup_rec = false;
                    foreach($player as $key => $player_dup)
                    {
                        if(in_array($player_dup->PlayerID, $picked_players)) //If duplicate Player, go to the next record
                        {
                            if(count($player) == 1)
                            {
                                $place++;
                                $dup_rec = true;
                                continue;
                            }
                            else
                            {
                                unset($player[$key]);
                            }
                        }
                    }
                    
                    if($dup_rec && count($player) == 1)
                        continue;

                    if(!isset($prizes[$place]))
                        break;

                    foreach($player as $player_dup)
                        $picked_players[$player_dup->PlayerID] = $player_dup->PlayerID;
                    
                    if(count($player) == 1)
                    {
                        $player[0]->type = $prizes[$place]['type'];
                        $player[0]->amount = $prizes[$place++]['amount'];
                        $player[0]->place = $place;                        
                        $player_prizes[] = $player[0];
                        continue;
                    }
                    else
                    {
                        $new_amount = 0;
                        foreach($player as $sub_winner)
                            if(isset($prizes[$place++]))
                                $new_amount += $prizes[$place++]['amount'];

                        foreach($player as $key => $sub_winner)
                        {
                            $sub_winner->amount = $new_amount / count($player);
                            $sub_winner->type = $prizes[$place - 1]['type'];
                            $sub_winner->place = $key + 1;
                            $player_prizes[] = $sub_winner;
                        }
                    }
                    
                }
                $this->load->model('signalone_model');

                $moneyWin = false;
                foreach($player_prizes as $player_prize)
                {
                    $win_row = array('player_id' => $player_prize->PlayerID,
                        'foreign_id' => $slot->ID,
                        'ticket_id' => $player_prize->SessionID,
                        'game_type' => 'Slots',
                        'serial_number' => sprintf("KS%05d", $slot->ID),
                        'order_num' => $player_prize->place,
                        'prize_name' => '$' . number_format((int) $player_prize->amount, 2),
                        'prize_email' => '$' . number_format((int) $player_prize->amount, 2),
                        'amount' => $player_prize->amount,
                        'game_name' => $player_prize->Name,
                        'processed' => 0,
                        'expirationDate' => date("Y-m-d H:i:s", strtotime("+48 hours")));
                    $temp = $this->getPlayer($player_prize->PlayerID);
                    //print_r($temp['player']); die();
                    if($temp['player']['accountStatus'] == "Active" && $player_prize->type == "Money")
                    {
                        $moneyWin = true;
                        $this->db->insert("Winners", $win_row);
                        $insertId = $this->db->insert_id();
                        $data = array('serialNumber' => $win_row['serial_number'], 'entry' => $insertId, 'gameName' => $win_row['game_name'], 
                            'tournamentType' => $slot->type, 'prizeName' => $win_row['prize_name'], 'prizeAmount' => $player_prize->amount);
                        $rec = array('playerId' => $win_row['player_id'], 'playerActionTaken' => 0, 'type' => 'slotTournament', 'data' => json_encode($data), 'pending' => 1, 
                            'expireDate' => date("Y-m-d H:i:s", strtotime("+48 hours")));
                        $this->db->insert("EventNotifications", $rec);
                        admin_model::addAudit($this->db->last_query(), "admin_model", "pickSlots");
                        $message .= "#" . $player_prize->place . ": " . $temp['player']['firstName'] . " " . $temp['player']['lastName'] . " From " . $temp['player']['city'] . "," . $temp['player']['state'] . ' Won $' . $player_prize->amount . "\n";
                        
                        $sms = array('headings' => 'Important message from Kizzang®', 'contents' => "You’re a potential winner!  Click here to claim.", 'isIos' => 'on', 'isAndroid' => 'on',
                            'key' => array('id'), 'relation' => array('='), 'value' => array($win_row['player_id']));
                        $ret = "";
                        //$this->signalone_model->addPushNotification($sms, $ret); 
                    }
                    elseif($temp['player']['accountStatus'] == "Active" && $player_prize->type == "Chedda")
                    {
                        $description = "You have won " . number_format($player_prize->amount, 0) .  " Chedda by getting " . $this->addOrdinalSuffix($player_prize->place) . " place in Slot Tournament " . $win_row['serial_number'];
                        $data = array('serialNumber' => $win_row['serial_number'], 'entry' => $win_row['foreign_id'], 'title' => 'Slot Chedda Win', 'description' => $description, 'chedda' => floor($player_prize->amount));
                        $rec = array('playerId' => $win_row['player_id'], 'playerActionTaken' => 0, 'type' => 'cheddaEvent', 'data' => json_encode($data), 'pending' => 1, 'expireDate' => date("Y-m-d H:i:s", strtotime("+24 hours")));
                        $this->db->insert("EventNotifications", $rec);
                        admin_model::addAudit($this->db->last_query(), "admin_model", "pickSlots");
                        $message .= "#" . $player_prize->place . ": " . $temp['player']['firstName'] . " " . $temp['player']['lastName'] . " From " . $temp['player']['city'] . "," . $temp['player']['state'] . ' Won ' . $player_prize->amount . " Chedda\n";
                    }
                }
                if(!$moneyWin)
                {
                    $win_row = array('player_id' => 0,
                        'foreign_id' => $slot->ID,
                        'ticket_id' => 0,
                        'game_type' => 'Slots',
                        'serial_number' => sprintf("KS%05d", $slot->ID),
                        'order_num' => 0,
                        'prize_name' => 0,
                        'prize_email' => 0,
                        'amount' => 0,
                        'status' => 'Expired',
                        'processed' => 1);
                    $this->db->insert("Winners", $win_row);
                    admin_model::addAudit($this->db->last_query(), "admin_model", "pickSlots");
                }
            }
            else
            {
                $win_row = array('player_id' => 0,
                        'foreign_id' => $slot->ID,
                        'ticket_id' => 0,
                        'game_type' => 'Slots',
                        'serial_number' => sprintf("KS%05d", $slot->ID),
                        'order_num' => 0,
                        'prize_name' => 0,
                        'prize_email' => 0,
                        'amount' => 0,
                        'status' => 'Expired',
                        'processed' => 1);
                    $this->db->insert("Winners", $win_row);
                    admin_model::addAudit($this->db->last_query(), "admin_model", "pickSlots");
                $message = "No Players Participated in this Tournament";
            }
            $rs = $db->query("Select PlayerID as playerId, l.SessionID, max(WinTotal) as win, sg.Name
                From $session_table s
                Inner join kizzangslot.SlotGame sg on sg.ID = s.GameID
                Inner join $log_table l on l.SessionID = s.SessionID
                Group by l.SessionID order by max(WinTotal) DESC");
            
            $recs = array();
            $place = 0;
            foreach($rs->result() as $row)
            {
                $place++;
                if(in_array($row->playerId, $picked_players))
                    continue;
                $picked_players[] = $row->playerId; 
                $row->type = $slot->type;
                $row->rank = $place;
                $recs[] = $row;
            }
            
            if($recs)
                $this->addNotifications ("Slots", $recs);
            
            return true;           
        }
        
        private function addOrdinalSuffix($num)
        {
            if (!in_array(($num % 100),array(11,12,13))){
                switch ($num % 10) {
                  // Handle 1st, 2nd, 3rd
                  case 1:  return $num.'st';
                  case 2:  return $num.'nd';
                  case 3:  return $num.'rd';
                }
              }
              return $num.'th';
        }
        
        private function addNotifications($type, $data)
        {
            $title = "Game Notification";
            $message = "";
            $parlayThemes = array('ptbdailyshowdown' => 'Pick the Board Daily Showdown', 'sidailyshowdown' => 'PTB Daily Showdown', 
                'cheddadailyshowdown' => 'Chedda Daily Showdown', 'profootball2016' => 'Pro Football 2016', 'sicollegebasketball' => 'College Basketball');
            switch($type)
            {
                case "Lottery": $message = "You picked [RANK] in the Lottery Tournament."; break;
                case "Parlay": $message = "You placed [RANK] in the [THEME] Parlay Tournament."; break;
                case "Slots": $message = "You placed [RANK] in the [TYPE] Slot Tournament."; break;
            }
            
            $recs = array();
            foreach($data as $row)
            {                
                switch($type)
                {
                    case "Parlay": 
                        $description = str_replace("[RANK]", $this->addOrdinalSuffix($row->rank), $message);
                        $description = str_replace("[THEME]", $parlayThemes[$row->theme], $description); break;
                    case "Slots": 
                        $description = str_replace("[RANK]", $this->addOrdinalSuffix($row->rank), $message);
                        $description = str_replace("[TYPE]", $row->type, $description); break;
                    case "Lottery":
                        $description = str_replace("[RANK]", $row->rank, $message); break;
                }
                $rec = array('playerId' => $row->playerId, 'type' => 'notice', 'data' => json_encode(array('title' => $title, 'description' => $description)), 'expireDate' => date("Y-m-d H:i:s", strtotime("24 hours")));
                $recs[] = $rec;
            }
            $this->db->insert_batch("EventNotifications", $recs);
        }
        
        public function updateWinners()
        {
            $slotdb = $this->load->database ('slots', true);
            $scratchdb = $this->load->database ('default', true);
            $scratch_games = array();
            $srs = $scratchdb->query("Select * from Scratch_GPGames");
            if($srs->num_rows())            
                foreach($srs->result() as $row)
                    $scratch_games[$row->SerialNumber] = $row->Name;
            
            $rs = $this->db->query("Select * from Winners where game_name is NULL order by id DESC");
            if($rs->num_rows())
            {
                foreach($rs->result() as $row)
                {
                    switch($row->game_type)
                    {
                        case "Sweepstakes":
                            $this->db->query("Update Winners set game_name = prize_name where id = ?", array($row->id));
                            break;
                        
                        case "Slots":
                            $srs = $slotdb->query("Select s.* from 
                                kizzangslot_archive.SlotAggregate a
                                Inner join kizzangslot.SlotGame s on a.GameId = s.ID 
                                where SlotTournamentId = ? and Rank = ?", array($row->foreign_id, $row->order_num -1));
                            if($srs->num_rows)                            
                                $this->db->query("Update Winners set game_name = ? where id = ?", array($srs->row()->Name, $row->id));
                            break;
                            
                        case "Parlay":
                            $this->db->query("Update Winners set game_name = 'Pick the Board' where id = ?", array($row->id));
                            break;
                        
                        case "Scratchers":                            
                            $this->db->query("Update Winners set game_name = ? where id = ?", array($scratch_games[$row->serial_number], $row->id));
                            break;
                    }
                }
            }
        }
        
        public function pickLottery($data, &$message)
        {
            $rs = $this->db->query("Select * from LotteryConfigs where id = ? and id not in (Select DISTINCT foreign_id from Winners where game_type = 'Lottery')", array($data['id']));
            if(!$rs->num_rows())
            {
                $message = "Invalid Lottery";
                return false;
            }
            
            $config = $rs->row();
            
            $prizes = array();
            $rs = $this->db->query("Select * from Payouts where gameType = 'Lottery' order by startRank");
            foreach($rs->result() as $prize)
                for($i = $prize->startRank; $i <= $prize->endRank; $i++)
                    $prizes[$i] = array('payType' => $prize->payType, 'rank' => $i, 'amount' => $prize->amount);
            
            $rs = $this->db->query("Select correctAnswers, count(*) as cnt from LotteryCards where lotteryConfigId = ? and correctAnswers > ? group by correctAnswers", array($config->id, $config->numAnswerBalls - count($prizes)));
            if(!$rs->num_rows())
            {
                $win_row = array('player_id' => 1,
                    'foreign_id' => $config->id,
                    'ticket_id' => 0,
                    'order_num' => 0,
                    'game_type' => 'Lottery',
                    'serial_number' => sprintf("KL%05d", $config->id),
                    'prize_name' => 'None',
                    'game_name' => 'Lottery',
                    'prize_email' => 'None',
                    'amount' => 0,
                    'processed' => 1);
                $this->db->insert("Winners", $win_row);
                $message = "No Lottery Winners";                
            }
            else
            {
                $winagg = array();    
                foreach($rs->result() as $row)
                    $winagg[$config->numAnswerBalls - $row->correctAnswers + 1] = $row->cnt;

                $rs = $this->db->query("Select * from LotteryCards where lotteryConfigId = ? and correctAnswers > ?", array($config->id, $config->numAnswerBalls - count($prizes)));

                $message = "The Winners are: ";
                $winIds = array();
                foreach($rs->result() as $card)
                {
                    $winIds[] = $card->playerId;
                    $player = $this->getPlayer($card->playerId);
                    $place = $config->numAnswerBalls - $card->correctAnswers + 1;
                    $win_row = array('player_id' => $card->playerId,
                        'foreign_id' => $config->id,
                        'ticket_id' => $card->id,
                        'order_num' => $place,
                        'game_type' => 'Lottery',
                        'serial_number' => sprintf("KL%05d", $config->id),
                        'prize_name' => $prize[$place]['payType'] == "Money" ? '$' . number_format($prize[$place]['amount'] / $winagg[$place], 2) : number_format($prize[$place]['amount'],0) . " Chedda",
                        'game_name' => 'Lottery',
                        'prize_email' => $prize[$place]['payType'] == "Money" ? '$' . number_format($prize[$place]['amount'] / $winagg[$place], 2) : number_format($prize[$place]['amount'],0) . " Chedda",
                        'amount' => $prize[$place]['payType'] == "Money" ? $prize[$place]['amount'] / $winagg[$place] : $prize[$place]['amount'],
                        'processed' => 0,
                        'expirationDate' => date("Y-m-d H:i:s", strtotime("+24 hours")));
                    $this->db->insert("Winners", $win_row);
                    $message .= $player['firstName'] . " " . $player['lastName'] . " From " . $player['city'] . "," . $player['state'] . "\n";
                }
            }
            $notices = $used_players = array();
            $rs = $this->db->query("Select concat(correctAnswers, ' out of ', ?) as rank, playerId from LotteryCards where lotteryConfigId = ? and correctAnswers <= ? order by correctAnswers DESC", 
                    array($config->numAnswerBalls, $config->id, $config->numAnswerBalls - count($prizes)));
             
            foreach($rs->result() as $row)
            {
                if(in_array($row->playerId, $used_players))                        
                    continue;
                $used_players[] = $row->playerId; 
                $notices[] = $row;
            }
            
            if(count($notices))
                $this->addNotifications ("Lottery", $notices);
            
            return true;
        }
        
        public function pickSweepstakes($data, &$message)
        {            
            
            $rs = $this->db->query("Select * from Sweepstakes where id = ? and id not in (Select DISTINCT foreign_id from Winners where game_type= 'Sweepstakes')", array($data['id']));
            if(!$rs->num_rows())
            {
                $message = "Invalid Sweepstakes";
                return false;
            }
            
            $sweepstakes = $rs->row();
            $winners = array();
                        
            $i = 0;
            while($i < $sweepstakes->maxWinners)
            {
                $rs = $this->db->query("Select * from Tickets where sweepstakesId = ? Order by rand() LIMIT 1", array($data['id']));
                $row = $rs->row();
                $temp = $this->getPlayer($row->playerId);
                if($temp['player']['accountStatus'] == "Active" && !in_array($row->playerId, array_keys($winners)))
                {
                    $winners[$row->playerId] = array('player_id' => $row->playerId, 'ticket_id' => $row->playerId, 'order_num' => $row->sweepstakesId);
                    $i++;
                }                    
            }
            
            $players = array();
            foreach($winners as $winner)
            {
                $temp = $this->getPlayer($winner['player_id']);
                $temp['player']['ticket_id'] = $winner['ticket_id'];
                $temp['player']['order_num'] = $winner['order_num'];
                $players[] = $temp['player'];
            }
            
            $message = "The winners are:\n";
            foreach($players as $player)
            {
                $win_row = array('player_id' => $player['id'],
                    'foreign_id' => $sweepstakes->id,
                    'ticket_id' => $player['ticket_id'],
                    'order_num' => $player['order_num'],
                    'game_type' => 'Sweepstakes',
                    'serial_number' => sprintf("KW%05d", $sweepstakes->id),
                    'prize_name' => $sweepstakes->displayName,
                    'game_name' => $sweepstakes->displayName,
                    'prize_email' => $sweepstakes->name,
                    'amount' => $sweepstakes->taxValue,
                    'processed' => 0);
                $this->db->insert("Winners", $win_row);
                $insertId = $this->db->insert_id();
                //Now add them to the EventNotification table
                $data = array('serialNumber' => $win_row['serial_number'], 'entry' => $insertId, 'prizeAmount' => $win_row['amount'], 'prizeName' => $win_row['prize_name']);
                $rec = array('playerId' => $win_row['player_id'], 'playerActionTaken' => 0, 'type' => 'sweepstakes', 
                    'data' => json_encode($data), 'pending' => 1, 'expireDate' => date("Y-m-d H:i:s", strtotime('+24 hours')));
                $this->db->insert("EventNotifications", $rec);
                admin_model::addAudit($this->db->last_query(), "admin_model", "pickSweepstakes");
                $message .= $player['firstName'] . " " . $player['lastName'] . " From " . $player['city'] . "," . $player['state'] . "\n";
            }
            
            return true;
        }
        
        //All Parlay Cards
        function getSportsSchedules($sport_cat_id)
        {  
            //print_r($this->db); die();
            if($sport_cat_id)
            {
                $rs = $this->db->query("Select ss.id, sc.name as sport, st1.name as team1, st2.name as team2, DATE_FORMAT(ss.dateTime, '%a %b %d, %Y %r') as date
                    From SportSchedule ss
                    Inner join SportTeams st1 on st1.id = ss.team1 and st1.sportCategoryID = ss.sportCategoryID
                    Inner join SportTeams st2 on st2.id = ss.team2 and st2.sportCategoryID = ss.sportCategoryID
                    Inner join SportCategories sc on sc.id = ss.sportCategoryID
                    Where ss.dateTime > convert_tz(now(), 'GMT', 'US/Pacific') and ss.sportCategoryID = ?
                    Order by ss.dateTime ASC
                    Limit 1000", array($sport_cat_id));
            }
            else
            {
                $rs = $this->db->query("Select ss.id, sc.name as sport, st1.name as team1, st2.name as team2, DATE_FORMAT(ss.dateTime, '%a %b %d, %Y %r') as date
                    From SportSchedule ss
                    Inner join SportTeams st1 on st1.id = ss.team1 and st1.sportCategoryID = ss.sportCategoryID
                    Inner join SportTeams st2 on st2.id = ss.team2 and st2.sportCategoryID = ss.sportCategoryID
                    Inner join SportCategories sc on sc.id = ss.sportCategoryID
                    Where ss.dateTime > convert_tz(now(), 'GMT', 'US/Pacific') 
                    Order by ss.dateTime ASC
                    Limit 1000");
            }
            
             $sports = $rs->result();
            
             $rs = $this->db->query("Select * from SportCategories order by name");
             $game_types = $rs->result();
             
             return compact('sports', 'game_types');
        }
        
        function getSportsSchedule($id)
        {
            $schedule = NULL;
            $teams = array();            
            
            $rs = $this->db->query("Select * from SportCategories order by name");
            $categories = $rs->result();
                        
            if($id)              
            {
                $rs = $this->db->query("Select * from SportSchedule where id = ?", array($id));
                $schedule = $rs->row();      
                
                $rs = $this->db->query("Select * from SportTeams where sportCategoryID = ?", array($schedule->sportCategoryID));
                $teams = $rs->result();
            }
            else
            {
                $rs = $this->db->query("Select * from SportTeams where sportCategoryID = ?", array($categories[0]->id));
                $teams = $rs->result();
            }
            return compact('schedule', 'teams', 'categories');
        }
        
        function addSportsSchedule($data)
        {
            if(isset($data['id']))
            {
                $this->db->where('id', $data['id']);
                $this->db->update('SportSchedule', $data);
            }
            else
            {
                $this->db->insert('SportSchedule', $data);
            }
            admin_model::addAudit($this->db->last_query(), "admin_model", "addSportsSchedule");
            return true;
        }
        
        function getTeamDropdown($id)
        {
            $ret = "";
            $rs = $this->db->query("Select * from SportTeams where sportCategoryID = ? order by name", array($id));
            foreach($rs->result() as $row)
                $ret .= "<option value='" . $row->id . "'>" . $row->name . "</option>";
            return $ret;
        }
        
        function removeAnswer($id)
        {
            $this->db->delete("SportGameResults", array('sportScheduleId' => $id));            
        }
        
        function getParlayCards($startDate, $endDate)
        {            
            $rs= $this->db->query("Select p.*, count(DISTINCT pc.id) as cnt, count(DISTINCT gr.sportScheduleId) as questions
                    from SportParlayConfig p
                    Left join SportParlayCards pc on pc.parlayCardId = p.parlayCardId                    
                    Left Join SportGameResults gr on gr.parlayCardId = p.parlayCardId                    
                    where p.cardDate between ? and ? OR ? between p.cardDate and p.endDate OR ? between p.cardDate and p.endDate
                    Group by p.id                    
                    Order by p.cardDate", array($startDate, $endDate, $startDate, $endDate));
            return $rs->result();                    
        }
        
        function getEventScores()
        {
            $rs = $this->db->query("Select group_concat(DISTINCT parlayCardId) as parlay_ids, sc.name as category, sportScheduleId, team1, team1Name, team2, team2Name, DATE_FORMAT(dateTime, '%a %b %d, %Y %r') as date 
                from SportParlayCards pc
                Inner join SportCategories sc on pc.sportCategoryId = sc.id
                Where sportScheduleId not in (Select sportScheduleId from SportGameResults) and dateTime < now()
                Group by sportScheduleId
                Order by dateTime DESC");
            return $rs->result();
        }
        
        function addEventScores($recs)
        {
            $total_parlay_ids = array();
            foreach($recs as $data)
            {
                $parlay_ids = array();
                if(strstr($data['parlay_id'], ","))            
                    $parlay_ids = explode(",", $data['parlay_id']);
                else
                    $parlay_ids[] = $data['parlay_id'];

                foreach($parlay_ids as $id)
                {
                    //BAD Fix to make sure that we don't have to change DB structure to accommedate Over / Under
                    $total_parlay_ids[$id] = $id;
                    $event = NULL;
                    $rs = $this->db->query("Select * from SportParlayCards where id = ?", $data['event_id']);
                    if($rs->num_rows())
                        $event = $rs->row();
                    
                    if($event && $event->overUnderScore)
                        $rs = $this->db->query("Select * from SportGameResults where sportScheduleId = ? and parlayCardId = ?", array($data['event_id'], $id));
                    else
                        $rs = $this->db->query("Select * from SportGameResults where sportScheduleId = ? and parlayCardId = ?", array($data['sportScheduleId'], $id));
                    
                    if($event && $event->overUnderScore)
                        $rec = array('sportScheduleId' => $data['event_id'], 'parlayCardId' => $id, 'score1' => $data['team1_score'], 'score2' => $data['team2_score']);
                    else
                        $rec = array('sportScheduleId' => $data['sportScheduleId'], 'parlayCardId' => $id, 'score1' => $data['team1_score'], 'score2' => $data['team2_score']);
                    if($data['team1_score'] > $data['team2_score'])
                        $rec['winner'] = $data['team1'];
                    elseif($data['team2_score'] > $data['team1_score'])
                        $rec['winner'] = $data['team2'];
                    else //Tie
                        $rec['winner'] = 0;
                    
                    if($rs->num_rows())
                    {
                        if($event && $event->overUnderScore)
                            $this->db->where('sportScheduleId', $data['event_id']);
                        else
                            $this->db->where('sportScheduleId', $data['sportScheduleId']);
                        $this->db->where('parlayCardId', $id);
                        $this->db->update('SportGameResults', $rec);
                    }
                    else
                    {
                        $this->db->insert('SportGameResults', $rec);
                    }
                    admin_model::addAudit($this->db->last_query(), "admin_model", "addEventScores");
                }
                
                foreach($total_parlay_ids as $id)
                {
                    $this->db->where('parlayCardId', $id);
                    $this->db->update('SportPlayerCards', array('wins' => 0, 'losses' => 0));
                }
            }
            //die();
            return true;
        }
        
        public function deleteParlayCard($id)
        {
            $rs = $this->db->query("Select count(*) as cnt from SportPlayerCards where parlayCardId = ?", array($id));
            $count = $rs->row();
            if($count->cnt)
                return false;
            
            $this->db->delete('SportParlayConfig', array('parlayCardId' => $id));
            admin_model::addAudit($this->db->last_query(), "admin_model", "deleteParlayCard");
            return true;
        }
        
        function reprocessParlayWinners()
        {
            $rs = $this->db->query("SELECT distinct parlayCardId FROM kizzang.SportParlayCards where overUnderScore is NOT NULL and parlayCardId in (Select parlayCardId from SportParlayConfig where cardDate < '2015-11-10')");
            foreach($rs->result() as $row)
            {
                $info = $this->getParlayWinners($row->parlayCardId);
                if(count($info['winners']))
                {
                    print "Parlay Card Id:" . $row->parlayCardId . "\n";
                    print_r($info['winners']);
                }
            }
            die();
        }
        
        function getParlayWinners($parlay_id)
        {            
            //Get the config
            $rs = $this->db->query("Select * from SportParlayConfig where parlayCardId = ?", array($parlay_id));
            $config = $rs->row();
            
            //Get all cards that haven't been processed            
            $rs = $this->db->query("Select * from SportPlayerCards where parlayCardId = ?", array($parlay_id));
            
            //Get the answer key                
            $answer = $this->db->query("Select sportScheduleId as id, winner from SportGameResults where parlayCardId = ?", array($parlay_id)); 
            $answers = array();
                        
            foreach($answer->result() as $row)
                $answers[$row->id] = $row->winner;
            
            $win_ids = array();
            if($rs->num_rows())
            {                                                                
                foreach($rs->result() as $row)
                {
                    $temp = explode(":", $row->picksHash);
                    $wins = 0;
                    $loses = count($answers) - count($temp); //In there just in case we had to delete an entry because of sport event cancellation
                    foreach($temp as $hash)
                    {
                        $ids = explode("|", $hash);                           
                        if(count($ids) == 2 && isset($answers[$ids[0]]) && $answers[$ids[0]] == $ids[1])
                            $wins++;
                        else
                            $loses++;
                    }
                    $win_ids[$wins][] = $row->id;                    
                }
            }
            
            if($win_ids)
            {
                foreach($win_ids as $win => $row)
                {
                    $this->db->query("Update SportPlayerCards set wins = ?, losses = ? where id in (" . implode(",", $row) . ")", array($win, count($answers) - $win));
                }
            }
            //print_r($win_ids); die();
            
            $winners = array();
            $losers = array();
            $limit = 10;
            $offset = 0;
            
            while(!$losers)
            {
                $rs = $this->db->query("Select *, pc.id as pc_id, (case when isQuickpick IS NULL then 0 when isQuickpick = 0 then 1 when isQuickpick = 1 then 2 end) as isQuickpick, p.screenName as name 
                    From SportPlayerCards pc 
                    Inner join Users p on p.id = pc.playerId
                    Where pc.parlayCardId = ?
                    Order By pc.wins DESC Limit ? Offset ? ", array($parlay_id, $limit, $offset));
                
                if(!$rs->num_rows())
                    break;
                
                foreach($rs->result() as $row)
                {
                    if($row->wins == count($answers) && count($winners) < 11)
                        $winners[] = $row;
                    else
                        $losers[] = $row;
                }
                $offset += $limit;
            }             
            
            if(count($winners)) //Add in more stats for them
            {
                foreach($winners as &$winner)
                {
                    //Get parlay card to show
                    $winner->card = $this->getParlayCardWinner($winner->pc_id);
                    $rs = $this->db->query("Select count(*) as total, playerId, sum(if(isQuickpick = 1, 1,0)) as qps, sum(if(isQuickpick = 0, 1,0)) as nonqps
                        from SportPlayerCards
                        Where playerId = ? and parlayCardId = ?", array($winner->playerId, $parlay_id));
                    $row = $rs->row();
                    $winner->qps = $row->qps;
                    $winner->total = $row->total;
                    $winner->nonqps = $row->nonqps;
                    $winner->id = $row->playerId;
                }
            }
            //print_r($winners); die();            
            return compact('winners', 'losers', 'config');
        }
        
        function getParlayCardWinner($id)
        {
            $rs = $this->db->query("Select * from SportPlayerCards where id = ?", array($id));
            if(!$rs->num_rows())
                return false;
            
            $card = $rs->row();
            $answers = array();
            //Get all the Over Under scores
            $rs = $this->db->query("SELECT * FROM kizzang.SportParlayCards p
                Inner join SportGameResults r on r.sportScheduleId = if(overUnderScore is NULL, p.sportScheduleId, p.id) and r.parlayCardId = p.parlayCardId 
                where p.parlayCardId = ?", array($card->parlayCardId));
            if($rs->num_rows())
            {
                foreach($rs->result() as $row)
                {
                    if($row->overUnderScore)
                    {
                        $answers[$row->id]['winner'] = $row->winner;
                        $answers[$row->id]['teams'][$row->team1] = "UNDER " . $row->team1Name . "/" . $row->team2Name . " " . $row->overUnderScore;
                        $answers[$row->id]['teams'][$row->team2] = "OVER " . $row->team1Name . "/" . $row->team2Name . " " . $row->overUnderScore;
                    }
                    else
                    {
                        $answers[$row->sportScheduleId]['winner'] = $row->winner;
                        $answers[$row->sportScheduleId]['teams'][$row->team1] = $row->team1Name . " Defeated " . $row->team2Name;
                        $answers[$row->sportScheduleId]['teams'][$row->team2] = $row->team2Name . " Defeated " . $row->team1Name;
                    }
                }
            }
            
            //Overwrite the Over Unders
            $rs = $this->db->query("SELECT * FROM kizzang.SportParlayCards p
                Inner join SportGameResults r on r.sportScheduleId = p.id and r.parlayCardId = p.parlayCardId 
                where p.parlayCardId = ?", array($card->parlayCardId));
            if($rs->num_rows())
                foreach($rs->result() as $row)
                    $answers[$row->id]['winner'] = $row->winner;
            
            $ret = array();
            $hash_array = explode(":", $card->picksHash);
            foreach($hash_array as $key_val)
            {
                $answer = array();
                $temp = explode("|", $key_val);
                if(!isset($answers[$temp[0]]))
                {
                    $answer['status'] = "Not Found";
                    $answer['answer'] = 0; //$answers[$temp[0]]['teams'][$temp[1]];
                }
                else
                {
                    if($answers[$temp[0]]['winner'] != $temp[1])
                        $answer['status'] = "Wrong Answer";
                    $answer['answer'] = $answers[$temp[0]]['teams'][$temp[1]];                        
                }
                $ret[] = $answer;
            }
            //print_r(compact('ret','answers','hash_array')); die();
            return $ret;
        }
        
        function updateParlayEmails()
        {
            //Really Bad Code ahead... :(
            $ret = array();
            $rs = $this->db->query("Select * from CronJobs where routine_id in (Select id from Routines where link = 'crons/send_parlay_emails')");
            if($rs->num_rows())
            {
                $cron = $rs->row();
                $rs = $this->db->query("Select c.id, c.parlayCardId, c.cardDate, min(a.dateTime) as schedule_date
                    From SportParlayConfig c
                    Inner join SportParlayCards a on a.parlayCardId = c.parlayCardId
                    Where (date(now()) between cardDate and endDate) and c.type not in ('profootball2016','collegefootball2016') 
                    group by c.id");
                
                $cards = array();
                foreach($rs->result() as $card)
                    $cards[$card->cardDate] = $card->schedule_date;
                
                $rs = $this->db->query("Select *, date(schedule_date) as date from CronSchedule where cron_id = ? and schedule_date > now()", array($cron->id));
                $jobs = array();
                foreach($rs->result() as $job)
                    $jobs[$job->date] = $job->schedule_date;
                
                foreach($cards as $index => $date)
                {
                    if(!isset($jobs[$index])) //Add job into the queue manually
                    {                        
                        $data = array('cron_id' => $cron->id, 'schedule_date' => $date);
                        $ret['inserted'][] = $data;
                        $this->db->insert("CronSchedule", $data);
                        
                        $data = array('cron_id' => $cron->id, 'schedule_date' => date("Y-m-d H:i:s", strtotime($date) + 3600));
                        $ret['inserted'][] = $data;
                        $this->db->insert("CronSchedule", $data);
                    }
                    else
                    {
                        if($date != $jobs[$index])
                        {
                            $ret['updated'][] = array('old' => $jobs[$index], "new" => $date);
                            $this->db->where(array("cron_id" => $cron->id, "schedule_date" => $jobs[$index]));
                            $this->db->update("CronSchedule", array("schedule_date" => $date));
                        }
                    }
                }
            }
            
            if(getenv("ENV") == "prod")
            {
                $rs = $this->db->query("Select * from CronJobs where routine_id in (Select id from Routines where link = 'crons/send_pro_football_email_insurance')");
                if($rs->num_rows())
                {
                    $cron = $rs->row();
                    $rs = $this->db->query("Select c.id, c.parlayCardId, c.cardDate, min(a.dateTime) as schedule_date
                        From SportParlayConfig c
                        Inner join SportParlayCards a on a.parlayCardId = c.parlayCardId
                        Where (date(now()) between cardDate and endDate) and c.type in ('profootball2016','collegefootball2016')  
                        group by c.id");

                    $cards = array();
                    foreach($rs->result() as $card)
                        $cards[$card->cardDate] = $card->schedule_date;

                    $rs = $this->db->query("Select *, date(schedule_date) as date from CronSchedule where cron_id = ? and schedule_date > now()", array($cron->id));
                    $jobs = array();
                    foreach($rs->result() as $job)
                        $jobs[$job->date] = $job->schedule_date;

                    foreach($cards as $index => $date)
                    {
                        if(!isset($jobs[$index])) //Add job into the queue manually
                        {
                            $data = array('cron_id' => $cron->id, 'schedule_date' => date("Y-m-d H:i:s", strtotime($date) - 600));
                            $ret['inserted'][] = $data;
                            $this->db->insert("CronSchedule", $data);
                        }
                        else
                        {
                            if($date != $jobs[$index])
                            {
                                $ret['updated'][] = array('old' => $jobs[$index], "new" => $date);
                                $this->db->where(array("cron_id" => $cron->id, "schedule_date" => $jobs[$index]));
                                $this->db->update("CronSchedule", array("schedule_date" => $date));
                            }
                        }
                    }
                }
            }
            
            $rs = $this->db->query("Select * from CronJobs where routine_id in (Select id from Routines where link = 'crons/send_roal_emails')");
            if($rs->num_rows())
            {
                $cron = $rs->row();
                $rs = $this->db->query("Select c.*, max(endTime) as schedule_date from ROALConfigs c
                    Inner join ROALQuestions q on q.ROALConfigId = c.id
                    Group by c.id
                    Having (c.id in (Select distinct ROALConfigId from ROALAnswers where isEmailed = 0) or c.id NOT IN (Select distinct ROALConfigId from ROALAnswers))
                    and c.cardDate >= date(now())");
                
                $cards = array();
                foreach($rs->result() as $card)
                    $cards[$card->cardDate] = $card->schedule_date;
                
                $rs = $this->db->query("Select *, date(schedule_date) as date from CronSchedule where cron_id = ? and schedule_date > now()", array($cron->id));
                $jobs = array();
                foreach($rs->result() as $job)
                    $jobs[$job->date] = $job->schedule_date;
                
                foreach($cards as $index => $date)
                {
                    if(!isset($jobs[$index])) //Add job into the queue manually
                    {                        
                        $data = array('cron_id' => $cron->id, 'schedule_date' => date("Y-m-d H:i:s", strtotime($date) + 600));
                        $ret['inserted'][] = $data;
                        $this->db->insert("CronSchedule", $data);
                    }
                    else
                    {
                        if($date != $jobs[$index])
                        {
                            $ret['updated'][] = array('old' => $jobs[$index], "new" => $date);
                            $this->db->where(array("cron_id" => $cron->id, "schedule_date" => $jobs[$index]));
                            $this->db->update("CronSchedule", array("schedule_date" => $date));
                        }
                    }
                }
            }
            $ret['success'] = true;
            return $ret;
        }
        
        function getParlayCard($id)
        {
            $config = $details = $places = NULL;
            if($id)
            {
                $rs = $this->db->query("Select pc.*, count(sp.id) as cnt  
                    from SportParlayConfig pc
                    Left join SportPlayerCards sp on sp.parlayCardId = pc.parlayCardId
                    where pc.parlayCardId = ?", array($id));
                $config = $rs->row();
                
                $rs = $this->db->query("Select pc.parlayCardId as parlay_ids, pc.spread, pc.question, gr.sportScheduleId as sgr_id, sc.name as category, pc.sportScheduleId, pc.id as event_id, pc.team1, pc.team1Name, pc.team2, pc.team2Name, DATE_FORMAT(ss.dateTime, '%a %b %d, %Y %r') as date, gr.sportScheduleId as is_done, gr.winner, pc.overUnderScore  
                from SportParlayCards pc
                Inner join SportCategories sc on pc.sportCategoryId = sc.id   
                Inner join SportSchedule ss on ss.id = pc.sportScheduleId
                Left join SportGameResults gr on gr.sportScheduleId = if(pc.overUnderScore IS NULL, pc.sportScheduleId, pc.id) and gr.parlayCardId = pc.parlayCardId
                where pc.parlayCardId = ?
                Group by pc.id
                Order by pc.sequence, pc.id", array($id));
                $details = $rs->result();
                
                $rs = $this->db->query("Select * from SportParlayPlaces where parlayCardId = ?", array($config->parlayCardId));
                $places = $rs->result();
            }
            
            $adPlacements = $this->getColumnEnum('kizzang', 'SportParlayConfig', 'adPlacement');
            $types = $this->getColumnEnum("kizzang", "SportParlayConfig", "type");
            
            $rules = array();
            $rs = $this->db->query("Select DISTINCT ruleURL from GameRules where gameType = 'Parlay' AND serialNumber = 'TEMPLATE'");
            $rules = $rs->result();
            
            $rule = NULL;
            if($config)
            {
                $rs = $this->db->query("Select * from GameRules where serialNumber = ?", $config->serialNumber);                
                
                if($rs->num_rows())
                {
                    $rule = $rs->row();
                    $rule->text = file_get_contents(str_replace("https://d23kds0bwk71uo.cloudfront.net", "https://kizzang-legal.s3.amazonaws.com",$rule->ruleURL));
                    if(count($rules))
                        $rule->template = file_get_contents(str_replace("https://d23kds0bwk71uo.cloudfront.net", "https://kizzang-legal.s3.amazonaws.com",$rules[0]->ruleURL));
                    else
                        $rule->template = "";
                }                
            }
            return compact('config', 'details', 'rules', 'rule', 'places', 'template','types','adPlacements');
        }
        
        public function addParlayPlace($data)
        {
            if(isset($data['id']))
            {
                $this->db->where('id', $data['id']);
                $this->db->update("SportParlayPlaces", $data);
            }
            else
            {
                $this->db->insert("SportParlayPlaces", $data);
            }
            admin_model::addAudit($this->db->last_query(), "admin_model", "addParlayPlace");
        }
                
        public function sendParlayEmails()
        {
            $ret = array();
            
            $rs = $this->db->query("Select *  
                from SportParlayConfig 
                where parlayCardId in (Select distinct parlayCardId from SportPlayerCards where emailed = 0) and endDate < convert_tz(now(), 'GMT', 'US/Pacific') order by endDate ASC");
            $config = $rs->row();
            if(!isset($config->parlayCardId))            
                return array('Error' => 'Parlay Card not found');
            
            $ret['parlayCardId'] = $config->parlayCardId;
            $rs = $this->db->query("Select playerId, group_concat(id) as ids from SportPlayerCards where emailed = 0 and parlayCardId = ? group by playerId", array($config->parlayCardId));
            $server = "";
            $image = "https://d1w836my735uqw.cloudfront.net/logos/daily_showdown_logo.png";
            $ret['environment'] = getenv("ENV");
            switch(getenv("ENV"))
            {
                case "dev": $server = "https://dev.kizzang.com"; break;
                case "staging": $server = "https://qa.kizzang.com"; break;
                case "prod": $server = "https://kizzang.com"; break;
            }
            
            $ret['count'] = $rs->num_rows();
            if($rs->num_rows())
            {
                $emails = $rs->result();
                //Do Prepwork to get team info 
                $schedules = array();
                $rs = $this->db->query("Select * from SportParlayCards where parlayCardId = ?", array($config->parlayCardId));                
                foreach($rs->result() as $temp)
                {
                    if(!$temp->overUnderScore)
                        $schedules[$temp->sportScheduleId] = $temp;
                    else
                        $schedules[$temp->id] = $temp;
                }
                
                $categories = array();
                $rs = $this->db->query("Select * from SportCategories");
                foreach($rs->result() as $temp)
                    $categories[$temp->id] = $temp->name;
                
                $teams = array();
                $rs = $this->db->query("Select * from SportTeams order by sportCategoryID, id");
                foreach($rs->result() as $team)
                    $teams[$team->sportCategoryID][$team->id] = $team->name;
                                
                foreach($emails as $email)
                {
                    $ids = explode(",", $email->ids);
                    $player = $this->getPlayer($email->playerId, true);
                    if($player['emailStatus'] == 'Transaction Opt Out' || $player['emailStatus'] == 'Both Opt Out')
                        continue;
                    
                    $cards = array();
                    $title = "";
                    $image = "https://d1w836my735uqw.cloudfront.net/logos/siptb_header_new.jpg";
                    switch ($config->type)
                    {
                        case "sidailyshowdown": $image = "https://d1w836my735uqw.cloudfront.net/logos/siptb_header_new.jpg"; $gtitle = "Daily Showdown"; $card_date = $config->cardDate; break;
                        case "collegefootball2016": $image = "https://d1w836my735uqw.cloudfront.net/logos/college_bonanza_header_new.jpg"; $gtitle = "College Football Bonanza"; $card_date = date("Y-m-d", strtotime($config->endDate)); break;
                        case "cheddadailyshowdown": $image = "https://d1w836my735uqw.cloudfront.net/logos/chedda_header_new.jpg"; $gtitle = "Chedda Daily Showdown"; $card_date = date("Y-m-d", strtotime($config->endDate)); break;
                        case "profootball2016": $image = "https://d1w836my735uqw.cloudfront.net/logos/profootball_2016_header_new.jpg"; $gtitle = "Pro Football 2016"; $card_date = date("Y-m-d", strtotime($config->endDate)); break;
                    }
                    
                    foreach($ids as $id)
                    {                        
                        $rs = $this->db->query("Select * from SportPlayerCards where id = ?", array($id));
                        $temp = $rs->row();
                        $card = array('date' => $temp->dateTime, 'card_date' => $card_date, 'id' => $temp->id, 'serial_number' => sprintf("KP%05d", $config->parlayCardId));
                        $hash = explode(":", $temp->picksHash);
                        $events = array();
                        foreach($hash as $index => $event)
                        {
                            $event = explode("|", $event);
                            if($schedules[$event[0]]->question)
                                $events[$index]['title'] = $schedules[$event[0]]->question;
                            else
                                $events[$index]['title'] = $categories[$schedules[$event[0]]->sportCategoryId] . " - " . date("g:i A", strtotime($schedules[$event[0]]->dateTime)) . " PDT";
                            
                            if(!$schedules[$event[0]]->overUnderScore)
                            {
                                if($schedules[$event[0]]->spread)
                                {
                                    $spread = 0;
                                    if($schedules[$event[0]]->team1 == $event[1])
                                        $spread = $schedules[$event[0]]->spread;
                                    else
                                        $spread = -1 * $schedules[$event[0]]->spread;
                                    
                                    $events[$index]['winner'] = $teams[$schedules[$event[0]]->sportCategoryId][$event[1]] . " " . ($spread >0 ? "+" . $spread : $spread);
                                }
                                else
                                {
                                    $events[$index]['winner'] = $teams[$schedules[$event[0]]->sportCategoryId][$event[1]];
                                }
                            }
                            else
                            {
                                if($schedules[$event[0]]->team1 == $event[1])  //KEY-LeaderBoard9e30deee8cacf3e50ec03c1c5575541e
                                    $events[$index]['winner'] = "Under ";
                                else
                                    $events[$index]['winner'] = "Over ";
                                $events[$index]['winner'] .= number_format ($schedules[$event[0]]->overUnderScore, 1) . " in " . $teams[$schedules[$event[0]]->sportCategoryId][$schedules[$event[0]]->team1] . " VS. " . $teams[$schedules[$event[0]]->sportCategoryId][$schedules[$event[0]]->team2];                                
                            }
                        }
                        $card['events'] = $events;
                        $cards[] = $card;
                    }
                    $ret['player'][$email->playerId] = count($cards);
                    $card_batch = array();
                    $i = 1;
                    if(!(count($cards) % 10))
                        $max_page = (count($cards) / 10) - 1;
                    else
                        $max_page = ceil(count($cards) / 10);
                    
                    foreach($cards as $index => $card)
                    {
                        if(!$index || ($index % 10))
                        {
                            $card_batch[] = $card;
                            continue;
                        }
                        $numerator = ceil($index / 10);
                        $info = array("server" => $server, "player" => $player, "cards" => $card_batch, "image" => $image, "cardDate" => $config->cardDate);
                        //print_r($info); die();
                        $content = $this->load->view("/emails/dailyShowdown", $info, true);
                        $body = $this->load->view("/emails/wrapper", array('content' => $content, 'url' => $server, 'emailCode' => md5($player['email'])), true);                        
                        $title = $gtitle . ' - Kizzang (' . $numerator . '/' . $max_page . ')';
                        
                        if($this->sendGenericEmail($player['email'], $title, $body))
                            $ret['good'][] = $title . " - " . $player['email'];
                        else
                            $ret['bad'][] = $title . " - " . $player['email'];
                        
                        $card_batch = array();
                        $card_batch[] = $card;
                    }
                    
                    if(count($card_batch))
                    {
                        $numerator = $max_page;
                        $info = array("server" => $server, "player" => $player, "cards" => $card_batch, "image" => $image, "cardDate" => $config->cardDate);
                        //print_r($info); die();
                       $content = $this->load->view("/emails/dailyShowdown", $info, true);
                        $body = $this->load->view("/emails/wrapper", array('content' => $content, 'url' => $this->getSiteUrl(), 'emailCode' => md5($player['email'])), true);
                        $title = $gtitle . ' - Kizzang (' . $numerator . '/' . $max_page . ')';
                        
                        if($this->sendGenericEmail($player['email'], $title, $body))
                            $ret['good'][] = $title . " - " . $player['email'];
                        else
                            $ret['bad'][] = $title . " - " . $player['email'];
                    }
                                        
                    $this->db->query("Update SportPlayerCards set emailed = 1 where id in (" . $email->ids . ")");
                    //print_r($body); die();
                }
            }
            $ret['success'] = true;           
            return $ret;
        }
        
        public function getParlayEmails($playerId, $parlayCardId)
        {
            $ret = array();
            $rs = $this->db->query("Select parlayCardId 
                from SportParlayConfig 
                where parlayCardId = ?", array($parlayCardId));
            $temp = $rs->row();
            if(!isset($temp->parlayCardId))            
                return array('Error' => 'Parlay Card not found');
            
            $rs = $this->db->query("Select playerId, group_concat(id) as ids from SportPlayerCards where playerId = ? and parlayCardId = ? group by playerId", array($playerId, $temp->parlayCardId));
            $server = "";
            $image = "https://d1w836my735uqw.cloudfront.net/logos/daily_showdown_logo.png";
            $ret['environment'] = getenv("ENV");
            switch(getenv("ENV"))
            {
                case "dev": $server = "https://dev.kizzang.com"; break;
                case "staging": $server = "https://qa.kizzang.com"; break;
                case "prod": $server = "https://www.kizzang.com"; break;
            }
            
            $ret['count'] = $rs->num_rows();
            if($rs->num_rows())
            {
                $emails = $rs->result();
                //Do Prepwork to get team info 
                $schedules = array();
                $rs = $this->db->query("Select * from SportParlayCards where parlayCardId in (Select distinct parlayCardId from SportPlayerCards where emailed = 0) order by parlayCardId, dateTime");                
                foreach($rs->result() as $temp)
                {
                    if(!$temp->overUnderScore)
                        $schedules[$temp->sportScheduleId] = $temp;
                    else
                        $schedules[$temp->id] = $temp;
                }
                
                $categories = array();
                $rs = $this->db->query("Select * from SportCategories");
                foreach($rs->result() as $temp)
                    $categories[$temp->id] = $temp->name;
                
                $teams = array();
                $rs = $this->db->query("Select * from SportTeams order by sportCategoryID, id");
                foreach($rs->result() as $team)
                    $teams[$team->sportCategoryID][$team->id] = $team->name;
                
                $pcards = array();
                $rs = $this->db->query("Select * from SportParlayConfig where parlayCardId in (Select distinct parlayCardId from SportPlayerCards where emailed = 0)");
                foreach($rs->result() as $temp)
                    $pcards[$temp->parlayCardId] = $temp;                
                
                foreach($emails as $email)
                {
                    $ids = explode(",", $email->ids);
                    $player = $this->nativesession->get('User');
                    $cards = array();                    
                    switch ($pcards[$temp->parlayCardId]->type)
                    {
                        case "ptbdailyshowdown": $image = "https://d1w836my735uqw.cloudfront.net/logos/daily_showdown_logo.png"; $card_date = $pcards[$temp->parlayCardId]->cardDate; break;
                        case "collegefootball": $image = "https://d1w836my735uqw.cloudfront.net/logos/collegefootball_logo.png"; $card_date = date("Y-m-d", strtotime($pcards[$temp->parlayCardId]->endDate)); break;
                        case "profootball": $image = "https://d1w836my735uqw.cloudfront.net/logos/profootball_logo.png"; $card_date = date("Y-m-d", strtotime($pcards[$temp->parlayCardId]->endDate)); break;
                    }
                    foreach($ids as $id)
                    {                        
                        $rs = $this->db->query("Select * from SportPlayerCards where id = ?", array($id));
                        $temp = $rs->row();
                        $card = array('date' => $temp->dateTime, 'card_date' => $card_date, 'id' => $temp->id, 'serial_number' => sprintf("KP%05d", $temp->parlayCardId));
                        $hash = explode(":", $temp->picksHash);
                        $events = array();
                        foreach($hash as $index => $event)
                        {
                            $event = explode("|", $event);
                            $events[$index]['title'] = $categories[$schedules[$event[0]]->sportCategoryId] . " - " . date("g:i A", strtotime($schedules[$event[0]]->dateTime)) . " PDT";
                            if(!$schedules[$event[0]]->overUnderScore)
                            {
                                $events[$index]['winner'] = $teams[$schedules[$event[0]]->sportCategoryId][$event[1]];
                            }
                            else
                            {
                                if($schedules[$event[0]]->team1 == $event[1])  //KEY-LeaderBoard9e30deee8cacf3e50ec03c1c5575541e
                                    $events[$index]['winner'] = "Under ";
                                else
                                    $events[$index]['winner'] = "Over ";
                                $events[$index]['winner'] .= number_format ($schedules[$event[0]]->overUnderScore, 1) . " in " . $teams[$schedules[$event[0]]->sportCategoryId][$schedules[$event[0]]->team1] . " VS. " . $teams[$schedules[$event[0]]->sportCategoryId][$schedules[$event[0]]->team2];                                
                            }
                        }
                        $card['events'] = $events;
                        $cards[] = $card;
                    }
                    
                    $card_batch = array();
                    $i = 1;
                    if(!(count($cards) % 10))
                        $max_page = (count($cards) / 10) - 1;
                    else
                        $max_page = ceil(count($cards) / 10);
                    
                    foreach($cards as $index => $card)
                    {
                        if(!$index || ($index % 10))
                        {
                            $card_batch[] = $card;
                            continue;
                        }
                        $numerator = ceil($index / 10);
                        $info = array("server" => $server, "player" => $player, "cards" => $card_batch, "image" => $image);
                        //print_r($info); die();
                        $content = $this->load->view("/emails/dailyShowdown", $info, true);
                        $body = $this->load->view("/emails/wrapper", array('content' => $content, 'url' => $this->getSiteUrl(), 'email_code' => $player['emailCode']), true);
                        $this->sendGenericEmail($player['email'], 'Daily Showdown Entry - Kizzang Sweepstakes (' . $numerator . '/' . $max_page . ')', $body);
                        $card_batch = array();
                        $card_batch[] = $card;
                    }
                    
                    if(count($card_batch))
                    {
                        $numerator = $max_page;
                        $info = array("server" => $server, "player" => $player, "cards" => $card_batch, "image" => $image);
                        //print_r($info); die();
                        $content = $this->load->view("/emails/dailyShowdown", $info, true);
                        $body = $this->load->view("/emails/wrapper", array('content' => $content, 'url' => $this->getSiteUrl(), 'email_code' => $player['emailCode']), true);
                        $this->sendGenericEmail($player['email'], 'Daily Showdown Entry - Kizzang Sweepstakes (' . $numerator . '/' . $max_page . ')', $body);
                    }                                                            
                }
            }            
        }
        
        public function getParlayCardCards($id, $playerId)
        {
            //Update the winners it no player id specified
            if(!$playerId)            
                $this->getParlayWinners($id);
            
            $rs = $this->db->query("Select id , firstName, lastName from Users where id in (Select distinct playerId from SportPlayerCards where parlayCardId = ?) order by screenName", array($id));
            $names = $rs->result();
            
            $rs = $this->db->query("Select * from SportParlayConfig where parlayCardId = ?", array($id));
            $config = $rs->row();
            
            $results = array();
            $rs = $this->db->query("Select * from SportGameResults where parlayCardId = ?", array($id));
            foreach($rs->result() as $row)
                $results[$row->sportScheduleId] = $row->winner;
            
            $answers = array();
            $rs = $this->db->query("Select * from SportParlayCards where parlayCardId = ? order by sequence", array($id));
            foreach($rs->result() as $row)
            {
                if($row->overUnderScore && isset($results[$row->id]))
                    $row->winner = $results[$row->id];
                elseif(!$row->overUnderScore && isset($results[$row->sportScheduleId]))
                    $row->winner = $results[$row->sportScheduleId];
                else
                    $row->winner = 0;
                
                if($row->overUnderScore)
                    $answers[$row->id] = $row;
                else
                    $answers[$row->sportScheduleId] = $row;
            }
            
            $cards = array();
            if(!$playerId)
            {
                $rs = $this->db->query("Select c.*, p.firstName, p.lastName from SportPlayerCards c
                    Inner join Users p on p.id = c.playerId 
                    where parlayCardId = ? order by wins DESC limit 50", array($id));
            }
            else
            {
                $rs = $this->db->query("Select c.*, p.firstName, p.lastName from SportPlayerCards c
                    Inner join Users p on p.id = c.playerId 
                    where parlayCardId = ? and playerId = ? order by wins DESC", array($id, $playerId));
            }
            
            foreach($rs->result() as $index => $row)
            {
                $cards[$index]['title'] = "#" . $row->id . " " .  $row->firstName . " " . $row->lastName . " (" . $row->playerId . ") (Wins: " . $row->wins . " Losses: " . $row->losses . ")";
                $picks_temp = explode(":", $row->picksHash);
                foreach($picks_temp as $temp)
                {
                    $key_value = explode("|", $temp);
                    $cards[$index]['cards'][$key_value[0]] = $key_value[1];
                }
            }
            
            return compact('config', 'answers', 'cards', 'names');
        }
        
        public function getAffiliates()
        {
            $rs = $this->db->query("Select s.name, s.artRepo, c.id, c.start_date, c.end_date, c.code, count(ag.Sponsor_Advertising_Campaign_Id) as num_games
                from Sponsor_Advertising_Campaigns c                
                Left Join AffiliateGames ag on c.id = ag.Sponsor_Advertising_Campaign_Id 
                Inner join Sponsors s on s.id = c.utm_source
                Where c.advertising_medium_id = 'affiliate'
                Group by c.id");
            $affiliates = $rs->result();
            return compact('affiliates');
        }
        
        public function getAffiliate($id = "")
        {
            $campaign = $games = "";
            if($id)
            {
                $rs = $this->db->query("Select * from Sponsor_Advertising_Campaigns where id = ?", array($id));
                $campaign = $rs->row();

                $rs = $this->db->query("Select * from AffiliateGames where Sponsor_Advertising_Campaign_Id = ?", array($id));
                $games = $rs->result();
            }
            
            $themes = array('Scratcher' => array(), 'Slot' => array());
            
            $scratch_conn = $this->load->database('default', true);
            $rs = $scratch_conn->query("Select Theme from Scratch_GPGames where CardType = 'Affiliate'");
            foreach($rs->result() as $row)
                $themes['Scratcher'][] = $row->Theme;
            
            $slot_conn = $this->load->database('slots', true);
            $rs = $slot_conn->query("Select Theme from SlotGame where SlotType = 'Affiliate' order by Theme");
            foreach($rs->result() as $row)
                $themes['Slot'][] = $row->Theme;
            
            $gameTypes = $this->getColumnEnum("kizzang", "AffiliateGames", "GameType");
            
            return compact('campaign','games','themes','gameTypes','id');
        }
        
        public function updateAffiliateGames($data)
        {
            $this->db->query("Delete from AffiliateGames where Sponsor_Advertising_Campaign_Id = ?", array($data['id']));
            foreach($data['game'] as $game)
                $this->db->insert('AffiliateGames', array('Sponsor_Advertising_Campaign_Id' => $data['id'], 'GameType' => $game['GameType'], 'Theme' => $game['Theme']));
            return true;
        }
        
        public function getParlayClones($id)
        {
            $rs = $this->db->query("Select parlayCardId as id, concat(serialNumber, ' - ', cardDate) as name from SportParlayConfig where parlayCardId <> ?", array($id));
            $payments = $rs->result();
            return compact('payments', 'id');
        }
        
        public function getPayoutInfo($id)
        {
            $rs = $this->db->query("Select * from SportParlayPlaces where parlayCardId = ? order by rank", array($id));
            $places = $rs->result();
            return compact("places", "id");
        }
        
        public function deleteParlayPlace($id)
        {
            $this->db->delete("SportParlayPlaces", array("id" => $id));
            admin_model::addAudit($this->db->last_query(), "admin_model", "deleteParlayPlace");
            return true;
        }
        
        function addParlayConfig($data)
        {
            if(isset($data['id']))
            {
                $this->db->where('id', $data['id']);
                $this->db->update('SportParlayConfig', $data);
            }
            else
            {
                //Adding in stupid code because the parlay id isn't attached to an auto_increment
                $rs = $this->db->query("Select max(id) as mx from SportParlayConfig");
                $id = $rs->row();
                $data['parlayCardId'] = $id->mx + 1;
                $data['serialNumber'] = sprintf("KP%05d", $data['parlayCardId']);
                $this->db->insert('SportParlayConfig', $data);       
                $id = $this->db->insert_id();
                $data['parlayCardId'] = $id;
                $data['serialNumber'] = sprintf("KP%05d", $id);
                $this->db->where('id', $id);
                $this->db->update("SportParlayConfig", array('parlayCardId' => $id, 'serialNumber' => $data['serialNumber']));
            }
            admin_model::addAudit($this->db->last_query(), "admin_model", "addParlayConfig");
            return $data['parlayCardId'];
        }
        
        public function searchParlayEvents($data)
        {          
            $xlat = array('collegefootball' => 'College Football', 'profootball' => 'Pro Football');
            $query = sprintf("Select %d as parlay_id, ss.id as event_id, sc.name as category, st1.name as team1, st1.powerRanking as pr1, st2.name as team2, st2.powerRanking pr2, ABS(st1.powerRanking - st2.powerRanking) as diff, sc.rank, ss.dateTime as date
                    From SportSchedule ss
                    Inner join SportTeams st1 on st1.id = ss.team1 and st1.sportCategoryID = ss.sportCategoryID
                    Inner join SportTeams st2 on st2.id = ss.team2 and st2.sportCategoryID = ss.sportCategoryID
                    Inner join SportCategories sc on sc.id = ss.sportCategoryID                    
                    Where ss.id not in (Select DISTINCT sportScheduleId from SportParlayCards where parlayCardId = %d) ", $data['parlay_id'], $data['parlay_id']);
            $where = "";
            if($data['sel_parlay_cat'])
                $where .= sprintf(" AND ss.sportCategoryId = %d", $data['sel_parlay_cat']);
            if($data['sel_parlay_team'])
                $where .= sprintf(" AND (ss.team1 = %d OR ss.team2 = %d)", $data['sel_parlay_team'], $data['sel_parlay_team']);
            if($data['parlay_date'] && !($data['parlay_type'] == "collegefootball" || $data['parlay_type'] == "profootball"))
                $where .= sprintf (" AND DATE(ss.dateTime) = '%s'", date('Y-m-d', strtotime($data['parlay_date'])));
            else
                $where .= sprintf (" AND ss.dateTime between '%s' and '%s'", date('Y-m-d', strtotime($data['parlay_date'])), date('Y-m-d', strtotime("+1 week",  strtotime($data['parlay_date']))));
            if($data['parlay_type'] == "collegefootball" || $data['parlay_type'] == "profootball")
                $where .= sprintf(" and sc.name = '%s'", $xlat[$data['parlay_type']]);
            
            $rs = $this->db->query($query . $where . " ORDER BY sc.rank ASC, ABS(st1.powerRanking - st2.powerRanking) ASC LIMIT 100");
            return $rs->result();
        }
        
        public function createRandomParlayUsers($id, $num_cards)
        {
            $players_array = array();
            $hashes = array();
            $date = date("Y-m-d H:i:00");
            $base_query = "Insert into SportPlayerCards (playerId, parlayCardId, picksHash, dateTime) values ";
            $query = "";
            
            $rs = $this->db->query("Select * from SportParlayCards where parlayCardId = ?", array($id));
            $events = $rs->result();
                        
            for($i = 0; $i < $num_cards; $i++)
            {
                $player_id = 107;
                $hash = "";
                foreach($events as $event)
                {
                    if(rand(0, 1000000) % 2)
                        $hash .= ":" . $event->sportScheduleId . "|" . $event->team1;
                    else
                        $hash .= ":" . $event->sportScheduleId . "|" . $event->team2;
                }
                $query .= sprintf("(%d, %d, '%s', '%s'),", $player_id, $id, trim($hash, ":"), $date);
                if($i && ($i % 2500 == 0))
                {
                    $this->db->query($base_query . trim($query, ","));
                    $query = "";
                }
                //$this->db->insert("SportPlayerCards", array('playerId' => $player_id, 'parlayCardId' => $id, 'picksHash' => trim($hash, ":"), 'dateTime' => $date));
            }
            $this->db->query($base_query . trim($query, ","));
        }
        
        public function addParlayQuestion($parlayCardId, $data)
        {
            if(!isset($data['question']) || !isset($data['answer1']) || !isset($data['answer2']) || !$data['question'] || !$data['answer1'] || !$data['answer2'] || !$parlayCardId)
                return array('success' => false, 'message' => 'Input Validation Failed');
            
            $rs = $this->db->query("Select id from SportCategories where name = 'Questions'");
            if(!$rs->num_rows())
                return array('success' => false, 'message' => 'Questions category does not exist');
            
            $sportCategoryId = $rs->row()->id;
            $answers = array();
            $rs = $this->db->query("Select * from SportTeams where name in (?,?) and sportCategoryID = ?", array($data['answer1'], $data['answer2'], $sportCategoryId));
            foreach($rs->result() as $row)
                $answers[strtolower($row->name)] = $row->id;
            
            $currentId = 1;
            $rs = $this->db->query("Select max(id) as id from SportTeams where sportCategoryID = ?", array($sportCategoryId));
            if($rs->num_rows())
                $currentId = $rs->row()->id + 1;
            
            $this->db->trans_begin();
            if(array_key_exists(strtolower($data['answer1']), $answers))
            {
                $team1 = $answers[strtolower($data['answer1'])];
                $team1Name = $data['answer1'];
            }
            else
            {
                $this->db->insert("SportTeams", array('sportCategoryID' => $sportCategoryId, 'name' => $data['answer1'], 'id' => $currentId));
                $team1 = $currentId;
                $currentId++;
                $team1Name = $data['answer1'];
            }
            
            if(array_key_exists(strtolower($data['answer2']), $answers))
            {
                $team2 = $answers[strtolower($data['answer2'])];
                $team2Name = $data['answer2'];
            }
            else
            {
                $this->db->insert("SportTeams", array('sportCategoryID' => $sportCategoryId, 'name' => $data['answer2'], 'id' => $currentId));
                $team2 = $currentId;
                $team2Name = $data['answer2'];
            }
            
            $rs = $this->db->query("Select * from SportParlayConfig where parlayCardId = ?", $parlayCardId);
            $config = $rs->row();
            
            $questionDate = date("Y-m-d H:i:s", strtotime("+1 year")); //PUT IT FAR OUT NOT TO INTERFERE WITH THE ENDDATE PROCESS
            $this->db->insert("SportSchedule", array('sportCategoryID' => $sportCategoryId, 'dateTime' => $questionDate, 'team1' => $team1, 'team2' => $team2));
            $sportScheduleId = $this->db->insert_id();
            
            $sequence = 1;
            $rs = $this->db->query("Select max(sequence) as cnt from SportParlayCards where parlayCardId = ?", array($parlayCardId));
            if($rs->num_rows())
                $sequence = $rs->row()->cnt + 1;
            
            $rec = array('parlayCardId' => $parlayCardId, 'sportScheduleId' => $sportScheduleId, 'sportCategoryId' => $sportCategoryId, 'dateTime' => $questionDate,
                'team1' => $team1, 'team2' => $team2, 'team1Name' => $team1Name, 'team2Name' => $team2Name, 'sequence' => $sequence, 'question' => $data['question']);
            
            $this->db->insert('SportParlayCards', $rec);
            
            $this->db->trans_complete();
            
            if($this->db->trans_status() === false)
            {
                $this->db->trans_rollback();
                return array('success' => false, 'message' => 'Failed to add question');
            }
            $this->db->trans_commit();
            return array('success' => true, 'counter' => $sequence);                        
        }
        
        public function addEventParlay($parlay_id, $event_id, $ou, $spread)
        {
            $rs = $this->db->query("Select count(*) as cnt from SportParlayCards where parlayCardId = ?", array($parlay_id));
            $count = $rs->row()->cnt;
            
            $rs = $this->db->query("Select ? as parlayCardId, ss.id as sportScheduleId, ss.sportCategoryId, ss.dateTime, ss.team1, ss.team2, st1.name as team1Name, st2.name as team2Name
                From SportSchedule ss
                Inner join SportTeams st1 on st1.id = ss.team1 and st1.sportCategoryID = ss.sportCategoryID
                Inner join SportTeams st2 on st2.id = ss.team2 and st2.sportCategoryID = ss.sportCategoryID
                Where ss.id = ? LIMIT 1", array($parlay_id, $event_id));
            if(!$rs->num_rows())
                return false;
            
            $data = $rs->row();
            $rs = $this->db->query("Select IFNULL(max(sequence), 0) as cnt from SportParlayCards where parlayCardId = ?", array($parlay_id));
            $data->sequence = $rs->row()->cnt + 1;
            if($spread)
                $data->spread = $spread;
            else
                $data->spread = NULL;
            
            $this->db->insert('SportParlayCards', $data);
            admin_model::addAudit($this->db->last_query(), "admin_model", "addEventParlay");
            $count++;
            
            if(is_numeric($ou))
            {
                $data->overUnderScore = $ou;
                unset($data->spread);
                $this->db->insert('SportParlayCards', $data);                
                $count++;
            }                        
            
            //Update Parlay Card End Date
            $rs = $this->db->query("Select min(dateTime) as date from SportParlayCards where parlayCardId = ?", array($parlay_id));
            if($rs->num_rows())
            {
                $date = $rs->row()->date;
                $this->db->query("Update SportParlayConfig set endDate = ? where parlayCardId = ?", array($date, $parlay_id));
            }
            return $count;            
        }
        
        public function updatePowerRanks($data)
        {
            foreach($data as $key => $rows)
            {
                $rs = $this->db->query("Select * from SportCategories where name = ?", array($key));
                if(!$rs->num_rows())
                    continue;
                
                $category = $rs->row();
                foreach($rows as $row)
                {                    
                    $where = sprintf("sportCategoryId = %d AND ('%s' = name or '%s' = alt or '%s' = name or '%s' = alt)", $category->id, str_replace("'", "''", $row['TeamName']), str_replace("'", "''", $row['TeamName']), str_replace("'", "''", str_replace(" St.", " State", $row['TeamName'])), str_replace("'", "''", str_replace(" St.", " State", $row['TeamName'])));
                    $this->db->where($where);
                    //$this->db->where("name", str_replace(" St.", " State", $row['TeamName']));
                    if(!$this->db->update('SportTeams', array('powerRanking' => $row['Rank'])))
                            print_r($row);
                }
            }
        }
        
        public function getPEDropDowns($id)
        {
            $rs = $this->db->query("Select * from SportParlayConfig where parlayCardId = ?", array($id));
            $config = $rs->row();
            
            $rs = $this->db->query("Select * from SportCategories order by name");
            $categories =  $rs->result();
            
            $rs = $this->db->query("Select count(DISTINCT id) as cnt from SportParlayCards where parlayCardId = ?", array($id));
            $temp = $rs->row();
            $count = $temp->cnt;
            
            $data = array('parlay_id' => $id, 'parlay_type' => $config->type, 'parlay_date' => $config->cardDate, 'sel_parlay_cat' => NULL, 'sel_parlay_team' => NULL);                     
            $events = $this->searchParlayEvents($data);
            
            return compact("categories", "count", "config", "events");
        }
        
        function saveScheduleSequence($data)
        {
            $ids = array();
            foreach($data['ids'] as $key)
            {
                if(strstr($key, "event_"))                   
                    $ids[] = str_replace("event_", "", $key);
                elseif(strstr($key, "tr_"))                   
                    $ids[] = str_replace("tr_", "", $key);
            }
            
            $this->db->query("Update SportParlayCards set sequence = 0 where parlayCardId = ?", array($data['parlayCardId']));
            admin_model::addAudit($this->db->last_query(), "admin_model", "saveScheduleSequence");
            foreach($ids as $index => $id)
                $this->db->query("Update SportParlayCards set sequence = ? where id = ?", array(($index + 1), $id));
            return true;
        }
                
        function deleteParlayEvent($id)
        {
            $this->db->delete('SportParlayCards', array('id' => $id));
            admin_model::addAudit($this->db->last_query(), "admin_model", "deleteParlayEvent");
            return true;
        }                
        
        //All Big Game 21 functions
        public function getBGConfigs()
        {
            $rs = $this->db->query("Select c.*, count(DISTINCT q.id) as questions 
                From BGQuestionsConfig c
                Left join BGQuestions q on q.parlayCardId = c.parlayCardId
                Group by c.id");
            return $rs->result();
        }
        
        public function updateBGConfig($data)
        {
            $this->db->where('id', $data['id']);
            $this->db->update('BGQuestionsConfig', $data);
            admin_model::addAudit($this->db->last_query(), "admin_model", "updateBGConfig");
            return true;
        }
        
        public function getBGGrades($id)
        {
            $questions = array();
            $rs = $this->db->query("Select * from BGQuestionsConfig where parlayCardId = ?", array($id));
            $config = $rs->row();
            
            $selected = explode(":", $config->answerHash);
            
            $rs = $this->db->query("Select q.*, group_concat(a.id, '::', a.answer SEPARATOR '||') as tmp
                From BGQuestions q
                Inner join BGAnswers a on a.questionId = q.id
                Where q.parlayCardId = ?
                Group by q.id", array($id));
            
            if($rs->num_rows())
                $questions = $rs->result();
            
            foreach($questions as &$question)
            {
                $question->answers = array();
                $temp = explode("||", $question->tmp);
                foreach($temp as $key_value)
                {
                    $tmp = explode("::", $key_value);
                    if(count($tmp) == 2)
                        $question->answers[$tmp[0]] = $tmp[1];
                }
            }
            return compact('questions', 'selected', 'config');
        }
        
        public function getBGQuestions()
        {
            $rs = $this->db->query("Select q.*, group_concat(a.answer) as answers 
                From BGQuestions q
                Left join BGAnswers a on a.questionId = q.id
                Group by q.id");
            return $rs->result();
        }
        
        public function getBGConfig($id)
        {
            $rs = $this->db->query("Select * from BGQuestionsConfig where id = ?", array($id));
            $config = $rs->row();
            
            $rs = $this->db->query("Select q.*, group_concat(a.answer) as answers
                From BGQuestions q
                Left Join BGAnswers a on q.id = a.questionId 
                where q.parlayCardId = ?
                Group by q.id", array($id));
            $questions = $rs->result();
            
            $rs = $this->db->query("Select * from BGQuestionsPlaces where parlayCardId = ?", array($config->parlayCardId));
            $places = $rs->result();
            
            $rs = $this->db->query("Select count(*) as cnt from BGPlayerCards where parlayCardId = ?", array($config->parlayCardId));
            $cnt = $rs->row();
            $count = $cnt->cnt;
            
            $rs = $this->db->query("Select * from GameRules where gameType = 'FT' and serialNumber = 'TEMPLATE' order by id DESC");
            $rules = $rs->result();
            
            $rule = NULL;
            if($config)
            {
                $rs = $this->db->query("Select * from GameRules where serialNumber = ?", $config->serialNumber);                
                
                if($rs->num_rows())
                {
                    $rule = $rs->row();
                    $rule->text = file_get_contents(str_replace("https://d23kds0bwk71uo.cloudfront.net", "https://kizzang-legal.s3.amazonaws.com",$rule->ruleURL));
                    if(count($rules))
                        $rule->template = file_get_contents(str_replace("https://d23kds0bwk71uo.cloudfront.net", "https://kizzang-legal.s3.amazonaws.com",$rules[0]->ruleURL));
                    else
                        $rule->template = "";
                }                
            }            
            
            return compact('config', 'questions', 'places', 'count', 'rules', 'rule');
        }
        
        public function getBGQuestion($id)
        {
            $rs = $this->db->query("Select * from BGQuestions where id = ?", array($id));
            $question = $rs->row();
            
            $rs = $this->db->query("Select * from BGAnswers where questionId = ?", array($id));
            $answers = $rs->result();
            
            return compact("question", "answers");
        }
        
        public function addBGAnswer($data)
        {
            //Do update if it's an update
            if(isset($data['id']))
            {
                $this->db->where('id', $data['id']);
                $this->db->update('BGAnswers', $data);
                return true;
            }
            
            //Do Insert and create HTML row
            $this->db->insert('BGAnswers', $data);
            admin_model::addAudit($this->db->last_query(), "admin_model", "addBGAnswers");
            $id = $this->db->insert_id();
            $answer = $data['answer'];
            $row = "<tr id='tr_$id'>
                    <td><input type='text' id='answer_$id' value='$answer'/></td>
                    <td><button type='button' rel='$id' class='btn btn-success update-answer'>Update</button></td>
                    <td><button type='button' rel='$id' class='btn btn-danger delete-answer'>Remove</button></td>
                </tr>";
            return $row;
        }
        
        public function addBGPlace($data)
        {
            if(isset($data['id']))
            {
                $this->db->where('id', $data['id']);
                $this->db->update("BGQuestionsPlaces", $data);
            }
            else
            {
                $this->db->insert("BGQuestionsPlaces", $data);
            }
            admin_model::addAudit($this->db->last_query(), "admin_model", "addBGPlace");
        }
        
        public function deleteBGPlace($id)
        {
            $this->db->delete("BGQuestionsPlaces", array("id" => $id));
        }
        
        public function deleteBGAnswer($id)
        {
            $this->db->delete("BGAnswers", array('id' => $id));
            admin_model::addAudit($this->db->last_query(), "admin_model", "deleteBGAnswer");
            return true;
        }
        
        public function addBGConfig($data)
        {
            if(isset($data['id']))
            {
                $this->db->where("id", $data['id']);
                $this->db->update("BGQuestionsConfig", $data);
                return $data['id'];
            }
            
            $this->db->insert('BGQuestionsConfig', $data);
            $id = $this->db->insert_id();
            $serialNumber = sprintf("KB%05d", $id);
            
            $this->db->where('id', $id);
            $this->db->update('BGQuestionsConfig', array('serialNumber' => $serialNumber, 'parlayCardId' => $id));
            return $id;
        }
        
        public function addBGQuestion($data)
        {
            if(isset($data['id']))
            {
                $this->db->where("id", $data['id']);
                $this->db->update("BGQuestions", $data);
                return $data['id'];
            }
            
            $this->db->insert('BGQuestions', $data);
            admin_model::addAudit($this->db->last_query(), "admin_model", "addBGQuestions");
            return $this->db->insert_id();
        }
        
        public function deleteBGQuestion($id)
        {
            $this->db->delete("BGQuestions", array('id' => $id));
            admin_model::addAudit($this->db->last_query(), "admin_model", "deleteBGQuestion");
            return true;
        }
        
        public function deleteBGConfig($id)
        {
            $this->db->delete("BGQuestionsConfig", array('id' => $id));
            admin_model::addAudit($this->db->last_query(), "admin_model", "deleteBGConfig");
            return true;
        }
        
        public function geoCodePlayers()
        {      
            $ret = array();
            $rs = $this->db->query("Select id from Players where longitude = 0.0 and latitude = 0.0");
            $ret['count'] = $rs->num_rows();
            if($rs->num_rows())
            {
                foreach($rs->result() as $row)
                {
                    $player = $this->getPlayer($row->id, true);
                    if($player['address'])
                    {                                                         
                        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $player['address'];
                        if($player['address2'])
                            $url .= ", " . $player['address2'];
                        if($player['city'])
                            $url .= ", " . $player['city'];
                        if($player['state'])
                            $url .= ", " . $player['state'];
                        if($player['zip'])
                            $url .= ", " . $player['zip'];
                        $url .= "&api=" . getenv("GOOGLEGEOCODINGAPI");
                         $ch = curl_init(str_replace(" ", "+", $url)); 
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);                        
                        $json = curl_exec($ch);
                        
                        $output = json_decode($json, true);
                        
                        if(is_array($output) && isset($output['results']) && isset($output['results'][0]))
                        {
                            $location = $output['results'][0]['geometry']['location'];
                            $this->db->query("Update Players set longitude = ?, latitude = ? where id = ?", array($location['lng'], $location['lat'], $row->id));
                        }                                         
                    }
                }
            }
            $ret['success'] = true;
            return $ret;
        }
        
        //Player Tool
        
        public function searchPlayers($string = "")
        {
            $css = array(1 => 'alert-danger', 2 => 'alert-warning', 3 => '', 4 => 'alert-info', 5 => 'alert-success');
            $players = array();
            $string = str_replace("'", "''", $string);
            $array = explode(" ", $string);
            $where = "1 = 1 ";
            foreach($array as $row)
                $where .=  " AND concat(IFNULL(firstName, ''), IFNULL(lastName,''), IFNULL(screenName,''), IFNULL(accountName,''), IFNULL(email,''), IFNULL(phone,''), IFNULL(address,''), IFNULL(city,''), IFNULL(state,''), IFNULL(zip,'')) like '%" . $row . "%'";
            
            $rs = $this->db->query("Select * from Users where $where order by id DESC limit 200");
            //print $this->db->last_query(); die();
            $players = $rs->result();
            
            //print_r($players); die();
            
            return compact('players','css');
        }
        
        public function getPlayers()
        {            
            
            $rs = $this->db->query("Select * from Users order by id DESC limit 200");
            
            if($crypt->init("KizfDkj353", 1) == "ok")
            {                   
                foreach($rs->result() as $temp)
                {
                    $data = $crypt->decrypt_128( utf8_encode( $temp->accountData ) );
                    $data = explode("::", $data);
                    //0 - First Name | 1 - Last Name | 2 - Address | 3 - Address2 | 4 - City | 5 - State | 6 - Zip | 7 - Phone | 8 - CellPhone | 9 - Email | 10 - DOB | 11 - Gender | 12 - Unknown
                    $i = 0;
                    $player = array();
                    foreach($template as $value)                    
                        $player[$value] = $data[$i++];
                    
                    $player['id'] = $temp->id;
                    $player['num_status'] = $temp->status;
                    $player['status_css'] = $css[$temp->status];
                    $player['status'] = $temp->isDeleted ? "Deleted" : ($temp->isSuspended ? "Suspended" : $temp->emailNotificationId ? "Email Suspended" : "Good");
                    $player['fbid'] = $crypt->decrypt_128(utf8_encode($temp->fbId));
                    $player['screen_name'] = $temp->screenName;
                    $player['email_verified'] = $temp->emailVerified;
                    $player['created'] =  $temp->accountCreated; 
                    $player['emailHash'] = $temp->emailHash;
                    $player['passwordHash'] = $temp->passwordHash;
                    $player['account_email'] = $crypt->decrypt_128(utf8_encode($temp->accountEmail));                    
                    $rows[] = array('id' => $temp->id, 'information' => json_encode($player));
                }
                        
                //print_r($rows); die();
                if($rows)
                    $this->db->insert_batch('PlayerSearch', $rows);
            }
        }
        
        public function getPlayerImages()
        {                        
            $rs = $this->db->query("Select * from Users where fbid IS NOT NULL AND fbid <> ''");
            return $rs->result();
        }                
        
        public function getPlayerDaily($player_id, $date)
        {
            $data = array();
            $rs = $this->db->query("Select * from reports.PlayerEvents where player_id = ? and date(started) = ? and game_type in ('Slots', 'Parlay', 'Scratchers', 'Sweepstakes') order by started", array($player_id, $date));
            return $rs->result();
        }    
        
        public function getPlayerTable($id, $player_only = false)
        {    
            $template = array('first_name', 'last_name', 'address', 'unknown', 'city', 'state', 'zip', 'phone', 'cellphone', 'email', 'dob', 'gender', 'address2');
            $crypt = new Crypt();
            $player = array();
            if($crypt->init("KizfDkj353", 1) == "ok")
            {
                $rs = $this->db->query("Select * from Players where id = ?", array($id));
                if($rs->num_rows())
                {
                    $temp = $rs->row();
                    $data = $crypt->decrypt_128( utf8_encode( $temp->accountData ) );
                    $data = explode("::", $data);
                    //0 - First Name | 1 - Last Name | 2 - Address | 3 - Unknown | 4 - City | 5 - State | 6 - Zip | 7 - Phone | 8 - CellPhone | 9 - Email | 10 - DOB | 11 - Gender | 12 - Unknown
                    $i = 0;
                    $player = array();
                    foreach($template as $value)                    
                        $player[$value] = $data[$i++];
                    
                    $player['accountData'] = $temp->accountData;
                    $player['status'] = $temp->status;
                    $player['gender'] = $temp->gender;
                    $player['passwordHash'] = $temp->passwordHash;
                    $player['lastApprovedTOS'] = $temp->lastApprovedTOS;
                    $player['lastApprovedPrivacyPolicy'] = $temp->lastApprovedPrivacyPolicy;
                    $player['referralId'] = $temp->referralID;
                    $player['accountCreated'] = $temp->accountCreated;
                    $player['phoneCode'] = $temp->phoneCode;
                    $player['accountPhone'] = $crypt->decrypt_128($temp->accountPhone);
                    $player['accountEmail'] = $crypt->decrypt_128($temp->accountEmail);
                    $player['payPal'] = $temp->payPal;
                    $player['paypal_email'] = $crypt->decrypt_128(utf8_encode($temp->payPal));
                    $player['id'] = $temp->id;
                    $player['roleId'] = $temp->roleId;
                    $player['isDeleted'] = $temp->isDeleted;
                    $player['isCelebrity'] = $temp->isCelebrity;
                    $player['isSuspended'] = $temp->isSuspended;
                    $player['emailNotificationId'] = $temp->emailNotificationId;
                    $player['emailVerified'] = $temp->emailVerified;
                    $player['fbid'] = $crypt->decrypt_128(utf8_encode($temp->fbId));
                    $player['screen_name'] = $temp->screenName;
                    $player['email_verified'] = $temp->emailVerified;
                    $player['emailCode'] = $temp->emailCode;
                    $player['emailHash'] = $temp->emailHash;
                    $player['newUserFlow'] = $temp->newUserFlow;
                    $player['profileComplete'] = $temp->profileComplete;
                    $player['account_email'] = $crypt->decrypt_128(utf8_encode($temp->accountEmail));
                }                 
            }
            
            return $player;
        }
        
        public function getPlayer($id, $player_only = false)
        {                               
            
            $player = array();
            $rs = $this->db->query("Select * from Users where id = ?", array($id));
            if($rs->num_rows())
            {
                $temp = $rs->row();
                $player = json_decode(json_encode($temp), true);                                
            }
            
            if($player_only)
                return $player;
            
            $devices = array();
            $rs = $this->db->query("Select * from notifications.players where player_id = ?", array($id));
            if($rs->num_rows())
            {
                foreach($rs->result() as $row)
                {
                    $row->timezone = timezone_name_from_abbr("", $row->timezone, 0);
                    //Check to see if it's in the devices table
                    $drs = $this->db->query("Select * from notifications.devices where device = ?", array($row->device_model));
                    if($drs->num_rows())
                        $row->tooltip = $drs->row();
                    $devices[] = $row;
                }                
            }
            
            $versions = array();
            $rs = $this->db->query("Select loginType, loginSource, mobileType, max(appId) as appId from PlayerLogins where playerId = ? and appId <> '0' group by loginType, loginSource, mobileType", array($id));
            if($rs->num_rows())            
                $versions = $rs->result();            
            
            $rs = $this->db->query("Select distinct(date(started)) as date from reports.PlayerEvents where player_id = ? order by started DESC", array($id));
            $daily_action = $rs->result();
                        
            $roles = $this->getColumnEnum("kizzang", "Users", "userType");
            $accountStatuses = $this->getColumnEnum("kizzang", "Users", "accountStatus");
            $genders = $this->getColumnEnum("kizzang", "Users", "gender");
            $emailStatuses = $this->getColumnEnum("kizzang", "Users", "emailStatus");
            
            $rs = $this->db->query("SELECT p.playerId, date(p.endDate) as date, gameType, sum(count) as count 
                FROM GameCount g
                Inner join PlayPeriod p on g.playPeriodId = p.id
                Where date(endDate) > now() - INTERVAL 2 WEEK and p.playerId = ?
                Group by p.playerId, date(endDate), gameType
                With Rollup", array($id));
            $stats = $rs->result();
            
            $rs = $this->db->query("Select w.serial_number as serialNumber, w.amount, w.prize_email, p.status, w.game_type, w.game_name, w.status as winStatus, w.prize_name, convert_tz(w.created, 'GMT', 'US/Pacific') as win_date 
                from Winners w
                Left join Payments p on p.winnerId = w.id
                where player_id = ? order by w.created DESC", array($id));
            $winners = $rs->result();
            
            $rs = $this->db->query("Select sum(count) as cnt from Chedda where playerId = ? and isUsed = 0", array($id));
            $chedda = $rs->row()->cnt;
            
            return compact('stats', 'player', 'roles', 'genders', 'accountStatuses', 'winners', 'daily_action', 'devices','versions', 'emailStatuses','chedda');
        }
               
        public function getPlayerNotes($id)
        {
            $rs = $this->db->query("Select pn.*, p.screenName as author
                From PlayerNotes pn
                Inner join Users p on pn.authorId = p.id
                Where pn.playerId = ? order by created DESC", array($id));
            $notes  = $rs->result();
            return compact('notes', 'id');
        }
        
        public function savePlayerNote($data)
        {
            $user = $this->nativesession->get('User');
            $data['authorId'] = $user['id'];

            if($this->db->insert("PlayerNotes", $data))
                    return true;
            return false;
        }
        
        public function updatePlayer($data)
        {
            if($data['password'])
                $data['passwordHash'] = md5($data['password']);
            
            unset($data['password']);
            
            if(isset($data['phone']) && !$data['phone'])
                $data['phone'] = NULL;
            
            if(isset($data['email']) && !$data['email'])
                $data['email'] = NULL;
            
            $rs = $this->db->query("Select * from Users where id = ?", array($data['id']));
            $origPlayer = $rs->row();
            
            $this->db->where('id', $data['id']);
            $this->db->update("Users", $data);                       
            
            if($origPlayer->accountStatus == "Active" && ($data['accountStatus'] == "Suspended" || $data['accountStatus'] == "Deleted"))  // Sent Deactivation Email
            {
                $body = $this->load->view("emails/wrapper", array('content' => $this->load->view("emails/accountClosed", array(), true), 'emailCode' => md5($data['email'])), true);
                $this->sendGenericEmail($origPlayer->email, "Kizzang - Account Disabled", $body);
            }
            //print $this->db->last_query(); die();
            admin_model::addAudit($this->db->last_query(), "admin_model", "updatePlayer");
            return true;            
        }
        
        public function forceLogout($id)
        {
            //Remove the key from the Sessions Table and delete from Memcache
            $rs = $this->db->query("Select * from Users where id = ?", array($id));            
            if($rs->num_rows())
            {                
                $row = $rs->row();                
                $token = base64_encode(implode(self::ENCRYPTION_KEY, array(md5($row->accountName), $row->passwordHash)));      
                $header = array(getenv("APIKEY"), "TOKEN: " . $token);
                $url = getenv("APISERVER") . "api/1/players/logout";
                $ch = curl_init($url);                          
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $ret = json_decode(curl_exec($ch), true);
                if($row->fbId && !isset($ret['code'])) // If facebook account, try to kill both possible tokens
                {
                    $token = base64_encode(implode(self::ENCRYPTION_KEY, array(md5($row->email), md5(NULL))));      
                    $header = array(getenv("APIKEY"), "TOKEN: " . $token);
                    $url = getenv("APISERVER") . "api/1/players/logout";
                    $ch = curl_init($url);                          
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $ret = json_decode(curl_exec($ch), true);
                }
                return  $ret;
            }
            return json_encode(array('code' => 1, 'message' => 'Logout Unsuccessful'));
        }
        
        //Cloudfront Functions
        function invalidateCloudfrontFiles($bucket, $files)
        {            
           foreach($files as &$file)
               $file = "/" . trim(str_replace ("||", "/", $file), "/");
           
           $distribution_codes = array('kizzang-legal' => 'EO6OOFN8RSVUY', 'kizzang-resources' => 'E2MUABVFHEQ4F7', 'kizzang-campaigns' => 'EUN66N7T9ZCV8');            
                     
           $cloudfront = \Aws\CloudFront\CloudFrontClient::factory(array('credentials' => array('key' => 'AKIAJPNUHVSQACCNDALQ', 'secret' => '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'), 'region' =>'us-east-1', 'version' => '2016-01-28'));           

           $cloudfront->createInvalidation(array('DistributionId' => $distribution_codes[$bucket], 'InvalidationBatch' => array('Paths' => array('Quantity' => count($files), 'Items' => $files), 'CallerReference' => time())));
           
        }
        
        static function addAudit($query_statement, $model, $action)
        {
            $model_main = new CI_Model();
            $db = $model_main->load->database ('admin', true);
            
            //Get current user or set to 1 since the crons don't have to login
            if(isset($_SESSION['User']))  
                $player_id = $_SESSION['User']['id'];            
            else            
                $player_id = 1;
            
            $query = sprintf("Insert into Audits (player_id, query_statement, model, action) values (%d, '%s', '%s', '%s')", $player_id, str_replace("'", "''", str_replace("\'", "'", $query_statement)), str_replace("'", "''", $model), str_replace("'", "''", $action));
            //print $query;
            $db->query($query);
        }
        
        function getCloudfrontFiles($bucket)
        {
            $this->load->library('s3');
            //print_r($this->s3->listBuckets());die();
            $buckets = array('kizzang-legal' => "Kizzang Legal (Rules)", 'kizzang-resources' => "Main Resource", 'kizzang-campaigns' => "Campaigns");
            $ret = array();
            switch($bucket)
            {
                case "kizzang-legal": 
                case "kizzang-resources-sweepstakes": 
                case "kizzang-campaigns":
                    $rows = $this->s3->getBucket($bucket); break;
                case "kizzang-resources": 
                    $rows = $this->s3->getBucket($bucket, "_prod/swf");
                    $rows = array_merge($this->s3->getBucket($bucket, "_dev/swf"), $rows); break;
                default: $rows = array();
            }
            
            foreach($rows as $row)
            {
                $obj = array();
                $obj['id'] = str_replace("/", "||", $row['name']);
                $path = explode("||", $obj['id']);
                $obj['text'] = $path[count($path) - 1];
                unset($path[count($path) - 1]);
                $parent = implode("||", $path);
                 if($path)
                    $obj['parent'] = $parent;
                else
                    $obj['parent'] = "#";    
                $obj['icon'] = "https://kizzang-resources-admin.s3.amazonaws.com/icons/" . substr($obj['text'], strrpos($obj['text'], ".") + 1) . ".png";
                while(!isset($obj[$parent]) && $parent != "#" && $path)
                {
                    $pobj = array();
                    $pobj['id'] = implode("||", $path);
                    $pobj['text'] = $path[count($path) - 1];
                    unset($path[count($path) - 1]);
                    if($path)
                        $parent = implode("||", $path);
                    else
                        $parent = "#";
                    $pobj['parent'] = $parent;
                    $ret[$pobj['id']] = $pobj;
                }
               
                if($obj['text'])
                    $ret[$obj['id']] = $obj;
            }
            $clean = array();
            foreach($ret as $row)
                $clean[] = $row;
            $json = json_encode($clean);
            return compact('json', 'buckets');
        }
        
        //All localization / internationalization functions
        public function getStrings($language)
        {
            $rs = $this->db->query("Select * from Localization where languageCode = ?", array($language));
            $strings =  $rs->result();
            
            $rs = $this->db->query("Select * from Languages order by language");
            $languages = $rs->result();
            
            return compact('strings', 'languages');
        }
        
        public function getString($id, $cur_language)
        {
            $string = NULL;
            if($id)
            {
                $rs = $this->db->query("Select * from Localization where id = ?", array($id));
                if($rs->num_rows())
                    $string = $rs->row();
            }
            
            $rs = $this->db->query("Select * from Languages order by language");
            $languages = $rs->result();
                
            $rs = $this->db->query("Select DISTINCT identifier from Localization order by identifier");
            $identifiers = $rs->result();
            
            return compact('string', 'languages', 'identifiers', 'cur_language');
        }
        
        public function addString($data)
        {
            if(isset($data['id']))
            {
                $this->db->where('id', $data['id']);
                $this->db->update('Localization', $data);
            }
            else
            {
                $this->db->insert('Localization', $data);
            }
            admin_model::addAudit($this->db->last_query(), "admin_model", "addString");
            return true;
        }
        
        //Functions for all the rules
        public function addGameRule($data)
        {
            $this->load->library('s3');
            $base = "https://d23kds0bwk71uo.cloudfront.net/" .getenv("ENV"). "/game_rules/";
            //Save the file first
            $cur_file = $data['serial_number'] . ".txt";
            $filename = rand(1, 10000000);
            $fh = fopen("/tmp/" . $filename, "w");
            fwrite($fh, $data['text']);
            fclose($fh);

            try{
                $this->s3->putObjectFile("/tmp/" . $filename, 'kizzang-legal', getenv("ENV"). '/game_rules/' . $cur_file, 'public-read');
            } catch (Exception $ex) {
                log_message("error", $ex->getMessage());
                return false;
            }                
           
            //Time to put it into the DB
            $rec = array();
            $rec['serialNumber'] = $data['serial_number'];
            $rec['gameType'] = $data['game_type'];
            $rec['ruleURL'] = $base . $cur_file;
            $rec['name'] = $data['name'];
            $rec['startDate'] = $data['startDate'];
            $rec['endDate'] = $data['endDate'];
            $rs = $this->db->query("Select * from GameRules where serialNumber = ?", array($data['serial_number']));
            if($rs->num_rows())
            {
                $this->db->where('serialNumber', $data['serial_number']);
                $this->db->update('GameRules', $rec);
            }
            else
            {
                $this->db->insert("GameRules", $rec);
            }
            admin_model::addAudit($this->db->last_query(), "admin_model", "addGameRules");
            //Invalidate file            
            //$this->invalidateCloudfrontFiles("kizzang-legal", array(ENV . '/game_rules/' . $cur_file));
            return true;        
        }
        
        public function addRuleTemplate($data)
        {
            $this->load->library('s3');
            $base = "https://d23kds0bwk71uo.cloudfront.net/rules/";
            $filename = basename($data['sel_file_name'], ".txt");
            $matches = array();
            $files = $this->s3->getBucket("kizzang-legal", "rules/");
            if($data['save_options'] == 0) //Create New Template
            {                     
                if(preg_match("/_v([0-9]+)$/", $filename, $matches))
                    $filename = str_replace("_v" . $matches[1], "_v" . ($matches[1] + 1), $filename);
                else
                    $filename .= "_v1";        
                
                while(array_key_exists("rules/" . $filename . ".txt", $files))
                {
                    preg_match("/_v([0-9]+)$/", $filename, $matches);
                    $filename = str_replace("_v" . $matches[1], "_v" . ($matches[1] + 1), $filename);
                }
            }
            
            $filename .= ".txt";
            
            $temp_name = rand(1, 10000000);
            $fh = fopen("/tmp/" . $temp_name, "w");
            fwrite($fh, $data['text']);
            fclose($fh);

            try{
                $this->s3->putObjectFile("/tmp/" . $temp_name, 'kizzang-legal', 'rules/' . $filename, 'public-read');
            } catch (Exception $ex) {
                log_message("error", $ex->getMessage());
                return false;
            }                

            //Time to put it into the DB
            $rec = array();
            $rec['serialNumber'] = "TEMPLATE";
            $rec['gameType'] = $data['game_type'];
            $rec['ruleURL'] = $base . $filename;
            
            if($data['save_options'] == 1)
            {
                $this->db->where('ruleURL', $data['sel_file_name']);
                $this->db->update('GameRules', $rec);
            }
            else
            {
                $this->db->insert("GameRules", $rec);
            }
            admin_model::addAudit($this->db->last_query(), "admin_model", "addRuleTemplate");
            return true;
        }
        
        public function addRule($data, &$text)
        {
            //die("HERE");
            $this->load->library('s3');
            $base = "https://d23kds0bwk71uo.cloudfront.net/" . getenv("ENV") . "/game_rules/";
            
            $file = $data['file_name'];
            if($file)
            {
                $text = file_get_contents(str_replace("https://d23kds0bwk71uo.cloudfront.net", "https://kizzang-legal.s3.amazonaws.com", $file));
            }
            else
            {
                $text = "File not Found";
                return false;
            }
            if($data['game_type'] ==  "Sweepstakes")
            {
                $rs = $this->db->query("Select * from Sweepstakes where id = ?", array( ltrim(substr($data['serial_number'], 2), "0")));
                if(!$rs->num_rows())
                {
                    $text = "Sweepstakes not found!";
                    return false;
                }
                $sweepstakes = $rs->row();
                $sweepstakes->serialNumber = $data['serial_number'];
                $data['startDate'] = $sweepstakes->startDate;
                $data['endDate'] = $sweepstakes->endDate;                
                
                $xlat = array('NAME' => $sweepstakes->name, 
                    'START_DATE' => date('m/d/Y', strtotime($sweepstakes->startDate)), 
                    'END_DATE' => date('m/d/Y', strtotime($sweepstakes->endDate)), 
                    'START_TIME' => date('g:i:s a', strtotime($sweepstakes->startDate)),
                    'END_TIME' => date('g:i:s a', strtotime($sweepstakes->endDate) - 1),                    
                    'DESCRIPTION' => $sweepstakes->description,                    
                    "SERIAL_NUMBER" => $sweepstakes->serialNumber,
                    "TICKET_RATIO" => $sweepstakes->ratioTicket,
                    "SERIAL_NUMBER" => $data['serial_number'],
                    "TOTAL_PRIZES" => $sweepstakes->maxWinners,
                    "DESCRIPTION" => $sweepstakes->displayName
                    );                                
            }
            elseif($data['game_type'] == "Slots")
            {
                $db = $this->load->database ('slots', true);
                $nums = array('First', 'Second', 'Third', 'Fourth', 'Fifth', 'Sixth', 'Seventh', 'Eighth', 'Ninth', 'Tenth');
                $rs = $db->query("Select * from SlotTournament where ID = ?", array( ltrim(substr($data['serial_number'], 2), "0")));
                if(!$rs->num_rows())
                {
                    $text = "Slot Tournament not found!";
                    return false;
                }
                $slot = $rs->row();
                $data['startDate'] = $slot->StartDate;
                $data['endDate'] = $slot->EndDate;
                $data['name'] = $data['serial_number'];
                $prizes = explode('","', trim(trim($slot->PrizeList, '["'), '"]'));
                $prize_string = "";
                foreach($prizes as $index => $prize)
                {
                    $prize_string .= "One (1) " . $nums[$index] . " Prize is available consisting of $prize\n\n";
                }
                $xlat = array(
                    'START_DATE' => date('m/d/Y', strtotime($slot->StartDate)), 
                    'END_DATE' => date('m/d/Y', strtotime($slot->EndDate)), 
                    'START_TIME' => date('g:i:s a', strtotime($slot->StartDate)),
                    'END_TIME' => date('g:i:s a', strtotime($slot->EndDate)),                    
                    'PRIZES' => $prize_string,
                    "SERIAL_NUMBER" => $data['serial_number']
                    );
            }
            elseif($data['game_type'] == "Scratchers")
            {                
                $db = $this->load->database ('default', true);
                
                $rs = $db->query("Select * from Scratch_GPGames where SerialNumber = ?", array($data['serial_number']));
                if($rs->num_rows())
                        $game = $rs->row();
                
                $rs = $db->query("Select * from Scratch_GPPayout where PayoutID = ? order by Rank", array($game->PayoutID));
                $payouts = $rs->result();
                
                $individual_odds = "";
                foreach($payouts as $payout)
                {
                    if($payout->Count == 0)
                        continue;
                    $individual_odds .= (($game->TotalCards / $game->CardIncrement) * $payout->Count) .  " " . $payout->PrizeName . " prizes are available.\nOdds of winning a " . $payout->PrizeName . " prize are 1:" . ($game->TotalWinningCards > 0 ? number_format($game->TotalCards / (($game->TotalCards / $game->CardIncrement) * $payout->Count), 0) : 0) . "\n\n";
                } 
                $prize_odds = $game->TotalWinningCards . " prizes are available. \nOdds of winning a prize are 1:" . ($game->TotalWinningCards > 0 ? number_format($game->TotalCards / $game->TotalWinningCards, 0) : 0) . "\n";
                $data['startDate'] = date('Y-m-d', strtotime($game->StartDate) -1);
                $data['endDate'] = $game->EndDate;
                
                $xlat = array('SCRATCHER_NAME' => $game->Name, 
                    'START_DATE' => date('m/d/Y', strtotime($game->StartDate)), 
                    'END_DATE' => date('m/d/Y', strtotime($game->EndDate)), 
                    'START_TIME' => date('g:i:s a', strtotime($game->StartDate)),
                    'END_TIME' => date('g:i:s a', strtotime($game->StartDate) - 1),
                    'TOTAL_PLAYS' => number_format($game->TotalCards, 0), 
                    'NUMBER_OF_PRIZES' => $game->TotalWinningCards, 
                    'OVERALL_ODDS' => $prize_odds,
                    'INDIVIDUAL_ODDS' => $individual_odds,
                    'DESCRIPTION' => "Cash",
                    'TOTAL_CARDS' => number_format($game->TotalCards, 0),
                    "SERIAL_NUMBER" => $game->SerialNumber
                    );
            }
            elseif($data['game_type'] == "Parlay")
            {
                $rs = $this->db->query("Select * from SportParlayConfig where id = ?", array( ltrim(substr($data['serial_number'], 2), "0")));
                if(!$rs->num_rows())
                {
                    $text = "Parlay Card not found!";
                    return false;
                }
                
                $config = $rs->row();
                $rs = $this->db->query("Select * from SportParlayPlaces where parlayCardId = ?", array($config->parlayCardId));
                $prize = "";
                if($rs->num_rows())
                {
                    foreach($rs->result() as $row)
                        $prize .= $row->prize . ",";
                    $prize = trim($prize, ",");
                }
                else
                {
                    $prize = $config->cardWin;
                }
                
                $data['startDate'] = date('Y/m/d', strtotime($config->cardDate) - 86400);
                $data['endDate'] = $config->endDate;
                $xlat = array( 
                    'CARD_DATE' => date('m/d/Y', strtotime($config->cardDate) - 86400), 
                    'END_DATE' => date('m/d/Y', strtotime($config->endDate) - 901), 
                    'CARD_TIME' => date('g:i:s a', strtotime($config->cardDate)),
                    'END_TIME' => date('g:i:s a', strtotime($config->endDate) - 901),
                    'HALFVALUE' => '$' . number_format(str_replace(",", "", $config->cardWin) /2, 0),
                    'PRIZES' => $prize,                    
                    "SERIALNUMBER" => $data['serial_number']
                    );
                $data['name'] = $config->serialNumber;
            }            
            elseif($data['game_type'] == "FT")
            {
                $rs = $this->db->query("Select * from FinalConfigs where serialNumber = ?", array($data['serial_number']));
                if(!$rs->num_rows())
                {
                    $text = "Final 3 Card not found!";
                    return false;
                }
                $config = $rs->row();
                
                $xlat = array(                     
                    "SERIALNUMBER" => $data['serial_number']
                    );
                $data['name'] = $config->serialNumber;
                $data['startDate'] = $config->startDate;
                $data['endDate'] = $config->endDate;
            }
            elseif($data['game_type'] == "Bracket")
            {
                $rs = $this->db->query("Select * from BracketConfigs where id = ?", array(trim(trim($data['serial_number'], 'KB'), '0')));
                if(!$rs->num_rows())
                {
                    $text = "Bracket not found!";
                    return false;
                }
                $config = $rs->row();
                
                $xlat = array(                     
                    "SERIAL_NUMBER" => $data['serial_number']
                    );
                $data['name'] = $data['serial_number'];
                $data['startDate'] = $config->startDate;
                $data['endDate'] = $config->endDate;
            }
            elseif($data['game_type'] == "Lottery")
            {
                $rs = $this->db->query("Select * from LotteryConfigs where id = ?", array(ltrim(ltrim($data['serial_number'], 'KL'), '0')));
                if(!$rs->num_rows())
                {
                    $text = "Lottery Config not Found!";
                    return false;
                }
                $config = $rs->row();
                
                $xlat = array(                     
                    "SERIAL_NUMBER" => $data['serial_number']
                    );
                $data['name'] = $data['serial_number'];
                $data['startDate'] = $config->startDate;
                $data['endDate'] = $config->endDate;
            }
            
            foreach($xlat as $key => $value)
            {
                $text = str_replace("[$key]", $value, $text);
            }
            
            $data['text'] = $text;
            if(!$this->addGameRule($data))
            {
                $text = "Error Saving file";
                return false;
            }
            
            return true;
        }
        
        //All functions for Sponsors
        public function getAdvertisingCampaigns()
        {
            $rs = $this->db->query("Select c.*, s.name from Sponsor_Advertising_Campaigns c
                Inner join Sponsors s on s.id = c.utm_source
                Order by c.created DESC");
            return $rs->result();
        }
        
        public function getAdvertisingCampaign($id = NULL)
        {
            $campaign = NULL;
            $reports = NULL;
            if($id)
            {
                $rs = $this->db->query("Select * from Sponsor_Advertising_Campaigns where id = ?", array($id));
                $campaign = $rs->row();
                
                $campaign->url = "https://kizzang.com/" . $campaign->redirect_url . "?id=" . $campaign->id . "&s=" . $campaign->utm_source . "&m=" . $campaign->advertising_medium_id . 
                        "&t=" . $campaign->utm_campaign . "&c=" . $campaign->utm_content . "&d=" . ($campaign->d == 'Web' ? 1 : 2);
                
                
                $rs = $this->db->query("Select date(convert_tz(created, 'GMT', 'US/Pacific')) as date, destination as platform, count(*) as cnt from 
                    marketing.impressions where email_campaign_id = ? group by date(convert_tz(created, 'GMT', 'US/Pacific')), destination with rollup", array($id));
                $reports = $rs->result();
            }
            
            $rs = $this->db->query("Select * from Advertising_Mediums order by description");
            $mediums = $rs->result();
            
            $ds = array('Web', 'Facebook');
            $urls = array('ref', 'refad');
            $cdns = array('None','var000','var001','var002');
            
            $rs = $this->db->query("Select * from Sponsors where sponsorType = 'Advertiser'");
            $sponsors = $rs->result();
                                    
            return compact('campaign','mediums','sponsors','reports', 'ds', 'urls','cdns');
        }
        
        public function addAdvertisingCampaign($data)
        {
            $this->db->insert('Sponsor_Advertising_Campaigns', $data);
            $this->db->where("id", $data['id']);
            $this->db->update('Sponsor_Advertising_Campaigns', $data);
            admin_model::addAudit($this->db->last_query(), "admin_model", "addAdvertisingCampaign");
            return true;
        }
        
        public function getSponsors()
        {
            $rs = $this->db->query("Select * from Sponsors order by name");
            return $rs->result();
        }
        
        public function getSponsor($id)
        {
            $rs = $this->db->query("Select * from Sponsors where id = ?", array($id));
            $sponsor = $rs->row();
            
            return compact('sponsor');
        }
        
        public function addSponsor($data)
        {
            if(isset($data['id']))
            {
                $this->db->where('id', $data['id']);
                $this->db->update('Sponsors', $data);
            }
            else
            {
                $this->db->insert('Sponsors',$data);
            }
            admin_model::addAudit($this->db->last_query(), "admin_model", "addSponsor");
            return true;
        }
        
        public function getSponsorCampaigns()
        {
            $rs = $this->db->query("Select sc.*, s.name as sponsor, t.name as campaign_type  
                from Sponsor_Campaigns sc
                Inner join Sponsors s on s.id = sc.sponsorID
                Inner join Sponsor_Campaign_Types t on t.id = sc.type
                order by s.name");
            return $rs->result();
        }
        
        public function getSponsorCampaign($id)
        {
            if($id)
            {
                $rs = $this->db->query("Select * from Sponsor_Campaigns where id = ?", array($id));
                $sponsor = $rs->row();
            }
            else
            {
                $sponsor = array();
            }
            
            $rs = $this->db->query("Select * from Genders");
            $genders = $rs->result();
            
            $rs = $this->db->query("Select abbreviation, name from MapStates order by name");
            $states = $rs->result();
            
            $rs = $this->db->query("Select id, name from Sponsors order by name");
            $snames = $rs->result();
            
            $rs = $this->db->query("Select * from Sponsor_Campaign_Types");
            $types = $rs->result();
            
            return compact('sponsor', 'genders', 'states', 'snames', 'types');
        }
        
        public function addSponsorCampaign($data)
        {
            //switch all URLs to cloudfront
            $data['artAssetUrl'] = str_replace("https://kizzang-campaigns.s3.amazonaws.com", "https://d1vksrhd974otw.cloudfront.net", $data['artAssetUrl']);
            $data['modalAssetUrl'] = str_replace("https://kizzang-campaigns.s3.amazonaws.com", "https://d1vksrhd974otw.cloudfront.net", $data['modalAssetUrl']);
            $data['mapID'] = 1;
            
            if(isset($data['id']))
            {
                $this->db->where('id', $data['id']);
                $this->db->update('Sponsor_Campaigns', $data);
            }
            else
            {
                $this->db->insert('Sponsor_Campaigns',$data);
            }
            admin_model::addAudit($this->db->last_query(), "admin_model", "addSponsorCampaign");
            return true;
        }
        
        public function deleteSponsorCampaign($id)
        {
            $this->db->delete("Sponsor_Campaigns", array('id' => $id));
            admin_model::addAudit($this->db->last_query(), "admin_model", "deleteSponsorCampaign");
            return true;
        }
        
        public function mergeSpotsCurves()
        {
            $rs = $this->db->query("Select * from MapStates where bezierCurveSpots IS NOT NULL AND bezierCurveSpots <> ''");
            foreach($rs->result() as $row)
            {
                $spots = json_decode($row->Spots);
                $curves = json_decode($row->bezierCurveSpots);
                $new_spots = array();
                foreach($spots as $key => $spot)
                {
                    if(isset($curves[$key]))
                    {
                        $spot->bezx1 = $curves[$key]->x1;
                        $spot->bezy1 = $curves[$key]->y1;
                        $spot->bezx2 = $curves[$key]->x2;
                        $spot->bezy2 = $curves[$key]->y2;
                        $new_spots[] = $spot;
                    }                    
                }
                $this->db->where("id", $row->id);
                $this->db->update("MapStates", array('Spots' => json_encode($new_spots)));                
            }
        }
        
        public function getMapInfo()
        {
            //Get Day Spots
            $spots = array();
            $beziers = array();
            $rs = $this->db->query("Select panelColumn as xoffset, panelRow as yoffset, Spots, id, bezierCurveSpots, stateId from MapStates where Spots IS NOT NULL");
            $temp = $rs->result();
            $i = 0;
            $j = 0;
            foreach($temp as $row)
            {
                $array = json_decode($row->Spots);
                foreach($array as $point)
                {
                    $point->orig_x = $point->x;
                    $point->orig_y = $point->y;
                    $point->x += $row->xoffset * 960;
                    $point->y += $row->yoffset * 720;
                    $point->xoffset = $row->xoffset;
                    $point->yoffset = $row->yoffset;
                    $point->id = $row->id;
                    if(!isset($point->day))                        
                        $point->day = NULL;
                    
                    $spots[$point->day] = $point;
                }                                   
            }
            
            foreach($temp as $row)
            {
                if(!$row->bezierCurveSpots)
                {
                    $array = json_decode($row->Spots);
                    foreach($array as $bezier)
                    {
                        if(!isset($spots[$bezier->day + 1]))
                            continue;
                        
                        $temp = array('id' => $bezier->day,
                            'stateId' => $row->stateId,
                            'x1' => $spots[$bezier->day]->x,
                            'y1' => $spots[$bezier->day]->y,
                            'x2' => $spots[$bezier->day]->x + ($bezier->x > 100 ? -30 : 30),
                            'y2' => $spots[$bezier->day]->y + ($bezier->y > 100 ? -30 : 30),
                            'x3' => $spots[$bezier->day + 1]->x + ($bezier->x > 100 ? -50 : 50),
                            'y3' => $spots[$bezier->day + 1]->y + ($bezier->y > 100 ? -50 : 50),
                            'x4' => $spots[$bezier->day + 1]->x,
                            'y4' => $spots[$bezier->day + 1]->y);
                        $beziers[$bezier->day] = $temp;
                    }
                    continue;
                }
                
                $array = json_decode($row->bezierCurveSpots);
               
                foreach($array as $bezier)
                {                    
                    $temp = array('id' => $bezier->day,
                        'stateId' => $row->stateId,
                        'x1' => $spots[$bezier->day]->x,
                        'y1' => $spots[$bezier->day]->y,
                        'x2' => $bezier->x1 + ($spots[$bezier->day]->xoffset * 960),
                        'y2' => $bezier->y1 + ($spots[$bezier->day]->yoffset * 720),
                        'x3' => $bezier->x2 + ($spots[$bezier->day]->xoffset * 960),
                        'y3' => $bezier->y2 + ($spots[$bezier->day]->yoffset * 720),
                        'x4' => $spots[$bezier->day + 1]->x,
                        'y4' => $spots[$bezier->day + 1]->y);
                    $beziers[$bezier->day] = $temp;
                }
            }
            
            asort($beziers);
            //print_r($beziers); die();

            $ads = array();
            $rs = $this->db->query("Select xPos as x, yPos as y, panelColumn as xoffset, panelRow as yoffset, artAssetUrl as image, sc.id
                From Sponsor_Campaigns sc
                Inner join MapStates m on sc.stateID = m.abbreviation
                Where sc.type = 1 or sc.type = 4");
            $temp = $rs->result();
            foreach($temp as $row)
            {
                $row->orig_x = $row->x;
                $row->orig_y = $row->y;
                $row->x += $row->xoffset * 960;
                $row->y += $row->yoffset * 720;
                $ads[] = $row;
            }
            
            //Now, get all the map configs
            $rs = $this->db->query("Select * from Configs where main_type = 'Map' and sub_type = 'Days'");
            if($rs->num_rows())
            {
                $config = array();
                foreach($rs->result() as $row)
                {
                    switch($row->data_type)
                    {
                        case 'Numeric': $info = (int) $row->info; break;
                        case 'Text': $info = $row->info; break;
                        case 'JSON': $info = json_decode($row->info, true); break;
                        case 'Serialize': $info = unserialize($row->info); break;
                        default: $info = $row->info; 
                    }            
                    $config[$info['day_number']][$info['action']] = $info;
                }        
            }            
            return compact('spots', 'ads', 'beziers','config');
        }
        
        public function addBezierEntries($data)
        {
            $state_data = $map_states = array();
            $rs = $this->db->query("Select * from MapStates order by stateId");
            $temp = $rs->result();            
            foreach($temp as $row)
                $map_states[$row->stateId] = $row;
            foreach($data['data'] as $day)
            {
                if(!is_array($day))
                    continue;
                
                $temp = new stdClass();
                $temp->x1 = $day['x1'] - ($map_states[$day['stateId']]->panelColumn * 960);
                $temp->y1 = $day['y1'] - ($map_states[$day['stateId']]->panelRow * 720);
                $temp->x2 = $day['x2'] - ($map_states[$day['stateId']]->panelColumn * 960);
                $temp->y2 = $day['y2'] - ($map_states[$day['stateId']]->panelRow * 720);
                $temp->day = $day['id'];
                $state_data[$day['stateId']][] = $temp;
            }
            
            foreach($state_data as $key => $state)
            {
                $this->db->where("stateId", $key);
                $this->db->update("MapStates", array('bezierCurveSpots' => json_encode($state)));                
            }
            admin_model::addAudit($this->db->last_query(), "admin_model", "addBezierEntries");
            return true;
        }
        
        public function addMapEntries($data)
        {
            if($data['type'] == "map")
            {
                $ad_x_offset = 280;
                $ad_y_offset = 50;
                $spot_x_offset = 235;
                $spot_y_offset = 17;
            }
            else
            {
                $ad_x_offset = 297;
                $ad_y_offset = 107;
                $spot_x_offset = 250;
                $spot_y_offset = 72;
            }
            
            foreach($data['entries'] as $key => $row)
            {
                $info = explode("_", $key);
                if(count($info) > 3)
                {
                    if($info[0] == "new")
                    {
                        $rs = $this->db->query("Select Spots from MapStates where id = ?", array($info[2]));
                        $state = $rs->row();
                        $spots = array();
                        
                        if($state->Spots)
                            $spots = json_decode($state->Spots);
                        
                        $x = ($row['x'] - $spot_x_offset) % 960;
                        $y = ($row['y'] - $spot_y_offset) % 720;
                        $point = new stdClass();
                        $point->x = $x;
                        $point->y = $y;
                        $point->day = $row['day'];
                        $spots[] = $point;
                        $this->db->where('id', $info[2]);
                        $this->db->update("MapStates", array('Spots' => json_encode($spots)));
                    }
                    else
                    {
                        if($info[1] == "ad")
                        {
                            $rs = $this->db->query("Select * from Sponsor_Campaigns where id = ?", array($info[0]));
                            $campaign = $rs->row();
                            
                            $x = ($row['x'] - $ad_x_offset) % 960;
                            $y = ($row['y'] - $ad_y_offset) % 720;

                            $rs = $this->db->query("Select * from MapStates where abbreviation = ?", array($campaign->stateID));
                            $state = $rs->row();

                            $this->db->where("id", $info[0]);
                            $this->db->update("Sponsor_Campaigns", array('stateID' => $state->abbreviation, 'xPos' => $x, 'yPos' => $y));                        
                        }
                        elseif($info[1] == "spot")
                        {
                            $day = $row['day'];
                            $rs = $this->db->query("Select * from MapStates where id = ?", array($info[0]));
                            $state = $rs->row();

                            //Find what state they are in
                            if($data['type'] == "state")
                            {
                                $panelColumn = $state->panelColumn;
                                $panelRow = $state->panelRow;
                            }
                            else
                            {
                                $panelColumn = floor(($row['x']- $spot_x_offset) / 960);
                                $panelRow = floor(($row['y'] - $spot_y_offset) / 720);
                            }
                            $x = ($row['x'] - $spot_x_offset) % 960;
                            $y = ($row['y'] - $spot_y_offset) % 720;

                            $rs = $this->db->query("Select * from MapStates where panelColumn = ? and panelRow = ?", array($panelColumn, $panelRow));
                            $states = $rs->result();

                            $is_match = false;
                            foreach($states as $state)
                                if($state->id = $info[0])
                                        $is_match = true;

                            $array = json_decode($state->Spots);
                            $index = -1;
                            foreach($array as $key => $row)
                                if($row->x == $info[2] && $row->y == $info[3])                            
                                    if($is_match)                                
                                        $index = $key;

                            if($index >= 0)
                            {
                                if($is_match) //If the information was in the array and the image hasn't changed panels
                                {
                                    $point = new stdClass();
                                    $point->x = $x;
                                    $point->y = $y;
                                    $point->day = $day;
                                    
                                    $array[$index] = $point;
                                    $this->db->where('id', $state->id);
                                    $this->db->update('MapStates', array('Spots' => json_encode($array)));
                                }
                                else
                                {
                                    unset($array[$key]);
                                    $this->db->where('id', $state->id);
                                    $this->db->update('MapStates', array('Spots' => json_encode($array)));

                                    $rs = $this->db->query("Select * from MapStates where panelColumn = ? and panelRow = ? LIMIT 1", array($panelColumn, $panelRow));
                                    $state = $rs->row();

                                    $array = json_decode($state->Spots);
                                    $point = new stdClass();
                                    $point->x = $x;
                                    $point->y = $y;
                                    $point->day = $day;
                                    $array[] = $point;

                                    $this->db->where('id', $state->id);
                                    $this->db->update('MapStates', array('Spots' => json_encode($array)));
                                }
                            }
                        }
                    }
                }
                admin_model::addAudit($this->db->last_query(), "admin_model", "addMapEntries");
            }
            
            return true;
        }
        
        public function getStates()
        {
            $rs = $this->db->query("Select * from MapStates");
            $states = $rs->result();
            return compact('states');
        }
        
        public function getState($id)
        {
            $rs = $this->db->query("Select * from MapStates where id = ?", array($id));
            $state = $rs->row();

            $spots = array();
            $i = 0;
            if($state->Spots)
            {
                $array = json_decode($state->Spots);
                foreach($array as $point)
                {
                    $point->orig_x = $point->x;
                    $point->orig_y = $point->y;                
                    $point->id = $state->id;
                    $spots[$i++] = $point;
                }
            }

            $ads = array();
            $rs = $this->db->query("Select xPos as x, yPos as y, panelColumn as xoffset, panelRow as yoffset, artAssetUrl as image, sc.id
                From Sponsor_Campaigns sc
                Inner join MapStates m on sc.stateID = m.abbreviation
                Where (sc.type = 1 or sc.type = 4) and sc.stateID = ?", array($state->abbreviation));
            $temp = $rs->result();
            foreach($temp as $row)
            {
                $row->orig_x = $row->x;
                $row->orig_y = $row->y;                
                $ads[] = $row;
            }
            
            return compact('state', 'spots', 'ads');
        }
        
        public function addState($data)
        {
            if(isset($data['id']))
            {
                $this->db->where('id', $data['id']);
                $this->db->update('MapStates', $data);
            }
            admin_model::addAudit($this->db->last_query(), "admin_model", "addState");
            return true;
        }
        
        public function getFTs()
        {
            $rs = $this->db->query("Select f.*, count(c.playerId) as cnt from FinalConfigs f
                Left Join FinalAnswers c on f.id = c.finalConfigId
                group by f.id");
            $fts = $rs->result();
            return compact('fts');
        }
        
        public function fixFinal3()
        {
            $rs = $this->db->query("Select * from FinalAnswers");
            foreach($rs->result() as $row)
            {
                $answers = json_decode($row->answerHash, true);
                $corrections = array();
                $newAnswers = array();
                $ids = array();
                foreach($answers as $answer)
                {
                    if($answer['gameId'] == 2 || $answer['gameId'] == 3)//Semi Finals
                    {
                        $newAnswers[] = $answer;
                        $teamId = $score = 0;
                        foreach($answer['scores'] as $semi)
                        {
                            $ids[$answer['gameId']]['choices'][] = $semi['teamId'];
                            if($semi['score'] > $score)
                            {    
                                $score = $semi['score'];
                                $ids[$answer['gameId']]['selected'] = $semi['teamId'];
                            }
                        }                        
                    }
                    else
                    {
                        foreach($answer['scores'] as &$final)
                            foreach($ids as $key => $id)
                                if(in_array($final['teamId'], $id['choices']))
                                    $final['teamId'] = $id['selected'];
                        
                        $newAnswers[] = $answer;
                    }
                }
                $this->db->query("Update FinalAnswers set answerHash = ? where id = ?", array(json_encode($newAnswers), $row->id));
                print_r($newAnswers);
            }
        }
        
        public function sendGenericEmail($to_address, $subject, $body, $from_address = 'noreply@kizzang.com', $attachments = array())
        {
            if(!$to_address || (!is_array($to_address) && strstr($to_address, "guest_") !== false && strstr($to_address, "kizzang.com") !== false))
                return false;
            
            $this->load->library('email');
            $config['mailtype']     = 'html';
            $config['protocol']     = 'smtp';
            $config['smtp_host']    = 'tls://email-smtp.us-east-1.amazonaws.com';
            $config['smtp_user']    = 'AKIAJNBPMBFTVPTBEWRQ';
            $config['smtp_pass']    = 'AgKt69yJlGzN186y23i+SYSfN6ihp0un7/TcShzKr5Wh';
            $config['smtp_port']    = '465';
            $config['wordwrap']     = TRUE;
            $config['newline']      = "\r\n"; 

            $this->email->initialize($config);

            $this->email->from($from_address);
            if(is_array($to_address))
                $this->email->to(implode(",", $to_address));
            else
                $this->email->to($to_address);
            $this->email->subject($subject);
            $this->email->message($body);
            if(stristr($subject, "showdown") === false)
                $this->email->bcc('barton.anderson@kizzang.com');
            
            if(stristr($subject, "Document") !== false)
                $this->email->cc('prod@kizzang.com');
            
            if(count($attachments))
                foreach($attachments as $attachment)
                    $this->email->attach($attachment);

            if($this->email->send())
                return TRUE;
            else
                return false;            
        }
                
        public function getFT($id = NULL)
        {
            $picksHash = array();
            $prizes = array();
            $games = array();
            $config = NULL;
            
            if($id)
            {
                $rs = $this->db->query("Select f.*, count(m.id) as cardCount 
                    from FinalConfigs f
                    Left join FinalAnswers m on f.id = m.finalConfigId
                    where f.id = ? group by f.id", array($id));
                $config = $rs->row();                

                if($config->pickHash)
                    $picksHash = json_decode($config->pickHash, true);                

                if($config->prizes)
                    $prizes = explode("|", $config->prizes);

                $rs = $this->db->query("Select * from SportTeams where sportCategoryID = ?", array($config->sportCategoryId));
                $teams = $rs->result();
                
                $rs = $this->db->query("Select f.*, a.name as team1Name, b.name as team2Name from FinalGames f
                    Inner join SportTeams a on a.sportCategoryID = ? and f.teamId1 = a.id 
                    Inner join SportTeams b on b.sportCategoryID = ? and f.teamId2 = b.id
                    where finalConfigId = ?", array($config->sportCategoryId, $config->sportCategoryId, $id));
                $games = $rs->result();
            }
            else
            {
                $rs = $this->db->query("Select * from SportTeams where sportCategoryID = 1");
                $teams = $rs->result();
            }
            
            $rs = $this->db->query("Select * from SportCategories");
            $categories = $rs->result();
            
            $rs = $this->db->query("Select * from GameRules where gameType = 'FT' and serialNumber = 'TEMPLATE' order by id DESC");
            $rules = $rs->result();
            
            $rule = NULL;
            if($config)
            {
                $rs = $this->db->query("Select * from GameRules where serialNumber = ?", $config->serialNumber);                
                
                if($rs->num_rows())
                {
                    $rule = $rs->row();
                    $rule->text = file_get_contents(str_replace("https://d23kds0bwk71uo.cloudfront.net", "https://kizzang-legal.s3.amazonaws.com",$rule->ruleURL));
                    if(count($rules))
                        $rule->template = file_get_contents(str_replace("https://d23kds0bwk71uo.cloudfront.net", "https://kizzang-legal.s3.amazonaws.com",$rules[0]->ruleURL));
                    else
                        $rule->template = "";
                }                
            }            
            
            $names = array('Semi1', 'Semi2', 'Final');
            
            return compact('config', 'prizes', 'teams', 'categories', 'games', 'names', 'picksHash', 'rules', 'rule');
        }
        
        public function updateFTScores($data)
        {
            $id = $data['id'];
            unset($data['id']);
            $pickHash = array();
            foreach($data as $key => $value)
            {
                $pickHash[$key]['id'] = $value['id'];
                $pickHash[$key]['val'] = $value['val'];
                $gameType = substr($key, 0, strpos($key, "_"));
                $teamId = substr($key, strpos($key, "_") + 1);
                $this->db->query("Update FinalGames set teamId$teamId = ? where finalConfigId = ? and gameType = ?", array($value['id'], $id, $gameType));
            }
            $this->db->where('id', $id);
            $this->db->update('FinalConfigs', array('pickHash' => json_encode($pickHash)));
            admin_model::addAudit($this->db->last_query(), "admin_model", "updateFTScores");
            return true;
        }
        
        public function addFTTeam($team)
        {
            $this->db->insert('FinalTeams', array('name' => $team));
            admin_model::addAudit($this->db->last_query(), "admin_model", "addFTTeam");
            $id = $this->db->insert_id();
            return "<option value='$id'>$team</option>";
        }
        
        public function addFTCategory($category)
        {
            $this->db->insert('FinalCategories', array('name' => $category));
            admin_model::addAudit($this->db->last_query(), "admin_model", "addFTCategory");
            $id = $this->db->insert_id();
            return "<option value='$id'>$category</option>";
        }
        
        public function deleteFTGame($id)
        {
            $this->db->delete("FinalMatches", array('id' => $id));
            admin_model::addAudit($this->db->last_query(), "admin_model", "deleteFTConfig");
            return true;
        }
        
        public function deleteFTConfig($id)
        {
            $this->db->delete("FinalMatchesConfig", array('id' => $id));
            admin_model::addAudit($this->db->last_query(), "admin_model", "deleteFTConfig");
            return true;
        }
        
        public function deleteFTPlace($data)
        {
            $rs = $this->db->query("Select * from FinalConfigs where id = ?", array($data['id']));
            if(!$rs->num_rows())
                return false;
            
            $row = $rs->row();
            $prizes = explode("|", $row->prizes);
            unset($prizes[$data['rank'] -1]);
            $this->db->where(array("id" => $data['id']));
            $this->db->update("FinalConfigs", array("prizes" => implode("|", $prizes)));
            admin_model::addAudit($this->db->last_query(), "admin_model", "deleteFTPlace");
            return true;
        }
        
        public function addFTGame($data)
        {
            if(isset($data['id']))
            {
                $this->db->where('id', $data['id']);
                $this->db->update('FinalGames', $data);
            }
            else
            {
                $this->db->insert("FinalGames", $data);
            }
            //print $this->db->last_query(); die();
            admin_model::addAudit($this->db->last_query(), "admin_model", "addFTGame");
            return true;
        }
        
        public function addFTPlace($data)
        {            
            if(isset($data['parlayCardId']))
            {
                $rs = $this->db->query("Select * from FinalConfigs where id = ?", array($data['parlayCardId']));
                if(!$rs->num_rows())
                    return false;
                $row = $rs->row();

                $prizes = explode("|", $row->prizes);
                
                $temp = array();
                if($data['rank'] > count($prizes))
                {
                    $prizes[] = $data['prize'];
                    $temp = $prizes;
                }
                else
                {
                    foreach($prizes as $key => $prize)
                    {
                        if($key + 1 == $data['rank'])
                            $temp[] = $data['prize'];
                        $temp[] = $prize;
                    }
                }
                
                $this->db->query("Update FinalConfigs set prizes = ? where id = ?", array(implode("|", $temp), $row->id));
                admin_model::addAudit($this->db->last_query(), "admin_model", "addFTPlace");
                return true;
            }  
            return false;
        }
        
        public function getFTGame($id)
        {            
            $rs = $this->db->query("Select * from FinalConfigs where id = ?", array($id));
            $config = $rs->row();
            
            $rs = $this->db->query("Select * from SportTeams where sportCategoryId = ? order by name", array($config->sportCategoryId));
            $teams = $rs->result();
            
            $categories = array('Semi1', 'Semi2', 'Final');
            
            return compact("categories", "teams", "id");
        }    
        
        public function addFTConfig($data)
        {
            $id = 0;
            if(isset($data['id']))
            {
                $this->db->where('id', $data['id']);
                $id = $data['id'];
                $this->db->update("FinalMatchesConfig", $data);
            }
            else
            {                
                $this->db->insert("FinalConfigs", $data);  
                $id = $this->db->insert_id();                
                
                $this->db->where("id", $id);
                $this->db->update("FinalConfigs", array('serialNumber' => sprintf("KF%05d", $id)));
            }
            admin_model::addAudit($this->db->last_query(), "admin_model", "addFTConfig");
            return $id;
        }
        
        public function getWheels()
        {
            $rs = $this->db->query("Select * from Wheels");
            return $rs->result();
        }
        
        public function getWheel($id)
        {
            $ret = array('wheel' => array(), 'wedges' => array(), 'types' => array('Basic', 'Sponsored'));
            if(!$id)
                return $ret;
            
            $rs = $this->db->query("Select * from Wheels where id = ?", array($id));
            $ret['wheel'] = $rs->row();
            
            if($ret['wheel']->wheelType == "Basic")
                $rs = $this->db->query("Select * from Wedges where wheelId = ?", array($id));
            else
                $rs = $this->db->query("Select w.*, c.artAssetUrl as image_url from Wedges w
                    Inner join Sponsor_Campaigns c on c.id = w.sponsorCampaignId 
                    where wheelId = ?", array($id));
            $temp = $rs->result();
            foreach($temp as $row)
            {
                $colors = explode(",", trim(trim($row->color, "["), "]"));
                if(count($colors) == 3)
                    $row->color = "#" . substr("000000" . dechex(65536 * $colors[0] + 256 * $colors[1] + $colors[2]), -6);
                else
                    $row->color = "#FAFAFA";
                $ret['wedges'][] = $row;
            }
            return $ret;
        }
        
        public function getWedge($wheel_id, $id)
        {
            $values = array('add' => 'add', 'delete' => 'delete', 'multiply' => 'multiply');
            $rs = $this->db->query("Select sc.id, sc.name as campaign_name, s.name from Sponsor_Campaigns sc
                Inner join Sponsors s on s.id = sc.sponsorID
                where type = 2 and Active = 1");
            $sponsors = $rs->result();
            $wedge = NULL;
            if($id)
            {
                $rs = $this->db->query("Select * from Wedges where wheelId = ? and id = ?", array($wheel_id, $id));
                if($rs->num_rows())
                {
                    $temp = $rs->row();
                    $parts = explode("][", trim(trim($temp->value, "["), "]"));
                    if(count($parts))
                    {                        
                        $temp->value = $parts[0];
                    }
                    $parts = explode(",", trim(trim($temp->color, "["), "]"));
                    if(count($parts) == 3)
                        $temp->color = "#" . substr("000000" . dechex(65536 * $parts[0] + 256 * $parts[1] + $parts[2]), -6);
                    else
                        $temp->color = "#000000";
                    
                    $wedge = $temp;
                }                
            }
            return compact("wedge", "values", "wheel_id", "sponsors");
        }
        
        public function addWheel($data)
        {
            if(isset($data['id']))
            {
                $this->db->where("id", $data['id']);
                $this->db->update("Wheels", $data);
                return $data['id'];
            }
            else
            {
                $this->db->insert("Wheels", $data);
                return $this->db->insert_id();
            }
            admin_model::addAudit($this->db->last_query(), "admin_model", "addWheel");
        }
        
        public function addWedge($data)
        {
            //Create that wierd value type
            $value = "[" . $data['ValueType'] . "][" . $data['value'] . "][]";
            
            //Create the color array
            $int_val = hexdec(trim($data['color'], "#"));
            $parts = array();
            $parts[0] = floor($int_val / 65536);
            $temp = $int_val % 65536;
            $parts[1] = floor($temp / 256);
            $parts[2] = $temp % 256;
            
            $rec = array("wheelId" => $data['wheelId'], "sponsorCampaignId" => $data['sponsorCampaignId'], "value" => $value, "displayString" => $data['value'], "color" => "[" . implode(",", $parts) . "]", "weight" => $data['weight']);
            if(isset($data['id']))
            {
                $this->db->where("id", $data['id']);
                $this->db->where("wheelId", $data['wheelId']);
                $this->db->update("Wedges", $rec);
            }
            else
            {
                $rs = $this->db->query("Select max(id) as id from Wedges where wheelId = ?", array($data['wheelId']));
                $temp = $rs->row();
                if(is_numeric($temp->id))
                    $rec['id'] = $temp->id + 1;
                else
                    $rec['id'] = 1;
                $this->db->insert("Wedges", $rec);
            }
            
            admin_model::addAudit($this->db->last_query(), "admin_model", "addWedge");
            //Update the wedge count
            $rs = $this->db->query("Select count(*) as cnt from Wedges where wheelId = ?", array($data['wheelId']));
            $row = $rs->row();
            
            $this->db->where('id', $data['wheelId']);
            $this->db->update("Wheels", array("numberOfWedges" => $row->cnt));
            return true;
        }
        
        public function updateWedges($data)
        {
            $wheelId = $data['wheelId'];
            $points = $data['points'];
            foreach($points as $point)
            {
                $this->db->where(array('id' => $point['id'], 'wheelId' => $wheelId));
                $this->db->update('Wedges', $point);                
            }
            return true;
        }
        
        public function deleteWedge($wheelId, $id)
        {
            $this->db->where(compact('wheelId','id'));
            $this->db->delete('Wedges');
            if($this->db->affected_rows())
                return true;
            return false;
        }
        
        public function getEventNotifications()
        {
            $rs = $this->db->query("Select e.*, p.screenName 
                from EventNotifications e
                Inner join Users p on p.id = e.playerId
                where pending = 1");
            return $rs->result();
        }
        
        public function addAcls($lists)
        {
            $rs = $this->db->query("Select * from Routines");
            $temp = $rs->result();
            $routines = array();
            
            foreach($temp as $row)
                $routines[] = $row->link;
            
            $recs = array();
            foreach($lists as $file)
            {
                foreach($file as $list)
                {
                    $url = $list['folder'] ? strtolower($list['folder']) . "/" : "";
                    $url .= strtolower($list['class']) . "/" . strtolower($list['function']);
                    if(in_array($url, $routines))                
                        continue;

                    $recs[] = array('name' => $list['class'] . " - " . ucwords(str_replace("_", " ", $list['function'])), 'link' => $url);                
                }
            }
            
            if(count($recs))
            {
                $this->db->insert_batch("Routines", $recs);
                //Add them to the admin group
                $this->db->query("Insert into Routine_Groups (routine_id, group_id) Select id, 1 from Routines where id not in (Select DISTINCT routine_id from Routine_Groups where group_id = 1)");
                admin_model::addAudit($this->db->last_query(), "admin_model", "addAcls");
            }
            
            return count($recs);
        }
        
        public function viewAcls()
        {
            $rs = $this->db->query("Select r.*, if(a.routine_id IS NULL, 0, 1) as Administrator, if(s.routine_id IS NULL, 0, 1) as Sports, 
                if(w.routine_id IS NULL, 0, 1) as Sweepstakes, if(c.routine_id IS NULL, 0, 1) as ScratchCards, if(l.routine_id IS NULL, 0, 1) as Slots, 
                if(p.routine_id IS NULL, 0, 1) as Sponsors, if(re.routine_id IS NULL, 0, 1) as Reports, if(pa.routine_id IS NULL, 0, 1) as Payments
                From Routines r
                Left join Routine_Groups a on r.id = a.routine_id and a.group_id = 1
                Left join Routine_Groups re on r.id = re.routine_id and re.group_id = 2
                Left join Routine_Groups s on r.id = s.routine_id and s.group_id = 3
                Left join Routine_Groups w on r.id = w.routine_id and w.group_id = 4
                Left join Routine_Groups c on r.id = c.routine_id and c.group_id = 5
                Left join Routine_Groups l on r.id = l.routine_id and l.group_id = 6
                Left join Routine_Groups pa on r.id = pa.routine_id and pa.group_id = 8
                Left join Routine_Groups p on r.id = p.routine_id and p.group_id = 7");
            $acls =  $rs->result();
            
            $rs = $this->db->query("Select * from Groups");
            $groups = array();
            foreach($rs->result() as $row)
                $groups[$row->name] = $row->id;
            
            return compact('acls', 'groups');
        }
        
        public function changeAcl($data)
        {
            if(!isset($data['id']) || !isset($data['checked']))
                return false;
            
            $ids = explode("_", $data['id']);
            if(count($ids) != 2)
                return false;
                        
            if($data['checked'] == "true")            
                $this->db->insert("Routine_Groups", array("routine_id" => $ids[0], "group_id" => $ids[1]));            
            else            
                $this->db->delete("Routine_Groups", array("routine_id" => $ids[0], "group_id" => $ids[1]));
            
            return true;
        }
        
        public function ipaddresses()
        {
            $rs = $this->db->query("Select * from player_lng_lat limit 3000 offset 12000");
            foreach($rs->result() as $row)
            {
                $rs2 = $this->db->query("Select * from geoip where ? between start_ip and end_ip", array($row->ip_address));
                if($rs2->num_rows())
                {
                    $ip = $rs2->row();
                    print sprintf("Update Users set longitude = %f, latitude = %f where id = %d and longitude = 0.0 and latitude = 0.0;\n", $ip->longitude, $ip->latitude, $row->player_id);
                }
            }
        }
        
        public function getAclGroups()
        {
            $players = array();
            $rs = $this->db->query("Select * from Users where email like '%@kizzang.com' and userType <> 'Guest'");
           
            foreach($rs->result() as $player)
                $players[$player->id] = $player;            
            
            $rs = $this->db->query("Select * from Player_Groups");
            if($rs->num_rows())            
                foreach($rs->result() as $group)
                    $players[$group->player_id]->groups[] = $group->group_id;
            
            $rs = $this->db->query("Select * from Groups");
            
            $groups = array();
            foreach($rs->result() as $group)
                $groups[$group->id] = $group->name;
                       
            return compact('players', 'groups');
        }
        
        public function addAclGroup($data)
        {
            if(!isset($data['player_id']) || !isset($data['group_id']))
                return false;
            
            $this->db->insert("Player_Groups", $data);
            admin_model::addAudit($this->db->last_query(), "admin_model", "addAclGroups");
            return true;
        }
        
        public function deleteAclGroup($player_id, $group_id)
        {
            $this->db->delete("Player_Groups", array("player_id" => $player_id, "group_id" => $group_id));
            admin_model::addAudit($this->db->last_query(), "admin_model", "deleteAclGroup");
            return true;
        }
}

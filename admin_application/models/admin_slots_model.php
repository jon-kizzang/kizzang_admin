<?php 

class admin_slots_model extends CI_Model
{        
        function __construct()
        {
            parent::__construct();
            $this->db = $this->load->database ('slots', true);
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
        
        public function getSlots()
        {
            $rs = $this->db->query("Select * from SlotGame order by Name");
            return $rs->result();
        }
                
        public function addTournaments($days_out = 8)
        {
            $types = array('Daily' => array('span' => 1, 'unit' => 'days', 'start_time' => '00:00:00', 'end_time' => '23:59:59'),
                'HalfDay' => array('span' => 1, 'unit' => 'days', 'start_time' => '00:00:00', 'end_time' => '11:59:59'),
                'Weekly' => array('span' => 1, 'unit' => 'weeks', 'start_time' => '00:00:00', 'end_time' => '23:59:59'),
                'Monthly' => array('span' => 1, 'unit' => 'month', 'start_time' => '00:00:00', 'end_time' => '23:59:59'));
            $ret = array();
            
            $this->db = $this->load->database ('slots', true); 
            $rs = $this->db->query("Select * from SlotTournament s
                Inner join (Select type, max(EndDate) as EndDate from SlotTournament group by type) a on s.EndDate = a.EndDate and s.type = a.type");

            foreach($rs->result() as $row)
            {                
                if($row->type == "HalfDay" || $row->type == "Daily") 
                    continue;
                $dates = array();
                $max_date = strtotime("+" . $days_out . " DAYS");
                $min_date = strtotime($row->EndDate) + 1;
               
                for($i = $min_date; $i <= $max_date; $i += (strtotime($types[$row->type]['span'] . " " . $types[$row->type]['unit'], strtotime($i)) - strtotime($i)))
                {
                    $dates[] = array('start_date' => date('Y-m-d ', $i) . $types[$row->type]['start_time'], 
                        'end_date' => date('Y-m-d ', strtotime($types[$row->type]['span'] . " " . $types[$row->type]['unit'], $i - 1))  . $types[$row->type]['end_time'], 'type' => $row->type);
                    
                    if($row->type == "HalfDay") //HACK to get this working                    
                        $dates[count($dates) - 1]['start_date'] = date('Y-m-d H:i:s', strtotime($dates[count($dates) - 1]['start_date']) +3600 * 24);
                    
                    if(count($dates) > 10)
                        break;
                }
                $ret['dates'][$row->type] = $dates;    
                $ret['ids'][$row->type] = $this->addTournamentDates(array('id' => $row->ID), $dates);                
            }            
            $ret['success'] = $this->validateTournaments();
            return $ret;
        }
        
        public function validateTournaments()
        {
            $rs = $this->db->query("Select * from SlotTournament where EndDate > convert_tz(now(), 'GMT', 'US/Pacific')");
            foreach($rs->result() as $row)
            {
                //Check to see if the tables exist
                $id = $row->ID;
                $st = $this->db->query("Select *  
                    from information_schema.TABLES 
                    where (TABLE_NAME like 'Log_$id' OR TABLE_NAME like 'Session_$id') AND TABLE_SCHEMA = 'kizzangslot' order by TABLE_NAME");
                if(!$st->num_rows() || $st->num_rows() < 2)
                {
                    $this->db->query(sprintf(SESSION_TABLE, $id));
                    $this->db->query(sprintf(LOG_TABLE, $id));
                }                
            }
            return true;
        }
        
        public function getStats($id)
        {
            $log_table = $session_table = NULL;
            $rs = $this->db->query("Select substring_index(TABLE_NAME, '_', 1) as id, concat(TABLE_SCHEMA, '.', TABLE_NAME) as name  from information_schema.TABLES where (TABLE_NAME like 'Log_$id' OR TABLE_NAME like 'Session_$id') order by TABLE_NAME");
            $tables = $rs->result();
            
            foreach($tables as $table)
            {
                switch($table->id)
                {
                    case "Log": $log_table = $table->name; break;
                    case "Session": $session_table = $table->name; break;
                }
            }
            
            //print_r($tables); die();
            
            if(!$log_table || !$session_table)
                return array('ranks' => array(), 'top_three' => array(), 'games' => array());
            
            $admin = $this->load->database ('admin', true);
            $rs = $this->db->query("SELECT s.SessionID, p.PlayerID, p.ScreenName, sg.Name, min(SpinsLeft) as SpinsLeft, max(WinTotal) as WinTotal 
                FROM $session_table s 
                Inner Join SlotGame sg on sg.id = s.GameID 
                Inner join Players p on p.PlayerID = s.PlayerID
                INNER JOIN $log_table l on s.SessionID = l.SessionID 
                group by SessionID order by max(WinTotal) DESC LIMIT 10");
            $ranks = $rs->result();
            
            $rs = $this->db->query("Select PlayerID, sum(WinTotal) as WinTotal, count(SessionID) as num_games from (SELECT s.SessionID, PlayerID, min(SpinsLeft) as SpinsLeft, max(WinTotal) as WinTotal FROM $session_table s inner join $log_table l on s.SessionID = l.SessionID group by SessionID) a Group by PlayerID Order by sum(WinTotal) DESC");
            $top_three = $rs->result();
            
            $rs = $this->db->query("Select sg.ID, sg.Name, min(l.WinTotal) as min_total, max(l.WinTotal) as max_total, count(DISTINCT PlayerID) as num_players, count(DISTINCT s.SessionID) as num_games
                From SlotGame sg
                Inner join $session_table s on s.GameID = sg.ID
                Inner join $log_table l on l.SessionID = s.SessionID
                Where l.SpinsLeft = 0
                Group by sg.ID");
            $games = $rs->result();
            
            $rs = $admin->query("Select id, screenName as name from Users");
            $temp = $rs->result();
            $players = array();
            
            foreach($temp as $row)
                $players[$row->id] = $row->name;
            
            foreach($ranks as &$rank)
                $rank->player_name = $players[$rank->PlayerID];
            
            foreach($top_three as &$person)
                $person->player_name = $players[$person->PlayerID];
            
            return compact('ranks', 'top_three', 'games');
        }
        
        public function getSlotTournaments($startDate, $endDate)
        {
            $rs = $this->db->query("Select ID, DATE_FORMAT(StartDate, '%a %b %d, %Y %r') as StartDate, DATE_FORMAT(EndDate, '%a %b %d, %Y %r') as EndDate, PrizeList, type as Type from SlotTournament where StartDate between ? and ? OR ? between StartDate and EndDate order by StartDate", array($startDate, $endDate, $startDate));
            $slots = array();
            foreach($rs->result() as $row)
            {
                if(preg_match_all('/"([0-9A-Za-z \$\-]+)"/', $row->PrizeList, $matches))                                    
                    $row->Prizes = $matches[1];                
                else
                    $row->Prizes = array();
                
                //Put check in to see if tables are there...
                $rs_log = $this->db->query(sprintf("Desc Log_%d", $row->ID));
                if(is_object($rs_log))
                    $row->Log = true;
                else
                    $row->Log = false;
                
                $rs_session = $this->db->query(sprintf("Desc Session_%d", $row->ID));
                if(is_object($rs_session))
                    $row->Session = true;
                else
                    $row->Session = false;
                
                $slots[] = $row;
            }
            return compact('slots', 'startDate', 'endDate');
        }
        
        public function getSlotTournament($id)
        {
            $rules = array();
            $rule = NULL;
            $prizes = array();
            $rs = $this->db->query("Select * from SlotTournament where ID = ?", array($id));
            if(!$rs->num_rows())
                return false;
            
            $tournament = $rs->row();
            $tournament->time = strtotime($tournament->EndDate) - strtotime($tournament->StartDate);
            $tournament->SerialNumber = sprintf("KS%05d", $tournament->ID);
            $tournament->games = explode(",", $tournament->GameIDs);
            
            if(preg_match_all('/"([0-9A-Za-z \$\-]+)"/', $tournament->PrizeList, $matches))
            {
                foreach($matches[1] as $key => $match)
                {
                    $prizes[$key]['Rank'] = $key + 1;
                    $prizes[$key]['Prize'] = $match;
                }
            }
            
            $db = $this->load->database ('admin', true);
            $rs = $db->query("Select distinct ruleURL from GameRules where gameType = 'Slots' and serialNumber = 'TEMPLATE'");
            $rules = $rs->result();
                    
            $rs = $db->query("Select * from GameRules where serialNumber = ?", $tournament->SerialNumber);                                

            if($rs->num_rows())
            {
                $rule = $rs->row();
                $text = file_get_contents(str_replace("https://d23kds0bwk71uo.cloudfront.net", "https://kizzang-legal.s3.amazonaws.com",$rule->ruleURL));
                $xlat_text = $text;                
                if(count($rules))
                    $rule->template = file_get_contents (str_replace("https://d23kds0bwk71uo.cloudfront.net", "https://kizzang-legal.s3.amazonaws.com",$rules[0]->ruleURL));
                else
                    $rule->template = "";
                $rule->text = $text;
            }
            
            return compact('tournament', 'prizes', 'rules', 'rule');
        }
        
        public function archiveTablePast()
        {
            $rs = $this->db->query("Select *, replace(replace(TABLE_NAME, 'Log_', ''), 'Session_', '') as id 
                from information_schema.TABLES where TABLE_SCHEMA = 'kizzangslot_archive' AND (TABLE_NAME like 'Log_%' OR TABLE_NAME like 'Session_%') 
                order by cast(id as unsigned), TABLE_NAME");
            
            if(!$rs->num_rows())
                return 0;
            
            $tables = array();
            foreach($rs->result() as $row)
            {
                if(stristr($row->TABLE_NAME, 'Log'))
                        $tables[$row->id]['Log'] = true;
                if(stristr($row->TABLE_NAME, 'Session'))
                        $tables[$row->id]['Session'] = true;
            }
            
            foreach($tables as $key => $table)
            {         
                $recs = array();
                if(isset($table['Log']) && isset($table['Session']))
                {                    
                    $rs = $this->db->query("SELECT $key as SlotTournamentId, s.SessionID as SessionId, PlayerID as PlayerId, GameID as GameId, min(SpinsLeft) as SpinsLeft, 
                        max(WinTotal) as WinTotal, DATE_FORMAT(FROM_UNIXTIME(s.StartTime / 1000), '%Y-%m-%d %H:%i:%s') as completed, (max(l.CreateTime) - min(l.CreateTime)) / 1000 as time_elapsed 
                        FROM kizzangslot_archive.Session_$key s inner join kizzangslot_archive.Log_$key l on s.SessionID = l.SessionID group by SessionID order by max(WinTotal) DESC");
                    if(!$rs->num_rows())
                        continue;
                    
                    foreach($rs->result_array() as $key => $row)
                    {
                        $row['Rank'] = $key;
                        $recs[] = $row;
                    }
                    //print_r($recs); die();
                    $this->db->insert_batch("kizzangslot_archive.SlotAggregate", $recs);
                }
            }
            return count($tables);
        }
        
        public function archiveTables()
        {
            $ret = array();
            $rs = $this->db->query("Select *, replace(replace(TABLE_NAME, 'Log_', ''), 'Session_', '') as id 
                from information_schema.TABLES where TABLE_SCHEMA = 'kizzangslot' AND (TABLE_NAME like 'Log_%' OR TABLE_NAME like 'Session_%') 
                order by cast(id as unsigned), TABLE_NAME");
            
            if(!$rs->num_rows())
                return 0;
            
            $tables = array();
            foreach($rs->result() as $row)
            {
                if(stristr($row->TABLE_NAME, 'Log'))
                        $tables[$row->id]['Log'] = true;
                if(stristr($row->TABLE_NAME, 'Session'))
                        $tables[$row->id]['Session'] = true;
            }

            foreach($tables as $key => $table)
            {
                $rs = $this->db->query("Select * from SlotTournament where ID = ? and convert_tz(now(), 'GMT', 'US/Pacific') > EndDate", array($key));
                $ret['tables'][$key] = $table;
                if($rs->num_rows())
                {
                    if(isset($table['Log']) && isset($table['Session']))
                    {
                        $recs = array();
                        $rs2 = $this->db->query("SELECT $key as SlotTournamentId, s.SessionID as SessionId, PlayerID as PlayerId, GameID as GameId, min(SpinsLeft) as SpinsLeft, 
                            max(WinTotal) as WinTotal, DATE_FORMAT(FROM_UNIXTIME(s.StartTime / 1000), '%Y-%m-%d %H:%i:%s') as completed, (max(l.CreateTime) - min(l.CreateTime)) / 1000 as time_elapsed 
                            FROM Session_$key s inner join Log_$key l on s.SessionID = l.SessionID group by SessionID order by max(WinTotal) DESC");
                        
                        if($rs2->num_rows())                            
                        {
                            $this->db->where("SlotTournamentId", $key);
                            $this->db->delete("kizzangslot_archive.SlotAggregate");
                            foreach($rs2->result_array() as $key2 => $row)
                            {
                                $row['Rank'] = $key2;
                                $recs[] = $row;
                            }                            

                            $this->db->insert_batch("kizzangslot_archive.SlotAggregate", $recs);                            
                        }
                    }
                    
                    if(isset($table['Log']))
                    {
                        $this->db->query("Create table IF NOT EXISTS kizzangslot_archive.Log_$key Select * from Log_$key");                        
                        $this->db->query("Drop table Log_$key");
                    }
                    if(isset($table['Session']))
                    {
                        $this->db->query("Create table IF NOT EXISTS kizzangslot_archive.Session_$key Select * from Session_$key");
                        $this->db->query("ALTER TABLE kizzangslot_archive.Session_$key ADD PRIMARY KEY (`SessionID`)");
                        $this->db->query("Drop table Session_$key");
                    }
                }
            }
            
            //Get Current Slot Tournaments 
            $rs = $this->db->query("Select * from SlotTournament where convert_tz(now(), 'GMT', 'US/Pacific') between StartDate and EndDate");
            if($rs->num_rows())
            {
                $slots = $rs->result();
                foreach($slots as $current)
                {                   
                    $key = $current->ID;
                    $rs2 = $this->db->query("SELECT $key as SlotTournamentId, s.SessionID as SessionId, PlayerID as PlayerId, GameID as GameId, min(SpinsLeft) as SpinsLeft, 
                                max(WinTotal) as WinTotal, DATE_FORMAT(FROM_UNIXTIME(max(l.CreateTime) / 1000), '%Y-%m-%d %H:%i:%s') as completed, (max(l.CreateTime) - min(l.CreateTime)) / 1000 as time_elapsed 
                                FROM Session_$key s inner join Log_$key l on s.SessionID = l.SessionID group by SessionID having SpinsLeft = 0 order by max(WinTotal) DESC");

                    $this->db->where("SlotTournamentId", $key);
                    $this->db->delete("kizzangslot_archive.SlotAggregate");
                    $recs = array();
                    if($rs2->num_rows())                            
                    {                    
                        foreach($rs2->result_array() as $key2 => $row)
                        {
                            $row['Rank'] = $key2;
                            $recs[] = $row;
                        }                            

                        $this->db->insert_batch("kizzangslot_archive.SlotAggregate", $recs);                        
                    }
                }
            }            
            $ret['success'] = true;
            return $ret;
        }
        
        public function calcTournamentDates($data)
        {
            $good = $bad = array();
            $interval = $data['interval'];
            $start = strtotime($data['start_date']);
            $end = strtotime($data['end_date']);
            for($i = $start; $i < $end; $i += ($interval + 1))
            {
                $start_date = date("Y-m-d H:i:s", $i);
                $end_date = date("Y-m-d H:i:s", $i + $interval);
                $rs = $this->db->query("Select * from SlotTournament where ? between StartDate and EndDate OR ? between StartDate and EndDate LIMIT 1", array($start_date, $end_date));
                if($rs->num_rows())
                    $bad[] = array('start_date' => $start_date, 'end_date' => $end_date);
                else
                    $good[] = array('start_date' => $start_date, 'end_date' => $end_date);
            }
            return compact('good', 'bad', 'data');
        }
        
        public function addTournamentDates($data, $dates)
        {
            $ids = array();
            $this->load->model("admin_model");
            $db = $this->load->database ('admin', true);            
            
            $rs = $this->db->query("Select * from SlotTournament where ID = ?", $data['id']);
            $orig = $rs->row();     
            //print_r($orig); die();
            
            $rules = $db->query("Select * from GameRules where gameType = 'Slots' and serialNumber = 'TEMPLATE' order by id DESC LIMIT 1");
            if($rules->num_rows())            
                $rule = $rules->row();            
            else
                $rule = NULL;
            
            foreach($dates as $row)         
            {
                $this->db->insert('SlotTournament', array('StartDate' => $row['start_date'], 'EndDate' => $row['end_date'], 
                    'PrizeList' => $orig->PrizeList, 'GameIDs' => $orig->GameIDs, 'type' => $orig->type, 'Title' => $orig->Title));
                admin_model::addAudit($this->db->last_query(), "admin_slots_model", "addTournamentDates");
                $id = $this->db->insert_id();
                $ids[] = $id;
                
                //Autogen Rules if template available
                if($rule)
                {                    
                    $text = "";
                    $data = array('game_type' => 'Slots', 'serial_number' => sprintf("KS%05d", $id), 'file_name' => $rule->ruleURL);
                    $this->admin_model->addRule($data, $text);
                }
                
                $session = sprintf(SESSION_TABLE, $id);                
                $log = sprintf(LOG_TABLE, $id);
                
                $this->db->query($session);
                $this->db->query($log);
            }
            
            return $ids;
        }
        
        public function addSlotTournament($data, &$errors)
        {
            //Subtract 1 sec from the end date
            $data['EndDate'] = date('Y-m-d H:i:s', strtotime($data['EndDate'])-1);
            if(!strtotime($data['StartDate']))
                $errors['StartDate'] = "Invalid Date";
            
            if(!strtotime($data['EndDate']))
                $errors['EndDate'] = "Invalid Date";
            
            if(strtotime($data['StartDate']) >= strtotime($data['EndDate']))
                $errors['DateTime'] = "Start Date needs to be before End Date";
            
            $used_ranks = array();
            foreach($data['ranks'] as $key => $rank)
            {
                if(!in_array($rank, $used_ranks))
                    $used_ranks[] = $rank;
                else
                    $errors['Ranks'] = "You have a duplicate Ranks in the Game Prizes";
                
                if(!$data['prizes'][$key])
                    $errors['Prize'] = "You have an empty / invalid prize";
            }
            
            if($errors)
                return false;
            
            //Validation passed and now to construct the record
            $rec = array();
            if(isset($data['ID']))
                $rec['ID'] = $data['ID'];
            
            $rec['StartDate'] = $data['StartDate'];
            $rec['EndDate'] = $data['EndDate'];
            $rec['type'] = $data['type'];
            $rec['Title'] = $data['Title'];
            if(isset($data['games']) && count($data['games']))
                $rec['GameIDs'] = implode (",", $data['games']);
            else
                $rec['GameIDs'] = '';
            
            $temp = array();
            foreach($data['ranks'] as $key => $rank)
                $temp[$rank] = $data['prizes'][$key];
            
            ksort($temp);
            $rec['PrizeList'] = '["' . implode('","', $temp) . '"]';
            
            if(isset($rec['ID']))
            {
                $this->db->where("ID", $rec['ID']);
                $this->db->update("SlotTournament", $rec);                
            }
            else
            {
                $this->db->insert("SlotTournament", $rec);                
                $id = $this->db->insert_id();
                
                //Get the latestset of rules and attach it to the Tournament
                $serial_number = sprintf("KS%05d", $id);
                $db = $this->load->database ('admin', true);
                $rs = $db->query("Select ruleURL from GameRules where gameType = 3 order by SerialNumber DESC LIMIT 1");
                if($rs->num_rows())
                {
                    $row = $rs->row();
                    $db->insert("GameRules", array('serialNumber' => $serial_number, 'ruleURL' => $row->ruleURL, 'gameType' => 3));
                }
                
                $session = sprintf(SESSION_TABLE, $id);                
                $log = sprintf(LOG_TABLE, $id);
                
                $this->db->query($session);
                $this->db->query($log);
            }
            
            return true;
        }
                
        public function getSlot($id)
        {            
            $slot = $prizes = NULL;
            $rs = $this->db->query("Select * from SlotGame where id = ?", array($id));
            if($rs->num_rows())            
                $slot = $rs->row();                                
                
            return compact('slot', 'rules', 'rule');
        }
        
        public function addSlot($data)
        {
            if(isset($data['ID']))
            {
                $this->db->where('ID', $data['ID']);
                unset($data['ID']);
                $this->db->update('SlotGame', $data);
            }
            else
            {
                $this->db->insert('SlotGame', $data);
            }
            admin_model::addAudit($this->db->last_query(), "admin_slots_model", "addSlot");
            return true;
        }
        
        public function getPrize($game_id, $place)
        {
            $rs = $this->db->query("Select * from SlotGame where ID = ?", array($game_id));
            if($rs->num_rows())
                $game = $rs->row();
            
            $prize = NULL;
            if($place)
            {
                $rs = $this->db->query("Select * from SlotPrizes where SlotGameId = ? and Place = ?", array($game_id, $place));
                if($rs->num_rows())
                    $prize = $rs->row();
            }
            return compact('game', 'prize');
        }
        
        public function addPrize($data)
        {
            $rs = $this->db->query("Select * from SlotPrizes where SlotGameId = ? and Place = ?", array($data['SlotGameId'], $data['Place']));
            if($rs->num_rows())
            {
                $this->db->where('SlotGameId', $data['SlotGameId']);
                $this->db->where('Place', $data['Place']);
                $this->db->update('SlotPrizes', $data);
            }
            else 
            {
                $this->db->insert('SlotPrizes', $data);
            }
            admin_model::addAudit($this->db->last_query(), "admin_slots_model", "addPrize");
            return true;
        }
        
        public function deletePrize($slot_id, $place)
        {
            $this->db->delete('SlotPrizes', array('SlotGameId' => $slot_id, 'Place' => $place));
            admin_model::addAudit($this->db->last_query(), "admin_slots_model", "deletePrize");
        }
}

<?php 

class sports_model extends CI_Model
{                                
        function __construct()
        {
            parent::__construct();
            $this->db = $this->load->database ('admin', true);
                                    
        }
                
        public function generateProFootballFile($id)
        {
            $rs = $this->db->query("Select * from SportParlayConfig where parlayCardId = ?", array($id));
            if(!$rs->num_rows())
                return false;
            
            $config = $rs->row();
            
            $rs = $this->db->query("Select * from SportParlayCards where parlayCardId = ?", array($id));
            if(!$rs->num_rows())
                return false;
            
            $questions = array();
            $rawQuestions = $rs->result();
            foreach($rawQuestions as $row)
            {
                if($row->overUnderScore)
                    $questions[$row->id] = $row;
                else
                    $questions[$row->sportScheduleId] = $row;
            }
            
            if($config->type == 'profootball2016')
                $filename = "/tmp/profootball_" . $id . ".txt";
            else
                $filename = "/tmp/collegefootball_" . $id . ".txt";
            
            $fp = fopen($filename, 'w');
            $line = implode("\t", array('id' => $id, 'startDate' => $config->cardDate . " 00:00:00", 'endDate' => $config->endDate));
            fwrite($fp, $line . "\n");
            
            foreach($rawQuestions as $index => $question)
            {
                $line = "";
                if($question->question)
                    $line = $index + 1 . "\tQ\t" . $question->question . "\t" . $question->team1Name . "\t" . $question->team2Name . "\n";
                elseif($question->overUnderScore)
                    $line = $index + 1 . "\tOU\t" . $question->team1Name . " Vs. " . $question->team2Name . "\t" . $question->overUnderScore . "\n";
                elseif($question->spread)
                    $line = $index + 1 . "\tS\t" . $question->team1Name . " Vs. " . $question->team2Name . "\t" . $question->spread . "\n";
                else
                    $line = $index + 1 . "\tW\t" . $question->team1Name . " Vs. " . $question->team2Name . "\n";
                fwrite($fp, $line);
            }
            
            $rs = $this->db->query("Select firstName, lastName, s.* from SportPlayerCards s
                Inner join Users u on u.id = s.playerId
                Where parlayCardId = ?", array($id));
            
            foreach($rs->result() as $card)
            {
                $line = $card->id . ", ";
                $picksHash = explode(":", $card->picksHash);
                $body = array();
                foreach($picksHash as $index => $pick)
                {
                    $pick = explode("|", $pick);
                    $temp = $questions[$pick[0]];
                    if($temp->overUnderScore)
                        $line .= $index + 1 . "." . ($pick[1] == $temp->team1 ? "U" : "O") . ", ";                        
                    elseif($temp->spread)
                        $line .= $index + 1 . "." . ($pick[1] == $temp->team1 ? "H" : "V") . ", ";
                    elseif($temp->question)
                        $line .= $index + 1 . "." . ($pick[1] == $temp->team1 ? $temp->team1Name : $temp->team2Name) . ", ";                        
                    else
                        $line .= $index + 1 . "." . ($pick[1] == $temp->team1 ? "H" : "V") . ", ";
                }                
                fwrite($fp, trim($line, ",") . "\r\n");
            }
            
            fclose($fp);
            
            $zip = new ZipArchive();
            $zipFileName = str_replace(".txt", ".zip", $filename);

            if($err = $zip->open($zipFileName, ZipArchive::CREATE) !== true)
                print "Error: " . $err;
            
            $zip->addFile($filename, "results.txt");
            $zip->close();
                        
            $checksum = md5_file($zipFileName);
            if($config->type == 'profootball2016')
                $body = $this->load->view("emails/proFootball", array('week' => $config->week, 'checksum' => $checksum), true);
            else
                $body = $this->load->view("emails/collegeFootball", array('week' => $config->week, 'checksum' => $checksum), true);
            
            $this->load->model('admin_model');

            $this->admin_model->sendGenericEmail(array("barton.anderson@kizzang.com"), 
                "Insured Game - Football Results Week " . $config->week, $body, "barton.anderson@kizzang.com", array($zipFileName));

        }
        
        public function createParlayCards($daysOut = 7)
        {
            $ret = array();
            $endDate = date("Y-m-d", strtotime("+$daysOut Days"));
            $configs = array('sidailyshowdown' =>
                    array('maxCards' => 20, 'type' => 'daily', 'cardWin' => 2000, 'numGames' => 16, 'gameType' => '0', 'disclaimer' => 'SIDS AutoGen'),
                'cheddadailyshowdown' =>
                    array('maxCards' => 20, 'type' => 'daily', 'cardWin' => 2000, 'numGames' => 7, 'gameType' => '0', 'disclaimer' => 'Chedda AutoGen'),
                'profootball2016' => 
                    array('maxCards' => 10, 'type' => 'weekly', 'cardWin' => 25000000, 'numGames' => 34, 'gameType' => '3', 'disclaimer' => 'Pro Football 2016 Autogen'),
                'collegefootball2016' => 
                    array('maxCards' => 10, 'type' => 'weekly', 'cardWin' => 10000000, 'numGames' => 32, 'gameType' => '8', 'disclaimer' => 'Pro College 2016 Autogen'));
            
            foreach($configs as $type => $config)
            {                
                $rs = $this->db->query("Select * from SportParlayConfig 
                    where type = ? order by endDate DESC limit 1", array($type));
                
                if($rs->num_rows())
                {
                    $last = $rs->row();
                    if($config['type'] == 'daily')
                    {
                        for($i = strtotime($last->cardDate) + 86400; $i <= strtotime($endDate); $i += 86400)
                        {
                            print_r($last); print "\n";
                            $rec = array('cardDate' => date("Y-m-d", $i), 'cardWin' => $config['cardWin'], 'maxCardCount' => $config['maxCards'], 'isActive' => 1, 'type' => $type, 'disclaimer' => $config['disclaimer']);
                            $this->db->insert('SportParlayConfig', $rec);
                            print $this->db->last_query() . "\n";
                            $id = $this->db->insert_id();
                            $ret['configs'][$id] = $rec;
                            
                            $rec = array('serialNumber' => sprintf("KP%05d", $id), 'parlayCardId' => $id);
                            $this->db->where('id', $id);
                            $this->db->update('SportParlayConfig', $rec);
                            print $this->db->last_query() . "\n";
                            
                            $query = "Select s.*, a.name as team1Name, b.name as team2Name from SportSchedule s
                                Inner join SportTeams a on a.sportCategoryID = s.sportCategoryID and a.id = s.team1
                                Inner join SportTeams b on b.sportCategoryID = s.sportCategoryID and b.id = s.team2
                                where date(dateTime) = ?";
                            if($config['gameType'] != '0')
                                $query .= " and sportCategoryID = " . $config['gameType'];
                            $query .= " order by dateTime DESC";
                            
                            $rs = $this->db->query($query, array(date("Y-m-d", $i)));
                            print $this->db->last_query() . "\n";
                            $cards = $rs->result();
                            $sequence = 1;
                            foreach($cards as $card)
                            {
                                if($sequence > $config['numGames'])
                                    break;
                                $rec = array('parlayCardId' => $id, 'sportScheduleId' => $card->id, 'sportCategoryId' => $card->sportCategoryID, 'dateTime' => $card->dateTime,
                                    'team1' => $card->team1, 'team2' => $card->team2, 'team1Name' => $card->team1Name, 'team2Name' => $card->team2Name, 'sequence' => $sequence++);
                                $this->db->insert('SportParlayCards', $rec);
                                print $this->db->last_query() . "\n";
                                
                                if($sequence > $config['numGames'])
                                    break;
                                $rec['sequence'] = $sequence++;
                                $rec['overUnderScore'] = "5.0";
                                $this->db->insert('SportParlayCards', $rec);
                                print $this->db->last_query() . "\n";
                            }
                            $this->db->where('id', $id);
                            $this->db->update('SportParlayConfig', array('endDate' => $rec['dateTime']));
                        }
                    }
                    else
                    {
                        if(strtotime($last->endDate) > strtotime($endDate))
                            continue;
                        
                        print_r($last); print "\n";
                        $rec = array('cardDate' => date("Y-m-d", strtotime($last->endDate) + 604800), 'cardWin' => $config['cardWin'], 'week' => $last->week + 1,
                            'maxCardCount' => $config['maxCards'], 'isActive' => 1, 'type' => $type, 'disclaimer' => $config['disclaimer']);
                        $this->db->insert('SportParlayConfig', $rec);
                        print $this->db->last_query() . "\n";
                        $id = $this->db->insert_id();
                        $ret['configs'][$id] = $rec;

                        $rec = array('serialNumber' => sprintf("KP%05d", $id), 'parlayCardId' => $id);
                        $this->db->where('id', $id);
                        $this->db->update('SportParlayConfig', $rec);
                        print $this->db->last_query() . "\n";

                        $query = "Select s.*, a.name as team1Name, b.name as team2Name from SportSchedule s
                            Inner join SportTeams a on a.sportCategoryID = s.sportCategoryID and a.id = s.team1
                            Inner join SportTeams b on b.sportCategoryID = s.sportCategoryID and b.id = s.team2
                            where date(dateTime) = ?";
                        if($config['gameType'] != '0')
                            $query .= " and s.sportCategoryID = " . $config['gameType'];
                        $query .= " order by dateTime DESC";

                        $rs = $this->db->query($query, array(date("Y-m-d", strtotime($last->endDate) + 604800)));
                        print $this->db->last_query() . "\n";
                        $cards = $rs->result();
                        $sequence = 1;
                        foreach($cards as $card)
                        {
                            if($sequence > $config['numGames'])
                                break;
                            $rec = array('parlayCardId' => $id, 'sportScheduleId' => $card->id, 'sportCategoryId' => $card->sportCategoryID, 'dateTime' => $card->dateTime,
                                'team1' => $card->team1, 'team2' => $card->team2, 'team1Name' => $card->team1Name, 'team2Name' => $card->team2Name, 'sequence' => $sequence++);
                            $this->db->insert('SportParlayCards', $rec);
                            print $this->db->last_query() . "\n";

                            if($sequence > $config['numGames'])
                                break;
                            $rec['sequence'] = $sequence++;
                            $rec['overUnderScore'] = "5.0";
                            $this->db->insert('SportParlayCards', $rec);
                            print $this->db->last_query() . "\n";
                        }
                        $this->db->where('id', $id);
                        $this->db->update('SportParlayConfig', array('endDate' => $rec['dateTime']));
                    }
                }
            }
            $ret['status'] = true;
            return $ret;
        }
        
        public function createROALCards($daysOut = 7)
        {
            $ret = array();
            $lastDate = strtotime("+ $daysOut days");
            $rs = $this->db->query("Select * from ROALConfigs order by cardDate DESC limit 1");
            if($rs->num_rows())
            {
                $config = $rs->row();
                for($i = strtotime($config->cardDate) + 86400; $i < $lastDate; $i += 86400)
                {
                    $ret['ROAL'][] = date('Y-m-d', $i);
                    $rec = array('cardDate' => date('Y-m-d', $i), 'theme' => 'siroal', 'disclaimer' => 'AutoGen Card');
                    $this->db->insert('ROALConfigs', $rec);
                    $id = $this->db->insert_id();
                    
                    $rs = $this->db->query("Select * from SportSchedule where date(dateTime) = ? order by rand() limit 3", array(date('Y-m-d', $i)));
                    foreach($rs->result() as $row)
                    {
                        $rec = array('ROALConfigId' => $id, 'SportScheduleId' => $row->id, 'startTime' => date("Y-m-d H:i:s", $i), 'endTime' => date("Y-m-d H:i:s", strtotime($row->dateTime) - 600));
                        $this->db->insert('ROALQuestions', $rec);
                        print $this->db->last_query() . "\n";
                    }
                }
            }
            $ret['success'] = true;
            return $ret;
        }
        
        public function sendPFEmailInsurance()
        {
            $rs = $this->db->query("Select parlayCardId from SportParlayConfig where endDate > ? and type in ('profootball2016','collegefootball2016') order by endDate ASC limit 1", array(date('Y-m-d H:i:s')));
            if(!$rs->num_rows())
                return array('success' => false);
            
            $id = $rs->row()->parlayCardId;
            $this->generateProFootballFile($id);
        }
        
        public function sendROALEmails()
        {
            $this->load->model('admin_model');
            $rs = $this->db->query("Select c.* from ROALConfigs c
                Inner join ROALQuestions q on c.id = q.ROALConfigId
                where c.id in (Select distinct ROALConfigId from ROALAnswers where isEmailed = 0) 
                order by cardDate DESC limit 1", array(date('Y-m-d')));
            if($rs->num_rows())
            {
                $config = $rs->row();
                $rs = $this->db->query("Select a.*, screenName, t.name as teamName, email, concat(a.playerId, a.ROALConfigId, a.ROALQuestionId) as id, convert_tz(a.created, 'GMT', 'US/Pacific') as created from ROALAnswers a
                    Inner join Users u on u.id = a.playerId
                    Inner join ROALQuestions q on q.id = a.ROALQuestionId
                    Inner join SportSchedule s on s.id = q.SportScheduleId
                    Inner join SportTeams t on s.sportCategoryID = t.sportCategoryID and a.winningTeam = t.id
                    where a.ROALConfigId = ? and isEmailed = 0", array($config->id));
                
                foreach($rs->result() as $row)
                {
                    $lastGraded = $this->db->query("Select currentStreak from ROALAnswers a
                        Inner join ROALQuestions q on q.id = a.ROALQuestionId and q.answer IS NOT NULL
                        Where a.playerId = ? order by q.endTime DESC limit 1", array($row->playerId));
                    $currentStreak = 0;
                    if($lastGraded->num_rows())
                        $currentStreak = $lastGraded->row()->currentStreak;
                    $data = array('cardDate' => $config->cardDate, 'screenName' => $row->screenName, 'currentStreak' => $currentStreak, 
                        'answer' => $row->teamName, 'configId' => $config->id, 'answerId' => $row->id, 'created' => $row->created);
                    $content = $this->load->view("/emails/roal", $data, true);
                    $body = $this->load->view("/emails/wrapper", array('content' => $content, 'emailCode' => md5($row->email)), true);
                    if($this->admin_model->sendGenericEmail($row->email, 'Run of a Lifetime - Kizzang', $body))
                        $this->db->query("Update ROALAnswers set isEmailed = 1 where concat(playerId, ROALConfigId, ROALQuestionId) = ?", array($row->id));
                }
            }
        }
        
        public function sendBigGameEmails()
        {
            $this->load->model('admin_model');
            $ret = array();
            $rs = $this->db->query("Select * from BGQuestionsConfig where parlayCardId in (Select distinct parlayCardId from BGPlayerCards where isEmailed = 0)");
            
            if(!$rs->num_rows())
                return array('messsage' => 'No Big Game games to be sent emails.');
            
            $answers = array();
            $config = $rs->row();
            $rs = $this->db->query("Select a.id, a.answer, q.question 
                From BGAnswers a
                Inner join BGQuestions q on a.questionId = q.id
                Where q.parlayCardId = ?", array($config->parlayCardId));
            foreach($rs->result() as $row)
                $answers[$row->id] = $row;
            
            $rs = $this->db->query("Select distinct playerId from BGPlayerCards where parlayCardId = ? and isEmailed = 0", array($config->parlayCardId));
            $ids = $rs->result();
            
            foreach($ids as $id)
            {
                $cards = array();
                $rs = $this->db->query("Select * from BGPlayerCards where parlayCardId = ? and playerId = ? and isEmailed = 0", array($config->parlayCardId, $id->playerId));
                foreach($rs->result() as $card)
                {
                    $temp = explode(":", $card->picksHash);
                    foreach($temp as $aId)
                        $card->answers[] = $answers[$aId];
                    $card->serialNumber = sprintf("KB%05d", $config->parlayCardId);
                    $cards[] = $card;
                }
                $player = $this->admin_model->getPlayer($id->playerId, true);
                if($player['emailStatus'] == 'Transaction Opt Out' || $player['emailStatus'] == 'Both Opt Out')
                    continue;
                
                $body = $this->load->view('/emails/biggame', array('cards' => $cards, 'url' => $this->admin_model->getSiteUrl(), 'emailCode' => md5($player['email'])), true);
                //print $body;
                $this->admin_model->sendGenericEmail($player['email'], "Big Game 30 - Kizzang Sweepstakes", $body);
                //$this->db->query("Update BGPlayerCards set isEmailed = 1 where playerId = ? and parlayCardId = ?", array($id->playerId, $config->parlayCardId));
            }
        }
        
        public function sendFinal3Emails()
        {
            $this->load->model('admin_model');
            $ret = array();
            $rs = $this->db->query("Select * from FinalConfigs where endDate < now() and id in (Select distinct finalConfigId from FinalAnswers where is_emailed = 0)");
            
            if(!$rs->num_rows())
                return array('message' => 'No Valid Final3 cards to process');
            
            $config = $rs->row();
            $rs = $this->db->query("Select * from SportTeams where sportCategoryId = ? order by id", array($config->sportCategoryId));
            $teams = array();
            foreach($rs->result() as $row)
                $teams[$row->id] = $row->name;
            
            $rs = $this->db->query("Select * from FinalGames where finalConfigId = ?", array($config->id));
            $games = array();
            foreach($rs->result() as $row)
                $games[$row->id] = $row;
            
            $rs = $this->db->query("Select distinct playerId from FinalAnswers where finalConfigId = ? and is_emailed = 1 and playerId = 107", array($config->id));
            $ids = $rs->result();
            
            foreach($ids as $id)
            {
                $cards = array();
                $rs = $this->db->query("Select * from FinalAnswers where finalConfigId = ? and playerId = ?", array($config->id, $id->playerId));
                foreach($rs->result() as $card)
                {
                    $answers = array();
                    $i = 0;
                    $temp = json_decode($card->answerHash, true);
                    foreach($temp as $row)
                    {
                        $answer = array();
                        $answer['title'] = $games[$row['gameId']]->description;
                        $answer['game_time']= date("l F j, Y g:i:s A", strtotime($games[$row['gameId']]->dateTime));
                        $answer['team1'] = $teams[$row['scores'][0]['teamId']];
                        $answer['team1score'] = $row['scores'][0]['score'];
                        $answer['team2'] = $teams[$row['scores'][1]['teamId']];
                        $answer['team2score'] = $row['scores'][1]['score'];
                        $answers[] = $answer;
                    }
                    $card->answers = $answers;
                    $cards[] = $card;
                }
                $player = $this->admin_model->getPlayer($id->playerId, true);
                if($player['emailStatus'] == 'Transaction Opt Out' || $player['emailStatus'] == 'Both Opt Out')
                    continue;
                $body = $this->load->view('/emails/final3', array('cards' => $cards, 'url' => $this->admin_model->getSiteUrl(), 'email_code' => $player['emailCode']), true);
                $email_body = $this->load->view('/emails/wrapper', array('content' => $body));
                //print $body;
                $this->admin_model->sendGenericEmail($player['account_email'], "Final 3 - Kizzang Sweepstakes", $body);
                //$this->db->query("Update FinalAnswers set emailed = 1 where playerId = ? and finalConfigId = ?", array($id->playerId, $config->id));
            }            
        }
        
        public function getBracketResults($id)
        {
            $rs = $this->db->query("Select * from BracketConfigs where id = ?", array($id));
            if(!$rs->num_rows())
                return json_encode (array('success' => false, 'message' => 'Invalid Config'));
            
            $config = $rs->row();
            
            $rs = $this->db->query("Select * from SportTeams where sportCategoryID = ?", array($config->sportCategoryId));
            foreach($rs->result() as $team)
                $teams[$team->id] = $team;
            
            $players = array();
            $recs = array();
            $rs = $this->db->query("Select * from BracketPlayerMatchups where bracketConfigId = ?", array($id));
            foreach($rs->result() as $entry)
            {
                if(!isset($players[$entry->playerId]))
                {
                    $rsp = $this->db->query("Select * from PlayerDups where id = ?", array($entry->playerId));
                    if(!$rsp->num_rows())
                        continue;
                    $players[$rsp->row()->id] = $rsp->row();
                }
                $rec['name'] = $players[$entry->playerId]->first_name . " " . $players[$entry->playerId]->last_name;
                $rec['email'] = $players[$entry->playerId]->email;
                $rec['date'] = $entry->created;
                $rec['entryId'] = $entry->id;
                $tmp = json_decode($entry->data, true);
                //print_r($tmp); die();
                $data = array();
                foreach($tmp as $outer)
                    foreach($outer as $index => $round)
                        foreach($round as $matchup)                    
                            $data[$index][] = $teams[$matchup['winner']]->name;
                
                foreach($data as $index => $array)
                    if($index != "round_1")
                        $rec[$index] = implode (",", $array);
                
                $recs[] = $rec;
            }
            print "Name\tEmail\tDate(GMT)\tEntry ID\tRound of 64\tRound of 32\tRound of 16\tRound of 8\tRound of 4\tRound of 2\n";
            foreach($recs as $rec)
                print implode ("\t", $rec) . "\n";
        }
        
        public function gradeBrackets($id)
        {
            $rs = $this->db->query("Select * from BracketConfigs where id = ?", array($id));
            if(!$rs->num_rows())
                return json_encode (array('success' => false, 'message' => 'Invalid Config'));
            
            $config = $rs->row();
            
            $left_bracket = $right_bracket = array();
            //Get left bracket Teams
            $rs = $this->db->query("Select a.id as team1, b.id as team2, c.id
                From BracketMatchups c
                Inner join SportTeams a on c.teamId1 = a.id and a.sportCategoryID = ?
                Inner join SportTeams b on c.teamId2 = b.id and b.sportCategoryID = ?
                Where bracketConfigId = ? and division in ('MidWest','West')
                    Order by division, sequence", array($config->sportCategoryId, $config->sportCategoryId, $config->id));

            foreach($rs->result() as $row)
            {
                $left_bracket[0][] = array($row->team1, $row->team2);
            }            

            //Get right bracket Teams
            $rs = $this->db->query("Select a.id as team1, b.id as team2, c.id
                From BracketMatchups c
                Inner join SportTeams a on c.teamId1 = a.id and a.sportCategoryID = ?
                Inner join SportTeams b on c.teamId2 = b.id and b.sportCategoryID = ?
                Where bracketConfigId = ? and division in ('South','East') 
                Order by division DESC, sequence ASC", array($config->sportCategoryId, $config->sportCategoryId, $config->id));

            foreach($rs->result() as $row)
            {
                $right_bracket[0][] = array($row->team1, $row->team2);
            }            
            
            $left_answers = json_decode($config->left_answers);
            $right_answers = json_decode($config->right_answers);
            
            while(count($left_answers) == 1)
                $left_answers = $left_answers[0];
            while(count($right_answers) == 1)
                $right_answers = $right_answers[0];
            
            //print_r($left_bracket); print_r($left_answers);
            $answers = array();
            $keys = array('round_64','round_32','round_16','round_8','round_4');
            //print_r($left_bracket);
            foreach($left_answers as $i => $group)
            {                
                foreach($group as $index => $array)
                {                    
                    if(is_numeric($array[0]) && is_numeric($array[1]))
                    {
                        if($array[0] > $array[1])
                            $answer = $left_bracket[$i][$index][0];
                        else
                            $answer = $left_bracket[$i][$index][1];
                        
                        $answers[$keys[$i]][$index] = $answer;
                        $left_bracket[$i + 1][$index / 2][] = $answer;
                    }
                }
                $last_index = $index + 1;
                foreach($right_answers[$i] as $index => $array)
                {                    
                    if(is_numeric($array[0]) && is_numeric($array[1]))
                    {
                        if($array[0] > $array[1])
                            $answer = $right_bracket[$i][$index][0];
                        else
                            $answer = $right_bracket[$i][$index][1];
                        
                        $answers[$keys[$i]][$index + $last_index] = $answer;
                        $right_bracket[$i + 1][$index / 2][] = $answer;
                    }
                }
            }
            
            if(isset($answers['round_4']))
            {                    
                if(isset($answers['round_4'][2]))
                {
                    $answers['round_4'][1] = $answers['round_4'][2];
                    unset($answers['round_4'][2]);
                }
            }
            
            if(is_numeric($config->champion_id))
            {
                $rs = $this->db->query("Select * from SportTeams where id = ? and sportCategoryId = ?", array($config->champion_id, $config->sportCategoryId));
                if($rs->num_rows())                                    
                    $answers['round_2'][0] = $rs->row()->name;                
            }
            
            $rs = $this->db->query("Select * from BracketPlayerMatchups where bracketConfigId = ?", array($id));
            foreach($rs->result() as $row)
            {
                $wins = $losses = 0;
                $data = array();
                $temp = json_decode($row->data, true);
                foreach($temp as $round)
                    foreach($round as $key => $value)
                        $data[$key] = $value;
                foreach($answers as $round => $set)
                {
                    foreach($set as $index => $value)
                    {
                        if($value == $data[$round][$index]['winner'])
                            $wins++;
                        else
                            $losses++;
                    }
                }
                $this->db->where("id", $row->id);
                $this->db->update('BracketPlayerMatchups', array('wins' => $wins, 'losses' => $losses));                
            }
            return true;
        }
        
        //Final 3 Stuff
        public function getFTWinners($id)
        {
            $places = array();
            $rs = $this->db->query("Select * from FinalConfigs where id = ?", array($id));
            $config = $rs->row();
            $prizes = explode("|", $config->prizes);
            foreach($prizes as $prize)
                $places[] = str_replace('$', "", str_replace(",", "", $prize));
            
            $answers = array();
            $temp = json_decode($config->pickHash, true);
            
            foreach($temp as $key => $value)           
                    $answers[substr($key, 0, strpos ($key, '_'))][] = $value;
                
            foreach($answers as $key => &$answer)
            {
                $score = 0;
                $win_array = null;
                foreach($answer as $row)
                {
                    if($row['val'] && $score < $row['val'])
                    {
                        $win_array = $row;
                        $score = $row['val'];
                    }
                }
                
                if($win_array)
                    $answer[3] = $win_array;
            }                        
            
            $game_hash = $game_array = array();
            $rs = $this->db->query("Select * from FinalGames where finalConfigId = ?", array($id));
            foreach($rs->result() as $row)
            {
                $game_array[$row->id] = $row;
                $game_hash[$row->gameType] = $row;
            }
                     
            $rs = $this->db->query("Select * from FinalAnswers where finalConfigId = ? order by wins DESC", array($id));
            if($rs->num_rows())
            {                
                $cards = $rs->result();                                                
                foreach($cards as $row)
                {                    
                    $delta = $wins = $losses = 0;
                    $playerHash = json_decode($row->answerHash, true);      
                   
                    foreach($playerHash as $matchup)
                    {                        
                        $game = $game_array[$matchup['gameId']];
                        if(!isset($answers[$game->gameType][3]))                                                    
                            continue;
                        
                        $winning_id = $score = 0;
                        foreach($matchup['scores'] as $pick)
                        {
                            if($pick['score'] > $score)
                            {
                                $winning_id = $pick['teamId'];
                                $score = $pick['score'];
                            }
                        }
                        
                        $isWin = true;
                        print $winning_id  . " = " . $answers[$game->gameType][3]['id'] . "\n";
                        if($winning_id == $answers[$game->gameType][3]['id'])
                        {
                            $wins++;
                        }
                        else
                        {
                            $losses++;
                            $isWin = false;
                        }
                        
                        foreach($answers[$game->gameType] as $key => $answer1)
                        {                            
                            if($key == 3)
                                break;
                            foreach($matchup['scores'] as $pick)
                            {
                                if($answer1['id'] == $pick['teamId'])
                                {
                                    if($answer1['val'] < $pick['score'] && $isWin)
                                    {
                                        $wins--;
                                        $losses++;
                                    }                                    
                                    $delta += $answer1['val'] - $pick['score'];
                                    break;
                                }
                            }                                        
                        }
                    }
                    $this->db->where(array("id" => $row->id));
                    $rec = compact('wins','losses','delta');
                    $this->db->update('FinalAnswers', $rec);      
                    print $this->db->last_query();
                }                
            }  
            return true;
        }
        
        function printFTWinners($id)
        {
            $lines = array();
            $this->load->model("admin_model");
            $rs = $this->db->query("Select * from FinalAnswers order by wins DESC, delta ASC limit 100");
            $winners = $rs->result();
            
            $rs = $this->db->query("Select * from SportTeams where sportCategoryId = 3 and id in (13,11,31,24)");
            $temp = $rs->result();
            $teams = array();
            foreach($temp as $row)
                $teams[$row->id] = $row->name;
            
            foreach($winners as $winner)
            {
                $player = $this->admin_model->getPlayer($winner->playerId, true);
                $line = $player['first_name'] . " " . $player['last_name'] . "\t" . $winner->wins . "\t" . $winner->losses . "\t";
                $games = json_decode($winner->answerHash, true);
                
                foreach($games as $game)
                {
                    foreach($game['scores'] as $index => $score)
                    {
                        if(!$index)
                            $line .= $teams[$score['teamId']] . " - " . $score['score'] . " vs ";
                        else
                            $line .= $teams[$score['teamId']] . " - " . $score['score'] . "\t";
                    }
                }
                $line .= $winner->delta;
                $lines[] = explode("\t", $line);
            }
            return $lines;
        }
        
        public function getDisclaimer($type)
        {
            $rs = $this->db->query("Select disclaimer from SportParlayConfig where type = ? and disclaimer <> '' and disclaimer IS NOT NULL order by id DESC limit 1", array($type));
            if($rs->num_rows())
                return $rs->row()->disclaimer;
            return "";
        }
        
        //All the Bracket stuff
        public function getBrackets()
        {
            $rs = $this->db->query("Select * from BracketConfigs order by created DESC");
            return $rs->result();
        }
        
        public function addBracketDate($id)
        {
            $rs = $this->db->query("Select max(id) as id from BracketTimes where bracketConfigId = ?", array($id));
            $bid = $rs->row()->id;
            
            if(!$bid)
                $bid = 1;
            else
                $bid++;
            
            return $this->load->view("admin/sports/bracketDate", array('id' => $bid));
        }
        
        public function updateBracketAnswers($id, $side, $data)
        {
            $this->db->where('id', $id);
            if($side == 'left')
                $this->db->update('BracketConfigs', array('left_answers' => json_encode ($data['results'])));
            else
                $this->db->update('BracketConfigs', array('right_answers' => json_encode ($data['results'])));
            return true;
        }
        
        public function updateBracketDates($data)
        {
            if($data['isNew'])
            {
                unset($data['isNew']);
                unset($data['id']);
                $this->db->insert('BracketTimes', $data);
            }
            else
            {
                unset($data['isNew']);
                $this->db->where('id', $data['id']);
                $this->db->update('BracketTimes', $data);
            }
            return true;
        }
        
        public function getSportsTeams($sportCatId)
        {
            $game_types = array();
            if($sportCatId)
                $rs = $this->db->query("Select * from SportTeams where sportCategoryID = ?", array($sportCatId));
            else
                $rs = $this->db->query("Select * from SportTeams");
            $sports = $rs->result();
            
            $rs = $this->db->query("Select * from SportCategories");
            foreach($rs->result() as $row)
                $game_types[$row->id] = $row;
            
            return compact('sports','game_types');
        }
        
        public function getSportsTeam($sportCategoryId, $id)
        {
            $rs = $this->db->query("Select * from SportTeams where sportCategoryID = ? and id = ?", array($sportCategoryId, $id));
            $team = $rs->row();
            return compact('team');
        }
        
        public function updateSportsTeam($data)
        {
            $this->db->where(array('id' => $data['id'], 'sportCategoryID' => $data['sportCategoryID']));
            $this->db->update('SportTeams', $data);
            return true;
        }
        
        public function updateBracketCampion($data)
        {
            if(!isset($data['id']) || !isset($data['champion_id']))
                return false;
            
            $this->db->query("Update BracketConfigs set champion_id = ? where id = ?", array($data['champion_id'], $data['id']));
            return true;
        }
        
        public function getBracket($id)
        {
            $config = NULL;
            $count = 0;
            $dates = array();
            $left_bracket = $right_bracket = $left_winner = $right_winner = "";
            if($id)
            {
                $rs = $this->db->query("Select * from BracketConfigs where id = ?", array($id));
                $config = $rs->row();
                
                $rs = $this->db->query("Select * from BracketTimes where bracketConfigId = ?", array($id));
                $dates = $rs->result();
                
                $rs = $this->db->query("Select count(*) as cnt from BracketPlayerMatchups where bracketConfigId = ?", array($id));
                $count = $rs->row()->cnt;
                
                $config->serialNumber = sprintf("KB%05d", $config->id);
                
                //Get left bracket Teams
                $rs = $this->db->query("Select a.name as team1, b.name as team2, c.id
                    From BracketMatchups c
                    Inner join SportTeams a on c.teamId1 = a.id and a.sportCategoryID = ?
                    Inner join SportTeams b on c.teamId2 = b.id and b.sportCategoryID = ?
                    Where bracketConfigId = ? and division in ('MidWest','West')
                    Order by division, sequence", array($config->sportCategoryId, $config->sportCategoryId, $config->id));

                foreach($rs->result() as $row)
                {
                    $left_bracket .= sprintf("[\"%s\",\"%s\"],", $row->team1, $row->team2);
                    $lb[0][] = array($row->team1, $row->team2);
                }
                $left_bracket = trim($left_bracket, ",");
                
                //Get right bracket Teams
                $rs = $this->db->query("Select a.name as team1, b.name as team2, c.id
                    From BracketMatchups c
                    Inner join SportTeams a on c.teamId1 = a.id and a.sportCategoryID = ?
                    Inner join SportTeams b on c.teamId2 = b.id and b.sportCategoryID = ?
                    Where bracketConfigId = ? and division in ('South','East')
                    Order by division DESC, sequence ASC", array($config->sportCategoryId, $config->sportCategoryId, $config->id));

                foreach($rs->result() as $row)
                {
                    $right_bracket .= sprintf("[\"%s\",\"%s\"],", $row->team1, $row->team2);
                    $rb[0][] = array($row->team1, $row->team2);
                }
                $right_bracket = trim($right_bracket, ",");
                
                $left_answers = json_decode($config->left_answers);
                $right_answers = json_decode($config->right_answers);
                
                while(count($left_answers) == 1)
                    $left_answers = $left_answers[0];
                while(count($right_answers) == 1)
                    $right_answers = $right_answers[0];

                //print_r($left_bracket); print_r($left_answers);
                $answers = array();
                $keys = array('round_64','round_32','round_16','round_8','round_4','round_2');
                //print_r($left_answers); die();
                if(is_array($left_answers) && is_array($right_answers))
                {
                    foreach($left_answers as $i => $group)
                    {                
                        foreach($group as $index => $array)
                        {                    
                            if(is_numeric($array[0]) && is_numeric($array[1]))
                            {
                                if($array[0] > $array[1])
                                    $answer = $lb[$i][$index][0];
                                else
                                    $answer = $lb[$i][$index][1];

                                $answers[$keys[$i]][$index] = $answer;
                                $lb[$i + 1][$index / 2][] = $answer;
                            }
                        }
                        $last_index = $index + 1;
                        foreach($right_answers[$i] as $index => $array)
                        {                    
                            if(is_numeric($array[0]) && is_numeric($array[1]))
                            {
                                if($array[0] > $array[1])
                                    $answer = $rb[$i][$index][0];
                                else
                                    $answer = $rb[$i][$index][1];

                                $answers[$keys[$i]][$index + $last_index] = $answer;
                                $rb[$i + 1][$index / 2][] = $answer;
                            }
                        }
                    }
                }
                if(isset($answers['round_4']))
                {                    
                    $rs = $this->db->query("Select * from SportTeams where name in (?,?) and sportCategoryID = ?", 
                            array(isset($answers['round_4'][0]) ? $answers['round_4'][0] : '', isset($answers['round_4'][2]) ? $answers['round_4'][2] : '', $config->sportCategoryId));
                    
                    foreach($rs->result() as $tmp)
                    {
                        if(isset($answers['round_4'][0]) && trim($tmp->name) == trim($answers['round_4'][0]))
                            $left_winner = $tmp;
                        if(isset($answers['round_4'][2]) && trim($tmp->name) == trim($answers['round_4'][2]))
                            $right_winner = $tmp;
                    }
                }
            }
            
            $rs = $this->db->query("Select * from SportCategories");
            $categories = $rs->result();
            
            $rs = $this->db->query("Select * from GameRules where gameType = 'Bracket' and serialNumber = 'TEMPLATE' order by id DESC");
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
            
            $divisions = array('MidWest','West','East','South');
            
            return compact('config','count','categories','rule','rules','divisions','left_bracket','right_bracket','left_winner','right_winner','dates');
        }
        
        public function addBracketConfig($data)
        {
            if(isset($data['id']))
            {
                $this->db->where(array("id" => $data['id']));
                $this->db->update("BracketConfigs", $data);
            }
            else
            {
                $this->db->insert("BracketConfigs", $data);
            }
            
            admin_model::addAudit($this->db->last_query(), "sports_model", "addBracketConfig");
            
            if($this->db->affected_rows())
                return true;
            return false;
        }
        
        public function deleteBracket($id)
        {
            $rs = $this->db->query("Select * from BracketMatchups where id = ?", array($id));
            if(!$rs->num_rows)
                return false;
            
            $rec = $rs->row();
            $this->db->query("Delete from BracketMatchups where id = ?", array($id));
            $this->db->query("Update BracketMatchups set sequence = sequence - 1 where sequence > ? and division = ?", array($rec->sequence, $rec->division));
            return true;
        }
        
        public function addBracket($data)
        {
            if($data['id'])
            {
                $this->db->where('id', $data['id']);
                $this->db->update('BracketMatchups', $data);
            }
            else
            {
                unset($data['id']);
                $rs = $this->db->query("Select max(sequence) as sequence from BracketMatchups where division = ? and bracketConfigId = ?", array($data['division'], $data['bracketConfigId']));
                $sequence = $rs->row()->sequence;
                if(!$sequence)
                    $sequence = 1;
                else
                    $sequence++;
                $data['sequence'] = $sequence;
                $this->db->insert('BracketMatchups', $data);
            }
            return true;
        }
        
        public function getBracketTeamInfo($bracketConfigId, $division = NULL)
        {
            $rs = $this->db->query("Select * from BracketConfigs where id = ?", array($bracketConfigId));
            if(!$rs->num_rows())
                return "";
            
            $config = $rs->row();
            $rs = $this->db->query("Select * from SportTeams where sportCategoryId = ? order by name", array($config->sportCategoryId));
            $teams = $rs->result();
            
            $ret = "";
            
            $rs = $this->db->query("Select * from BracketMatchups where bracketConfigId = ? and division = ? order by sequence", array($config->id, $division));
            if($rs->num_rows())
            {
                foreach($rs->result() as $matchup)
                {
                    $data = array('rec' => $matchup, 'teams' => $teams, 'isNew' => false, 'id' => $matchup->id);
                    $ret .= $this->load->view("admin/sports/bracket_matchup", $data, true);
                }
            }
            else
            {
                $rs = $this->db->query("Select max(id) as id from BracketMatchups");
                $id = $rs->row()->id;
                if(!$id)
                    $id = 1;
                else
                    $id++;
                
                $data = array('rec' => NULL, 'teams' => $teams, 'isNew' => true, 'id' => $id);
                $ret = $this->load->view("admin/sports/bracket_matchup", $data, true);
            }
            return $ret;
        }
}

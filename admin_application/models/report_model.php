<?php 

class report_model extends CI_Model
{
        private $base_config = array();
        
        function __construct()
        {
            parent::__construct();
            $this->db = $this->load->database ('admin', true);
        }
        
        public function getMainGraphs($startDate, $endDate)
        {
            $this->db->query("Set time_zone = 'America/Los_Angeles'");
            $rs = $this->db->query("Select date(lastLogin) as date, count(distinct playerId) as total,
                count(DISTINCT case when loginType = 'Normal' and loginSource = 'Web' then playerId else NULL end) as web_total,
                count(DISTINCT case when loginType = 'Facebook' and loginSource = 'Web' then playerId else NULL end) as facebook_total,
                count(DISTINCT case when loginType = 'Normal' and loginSource = 'Mobile' then playerId else NULL end) as mobile_total,
                count(DISTINCT case when loginType = 'Facebook' and loginSource = 'Mobile' then playerId else NULL end) as fb_mobile_total
                from PlayerLogins where date(lastLogin) between ? and ? group by date(lastLogin)", array($startDate, $endDate));
            $player_logins = $rs->result();
            
            $rs = $this->db->query("Select date(started) as date, sum(case when game_type in ('Sweepstakes','Slots','Parlay','Scratchers') then 1 else 0 end) as total, sum(case when game_type = 'Sweepstakes' then 1 else 0 end) as sweepstakes,
                sum(case when game_type = 'Slots' then 1 else 0 end) as slots, sum(case when game_type = 'Scratchers' then 1 else 0 end) as scratchers,
                sum(case when game_type = 'Parlay' then 1 else 0 end) as parlay
                From reports.PlayerEvents where date(started) between ? and ? group by date(started)", array($startDate, $endDate));
            $winner_payments = $rs->result();
            
            return compact("player_logins", "startDate", "endDate", "winner_payments");
        }
        
        public function sendNotificationEmails()
        {
            $this->load->model('admin_model');
            $times = array(12, 24);
            foreach($times as $time)
            {
                $rs = $this->db->query("Select w.*, email, sum(a.amount) as total from Winners w
                    Inner join Users u on w.player_id = u.id
                    Inner join Winners a on a.player_id = w.player_id and YEAR(a.created) = YEAR(now())
                    where date_sub(w.expirationDate, INTERVAL + $time HOUR) between convert_tz(now(), 'GMT', 'US/Pacific') and convert_tz(now(), 'GMT', 'US/Pacific') + INTERVAL 1 HOUR
                    and (w.comments IS NULL or w.comments not like '%$time%') and w.status in ('Document','New')
                    group by w.id");
                
                //print $this->db->last_query() . "\n";
                if($rs->num_rows())
                {
                    foreach($rs->result() as $row)
                    {     
                        $guid = $this->db->query("Select * from rightSignature.signins where playerId = ? and status = 'Pending' and expirationDate > convert_tz(now(), 'GMT', 'US/Pacific') order by created DESC limit 1", array($row->player_id));
                        if(!$guid->num_rows())

                            $signinGuid = "";
                        else
                            $signinGuid = $guid->row()->id;
                        $content = $this->load->view('/emails/12-24_notice', 
                            array('time' => $time, 'signinGuid' => $signinGuid, 'total' => $row->total, 'expirationDate' => $row->expirationDate, 'prize' => $row->prize_name, 'game' => $row->game_name, 'serialNumber' => $row->serial_number, 'entryId' => $row->id), true);
                        $body = $this->load->view('/emails/wrapper', array('content' => $content, 'emailCode' => $row->email), true);
                        //print $body; die();
                        if($this->admin_model->sendGenericEmail($row->email, "Documents Needed - $time hours left!", $body))
                        {
                            $row->comments .= $row->comments ? $row->comments . "\n$time Notice Emailed" : "$time Notice Emailed";
                            $this->db->query("Update Winners set comments = ? where id = ?", array($row->comments, $row->id));
                        }
                    }
                }
            }
        }
        
        public function playerStats($id, $startDate, $endDate)
        {
            for($i = strtotime($startDate); $i <= strtotime($endDate); $i += 86400)
            {
                $rs = $this->db->query("Select * from reports.PlayerEvents where game_type not in ('Login','Tickets') and player_id = ? and date(started) = ?", array($id, date("Y-m-d", $i)));
                $counts = array();
                $runs = array();
                $run = 0;
                $prevGame = "";
                $prevDate = "";
                $isFirst = true;
                $date = date("Y-m-d", $i);
                $games = $rs->result();
                foreach($games as $index => $game)
                {
                    if(!$index)
                    {
                        $prevGame = $game->game_type;
                        $prevDate = $game->started;
                        continue;
                    }
                    
                    if($game->game_type != $prevGame)
                    {
                        $prevGame = $game->game_type;
                        $isFirst = true;
                        $runs[] = $run;
                        $run = 0;
                        continue;
                    }
                    
                    if($isFirst)
                    {
                        //This is the first of a section
                        //$recs[] = array('startDate' => '', 'endDate' => $game->started, 'secs' => 0, 'type' => $game->game_type);
                        $prevDate = $game->started;
                        $isFirst = FALSE;
                    }
                    else
                    {
                        $recs[] = array('startDate' => $prevDate, 'endDate' => $game->started, 'secs' => strtotime($game->started) - strtotime($prevDate), 'type' => $game->game_type);
                        $prevDate = $game->started;
                    }
                    $run++;
                    if(isset($counts[$game->game_type]))
                        $counts[$game->game_type] += 1;
                    else
                        $counts[$game->game_type] = 1;
                }
                
                $this->db->query("Create table reports.tmpPlayer" . $id . " (startDate datetime, endDate datetime, secs int, type varchar(20));");
                $this->db->insert_batch('reports.tmpPlayer' . $id, $recs);
                
                if($run)
                    $runs[] = $run;
                
                print_r(compact('recs','counts','runs')); die();
            }
        }
        
        public function manageChedda()
        {
            $rs = $this->db->query("Select playerId from (Select playerId, max(lastLogin) as lastLogin from PlayerLogins group by playerId) a where a.lastLogin < now() - INTERVAL 3 MONTH");
            $ids = array();
            foreach($rs->result() as $row)
                $ids[] = $row->playerId;
            
            if($ids)
            {
                $ids = implode(",", $ids);
                $this->db->query("Delete from Chedda where playerId in ($ids) and isUsed = 0");
            }
            $ret['ids'] = $ids;
            $ret['success'] = true;
            return $ret;
        }
        
        public function importPlayers()
        {
            $this->load->model("admin_model");
            $genders = array();
            
            $rs = $this->db->query("Select * from Genders");
            foreach($rs->result() as $row)
                $genders[$row->id] = $row->name;
            
            $rs = $this->db->query("Select id from Players where id not in (Select id from Users) limit 5000");
            if(!$rs->num_rows())
                return false;
            
            $players = array();
            foreach($rs->result() as $row)
            {
                $player = $this->admin_model->getPlayerTable($row->id, true);
                $user = array("id" => $player['id'],
                    "firstName" => $player['first_name'],
                    "lastName" => $player['last_name'],
                    "screenName" => $player['screen_name'],
                    "dob" => date("Y-m-d", strtotime($player['dob'])),
                    "accountName" => $player['account_email'] ? $player['account_email'] : $player['accountPhone'],
                    "payPalEmail" => $player['paypal_email'],
                    "accountCode" => $player['account_email'] ? $player['emailCode'] : $player['phoneCode'],
                    "passwordHash" => $player['passwordHash'],
                    "fbid" => $player["fbid"] ? $player["fbid"] : NULL,
                    "email" => $player['email'],
                    "phone" => $player['cellphone'],
                    "address" => trim($player["address"] . " " . $player['address2']),
                    "city" => $player['city'],
                    "state" => $player['state'],
                    "zip" => $player['zip'],
                    "lastApprovedTOS" => $player['lastApprovedTOS'],
                    "lastApprovedPrivacyPolicy" => $player['lastApprovedPrivacyPolicy'],
                    "referralCode" => $player['referralId'],
                    "gender" => $genders[$player['gender']],
                    "accountStatus" => $player['isDeleted'] ? "Deleted" : ($player['isSuspended'] ? "Suspended" : "Active"),
                    "newUserFlow" => $player['newUserFlow'],
                    "profileComplete" => $player['profileComplete'],
                    "status" => $player['status'],
                    "created" => $player['accountCreated']
                    );
               $players[] = $user;
               //print_r($user); die();
               if(count($players) && count($players) % 500 == 0)
               {
                   $this->db->insert_batch("Users", $players);
                   $players = array();
               }
                
                //print_r(compact("user", "address")); die();
            }
            if(count($players))
               {
                   $this->db->insert_batch("Users", $players);
                   $players = array();
               }
        }
        
        public function getMapPoints()
        {
            $rs = $this->db->query("Select longitude, latitude, screenName from Users where longitude <> 0.0 and latitude <> 0.0");
            return $rs->result();
        }
        
        public function lastWeekWinners()
        {
            $rs = $this->db->query("Select player_id, sum(amount) as amount, p.* 
                            from Winners w
                            Inner join Users p on w.player_id = p.id
                            where week(w.created) = if(week(now()) = 1, 52, week(now()) - 1) and year(w.created) = if(week(now()) = 1, year(now()) - 1, year(now())) 
                            group by player_id 
                            order by sum(amount) DESC limit 10");
            $winners = $rs->result();            
            return $winners;
        }
        
        public function updateDups()
        {
            $ret = array('count' => 0);
            //$this->db->query("Delete from PlayerDups where created < now() - INTERVAL 48 HOUR");
            $rs = $this->db->query("Select id from Users where id not in (Select distinct id from PlayerDups) limit 10000");
            $recs = array();
            if($rs->num_rows())
            {                
                $this->load->model("admin_model");
                $ret['count'] = $rs->num_rows();
                foreach($rs->result() as $id)
                {
                    $player = $this->admin_model->getPlayer($id->id, true);
                    $rec = array('id' => $id->id,
                        'first_name' => $player['first_name'],
                        'last_name' => $player['last_name'],
                        'email' => $player['email'],
                        'paypal_email' => $player['paypal_email'],
                        'dob' => date("Y-m-d", strtotime($player['dob'])),
                        'city' => $player['city'],
                        'state' => $player['state'],
                        'zipcode' => $player['zip'],
                        'phone' => $player['phone'],
                        'is_deleted' => $player['isDeleted'],
                        'is_suspended' => $player['isSuspended'],
                        'ip_address' => 0);
                    $rs2 = $this->db->query("Select INET_ATON(ipAddress) as ipaddress from PlayerLogins where playerId = ? and ipAddress <> 'None' order by created DESC limit 1", array($id->id));
                    if($rs2->num_rows())
                        $rec['ip_address'] = $rs2->row()->ipaddress;
                    
                    $recs[] = $rec;
                    if(count($recs) && count($recs) % 100 == 0)
                    {
                        $this->db->insert_batch('PlayerDups', $recs);
                        $recs = array();
                    }
                }
            }
            
            if(count($recs))
                $this->db->insert_batch('PlayerDups', $recs);
            //print_r($recs); 
            $ret['success'] = true;
            return $ret;
        }
        
        public function findDups($data)
        {
            $where = $group_by = "";
            $duplicates = array();
            if(isset($data['duplicate']))
            {
                $duplicates = $data['duplicate'];
                unset($data['duplicate']);
            }
            
            foreach($data as $key => $value)
            {
                if(!$value)
                    continue;
                
                if(!$where)
                    $where = " WHERE 1=1 ";
                switch($key)
                {
                    case 'ip_address' : $where .= " AND ip_address = " . ip2long($value); break;
                    case 'dob' : $where .= " AND dob = '" . date("Y-m-d", strtotime($value)) . "'";
                    default: $where .= " AND $key like '%" . str_replace("'", "''", $value) . "%'";
                }
            }
            
            $recs = array();
            if(count($duplicates))
            {
                $group_by = " GROUP BY " . implode(",", $duplicates);
                if(!$where)
                    $where = " HAVING count(*) > 1";
                else
                    $where = str_replace("WHERE", "HAVING", $where .= " AND count(*) > 1");
                $query = "Select firstName, lastName, dob, userType, accountName, payPalEmail, email, phone, address, city, state, zip, accountStatus, count(*) as cnt, group_concat(id) as ids from Users $group_by $where";
                $rs = $this->db->query($query);
                if($rs->num_rows())
                {
                    foreach($rs->result() as $row)
                    {
                        $rs2 = $this->db->query("Select firstName, lastName, dob, userType, email, phone, address, city, state, zip, accountStatus from Users where id in (" . $row->ids . ") order by id");
                        $recs[] = $rs2->result();
                    }
                }
            }
            else
            {
                $query = "Select firstName, lastName, dob, userType, email, phone, address, city, state, zip, accountStatus from Users $where";
                $rs = $this->db->query($query);
                $recs[] = $rs->result();
            }
                        
            return compact('duplicates', 'recs');
        }
        
        public function get365names()
        {
            $rs = $this->db->query("Select * from PlayerDups p
                Where id not in (Select playerId from Positions 
                where id not in (Select id from (Select playerId, max(id) as id from Positions group by playerId) a) and endPosition + 220 > 365) 
                and is_suspended = 0 and is_deleted = 0");
            
            foreach($rs->result() as $index => $row)
            {
                print $row->first_name . "\t" . $row->last_name . "\t\t\t\t" . $row->email . "\n";
            }
           
            die();
        }

        public function getSlotGraphs($startDate, $endDate)
        {
            $db = $this->load->database ('slots', true);
            $rs = $db->query("Select * from SlotGame where ID in (Select distinct GameID FROM kizzangslot_archive.SlotAggregate
                Where date(completed) between ? and ?)", array($startDate, $endDate));

            $games = $rs->result();
            $game_names = array();
            $game_themes = array();
            $template = array();
            foreach($games as $key => $row)
            {
                $game_names[$row->ID] = str_replace("'", "", $row->Name);
                $game_themes[$row->ID] = $row->Theme;   
                $template[$row->Theme] = 0;
            }
            
            $rs = $db->query("SELECT GameID, cast(WinTotal / 1000000 as unsigned) as num_millions, count(*) as cnt FROM kizzangslot_archive.SlotAggregate
                Where (date(completed) between ? and ?) and SpinsLeft = 0 group by GameId, cast(WinTotal / 1000000 as unsigned) with rollup;", array($startDate, $endDate));
            
            $slot_plays = array();
            $totals = array();

            foreach($rs->result() as $row)
            {
                if($row->num_millions)
                {
                    $slot_plays[$row->num_millions][$game_themes[$row->GameID]] = $row->cnt;
                }
                else
                {
                    if($row->GameID)
                        $totals[$game_themes[$row->GameID]] = $row->cnt;
                }
            }
            
            foreach($slot_plays as &$play)
                $play = array_merge ($template, $play);
            
            ksort($slot_plays);
            $lines = array();
            foreach($slot_plays as $key => $array)
            {
                $line = "{num: '$key', ";
                foreach($array as $key => $value)
                    if(isset($totals[$key]))
                        $line .= $key . ": '" . number_format(($value / $totals[$key]) * 100,1) . "', ";
                $lines[] = trim($line, ", ") . "}";
            }
            $ykeys = "['" . implode("','", $game_themes) . "']";
            $labels = "['" . implode("','", $game_names) . "']";
            //print_r($ykeys); die();
            $rs = $db->query("SELECT date(completed) as date, max(WinTotal) as max, min(WinTotal) as min, convert(avg(WinTotal), unsigned) as avg 
                From kizzangslot_archive.SlotAggregate 
                where date(completed) between ? and ? and SpinsLeft = 0
                group by date(completed);", array($startDate, $endDate));
            if($rs->num_rows())
                $slot_scores = $rs->result();
            else
                $slot_scores = array();
            
            foreach($slot_plays as &$array)
                foreach($array as $key => &$value)
                    if(isset($totals[$key]))
                        $value = number_format(($value / $totals[$key]) * 100,1);
            
            foreach($slot_plays as $key => &$play)
                $play = array_merge (array('num' => "$key"), $play);
            
            $slot_plays = array_values($slot_plays);            
            
            return compact("lines", "ykeys", "labels", "startDate", "endDate", "slot_scores","slot_plays");
        }
        
        public function updateCampaignConversions()
        {
            
            $date = date("Y-m-d");
            $rs = $this->db->query("Select l.playerId, l.loginType, l.loginSource, l.mobileType, l.appId, i.email_campaign_id from PlayerLogins l
                Inner join Users p on p.id = l.playerId
                Inner join marketing.impressions i on INET_ATON(i.ip_address) = INET_ATON(l.ipAddress) and date(i.created) = ?
                Where INET_ATON(l.ipAddress) in (Select DISTINCT INET_ATON(ip_address) from marketing.impressions where date(created) = ?) 
                and date(accountCreated) = ? group by l.playerId;", array($date, $date, $date));
            $accounts = $rs->result();

            foreach($accounts as $account)
            {
                $this->db->query("Update Users set referralCode = ? where id = ?", array($account->email_campaign_id, $account->playerId));
                //print $this->db->last_query() . ";\n";
            }
            
        }
        
        public function campaignConversions($date)
        {
            $rs = $this->db->query("Select l.playerId, l.loginType, l.loginSource, l.mobileType, l.appId, p.referralCode as email_campaign_id
                from PlayerLogins l
                Inner join Users p on p.id = l.playerId                
                Where date(p.created) = ? and referralCode <> '0' group by l.playerId;", array($date));
            $accounts = $rs->result();
            $users = $this->load->view("/admin/reports/conversions_tbl", compact('accounts'), true);
            
            $rs = $this->db->query("Select email_campaign_id as label, count(*) as value 
                from (Select l.playerId, l.loginType, l.loginSource, l.mobileType, l.appId, p.referralCode as email_campaign_id
                from PlayerLogins l
                Inner join Users p on p.id = l.playerId
                Where date(p.created) = ? and referralCode <> '0' group by l.playerId) a group by email_campaign_id", array($date));
            $types = $rs->result();
            
            return compact('users', 'types');
            
        }
        
        public function sponsorFallOut($sponsorId)
        {
            $recs = array();
            $rs = $this->db->query("Select id, name from Sponsors where sponsorType = 'Advertiser' order by name");
            $sponsors = $rs->result();
            
            if($sponsorId)
            {
                $rs = $this->db->query("Select agg_date, device_type, sum(impression_count) as impression_count, sum(conversion_count) as conversion_count 
                    from marketing.impression_aggregate where sponsor = ?
                    group by agg_date, device_type with rollup", array(urldecode($sponsorId)));
                //print $this->db->last_query(); die();
                $recs = $rs->result();
                $sponsorId = urldecode($sponsorId);
            }
            
            return compact('sponsors','recs','sponsorId');
        }
        
        public function impressionFallOut($campaignId)
        {
            $recs = array();
            $rs = $this->db->query("Select id, description from Sponsor_Advertising_Campaigns order by id");
            $campaigns = $rs->result();
            
            if($campaignId)
            {
                $rs = $this->db->query("Select * from marketing.impression_aggregate where campaign = ?", array($campaignId));
                $recs = $rs->result();              
            }
            
            return compact('campaigns','recs','campaignId');
        }
        
        public function aggregateImpressions()
        {
            //Run this until they fix the mobile determination on the app
            $this->db->query("Update PlayerLogins, notifications.players set PlayerLogins.mobileType = notifications.players.device_type 
                where PlayerLogins.loginSource = 'Mobile' and (PlayerLogins.mobileType <> 'iOS' or PlayerLogins.mobileType <> 'Android') 
                    and PlayerLogins.playerId = notifications.players.player_id");
            
            $rs = $this->db->query("SELECT date(convert_tz(i.created, 'GMT', 'US/Pacific')) as date, p.name, email_campaign_id, destination, count(distinct i.id) as impression_count, 0 as conversion_count, 0 as conversion_percent 
                    From marketing.impressions i
                    Inner join Sponsor_Advertising_Campaigns s on i.email_campaign_id = s.id
                    Inner join Sponsors p on p.id = s.utm_source 
                    Group by date(convert_tz(i.created, 'GMT', 'US/Pacific')), email_campaign_id, destination;");
            
            $recs = array();
            $ret = array('count' => $rs->num_rows());
            foreach($rs->result() as $row)
            {
                $recs[$row->email_campaign_id][$row->date][$row->destination] = $row;
            }
            
            $rs = $this->db->query("Select date, referralCode as referralId, destination, count(playerId) as cnt from (Select l.playerId, min(date(convert_tz(l.created, 'GMT', 'US/Pacific'))) as date, if(mobileType = 'Android' or mobileType = 'iOS', mobileType, 'Web') as destination, p.referralCode as referralId
                From PlayerLogins l
                Inner join Users p on p.id = l.playerId
                Where referralID <> '0'
                Group by l.playerId) a
                Group by date, referralId, destination");
            foreach($rs->result() as $row)
            {               
                if(isset($recs[$row->referralId][$row->date][$row->destination]))
                {
                    $recs[$row->referralId][$row->date][$row->destination]->conversion_count = $row->cnt;                
                }
                else
                {
                    if(isset($recs[$row->referralId][$row->date]))
                    {
                        foreach($recs[$row->referralId][$row->date] as $index => $temp)
                        {
                            $recs[$row->referralId][$row->date][$index]->conversion_count += $row->cnt;
                            break;
                        }
                    }
                }
            }
            
            $query = "Insert into marketing.impression_aggregate (agg_date, campaign, device_type, sponsor, impression_count, conversion_count) values ";
            $value = "('%s','%s','%s','%s',%d,%d),";
            $post_query = " On duplicate key update impression_count = VALUES(impression_count), conversion_count = VALUES(conversion_count)";
            $values = "";
            $i = 1;
            foreach($recs as $campaigns)
            {
                foreach($campaigns as $campaign)
                {
                    foreach($campaign as $type)
                    {
                        $values .= sprintf($value, $type->date, $type->email_campaign_id, $type->destination, $type->name, $type->impression_count, $type->conversion_count);
                        if($i % 5000 == 0)
                        {
                            $this->db->query($query . rtrim($values, ",") . $post_query);
                            $i = 1;
                            $values = "";
                        }
                    }
                }
            }
            if($values)
            {
                $this->db->query($query . rtrim($values, ",") . $post_query);
            }    
            return $ret;
        }                
        
        public function getSweepstakesGraphs($id)
        {
            $rs = $this->db->query("Select name, startDate, endDate, taxValue, screenName as winner, sum(ticketCount) as cnt, count(DISTINCT t.playerId) as people_cnt
                From Sweepstakes s
                Left Join Winners w on s.id = w.foreign_id and w.game_type = 'Sweepstakes'
                Left Join Users p on w.player_id = p.id
                Left Join TicketAggregate t on t.sweepstakesId = s.id
                Where s.id = ?
                Group by s.id", array($id));
            $config = $rs->row();
            
            $info = $this->load->view("admin/reports/sweeps_tbl", compact('config'), true);
            $rs = $this->db->query("Select screenName as label, ticketCount as value 
                From TicketAggregate s
                Inner join Users p on s.playerId = p.id
                Where s.sweepstakesId = ? order by ticketCount DESC limit 100", array($id));
            $sweeps_player = $rs->result();
            
            $rs = $this->db->query("Select date(updated) as date, sum(count) as played, count(DISTINCT playerId) as players 
                from TicketCompressed
                Where sweepstakesId = ?
                Group by date(updated)", array($id));
            $sweeps_play = $rs->result();
            if(!$sweeps_play)
            {
                $rs = $this->db->query("Select date(updated) as date, sum(count) as played, count(DISTINCT playerId) as players 
                    from TicketCompressedArchive 
                    Where sweepstakesId = ?
                    Group by date(updated)", array($id));
                $sweeps_play = $rs->result();
            }
            return compact("sweeps_play", "id", "sweeps_player", "info");
        }
        
        public function getParlayDD()
        {
            $rs = $this->db->query("Select parlayCardId as id, date(cardDate) as date from SportParlayConfig where cardDate < now() and parlayCardId in (Select DISTINCT parlayCardId from SportGameResults) order by cardDate DESC");
            return $rs->result();
        }
        
        public function getParlayGraphs($id)
        {            
            $rs = $this->db->query("Select wins, count(id) as num_winners, count(DISTINCT playerId) as distinct_winners
                From SportPlayerCards                
                Where parlayCardId = ?
                Group by wins", array($id));
            $parlay_wins = $rs->result();
            
            $rs = $this->db->query("Select screenName as label, count(s.id) as value
                From SportPlayerCards s
                Inner join Users p on s.playerId = p.id
                Where s.parlayCardId = ? Group by p.id", array($id));
            $parlay_play = $rs->result();
            
            return compact("parlay_play", "id", "parlay_wins");
        }
        
        public function retentionCreate()
        {
            $date_query = '2016-09-27';
            $recs = array();
            $this->db->query("SET SESSION group_concat_max_len = 10000000");
            $rs = $this->db->query("Select started, days, group_concat(player_id ORDER BY player_id) as ids from (SELECT player_id, date(started) as started, datediff(date(started), '2016-09-27') as days 
                FROM reports.PlayerEvents group by player_id, date(started) having days >= 0 and started IS NOT NULL) a group by started", array($date_query));
            $dates = $rs->result();
            foreach($dates as $date)
            {
                $recs[$date->days]['ret_date'] = $date->started;
                $recs[$date->days]['day_diff'] = $date->days;
                $recs[$date->days]['ids_started'] = "[]";
                $recs[$date->days]['ids_played'] = json_encode(explode(",", $date->ids));
            }
            
            $rs = $this->db->query("Select 
                date(convert_tz(created, 'GMT', 'US/Pacific')) as date,
                datediff(date(convert_tz(created, 'GMT', 'US/Pacific')), ?) as days,
                group_concat(DISTINCT p.id ORDER BY p.id) as player_ids 
                from Users p
                group by date(convert_tz(created, 'GMT', 'US/Pacific')) having days >= 0 and date IS NOT NULL", array($date_query));
            $players = $rs->result();
                        
            $starts = array();
            foreach($players as $player)
                if($player->days >= 0)
                    $starts[$player->days] = $player->player_ids;
                        
            foreach($starts as $index => $start)
            {
                $player_ids = explode(",", $start);                
                $recs[$index]['ids_started'] = json_encode(array_values(array_unique($player_ids)));
            }
            
            //print_r($recs); die();
            
            $this->db->query("Truncate reports.retention");  
            $this->db->insert_batch("reports.retention", $recs);
            return array('success' => true);
        }
        
        public function retentionMatrix($date = '2016-09-27')
        {
            $raw = array();
            $rs = $this->db->query("Select * from reports.retention where ret_date >= ? ", array($date));
            $temp = $rs->result();
            foreach($temp as $index => $row)
            {
                if(!$index)                
                    $start_index = $index;
                
                $raw[$index - $start_index] = array('date' => $row->ret_date, 'ids_started' => json_decode($row->ids_started), 'ids_played' => json_decode($row->ids_played));
            }
            
            $matrix = array();
            $row_info = array();
            $seven_day = array();
            $thirty_day = array();
            foreach($raw as $y => $start_row)
            {
                $row_info[$y] = array('date' => $start_row['date'], 'total' => count($start_row['ids_started']));
                foreach($raw as $x => $compare_row)
                {
                    if($x < $y)
                        continue;
                    $temp = array_intersect($start_row['ids_started'], $compare_row['ids_played']);
                    $matrix[$y][$x - $y] = count($temp);
                    if(($x - $y) == 7)
                        if(count($start_row['ids_started']))
                            $seven_day[] = array('total' => count($temp), 'date' => $start_row['date'], 'percent' => number_format ((count($temp) / count($start_row['ids_started'])) * 100, 1));
                    if(($x - $y) == 30)
                        if(count($start_row['ids_started']))
                            $thirty_day[] = array('total' => count($temp), 'date' => $start_row['date'], 'percent' => number_format ((count($temp) / count($start_row['ids_started'])) * 100, 1));
                }
            }
            
            return compact('row_info', 'matrix', 'seven_day', 'thirty_day', 'date');
        }
        
        public function getSweepstakesDD()
        {
            $rs = $this->db->query("Select id, concat(name, ' ', date(endDate)) as description from Sweepstakes where startDate < now() order by endDate DESC");
            return $rs->result();
        }
        
        public function updateEvents()
        {
            $ret = array();
            $slots = $this->load->database ('slots', true);
            $scratchers = $this->load->database ('default', true);            
            $recs = array();
            
            //Do Slots first
            $rs = $this->db->query("Select max(started) as started from reports.PlayerEvents where game_type = 'Slots'");
            $started = $rs->row()->started;
            if(!$started)
                $started = '2015-02-23';
            
            $rs = $slots->query("Select *, convert_tz(completed, 'GMT', 'US/Pacific') as date 
                from kizzangslot_archive.SlotAggregate where convert_tz(completed, 'GMT', 'US/Pacific') > ? order by completed limit 10000", array($started));
            $ret['slots'] = $rs->num_rows();
            foreach($rs->result() as $key => $row)
            {
                $recs[] = array("player_id" => $row->PlayerId, "started" => $row->date, "game_type" => "Slots", "foreign_key" => ($row->SlotTournamentId * 1000000) + $row->SessionId, "extra" => number_format($row->WinTotal));
            }
            
            //Do Parlay Cards
            $rs = $this->db->query("Select max(started) as started from reports.PlayerEvents where game_type = 'Parlay'");
            $started = $rs->row()->started;
            if(!$started)
                $started = '2015-02-23';
            
            $rs = $this->db->query("Select * from SportPlayerCards where dateTime > ? limit 10000", array($started));
            $ret['parlay'] = $rs->num_rows();
            foreach($rs->result() as $key => $row)
            {
                $recs[] = array("player_id" => $row->playerId, "started" => $row->dateTime, "game_type" => "Parlay", "foreign_key" => $row->id, "extra" => $row->wins);
            }
            
            //Do Brackets
            $rs = $this->db->query("Select max(started) as started from reports.PlayerEvents where game_type = 'Bracket'");
            $started = $rs->row()->started;
            if(!$started)
                $started = '2015-02-23';
            
            $rs = $this->db->query("Select *, convert_tz(created, 'GMT', 'US/Pacific') as dateTime from BracketPlayerMatchups where date(convert_tz(created, 'GMT', 'US/Pacific')) > ? limit 10000", array($started));
            $ret['bracket'] = $rs->num_rows();
            foreach($rs->result() as $key => $row)
            {
                $recs[] = array("player_id" => $row->playerId, "started" => $row->dateTime, "game_type" => "Bracket", "foreign_key" => $row->id, "extra" => $row->wins);
            }
            
            //Do Scratcher Cards
            $rs = $this->db->query("Select max(started) as started from reports.PlayerEvents where game_type = 'Scratchers'");
            $started = $rs->row()->started;
            if(!$started)
                $started = '2015-02-23';
            
            $rs = $scratchers->query("Select PlayerId, TimeStamp as ts, ScratchId, SerialNumber from Scratch_GPPlays where TimeStamp > ? order by TimeStamp limit 10000", array($started));
            $ret['scratchers'] = $rs->num_rows();
            foreach($rs->result() as $key => $row)
            {
                $recs[] = array("player_id" => $row->PlayerId, "started" => $row->ts, "game_type" => "Scratchers", "foreign_key" => $row->ScratchId, "extra" => $row->SerialNumber);
            }
                        
            //Do Logins
            $rs = $this->db->query("Select max(started) as started from reports.PlayerEvents where game_type = 'Login'");
            $started = $rs->row()->started;
            if(!$started)
                $started = '2015-02-23';
            
            $rs = $this->db->query("Select * from PlayerLogins where lastLogin > ? order by lastLogin limit 100000", array($started));
            $ret['logins'] = $rs->num_rows();
            foreach($rs->result() as $key => $row)
            {
                $recs[] = array("player_id" => $row->playerId, "started" => $row->lastLogin, "game_type" => "Login", "foreign_key" => $row->playerId, "extra" => $row->loginType);
            }
            
            //Do Sweepstakes
            $rs = $this->db->query("Select max(started) as started from reports.PlayerEvents where game_type = 'Sweepstakes'");
            $started = $rs->row()->started;
            if(!$started)
                $started = '2015-02-23';
            
            $rs = $this->db->query("Select *, convert_tz(created, 'GMT', 'US/Pacific') as date from Tickets where created > ? order by created limit 100000", array($started));
            $ret['sweepstakes'] = $rs->num_rows();
            foreach($rs->result() as $key => $row)
            {
                $recs[] = array("player_id" => $row->playerId, "started" => $row->date, "game_type" => "Sweepstakes", "foreign_key" => $row->sweepstakesId, "extra" => $row->sweepstakesId);
            }
            
            //Do Lottery
            $rs = $this->db->query("Select max(started) as started from reports.PlayerEvents where game_type = 'Lottery'");
            $started = $rs->row()->started;
            if(!$started)
                $started = '2015-02-23';
            
            $rs = $this->db->query("Select *, convert_tz(created, 'GMT', 'US/Pacific') as startDate from LotteryCards where convert_tz(created, 'GMT', 'US/Pacific') > ? order by created limit 100000", array($started));
            $ret['lottery'] = $rs->num_rows();
            foreach($rs->result() as $key => $row)
            {
                $recs[] = array("player_id" => $row->playerId, "started" => $row->startDate, "game_type" => "Lottery", "foreign_key" => $row->id, "extra" => $row->lotteryConfigId);
            }
            
            //Do ROAL
            $rs = $this->db->query("Select max(started) as started from reports.PlayerEvents where game_type = 'ROAL'");
            $started = $rs->row()->started;
            if(!$started)
                $started = '2015-02-23';
            
            $rs = $this->db->query("Select *, convert_tz(created, 'GMT', 'US/Pacific') as startDate from ROALAnswers where convert_tz(created, 'GMT', 'US/Pacific') > ? order by created limit 100000", array($started));
            $ret['roal'] = $rs->num_rows();
            foreach($rs->result() as $key => $row)
            {
                $recs[] = array("player_id" => $row->playerId, "started" => $row->startDate, "game_type" => "ROAL", "foreign_key" => $row->ROALQuestionId, "extra" => $row->ROALConfigId);
            }
            
            //Insert Records
            $insert = "Insert into reports.PlayerEvents (player_id, started, game_type, foreign_id, extra) values";
            $query = "";
            $insert_append = " ON DUPLICATE KEY UPDATE foreign_id = values(foreign_id), extra = values(extra);";
            $ret['total'] = count($recs);
            foreach($recs as $index => $rec)
            {
                $query .= sprintf("(%d, '%s', '%s', %d, '%s'),", $rec['player_id'], $rec['started'], $rec['game_type'], $rec['foreign_key'], $rec['extra']);
                if($index && !($index % 5000))
                {                    
                    $this->db->query($insert . trim($query, ",") . $insert_append);
                    $query = "";
                }
            }
                        
            if($query)
            {
                $this->db->query($insert . trim($query, ",") . $insert_append);
            }
            
            $ret['success'] = true;
            return $ret;
        }
        
        public function updateGameLeaderBoards()
        {
            $slots = $this->load->database ('slots', true);
            $rows = array();
            $ret = array();
            
            //Get all the slot
            $rs = $slots->query("Select * from SlotTournament where date(convert_tz(now(), 'GMT', 'US/Pacific')) between StartDate and EndDate");
            if($rs->num_rows())
            {                
                $tourneys = $rs->result();
                $ret['slot_tournaments'] = $tourneys;
                foreach($tourneys as $tourney)
                {
                    $prizes = json_decode($tourney->PrizeList);
                    $rs = $slots->query(sprintf("Select ScreenName, p.PlayerID, Name as game, s.SessionID, max(WinTotal) as score  
                        from Session_%d s
                        Inner join Log_%d l on s.SessionID = l.SessionID
                        Inner join SlotGame g on g.ID = s.GameID
                        Inner join Players p on p.PlayerID = s.PlayerID 
                        Group by s.SessionID order by max(WinTotal) DESC", $tourney->ID, $tourney->ID));
                    if($rs->num_rows())
                    {
                        foreach($rs->result() as $index => $row)
                        {
                            $rows[] = array('game_type' => 'Slot', 'game_sub_type' => $tourney->type, 'foreign_key' => $row->SessionID, 'game' => $row->game, 'player_id' => $row->PlayerID,
                                'player_name' => $row->ScreenName, 'score' => number_format ($row->score), 'endDate' => date("Y-m-d", strtotime($tourney->EndDate)), 
                                'prize' => isset($prizes[$index]) ? $prizes[$index] : 0, 'place' => $index + 1);
                        }
                    }
                }
            }
            
            $this->load->model("admin_model");
            //Get all the parlay
            
            $rs = $this->db->query("SELECT type, max(s.parlayCardId) as id FROM SportGameResults s
                Inner join SportParlayConfig p on s.parlayCardId = p.parlayCardId
                Where p.endDate > date(now()) - INTERVAL 20 DAY group by type");
            
            foreach($rs->result() as $rc)
            {
                $this->admin_model->getParlayWinners($rc->id);
                $rs = $this->db->query("Select * from SportParlayConfig where parlayCardId = ?", array($rc->id));
                if($rs->num_rows())
                {
                    $cards = $rs->result();
                    $ret['parlay_cards'] = $cards;
                    foreach($cards as $card)
                    {
                        $rs = $this->db->query("Select count(id) as cnt from SportPlayerCards where parlayCardId = ? and wins = (Select max(wins) from SportPlayerCards where parlayCardId = ?)", array($card->parlayCardId, $card->parlayCardId));
                        $cnt = $rs->row()->cnt;
                        if($cnt)
                            $prize = str_replace(",", "", $card->cardWin) / $cnt;
                        else
                            $prize = 0;
                        $rs = $this->db->query("Select spc.id, p.screenName, p.id as player_id, spc.wins, spc.losses
                            From SportPlayerCards spc                
                            Inner join Users p on p.id = spc.playerId                        
                            Where parlayCardId = ? order by wins DESC limit 100", array($card->parlayCardId));
                        if($rs->num_rows())
                        {
                            $place = 1;
                            $last_wins = 0;
                            $num_places = 0;
                            foreach($rs->result() as $index => $row)
                            {
                                $temp = array('game_type' => 'Parlay', 'game_sub_type' => $card->type, 'foreign_key' => $row->id, 'game' => NULL, 'player_id' => $row->player_id,
                                    'player_name' => $row->screenName, 'score' => $row->wins . "-" . $row->losses, 'endDate' => date("Y-m-d", strtotime($card->endDate)), 'prize' => $place == 1 ? number_format($prize, 2) : 0);
                                if(!$index)
                                {
                                    $temp['place'] = $place;
                                }
                                elseif($last_wins == $row->wins)
                                {
                                    $temp['place'] = $place;
                                    $num_places++;                                   
                                }
                                else
                                {
                                    $place += ($num_places ? $num_places : 1);
                                    $num_places = 0;
                                    $temp['place'] = $place;
                                }
                                $last_wins = $row->wins;
                                $rows[] = $temp;
                            }
                        }
                    }
                }
            }
            
            //Get the biggame 30 stuff
            $rs = $this->db->query("Select * from BGQuestionsConfig where date(convert_tz(now(), 'GMT', 'US/Pacific')) between startDate and endDate");
            if($rs->num_rows())
            {
                $cards = $rs->result();
                $ret['parlay_cards'] = $cards;
                foreach($cards as $card)
                {                    
                    $rs = $this->db->query("Select p.screenName, p.id as player_id, bg.wins, bg.losses
                        From BGPlayerCards bg                
                        Inner join Users p on p.id = bg.playerId                        
                        Where parlayCardId = ? order by wins DESC limit 100", array($card->parlayCardId));
                    if($rs->num_rows())
                    {
                        $place = 1;
                        $last_wins = 0;
                        $num_places = 0;
                        foreach($rs->result() as $index => $row)
                        {
                            $temp = array('game_type' => 'Parlay', 'game_sub_type' => 'sibiggame', 'foreign_key' => 0, 'game' => NULL, 'player_id' => $row->player_id,
                                'player_name' => $row->screenName, 'score' => $row->wins . "-" . $row->losses, 'endDate' => date("Y-m-d", strtotime($card->endDate)), 'prize' => 0);
                            if(!$index)
                            {
                                $temp['place'] = $place;
                            }
                            elseif($last_wins == $row->wins)
                            {
                                $temp['place'] = $place;
                                $num_places++;
                            }
                            else
                            {
                                $place += $num_places;
                                $num_places = 0;
                                $temp['place'] = $place;
                            }
                            $last_wins = $row->wins;
                            $rows[] = $temp;
                        }
                    }
                }
            }
            
            //Get the Run of a Lifetime Stuff
            $rs = $this->db->query("Select 'Parlay' as game_type, 'ROAL' as game_sub_type, ROALConfigId as foreign_key, 'ROAL' as game, u.id as player_id, u.screenName as player_name,
                currentStreak as score, date(now()) as endDate, 0 as prize, currentStreak as place
                from ROALAnswers a
                Inner join Users u on u.id = a.playerId                
                where concat(playerId, '-', ROALConfigId) in 
                    (Select concat(playerId, '-', max(b.ROALConfigId)) from ROALAnswers b 
                        Inner join ROALQuestions q on q.id = b.ROALQuestionId and q.answer IS NOT NULL
                        group by playerId) 
                order by currentStreak DESC");
            foreach($rs->result() as $row)
                $rows[] = json_decode (json_encode($row), true);
            
            //Get the bracket challenge stuff
            $rs = $this->db->query("Select c.*, min(t.startDate) as gameStartDate, max(t.endDate) as gameEndDate from BracketConfigs c
                Inner join BracketTimes t on c.id = t.bracketConfigId
                group by c.id
                having date(convert_tz(now(), 'GMT', 'US/Pacific')) between gameStartDate and gameEndDate");
            if($rs->num_rows())
            {
                $cards = $rs->result();
                $ret['bracket_cards'] = $cards;
                foreach($cards as $card)
                {                    
                    $rs = $this->db->query("Select p.screenName, p.id as player_id, b.wins, b.losses, date(convert_tz(b.created, 'GMT', 'US/Pacific')) as created
                        From BracketPlayerMatchups b                
                        Inner join Users p on p.id = b.playerId                        
                        Where b.bracketConfigId = ? order by wins DESC limit 100", array($card->id));
                    if($rs->num_rows())
                    {
                        $place = 1;
                        $last_wins = 0;
                        $num_places = 1;
                        foreach($rs->result() as $index => $row)
                        {
                            $temp = array('game_type' => 'Parlay', 'game_sub_type' => 'bracket', 'game' => NULL, 'player_id' => $row->player_id,
                                'player_name' => $row->screenName, 'score' => $row->wins . "-" . $row->losses, 'endDate' => date("Y-m-d", strtotime($row->created)), 'prize' => 0);
                            if(!$index)
                            {
                                $temp['place'] = $place;
                            }
                            elseif($last_wins == $row->wins)
                            {
                                $temp['place'] = $place;
                                $num_places++;
                            }
                            else
                            {
                                $place += $num_places;
                                $num_places = 1;
                                $temp['place'] = $place;
                            }
                            $last_wins = $row->wins;
                            $rows[] = $temp;
                        }
                    }
                }
            }
            
            //print_r($rows); die();
            if(count($rows))
            {
                $ret['entry_count'] = count($rows);
                //print_r($rows); die();
                $this->db->query("Truncate GameLeaderBoards");                
                $this->db->insert_batch("GameLeaderBoards", $rows);
            }
            $ret['success'] = true;
            return $ret;
        }
        
        public function getSurveyEmails()
        {
            $rs = $this->db->query("SELECT * FROM kizzang.PlayerSearch where id in 
                (Select p.id from Users p
                Inner join (SELECT playerId, count(distinct date(lastLogin)) as cnt FROM kizzang.PlayerLogins group by playerId having cnt = 1) a on a.playerId = p.id 
                where accountStatus = 'Active' and p.created between '2015-10-16' and '2015-10-21');");
            foreach($rs->result() as $row)
            {
                $info = json_decode($row->information, true);
                print ".\t.\t.\t.\t.\t" . $info['email'] . "\n";
            }
        }
        
        public function userGainLoss()
        {
            $previous_day = array();
            $rows = array();
            $i = 0;
            $limit = 20;
            $offset = 0;
            $rs = $this->db->query("Select * from reports.retention order by ret_date limit 20");
            while($rs->num_rows())
            {
                foreach($rs->result() as $row)
                {
                    $rows[$i]['date'] = $row->ret_date;
                    $added = json_decode($row->ids_started);
                    $rows[$i]['people_added'] = count($added);
                    $played = json_decode($row->ids_played);
                    $rows[$i]['people_played'] = count($played);
                    $rows[$i]['people_returning'] = count(array_intersect($played, $previous_day));
                    $rows[$i]['people_not_returning'] = count(array_diff($previous_day, $played));
                    $previous_day = $added;
                    $i++;
                }
                $offset += $limit;
                $rs = $this->db->query(sprintf("Select * from reports.retention order by ret_date limit %d offset %d", $limit, $offset));
            }
            print_r($rows);
        }
        
        public function processEvents()
        {
            $insert_recs = array();
            $rs = $this->db->query("Select max(date(login_ts)) as last_date from reports.PlayerDay");
            $last_date = $rs->row()->last_date;
            if(!$last_date)
                $last_date = '2015-02-23';
            
            $rs = $this->db->query("Select distinct player_id from reports.PlayerEvents where player_id <> 0 and date(started) > ? order by player_id", array($last_date));
            if($rs->num_rows())
            {
                $players = $rs->result();                
                foreach($players as $player)
                {
                    $last_date = NULL;
                    $recs = array();
                    $rec = array();
                    $rs = $this->db->query("Select * from reports.PlayerEvents where player_id = ? order by started", array($player->player_id));
                    foreach($rs->result() as $event)
                    {
                        switch($event->game_type)
                        {
                            case "Login":
                                if(!isset($rec['data']))
                                {
                                    $rec['login_ts'] = $event->started;
                                    $rec['event_date'] = date("Y-m-d", strtotime($event->started));
                                    $rec['player_id'] = $player->player_id;
                                }
                                else
                                {
                                    $rec['logout_ts'] = $last_date;
                                    $recs[] = $rec;
                                    $rec = array();
                                    $rec['login_ts'] = $event->started;
                                    $rec['event_date'] = date("Y-m-d", strtotime($event->started));
                                    $rec['player_id'] = $player->player_id;
                                }
                                break;
                                
                            case "Slots":
                            case "Sweepstakes":
                            case "Tickets":
                            case "Scratchers":
                            case "Parlay":
                                if(isset($rec['event_date']) && date("Y-m-d", strtotime($event->started)) == $rec['event_date'])
                                {
                                    $rec['data'][] = $event;
                                }
                                else
                                {
                                    $rec['logout_ts'] = $last_date;
                                    $recs[] = $rec;
                                    $rec = array();
                                    $rec['login_ts'] = $event->started;
                                    $rec['event_date'] = date("Y-m-d", strtotime($event->started));
                                    $rec['player_id'] = $player->player_id;
                                    $rec['data'][] = $event;
                                }
                                break;
                        }
                        $last_date = $event->started;
                    }
                    
                    //Time to filter out any records that make no sense
                    foreach($recs as $rec)
                    {
                        if(isset($rec['login_ts']) && isset($rec['logout_ts']) && $rec['login_ts'] != $rec['logout_ts'])
                        {
                            $rec['data'] = json_encode($rec['data']);
                            $insert_recs[] = $rec;
                        }
                    }    
                }
                
                $insert = "Insert into reports.PlayerDay (player_id, event_date, login_ts, logout_ts, day_data) values ";
                $query = "";
                foreach($insert_recs as $index => $rec)
                {
                    if($index && !($index % 500))
                    {
                        $this->db->query($insert . trim($query, ","));
                        $query = "";
                    }
                    else
                    {
                        $query .= sprintf("(%d,'%s','%s','%s','%s'),", $rec['player_id'], $rec['event_date'], $rec['login_ts'], $rec['logout_ts'], $rec['data']);
                    }
                }
                
                if($query)
                    $this->db->query($insert . trim($query, ","));
                                
            }
            print "DONE"; die();
        }
        
        public function DBSizes()
        {
            $recs = array();
            $dbs = array('default','slots','admin');
            foreach($dbs as $dbname)
            {
                $db = $this->load->database ($dbname, true);
                $rs = $db->query("SELECT
                    count(*) tables,
                    table_schema,concat(round(sum(table_rows)/1000000,2),'M') rows,
                    concat(round(sum(data_length)/(1024*1024*1024),2),'G') data,
                    concat(round(sum(index_length)/(1024*1024*1024),2),'G') idx,
                    concat(round(sum(data_length+index_length)/(1024*1024*1024),2),'G') total_size,
                    round(sum(index_length)/sum(data_length),2) idxfrac
                    FROM information_schema.TABLES
                    GROUP BY table_schema
                    WITH ROLLUP");
                $recs[$dbname] = $rs->result();
            }
            return $recs;
        }
        
        public function DBTableSizes($conn, $schema)
        {
            $db = $this->load->database ($conn, true);
            $rs = $db->query("SELECT TABLE_NAME, table_rows, data_length, index_length, 
            sum(round(((data_length + index_length) / 1024 / 1024),2)) size
            FROM information_schema.TABLES WHERE table_schema = ? group by TABLE_NAME with rollup", array($schema));
            return $rs->result();
        }
        
        public function getTopTen($num_recs, $order_by = 'game_total')
        {            
            switch($order_by)
            {
                case "game_total": $order = "sum(count) DESC"; break;
                case "slot_total": $order = "sum(if(gameType = 'SlotTournament', count, 0)) DESC"; break;
                case "scratcher_total": $order = "sum(if(gameType = 'ScratchCard', count, 0)) DESC"; break;
                case "sport_total": $order = "sum(if(gameType = 'SportsEvent', count, 0)) DESC"; break;
                default: $order = "sum(count) DESC"; break;
            }
            $genders = array(1 => 'Male', 2 => 'Female', 3 => 'Other');
            $counts = array(10, 25, 50, 100);
            if($order_by != "amount")
            {
                $rs = $this->db->query("Select p.id as playerId, p.screenName, p.gender, sum(count) as total_count, sum(if(gameType = 'SlotTournament', count, 0)) as slot_count, 
                        sum(if(gameType = 'ScratchCard', count, 0)) as scratcher_count, sum(if(gameType = 'SportsEvent', count, 0)) as sport_count
                        From Users p
                        Inner join GameCount g on p.id = g.playerId                    
                        Group by p.id
                        order by $order 
                        limit $num_recs;");
            }
            else
            {
                $rs = $this->db->query("Select p.id as playerId, p.screenName, p.gender, sum(count) as total_count, sum(if(gameType = 'SlotTournament', count, 0)) as slot_count, 
                        sum(if(gameType = 'ScratchCard', count, 0)) as scratcher_count, sum(if(gameType = 'SportsEvent', count, 0)) as sport_count
                        From Users p
                        Inner join GameCount g on p.id = g.playerId                                            
                        where p.id in (Select id from (Select player_id as id, sum(amount) from Winners group by player_id order by sum(amount) DESC LIMIT $num_recs) a)
                        Group by p.id
                        limit $num_recs;");
            }
            
            $ids = $players = array();
            foreach($rs->result() as $row)
            {                
                $row->amount = 0;
                $players[$row->playerId] = $row;
                $ids[] = $row->playerId;
            }
            
            $id_string = implode(",", $ids);
                        
            $rs = $this->db->query(sprintf("Select player_id, sum(amount) as amount from Winners where player_id in (%s) group by player_id", $id_string));
                foreach($rs->result() as $row)
                    $players[$row->player_id]->amount = $row->amount;                            
            
            $rs = $this->db->query(sprintf("Select playerId, max(lastLogin) as date from PlayerLogins where playerId in (%s) group by playerId", $id_string));
            foreach($rs->result() as $row)
                $players[$row->playerId]->lastLogin = $row->date;
            
            foreach($players as &$player)
            {
                $rs = $this->db->query("Select avg(tos) as tos_avg, count(tos) as cnt from (Select playPeriodId, sum(case when gameType = 'SlotTournament' then count * 3.5 when gameType = 'ScratchCard' then count * .5 when gameType = 'SportsEvent' then count * .5 end) as tos
                    from GameCount where playerId = ? group by playPeriodId) a;", array($player->playerId));
                $player->tos = (floor($rs->row()->tos_avg / 60)) . " Hours " . (ceil($rs->row()->tos_avg) % 60) . " Minutes";
                $player->days = $rs->row()->cnt;
            }
            $players = array_values($players);
            
            if($order_by == "amount")
            {
                $amounts = array();
                foreach($players as $key => $row)
                    $amounts["$key"] = $row->amount;
                arsort($amounts);
                $tmp = array();
                foreach($amounts as $key => $value)
                    $tmp[] = $players[$key];
                $players = array_values($tmp);
            }
            
            return compact('players','num_recs','counts','order_by');
        }
        
        public function getDashboardInfo()
        {
            $userTypes = array('User','Guest','Admin','All');
            $mobileTypes = array('None','iOS','Android','All');
            $loginSources = array('Web','Mobile','All');
            $loginTypes = array('Normal','Facebook','All');
            $startDate = date("Y-m-d", strtotime("-7 days"));
            $endDate = date("Y-m-d", strtotime("today"));
            return compact('userTypes','mobileTypes','loginSources','loginTypes','startDate','endDate');
        }
        
        public function getDashboardData($data)
        {            
            $date_query = sprintf("Where date(lastLogin) between '%s' and '%s'", $data['startDate'], $data['endDate']);
            $where = "";
            if($data['userType'] != "All")
                $where .= " and userType = '" . $data['userType'] . "'";
            if($data['mobileType'] != "All")
                $where .= " and mobileType = '" . $data['mobileType'] . "'";
            if($data['loginType'] != "All")
                $where .= " and loginType = '" . $data['loginType'] . "'";
            if($data['loginSource'] != "All")
                $where .= " and loginSource = '" . $data['loginSource'] . "'";

            $allUsers = $newUsers = $conversions = array();
            $this->db->query("SET @@session.time_zone = \"America/Los_Angeles\";");
            $this->db->query("SET group_concat_max_len=15000000");
            
            //Get All users per day
            $rs = $this->db->query("Select date(lastLogin) as date, count(DISTINCT playerId) as cnt
                From PlayerLogins p
                Inner join Users u on u.id = p.playerId 
                $date_query $where 
                Group by date(lastLogin)");
            //print $this->db->last_query() . "\n";
            if($rs->num_rows())
                foreach($rs->result() as $row)
                    $allUsers[$row->date] = $row->cnt;
            
            //Get All NEW Users per day
            $where = str_replace("lastLogin", "lastApprovedTOS", $where);
            $rs = $this->db->query("Select date(lastApprovedTOS) as date, count(DISTINCT u.id) as cnt
                From Users u
                Inner join PlayerLogins p on u.id = p.playerId 
                $date_query $where 
                Group by date(lastApprovedTOS)");
            //print $this->db->last_query() . "\n";
            if($rs->num_rows())
                foreach($rs->result() as $row)
                    $newUsers[$row->date] = $row->cnt;
            
            $mod_date_query = str_replace("lastLogin", "conversionTime", $date_query);
            $rs = $this->db->query("Select date(g.conversionTime) as date, count(DISTINCT g.playerId) cnt
                From Users u
                Inner join GuestConversions g on u.id = g.playerId
                Inner join PlayerLogins p on p.playerId = g.playerId  
                $mod_date_query $where 
                Group by date(lastApprovedTOS)");
            if($rs->num_rows())
                foreach($rs->result() as $row)
                    $conversions[$row->date] = $row->cnt;
            
            $user_info = array();
            foreach($allUsers as $date => $row)
                $user_info[] = array('date' => $date, 'dau' => $row, 'newuser' => isset($newUsers[$date]) ? $newUsers[$date] : 0, 
                    'conversion' => isset($conversions[$date]) ? $conversions[$date] : 0);
            
            //print_r($user_info); die();
            
            $startDate = strtotime($data['startDate']);
            $endDate = strtotime($data['endDate']);
            $midRets = array();
            
            for($i = $startDate; $i <= $endDate; $i += 86400)
            {
                $days = array(1, 3, 5, 7, 14, 30);
                foreach($days as $day)
                    $dates[$day] = date("Y-m-d", $i - (86400 * $day));
                
                $retDAU = $retNew = array();
                
                $date_query = "Where date(lastLogin) = '" . date("Y-m-d", $i) . "'";                
                $rs = $this->db->query("Select date(lastLogin) as date, group_concat(DISTINCT playerId) as ids
                    From PlayerLogins p
                    Inner join Users u on u.id = p.playerId 
                    $date_query $where 
                    Group by date(lastLogin)");
                if($rs->num_rows())
                    foreach($rs->result() as $row)
                        $retDAU = strstr($row->ids, ",") ? explode (",", $row->ids) : array($row->ids);
                
                $date_query = "Where date(lastApprovedTOS) in ('" . implode("','", $dates) . "') ";
                $rs = $this->db->query("Select date(lastApprovedTOS) as date, group_concat(DISTINCT u.id) as ids
                    From Users u
                    Inner join PlayerLogins p on u.id = p.playerId 
                    $date_query $where 
                    Group by date(lastApprovedTOS)");
                if($rs->num_rows())
                    foreach($rs->result() as $row)
                        $retNew[$row->date] = strstr($row->ids, ",") ? explode (",", $row->ids) : array($row->ids);
                
                foreach($dates as $day => $date)
                {
                    if(isset($retNew[$date]))
                        $temp = array_intersect($retNew[$date], $retDAU);
                    else
                        $temp = array();
                    $midRets[date("Y-m-d", $i)][$day] = count($temp);
                }
            }
            
            $ret_info = array();
            foreach($midRets as $date => $array)
            {
                $ret_info[$date] = array('date' => $date);
                foreach($array as $key => $value)
                    $ret_info[$date]['day' . $key] = $value;
            }
            $ret_info = array_values($ret_info);
            return compact('ret_info', 'user_info');
        }
        
         public function getStats($date)
        {
            $m = new Memcached();
            $m->addServer("localhost", 11211) or die("Blah");            
            
            $parlay_time = 20;
            $scratcher_time = 10;
            $slot_time = 180;
            
            $times = $hours =array();
            
            $dates = array();
            $start_date = strtotime(date("Y-m-d", strtotime("-21 days")));
            $today = strtotime("now");
            for($i = $start_date; $i < $today; $i += 86400)
                $dates[] = date("Y-m-d", $i);
            
            $mkey = "STATS-" . $date . "-new";
            if($m && $m->get($mkey))            
            {
                $temp = unserialize($m->get($mkey));
                //print_r($temp); die();
                $temp['dates'] = $dates;
                //return $temp;
            }
            
            $db = $this->load->database ('slots', true);
            $db_scratch = $this->load->database ('default', true);
            
            //Query for active session time
            $genders = array();
            $this->db->query("SET @@session.time_zone = \"America/Los_Angeles\";");
            $this->db->query("SET group_concat_max_len=15000000");
            $rs = $this->db->query("Select game_type, gender, count(u.id) as cnt, count(distinct u.id) as user_count from Users u
                Inner join reports.PlayerEvents p on u.id = p.player_id and date(p.started) = ? and game_type not in('Login','Tickets')
                group by game_type, gender with rollup", array($date));

            if($rs->num_rows())
            {
                foreach($rs->result() as $row)
                {
                    $multiplier = 1;
                    switch($row->game_type)
                    {
                        case 'Slots': $multiplier = 180; break;
                        case 'Parlay': $multiplier = 20; break;
                        case 'Scratchers': $multiplier = 15; break;
                        case 'Lottery': $multiplier = 10; break;
                        case 'ROAL': $multiplier = 10; break;
                        default: $multiplier = 1;
                    }
                    if($row->gender)
                    {
                        $times[$row->game_type][$row->gender]['cnt'] = $row->cnt;
                        $times[$row->game_type][$row->gender]['user_count'] = $row->user_count;
                        $times[$row->game_type][$row->gender]['time_per_user'] = floor(($row->cnt / $row->user_count) * $multiplier);
                        isset($genders[$row->gender]) ? $genders[$row->gender] += floor(($row->cnt / $row->user_count) * $multiplier) : $genders[$row->gender] = floor(($row->cnt / $row->user_count) * $multiplier);
                    }
                    elseif($row->game_type && !$row->gender)
                    {
                        $times[$row->game_type]['All']['cnt'] = $row->cnt;
                        $times[$row->game_type]['All']['user_count'] = $row->user_count;
                        $times[$row->game_type]['All']['time_per_user'] = floor(($row->cnt / $row->user_count) * $multiplier);
                        isset($genders["All"]) ? $genders["All"] += floor(($row->cnt / $row->user_count) * $multiplier) : $genders["All"] = floor(($row->cnt / $row->user_count) * $multiplier);
                    }
                }
                //print_r(compact('times','genders')); die();
            }   
                        
            //Get Chedda stats
            $chedda = array();
            $rs = $this->db->query("SELECT sum(count) as chedda_count FROM kizzang.Chedda where isUsed = 0 and date(convert_tz(created, 'GMT', 'US/Pacific')) <= ?", array($date));
            $chedda['unused'] = $rs->row()->chedda_count;
            
            $rs = $this->db->query("SELECT sum(count) as chedda_count FROM kizzang.Chedda where isUsed <> 0 and date(convert_tz(updated, 'GMT', 'US/Pacific')) = ?", array($date));
            $chedda['used'] = $rs->row()->chedda_count;
            
            //Get the retention Numbers
            $retention = array();
            $temp = $this->retentionMatrix(date("Y-m-d", strtotime("-30 Days")));
            foreach($temp['row_info'] as $index => $row)
            {
                $day = (strtotime($date) - strtotime($row['date'])) / 86400;
                switch($day)
                {
                    case 30:
                    case 14:
                    case 7:
                    case 5:
                    case 3:
                    case 1:
                        $retention[$day] = array('count' => $temp['matrix'][$index][count($temp['matrix'][$index]) - 1], 'original' => $row['total'],
                            'percent' => $row['total'] ? number_format(($temp['matrix'][$index][count($temp['matrix'][$index]) - 1] / $row['total']) * 100, 2) : 0);
                        break;
                }
            }
            
            $rs = $this->db->query("Select count(*) as cnt, avg(secDiff) as avg_diff from GuestConversions where date(conversionTime) = ?", array($date));
            $guest_conversions = $rs->row();
            
            $rs = $this->db->query("Select hour(started) as hour, count(distinct player_id) as cnt, 
                sum(if(game_type = 'Slots', 1, 0)) as slots,
                sum(if(game_type = 'Sweepstakes', 1, 0)) as sweepstakes,
                sum(if(game_type = 'ROAL', 1, 0)) as roal,
                sum(if(game_type = 'Lottery', 1, 0)) as lottery,
                sum(if(game_type = 'Parlay', 1, 0)) as parlay,
                sum(if(game_type = 'Bracket', 1, 0)) as brackets,
                sum(if(game_type = 'Scratchers', 1, 0)) as scratchers from reports.PlayerEvents where date(started) = ? group by hour(started)", array($date));
            $hours = $rs->result();
            
            $rs = $db_scratch->query("SELECT g.ID, g.Name, count(p.ScratchID) as cnt, count(DISTINCT p.PlayerID) as player_count 
                FROM Scratch_GPGames g
                Inner join Scratch_GPPlays p on p.SerialNumber = g.SerialNumber and date(p.TimeStamp) = ?
                Group by g.ID
                With Rollup", array($date));
            $scratchers = array();
            if($rs->num_rows())
                $scratchers = $rs->result();
            
            $rs = $this->db->query("SELECT p.serialNumber, p.cardDate, count(DISTINCT playerId) as player_count, count(c.id) as cnt, sum(if(c.isQuickpick = 1, 1, 0)) as qps, sum(if(c.isQuickpick = 0, 1, 0)) as nonqps 
                FROM SportParlayConfig p
                Inner join SportPlayerCards c on p.parlayCardId = c.parlayCardId AND date(dateTime) = ?
                Group by p.serialNumber
                With Rollup", array($date));
            $parlays = array();
            if($rs->num_rows())
                $parlays = $rs->result();
            
            $rs = $this->db->query("SELECT f.serialNumber, date(f.startDate) as cardDate, count(DISTINCT playerId) as player_count, count(a.id) as cnt 
                FROM FinalConfigs f
                Inner join FinalAnswers a on f.id = a.finalConfigId AND date(convert_tz(a.created, 'GMT', 'US/Pacific')) = ?
                Group by f.serialNumber
                With Rollup", array($date));
            $fts = array();
            if($rs->num_rows())
                $fts = $rs->result();
            
            $rs = $this->db->query("SELECT displayName, sweepstakesId, count(*) as cnt FROM Tickets t
                Inner join Sweepstakes s on s.id = t.sweepstakesId 
                where ticketDate = date(convert_tz(now(), 'GMT', 'US/Pacific'))
                Group by sweepstakesId;");
            $sweepstakes = array();
            if($rs->num_rows())
                $sweepstakes = $rs->result();
            
            $rs = $this->db->query("Select c.*, count(l.id) as cnt, count(DISTINCT playerId) as player_count
                From LotteryConfigs c
                Inner join LotteryCards l on c.id = l.lotteryConfigId and date(convert_tz(l.created, 'GMT', 'US/Pacific')) = ?
                Group by c.id", array($date));
            $lottery = array();
            if($rs->num_rows())
                $lottery = $rs->result();
            
            $rs = $this->db->query("Select c.*, count(a.playerId) as cnt 
                From ROALConfigs c
                Inner join ROALAnswers a on c.id = a.ROALConfigId and date(convert_tz(a.created, 'GMT', 'US/Pacific')) = ?
                Group by c.id", array($date));
            $roal = array();
            if($rs->num_rows())
                $roal = $rs->result();
            
            $rs = $db->query("Select * from SlotTournament where startDate <= ? and endDate > ? and type in ('Daily','Weekly','Monthly') order by endDate DESC", array($date, $date));
            $slot_configs = $rs->result();
            
            foreach($slot_configs as $slot_config)
            {
                $id = $slot_config->ID;
                $log_table = $session_table = NULL;
                $rs = $db->query("Select substring_index(TABLE_NAME, '_', 1) as id, concat(TABLE_SCHEMA, '.', TABLE_NAME) as name  
                    from information_schema.TABLES where (TABLE_NAME like 'Log_$id' OR TABLE_NAME like 'Session_$id') order by TABLE_NAME");
                $tables = $rs->result();

                foreach($tables as $table)
                {
                    switch($table->id)
                    {
                        case "Log": $log_table = $table->name; break;
                        case "Session": $session_table = $table->name; break;
                    }
                }

                $rs = $db->query("Select sg.ID, sg.Name, min(l.WinTotal) as min_total, max(l.WinTotal) as max_total, count(DISTINCT PlayerID) as num_players, count(DISTINCT s.SessionID) as num_games
                    From SlotGame sg
                    Inner join $session_table s on s.GameID = sg.ID
                    Inner join $log_table l on l.SessionID = s.SessionID
                    Where l.SpinsLeft = 0
                    Group by sg.ID
                    With Rollup");
                $slots[$slot_config->type] = $rs->result();
            }
                        
            $rs = $this->db->query("Select sum(if(date(created) <= ?, 1, 0)) as total_accounts, sum(if(userType = 'User' AND date(created) <= ?, 1, 0)) as total_user_accounts, 
                sum(if(userType = 'Guest' AND date(created) <= ?, 1, 0)) as total_guest_accounts,
                sum(if(date(created) = ?, 1, 0)) as new_signups, sum(if(date(created) = ? AND userType = 'User', 1, 0)) as new_user_signups, 
                sum(if(date(created) = ? AND userType = 'Guest', 1, 0)) as new_guest_signups,
                1 as returning
                from Users p", 
                array($date, $date, $date, $date,$date,$date));
            $player = $rs->row();
            
            $rs = $this->db->query("Select game_type, sum(if(userType = 'Guest', a.cnt, 0)) as guest, count(distinct player_id) as cnt, 
                sum(if(userType = 'User', a.cnt, 0)) as registered,
                sum(if(userType = 'Administrator', a.cnt, 0)) as admin, count(DISTINCT player_id) as total,
                sum(if(loginSource = 'Web', a.cnt, 0)) as online_count, sum(if(mobileType = 'iOS', a.cnt, 0)) as ios, sum(if(mobileType = 'Android', a.cnt, 0)) as android
                From (Select player_id, game_type, count(*) as cnt, count(DISTINCT player_id) as unq_cnt from reports.PlayerEvents where date(started) = date(convert_tz(now(), 'GMT', 'US/Pacific')) and game_type <> 'Login' group by player_id, game_type) a
                Inner Join (Select playerId, loginType, loginSource, mobileType from PlayerLogins where date(lastLogin) = date(convert_tz(now(), 'GMT', 'US/Pacific')) group by playerId) b on a.player_id = b.playerId
                Inner Join Users u on u.id = a.player_id
                Group by game_type
                With Rollup");
            $all_games = $rs->result();
            
            //New Users            
            $rs = $this->db->query("Select count(player_id) as cnt, sum(if(userType = 'User', 1, 0)) as user_cnt, sum(if(userType = 'Guest', 1, 0)) as guest_cnt 
                from Users u 
                Inner join (Select distinct player_id from reports.PlayerEvents where date(started) = ?) e on e.player_id = u.id", array($date));
            $temp = $rs->row();
            $player->daily_active_total = $temp->cnt;
            $player->daily_active_user = $temp->user_cnt;
            $player->daily_active_guest = $temp->guest_cnt;
            $player->new_users = 0;
            
            $accounts = array();
            //Account breakdown
            $rs = $this->db->query("Select loginType, userType, loginSource, IFNULL(mobileType, 'None') as mobileType, count(distinct playerId) as cnt, group_concat(distinct playerId) as ids 
                from Users p
                Inner join PlayerLogins l on p.id = l.playerId 
                where date(l.lastLogin) = ? and date(p.created) = ? group by loginType, loginSource, IFNULL(mobileType, 'None'), userType;", array($date, $date));
            $player->new_users = array('Total' => 0, 'User' => 0, 'Guest' => 0);
            if($rs->num_rows())
            {                               
                $used_ids = array();
                foreach($rs->result() as $row)
                {                    
                    $offset = 0;
                    $temp = explode(",", $row->ids);
                    foreach($temp as $pdup)
                    {
                        if(isset($used_ids[$pdup]))
                            $offset++;
                        else
                            $used_ids[$pdup] = $pdup;
                    }
                    $accounts[$row->loginType . "-" . $row->loginSource . "-" . $row->mobileType][$row->userType] = $row->cnt - $offset;
                    if(isset($accounts[$row->loginType . "-" . $row->loginSource . "-" . $row->mobileType]['Total']))
                        $accounts[$row->loginType . "-" . $row->loginSource . "-" . $row->mobileType]['Total'] += $row->cnt - $offset;
                    else
                        $accounts[$row->loginType . "-" . $row->loginSource . "-" . $row->mobileType]['Total'] = $row->cnt - $offset;
                    $player->new_users[$row->userType] += $row->cnt - $offset;
                    $player->new_users['Total'] += $row->cnt - $offset;
                }                
            }
            //print_r($accounts); die();
            
            $rs = $this->db->query("Select sum(if(a.date = ?, 1, 0)) as daily_active_users, sum(if(a.date = ? AND date(p.created) <> ?, 1, 0)) as returning
                From Users p
                Inner join (Select playerId, date(lastLogin) as date from PlayerLogins group by playerId, date(lastLogin)) a on a.playerId = p.id
                where a.date = ?", array($date, $date, $date, $date));
            $temp = $rs->row();
            
            $player->daily_active_users = $temp->daily_active_users;
                                   
            $user_winners = $guest_winners = array();
            $rs = $this->db->query("Select w.id, serial_number, game_type, game_name, prize_name, sum(amount) as amount, w.status, ticket_id, processed, concat(u.firstName, ' ', u.lastName) as name, concat(u.address, ' ', u.city, ',', u.state) as address 
                from Winners w
                Inner join Users u on u.id = w.player_id
                where date(w.created) = ? and u.userType = 'User' and (prize_email <> 'Imported from Old System' or prize_email IS NULL) 
                group by w.id
                with rollup", array($date));
            if($rs->num_rows())
                $user_winners = $rs->result();
            
            $rs = $this->db->query("Select w.id, serial_number, game_type, game_name, prize_name, sum(amount) as amount, w.status, ticket_id, processed, concat(u.firstName, ' ', u.lastName) as name, concat(u.address, ' ', u.city, ',', u.state) as address 
                from Winners w
                Inner join Users u on u.id = w.player_id
                where date(w.created) = ? and u.userType = 'Guest' and (prize_email <> 'Imported from Old System' or prize_email IS NULL)
                group by w.id
                with rollup", array($date));
            if($rs->num_rows())
                $guest_winners = $rs->result();
                        
            $info = compact('date', 'player', 'slots', 'all_games', 'sports', 'sweepstakes', 'chedda', 'user_winners', 'guest_winners', 'dates', 'genders', 'retention', 'scratchers', 'parlays', 'lottery','roal', 'times', 'fts', 'hours', 'accounts','guest_conversions');
            if($m)
            {
                if($date == date('Y-m-d')) // If Today                
                    $m->add($mkey, serialize($info), 600);
                elseif(strtotime($date) < strtotime("now"))
                    $m->add($mkey, serialize($info), 0);
            }                        
            
            return $info;
        }
        
        public function getAdStats($startDate, $endDate)
        {
            //Get Ad Information
            $rs = $this->db->query("Select count(DISTINCT playerId) as unq_people, type, status, count(*) as cnt 
                from Ads
                Where date(convert_tz(created, 'GMT', 'US/Pacific')) between ? and ?
                Group by type, status", array($startDate, $endDate));
            $ad_overview = $rs->result();

            $rs = $this->db->query("Select gameType, sum(if(status = 'Viewed', 1, 0)) as Viewed, sum(if(status = 'Clicked', 1, 0)) as Clicked, sum(if(status = 'Closed', 1, 0)) as Closed,
                sum(if(status = 'Empty', 1, 0)) as Empty, sum(if(status = 'Error', 1, 0)) as Error from Ads
                Where date(convert_tz(created, 'GMT', 'US/Pacific')) between ? and ?
                group by gameType;", array($startDate, $endDate));
            $ad_games = $rs->result();
            
            $rs = $this->db->query("Select sum(if(u.userType = 'Guest', a.cnt, 0)) as sum_guest, sum(if(u.userType = 'Guest', 1, 0)) as cnt_guest, 
                sum(if(u.userType = 'User', a.cnt, 0)) as sum_user, sum(if(u.userType = 'User', 1, 0)) as cnt_user, 
                sum(a.cnt) as sum_total, count(a.playerId) as cnt_total,
                sum(if(b.mobileType = 'iOS', a.cnt, 0)) as sum_ios, sum(if(b.mobileType = 'iOS', 1, 0)) as cnt_ios, 
                sum(if(b.mobileType = 'Android', a.cnt, 0)) as sum_android, sum(if(b.mobileType = 'Android', 1, 0)) as cnt_android,
                sum(if(b.mobileType = 'None', a.cnt, 0)) as sum_online, sum(if(b.mobileType = 'None', 1, 0)) as cnt_online
            From (Select playerId, count(*) as cnt from Ads where date(created) between ? and ? group by playerId) a
            Inner Join (Select playerId, loginType, loginSource, mobileType from PlayerLogins where date(lastLogin) between ? and ? group by playerId) b on a.playerId = b.playerId
            Inner Join Users u on u.id = a.playerId", array($startDate, $endDate, $startDate, $endDate));
            $ad_summary = $rs->row();
            
            return compact('ad_overview','ad_games','ad_summary','startDate', 'endDate');
        }
        
        public function getSlotStats($startDate, $endDate)
        {
            $db = $this->load->database ('slots', true);
            $rs = $db->query("Select * from SlotTournament 
                where startDate between ? and ? 
                or endDate between ? and ?  
                or ? between startDate and endDate
                or ? between startDate and endDate",
                array($startDate, $endDate, $startDate, $endDate, $startDate, $endDate));
            $slot_configs = $rs->result();
            
            foreach($slot_configs as $slot_config)
            {
                $id = $slot_config->ID;
                $log_table = $session_table = NULL;
                $rs = $db->query("Select substring_index(TABLE_NAME, '_', 1) as id, concat(TABLE_SCHEMA, '.', TABLE_NAME) as name  
                    from information_schema.TABLES where (TABLE_NAME like 'Log_$id' OR TABLE_NAME like 'Session_$id') order by TABLE_NAME");
                $tables = $rs->result();

                foreach($tables as $table)
                {
                    switch($table->id)
                    {
                        case "Log": $log_table = $table->name; break;
                        case "Session": $session_table = $table->name; break;
                    }
                }

                $rs = $db->query("Select sg.ID, sg.Name, min(l.WinTotal) as min_total, max(l.WinTotal) as max_total, count(DISTINCT PlayerID) as num_players, count(DISTINCT s.SessionID) as num_games
                    From SlotGame sg
                    Inner join $session_table s on s.GameID = sg.ID
                    Inner join $log_table l on l.SessionID = s.SessionID
                    Where l.SpinsLeft = 0
                    Group by sg.ID
                    With Rollup");
                $row = new stdClass();
                $row->games = $rs->result();
                $row->startDate = $slot_config->StartDate;
                $row->endDate = $slot_config->EndDate;
                $slots[$slot_config->type][] = $row;
            }
            return compact('slots');
        }
}

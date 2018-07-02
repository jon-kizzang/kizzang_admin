<?php 

class signalone_model extends CI_Model
{        
                
        private $url;
        private $headers;
        private $app_id;
        //ALL FUNCTIONS TO DO API CALLS
        function __construct()
        {
            parent::__construct();
            $this->url = "https://onesignal.com/api/v1/";
            $this->headers = array('Authorization: Basic NDZjY2RlMDQtYWI3My00OTBjLTkyMzMtOTE2MjlkY2YwMzY2', 'Content-Type: application/json');
            $this->app_id = getenv("SIGNALONEKEY");
            $this->db = $this->load->database ('admin', true);
                                    
        }
        
        private function processRequest($url, $data, $type)
        {
            switch($type)
            {
                case "POST": $is_post = true; break;
                case "GET": $is_post = false; break;
                case "PUT": $is_post = true; break;
            }

            $ch = curl_init($url);
            if($type == "PUT")
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            else
                curl_setopt($ch, CURLOPT_POST, $is_post);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
            if($is_post)
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            
            $ret = curl_exec($ch);
                        
            if(!$ret)
            {
                return "Curl Error:" . curl_errno($ch) . " - " . curl_error($ch);                
            }            
            
            return json_decode($ret, true);
        }        
        
        public function getNotifications()
        {
            return $this->signalone_model->processRequest($this->url . "notifications?" . http_build_query(array("app_id" => $this->app_id)), array() , "GET");
        }
        
        public function getPlayers($offset = 0)
        {
            return $this->signalone_model->processRequest($this->url . "players?" . http_build_query(array("app_id" => $this->app_id, "offset" => $offset)), array() , "GET");
        }
        
        public function getNotificationInfo($id = NULL)
        {
            $relations = array("=", "<", ">");
            $tags = array();
            $pn = NULL;
            
            if($id)
            {
                $rs = $this->db->query("Select * from notifications.queue where id = ?", array($id));
                if($rs->num_rows())
                {
                    $temp = $rs->row();
                    $pn = unserialize($temp->info);
                    if(isset($pn['contents']))
                        $pn['contents'] = $pn['contents']['en'];
                    else
                        $pn['contents'] = "";

                    if(isset($pn['headings']))
                        $pn['headings'] = $pn['headings']['en'];
                    else
                        $pn['headings'] = "";
                    
                    $pn['id'] = $id;                    
                }            
            }
            
            $rs = $this->db->query("Select tags from notifications.players where tags <> ''");
            $temp = $rs->result();
            foreach($temp as $row)
            {
                $tag_array = json_decode($row->tags, true);
                foreach($tag_array as $key => $value)
                {
                    if(!isset($tags[$key]))
                        $tags[$key] = $key;
                }
            }                          
            $types = array('Day 2','Day 5','Multiplier','Pro Football','College Football','Top 5','Sweepstakes');
            $notification_types =array('Batch','Individual');
            $players = array();
            
            return compact('relations', 'players', 'tags', 'pn', 'types','notification_types');
        }
        
        public function getNotificationHistory()
        {
            $rs = $this->db->query("Select * from notifications.history order by queued_at DESC");
            return $rs->result();
        }
        
        public function getNotificationQueue()
        {
            $pns = array();
            $rs = $this->db->query("Select q.*, count(c.id) as cnt 
                from notifications.queue q
                left join notifications.crons c on c.queue_id = q.id and c.status = 'Pending'
                group by q.id
                order by created DESC");
            foreach($rs->result() as $row)
            {                
                $temp = unserialize($row->info);
                $temp['id'] = $row->id;
                $temp['updated'] = $row->updated;
                $temp['cnt'] = $row->cnt;
                
                if(isset($temp['contents']))
                    $temp['contents'] = $temp['contents']['en'];
                else
                    $temp['contents'] = "N/A";
                
                if(isset($temp['headings']))
                    $temp['headings'] = $temp['headings']['en'];
                else
                    $temp['headings'] = "N/A";
                
                if(isset($temp['template_id']))
                {
                    $rs = $this->db->query("Select * from notifications.templates where id = ?", array($temp['template_id']));
                    $tmp = $rs->row();
                    $temp['template'] = $tmp->name;
                }
                else
                {
                    $temp['template'] = "N/A";
                }                
                $pns[] = $temp;
            }
            return $pns;
        }
        
        public function deleteFromQueue($id)
        {
            $this->db->delete("notifications.queue", array("id" => $id));
        }
        
        public function addPushNotification($data, &$ret, $save = false)
        {
            if(!$data['contents'] && !$data['template_id'])
            {
                $ret = "Either Contents or Templates need to be filled in.";
                return false;
            }
            
            if(!isset($data['isIos']) && !isset($data['isAndroid']))
            {
                $ret = "You have to select either iOS or Android or Both for your platform";
                return false;
            }
            
            $cdata = array('app_id' => $this->app_id, 'isIos' => isset($data['isIos']) ? true : false, 'isAndroid' => isset($data['isAndroid']) ? true : false);
            foreach($data as $key => $value)
            {
                if(!$value)
                    continue;
                
                switch($key)
                {
                    case 'contents':
                    case 'headings':                        
                        $cdata[$key] = array('en' => $value); break;
                    
                    case 'template_id':                         
                        $cdata[$key] = $value; break;
                    
                    //case 'include_player_ids':                        
                    //    $cdata[$key] = json_encode(array($value)); break;
                }
            }
            
            //Now for the filter stuff
            if(isset($data['key'][0]))
            {
                foreach($data['key'] as $index => $value)
                {
                    if($data['key'][$index] && $data['relation'][$index])
                        $cdata['tags'][] = array('key' => $data['key'][$index], 'relation' => $data['relation'][$index], 'value' => $data['value'][$index]);
                }
            }          
            
            //print_r($cdata); die();
            if($save)
            {
                if(isset($data['id']))
                {
                    $this->db->where("id", $data['id']);
                    $this->db->update("notifications.queue", array('info' => serialize($cdata)));
                }
                else
                {
                    $this->db->insert("notifications.queue", array('info' => serialize($cdata)));
                }
                
                $ret = "Notification Saved in Queue";
                return true;
            }
            else
            {
                $cdata = str_replace("<<YESTERDAY>>", date("m-d-Y", strtotime("yesterday")), str_replace("<<DATE>>", date("m-d-Y"), json_encode($cdata)));
                $temp = $this->signalone_model->processRequest($this->url . "notifications", $cdata , "POST");
            }
            
            if(isset($temp['recipients']))
            {
                $ret = "Sent to " . $temp['recipients'] . " Recipients!" ;
                return true;
            }
            if(isset($temp['errors']))
            {
                $ret = $temp['errors'][0];
                return false;
            }
            
            $ret = "Error sending notification: " . json_encode($temp);
            return false;
        }
        
        private function updatePlayerTags()
        {
            $date = date('Y-m-d');
            $stupid_date = date('m-d-Y');
            $rs = $this->db->query("Select p.id, p.player_id, sum(count) as games_played, gamesCredit 
                From notifications.players p
                Inner join PlayPeriod l on l.playerId = p.player_id and date(endDate) = ?
                Inner join GameCount g on g.playPeriodId = l.id
                Group by p.id", array($date));
            
            foreach($rs->result() as $key => $user)
            {                
                $info = json_encode(array('tags' => array("gamesRemainingToCompleteDay" => $user->gamesCredit > $user->games_played ? (string) ($user->gamesCredit - $user->games_played) : "0", "lastDatePlayed" => $stupid_date, "id" => $user->player_id)));
                $ret = $this->processRequest($this->url . "players/" . urlencode($user->id), $info, "PUT");                
            }
        }
        
        public function processCrons()
        {
            $ret = array();
            $startDate = date("Y-m-d H:i:s", strtotime("-20 minutes"));
            $endDate = date("Y-m-d H:i:s");
            $rs = $this->db->query("Select q.*, c.id as cron_id from notifications.queue q
                Inner join notifications.crons c on c.queue_id = q.id
                Where c.schedule_date between ? and ? and c.status = 'Pending' and c.is_active = 1", array($startDate, $endDate));
            
            foreach($rs->result() as $index => $row)
            {
                $temp = $this->processRequest($this->url . "notifications", json_encode(unserialize($row->info)) , "POST");  
                $ret[$index]['job'] = $row;
                $ret[$index]['result'] = $temp;
                $this->db->query("Update notifications.crons set status = 'Complete' where id = ?", array($row->cron_id));
            }
            return $ret;
        }
        
        public function runQueue($type = 'Games Left')
        {
            //Update Player Tags for cross platform stuff
            //$this->updatePlayerTags();
            $ret = array();
            $rs = $this->db->query("Select * from notifications.queue where type = ?", array($type));
            foreach($rs->result() as $index => $row)
            {
                $info = json_encode(unserialize($row->info));
                //Do all the filter replacements
                $info = str_replace("<<DATE>>", date("m-d-Y"), $info);
                $info = str_replace("<<YESTERDAY>>", date("m-d-Y", strtotime("yesterday")), $info);
                
                $info = json_decode($info, true); 
                
                if(strstr($info['contents']['en'], "\n"))
                    $contents = explode("\n", $info['contents']['en']);
                else
                    $contents[] = $info['contents']['en'];
                
                $info['contents']['en'] = $contents[rand(0, count($contents) - 1)];
                
                if($row->notification_type == "Individual") //Then find the users in the players table and update the content individually and send them seperately
                {
                    $this->updateDB();
                    $this->load->model("admin_model");
                    if($row->type == "Top 5")
                    {
                        $rs = $this->db->query("Select player_id, sum(amount) as amount
                            from Winners 
                            where week(created) = if(week(now()) = 1, 52, week(now()) - 1) and year(created) = if(week(now()) = 1, year(now()) - 1, year(now())) 
                            group by player_id 
                            order by sum(amount) DESC limit 5");
                        foreach($rs->result() as $i => $winner)
                        {
                            $player = $this->admin_model->getPlayer($winner->player_id, true);
                            $info['content'] = str_replace("<<FIRST_NAME>>", $player['first_name']);
                            $info['content'] = str_replace("<<LAST_INITIAL>>", substr($player['last_name'], 0, 1));
                            $info['content'] = str_replace("<<AMOUNT>>", $winner->amount);
                            
                            $temp = $this->processRequest($this->url . "notifications", json_encode($info) , "POST");  
                            $ret[$i]['job'] = $row;
                            $ret[$i]['result'] = $temp;
                        }
                    }
                }
                else
                {
                    $temp = $this->processRequest($this->url . "notifications", json_encode($info) , "POST");  
                    $ret[$index]['job'] = $row;
                    $ret[$index]['result'] = $temp;
                }                
            }
            //$this->updateDB();
            $ret['success'] = true;
            return $ret;
        }
        
        public function updateDB()
        {
            $ret = array();
            $devices = array('iOS', 'Android', 'Amazon', 'Windows Phone');
            
            $notifications = $this->getNotifications();
            $ret['notification_count'] = count($notifications);
            
            if(isset($notifications['notifications']))
            {
                $insert = "Insert into notifications.history (id,successful,failed,converted,remaining,queued_at,contents,headings,segments,player_ids) values  ";
                $insert_update = " On duplicate key update successful=values(successful),failed=values(failed),converted=values(converted),remaining=values(remaining),queued_at=values(queued_at),contents=values(contents),headings=values(headings),segments=values(segments),player_ids=values(player_ids)";
                foreach($notifications['notifications'] as $notification)
                {
                    $insert .= sprintf("('%s',%d,%d,%d,%d,'%s','%s','%s','%s','%s'),", $this->cf($notification['id']), $notification['successful'], $notification['failed'], $notification['converted'], $notification['remaining'], date("Y-m-d H:i:s", $notification['queued_at']),
                            $this->cf($notification['contents']), $this->cf($notification['headings']), $this->cf(isset($notification['include_segments']) ? $notification['include_segments'] : ''), $this->cf(isset($notification['include_player_ids']) ? $notification['include_player_ids'] : ''));
                }
                $this->db->query(trim($insert, ",") . $insert_update);
                //print trim($insert, ",") . $insert_update; die();
            }
            
            $offset = 0;
            do {
                $players = $this->getPlayers($offset);
                $ret['player_count'] = count($players);

                if(isset($players['players']))
                {
                    $insert = "Insert into notifications.players (id,identifier,session_count,language,timezone,game_version,device_os,device_type,device_model,facebook_id,tags,last_active,created,badge_count) values";
                    $insert_update = " on duplicate key update identifier=values(identifier),session_count=values(session_count),language=values(language),timezone=values(timezone),game_version=values(game_version),device_os=values(device_os),
                        device_type=values(device_type),device_model=values(device_model),facebook_id=values(facebook_id),tags=values(tags),last_active=values(last_active),created=values(created),badge_count=values(badge_count)";
                    foreach($players['players'] as $player)
                    {
                        $insert .= sprintf("('%s','%s',%d,'%s',%d,'%s','%s','%s','%s',%d,'%s','%s','%s',%d),", $this->cf($player['id']), $this->cf($player['identifier']), $player['session_count'], $player['language'], $player['timezone'], $this->cf($player['game_version']),
                                $this->cf($player['device_os']), $devices[$player['device_type']], $this->cf($player['device_model']), $this->cf($player['facebook_id']), $this->cf($player['tags']), date("Y-m-d H:i:s", $player['last_active']),
                                date("Y-m-d H:i:s", $player['created_at']), $player['badge_count']);
                    }
                    $this->db->query(trim($insert, ",") . $insert_update);
                    //print trim($insert, ",") . $insert_update; die();
                }
                $offset += 50;
            } while(isset($players['players']) && count($players['players']));
            
            //Update the player_ids
            $rs = $this->db->query("Select * from notifications.players where tags like '%\"id\"%'");
            foreach($rs->result() as $row)
            {
                $tags = json_decode($row->tags, true);
                if(isset($tags['id']))
                {
                    $this->db->where("id", $row->id);
                    $this->db->update("notifications.players", array("player_id" => $tags['id']));
                }
            }
            $ret['success'] = true;
            return $ret;
        }
        
        private function cf($val)
        {
            if(is_array($val) && !count($val))
                return "";
            elseif(is_array($val) && count($val))
                return str_replace("'", "''", json_encode($val));
            return str_replace("'", "''", $val);
        }
}

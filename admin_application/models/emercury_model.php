<?php 

class emercury_model extends CI_Model
{        
        private $base_template;
        private $data;
        
        public $xml;
        public $error;
        public $message;
        public $return_data;
        
        //ALL FUNCTIONS TO DO API CALLS
        function __construct()
        {
            parent::__construct();
            $this->db = $this->load->database ('admin', true);
            $this->data = array(
                'method' => "", 
                'user' => array(
                    "@attributes" => array('mail' => "janitor@kizzang.com", 'API_key' => "79541da9bd386480736082108f6083b6")
                )
            );
                        
        }
        
        private function processRequest()
        {
            $xml = Array2XML::createXML("request", $this->data);
            
            $ch = curl_init("https://panel.v5.emercurymail.net/api.php");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array('request' => $xml->saveXML()));
            $data = curl_exec($ch);
            $this->xml = simplexml_load_string($data);
            $json = json_encode($this->xml);
            $temp = json_decode($json,TRUE);
            
            $this->message = "";
            $this->error = "";
            $this->return_data = array();
            
            if(is_array($temp))            
                return $temp;                  
            else            
                $this->error = "Problem with getting information back from API";
            
            return false;
        }
        
        public function updateDB()
        {            
            $ret = array();
            $categories = $this->getCategories();
            if($categories && isset($categories['categorys']))
            {
                $ret['category_count'] = count($categories['categorys']);
                $recs = $categories['categorys']['category'];
                $insert = "Insert into marketing.categories (id, name) values ";
                $insert_update = " ON DUPLICATE KEY UPDATE name=values(name)";
                foreach($recs as $index => $rec)
                {
                    $insert .= sprintf("(%d,'%s')", $rec['id'], $this->cf($rec['name']));
                    $insert .= ($index == count($recs) - 1) ? "" : ",";
                }
                
                $this->db->query($insert . $insert_update);
                //print $insert . $insert_update;
            }
            
            $campaigns = $this->getCampaignsInformation();
            //print_r($campaigns); die();
            $campaign_audiences = array();
            if($campaigns && isset($campaigns['campaigns']))
            {            
                $ret['campaign_count'] = count($campaigns['campaigns']);
                $insert = "Insert into marketing.campaigns (id, campaign_name, from_name, from_email, reply_to, subject, message_type, cat_id, delivery_reminder, 
                    permission_reminder, track_customer_activity, ff_link, ff_link_text, active_campaign, deleted) values ";
                $insert_update = " ON DUPLICATE KEY UPDATE campaign_name=values(campaign_name), from_name=values(from_name), from_email=values(from_email), reply_to=values(reply_to),
                    subject=values(subject), message_type=values(message_type), cat_id=values(cat_id), delivery_reminder=values(delivery_reminder), permission_reminder=values(permission_reminder),
                    track_customer_activity=values(track_customer_activity), ff_link=values(ff_link), ff_link_text=values(ff_link_text), active_campaign=values(active_campaign), deleted=values(deleted)";
                if(isset($campaigns['campaigns']['campaign']['campaign_id']))
                    $campaign_list[] = $campaigns['campaigns']['campaign'];
                else
                    $campaign_list = $campaigns['campaigns']['campaign'];
                foreach($campaign_list as $index => $campaign)
                {
                    $insert .= sprintf("(%d,'%s','%s','%s','%s','%s','%s',%d,%d,%d,%d,%d,'%s',%d,%d)", $campaign['campaign_id'], $this->cf($campaign['campaign_name']), $this->cf($campaign['from_name']), $this->cf($campaign['from_email'])
                            , $this->cf($campaign['reply_to']), $this->cf($campaign['subject']), $this->cf($campaign['message_type']), $this->cf($campaign['cat_id']), $this->cf($campaign['delivery_reminder'])
                            , $this->cf($campaign['permission_reminder']), $this->cf($campaign['track_customer_activity']), $this->cf($campaign['ff_link']), $this->cf($campaign['ff_link_text']), $this->cf($campaign['active_campaign'])
                            , $this->cf($campaign['deleted']));
                    $insert .= ($index == count($campaign_list) - 1) ? "" : ",";
                    if(isset($campaign['list_id']) && $campaign['list_id'])
                    {
                        if(strstr($campaign['list_id'], ","))
                        {
                            $temp = explode(",", $campaign['list_id']);
                            foreach($temp as $value)
                                $campaign_audiences[] = array('campaign_id' => $campaign['campaign_id'], 'audience_id' => $value);
                        }
                        else
                        {
                            $campaign_audiences[] = array('campaign_id' => $campaign['campaign_id'], 'audience_id' => $campaign['list_id']);
                        }
                    }
                }
                $this->db->query($insert . $insert_update);
                //print $insert . $insert_update; die();
                if($campaign_audiences)
                {
                    $insert = "Insert IGNORE into marketing.campaign_audiences (campaign_id, audience_id) values ";
                    foreach($campaign_audiences as $row)
                        $insert .= sprintf("(%d,%d),", $row['campaign_id'], $row['audience_id']);
                    $this->db->query(trim($insert, ","));
                    //print $insert; die();
                }                
            }       
            
            $audiences = $this->getAudiences();
            if($audiences && isset($audiences['audiences']))
            {
                $ret['audience_count'] = count($audiences['audiences']);
                $recs = $audiences['audiences']['audience'];
                $insert = "Insert into marketing.audiences (id, name, size, subscribers_count, status) values ";
                $insert_update = " ON DUPLICATE KEY UPDATE name=values(name), size=values(size), subscribers_count=values(subscribers_count), status=values(status)";
                foreach($recs as $index => $rec)
                {
                    $insert .= sprintf("(%d,'%s',%d,%d,%d)", $rec['id'], $this->cf($rec['name']), $rec['size'], $rec['subscribers_count'], $rec['status']);
                    $insert .= ($index == count($recs) - 1) ? "" : ",";
                }
                
                $this->db->query($insert . $insert_update);
                //print $insert . $insert_update;
            }         
            
            $fields = $this->getCustomFields();            
            //print_r($fields); die();
            if($fields && isset($fields['Fields']))
            {
                $recs = $fields['Fields']['Field'];
                $ret['custom_field_count'] = count($recs);
                $insert = "Insert into marketing.custom_fields (id, name) values ";
                $insert_update = " ON DUPLICATE KEY UPDATE name=values(name)";
                foreach($recs as $index => $rec)
                {
                    $insert .= sprintf("(%d,'%s')", $rec['db_field_id'], $this->cf($rec['name']));
                    $insert .= ($index == count($recs) - 1) ? "" : ",";
                }
                
                $this->db->query($insert . $insert_update);
                //print $insert . $insert_update;
            }
            
            $rs = $this->db->query("Select * from marketing.audiences");
            if($rs->num_rows())
            {
                $insert = "Insert into marketing.subscribers (audience_id, email, first_name, last_name, city, state, email_hash, optin_date, optin_ip, optin_website) values ";
                $insert_update = " ON DUPLICATE KEY UPDATE first_name=values(first_name), last_name=values(last_name), city=values(city), state=values(state), email_hash=values(email_hash),optin_date=values(optin_date),
                    optin_ip=values(optin_ip),optin_website=values(optin_website)";
                foreach($rs->result() as $audience)
                {
                    $subscribers = $this->getSubscribers($audience->id);
                    if(isset($subscribers['subscribers']))
                    {
                        if(!isset($ret['subscriber_count']))
                            $ret['subscriber_count'] = count($subscribers['subscribers']);
                        else
                            $ret['subscriber_count'] += count($subscribers['subscribers']);
                        
                        foreach($subscribers['subscribers']['subscriber'] as $index => $person)
                        {
                            $insert .= sprintf("(%d,'%s','%s','%s','%s','%s','%s','%s','%s','%s'),", $audience->id, $this->cf($person['email']), $this->cf($person['first_name']), $this->cf($person['last_name']), $this->cf($person['city']),
                                    $this->cf($person['state']), $this->cf(md5($person['email'])), $this->cf($person['optin_date']), $this->cf($person['optin_ip']), $this->cf($person['optin_website']));                            
                        }
                    }
                }
                $this->db->query(trim($insert, ",") . $insert_update);
                //print trim($insert, ",") . $insert_update;
            }             
            
            $testers = $this->getTestSubscribers();
            if($testers && isset($testers['subscribers']))
            {
                $recs = $testers['subscribers']['subscriber'];
                $insert = "Insert into marketing.test_subscribers (id, email) values ";
                $insert_update = " ON DUPLICATE KEY UPDATE email=values(email)";
                foreach($recs as $index => $rec)
                {
                    $insert .= sprintf("(%d,'%s')", $rec['id'], $this->cf($rec['email']));
                    $insert .= ($index == count($recs) - 1) ? "" : ",";
                }
                
                $this->db->query($insert . $insert_update);
                //print $insert . $insert_update;
            }
            
            
            $rs = $this->db->query("Select * from marketing.campaigns");
            if($rs->num_rows())
            {
                foreach($rs->result() as $row)
                {
                    $preview = $this->getPreview($row->id);
                    if(isset($preview['preview']))
                    {
                        $query = sprintf("Update marketing.campaigns set html_body = '%s' where id = %d", $this->cf($preview['preview']), $row->id);                    
                        $this->db->query($query);
                    }
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
                return str_replace("'", "''", implode (",", $val));
            return str_replace("'", "''", $val);
        }
        
        public function getAudiences()
        {
            $this->data['method'] = "GetAudiences";
            return $this->processRequest();
        }
                
        public function getSubscribers($audience_id)
        {
            $this->data['method'] = "GetSubscribers";
            $this->data['parameters'] = array("audience_id" => $audience_id);
            return $this->processRequest();
        }
        
        public function updateSubscribers($audience_id, $subscribers)
        {
            $this->data['method'] = "UpdateSubscribers";
            $template = array(
                "email" => "",
                "optin_date" => "",
                "optin_ip" => "",
                "optin_website" => "",
                "first_name" => "",
                "last_name" => "",
                "city" => "",
                "state" => ""
            );
            
            $this->data['method'] = "UpdateSubscribers";
            $this->data['parameters']['audience_id'] = $audience_id;
            foreach($subscribers as $subscriber)
                $this->data['parameters']['subscriber'][] = array_merge ($template, $subscriber);
            
            return $this->processRequest();
        }
        
        public function addAudience($name)
        {
            $this->data['method'] = "addAudiences";
            $this->data['parameters']['audience_name']['name'] = $name;
            return $this->processRequest();
        }
        
        public function deleteSubscribers($audience_id, $emails)
        {
            $this->data['method'] = "deleteSubscriber";
            $this->data['parameters']['audience_id'] = $audience_id;
            foreach($emails as $email)
                $this->data['parameters'][]['subscribers']['email'] = $email;
            return $this->processRequest();
        }
        
        public function getCustomFields()
        {
            $this->data['method'] = "getCustomFields";
            return $this->processRequest();
        }
        
        public function getCategories()
        {
            $this->data['method'] = "getCategories";
            return $this->processRequest();
        }
        
        public function getPreview($campaign_id)
        {
            $this->data['method'] = "getPreview";
            $this->data['parameters']['campaign_id'] = $campaign_id;
            return $this->processRequest();
        }
        
        public function campaignSendTest($campaign_id, $user_id)
        {
            $this->data['method'] = "campaignSendTest";
            $this->data['parameters']['campaign_id'] = $campaign_id;
            $this->data['parameters']['send_test_subscriber']['id'] = $user_id;
            return $this->processRequest();
        }
        
        public function getTestSubscribers()
        {
            $this->data['method'] = "getTestSubscribers";
            return $this->processRequest();
        }
        
        public function getCampaignsInformation($campaign_id = NULL)
        {
            $this->data['method'] = "getCampaignsInformation";
            if($campaign_id)
                $this->data['parameters']['campaign_id'] = $campaign_id;
            return $this->processRequest();
        }
        
        public function getUnsubscribed($campaign_id = NULL)
        {
            $this->data['method'] = "getUnsubscribed";
            if($campaign_id)
                $this->data['paramenters']['campaign_id'] = $campaign_id;
            return $this->processRequest();
        }
        
        public function getComplaints($campaign_id = NULL)
        {
            $this->data['method'] = "getComplaints";
            if($campaign_id)
                $this->data['paramenters']['campaign_id'] = $campaign_id;
            return $this->processRequest();
        }
        
        public function addCampaign($data)
        {
            $template = array('audiences' => array(),
                'suppressions' => array(),
                'campaign_name' => '',
                'from_name' => '',
                'from_email' => '',
                'reply_to' => '',
                'html_body' => '',
                'message_type' => 'html',
                'subject' => '',
                'cat_id' => '',
                'is_wz' => 1,
                'delivery_reminder' => 'On',
                'permission_reminder' => 'On',
                'track_customer_activity' => 'Off',
                'ff_link' => 'Off',
                'ff_link_text' => '',
                'send_now' => false,
                'schedule_date' => '',
                'schedule_time' => '');
            
            $this->data['method'] = "addCampaign";
            $this->data['parameters'] = array_merge($template, $data);
            return $this->processRequest();
        }
        
        public function sendCampaign($campaign_id)
        {
            $this->data['method'] = "sendCampaign";
            $this->data['parameters']['campaign_id'] = $campaign_id;
            return $this->processRequest();
        }
        
        public function addSchedule($campaign_id, $date, $time)
        {
            $this->data['method'] = "addSchedule";
            $this->data['parameters']['campaign_id'] = $campaign_id;
            $this->data['parameters']['schedule_date'] = $date;
            $this->data['parameters']['schedule_time'] = $time;
            return $this->processRequest();
        }        
}

<?php 
use Aws\Common\Aws;
class marketing_model extends CI_Model
{
        private $base_config = array();
        
        function __construct()
        {
            parent::__construct();
            $this->db = $this->load->database ('admin', true);
        }
        
        
        public function getAll()
        {
            $rs = $this->db->query("Select mc.*, count(mce.marketing_email_id) as cnt 
                from MarketingCampaigns mc
                Left join MarketingCampaignEmails mce on mce.marketing_campaign_id = mc.id
                Group by mc.id");
            return $rs->result();
        }
        
        public function get($id)
        {
            //$assigned = $emails = array();
            $rs = $this->db->query("Select * from MarketingCampaigns where id = ?", array($id));            
            $config = $rs->row();
            
            $rs = $this->db->query("Select me.* 
                from MarketingEmails me
                Inner join MarketingCampaignEmails mce on mce.marketing_email_id = me.id and mce.marketing_campaign_id = ?", array($id));
            $emails = $rs->result();
            
            $rs = $this->db->query("Select sum(if(processed = 1, 1, 0)) as sent, sum(if(processed = 1, 0, 1)) as `left`, sum(if(opened = 1, 1, 0)) as opened, (sum(if(opened = 1, 1, 0)) / sum(if(processed = 1, 1, 0))) * 100 as opened_percent
                From MarketingCampaignEmails 
                Where marketing_campaign_id = ?", array($id));
            $stats = $rs->row();
            return compact('config','emails','stats');
        }
        
        public function sendCampaign($id)
        {
            $monikers = array('[FIRST_NAME]' => 'first_name', '[LAST_NAME]' => 'last_name', '[OPTOUT_KEY]' => 'email_hash');
            $this->load->model('admin_model');
            
            $rs = $this->db->query("Select * from MarketingCampaigns where id = ?", array($id));
            if(!$rs->num_rows())
                return json_encode (array('success' => false, 'message' => 'Invalid Marketing Campaign'));
            $config = $rs->row();
            
            $rs = $this->db->query("Select m.* from MarketingEmails m
                Inner join MarketingCampaignEmails mc on mc.marketing_email_id = m.id
                Where marketing_campaign_id = ? and processed = 0", array($id));
            
            $count = $rs->num_rows();
            foreach($rs->result() as $index => $email)
            {
                $body = $config->body;
                foreach($monikers as $key => $value)
                    $body = str_replace ($key, $email->$value, $body);
                
                $this->admin_model->sendGenericEmail($email->email, $config->subject, $body, $config->from_address);
                if($index && $index % 10 == 0)
                    sleep (1);
                
                $this->db->query("Update MarketingCampaignEmails set processed = 1 where marketing_campaign_id = ? and marketing_email_id = ?", array($id, $email->id));
            }
            return json_encode(array('success' => true, 'message' => $count . " Emails Processed"));
        }
        
        public function getEmailList()
        {
            $rs = $this->db->query("Select * from PlayerSearch ps
                Inner join Users p on ps.id = p.id and accountStatus = 'Active' where information not like '%kizzang%' and ps.id in (Select distinct playerId from PlayerLogins where created > now() - INTERVAL 1 MONTH)");
            foreach($rs->result() as $row)
            {
                $info = json_decode($row->information, true);
                if(is_numeric(trim($info['email'])) || !trim($info['email']))
                    continue;
                
                print $info['first_name'] . "\t" . $info['last_name'] . "\t\t\t" . $info['cellphone'] . "\t" . $info['email'] . "\n";
            }
            die();
        }
        
        public function get365()
        {
            $rs = $this->db->query("Select * from PlayerSearch p
                Inner join (Select playerId, endPosition from Positions where id in (Select id from (Select playerId, max(id) as id from Positions group by playerId) a) and endPosition + 225 > 365) b on b.playerId = p.id
                Where information not like '%kizzang%' and information not like '%mailinator%';");
            foreach($rs->result() as $row)
            {
                $info = json_decode($row->information, true);
                print $info['first_name'] . " " . $info['last_name'] . "\t" .  $info['email'] . "\t" . $row->endPosition . "\n";
            }
        }
        
        public function addEmails($file, $id)
        {
            $fp = fopen($file, "r");
            $ids = array();
            while($line = fgets($fp))
            {
                $line_array = explode("\t", $line);
                //print $line;
                if(count($line_array) == 6) //First Name, Last Name, DOB, Address, Phone, Email
                {
                    $rec = array('first_name' => trim($line_array[0]), 'last_name' => trim($line_array[1]), 'birth_date' => $line_array[2],
                        'address' => $line_array[3], 'phone' => $line_array[4], 'email' => trim($line_array[5]), 'email_hash' => md5(strtolower(trim($line_array[5]))));
                    if($this->db->insert('MarketingEmails', $rec))
                    {                        
                        $ids[] = $this->db->insert_id();
                    }
                    else // Look for the record in the DB
                    {
                        $rs = $this->db->query("Select * from MarketingEmails where email_hash = ?", array(md5(strtolower($line_array[5]))));
                        if($rs->num_rows())
                            $ids[] = $rs->row()->id;
                    }
                }
            }
            
            if($ids)
            {
                foreach($ids as $marketing_email_id)
                {
                    $rec = array('marketing_campaign_id' => $id, 'marketing_email_id' => $marketing_email_id, 'processed' => 0);
                    $this->db->insert("MarketingCampaignEmails", $rec);
                }
            }
            
            return count($ids) . " Added to the DB";
        }
        
        public function addCampaign($data)
        {
            if(isset($data['id']))
            {
                $this->db->where('id', $data['id']);
                $this->db->update("MarketingCampaigns", $data);
            }
            else
            {
                $this->db->insert("MarketingCampaigns", $data);
            }
            admin_model::addAudit($this->db->last_query(), "marketing_model", "addCampaign");
            return true;
        }
        
        public function processEmails($ids, $recs)
        {
            $insert_ids = array();
            foreach($recs as $rec)
            {
                $this->db->insert("MarketingEmails", $rec);
                $insert_ids[] = $this->db->insert_id();
            }
            
            if($ids)
            {
                foreach($ids as $id)
                    foreach($insert_ids as $insert_id)
                        $this->db->insert("MarketingCampaignEmails", array("marketing_email_id" => $insert_id, "marketing_campaign_id" => $id));
            }
            return true;
        }
        
        public function addCampaignEmails($data)
        {
            $this->db->where("marketing_campaign_id", $data['id']);
            $this->db->delete("MarketingCampaignEmails");
            
            foreach($data['assigned'] as $id)
                $this->db->insert("MarketingCampaignEmails", array("marketing_email_id" => $id, "marketing_campaign_id" => $data['id']));
            
            admin_model::addAudit($this->db->last_query(), "marketing_model", "addCampaignEmails");
            return true;
        }
}

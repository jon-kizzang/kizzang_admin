<?php 

class lottery_model extends CI_Model
{        
        function __construct()
        {
            parent::__construct();
            $this->db = $this->load->database ('admin', true);
        }
        
        public function getAnswers($id)
        {
            $rs = $this->db->query("Select * from LotteryConfigs where id = ?", array($id));
            $config = $rs->row();

            $answers = array();
            if($config->answerHash)
                $answers = explode (",", $config->answerHash);

            return compact('config','answers');
        }
        
        public function gradeLotteryAnswers($id)
        {
            $rs = $this->db->query("Select * from LotteryConfigs where id = ? and endDate < convert_tz(now(), 'GMT', 'US/Pacific')", array($id));
            if(!$rs->num_rows())
                return json_encode(array('success' => false, 'message' => 'Invalid Card'));
            
            $config = $rs->row();
            $array = explode(",", $config->answerHash);
            if(count($array) != $config->numAnswerBalls)
                return json_encode(array('success' => false, 'message' => 'numAnswerBalls does not match up to items in answerHash'));
            
            $rs = $this->db->query("Select id, playerId, answerHash from LotteryCards where lotteryConfigId = ?", array($id));
            foreach($rs->result() as $row)
            {
                $player = explode(",", $row->answerHash);
                $temp = array_intersect($array, $player);
                $this->db->query("Update LotteryCards set correctAnswers = ? where id = ?", array(count($temp), $row->id));
            }
            return json_encode(array('success' => true, 'message' => 'Cards Graded'));
        }        
        
        public function updateAnswers($data)
        {
            $rec['answerHash'] = implode(",", $data['answers']);
            $this->db->where("id", $data['id']);
            return $this->db->update("LotteryConfigs", $rec);
        }
        
        public function addTest($num, $id)
        {
            $rs = $this->db->query("Select * from LotteryConfigs where id = ?", array($id));
            if(!$rs->num_rows())
                return false;
            
            $config = $rs->row();
            $recs = array();
            
            for($i = 0; $i < $num; $i++)
            {
                $nums = array();
                $j = 0;
                while($j < $config->numAnswerBalls)
                {
                    $rand = rand(1, $config->numTotalBalls);
                    if(!in_array($rand, $nums))                    
                        $nums[$j++] = $rand;                    
                }
                $recs[] = array('playerId' => 107, 'lotteryConfigId' => $id, 'answerHash' => implode(",", $nums));
                if(count($recs) && !(count($recs) % 10000))
                {
                    $this->db->insert_batch('LotteryCards', $recs);                    
                    $recs = array();
                }
            }
            
            if(count($recs))
                $this->db->insert_batch('LotteryCards', $recs);
        }
        
        public function add($data)
        {
            $errors = array();
            foreach($data as $key => $value)
            {
                switch($key)
                {
                    case 'numTotalBalls':
                    case 'numAnswerBalls':
                    case 'numCards':
                    case 'id':
                        if(!$value || !is_numeric($value))
                            $errors[$key] = "Invalid Value";
                        break;
                        
                    case 'startDate':
                    case 'endDate':
                        if(!strtotime($value))
                            $errors[$key] = "Invalid Date Format";
                        break;
                }
            }
            
            if(count($errors))
                return json_encode (array('success' => 0, 'errors' => $errors));
            
            if(isset($data['id']))
            {
                $this->db->where('id', $data['id']);
                $this->db->update('LotteryConfigs', $data);                
            }
            else
            {
                $this->db->insert('LotteryConfigs', $data);
                $data['id'] = $this->db->insert_id();
            }
            return json_encode(array('success' => 1, 'data' => $data));
        }
        
        public function getConfigs($dates)
        {
            $rs = $this->db->query("Select * from LotteryConfigs where 
                (abs(datediff(startDate, ?)) < abs(datediff(?, ?))) or (abs(datediff(endDate, ?)) < abs(datediff(?, ?))) or ? < endDate or ? < startDate", 
                array($dates['startDate'], $dates['startDate'], $dates['endDate'], $dates['endDate'], $dates['startDate'], $dates['endDate'], $dates['endDate'], $dates['startDate']));

            $dates['configs'] = $rs->result();
            return $dates;
        }
        
        public function getConfig($id)
        {
            $config = NULL;
            $cardLimits = array('Per Day','Per Game');
            if($id)
            {
                $rs = $this->db->query("Select l.*, count(c.id) as cnt from LotteryConfigs l 
                    Left join LotteryCards c on c.lotteryConfigId = l.id
                    where l.id = ? group by l.id", array($id));
                $config = $rs->row();
                $config->serialNumber = sprintf("KL%05d", $config->id);
            }
            
            $rules = array();
            $rs = $this->db->query("Select DISTINCT ruleURL from GameRules where gameType = 'Lottery' AND serialNumber = 'TEMPLATE'");
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
            return compact('config','cardLimits','rules','rule');
        }
}

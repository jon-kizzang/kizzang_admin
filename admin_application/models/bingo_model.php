<?php 
class XmlElement {
  var $name;
  var $attributes;
  var $content;
  var $children;
};

class bingo_model extends CI_Model
{        
    function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database ('admin', true);
    }
    
    public function runGames()
    {
        //Find one within the last +-init5 minutes
        $rs = $this->db->query("Select * from BingoGames where status = 'Pending' and startTime between convert_tz(now(), 'GMT', 'US/Pacific') - INTERVAL 5 MINUTE and convert_tz(now(), 'GMT', 'US/Pacific') + INTERVAL 5 MINUTE");
        
        if(!$rs->num_rows())
            return array('status' => 'Success', 'message' => 'No Active Cards');
        
        $game = $rs->row();
        
        exec("php " . BASEPATH . "../www/index.php crons run_bingo_game " . $game->id);
    }
    
    public function runGame($id)
    {
        set_time_limit(0);
        ob_implicit_flush();
        $rs = $this->db->query("Select * from BingoGames where id = ?", array($id));
        $game = $rs->row();
        $numbers = json_decode($game->cardNumbersPicked);
        $index = 0;
        $status = $game->status;
        $address = gethostbyname('devbingo.kizzang.com');
        $port = 4000;
        $error = "";
                
        //$address = '127.0.0.1';

        /* Create a TCP/IP socket. */
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false)
            $error .= "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
        
        echo "Attempting to connect to '$address' on port '$port'...";
        $result = socket_connect($socket, $address, $port);
        if ($result === false)
            $error .= "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
                        
        if($status != 'Pending')
            $error .= "Invalid state for game: " . $status;
        
        if($error)
            return $error;
        
        $this->db->where("id", $id);
        $this->db->update("BingoGames", array('status' => 'Active'));
        $status = 'Active';
        $type = 'BallCalled';
        
        while($index < $game->maxNumber)
        {
            $rs = $this->db->query("Select status from BingoGames where id = ?", array($id));
            $status = $rs->row()->status;
            
            if($status == 'Complete')
                break;
            
            if($status == 'Active')
            {
                $currentBall = $numbers[$index];
                $calledBalls = array_slice($numbers, 0, $index + 1);
                $msg = json_encode(compact('type','status','currentBall','calledBalls'));
                print $msg;
                socket_write($socket, $msg, strlen($msg));
                $index++;
            }
            sleep($game->callTime);
            $this->db->where("id", $id);
            $this->db->update("BingoGames", array('currentNum' => ($index + 1)));
        }
        socket_close($socket);
        $this->db->where("id", $id);
        $this->db->update("BingoGames", array('status' => 'Complete'));
        
        $this->gradeCards($id);
        return true;
    }
    
    private function gradeCards($id)
    {
        //Now grade the cards
        $rs = $this->db->query("Select * from BingoGames where id = ?", array($id));
        $game = $rs->row();
        $game->nums = array_splice(json_decode($game->cardNumbersPicked), $game->currentNum + 1);
        
        $rs = $this->db->query("Select * from BingoCards where bingoGameId = ?", array($id));
        $cards = $rs->result();
        
        foreach($cards as $card)
        {
            $numberHits = count(array_intersect($game->nums, json_decode($card->cardNumbers)));
            $this->db->query("Update BingoCards set numberHits = ? where bingoGameId = ? and playerId = ?", array($numberHits, $card->bingoGameId, $card->playerId));
        }
    }
    
    public function viewCards($id)
    {
        $this->gradeCards($id);
        $rs = $this->db->query("Select * from BingoGames where id = ?", array($id));
        $game = $rs->row();
        
        $rs = $this->db->query("Select b.*, p.firstName, p.lastName from BingoCards b
            Inner join Users p on p.id = b.playerId
            where bingoGameId = ? order by numberHits DESC limit 10", array($id));
        $cards = $rs->result();
        
        $views = array();
        
        $nums = array_splice(json_decode($game->cardNumbersPicked), $game->currentNum + 1);
        foreach($cards as $card)
        {
            $temp = json_decode($card->cardNumbers);

            $adjust = 0;
            $views[$card->playerId]['player'] = $card;
            foreach($temp as $index => $num)
            {
                if(in_array($num, $nums))
                    $class = 'marked';
                else
                    $class = 'unmarked';
                
                if(floor(($index) / 5) == 2 && ($index) % 5 == 2)
                {
                    $div76 = in_array(76, $nums) ? 'marked' : 'unmarked';
                    $div77 = in_array(77, $nums) ? 'marked' : 'unmarked';
                    $views[$card->playerId]['card'][($index + $adjust) % 5] .= "<td><div class='ctr-left $div76'>76</div><div class='ctr-right $div77'>77</div></td>";
                    $adjust = -1;                    
                }                                
                
                if(floor(($index + 1) / 5) == 5)
                    break;
                
                if(!floor($index / 5))
                    $views[$card->playerId]['card'][($index + $adjust) % 5] = "<tr><td class='$class'>" . $num . "</td>";
                elseif(floor(($index + 1) / 5) == 4)
                    $views[$card->playerId]['card'][($index + $adjust) % 5] .= "<td class='$class'>" . $num . "</td></tr>";
                elseif($adjust < 0)
                    $views[$card->playerId]['card'][(($index + 1) % 5)] .= "<td class='$class'>" . $num . "</td>";
                else
                    $views[$card->playerId]['card'][($index + $adjust) % 5] .= "<td class='$class'>" . $num . "</td>";
            }
            //print_r($views); die();
        }
        return compact('views','game');
    }

    public function addGames($numMinutes = 60, $maxPicks = 45, $callTime = 10)
    {
        $rs = $this->db->query("Select * from BingoGames order by endTime DESC limit 1");
        
        if(!$rs->num_rows())
        {
            $date = date('Y-m-d H:00:00', strtotime("+1 Hour"));
            $template = array('startTime' => $date, 'endTime' => date("Y-m-d H:00:00", strtotime($date) + (60 * $numMinutes)), 'callTime' => $callTime, 'maxNumber' => $maxPicks, 'cardNumbersPicked' => json_encode($this->getNumbers($maxPicks)));
        }
        else
        {
            $row = $rs->row();
            $template = array('startTime' => $row->endTime, 'endTime' => date("Y-m-d H:00:00", strtotime($row->endTime) + (60 * $numMinutes)), 'callTime' => $callTime, 'maxNumber' => $maxPicks, 'cardNumbersPicked' => json_encode($this->getNumbers($maxPicks)));
            $date = $row->endTime;
        }
        
        $this->db->insert('BingoGames', $template);
        print $this->db->last_query();
        
        while(strtotime($date) < strtotime("+7 Days"))
        {
            $date = $template['endTime'];
            $template['startTime'] = $date;
            $template['endTime'] = date("Y-m-d H:00:00", strtotime($date) + ($numMinutes * 60));
            $template['cardNumbersPicked'] = json_encode($this->getNumbers($maxPicks));
            $this->db->insert('BingoGames', $template);
            print $this->db->last_query();
        }
    }
    
    private function getNumbers($maxNum)
    {
        $index = 0;
        $numbers = array();
        while($index < $maxNum)
        {
            $num = rand(1, 77);
            if(!in_array($num, $numbers))
            {
                $numbers[] = $num;
                $index++;
            }
        }
        return $numbers;
    }
    
    public function getBingoGames($startDate, $endDate)
    {
        $rs = $this->db->query("Select g.*, count(c.bingoGameId) as cnt from BingoGames g 
            Left join BingoCards c on g.id = c.bingoGameId
            where date(startTime) between ? and ? group by g.id", array($startDate, $endDate));
        $games = $rs->result();
        return compact('games','startDate','endDate');
    }

    public function getBingoGame($id = 0)
    {        
        $game = NULL;
        if($id)
        {
            $rs = $this->db->query("Select * from BingoGames where id = ?", array($id));
            $game = $rs->row();
        }
        $statuses = $this->admin_model->getColumnEnum("kizzang", "BingoGames", "status");
        return compact('game','statuses');
    }

    public function updateBingoGame($data, &$error)
    {
        //For Future Games, redo the numbers for the game
        if(strtotime($data['startTime']) > strtotime("now"))
        {
            $numbers = $this->getNumbers($data['maxNumber']);
            $data['cardNumbersPicked'] = json_encode($numbers);
        }
        
        if(isset($data['id']))
        {
            $this->db->where('id', $data['id']);
            $ret = $this->db->update('BingoGames', $data);
            if(!$ret)
                $error = $this->db->_error_message();
        }
        else
        {
            $ret = $this->db->insert('BingoGames', $data);
            if(!$ret)
                $error = $this->db->_error_message();
        }
        //print $this->db->last_query();
    }
}

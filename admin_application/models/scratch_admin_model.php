<?php 
/*
 * This class breaks out the adminstrative functions for the scratch_model.php
 * This php file should not be distributed to the production server since it allows for the creation and destruction of game data
 * 
 * */

// Define the administrative class as an extension of the basic scratch game
class scratch_admin_model extends CI_Model
{
	// Empties the Scratches table
	// Deletes everything in the Scratches and all related tables
	// Creates a new list of winning card ids
	// Returns true on success, false on failure
	public function createNewScratchesTable ($admin_token=null)
	{
		$success = false;
		
		// Error if the configuration file isn't present
		if (!$this->db->table_exists ('ScratchGames'))
		{
			echo "ERROR: Missing table ScratchGames." . PHP_EOL;
			return false;
		}
		
		// Create the tables that must exist if they dont exist
		//
		// CREATE SCRATCHLOGS
		$sql="CREATE TABLE IF NOT EXISTS `ScratchLogs` (`SerialNumber` varchar(64) NOT NULL, `Id` int(11) NOT NULL AUTO_INCREMENT, `TimeStamp` datetime NOT NULL, `Message` varchar(1024) NOT NULL,PRIMARY KEY (`Id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
		$this->db->query ($sql);
		//
		// CREATE SCRATCHPLAYS
		$sql="CREATE TABLE IF NOT EXISTS `ScratchPlays` (`SerialNumber` varchar(64) NOT NULL, `TimeStamp` datetime NOT NULL,`PlayerId` int(11) NOT NULL,`ScratchId` int(10) unsigned NOT NULL,UNIQUE KEY `index_ScratchPlays_SerialNumber_ScratchId` (`SerialNumber`,`ScratchId`), KEY `index_ScratchPlays_PlayerId_TimeStamp` (`PlayerId`,`TimeStamp`)) ENGINE=InnoDB DEFAULT CHARSET=latin1";
		$this->db->query ($sql);
		//
		// CREATE SCRATCHCARDS
		$sql="CREATE TABLE IF NOT EXISTS `ScratchCards` (`SerialNumber` varchar(64) NOT NULL,`ScratchId` int(4) unsigned NOT NULL,`Amount` decimal(10,2) NOT NULL,`CardNumber` int(4) unsigned NOT NULL,`Values` int(2) unsigned NOT NULL,KEY `index_ScratchCards_Amount` (`Amount`),KEY `ScratchId` (`ScratchId`)) ENGINE=InnoDB DEFAULT CHARSET=latin1";
		$this->db->query ($sql);
				
		//
		// Clear the MemCache entry for this game
		$memcache = createMemcached ();
		// Memcached key for the configuration of this scratch game
		$key = self::configurationKey ($this->serial_number);
		$result = null;		
		if ($memcache != null)
		{
			// Delete the memcache key
			$result = $memcache->delete($key); // Memcached object 
		}
		
		$configuration=$this->readConfiguration ($this->serial_number, true);
		$configuration['TotalCards']=0;
		$configuration['TotalWinningCards']=0;
		$this->writeConfiguration ($this->serial_number, $configuration);
		
		// Re-initialize now that the cache item is gone
		// This will force the read from the database file
		if (true == $this->init ($this->serial_number, $admin_token))
		{
			// Clear out the number of cards and winning cards for this serial number
			$data = array(
							'Card_Count' => 0,
					   		'Win_Count' => 0,
						 );
			$this->db->where ('SerialNumber', $this->serial_number);
			$result = $this->db->update (self::CONFIGURATION_TABLE_NAME, $data);
			
			// Delete any existing plays on this serial number
			$this->db->where('SerialNumber', $this->serial_number);
			$this->db->delete($this->tableName); 
	
			// Delete any existing cards for this serial number
			$this->db->where('SerialNumber', $this->serial_number);
			$this->db->delete($this->cardsTableName); 
					
			// Delete log entries for this serial number
			$this->db->where('SerialNumber', $this->serial_number);
			$this->db->delete(self::LOGS_TABLE_NAME); 
	

			// Log the result
	 		$success = $this->expandGame ($admin_token);
	 		if ($success)
	 		{
	 			$this->log ("Game created");	
	 		}
			else
			{
				$this->log ("Game failed to create");
			}
		}
		else
		{
			// Init failed
			echo "Could not initialize with serial number " . PHP_EOL;
			$success = false;	
		}
 		return $success;
	}
	
        public function getWinners()
        {
                $ret = array();
                $rs = $this->db->query ("Select SerialNumber, IF(WinConfirmed is NULL, 'No', 'Yes') as Confirmed, PlayerID, PrizeAmount, PrizeName, DATE_FORMAT(WinDate, '%a, %b %d, %Y %r') as WinDate from WinConfirmations order by WinDate DESC");
                foreach($rs->result() as $row)
                        $ret[] = $row;
                return $ret;
        }
        
        public function getGames()
        {
                $ret = array();
                $rs = $this->db->query ("Select g.*, max(ScratchID) as played
                    from Scratch_GPGames g
                    left join Scratch_GPPlays p on g.SerialNumber = p.SerialNumber
                    group by g.ID
                    order by Name");
                return $rs->result();
        }          
        
        public function getFutureWinners()
        {
            $averages = array();
            $maxes = array();
            $ret = array();
            $rs = $this->db->query("Select SerialNumber, avg(cnt) as num from (SELECT SerialNumber, date(TimeStamp) as date, count(*) cnt FROM ebdb.Scratch_GPPlays where TimeStamp > now() - INTERVAL 7 DAY group by SerialNumber, date(TimeStamp)) a group by SerialNumber;");
            foreach($rs->result() as $row)
                $averages[$row->SerialNumber] = $row;
            
            $rs = $this->db->query("Select SerialNumber, max(ScratchId) as num from Scratch_GPPlays group by SerialNumber");
            foreach($rs->result() as $row)
                $maxes[$row->SerialNumber] = $row;
            
            $rs = $this->db->query("Select * from Scratch_GPGames where now() between StartDate and EndDate order by SerialNumber");
            $games = $rs->result();
            
            $start = strtotime("tomorrow");
            $end = strtotime("+2 weeks");
            
            for($i = $start, $j = 0; $i <= $end, $j < 24; $i += (3600*24), $j++)
            {
                foreach($games as $index => $game)
                {
                    if(!isset($maxes[$game->SerialNumber]->num) || !isset($averages[$game->SerialNumber]->num))
                    {
                        unset($games[$index]);
                        continue;
                    }
                    $start_id = floor($maxes[$game->SerialNumber]->num + ($averages[$game->SerialNumber]->num * $j));
                    $end_id = floor($maxes[$game->SerialNumber]->num + ($averages[$game->SerialNumber]->num * ($j + 1)));
                    $rs = $this->db->query("Select * from Scratch_GPCards where ScratchID between ? and ? AND SerialNumber = ?", array($start_id, $end_id, $game->SerialNumber));
                    if($rs->num_rows())
                        $ret[date('Y-m-d', $i)][$game->SerialNumber] = $rs->result();
                    else
                        $ret[date('Y-m-d', $i)][$game->SerialNumber] = array();
                }
            }
            return compact('ret', 'games');
        }
        
        private function getColumnEnum($schema, $table, $column)
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

        public function getGame($id)
        {
                $game = "";
                $rule = NULL;
                $rules = NULL;
                $rs = $this->db->query("Select * from Scratch_GPGames where ID = ?", array($id));
                if($rs->num_rows())
                        $game = $rs->row();

                $rs = $this->db->query("Select * from Scratch_GPPayout where PayoutID = ? order by Rank", array($game->PayoutID));
                if($rs->num_rows())
                    $payouts = $rs->result();
                else
                    $payouts = array();
                
                $db = $this->load->database ('admin', true);
                $rs = $db->query("Select distinct ruleURL from GameRules where gameType = 'Scratchers' AND serialNumber = 'TEMPLATE'");
                $rules = $rs->result();
                
                $adPlacements = $this->getColumnEnum('ebdb','Scratch_GPGames','adPlacement');
                $cardTypes = $this->getColumnEnum('ebdb', 'Scratch_GPGames', 'CardType');
                    
                $rs = $db->query("Select * from GameRules where serialNumber = ? and gameType = 'Scratchers'", $game->SerialNumber);
                
                if($rs->num_rows())
                {
                    $rule = $rs->row();
                    $text = file_get_contents(str_replace("https://d23kds0bwk71uo.cloudfront.net", "https://kizzang-legal.s3.amazonaws.com",$rule->ruleURL));
                                        
                    $rule->text = $text;
                    if(count($rules))
                        $rule->template = file_get_contents (str_replace("https://d23kds0bwk71uo.cloudfront.net", "https://kizzang-legal.s3.amazonaws.com",$rules[0]->ruleURL));
                    else
                        $rule->template = "";

                }
                
                //$rs = $this->db->query("Select PayoutID, Rank, PrizeAmount, ");
                $cards = NULL;
                return compact('game', 'payouts','cards','rule', 'rules', 'adPlacements','cardTypes');
        }

        public function alterGame($game)
        {
                if(isset($game['ID']))
                {
                        $this->db->where('ID', $game['ID']);
                        unset($game['ID']);
                        $this->db->update('Scratch_GPGames', $game);
                        admin_model::addAudit($this->db->last_query(), "scratch_admin_model", "alterGame");
                        return 0;
                }
                else
                {
                        //Find max PayoutID and apply it to the record
                        $rs = $this->db->query("Select max(PayoutID) as PayoutID from Scratch_GPPayout");
                        $payout = $rs->row();
                        $game['PayoutID'] = $payout->PayoutID + 1;
                        $this->db->insert('Scratch_GPGames', $game);
                        admin_model::addAudit($this->db->last_query(), "scratch_admin_model", "alterGame");
                        return $this->db->insert_id();
                }
        }

        public function getFindWinCards ($id)
        {                
                return 0;
        }

        public function incrementCards($id, &$message)
        {
                $temp = $this->db->query("Select * from Scratch_GPGames where ID = ?", array($id));
                if(!$temp->num_rows())
                        return false;

                $game = $temp->row();

                $payouts= $this->db->query("Select * from Scratch_GPPayout where PayoutID = ? order by Rank", array($game->PayoutID));
                $new_winners = array();                

                //Create only winner records for the cards table
                foreach($payouts->result() as $payout)
                {
                        for($i = 0; $i < $payout->Count; $i++)
                        {
                                $temp = 0;
                                $j = 0;
                                do
                                {
                                        $temp = mt_rand($game->TotalCards + 1, $game->TotalCards + $game->CardIncrement);                                               
                                } while(array_key_exists($temp, $new_winners));
                                $new_winners[$temp]['PrizeAmount'] = $payout->PrizeAmount;
                                $new_winners[$temp]['PrizeRank'] = $payout->Rank;
                                $new_winners[$temp]['CardNumber'] = $temp;
                                $new_winners[$temp]['ScratchId'] = $temp;
                                $new_winners[$temp]['SerialNumber'] = $game->SerialNumber;
                                $value = 0;
                                $j = 0;
                                for($k= 1; $k <= $game->WinningSpots; $k++)
                                {
                                        $bit_value = 1;
                                        $bit = rand(0, $game->SpotsOnCard - 1);
                                        $bit_value = $bit_value << $bit;
                                        if($bit_value & $value)
                                                $k--;
                                        else
                                                $value = $value | $bit_value;
                                }
                                $new_winners[$temp]['Values'] = $value; 
                        }
                }

                try 
                {

                        $i = 0;
                        $query = "Insert into Scratch_GPCards (PrizeAmount, PrizeRank, CardNumber, ScratchId, SerialNumber, `Values`) values ";
                        if($new_winners)
                        {
                            foreach($new_winners as $winner)
                            {
                                    $query .= sprintf("('%s', '%d', '%d', '%d', '%s', '%s'),", $this->SQLClean($winner['PrizeAmount']), $this->SQLClean($winner['PrizeRank']), $this->SQLClean($winner['CardNumber']), $this->SQLClean($winner['ScratchId']),$this->SQLClean($winner['SerialNumber']),$this->SQLClean($winner['Values']));
                                    if($i && ($i % 5000 == 0))
                                    {                     
                                            $this->db->query(trim($query, ",") . ";");
                                            $query = "Insert into Scratch_GPCards (PrizeAmount, PrizeRank, CardNumber, ScratchId, SerialNumber, `Values`) values ";
                                    }
                                    $i++;
                            }   
                            $this->db->query(trim($query, ",") . ";");
                        }
                        
                } catch (Exception $ex) {
                        return false;
                }

                //Update the games table with the new card / winning card counts
                $game_update = array('TotalCards' => $game->TotalCards + $game->CardIncrement, 'TotalWinningCards' => $game->TotalWinningCards + $game->WinningCardIncrement);
                $this->db->where('ID', $game->ID);
                $this->db->update('Scratch_GPGames', $game_update);
                admin_model::addAudit($this->db->last_query(), "scratch_admin_model", "incrementCards");
                
                $token = $message = NULL;
                $this->load->model("admin_model");
                $this->admin_model->getAuthToken($token, $message);
                
                $url = getenv("APISERVER") . "api/scratchcards/killkey/" . $game->SerialNumber;
                $header = array(getenv("APIKEY"), "TOKEN:" . $token);
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $info = json_decode(curl_exec($ch), true);
                if(isset($info['error']))
                {
                    $message = "Problem with resetting memcache" . print_r($info, true);
                    return false;
                }
                return true;

        }
                
                private function SQLClean($val)
                {
                        return str_replace("'", "''", str_replace("\'", "'", $val));
                }
                
                public function delete($table_name, $id_name, $pk)
                {
                        $this->db->where($id_name, $pk);
                        return $this->db->delete($table_name);                        
                }
                
                public function alterPayout($game)
                {
                        //print_r($game); die();
                        if(isset($game['KeyID']))
                        {
                                $this->db->where('KeyID', $game['KeyID']);
                                $game['count'] = $game['Weight'];
                                unset($game['KeyID']);
                                $this->db->update('Scratch_GPPayout', $game);
                        }
                        else
                        {
                            $game['count'] = $game['Weight'];
                                $this->db->insert('Scratch_GPPayout', $game);
                        }
                        
                        //Need to update the game record when these are changed
                        $rs = $this->db->query("Select sum(count) as cnt, max(PrizeAmount) as PrizeAmount from Scratch_GPPayout where PayoutID = ?", array($game['PayoutID']));
                        $payout = $rs->row();
                        $rec = array('WinningCardIncrement' => $payout->cnt, 'WinAmount' => $payout->PrizeAmount);
                        $this->db->where('PayoutID', $game['PayoutID']);
                        $this->db->update('Scratch_GPGames', $rec);
                        admin_model::addAudit($this->db->last_query(), "scratch_admin_model", "alterPayout");
                }
                
                public function clonePayoutInsert($data)
                {
                    $rs = $this->db->query("Delete from Scratch_GPPayout where PayoutID = ?", array($data['cur_payout_id']));
                    $rs = $this->db->query("Insert into Scratch_GPPayout (PayoutID, Rank, PrizeAmount, PrizeName, TaxableAmount, Weight, Count, WinCount) 
                        Select ? as PayoutID, Rank, PrizeAmount, PrizeName, TaxableAmount, Weight, Count, WinCount from Scratch_GPPayout where PayoutID = ?", array($data['cur_payout_id'], $data['new_payout_id']));
                    $rs = $this->db->query("Select max(TaxableAmount) as amount, sum(Weight) as weight from Scratch_GPPayout where PayoutID = ?", array($data['new_payout_id']));
                    $counts = $rs->row();
                    
                    $this->db->where('PayoutID', $data['cur_payout_id']);
                    $this->db->update("Scratch_GPGames", array('WinningCardIncrement' => $counts->weight, 'WinAmount' => $counts->amount));
                    admin_model::addAudit($this->db->last_query(), "scratch_admin_model", "clonePayoutInsert");
                    return true;
                }
                
                public function clonePayout($id)
                {
                    $rs = $this->db->query("Select * from Scratch_GPGames where ID = ?", array($id));
                    if(!$rs->num_rows())
                        return false;
                    
                    $game = $rs->row();
                    
                    $rs = $this->db->query("Select concat(SerialNumber, ' - ', Name) as Name, PayoutID from Scratch_GPGames order by SerialNumber");
                    $payments = $rs->result();
                    
                    return compact('game', 'payments');
                }
                
                public function getPayoutInfo($id)
                {
                    $rs = $this->db->query("Select * from Scratch_GPPayout where PayoutID = ? order by Rank", array($id));
                    $payouts = $rs->result();
                    return compact('payouts');
                }
                
                public function getPayout($id, $payout_id)
                {
                        $game = "";
                        $payout = "";
                        $rs = $this->db->query("Select g.*, group_concat(p.Rank) as rank_array 
                                from Scratch_GPGames g 
                                Left Join Scratch_GPPayout p on p.PayoutID = g.PayoutID
                                where g.ID = ?
                                Group by g.ID", array($id));
                        if($rs->num_rows())
                        {
                                $game = $rs->row();
                                $temp = explode(",", $game->rank_array);
                                $rank_array = array();
                                for($i = 0; $i < 20; $i++)
                                {
                                        if(!$game->rank_array || !in_array($i, $temp))
                                                $rank_array[] = $i;
                                }
                                $game->rank_array = $rank_array;
                        }
                        
                        if($payout_id)
                        {
                                $rs = $this->db->query("Select * from Scratch_GPPayout where KeyID = ?", array($payout_id));
                                if($rs->num_rows())
                                {
                                        $payout = $rs->row();
                                }
                        }
                        return compact('payout', 'game');
                }
                	
	// Generate a random winning card
	public function generateWinningCard ()
	{
		$card = new ScratchCard ();
		// Generate the card with all the winning symbols
		$card->generateCard ($this->numberWinningSpots ());
		$card->setWinAmount ($this->winAmount());
		return $card;
	}
	
	public function generateLoserCard ()
	{
		$card = new ScratchCard ();
		$card->generateCard ($this->generateLosingSpotCount ());
		return $card;
	}
}
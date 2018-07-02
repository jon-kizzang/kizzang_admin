<?php
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class Oldpayments extends CI_Model {
    private $apiContext;
    
    function __construct()
    {
        parent::__construct();
        
        $this->db = $this->load->database ('admin', true);  
        
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                getenv("PAYPAL_API_CLIENT_ID"),
                getenv("PAYPAL_API_SECRET")
            )
        );
    
        $this->apiContext->setConfig(
            array(
                'mode' => getenv("PAYPAL_API_MODE")
            )
        );
    }    
    
    public function unpaidTotal()
    {        
        $rs = $this->db->query("SELECT COALESCE(SUM(amount), 0) AS Total FROM archive.paidList WHERE amountPaid <> 0.00");
        return $rs->row()->Total;
    }
       
    public function getAll($days_back = 7, $dollar_amount = 0)
    {
        $now = date("Y-m-d H:i:s");
        $where = "Where 1 = 1 ";        
                
        if($dollar_amount)       
            $where .= " AND amount < $dollar_amount";
        
        $where .= " AND dateWon > now() - INTERVAL $days_back DAY";
        
        $rs = $this->db->query("Select l.*, city, state, zipcode from archive.paidList l
            Inner join archive.Players p on p.id = l.playerId 
            $where order by dateWon DESC");
        //print $this->db->last_query(); die();
       $recs = $rs->result();
       $balance = $this->getBalance();
       $unpaid = $this->unpaidTotal();       
       $dollar_amounts = array('10' => 'Under $10', '20' => 'Under $20', '50' => 'Under $50');
        return compact('recs', 'balance', 'unpaid', 'days_back', 'dollar_amount','dollar_amounts');
    }
    
    public function get($id)
    {        
        $rs = $this->db->query("Select * from archive.paidList where id = ?", array($id));
        
        $rec = array();
        if($rs->num_rows())
            $rec = $rs->row();                        

        return compact('rec');
    }
    
    //FUNCTIONS TO PROCESS THE CLAIMS
    public function forfeitClaim($id)
    {
        $rs = $this->db->query("Select * from archive.paidList where id = ?", array($id));
        $claim = $rs->row();
        
        //Look for a replicated record in the Winners table
        $rs = $this->db->query("Select w.* from Winners w
            Inner join Users u on w.player_id = u.id
            where u.email = ? and w.serial_number = ?", array($claim->email, $claim->playerId));
        
        if($rs->num_rows())
        {
            $row = $rs->row();
            $this->db->query("Update Winners set status = 'Denied', prize_email = 'From Old System' where id = ?", array($row->id));
        }
        else
        {
            switch(substr($claim->serialNumber, 0, 2))
            {
                case 'KZ': $gameType = 'Scratchers'; break;
                case 'KS': $gameType = 'Slots'; break;
                case 'KP': $gameType = 'Parlay'; break;
                case 'KW': $gameType = 'Sweepstakes'; break;
            }
            $rec = array('player_id' => $claim->playerId, 'foreign_id' => $claim->id, 'game_type' => $gameType, 'serial_number' => $claim->serialNumber,
                'prize_name' => '$' . $claim->amount, 'prize_email' => 'From Old System', 'amount' => $claim->amount, 'processed' => 1, 'status' => 'Denied',
                'comments' => 'From Old System');
            
            if(!$this->db->insert('Winners', $rec))
                return false;
        }
        
        $this->db->query("Delete from archive.paidList where id = ?", array($id));
        
        return true;
    }
    
    public function payClaims($ids, &$message)
    {
        foreach($ids as $id)
            $this->payClaim ($id, $message);
    }
    
    public function payClaim($id, &$message)
    {        
        $rs= $this->db->query("Select * from archive.paidList l
            Inner join archive.Players p on p.id = l.playerId
            where l.id = ?", array($id));
        if($rs->num_rows())
        {
            $claim = $rs->row();
            $rs = $this->db->query("Select w.* from Winners w
            Inner join Users u on w.player_id = u.id
            where u.email = ? and w.serial_number = ?", array($claim->email, $claim->playerId));
            
            if($rs->num_rows())
            {
                $row = $rs->row();
                $this->db->query("Update Winners set status = 'Approved', prize_email = 'From Old System' where id = ?", array($row->id));
            }
            else
            {
                switch(substr($claim->serialNumber, 0, 2))
                {
                    case 'KZ': $gameType = 'Scratchers'; break;
                    case 'KS': $gameType = 'Slots'; break;
                    case 'KP': $gameType = 'Parlay'; break;
                    case 'KW': $gameType = 'Sweepstakes'; break;
                }
                $rec = array('player_id' => $claim->playerId, 'foreign_id' => $claim->id, 'game_type' => $gameType, 'serial_number' => $claim->serialNumber,
                    'prize_name' => '$' . $claim->amount, 'prize_email' => 'From Old System', 'amount' => $claim->amount, 'processed' => 1, 'status' => 'Approved',
                    'comments' => 'From Old System');

                if(!$this->db->insert('Winners', $rec))
                    return false;
                
                $winnerId = $this->db->insert_id();
            }
            
            $payment = array('winnerId' => $winnerId, 'playerId' => $claim->playerId, 'amount' => $claim->amount, 'prizeName' => '$' . $claim->amount, 'serialNumber' => $claim->serialNumber,
                'status' => 'Unpaid', 'firstName' => $claim->firstName, 'lastName' => $claim->lastName, 'email' => $claim->email, 'phone' => $claim->phone, 'city' => $claim->city, 'address' => '',
                'state' => $claim->state, 'zip' => $claim->zipcode, 'payPalEmail' => $claim->paypal_email);
            
            if(!$this->db->insert('Payments', $payment))
                return false;
                        
            $paymentId = $this->db->insert_id();
            
            $this->db->query("Delete from archive.paidList where id = ?", array($id));
            admin_model::addAudit($this->db->last_query(), "payments", "manualPayClaim");
            
            $payouts = new \PayPal\Api\Payout();

            $senderBatchHeader = new \PayPal\Api\PayoutSenderBatchHeader();

            $batchId = uniqid();
            $senderBatchHeader->setSenderBatchId($batchId)
                ->setEmailSubject("You have a Payment from Kizzang");

            $senderItem = new \PayPal\Api\PayoutItem();
            $senderItem->setRecipientType('Email')
                ->setNote('Thanks for your patronage!')
                ->setReceiver($claim->paypal_email)
                ->setSenderItemId($claim->id)
                ->setAmount(new \PayPal\Api\Currency('{
                                    "value":' . $claim->amount . ',
                                    "currency":"USD"
                                }'));

            $payouts->setSenderBatchHeader($senderBatchHeader)
                ->addItem($senderItem);

            try {
                $output = $payouts->createSynchronous($this->apiContext);
            } catch (Exception $ex) {                            
                exit(1);
            }                         

            foreach($output->items as $item)
            {
                $rec = array('payPalItemId' => $item->payout_item_id, 'payPalTransactionId' => $item->transaction_id, 
                    'payPalStatus' => $item->transaction_status, 'payPalBatchId' => $output->batch_header->payout_batch_id, 'status' => 'Pending');
                $rec['payPalError'] = 'None';
                if(isset($item->errors))                
                    $rec['payPalError'] = $item->errors->name;                                    
                
                if($item->transaction_status == "SUCCESS")
                {
                    $rec['status'] = 'Paid';
                    $message .= "Claim $paymentId was paid successfully in PayPal.\n";
                    $this->updateQB($paymentId, $message);
                }
                
                $this->db->where("id", $paymentId);
                $this->db->update("Payments", $rec);
                admin_model::addAudit($this->db->last_query(), "payments", "PayClaim");
                
                if($rec['payPalError'] == 'RECEIVER_UNREGISTERED') // Then Automatically Forfeit it
                {
                    $this->db->query("Update Payments set status = 'Forfeited' where id = ?", array($paymentId));
                    admin_model::addAudit($this->db->last_query(), "payments", "PayClaim");
                    $message .= "Claim $id was forfeited because email not registered.\n";
                }
                return true;
            }
        }
        $message .= "Query Failed for getting claim\n";
        return false;
    }
    
    public function updateQB($id, &$message)
    {        
         $rs = $this->db->query("Select * from Payments where id = ? and qb = 0", array($id));
         if($rs->num_rows())
         {
              $claim = $rs->row();
              $qb_result = $this->updateQuickbooks ($claim);
                
               if( array_key_exists('success', $qb_result) && $qb_result['success'] == false)
               {
                    if( array_key_exists('error', $qb_result) )
                    {
                        $message = $qb_result['error'];
                        return false;                        
                    }
                    $message .= "Claim $id had an Unknown Error in Updating QB Record.\n";
                    return false;
               } 

               $this->db->where('id', $id);
               $this->db->update('Payments', array('qb' => 1));
               admin_model::addAudit($this->db->last_query(), "payments", "updateQB");
               $message .= "Claim $id: Quickbooks Update Correctly.\n";
               return true;
         }                 
         $message .= "Qualifying Claim not found in the DB";
         return false;
    }
    
    public function manualPayClaim($id) 
    {
        $rs= $this->db->query("Select * from archive.paidList l
            Inner join archive.Players p on p.id = l.playerId
            where l.id = ?", array($id));
        if($rs->num_rows())
        {
            $claim = $rs->row();
            $rs = $this->db->query("Select w.* from Winners w
            Inner join Users u on w.player_id = u.id
            where u.email = ? and w.serial_number = ?", array($claim->email, $claim->playerId));
            
            if($rs->num_rows())
            {
                $row = $rs->row();
                $this->db->query("Update Winners set status = 'Approved', prize_email = 'From Old System' where id = ?", array($row->id));
            }
            else
            {
                switch(substr($claim->serialNumber, 0, 2))
                {
                    case 'KZ': $gameType = 'Scratchers'; break;
                    case 'KS': $gameType = 'Slots'; break;
                    case 'KP': $gameType = 'Parlay'; break;
                    case 'KW': $gameType = 'Sweepstakes'; break;
                }
                $rec = array('player_id' => $claim->playerId, 'foreign_id' => $claim->id, 'game_type' => $gameType, 'serial_number' => $claim->serialNumber,
                    'prize_name' => '$' . $claim->amount, 'prize_email' => 'From Old System', 'amount' => $claim->amount, 'processed' => 1, 'status' => 'Approved',
                    'comments' => 'From Old System');

                //print_r($rec);
                if(!$this->db->insert('Winners', $rec))
                    return false;
                
                //print $this->db->last_query();
                $winnerId = $this->db->insert_id();
            }
            
            $payment = array('winnerId' => $winnerId, 'playerId' => $claim->playerId, 'amount' => $claim->amount, 'prizeName' => '$' . $claim->amount, 'serialNumber' => $claim->serialNumber,
                'status' => 'Paid', 'firstName' => $claim->firstName, 'lastName' => $claim->lastName, 'email' => $claim->email, 'phone' => $claim->phone, 'city' => $claim->city, 'address' => '',
                'state' => $claim->state, 'zip' => $claim->zipcode, 'payPalEmail' => $claim->paypal_email);
            
            if(!$this->db->insert('Payments', $payment))
                return false;
            
            admin_model::addAudit($this->db->last_query(), "payments", "manualPayClaim");
            
            $this->db->query("Delete from archive.paidList where id = ?", array($id));
            admin_model::addAudit($this->db->last_query(), "payments", "manualPayClaim");
        }
                
        return true;
    }
        
    //PAYPAL SECTION
    public function getBalance()
    {        
        return "Find Balance Call";
    }       
    
    //QUICKBOOK SECTION
    function updateQuickbooks ($data)
    {        
        $config = new Qb_config();
        $rs = $this->db->query("SELECT qbVendorId FROM UserQB WHERE playerId=?", array($data->playerId));
        
        $quickbooksVendorID = 0;
        if ($rs->num_rows())
        {
            $temp = $rs->row();
            $quickbooksVendorID = $temp->qbVendorId;            
        }

        // Update Quickbooks Online
        //
        // Set up the IPP instance
        $IPP = new QuickBooks_IPP($config->dsn);

        // Enables sandbox mode            
        $IPP->sandbox($config->sandbox_mode);            
        // Get our OAuth credentials from the database	
        $creds = $config->IntuitAnywhere->load($config->the_username, $config->the_tenant);	

        // Tell the framework to load some data from the OAuth store
        $IPP->authMode(QuickBooks_IPP::AUTHMODE_OAUTH, $config->the_username, $config->creds);

        //print_r($creds); // Print the credentials we're using

        // This is our current realm
        $realm = $creds['qb_realm'];

        // Load the OAuth information from the database
        if ($Context = $IPP->context())
        {
            // Set the IPP version to v3 
            $IPP->version(QuickBooks_IPP_IDS::VERSION_3);

            $VendorService = new QuickBooks_IPP_Service_Vendor();
            if ($quickbooksVendorID == 0)
            {
                // Create a new vendor
                $Vendor = new QuickBooks_IPP_Object_Vendor();                    
            }
            else
            {
                // Get the vendor information from quickbooks
                $vendors = $VendorService->query($Context, $realm, "SELECT * FROM Vendor WHERE Id = '" . $quickbooksVendorID . "'");
                $Vendor = isset($vendors[0]) ? $vendors[0] : NULL;
                if(!$Vendor)
                    $Vendor = new QuickBooks_IPP_Object_Vendor();
            }

            if ( !isset( $Vendor ) )
            {
                $result['success'] = false;
                $result['error'] = "Problem with Getting Vender ID";
                return $result;
            }

            $Vendor->setAccountNumber ($data->playerId);
            $Vendor->setGivenName($data->firstName);

            $Vendor->setFamilyName($data->lastName);

            // Create the address
            $Address = new QuickBooks_IPP_Object_BillAddr ();
            $Address->setLine1 ($data->address);
            $Address->setCity($data->city);
            $Address->setCountrySubDivisionCode ($data->state);
            $Address->setPostalCode ($data->zip);
            $Address->setTag ('Billing');

            // Add the address to the vendor
            $Vendor->setBillAddr ($Address);

            // Email
            $PrimaryEmailAddr = new QuickBooks_IPP_Object_PrimaryEmailAddr();
            $PrimaryEmailAddr->setAddress($data->payPalEmail);
            $Vendor->setPrimaryEmailAddr($PrimaryEmailAddr);

            // The account number is the kizzang player id
            $Vendor->setAcctNum ($data->playerId);
            $Vendor->setVendor1099 (true);

            $PrimaryPhone = new QuickBooks_IPP_Object_PrimaryPhone();
            $PrimaryPhone->setFreeFormNumber($data->phone);
            $Vendor->setPrimaryPhone($PrimaryPhone);

            $MobilePhone = new QuickBooks_IPP_Object_PrimaryPhone();
            $MobilePhone->setFreeFormNumber($data->phone);
            $Vendor->setMobile($MobilePhone);

            $display_name = $data->firstName . ' ' . $data->lastName . ' playerId ' . $data->playerId;
            $Vendor->setDisplayName ($display_name);			

            if ($quickbooksVendorID != 0)
            {
                if ($resp = $VendorService->update($Context, $realm, $quickbooksVendorID, $Vendor))
                {
                    //$quickbooksVendorID = filter_var($resp, FILTER_SANITIZE_NUMBER_INT);
                    //$quickbooksVendorID = abs ($quickbooksVendorID);
                }
                else
                {
                    echo $VendorService->lastRequest ();
                    echo $VendorService->lastResponse ();
                    echo $VendorService->lastError ();

                    $last_error = $VendorService->lastError ();
                    $result['error'] = 'Unable to add QB Vendor:' . $last_error;

                    return $result;
                }
            }
            else
            {                
                if ($resp = $VendorService->add($Context, $realm, $Vendor))
                {
                    $quickbooksVendorID = filter_var($resp, FILTER_SANITIZE_NUMBER_INT);
                    $quickbooksVendorID = intval( abs ($quickbooksVendorID) );

                    // Insert the information into the database
                    $this->db->insert("UserQB", array('playerId' => $data->playerId, 'qbVendorId' => $quickbooksVendorID));                        
                }
                else
                {
                    $last_error = $VendorService->lastError ();
                    $result['error'] = 'Unable to add QB Vendor:' . $last_error;
                    return $result;
                }
            }

            $PurchaseService = new QuickBooks_IPP_Service_Purchase();

            if ( isset( $PurchaseService ) )
            {
                // Create our Purchase
                $Purchase = new QuickBooks_IPP_Object_Purchase();

                if ( isset( $Purchase ))
                {
                    $memo = "Serial #". $data->serialNumber .", entry #". $data->id .", event #" . $data->winnerId;

                    $Line = new QuickBooks_IPP_Object_Line();
                    $Line->setDescription($memo);
                    $Line->setAmount($data->amount);
                    $Line->setDetailType('AccountBasedExpenseLineDetail');

                    $checkingAccountId = ACCOUNT_CHECKING;          // Checking Account ID
                    $transactionType = ACCOUNT_PROMOTIONS;          // Promotion Type ID
                    $commissionsAndFees = ACCOUNT_COMMISIONS_FEES;  // Commissions & Fees ID

                    $AccountBasedExpenseLineDetail = new QuickBooks_IPP_Object_AccountBasedExpenseLineDetail();
                    $AccountBasedExpenseLineDetail->setAccountRef('{-'.$transactionType.'}');
                    $AccountBasedExpenseLineDetail->setBillableStatus('NotBillable');

                    $Line->setAccountBasedExpenseLineDetail($AccountBasedExpenseLineDetail);

                    $Purchase->addLine($Line);

                    $Purchase->setPrivateNote($memo);
                    $Purchase->setAccountRef('{-'.$checkingAccountId.'}');
                    $Purchase->setEntityRef($quickbooksVendorID);
                    $Purchase->setPaymentType('Cash');

                    // Add commissions/fee to the transactions
                    $Fee = new QuickBooks_IPP_Object_Line();
                    $Fee->setDescription("PayPal Transaction Fee: ".$memo);
                    $Fee->setAmount($amount * .02);
                    $Fee->setDetailType('AccountBasedExpenseLineDetail');

                    $AccountBasedExpenseLineDetail = new QuickBooks_IPP_Object_AccountBasedExpenseLineDetail();
                    $AccountBasedExpenseLineDetail->setAccountRef('{-'.$commissionsAndFees.'}');
                    $AccountBasedExpenseLineDetail->setBillableStatus('NotBillable');

                    $Fee->setAccountBasedExpenseLineDetail($AccountBasedExpenseLineDetail);

                    $Purchase->addLine($Fee);

                    $Purchase->setPrivateNote($memo);
                    $Purchase->setAccountRef('{-'.$checkingAccountId.'}');
                    $Purchase->setEntityRef($quickbooksVendorID);
                    $Purchase->setPaymentType('Cash');

                    // Send payment to QBO 
                    if ($resp = $PurchaseService->add($Context, $realm, $Purchase))
                    {
                        //print('Our new Payment ID is: [' . $resp . ']');
                        $result['success'] = true;
                    }
                    else
                    {
                        print($PurchaseService->lastError());
                        $result['success'] = false;
                        $result['error'] = "Unable to update quickbooks account:".$quickbooksVendorID.",".$memo;
                    }
                }                
            }
        }
        else
        {
            $result['error'] = "Unable to get QB context";
            return $result;
        }			

        return $result;
    }
}
<?php 

class rightsig_model extends CI_Model
{        
                
        private $url;
        private $headers;
        private $app_id;
        //ALL FUNCTIONS TO DO API CALLS
        function __construct()
        {
            parent::__construct();
            $this->url = getenv("RIGHT_SIGNATURE_URL");
            $this->headers = array(getenv("RIGHT_SIGNATURE_TOKEN"));
            $this->db = $this->load->database ('admin', true);
                                    
        }
        
        private function processRequest($url, $data, $type = 'POST')
        {
            switch($type)
            {
                case "POST": $is_post = true; break;
                case "GET": $is_post = false; break;
                case "PUT": $is_post = true; break;
            }

            $ch = curl_init($url);
            
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
        
        private function updateUserIds()
        {
            $rs = $this->db->query("Select id, tags from rightSignature.documents where tags like '%user_id%' and playerId is NULL");
            if($rs->num_rows())
            {
                foreach($rs->result() as $row)
                {
                    if(preg_match("/user_id:([0-9]+)/", $row->tags, $matches))
                    {
                        $this->db->query("Update rightSignature.documents set playerId = ? where id = ?", array($matches[1], $row->id));
                    }
                }
            }
        }
        
        public function getTemplates()
        {
            $url = getenv("RIGHT_SIGNATURE_URL") . "/api/templates.json";
            return $this->processRequest($url, array(), 'GET');
        }
        
        public function getDocuments($queryString = NULL)
        {            
            if(!$queryString)
                $url = $this->url . "/api/documents.json?state=completed&per_page=50&page=1";
            else
                $url = $this->url . "/api/documents.json?" . $queryString;
            
            $isDone = false;
            $query = "Insert into rightSignature.documents (id, status, processingState, tags, thumbUrl, signedUrl, createdDate, completedDate, expirationDate) values 
                (?,?,?,?,?,?,?,?,?) 
                On duplicate key update status = VALUES(status), processingState = VALUES(processingState), tags = VALUES(tags), thumbUrl = VALUES(thumbUrl),
                signedUrl = VALUES(signedUrl), createdDate = VALUES(createdDate), completedDate = VALUES(completedDate), expirationDate = VALUES(expirationDate)";
            
            $audit_query = "Insert into rightSignature.audits (documentId, sequence, message, created) values (?, ?, ?, ?) on duplicate key update message = VALUES(message), created = VALUES(created)";
            
            $attachment_query = "Insert into rightSignature.attachments (id, documentId, action, downloadUrl) values (?,?,?,?) on duplicate key update action = VALUES(action), downloadUrl = VALUES(downloadUrl)";
            while(!$isDone)
            {
                $ret = $this->processRequest($url, array(), 'GET');
                foreach($ret['page']['documents']as $document)
                {
                    $this->db->query($query, array($document['guid'], $document['state'], $document['processing_state'], $document['tags'],
                        urldecode($document['large_url']), urldecode($document['signed_pdf_url']), date("Y-m-d H:i:s", strtotime($document['created_at'])),
                        date("Y-m-d H:i:s", strtotime($document['completed_at'])), date("Y-m-d H:i:s", strtotime($document['expires_on']))));
                    
                    if(isset($document['audit_trails']))
                    {
                        foreach($document['audit_trails'] as $index => $audit)
                            $this->db->query($audit_query, array($document['guid'], $index, $audit['message'], date("Y-m-d H:i:s", strtotime($audit['timestamp']))));                        
                    }
                    
                    if(isset($document['signer_attachments']))
                    {
                        foreach($document['signer_attachments'] as $attachment)
                            $this->db->query($attachment_query, array($attachment['id'], $document['guid'], $attachment['action'], urldecode($attachment['download_url'])));
                    }
                }
                
                if($ret['page']['current_page'] < $ret['page']['total_pages'])                
                    $url = str_replace("page=" . $ret['page']['current_page'], "page=" . ($ret['page']['current_page'] + 1), $url);                
                else
                    $isDone = true;
                
            }
            $this->updateUserIds();
            return array('success' => 1);
        }
}

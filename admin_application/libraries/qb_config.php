<?php
require_once dirname(__FILE__) . '/quickbooks/QuickBooks.php';

class Qb_config {

    public $token;
    public $dsn;
    public $the_username;
    public $the_tenant;
    public $quickbooks_oauth_url;
    public $quickbooks_success_url;
    public $quickbooks_menu_url;
    public $encryption_key;
    public $IntuitAnywhere;
    public $quickbooks_CompanyInfo;
    public $sandbox_mode;
    public $creds;
    
    function __construct()
    {
        $this->token = getenv("OAUTH_QUICKBOOKS_TOKEN");
        $this->oauth_consumer_key = getenv("OAUTH_CONSUMER_KEY");
        $this->oauth_consumer_secret = getenv("OAUTH_CONSUMER_SECRET");
        if(isset($_SERVER['SERVER_NAME']))
        {
            $server_url = "http://" . $_SERVER['SERVER_NAME'];
        }
        else
        {
            switch(getenv("ENV"))
            {
                case 'dev': $server_url = "http://devadmin.kizzang.com"; break;
                case 'stage': $server_url = "http://stageadmin.kizzang.com"; break;
                case 'prod': $server_url = "http://admin.kizzang.com"; break;
            }
        }
        $this->quickbooks_oauth_url = $server_url.'/qb/oauth.php';
        $this->quickbooks_success_url = $server_url.'/qb/success.php';
        $this->quickbooks_menu_url = $server_url.'/qb/menu.php';
        $this->dsn = "mysqli://". getenv("MAINUSER").":".getenv("MAINPASSWORD")."@".getenv("MAINHOST")."/OAuth";
        
        $this->encryption_key = 'bcde1234';
        $this->the_username = 'DO_NOT_CHANGE_ME';
        $this->the_tenant = 12345;
        $this->sandbox_mode = (getenv("ENV") != "prod") ? false : true;
        
        // Initialize the database tables for storing OAuth information
        if (!QuickBooks_Utilities::initialized($this->dsn))
        {
            // Initialize creates the neccessary database schema for queueing up requests and logging
            QuickBooks_Utilities::initialize($this->dsn);
        }

        // Instantiate our Intuit Anywhere auth handler 
        // 
        // The parameters passed to the constructor are:
        //	$dsn					
        //	$oauth_consumer_key		Intuit will give this to you when you create a new Intuit Anywhere application at AppCenter.Intuit.com
        //	$oauth_consumer_secret	Intuit will give this to you too
        //	$this_url				This is the full URL (e.g. http://path/to/this/file.php) of THIS SCRIPT
        //	$that_url				After the user authenticates, they will be forwarded to this URL
        // 
        $this->IntuitAnywhere = new QuickBooks_IPP_IntuitAnywhere($this->dsn, $this->encryption_key, $this->oauth_consumer_key, $this->oauth_consumer_secret, $this->quickbooks_oauth_url, $this->quickbooks_success_url);

        // Are they connected to QuickBooks right now? 
        if ($this->IntuitAnywhere->check($this->the_username, $this->the_tenant))// and IntuitAnywhere->test($the_username, $the_tenant))
        {
            // Yes, they are 
            $quickbooks_is_connected = true;

            // Set up the IPP instance
            $IPP = new QuickBooks_IPP($this->dsn);

            // Enables sandbox mode           
            $IPP->sandbox($this->sandbox_mode);

            // Get our OAuth credentials from the database
            $this->creds = $this->IntuitAnywhere->load($this->the_username, $this->the_tenant);

            // Tell the framework to load some data from the OAuth store
            $IPP->authMode(
                    QuickBooks_IPP::AUTHMODE_OAUTH, 
                    $this->the_username, 
                    $this->creds);

            // Print the credentials we're using
            //print_r($creds);

            // This is our current realm
            $realm = $this->creds['qb_realm'];

            // Load the OAuth information from the database
            $Context = $IPP->context();

            // Get some company info
            $CompanyInfoService = new QuickBooks_IPP_Service_CompanyInfo();
            $this->quickbooks_CompanyInfo = $CompanyInfoService->get($Context, $realm);
        }
        else
        {
            $quickbooks_is_connected = false;
        }
    }    		       
}
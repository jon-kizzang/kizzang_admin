<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Kizzang Admin - <?= getenv("ENV") ?></title>

    <!-- Bootstrap Core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
    <!-- MetisMenu CSS -->
    <link href="/css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="/font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    
    <!-- Datatables -->
    <link href="/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <!-- jQuery -->
    <script src="/js/jquery.js"></script>
    <script   src="//code.jquery.com/ui/1.10.4/jquery-ui.min.js"   integrity="sha256-oTyWrNiP6Qftu4vs2g0RPCKr3g1a6QTlITNgoebxRc4="   crossorigin="anonymous"></script>
    
    <!-- Bootstrap Core JavaScript -->
    <script src="/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="/js/plugins/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="/js/sb-admin-2.js"></script>
    
    <script src="/js/jquery.dataTables.min.js"></script>
    <script src="/js/jquery.datetimepicker.js"></script>

</head>
<?php if(!$this->nativesession->get('User')) redirect ("/admin/login"); $nav = $this->nativesession->get('Nav')?>
<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html">Kizzang Admin</a>                
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">                
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
                        </li>
                        <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="/admin/logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a <?php if($page == "Home") : ?>class="active"<?php endif; ?> href="/admin"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>  
                        <?php if(in_array('Reports', $nav)) : ?>
                        <li>
                            <a <?php if($page == "MainReport") : ?>class="active"<?php endif; ?> href="/admin/mainReport"><i class="fa fa-exclamation-circle fa-fw"></i> Main Report</a>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array('Players', $nav)) : ?>
                        <li>
                            <a <?php if($page == "ViewPlayers") : ?>class="active"<?php endif; ?> href="/admin/players"><i class="fa fa-users fa-fw"></i> View Players</a>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array('Payments', $nav)) : ?>
                        <li <?php if($page == "PaymentsView" || $page == "PaymentPlayers" || $page == "PaymentsOldView") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-money fa-fw"></i> Payments<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a <?php if($page == "PaymentsView") : ?>class="active"<?php endif; ?> href="/payment/index">View Payments</a>
                                </li>
                                <li>
                                    <a <?php if($page == "PaymentPlayers") : ?>class="active"<?php endif; ?> href="/payment/players">View Player Payments</a>
                                </li>
                                <li>
                                    <a <?php if($page == "PaymentsOldView") : ?>class="active"<?php endif; ?> href="/payment/old_all">Old System Payments</a>
                               </li>                                
                            </ul>
                        </li>                        
                        <?php endif; ?>                                      
                        <?php if(in_array('Sponsors', $nav)) : ?>
                        <li <?php if($page == "ViewSponsors" || $page == "ViewAdvertisingCampaigns" || $page == "EditAdvertisingCampaign" || 
                                $page == "EditSponsors" || $page == "Affiliates" || $page == "ViewMarketingCampaigns" || 
                                $page == "EditMarketingCampaign" || $page == "ViewStates") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-credit-card fa-fw"></i> Sponsors<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a <?php if($page == "Affiliates") : ?>class="active"<?php endif; ?> href="/admin/affiliates/">Affiliates</a>
                                </li>
                                <li>
                                    <a <?php if($page == "ViewSponsors") : ?>class="active"<?php endif; ?> href="/admin/view_sponsors/">View Sponsors</a>
                                </li>
                                <li>
                                    <a <?php if($page == "EditSponsors") : ?>class="active"<?php endif; ?> href="/admin/edit_sponsor">Add Sponsors</a>
                                </li>
                                <li>
                                    <a <?php if($page == "ViewAdvertisingCampaigns") : ?>class="active"<?php endif; ?> href="/admin/view_advertising_campaigns/">View Advertising Campaigns</a>
                                </li>
                                <li>
                                    <a <?php if($page == "EditAdvertisingCampaign") : ?>class="active"<?php endif; ?> href="/admin/advertising_campaign">Add Advertising Campaign</a>
                                </li>
                                <li>
                                    <a <?php if($page == "ViewMarketingCampaigns") : ?>class="active"<?php endif; ?> href="/marketing_campaigns/view">View Marketing Campaigns</a>
                                </li>
                                <li>
                                    <a <?php if($page == "EditMarketingCampaign") : ?>class="active"<?php endif; ?> href="/marketing_campaigns/add">Add Marketing Campaign</a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array('ScratchCards', $nav)) : ?>
                        <li <?php if($page == "ViewLotteryConfigs" || $page == "EditLotteryConfigs") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-adn fa-fw"></i> Magnanimous Millions<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a <?php if($page == "ViewLotteryConfigs") : ?>class="active"<?php endif; ?> href="/lottery/view">View Lottery Configs</a>
                                </li>
                                <li>
                                    <a <?php if($page == "EditLotteryConfigs") : ?>class="active"<?php endif; ?> href="/lottery/add">Add Lottery Config</a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array('ScratchCards', $nav)) : ?>
                        <li <?php if($page == "ViewBBGame" || $page == "AddBBGame") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-gamepad fa-fw"></i> Bottom Bar Games<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a <?php if($page == "ViewBBGame") : ?>class="active"<?php endif; ?> href="/admin/games">View Games</a>
                                </li>
                                <li>
                                    <a <?php if($page == "AddBBGame") : ?>class="active"<?php endif; ?> href="/admin/add_game">Add Game</a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array('ScratchCards', $nav)) : ?>
                        <li <?php if($page == "ViewBingos" || $page == "AddBingo") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-beer fa-fw"></i> Bingo<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a <?php if($page == "ViewBingos") : ?>class="active"<?php endif; ?> href="/admin/bingo_games">View Games</a>
                                </li>
                                <li>
                                    <a <?php if($page == "AddBingo") : ?>class="active"<?php endif; ?> href="/admin/add_bingo_game">Add Game</a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array('ScratchCards', $nav)) : ?>
                        <li <?php if($page == "ViewPayouts" || $page == "AddPayouts") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-money fa-fw"></i> Game Payouts<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a <?php if($page == "ViewPayouts") : ?>class="active"<?php endif; ?> href="/admin/view_game_payouts">View Game Payouts</a>
                                </li>
                                <li>
                                    <a <?php if($page == "AddPayouts") : ?>class="active"<?php endif; ?> href="/admin/add_game_payout">Add Game Payouts</a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array('ScratchCards', $nav)) : ?>
                    <li <?php if($page == "ViewGame" || $page == "EditGame") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-photo fa-fw"></i> Scratch Cards<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a <?php if($page == "ViewGame") : ?>class="active"<?php endif; ?> href="/admin/view_games">View Scratch Cards</a>
                                </li>
                                <li>
                                    <a <?php if($page == "EditGame") : ?>class="active"<?php endif; ?> href="/admin/edit_game">Add Scratch Cards</a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array('Sweepstakes', $nav)) : ?>
                        <li <?php if($page == "ViewSweepstakes" || $page == "EditSweepstakes") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-gamepad fa-fw"></i> Sweepstakes<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a <?php if($page == "ViewSweepstakes") : ?>class="active"<?php endif; ?> href="/admin_sweepstakes/">View Sweepstakes</a>
                                </li>
                                <li>
                                    <a <?php if($page == "EditSweepstakes") : ?>class="active"<?php endif; ?> href="/admin_sweepstakes/add">Add Sweepstakes</a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array('Slots', $nav)) : ?>
                        <li <?php if($page == "ViewSlots" || $page == "AddSlots" || $page == "ViewSlotTournaments" || $page == "AddSlotTournaments") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-money fa-fw"></i> Slots<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a <?php if($page == "ViewSlots") : ?>class="active"<?php endif; ?> href="/admin_slots/">View Slots</a>
                                </li>
                                <li>
                                    <a <?php if($page == "AddSlots") : ?>class="active"<?php endif; ?> href="/admin_slots/add_slot">Add Slots</a>
                                </li>
                                <li>
                                    <a <?php if($page == "ViewSlotTournaments") : ?>class="active"<?php endif; ?> href="/admin_slots/view_tournaments">View Slot Tournaments</a>
                                </li>
                                <li>
                                    <a <?php if($page == "AddSlotTournaments") : ?>class="active"<?php endif; ?> href="/admin_slots/add_tournament">Add Slot Tournaments</a>
                                </li>
                            </ul>
                        </li>                        
                        <?php endif; ?>
                        <?php if(in_array('Parlay', $nav)) : ?>
                        <li <?php if($page == "ViewSportsSchedule" || $page == "ViewSportsTeams" || $page == "AddSportsSchedule" || $page == "EditEventScores" || $page == "ViewParlay" || $page == "AddParlay") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-ambulance fa-fw"></i> Parlay Cards<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a <?php if($page == "ViewSportsSchedule") : ?>class="active"<?php endif; ?> href="/admin_sports/view_sports_schedule">View Schedule Events</a>
                                </li>
                                <li>
                                    <a <?php if($page == "AddSportsSchedule") : ?>class="active"<?php endif; ?> href="/admin_sports/add_sports_schedule/">Add Scheduled Event</a>
                                </li>                                
                                <li>
                                    <a <?php if($page == "ViewParlay") : ?>class="active"<?php endif; ?> href="/admin_sports/view_parlay">View Parlay Cards</a>
                                </li>
                                <li>
                                    <a <?php if($page == "AddParlay") : ?>class="active"<?php endif; ?> href="/admin_sports/add_parlay/">Add Parlay Card</a>
                                </li>   
                                <li>
                                    <a <?php if($page == "ViewSportsTeams") : ?>class="active"<?php endif; ?> href="/admin_sports/view_sports_teams">View Sports Teams</a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array('Parlay', $nav)) : ?>
                        <li <?php if($page == "ViewROAL" || $page == "AddROAL") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-rocket fa-fw"></i> Run of a Lifetime<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">                                
                                <li>
                                    <a <?php if($page == "ViewROAL") : ?>class="active"<?php endif; ?> href="/admin/view_roals">View ROAL Configs</a>
                                </li>
                                <li>
                                    <a <?php if($page == "AddROAL") : ?>class="active"<?php endif; ?> href="/admin/edit_roal/">Add ROAL Config</a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array('Brackets', $nav)) : ?>
                        <li <?php if($page == "ViewBrackets" || $page == "AddBracket") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-bomb fa-fw"></i> Brackets<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">                                
                                <li>
                                    <a <?php if($page == "ViewBrackets") : ?>class="active"<?php endif; ?> href="/admin_sports/view_brackets">View Configs</a>
                                </li>
                                <li>
                                    <a <?php if($page == "AddBracket") : ?>class="active"<?php endif; ?> href="/admin_sports/add_bracket">Add Config</a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array('BG21', $nav)) : ?>
                        <li <?php if($page == "ViewBGQuestions" || $page == "AddBGQuestions" || $page == "ViewBGConfigs" || $page == "AddBGConfigs") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-bank fa-fw"></i> Big Game 21<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">                                
                                <li>
                                    <a <?php if($page == "ViewBGConfigs") : ?>class="active"<?php endif; ?> href="/admin_sports/view_bg_configs">View Configs</a>
                                </li>
                                <li>
                                    <a <?php if($page == "AddBGConfigs") : ?>class="active"<?php endif; ?> href="/admin_sports/add_bg_config">Add Config</a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array('FT', $nav)) : ?>
                        <li <?php if($page == "ViewFTConfigs" || $page == "AddFTConfigs") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-bank fa-fw"></i> Final 3<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">                                
                                <li>
                                    <a <?php if($page == "ViewFTConfigs") : ?>class="active"<?php endif; ?> href="/admin_sports/view_ft">View Configs</a>
                                </li>
                                <li>
                                    <a <?php if($page == "AddFTConfigs") : ?>class="active"<?php endif; ?> href="/admin_sports/add_ft_config">Add Config</a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array('Reports', $nav)) : ?>
                        <li <?php if($page == "ViewTestimonials" || $page == "AddTestimonial") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-envelope-square fa-fw"></i> Testimonials<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">                                
                                <li>
                                    <a <?php if($page == "ViewTestimonials") : ?>class="active"<?php endif; ?> href="/admin/view_testimonials">View Testimonials</a>
                                </li>
                                <li>
                                    <a <?php if($page == "AddTestimonial") : ?>class="active"<?php endif; ?> href="/admin/add_testimonial">Add Testimonial</a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array('Reports', $nav)) : ?>
                        <li <?php if($page == "ViewStoreItems" || $page == "AddStoreItem") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-dollar fa-fw"></i> Store<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">                                
                                <li>
                                    <a <?php if($page == "ViewStoreItems") : ?>class="active"<?php endif; ?> href="/admin/view_store_items">View Store Items</a>
                                </li>
                                <li>
                                    <a <?php if($page == "AddStoreItem") : ?>class="active"<?php endif; ?> href="/admin/add_store_item">Add Store Item</a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array('Admin', $nav)) : ?>
                        <li <?php if($page == "ViewNotifications" || $page == "AddNotifications" || $page == "ViewNotificationHistory") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-meh-o fa-fw"></i> Push Notifications<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">                                
                                <li>
                                    <a <?php if($page == "ViewNotificationHistory") : ?>class="active"<?php endif; ?> href="/admin/view_notification_history">View Notification History</a>
                                </li>
                                <li>
                                    <a <?php if($page == "ViewNotifications") : ?>class="active"<?php endif; ?> href="/admin/view_notifications">View Notification Queue</a>
                                </li>
                                <li>
                                    <a <?php if($page == "AddNotifications") : ?>class="active"<?php endif; ?> href="/admin/add_notifications">Add Notifications</a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array('Strings', $nav)) : ?>
                        <li <?php if($page == "ViewStrings" || $page == "EditStrings") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-language fa-fw"></i> Localizations<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a <?php if($page == "ViewStrings") : ?>class="active"<?php endif; ?> href="/admin/view_strings">View Localizations</a>
                                </li>
                                <li>
                                    <a <?php if($page == "EditStrings") : ?>class="active"<?php endif; ?> href="/admin/add_string">Add Localizations</a>
                                </li>
                            </ul>
                        </li> 
                        <?php endif; ?>                        
                        <?php if(in_array('Winners', $nav)) : ?>
                        <li <?php if($page == "ViewWinners" || $page == "LeaderBoard" || $page == "ManualWinners" || $page == "PickWinners" || $page == "ViewPaidWinners") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-money fa-fw"></i> Winners<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a <?php if($page == "ViewWinners") : ?>class="active"<?php endif; ?> href="/admin/view_winners/">Unpaid Winners</a>
                                </li>                                
                                <li>
                                    <a <?php if($page == "LeaderBoard") : ?>class="active"<?php endif; ?> href="/admin/leaderboard">Leader Board</a>
                                </li>
                                <li>
                                    <a <?php if($page == "PickWinners") : ?>class="active"<?php endif; ?> href="/admin/pick_winners">Pick Winners</a>
                                </li>
                                <li>
                                    <a <?php if($page == "ManualWinners") : ?>class="active"<?php endif; ?> href="/admin/manual_winners">Manual Winners</a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>                      
                        <?php if(in_array('Admin', $nav)) : ?>
                        <li <?php if($page == "ViewEventNotifications" || $page == "ViewACLS" || $page == "ViewACLGroups" || $page == "MemRedis" || $page == "MemRedis" || $page == "ViewConfigs" || $page == "ViewDBConfigs" || $page == "Cloudfront") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-search-plus fa-fw"></i> Admin<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a <?php if($page == "ViewEventNotifications") : ?>class="active"<?php endif; ?> href="/admin/view_event_notifications/">View Event Notifications</a>
                                </li>
                                <li>
                                    <a <?php if($page == "ViewACLS") : ?>class="active"<?php endif; ?> href="/admin/view_acls/">View Access Control Lists</a>
                                </li>
                                <li>
                                    <a <?php if($page == "ViewACLGroups") : ?>class="active"<?php endif; ?> href="/admin/acl_groups/">View User Groups</a>
                                </li>
                                <li>
                                    <a <?php if($page == "MemRedis") : ?>class="active"<?php endif; ?> href="/admin/mem_redis/">Memcache / Redis</a>
                                </li>
                                <li>
                                    <a <?php if($page == "Cloudfront") : ?>class="active"<?php endif; ?> href="/admin/cloudfront/">Cloudfront Invalidation</a>
                                </li>
                                <li>
                                    <a <?php if($page == "ViewConfigs") : ?>class="active"<?php endif; ?> href="/admin/view_configs">View S3 Configs</a>
                                </li>  
                                <li>
                                    <a <?php if($page == "ViewDBConfigs") : ?>class="active"<?php endif; ?> href="/admin/configs">View DB Configs</a>
                                </li> 
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array('Admin', $nav)) : ?>
                        <li <?php if($page == "ViewCrons" || $page == "AddCron" || $page == "ViewCronSchedule") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-clock-o fa-fw"></i> Crons<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a <?php if($page == "ViewCrons") : ?>class="active"<?php endif; ?> href="/admin/cron">View Cron Jobs</a>
                                </li>
                                <li>
                                    <a <?php if($page == "AddCron") : ?>class="active"<?php endif; ?> href="/admin/add_cron">Add Cron Jobs</a>
                                </li>
                                <li>
                                    <a <?php if($page == "ViewCronSchedule") : ?>class="active"<?php endif; ?> href="/admin/cron_schedule">View Cron Schedule</a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array('Reports', $nav)) : ?>
                        <li <?php if($page == "SlotReports" || $page == "AdStats" || $page == "SlotStats" || $page == "CampaignFallout" || $page == "SponsorFallout" || $page == "WeekWinnersReports" || $page == "MapReport" || $page == "Conversions" || $page == "TopTenReport" || $page == "DBSizes" || $page == "MainReports" || $page == "Retention" || $page == "ParlayReports") : ?>class="active"<?php endif; ?>>
                            <a href="#"><i class="fa fa-beer fa-fw"></i> Reports<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a <?php if($page == "Retention") : ?>class="active"<?php endif; ?> href="/admin_reports/retention">Retention</a>
                                </li>
                                <li>
                                    <a <?php if($page == "SlotStats") : ?>class="active"<?php endif; ?> href="/admin_reports/slot_stats">Slot Stats</a>
                                </li>
                                <li>
                                    <a <?php if($page == "AdStats") : ?>class="active"<?php endif; ?> href="/admin_reports/ad_stats">Ad Stats</a>
                                </li>                                
                                <li>
                                    <a <?php if($page == "Conversions") : ?>class="active"<?php endif; ?> href="/admin_reports/campaign_conversions">Campaign Conversions</a>
                                </li>
                                <li>
                                    <a <?php if($page == "CampaignFallout") : ?>class="active"<?php endif; ?> href="/admin_reports/fallout">Campaign Fallout</a>
                                </li>
                                <li>
                                    <a <?php if($page == "SponsorFallout") : ?>class="active"<?php endif; ?> href="/admin_reports/sponsor_fallout">Sponsor Fallout</a>
                                </li>
                                <li>
                                    <a <?php if($page == "WeekWinnersReports") : ?>class="active"<?php endif; ?> href="/admin_reports/last_week_winners">Last Week Winners</a>
                                </li>
                                <!--
                                <li>
                                    <a <?php if($page == "MapReport") : ?>class="active"<?php endif; ?> href="/admin_reports/map">Heat Map</a>
                                </li>-->
                                <li>
                                    <a <?php if($page == "MainReports") : ?>class="active"<?php endif; ?> href="/admin_reports/main">Main</a>
                                </li>
                                <li>
                                    <a <?php if($page == "TopTenReport") : ?>class="active"<?php endif; ?> href="/admin_reports/top_ten">Top Ten</a>
                                </li>
                                <li>
                                    <a <?php if($page == "SlotReports") : ?>class="active"<?php endif; ?> href="/admin_reports/slots">Slots</a>
                                </li>                                
                                <li>
                                    <a <?php if($page == "ParlayReports") : ?>class="active"<?php endif; ?> href="/admin_reports/parlays">Parlays</a>
                                </li>
                                <li>
                                    <a <?php if($page == "DBSizes") : ?>class="active"<?php endif; ?> href="/admin_reports/db_size">DB Sizes</a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            <?= $content; ?>
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                </div>
            </div>
        </div>
    
    <div class="modal bs-example-modal-lg fade" id="big-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                </div>
            </div>
        </div>
    
    <div class="modal bs-example-modal-sm fade" id="small-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                </div>
            </div>
        </div>
      <script>
          $(document).on('hidden.bs.modal', function (e) {
                $(e.target).removeData('bs.modal');
            });
            
            function ChangeDB(name)
            {
                $.get("/admin/update_db_source/" + name, {}, function(data){
                    if(data.success)
                    {
                        location.reload();
                    }
                    else
                    {
                        alert("Invalid Request");
                    }
                }, 'json')
            }
      </script>

</body>

</html>

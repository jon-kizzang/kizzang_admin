<?php if($hours) : ?>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
<?php endif; ?>
<?php if(isset($main)) : ?>
<?php if($mess) : ?>
<div class="alert alert-danger"><?= $mess; ?></div>
<?php $this->nativesession->delete("access_error"); ?>
<?php endif; ?>
<div class="well">
    <form id="frm_date" method="POST">
    <label>Select Date:</label>
    <input id="date_select" name="date_select" value="<?= $date; ?>"/>        
    </form>
</div>
<?php endif; ?>

<div id="dashboard">
<h1>Kizzang Daily Highlights</h1>
<h2><?= date('l, F j, Y', strtotime($date));?></h2>
<h3>Players</h3>
<table class="table table-bordered table-striped">
    <tr>
        <th>Total Accounts</th>
        <td>Total: <?= $player->total_accounts; ?><br/>User: <?= $player->total_user_accounts; ?><br/>Guest: <?= $player->total_guest_accounts; ?></td>
    </tr>
    <tr>
        <th>New Accounts</th>
        <td>Total: <?= $player->new_signups; ?><br/>User: <?= $player->new_user_signups; ?><br/>Guest: <?= $player->new_guest_signups; ?></td>
    </tr>
    <?php if($accounts && isset($accounts['Facebook-Web-None'])) : ?>
    <tr>
        <th>New Facebook Web Users</th>
        <td>Total: <?= $accounts['Facebook-Web-None']['Total']; ?> 
            <?php if(isset($accounts['Facebook-Web-None']['User'])) : ?> <br/>User: <?= $accounts['Facebook-Web-None']['User']; ?><?php endif; ?>
            <?php if(isset($accounts['Facebook-Web-None']['Guest'])) : ?> <br/>Guest: <?= $accounts['Facebook-Web-None']['Guest']; ?><?php endif; ?></td>
    </tr>
    <?php endif; ?>
    <?php if($accounts && isset($accounts['Facebook-Mobile-iOS'])) : ?>
    <tr>
        <th>New Facebook iOS Users</th>
        <td>Total: <?= $accounts['Facebook-Mobile-iOS']['Total']; ?> 
            <?php if(isset($accounts['Facebook-Mobile-iOS']['User'])) : ?> <br/>User: <?= $accounts['Facebook-Mobile-iOS']['User']; ?><?php endif; ?>
            <?php if(isset($accounts['Facebook-Mobile-iOS']['Guest'])) : ?> <br/>Guest: <?= $accounts['Facebook-Mobile-iOS']['Guest']; ?><?php endif; ?></td>
    </tr>
    <?php endif; ?>
    <?php if($accounts && isset($accounts['Facebook-Mobile-Android'])) : ?>
    <tr>
        <th>New Facebook Android Users</th>
        <td>Total: <?= $accounts['Facebook-Mobile-Android']['Total']; ?> 
            <?php if(isset($accounts['Facebook-Mobile-Android']['User'])) : ?> <br/>User: <?= $accounts['Facebook-Mobile-Android']['User']; ?><?php endif; ?>
            <?php if(isset($accounts['Facebook-Mobile-Android']['Guest'])) : ?> <br/>Guest: <?= $accounts['Facebook-Mobile-Android']['Guest']; ?><?php endif; ?></td>
    </tr>
    <?php endif; ?>
    <?php if($accounts && isset($accounts['Normal-Web-None'])) : ?>
    <tr>
        <th>New NonFB Web Users</th>
        <td>Total: <?= $accounts['Normal-Web-None']['Total']; ?> 
            <?php if(isset($accounts['Normal-Web-None']['User'])) : ?> <br/>User: <?= $accounts['Normal-Web-None']['User']; ?><?php endif; ?>
            <?php if(isset($accounts['Normal-Web-None']['Guest'])) : ?> <br/>Guest: <?= $accounts['Normal-Web-None']['Guest']; ?><?php endif; ?></td>
    </tr>
    <?php endif; ?>
    <?php if($accounts && isset($accounts['Normal-Mobile-None'])) : ?>
    <tr>
        <th>New NonFB Mobile Users</th>
        <td>Total: <?= $accounts['Normal-Mobile-None']['Total']; ?> 
            <?php if(isset($accounts['Normal-Mobile-None']['User'])) : ?> <br/>User: <?= $accounts['Normal-Mobile-None']['User']; ?><?php endif; ?>
            <?php if(isset($accounts['Normal-Mobile-None']['Guest'])) : ?> <br/>Guest: <?= $accounts['Normal-Mobile-None']['Guest']; ?><?php endif; ?></td>
    </tr>
    <?php endif; ?>
    <?php if($accounts && isset($accounts['Normal-Mobile-iOS'])) : ?>
    <tr>
        <th>New NonFB iOS Users</th>
        <td>Total: <?= $accounts['Normal-Mobile-iOS']['Total']; ?> 
            <?php if(isset($accounts['Normal-Mobile-iOS']['User'])) : ?> <br/>User: <?= $accounts['Normal-Mobile-iOS']['User']; ?><?php endif; ?>
            <?php if(isset($accounts['Normal-Mobile-iOS']['Guest'])) : ?> <br/>Guest: <?= $accounts['Normal-Mobile-iOS']['Guest']; ?><?php endif; ?></td>
    </tr>
    <?php endif; ?>
    <?php if($accounts && isset($accounts['Normal-Mobile-Android'])) : ?>
    <tr>
        <th>New NonFB Android Users</th>
        <td>Total: <?= $accounts['Normal-Mobile-Android']['Total']; ?> 
            <?php if(isset($accounts['Normal-Mobile-Android']['User'])) : ?> <br/>User: <?= $accounts['Normal-Mobile-Android']['User']; ?><?php endif; ?>
            <?php if(isset($accounts['Normal-Mobile-Android']['Guest'])) : ?> <br/>Guest: <?= $accounts['Normal-Mobile-Android']['Guest']; ?><?php endif; ?></td>
    </tr>
    <?php endif; ?>    
    <tr>
        <th>Daily Active Users</th>
        <td>Total: <?= $player->daily_active_total; ?><br/>User: <?= $player->daily_active_user; ?><br/>Guest: <?= $player->daily_active_guest; ?></td>
    </tr>
    <tr>
        <th>Returning</th>
        <td>Total: <?= $player->daily_active_total - $player->new_signups; ?><br/>User: <?= $player->daily_active_user - $player->new_user_signups; ?><br/>Guest: <?= $player->daily_active_guest - $player->new_guest_signups; ?></td>
    </tr>
    <tr>
        <th>Guest Conversion</th>
        <td><?= $guest_conversions->cnt; ?></td>
    </tr>
    <tr>
        <th>Guest Conversion %</th>
        <td><?php echo ($player->new_signups ? number_format($guest_conversions->cnt / $player->new_signups * 100, 2) : "0") . " %"; ?></td>
    </tr>
    <tr>
        <th>Guest Conversion (Time)</th>
        <td><?= floor(floor($guest_conversions->avg_diff) / 3600) . " Hours " . floor((floor($guest_conversions->avg_diff) % 3600) / 60) . " Minutes"; ?></td>
    </tr>
    <tr>
        <th>Chedda Available</th>
        <td><?= number_format($chedda['unused'], 0); ?></td>
    </tr>
    <tr>
        <th>Chedda Used Today</th>
        <td><?php if($chedda['used']) echo number_format($chedda['used']); else echo "0"; ?></td>
    </tr>
</table>

<?php if($genders) : ?>
<h3>Time on Site by Gender</h3>
<table class="table table-bordered table-striped">
     <thead>
            <tr>            
                <?php if(isset($genders['All'])) : ?><th>All</th><?php endif; ?>
                <?php if(isset($genders['Male'])) : ?><th>Male</th><?php endif; ?>
                <?php if(isset($genders['Female'])) : ?><th>Female</th><?php endif; ?>
                <?php if(isset($genders['None'])) : ?><th>None</th><?php endif; ?>
                <?php if(isset($genders['Other'])) : ?><th>Other</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>            
            <tr>            
                <?php if(isset($genders['All'])) : ?><td><?= floor($genders['All'] / 3600) . "h " . floor(($genders['All'] % 3600) / 60) . "m"; ?></td><?php endif; ?>
                <?php if(isset($genders['Male'])) : ?><td><?= floor($genders['Male'] / 3600) . "h " . floor(($genders['Male'] % 3600) / 60) . "m"; ?></td><?php endif; ?>
                <?php if(isset($genders['Female'])) : ?><td><?= floor($genders['Female'] / 3600) . "h " . floor(($genders['Female'] % 3600) / 60) . "m"; ?></td><?php endif; ?>
                <?php if(isset($genders['None'])) : ?><td><?= floor($genders['None'] / 3600) . "h " . floor(($genders['None'] % 3600) / 60) . "m"; ?></td><?php endif; ?>
                <?php if(isset($genders['Other'])) : ?><td><?= floor($genders['Other'] / 3600) . "h " . floor(($genders['Other'] % 3600) / 60) . "m"; ?></td><?php endif; ?>
            </tr>            
        </tbody>
</table>
<?php endif; ?>

<?php if($times) : ?>
<h3>App Utility Time</h3>
<table class="table table-bordered table-striped">  
    <thead>
            <tr>            
                <th>Type</th>
                <th>Average All</th>
                <th>Games Played All</th>
                <th># People All</th>
                <th>Average Male</th>
                <th>Games Played Male</th>
                <th># People Male</th>
                <th>Average Female</th>
                <th>Games Played Female</th>
                <th># People Female</th>
                <th>Average Other</th>
                <th>Games Played Other</th>
                <th># People Other</th>
                <th>Average None</th>
                <th>Games Played None</th>
                <th># People None</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($times as $type => $time) : ?>
            <tr>            
                <th><?= $type; ?></th>
                <?php if(!isset($time['All'])) : ?><td></td><td></td><td></td>
                <?php else : ?>
                <td><?= floor($time['All']['time_per_user'] / 3600) . "h " . floor(($time['All']['time_per_user'] % 3600) / 60) . "m"; ?></td>
                <td><?= number_format($time['All']['cnt'], 0); ?></td>
                <td><?= number_format($time['All']['user_count'], 0); ?></td>
                <?php endif; ?>
                <?php if(!isset($time['Male'])) : ?><td></td><td></td><td></td>
                <?php else : ?>
                <td><?= floor($time['Male']['time_per_user'] / 3600) . "h " . floor(($time['Male']['time_per_user'] % 3600) / 60) . "m"; ?></td>
                <td><?= number_format($time['Male']['cnt'], 0); ?></td>
                <td><?= number_format($time['Male']['user_count'], 0); ?></td>
                <?php endif; ?>
                <?php if(!isset($time['Female'])) : ?><td></td><td></td><td></td>
                <?php else : ?>
                <td><?= floor($time['Female']['time_per_user'] / 3600) . "h " . floor(($time['Female']['time_per_user'] % 3600) / 60) . "m"; ?></td>
                <td><?= number_format($time['Female']['cnt'], 0); ?></td>
                <td><?= number_format($time['Female']['user_count'], 0); ?></td>
                <?php endif; ?>
                <?php if(!isset($time['Other'])) : ?><td></td><td></td><td></td>
                <?php else : ?>
                <td><?= floor($time['Other']['time_per_user'] / 3600) . "h " . floor(($time['Other']['time_per_user'] % 3600) / 60) . "m"; ?></td>
                <td><?= number_format($time['Other']['cnt'], 0); ?></td>
                <td><?= number_format($time['Other']['user_count'], 0); ?></td>
                <?php endif; ?>
                <?php if(!isset($time['None'])) : ?><td></td><td></td><td></td>
                <?php else : ?>
                <td><?= floor($time['None']['time_per_user'] / 3600) . "h " . floor(($time['None']['time_per_user'] % 3600) / 60) . "m"; ?></td>
                <td><?= number_format($time['None']['cnt'], 0); ?></td>
                <td><?= number_format($time['None']['user_count'], 0); ?></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
</table>
<?php endif; ?>
<?php if($hours) : ?>
<div class="panel panel-primary">
    <div class="panel-heading">Day Breakdown</div>
    <div class="panel-body"><div id="day_breakdown"></div></div>
</div>
<?php endif; ?>
<?php if($retention) : ?>
<h3>Retention</h3>
<table class="table table-bordered table-striped">
     <thead>
            <tr>      
                <th>Days</th>
                <?php foreach($retention as $key => $value) : ?>
                <th><?= $key; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <tr>      
                <th>Original Users</th>
                <?php foreach($retention as $key => $value) : ?>
                <td><?= $value['original']; ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>      
                <th>Returning Users</th>
                <?php foreach($retention as $key => $value) : ?>
                <td><?= $value['count']; ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>      
                <th>Percent</th>
                <?php foreach($retention as $key => $value) : ?>
                <td><?= $value['percent']; ?> %</td>
                <?php endforeach; ?>
            </tr>
        </tbody>
</table>
<?php endif; ?>
<?php if($all_games) : ?>
<h3>User Ad Breakdown</h3>
<table class="table table-bordered table-striped">
     <thead>
            <tr>            
                <th>Game Type</th>
                <th>Guest</th>
                <th>Registered User</th>
                <th>Admin</th>
                <th>Online</th>
                <th>iOS</th>
                <th>Android</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($all_games as $row) : ?>
            <tr>
                <?php if($row->game_type) : ?>
                <td><?= $row->game_type; ?></td>
                <td><?= $row->guest; ?></td>
                <td><?= $row->registered; ?></td>
                <td><?= $row->admin; ?></td>                
                <td><?= $row->online_count; ?></td>
                <td><?= $row->ios; ?></td>
                <td><?= $row->android; ?></td>
                <?php else : ?>
                <td><b>Games per User:</b></td>
                <td><?= number_format($row->guest / $row->cnt, 1); ?></td>
                <td><?= number_format($row->registered / $row->cnt, 1); ?></td>
                <td><?= number_format($row->admin / $row->cnt, 1); ?></td>
                <td><?= number_format($row->online_count / $row->cnt, 1); ?></td>
                <td><?= number_format($row->ios / $row->cnt, 1); ?></td>
                <td><?= number_format($row->android / $row->cnt, 1); ?></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
</table>
<?php endif; ?>
<?php if($sweepstakes) : ?>
<h3>Sweepstakes</h3>
<table class="table table-bordered table-striped">
     <thead>
            <tr>            
                <th>Name</th>
                <th>Serial Number</th>
                <th>Entries</th>                
            </tr>
        </thead>
        <tbody>
            <?php foreach($sweepstakes as $sweepstake) : ?>
            <tr>            
                <td><?= $sweepstake->displayName; ?></td>
                <td><?= sprintf("KW%05d", $sweepstake->sweepstakesId); ?></td>
                <td><?= $sweepstake->cnt; ?></td>
            <?php endforeach; ?>
        </tbody>
</table>
<?php endif; ?>
<?php if($slots) : ?>
<?php foreach($slots as $key => $slot) : ?>
<h3>Slot Stats <?= $key; ?></h3>
<table class="table table-bordered table-striped">
     <thead>
            <tr>            
                <th>Name</th>
                <th>Min Win</th>
                <th>Max Win</th>
                <th>Unique Players</th>
                <th>Completed Games</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($slot as $game) : ?>
            <tr>            
                <td><?php if($game->ID) : ?> <?=$game->Name; ?> <?php else : ?> <strong>TOTAL</strong><?php endif; ?></td>
                <td><?= number_format($game->min_total, 0); ?></td>
                <td><?= number_format($game->max_total, 0); ?></td>
                <td><?= $game->num_players; ?></td>
                <td><?= $game->num_games; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
</table>
<?php endforeach;?>
<?php endif; ?>

<?php if($scratchers) : ?>
<h3>Scratch Card Stats</h3>
<table class="table table-bordered table-striped">
     <thead>
            <tr>            
                <th>Name</th>
                <th>Unique Players</th>
                <th># Cards Played</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($scratchers as $scratcher) : ?>
            <tr>            
                <td><?php if($scratcher->ID) : ?> <?=$scratcher->Name; ?> <?php else : ?> <strong>TOTAL</strong><?php endif; ?></td>
                <td><?= number_format($scratcher->player_count, 0); ?></td>
                <td><?= number_format($scratcher->cnt, 0); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
</table>
<?php endif; ?>

<?php if($lottery) : ?>
<h3>Lottery Stats</h3>
<table class="table table-bordered table-striped">
     <thead>
            <tr>            
                <th>Start Date</th>
                <th>End Date</th>
                <th># Cards Played</th>                
                <th>Unique Players</th>                
            </tr>
        </thead>
        <tbody>
            <?php foreach($lottery as $lottery_game) : ?>
            <tr>            
                <td><?= date("D M j, Y", strtotime($lottery_game->startDate)); ?></td>
                <td><?= date("D M j, Y", strtotime($lottery_game->endDate)); ?></td>
                <td><?= number_format($lottery_game->cnt, 0); ?></td>
                <td><?= number_format($lottery_game->player_count, 0); ?></td>                
            </tr>
            <?php endforeach; ?>
        </tbody>
</table>
<?php endif; ?>

<?php if($roal) : ?>
<h3>Run of a Lifetime Stats</h3>
<table class="table table-bordered table-striped">
     <thead>
            <tr>            
                <th>Card Date</th>
                <th>Theme</th>
                <th># Cards Played</th>                
            </tr>
        </thead>
        <tbody>
            <?php foreach($roal as $roal_game) : ?>
            <tr>            
                <td><?= date("D M j, Y", strtotime($roal_game->cardDate)); ?></td>
                <td><?= $roal_game->theme; ?></td>
                <td><?= number_format($roal_game->cnt, 0); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
</table>
<?php endif; ?>

<?php if($parlays) : ?>
<h3>Parlay Card Stats</h3>
<table class="table table-bordered table-striped">
     <thead>
            <tr>            
                <th>Serial Number</th>
                <th>Card Date</th>                
                <th>Unique Players</th>
                <th># of Cards</th>
                <th>Manual Entry</th>
                <th>Quick Pick</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($parlays as $parlay) : ?>
            <tr>            
                <td><?php if($parlay->serialNumber) : ?> <?=$parlay->serialNumber; ?> <?php else : ?> <strong>TOTAL</strong><?php endif; ?></td>
                <td><?= date('D, F d Y', strtotime($parlay->cardDate));?></td>
                <td><?= number_format($parlay->player_count, 0); ?></td> 
                <td><?= number_format($parlay->cnt, 0); ?></td>
                <td><?= number_format($parlay->nonqps, 0); ?></td>
                <td><?= number_format($parlay->qps, 0); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
</table>
<?php endif; ?>

<?php if($fts) : ?>
<h3>Final 3 Stats</h3>
<table class="table table-bordered table-striped">
     <thead>
            <tr>            
                <th>Serial Number</th>
                <th>Card Date</th>                
                <th>Unique Players</th>
                <th># of Cards</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($fts as $ft) : ?>
            <tr>            
                <td><?php if($ft->serialNumber) : ?> <?=$ft->serialNumber; ?> <?php else : ?> <strong>TOTAL</strong><?php endif; ?></td>
                <td><?= date('D, F d Y', strtotime($ft->cardDate));?></td>
                <td><?= number_format($ft->player_count, 0); ?></td> 
                <td><?= number_format($ft->cnt, 0); ?></td>                               
            </tr>
            <?php endforeach; ?>
        </tbody>
</table>
<?php endif; ?>

<?php if($user_winners) : ?>
<h3>Winners (Users)</h3>
<table class="table table-bordered table-striped">
     <thead>
            <tr>      
                <th>Player</th>
                <th>Address</th>
                <th>Serial Number</th>
                <th>Game Type</th> 
                <th>Game Name</th>
                <th>Prize Name</th>
                <th>Amount</th>
                <th>Ticket ID</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($user_winners as $winner) : ?>
            <tr>
                <?php if($winner->id) : ?>
                <td><?= $winner->name; ?></td>
                <td><?= $winner->address; ?></td>
                <td><?= $winner->serial_number; ?></td>
                <td><?= $winner->game_type; ?></td>
                <td><?= $winner->game_name; ?></td>
                <td><?= $winner->prize_name; ?></td> 
                <td><?= $winner->amount; ?></td>    
                <td><?= $winner->ticket_id; ?></td>
                <td><?= $winner->status; ?></td>
                <?php else : ?>
                <td colspan="6"><strong>TOTAL</strong></td>
                <td><?= $winner->amount; ?></td>
                <td colspan="2"></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
</table>
<?php endif; ?>

<?php if($guest_winners) : ?>
<h3>Winners (Guests)</h3>
<table class="table table-bordered table-striped">
     <thead>
            <tr>      
                <th>Player</th>
                <th>Address</th>
                <th>Serial Number</th>
                <th>Game Type</th> 
                <th>Game Name</th>
                <th>Prize Name</th>
                <th>Amount</th>
                <th>Ticket ID</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($guest_winners as $winner) : ?>
            <tr>  
                <?php if($winner->id) : ?>
                <td><?= $winner->name; ?></td>
                <td><?= $winner->address; ?></td>
                <td><?= $winner->serial_number; ?></td>
                <td><?= $winner->game_type; ?></td>
                <td><?= $winner->game_name; ?></td>
                <td><?= $winner->prize_name; ?></td> 
                <td><?= '$' . number_format($winner->amount, 2); ?></td>    
                <td><?= $winner->ticket_id; ?></td>
                <td><?= $winner->status; ?></td>
                <?php else : ?>
                <td colspan="6"><strong>TOTAL</strong></td>
                <td><?= $winner->amount; ?></td>
                <td colspan="2"></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
</table>
<?php endif; ?>

</div>
<?php if(isset($main)) : ?>
<script>
    $('#date_select').datepicker({
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            numberOfMonths: 1,
            onClose: function(){
                $("#frm_date").submit();
            }
        });
    
    <?php if($hours) : ?>
    var slot_plays = Morris.Line({  
        element: 'day_breakdown',  
        data: [
            <?php foreach($hours as $key => $row) : ?>
                        {hour: '<?= date("Y-m-d ", strtotime($date)) . $row->hour; ?>:00:00', total: '<?= $row->cnt; ?>', sweepstakes: '<?= $row->sweepstakes; ?>', slots: '<?= $row->slots; ?>', parlay: '<?= $row->parlay; ?>', scratchers: '<?= $row->scratchers; ?>', brackets: '<?= $row->brackets; ?>', lottery: '<?= $row->lottery; ?>', roal: '<?= $row->roal; ?>'}<?php if($key != count($hours) - 1) echo ",\n"; ?>
            <?php endforeach;?>
        ],  
        xkey: 'hour',
        ykeys: ['total', 'slots', 'parlay', 'scratchers', 'brackets','lottery','roal','sweepstakes'],
        labels: ['Users', 'Slots', 'Parlay', 'Scratchers', 'Brackets','Lottery','Run of a Lifetime','Sweepstakes']
      });
    <?php endif; ?>
</script>
<?php endif; ?>
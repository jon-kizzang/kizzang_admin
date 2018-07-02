<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
<div class="well">
    <label>Start Date: </label>
    <input style="margin-left: 20px;" id="StartDate" value="<?= $startDate; ?>"/>
    <label style="margin-left: 20px;">End Date: </label>
    <input style="margin-left: 20px;" id="EndDate" value="<?= $endDate; ?>"/>
</div>
<div class="panel panel-primary">
    <div class="panel-heading">Player Logins</div>
    <div class="panel-body">
        <div id="player_login" style="height: 250px;"></div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">Games Played</div>
    <div class="panel-body">
        <div id="winner_payments" style="height: 250px;"></div>
    </div>
</div>

<script>
 var main_chart = Morris.Line({  
  element: 'player_login',  
  data: [
      <?php foreach($player_logins as $key => $row) : ?>
                  {date: '<?= $row->date; ?>', total: '<?= $row->total; ?>', web_total: '<?= $row->web_total; ?>', facebook_total: '<?= $row->facebook_total; ?>', mobile_total: '<?= $row->mobile_total; ?>', fb_mobile_total: '<?= $row->fb_mobile_total; ?>'}<?php if($key != count($player_logins)) echo ",\n"; ?>
      <?php endforeach;?>
  ],  
  xkey: 'date',
  ykeys: ['total', 'web_total', 'facebook_total','mobile_total','fb_mobile_total'],
  labels: ['Total Players', 'Web NonFB', 'Web Facebook', 'Mobile NonFB', 'Mobile Facebook']
});

var winner_chart = Morris.Line({  
  element: 'winner_payments',  
  data: [
      <?php foreach($winner_payments as $key => $row) : ?>
                  {date: '<?= $row->date; ?>', total: '<?= $row->total; ?>', sweepstakes: '<?= $row->sweepstakes; ?>', slots: '<?= $row->slots; ?>', parlay: '<?= $row->parlay; ?>', scratchers: '<?= $row->scratchers; ?>'}<?php if($key != count($winner_payments)) echo ",\n"; ?>
      <?php endforeach;?>
  ],  
  xkey: 'date',
  ykeys: ['total', 'sweepstakes', 'slots', 'parlay', 'scratchers'],
  labels: ['Total', 'Sweepstakes', 'Slots', 'Parlay', 'Scratchers']
});

$(document).ready(function() {
    $( "#StartDate" ).datepicker({
            dateFormat: "yy-mm-dd",
            setDate: "<?= $startDate; ?>", 
            changeMonth: true,
            numberOfMonths: 3,
            maxDate: "<?= $endDate; ?>",
            onClose: function( selectedDate ) {
                $( "#EndDate" ).datepicker( "option", "minDate", selectedDate );
                getData();
            }
        });

        $( "#EndDate" ).datepicker({
            dateFormat: "yy-mm-dd",
            setDate: "<?= $endDate; ?>",
            changeMonth: true,
            numberOfMonths: 3,
            minDate: "<?= $startDate; ?>",
            onClose: function( selectedDate ) {
                $( "#StartDate" ).datepicker( "option", "maxDate", selectedDate );
                getData();
            }
        });          
});

function getData()
{
    $.post("/admin_reports/ajax_main", {startDate: $("#StartDate").val(), endDate: $("#EndDate").val()}, function(data){
        if(data.success)
        {
            main_chart.setData(data.player_logins);
            winner_chart.setData(data.winner_payments);
        }
    }, 'json')
}
</script>
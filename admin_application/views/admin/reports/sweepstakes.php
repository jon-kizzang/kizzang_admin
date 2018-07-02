<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
<div class="well">
    <label>Select Sweepstakes: </label>
    <select style="margin-left: 20px;" id="sweepstakes">
        <option value="">Select Sweepstakes</option>
        <?php foreach($sweepstakes as $sweepstake) : ?>
        <option value="<?= $sweepstake->id; ?>"><?= $sweepstake->description; ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="panel panel-primary">
    <div class="panel-heading">Sweepstakes Information</div>
    <div class="panel-body" id="info">
        
    </div>
</div>
<div class="panel panel-primary">
    <div class="panel-heading">Player Tickets Submitted</div>
    <div class="panel-body">
        <div id="sweeps_player" style="height: 400px;"></div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">Tickets Played per Day</div>
    <div class="panel-body">
        <div id="sweeps_play" style="height: 250px;"></div>
    </div>
</div>

<script>
 var sweeps_player = Morris.Donut({  
  element: 'sweeps_player',  
  data: [
      {label: "None", value: 10}
  ],    
});

var sweeps_play = Morris.Line({  
  element: 'sweeps_play',  
  data: [               
  ],  
  xkey: 'date',
  ykeys: ['played', 'players'],
  labels: ['Tickets Played', '# of Players']
});


$("#sweepstakes").change(function(){
    getData();
});


function getData()
{
    $.post("/admin_reports/ajax_sweepstakes", {id: $("#sweepstakes").val()}, function(data){
        if(data.success)
        {
            sweeps_player.setData(data.sweeps_player);
            sweeps_play.setData(data.sweeps_play);
            $("#info").html(data.info);
        }
    }, 'json')
}
</script>
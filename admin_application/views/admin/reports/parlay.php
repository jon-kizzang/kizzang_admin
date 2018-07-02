<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
<div class="well">
    <label>Select Sweepstakes: </label>
    <select style="margin-left: 20px;" id="parlays">
        <option value="">Select Parlay Date</option>
        <?php foreach($parlays as $parlay) : ?>
        <option value="<?= $parlay->id; ?>"><?= $parlay->date; ?></option>
        <?php endforeach; ?>
    </select>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">Player Played Tickets</div>
    <div class="panel-body">
        <div id="parlay_play" style="height: 400px;"></div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">Breakdown</div>
    <div class="panel-body">
        <div id="parlay_wins" style="height: 250px;"></div>
    </div>
</div>

<script>
 var parlay_play = Morris.Donut({  
  element: 'parlay_play',  
  data: [
      {label: "None", value: 10}
  ],    
});

var parlay_wins = Morris.Bar({  
  element: 'parlay_wins',  
  data: [               
  ],  
  xkey: 'wins',
  ykeys: ['num_winners', 'distinct_winners'],
  labels: ['# of Winner Cards', '# of Players']
});


$("#parlays").change(function(){
    getData();
});


function getData()
{
    $.post("/admin_reports/ajax_parlay", {id: $("#parlays").val()}, function(data){
        if(data.success)
        {
            parlay_play.setData(data.parlay_play);
            parlay_wins.setData(data.parlay_wins);
        }
    }, 'json')
}
</script>
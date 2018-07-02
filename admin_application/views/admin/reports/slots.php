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
    <div class="panel-heading">Slot Plays</div>
    <div class="panel-body">
        <div id="slot_plays" style="height: 250px;"></div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">Highest / Lowest / Average</div>
    <div class="panel-body">
        <div id="slot_scores" style="height: 250px;"></div>
    </div>
</div>

<script>
 var slot_plays = Morris.Line({  
  element: 'slot_plays',  
  data: [
      <?php foreach($lines as $key => $line) : ?>
                 <?= $line; ?><?php if($key != count($lines) - 1) echo ",\n"; ?>
      <?php endforeach;?>
  ],  
  xkey: 'num',
  xLabelFormat: function (x) { return x.getYear() + " Million"; },
  postUnits: '%',
  xLabelAngle: 45,
  ykeys: <?= $ykeys; ?>,
  labels: <?= $labels; ?>
});

var slot_scores = Morris.Line({  
  element: 'slot_scores',  
  data: [
      <?php foreach($slot_scores as $key => $row) : ?>
                  {date: '<?= $row->date; ?>', max: '<?= $row->max; ?>', min: '<?= $row->min; ?>', avg: '<?= $row->avg; ?>'}<?php if($key != count($slot_scores)) echo ",\n"; ?>
      <?php endforeach;?>
  ],  
  xkey: 'date',
  ykeys: ['max', 'min', 'avg'],
  labels: ['Max', 'Min', 'Average']
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
    $.post("/admin_reports/ajax_slots", {startDate: $("#StartDate").val(), endDate: $("#EndDate").val()}, function(data){
        if(data.success)
        {
            slot_scores.setData(data.slot_scores);
            slot_plays.setData(data.slot_plays);
        }
    }, 'json')
}
</script>
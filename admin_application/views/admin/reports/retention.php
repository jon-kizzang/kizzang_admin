<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
<div class="well">
    <label>Start Date: </label>
    <form id="frm_date" method="POST">
    <input style="margin-left: 20px;" id="StartDate" name="date" value="<?= $date; ?>"/>
    </form>
</div>
<?php if($seven_day) : ?>
<div class="panel panel-primary">
    <div class="panel-heading">7 Day</div>
    <div class="panel-body">
        <div id="7_day" style="height: 250px;"></div>
    </div>
</div>
<?php endif; ?>
<?php if($thirty_day) : ?>
<div class="panel panel-primary">
    <div class="panel-heading">30 Day</div>
    <div class="panel-body">
        <div id="30_day" style="height: 250px;"></div>
    </div>
</div>
<?php endif; ?>

<?php if($matrix) : ?>

<table class="table table-condensed table-bordered" style="table-layout: fixed; font-size: 12px; text-align: center;">
    <tr>
        <th style="width: 100px;">Date</th>
        <th style="width: 50px;">Total</th>
        <?php foreach($matrix[0] as $key => $value) : ?>
        <th style="text-align: center; width: 70px;"><?= $key; ?></th>
        <?php endforeach; ?>
    </tr>
<?php foreach($matrix as $y => $row) : ?>
    <tr>
        <th><?= $row_info[$y]['date']; ?></th>
        <th><?= $row_info[$y]['total']; ?></th>
        <?php foreach($row as $col => $value) : ?>
        <?php if($row_info[$y]['total']) : ?>
            <td style="<?php if($col == 7 || $col == 30): ?>border-left: 2px #000 solid; border-right: 2px #000 solid;<?php endif; ?> background-color: #00<?= sprintf("%02X",number_format((($value / $row_info[$y]['total']) * 150) + 100, 0)); ?>00"><?= number_format(($value / $row_info[$y]['total']) * 100, 0); ?>% (<?= $value; ?>)</td>
        <?php else : ?>
            <td style="background-color: #006400">0% (0)</td>
        <?php endif; ?>
        <?php endforeach; ?>
    </tr>
<?php endforeach; ?>
</table>

<?php endif; ?>
<script>
 <?php if($seven_day) : ?>
 var slot_plays = Morris.Line({  
  element: '7_day',  
  data: [
      <?php foreach($seven_day as $key => $row) : ?>
                  {date: '<?= $row['date']; ?>', total: '<?= $row['total']; ?>', percent: '<?= $row['percent']; ?>'}<?php if($key != count($seven_day)) echo ",\n"; ?>
      <?php endforeach;?>
  ],  
  xkey: 'date',
  ykeys: ['total', 'percent'],
  labels: ['Total', 'Percent']
});
<?php endif; ?>
<?php if($thirty_day) : ?>
var slot_plays = Morris.Line({  
  element: '30_day',  
  data: [
      <?php foreach($thirty_day as $key => $row) : ?>
                  {date: '<?= $row['date']; ?>', total: '<?= $row['total']; ?>', percent: '<?= $row['percent']; ?>'}<?php if($key != count($thirty_day)) echo ",\n"; ?>
      <?php endforeach;?>
  ],  
  xkey: 'date',
  ykeys: ['total', 'percent'],
  labels: ['Total', 'Percent']
});
<?php endif; ?>

$(document).ready(function() {
    $( "#StartDate" ).datepicker({
            dateFormat: "yy-mm-dd",
            setDate: "<?= $date; ?>", 
            changeMonth: true,
            numberOfMonths: 3,
            onClose: function( selectedDate ){
                $("#frm_date").submit();
            }
        });
});

</script>
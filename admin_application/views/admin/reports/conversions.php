<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
<div class="well">
    <label>Date: </label>
    <input name="date" id="date" type="text" value="<?= date("Y-m-d"); ?>"/>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">User List</div>
    <div class="panel-body" id="users" style="max-height: 400px; overflow: auto;">
        <?= $users; ?>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">Breakdown</div>
    <div class="panel-body">
        <div id="conversion_agg" style="height: 250px;"></div>
    </div>
</div>

<script>
 var conversion_agg = Morris.Donut({  
  element: 'conversion_agg',  
  data: [
      <?php if($types) : ?>                  
      <?php foreach($types as $index => $type) : ?>
      {label: "<?= $type->label; ?>", value: <?= $type->value; ?>}<?php if($index != (count($types) - 1)) echo ",\n"; ?>
      <?php endforeach; ?>
      <?php else : ?>
          {label: "No Users Added", value: 100}
      <?php endif; ?>
  ],    
});

$("#date").change(function(){
    getData();
});

    $('#date').datepicker({
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            numberOfMonths: 1,
            onClose: function(){
                $("#frm_date").submit();
            }
        });

function getData()
{
    $.post("/admin_reports/ajax_conversions", {date: $("#date").val()}, function(data){
        if(data.success)
        {
            conversion_agg.setData(data.types);
            $("#users").html(data.users);
        }
    }, 'json')
}
</script>
<link href="/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<script src="/js/jquery.datetimepicker.js"></script>

<div class="panel panel-primary" style="margin-bottom: 0px;">
    <div class="panel-heading">Replicate Tournament</div>
    <div class="panel-body">                               
    <div class="form-group" id="div_startDate">
        <h5>Please select the Start and End date you want to replicate this over:</h5>
        <h5>This tournament is <?= floor($tournament->time /  86400) . " Days, " . floor(($tournament->time % 86400) / 3600) . " Hours, " . ($tournament->time % 60) . " Minutes "; ?> in Duration.</h5>
        <input type="hidden" id="tourny_id" value="<?= $tournament->ID; ?>"/>
        <input type="hidden" id="time_interval" value="<?= $tournament->time; ?>"/>
    <label for="StartDate">Start Date</label>
    <input type="text" class="form-control" id="StartDate" name="StartDate" placeholder="StartDate" value="">
  </div>
    <div class="form-group" id="div_endDate">
    <label for="EndDate">End Date</label>
    <input type="text" class="form-control" id="EndDate" name="EndDate" placeholder="EndDate" value="">
  </div>
        <div id="date"></div>
</div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="validate_dates" class="btn btn-primary">Validate Dates</button></div>
</div>

<script>
   
$(function() {
        $("body").on("click", "#add_dates", function(e){
            e.preventDefault();
            var data_sent = {
                start_date: $("#StartDate").val(),
                end_date: $("#EndDate").val(),
                id: $("#tourny_id").val(),
                interval: $("#time_interval").val()
            };
            $.post("/admin_slots/add_tournament_dates", data_sent, function(data){
                location.reload();
            });
                        
        });
        
        $("#validate_dates").click(function(){
            var data_sent = {
                start_date: $("#StartDate").val(),
                end_date: $("#EndDate").val(),
                id: $("#tourny_id").val(),
                interval: $("#time_interval").val()
            };
            $.post("/admin_slots/validate_tournament_dates", data_sent, function(data){
                $("#date").html(data);
            });
        });
                
        $('#StartDate').datetimepicker({
            format:'Y-m-d H:i',            
            step: 60
          });
          
          $('#EndDate').datetimepicker({
            format:'Y-m-d H:i',            
            step: 60
          });
        
    });
</script>
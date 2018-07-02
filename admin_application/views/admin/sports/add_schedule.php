<link href="/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<script src="/js/jquery.datetimepicker.js"></script>

<div class="panel panel-primary" style="margin-bottom: 0px;">
    <div class="panel-heading"><?php if($schedule) : ?>Edit<?php else : ?>Add<?php endif; ?> Scheduled Event</div>
    <div class="panel-body">
        <div id="schedule_message"></div>
        <form role="form" id="frm_schedule">
   <?php if($schedule) : ?><input type="hidden" name="id" value="<?=$schedule->id?>"/><?php endif; ?>
    <div class="form-group" id="div_sportCategoryID">
    <label for="sportCategoryID">Sports Category</label>
    <select class="form-control" id="sportCategoryID" name="sportCategoryID">        
        <?php foreach($categories as $category) : ?>                                
            <option value="<?=$category->id?>" <?php if($schedule && $schedule->sportCategoryID == $category->id) echo " selected ";?>><?=$category->name?></option>                
        <?php endforeach; ?>
    </select>
  </div>
   <div class="form-group" id="div_team1">
    <label for="team1">Team 1</label>
    <select class="form-control" id="team1" name="team1">        
        <?php foreach($teams as $team) : ?>                                
            <option value="<?=$team->id?>" <?php if($schedule && $schedule->team1 == $team->id) echo " selected ";?>><?=$team->name?></option>                
        <?php endforeach; ?>
    </select>
  </div>
   <div class="form-group" id="div_team2">
    <label for="team2">Team 2</label>
    <select class="form-control" id="team2" name="team2">        
        <?php foreach($teams as $team) : ?>                                
            <option value="<?=$team->id?>" <?php if($schedule && $schedule->team2 == $team->id) echo " selected ";?>><?=$team->name?></option>                
        <?php endforeach; ?>
    </select>
  </div>  
   <div class="form-group" id="div_dateTime">
    <label for="Name">Date</label>
    <input type="text" class="form-control" id="dateTime" name="dateTime" placeholder="dateTime" <?php if($schedule) : ?> value="<?= $schedule->dateTime?>"<?php endif; ?>>
  </div>   
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_schedule" class="btn btn-primary"><?php if($schedule) : ?>Update<?php else : ?>Add<?php endif; ?> Scheduled Event</button></div>
</form>
</div>

<script>
$(function() {
        $('#dateTime').datetimepicker({
            format:'d.m.Y H:i',
            value:'<?php if($schedule) echo date('d.m.Y H:i', strtotime($schedule->dateTime)); else echo date('d.m.Y H:00');?>',
            step: 5
          });
          
        $("#sportCategoryID").change(function(){
            var id = $(this).val();
            $.get('/admin_sports/ajax_category_change/' + id, {}, function(data){
                $("#team1").html(data);
                $("#team2").html(data);
            });
        });
        
        $("#update_schedule").click(function(){
            $.post("/admin_sports/ajax_add_schedule", $("#frm_schedule").serialize(), function(data){
                if(data.success)
                {
                        $("#schedule_message").html("Insert / Update was good.").addClass("alert alert-success").removeClass('alert-danger');
                        $('html,body').scrollTop(0);
                        var command = "window.location = '/admin_sports/view_sports_schedule';";
                        setTimeout(command, 1000);
                }
                else
                {
                        $("#schedule_message").addClass("alert alert-danger").html("There were errors. They are listed / highlighted below.");
                        for(var key in data.errors)
                            $("#div_" + key).addClass("alert-danger");
                        $('html,body').scrollTop(0);
                }
            }, 'json');
        });
    });
</script>
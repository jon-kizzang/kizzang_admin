<link href="/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<script src="/js/jquery.datetimepicker.js"></script>
<div id="game_message"></div>
<div class="panel panel-primary" style="margin-bottom: 0px;">
    <div class="panel-heading"><?php if($game) : ?>Edit<?php else : ?>Add<?php endif; ?> Bingo Game</div>
    <form role="form" id="frm_game">
    <div class="panel-body">
        <div class="form-group" id="div_startDate">
            <label for="StartDate">Start Date</label>
            <input type="text" class="form-control" id="StartDate" name="startTime" placeholder="StartDate" value="<?php if($game) echo date("Y-m-d H:i", strtotime($game->startTime)); ?>">
        </div>
        <div class="form-group" id="div_endDate">
            <label for="EndDate">End Date</label>
            <input type="text" class="form-control" id="EndDate" name="endTime" placeholder="EndDate" value="<?php if($game) echo date("Y-m-d H:i", strtotime($game->endTime) + 1); ?>">
        </div>
        <?php if($game) : ?><input type="hidden" name="id" value="<?=$game->id?>"/><?php endif; ?>   
        <div class="form-group" id="div_languageCode">
         <label for="Name">Status</label>
         <select class="form-control" id="status" name="status">
             <?php foreach($statuses as $status) : ?>                                
                 <option value="<?=$status?>" <?php if(($game && $game->status == $status)) echo 'selected=""'; ?>><?=$status?></option>                
             <?php endforeach; ?>
         </select>    
        </div>
        <div class="form-group" id="div_description">
          <label for="description">Numbers</label>
          <textarea class="form-control" readonly="readonly" placeholder="Automatically Generated"><?php if($game)  echo $game->cardNumbersPicked;?></textarea>
        </div>   
        <div class="form-group" id="div_title">
            <label for="StartDate">Max Number of Balls</label>
            <input type="text" class="form-control" id="maxNumber" name="maxNumber" placeholder="45" value="<?php if($game) echo $game->maxNumber; ?>">
        </div>
        <div class="form-group" id="div_title">
            <label for="StartDate">Seconds between balls</label>
            <input type="text" class="form-control" id="callTime" name="callTime" placeholder="10" value="<?php if($game) echo $game->callTime; ?>">
        </div>
        <div class="form-group" id="div_title">
            <label for="StartDate">Current Ball</label>
            <input type="text" class="form-control" id="currentNum" readonly="" value="<?php if($game) echo $game->currentNum; ?>">
        </div>
    </div>
    </form>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_game" class="btn btn-primary"><?php if($game) : ?>Update<?php else : ?>Add<?php endif; ?> Bingo Game</button></div>
</div>

<script>
$(function() {
    $('#StartDate').datetimepicker({
        format:'Y-m-d H:i',
        value:'<?php if($game) echo date("Y-m-d H:i", strtotime($game->startTime)); ?>',
        step: 10
    });

    $('#EndDate').datetimepicker({
        format:'Y-m-d H:i',
        value:'<?php if($game) echo date("Y-m-d H:i", strtotime($game->endTime)); ?>',
        step: 10
    });
    
    $("#update_game").click(function(){
        $.post('/admin/ajax_save_bingo_game', $("#frm_game").serialize(), function(data){            
                if(data.success)
                {
                        $("#game_message").html("Insert / Update of the Bingo Game was good.").addClass("alert alert-success").removeClass('alert-danger');
                        $('html,body').scrollTop(0);
                        var command = "window.location = '/admin/bingo_games';";
                        setTimeout(command, 1000);
                }
                else
                {
                        alert(data.error);
                }
        },'json');
    });
});
</script>
<link href="/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<script src="/js/jquery.datetimepicker.js"></script>
<div class="panel panel-primary">    
    <div class="panel-heading"><?php if($config) : ?>Edit<?php else : ?>Add<?php endif; ?> Lottery Config</div>
    <div class="panel-body">
        <div id="payout_message"></div>
        <form role="form" id="frm_lottery">
   <?php if($config) : ?><input type="hidden" name="id" id="id" value="<?=$config->id?>"/><?php endif; ?>
   <div class="form-group" id="div_numTotalBalls">
    <label for="Name"># of Total Balls</label>
    <input type="text" class="form-control" id="numTotalBalls" name="numTotalBalls" placeholder="numTotalBalls" <?php if($config) : ?> value="<?= $config->numTotalBalls?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="div_numAnswerBalls">
    <label for="Name"># of Answer Balls</label>
    <input type="text" class="form-control" id="numAnswerBalls" name="numAnswerBalls" placeholder="numAnswerBalls" <?php if($config) : ?> value="<?= $config->numAnswerBalls?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="div_numCards">
    <label for="Name"># of Limit</label>
    <input type="text" class="form-control" id="numCards" name="numCards" placeholder="numCards" <?php if($config) : ?> value="<?= $config->numCards?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="div_cardLimit">
    <label for="Name">Limit Type</label>
    <select name="cardLimit" class="form-control">
    <?php foreach($cardLimits as $limit) : ?>
        <option value="<?= $limit; ?>" <?php if($config && $config->cardLimit == $limit) echo "selected=''"; ?>><?= $limit; ?></option>
    <?php endforeach; ?>
    </select>
  </div>
   <div class="form-group" id="div_startDate">
    <label for="Name">Start Date</label>
    <input type="text" class="form-control" id="StartDate" name="startDate" placeholder="startDate" <?php if($config) : ?> value="<?= $config->startDate?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="div_endDate">
    <label for="Name">End Date</label>
    <input type="text" class="form-control" id="EndDate" name="endDate" placeholder="endDate" <?php if($config) : ?> value="<?= $config->endDate?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="div_cnt">
    <label for="Name"># of Cards Currently Being Played</label>
    <input type="text" class="form-control" readonly="" <?php if($config) : ?> value="<?= $config->cnt?>"<?php endif; ?>>
  </div>
    </div>
    <div class="panel-footer" style="text-align: right;">
        <button type="button" id="update_lottery" class="btn btn-primary"><?php if($config) : ?>Update<?php else : ?>Add<?php endif; ?> Lottery Config</button>
        <?php if($config) : ?><a class="btn btn-primary" href="/lottery/add_answers/<?=$config->id; ?>" data-target="#modal" data-toggle="modal">Add Answers</a>
        <button type="button" id="grade_cards" class="btn btn-success">Grade Cards</button><?php endif; ?>
    </div>
</form>
</div>

<?php if($rule) : ?>
<div class="panel panel-primary">
    <div class="panel-heading">Rules</div>
    <div class="panel-body">
        <div class="panel panel-default col-lg-6 col-md-6 col-sm-6" style="padding: 0;">
            <div class="panel-heading">Template</div>
            <div class="panel-body">
                <form id="frm_rule">                    
                    <div style="height: 400px;" id="sel_file_name">
                        <select class="form-control" name="sel_file_name" id="sel_fname">
                        <?php foreach($rules as $row) : ?>
                        <option value="<?= $row->ruleURL; ?>"><?= $row->ruleURL; ?></option>
                        <?php endforeach; ?>
                    </select>
                        <textarea class="form-control" id="preview_text" name="text" style="height: 350px;"><?= $rule->template; ?></textarea>
                    </div>
                <div class="form-group" id="div_DeployMobile">
                    <label>
                            Saving Options?
                        </label>
                        <label class="radio-inline">
                            <input type="radio" class="save-options" name="save_options" value="0" checked="">Create New Template
                        </label>
                        <label class="radio-inline">
                            <input type="radio" class="save-options" name="save_options" value="1"> Overwrite Existing Template
                        </label>
                        <label class="radio-inline">
                            <input type="radio" class="save-options" name="save_options" value="2"> Choose Existing Template 
                        </label>
                </div>                
                <input type="hidden" name="rule_id" value="<?= $rule->id; ?>"/>
                <input type="hidden" name="game_type" id="game_type" value="Lottery"/>
                <input type="hidden" name="serial_number" id="serial_number" value="<?= $config->serialNumber; ?>"/>
                </form>
            </div>
            <div class="panel-footer" style="text-align: right;"><button type="button" id="update_rule_template" class="btn btn-primary">Save Template File</button></div>
        </div>
        <div class="panel panel-default  col-lg-6 col-md-6 col-sm-6" style="padding: 0;">
            <div class="panel-heading">Game Rule</div>
            <div class="panel-body">
                <textarea class="form-control" id="txt_xlat_text" style="height: 400px;"><?= $rule->text; ?></textarea>
            </div>
            <div class="panel-footer" style="text-align: right;"><button type="button" id="update_rule_game" class="btn btn-primary">Save Rule File</button></div>
        </div>
    </div>
</div>
<?php elseif($config) : ?>
    <div class="panel panel-primary">
    <div class="panel-heading">Rules</div>
    <div class="panel-body">
    <select class="form-control" name="sel_file_name_none" id="sel_fname_none">
        <?php foreach($rules as $row) : ?>
        <option value="<?= $row->ruleURL; ?>"><?= $row->ruleURL; ?></option>
        <?php endforeach; ?>
    </select>
        <textarea class="form-control" readonly="" id="preview_text_none" style="height: 350px;"></textarea>    
        <input type="hidden" id="game_type_none" value="Lottery"/>
        <input type="hidden" id="serial_type_none" value="<?= $config->serialNumber; ?>"/>
   </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_rule_none" class="btn btn-primary">Create Rules from Template</button></div>
    </div>
<?php endif; ?>

<script>
    var updates = {};
$(function() {
        
         $("#update_rule_none").click(function(){
            var data_send = {
                game_type: $("#game_type_none").val(),
                serial_number: $("#serial_type_none").val(),
                file_name: $("#sel_fname_none").val(),
                name: $("#name").val()
            };
            $.post("/admin/ajax_add_rule", data_send, function(data){
                if(data.success)
                {
                    alert('Rules Updated!');
                    location.reload();
                }
                else
                {
                    alert ("Rules Update Failed");
                }
            }, 'json');
        });
    
        $("#sel_fname").change(function(){
             $.post('/admin/get_preview', {file: $(this).val()}, function(data){
                    $("#preview_text").html(data);                    
                });
        });
        
        $(".save-options").click(function(){
            var val = $(this).val();
            if(val == 2)
                $("#update_rule_template").html("Generate Game Rule File from Template");            
            else
                $("#update_rule_template").html("Save Template File");
        });
                
        $("#update_rule_template").click(function(){
            var selected = $(".save-options:checked");
            if(selected.val() == 2)
            {
                $.post("/admin/ajax_add_rule", {file_name: $("#sel_fname").val(), serial_number: $("#serial_number").val(), game_type: $("#game_type").val(), name: $("#name").val()}, function(data){
                    if(data.success)
                    {
                        alert("Game Rules saved for this Parlay Card");
                        $("#txt_xlat_text").html(data.text);
                    }
                    else
                    {
                        alert("Game Rules were not saved");
                    }
                }, 'json');
            }
            else
            {
                $.post("/admin/ajax_add_rule_template", $("#frm_rule").serialize(), function(data){
                    if(data.success)
                    {
                        alert('Rules Template Updated!');
                        location.reload();
                    }
                    else
                    {
                        alert ("Rules Update Failed");
                    }
                }, 'json');
            }
        });
        
        $("#update_rule_game").click(function(){
            $.post("/admin/ajax_add_rule_game", {text: $("#txt_xlat_text").val(), serial_number: $("#serial_number").val(), startDate: $("#cardDate").val(), endDate: $("#endDate").val(), game_type: $("#game_type").val(), name: $("#serial_number").val()}, function(data){
                if(data.success)
                {
                    alert("Game Rules saved for this Parlay Card");
                    $("#txt_xlat_text").html(data.text);
                }
                else
                {
                    alert("Game Rules were not saved");
                }
            }, 'json');
        });
       
        $("#update_lottery").click(function(){
                $.post('/lottery/ajax_add', $("#frm_lottery").serialize(), function(data){
                        $("#frm_lottery div").removeClass('alert-danger');
                        if(data.success)
                        {
                                $("#payout_message").html("Insert / Update was good.").addClass("alert alert-success").removeClass('alert-danger');
                                $('html,body').scrollTop(0);
                                var command = "window.location = '/lottery/add/"  + data.data.id + "';";
                                setTimeout(command, 1000);
                        }
                        else
                        {
                                $("#payout_message").addClass("alert alert-danger").html("There were errors. They are listed / highlighted below.");
                                for(var key in data.errors)
                                    $("#div_" + key).addClass("alert-danger");
                        }
                },'json');
                
        });
        
        $("#grade_cards").click(function(){
            $.get("/lottery/ajax_grade_cards/" + $("#id").val(), function(data){
                alert(data.message);
            }, 'json');
        });
        
        $('#StartDate').datetimepicker({
            format:'Y-m-d H:i',
            value:'<?php if($config) : ?><?= date("Y-m-d H:i", strtotime($config->startDate)); ?><?php endif; ?>',
            step: 10
          });
          
          $('#EndDate').datetimepicker({
            format:'Y-m-d H:i',
            value:'<?php if($config) : ?><?= date("Y-m-d H:i", strtotime($config->endDate)); ?><?php endif; ?>',
            step: 10
          });
        
        $.post('/admin/get_preview', {file: $("#sel_fname_none").val()}, function(data)
        {
            $("#preview_text_none").html(data);                    
        }); 

    });
   
</script>
<div class="panel panel-primary">    
    <div class="panel-heading"><?php if($config) : ?>Edit<?php else : ?>Add<?php endif; ?> Run of a Lifetime Card</div>
    <div class="panel-body">
        <div id="payout_message"></div>
        <form role="form" id="frm_parlay">
            <?php if($config) : ?><input type="hidden" name="id" id="id" value="<?=$config->id?>"/><?php endif; ?>   
            <div class="form-group" id="div_cardDate">
             <label for="Name">Card Date</label>
             <input type="text" class="form-control" id="cardDate" name="cardDate" placeholder="cardDate" <?php if($config) : ?> value="<?= $config->cardDate?>"<?php endif; ?>>
           </div>
            <div class="form-group" id="div_theme">
             <label for="Name">Theme</label>
             <input type="text" class="form-control" id="theme" name="theme" placeholder="theme" <?php if($config) : ?> value="<?= $config->theme?>"<?php endif; ?>>
           </div>
            <div class="form-group" id="div_disclaimer">
             <label for="Name">Disclaimer</label>
             <input type="text" class="form-control" id="disclaimer" name="disclaimer" placeholder="disclaimer" <?php if($config) : ?> value="<?= $config->disclaimer?>"<?php endif; ?>>
           </div>
        </form>
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_roal" class="btn btn-primary"><?php if($config) : ?>Update<?php else : ?>Add<?php endif; ?> Config</button></div>
</div>

<?php if($config) : ?>
<div class="panel panel-primary">    
    <div class="panel-heading">RoaL Card Events (<?= count($questions); ?>)</div>
    <div class="panel-body">
        <table class="table table-striped table-responsive" id="schedule">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Team 1</th>
                    <th>Team 2</th>
                    <th>Date</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($questions as $question) : ?>
                <tr id="event_<?= $question->id; ?>">
                    <td><?= $question->category; ?></td>
                    <td><?php if(!$config->cnt) :?><?= $question->teamName1; ?><?php else : ?><button id="answer_<?= $question->id . "_" . $question->team1; ?>" class="btn btn-answer btn-<?= $question->id; ?> <?php if($question->answer == $question->team1) echo "btn-success"; else echo "btn-default"; ?>" rel='<?= $question->team1; ?>' question='<?= $question->id; ?>'><?= $question->teamName1; ?></button><?php endif; ?></td>
                    <td><?php if(!$config->cnt) :?><?= $question->teamName2; ?><?php else : ?><button id="answer_<?= $question->id . "_" . $question->team2; ?>" class="btn btn-answer btn-<?= $question->id; ?> <?php if($question->answer == $question->team2) echo "btn-success"; else echo "btn-default"; ?>" rel='<?= $question->team2; ?>' question='<?= $question->id; ?>'><?= $question->teamName2; ?></button><?php endif; ?></td>
                    <td><?= $question->date; ?></td>
                    <td><?= $question->startTime; ?></td>
                    <td><?= $question->endTime; ?></td>
                    <td><?php if(!$config->cnt) :?><button type="button" id="add_event" rel="<?= $question->id; ?>" class="btn btn-danger delete-event">Remove Event</button><?php else : ?>N/A<?php endif; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="panel-footer" style="text-align: right;"><?php if(!$config->cnt) :?><a data-toggle="modal" href="/admin/add_roal_event/<?=$config->id; ?>" data-target="#big-modal" class="btn btn-primary"><span class='glyphicon glyphicon-plus'></span> Add Event</a><?php else : ?><button class="btn btn-primary" id="grade_cards">Grade Cards</button><?php endif; ?></div>
</div>
<?php endif; ?>

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
                <input type="hidden" name="game_type" id="game_type" value="ROAL"/>
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
        <input type="hidden" id="game_type_none" value="ROAL"/>
        <input type="hidden" id="serial_type_none" value="<?= $config->serialNumber; ?>"/>
   </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_rule_none" class="btn btn-primary">Create Rules from Template</button></div>
    </div>
<?php endif; ?>

<script>
    var updates = {};
$(function() {
        var fixHelper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        };

        $("#type").change(function(){
            $.get("/admin_sports/ajax_get_disclaimer/" + $(this).val(), {}, function(data){
                $("#disclaimer").val(data);
            }, 'html');
        });
        $("#schedule tbody").sortable({
            helper: fixHelper,
            stop: function(e, ui){
                $("#save_order").prop("disabled", false);
            }
        }).disableSelection();
        
        $("#save_order").click(function(){
            var ids = [];
            $("#save_order").prop("disabled", true);
            $("#schedule tr").each(function(){
                ids.push(this.id);
            });
            
            $.post("/admin_sports/ajax_save_sequence", {ids: ids, parlayCardId: $("#parlayCardId").val()}, function(data){
                if(data.success)
                {                    
                    alert("Schedule Order Saved");
                }
            }, 'json');
        })
          
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
        
        $(".btn-update-place").click(function(){
            var id = $(this).attr("rel");
            var post = {
                rank: $("#place_rank_" + id).val(),
                prize: $("#place_prize_" + id).val(),            
                id: id
            };

            $.post("/admin_sports/ajax_add_parlay_place", post, function(data){
                if(data.success)
                {
                    alert("Place Updated");
                }
            }, 'json');
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
        
        $(".delete-event").click(function(){
            
                var id = $(this).attr("rel");
                $.get("/admin/ajax_delete_roal_event/" + id, {}, function(data){
                    if(data.success)
                    {
                        $("#event_" + id).fadeOut();
                    }
                    else
                    {
                        alert("Something went wrong");
                    }
                }, 'json');
        });
        
        $("#grade_cards").click(function(){
            $.get("/admin/ajax_grade_roal/" + $("#id").val(), {}, function(data){
                alert("User Answers have been graded.");
            }, 'json');
        });
        
        $(".btn-answer").click(function(){
            var questionId = $(this).attr('question');
            var answer = $(this).attr('rel');
           $.post("/admin/ajax_add_roal_answer", {questionId: questionId,answer: answer}, function(data){
               $(".btn-" + questionId).removeClass("btn-success").addClass("btn-default");               
               $("#answer_" + questionId + "_" + answer).addClass("btn-success").removeClass("btn-default");
           });
        });
        
        $("#update_roal").click(function(){
                $.post('/admin/ajax_add_roal', $("#frm_parlay").serialize(), function(data){
                        $("#frm_parlay div").removeClass('alert-danger');
                        if(data.success)
                        {
                                $("#payout_message").html("Insert / Update was good.").addClass("alert alert-success").removeClass('alert-danger');
                                $('html,body').scrollTop(0);
                                var command = "window.location = '/admin/edit_roal/"  + data.id + "';";
                                setTimeout(command, 1000);
                        }
                        else
                        {
                                alert(data.message);
                        }
                },'json');
                
        });
        
        window.onbeforeunload = function(){
            var has_keys = false;
            for(var prop in updates)
                has_keys = true;
            
            if(has_keys)
            {
                return "There are records that haven't been saved, are you sure you want to leave?";
            }
        };
        
        $( "#cardDate" ).datepicker({
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            numberOfMonths: 1
        });        
        
        $.post('/admin/get_preview', {file: $("#sel_fname_none").val()}, function(data){
                    $("#preview_text_none").html(data);                    
                }); 

    });   
</script>
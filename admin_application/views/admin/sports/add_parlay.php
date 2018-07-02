<div class="panel panel-primary">    
    <div class="panel-heading"><?php if($config) : ?>Edit<?php else : ?>Add<?php endif; ?> Parlay Card</div>
    <div class="panel-body">
        <div id="payout_message"></div>
        <form role="form" id="frm_parlay">
   <?php if($config) : ?><input type="hidden" name="id" value="<?=$config->id?>"/><?php endif; ?>
   <?php if($config) : ?><input type="hidden" id="parlayCardId" name="parlayCardId" value="<?=$config->parlayCardId?>"/><?php endif; ?>
   <div class="form-group" id="div_cardWin">
    <label for="Name">Card Win (Number only, no commas)</label>
    <input type="text" class="form-control" id="cardWin" name="cardWin" placeholder="cardWin" <?php if($config) : ?> value="<?= $config->cardWin?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="div_cardDate">
    <label for="Name">Card Date</label>
    <input type="text" class="form-control" id="cardDate" name="cardDate" placeholder="cardDate" <?php if($config) : ?> value="<?= $config->cardDate?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="div_endDate">
    <label for="Name">End Date</label>
    <input type="text" class="form-control" id="endDate" readonly="" placeholder="endDate" <?php if($config) : ?> value="<?= $config->endDate?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="div_type">
    <label for="Name">Type</label>
    <select class="form-control" id="type" name="type">
        <?php foreach($types as $type) : ?>
        <option value="<?= $type; ?>" <?php if($config && $config->type == $type) echo "selected=''"; ?>><?= $type; ?></option>
        <?php endforeach; ?>
    </select>
  </div>
   <div class="form-group" id="div_week" <?php if(($config && ($config->type == "profootball2016" || $config->type == "collegefootball2016")) || !$config) : ?> <?php else : ?>  style="display: none;" <?php endif; ?>>
    <label for="Name">Week</label>
    <input type="text" class="form-control" id="week" name="week" placeholder="week" <?php if($config) : ?> value="<?= $config->week?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="div_maxCardCount">
    <label for="Name">Max Card Count</label>
    <input type="text" class="form-control" id="maxCardCount" name="maxCardCount" placeholder="# of cards per day" <?php if($config) : ?> value="<?= $config->maxCardCount?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="div_adPlacement">
    <label for="WinningSpots">Ad Placement</label>
    <select name="adPlacement" id="adPlacement" class="form-control">
        <?php foreach($adPlacements as $adPlacement) : ?>
        <option value="<?= $adPlacement; ?>" <?php if($config && $config->adPlacement == $adPlacement) echo 'selected="selected"'; ?>><?= $adPlacement; ?></option>
        <?php endforeach; ?>
    </select>
    </div>
   <div class="form-group" id="div_disclaimer">
    <label for="Name">Disclaimer</label>
    <textarea type="text" class="form-control" id="disclaimer" name="disclaimer" placeholder="disclaimer"><?php if($config) : ?><?= $config->disclaimer?><?php endif; ?></textarea>
  </div>
   <div class="form-group" id="div_isActive">
        <label>
                Is Active?
            </label>
            <label class="radio-inline">
                <input type="radio" name="isActive" value="0" <?php if(!$config) echo "disabled=''"; ?> <?php if($config && $config->isActive == 0) : ?>checked="checked"<?php endif; ?>> No
            </label>
            <label class="radio-inline">
                <input type="radio" name="isActive" value="1" <?php if(!$config) echo "disabled=''"; ?> <?php if($config && $config->isActive == 1) : ?>checked="checked"<?php endif; ?>> Yes
            </label>
    </div>
   <div class="form-group" id="div_cnt">
    <label for="Name"># of Cards Currently Being Played</label>
    <input type="text" class="form-control" readonly="" <?php if($config) : ?> value="<?= $config->cnt?>"<?php endif; ?>>
  </div>
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_parlay" class="btn btn-primary"><?php if($config) : ?>Update<?php else : ?>Add<?php endif; ?> Parlay Config</button></div>
</form>
</div>

<?php if($config) : ?>
<div class="panel panel-primary">    
    <div class="panel-heading">Parlay Card Events (<?= count($details); ?>)</div>
    <div class="panel-body">
        <?php if(!$config->cnt) : ?>
        <table class="table table-striped table-responsive" id="schedule">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Question</th>
                    <th>Team 1</th>
                    <th>Team 2</th>
                    <th>Date</th>
                    <th>Over/Under</th>
                    <th>Spread</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($details as $detail) : ?>
                <tr id="event_<?= $detail->event_id; ?>">
                    <td><?= $detail->category; ?></td>
                    <td><?= $detail->question; ?></td>
                    <td><?= $detail->team1Name; ?></td>
                    <td><?= $detail->team2Name; ?></td>
                    <td><?= $detail->date; ?></td>
                    <td><?php if($detail->overUnderScore) echo $detail->overUnderScore; else echo "-"; ?></td>
                    <td><?= $detail->spread; ?></td>
                    <td><?php if(!$config->cnt) :?><button type="button" id="add_event" rel="<?= $detail->event_id; ?>" class="btn btn-danger delete-event">Remove Event</button><?php else : ?>N/A<?php endif; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else : ?>
        <table id="schedule" class="table table-striped">
            <thead>
                <tr>        
                    <th>ID</th>
                    <th>Question</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Team 1</th>           
                    <th>Team 2</th>   
                    <th>Tie</th>  
                    <th>Over/Under</th>
                    <th>Spread</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                    <?php foreach($details as $detail) : ?>
                <tr id="tr_<?= $detail->event_id;?>">     
                    <td><?= $detail->sgr_id;?></td>
                    <td><?= $detail->question; ?></td>
                    <td><?= $detail->date; ?> <input type="hidden" id="<?= $detail->event_id;?>_parlay_id" value="<?= $detail->parlay_ids; ?>"/><input type="hidden" id="<?= $detail->event_id;?>_sportScheduleId" value="<?= $detail->sportScheduleId; ?>"/></td>
                    <td><?= $detail->category; ?><input type="hidden" id="<?= $detail->event_id;?>_team2" value="<?= $detail->team2;?>"/><input type="hidden" id="<?= $detail->event_id;?>_team1" value="<?= $detail->team1;?>"/></td>
                    <?php if(!$detail->overUnderScore) : ?>
                    <td><button id="btn_<?= $detail->event_id;?>_<?= $detail->team1;?>" class="btn <?php if(!$detail->is_done) : ?>btn-default<?php elseif($detail->is_done && $detail->winner == $detail->team1) : ?>btn-success<?php else : ?>btn-danger<?php endif; ?>" onclick="pick_winner(<?= $detail->event_id;?>, <?= $detail->team1;?>)"><?= $detail->team1Name; ?> (<?= $detail->team1;?>)</button></td>            
                    <td><button id="btn_<?= $detail->event_id;?>_<?= $detail->team2;?>" class="btn <?php if(!$detail->is_done) : ?>btn-default<?php elseif($detail->is_done && $detail->winner == $detail->team2) : ?>btn-success<?php else : ?>btn-danger<?php endif; ?>" onclick="pick_winner(<?= $detail->event_id;?>, <?= $detail->team2;?>)"><?= $detail->team2Name; ?> (<?= $detail->team2;?>)</button></td>           
                    <td><button id="btn_<?= $detail->event_id;?>_0"class="btn <?php if(!$detail->is_done) : ?>btn-default<?php elseif($detail->is_done && $detail->winner == 0) : ?>btn-success<?php else : ?>btn-danger<?php endif; ?>" onclick="pick_winner(<?= $detail->event_id;?>, 0)">TIE</button></td>
                    <td>-</td>
                    <?php else : ?>
                    <td><button id="btn_<?= $detail->event_id;?>_<?= $detail->team1;?>" class="btn <?php if(!$detail->is_done) : ?>btn-default<?php elseif($detail->is_done && $detail->winner == $detail->team1) : ?>btn-success<?php else : ?>btn-danger<?php endif; ?>" onclick="pick_winner(<?= $detail->event_id;?>, <?= $detail->team1;?>)">UNDER (<?= $detail->team1;?>)</button></td>            
                    <td><button id="btn_<?= $detail->event_id;?>_<?= $detail->team2;?>" class="btn <?php if(!$detail->is_done) : ?>btn-default<?php elseif($detail->is_done && $detail->winner == $detail->team2) : ?>btn-success<?php else : ?>btn-danger<?php endif; ?>" onclick="pick_winner(<?= $detail->event_id;?>, <?= $detail->team2;?>)">OVER (<?= $detail->team2;?>)</button></td>           
                    <td><button id="btn_<?= $detail->event_id;?>_0"class="btn <?php if(!$detail->is_done) : ?>btn-default<?php elseif($detail->is_done && $detail->winner == 0) : ?>btn-success<?php else : ?>btn-danger<?php endif; ?>" onclick="pick_winner(<?= $detail->event_id;?>, 0)">TIE</button></td>
                    <td>Over/Under: <?= $detail->overUnderScore; ?><br><?= $detail->team1Name; ?> VS. <?= $detail->team2Name; ?></td>
                    <?php endif; ?>
                    <td><?= $detail->spread; ?></td>
                    <td><button type="button" id="add_event" rel="<?= $detail->event_id; ?>" class="btn btn-danger delete-event">Remove Event</button></td>
                </tr>
                    <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <div class="panel-footer" style="text-align: right;"><button id="save_order" class="btn btn-success" style="margin-right: 20px;" disabled="">Save Order</button><?php if(!$config->cnt) :?><a data-toggle="modal" href="/admin_sports/add_parlay_event/<?=$config->parlayCardId; ?>" data-target="#big-modal" class="btn btn-primary"><span class='glyphicon glyphicon-plus'></span> Add Event</a><?php else : ?><button class="btn btn-primary" id="update_events">Update Events</button><?php endif; ?></div>
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
                <input type="hidden" name="game_type" id="game_type" value="Parlay"/>
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
        <input type="hidden" id="game_type_none" value="Parlay"/>
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
            if($(this).val() == "profootball2016" || $(this).val() == "collegefootball2016")
                $("#div_week").show();
            else
                $("#div_week").hide();
            
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
                $.get("/admin_sports/ajax_delete_parlay_event/" + id, {}, function(data){
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
        
        $(".redo-pick").click(function(){
            var id = $(this).attr('rel');
            $.get("/admin_sports/remove_answer/" + id, {}, function(data){
                if(data.success)
                    location.reload();
            }, 'json');
        });
    
        $("#update_parlay").click(function(){
                $.post('/admin_sports/ajax_add_parlay', $("#frm_parlay").serialize(), function(data){
                        $("#frm_parlay div").removeClass('alert-danger');
                        if(data.success)
                        {
                                $("#payout_message").html("Insert / Update was good.").addClass("alert alert-success").removeClass('alert-danger');
                                $('html,body').scrollTop(0);
                                var command = "window.location = '/admin_sports/add_parlay/"  + data.id + "';";
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
        
        $("#update_events").click(function(){

            $.post("/admin_sports/ajax_add_event_scores", updates, function(data){
                if(data.success)
                {
                    updates = {};
                    alert("All records have been updated.");
                }
                else
                {
                    var mess = "";
                    for(var key in data.errors)
                        mess = mess + key + ": " + data.errors[key] + "\n";
                    alert("Errors were as follows:\n" + mess);
                }
            }, 'json');

        });
        
        $(".btn-delete-place").click(function(){
            var id = $(this).attr('rel');
            $.get("/admin_sports/ajax_delete_parlay_place/" + id, {}, function(data){
                    $("#tr_prize_" + id).fadeOut();
            }, 'json');
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

   function pick_winner(id, team_id)
    {
        var team1 = 1;
        var team2 = 1;
        
        if(team_id === parseInt($("#" + id + "_team1").val()))
            team1 = 2;
        else if(team_id === parseInt($("#" + id + "_team2").val()))
            team2 = 2;
        var data = {
            parlay_id: $("#" + id + "_parlay_id").val(),
            event_id: id,
            team1_score: team1,
            team2_score: team2,
            team1: $("#" + id + "_team1").val(),
            team2: $("#" + id + "_team2").val(),
            sportScheduleId: $("#" + id + "_sportScheduleId").val()
        };
        $("#tr_" + id + " button").removeClass("btn-default btn-success");
        $("#tr_" + id + " button").addClass("btn-danger");
        $("#btn_" + id + "_" + team_id).removeClass("btn-danger").addClass("btn-success");
        updates[id] = data;
        //console.log(updates);        
    }        
</script>
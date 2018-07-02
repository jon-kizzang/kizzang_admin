<link href="/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<script src="/js/jquery.datetimepicker.js"></script>
<link href="/css/bracket.css" rel="stylesheet" type="text/css"/>
<script src="/js/bracket.js"></script>
<style>
    .label {color: #000;}
    .tools {display: none;}
</style>

<div class="panel panel-primary">    
    <div class="panel-heading"><?php if($config) : ?>Edit<?php else : ?>Add<?php endif; ?> Bracket Config</div>
    <div class="panel-body">
        <div id="config_message"></div>
   <form role="form" id="frm_bgconfig">
   <?php if($config) : ?><input type="hidden" name="id" id="id" value="<?=$config->id?>"/><?php endif; ?>
   <div class="form-group" id="divc_name">
    <label for="Name">Name</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="name" <?php if($config) : ?> value="<?= $config->name?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="divc_theme">
    <label for="Theme">Theme</label>
    <input type="text" class="form-control" id="theme" name="theme" placeholder="theme" <?php if($config) : ?> value="<?= $config->theme?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="divc_startDate">
    <label for="StartDate">Start Date</label>
    <input type="text" class="form-control" id="StartDate" name="startDate" placeholder="StartDate" value="<?php if($config) echo date("Y-m-d H:i", strtotime($config->startDate)); ?>">
  </div>
    <div class="form-group" id="divc_endDate">
    <label for="EndDate">End Date</label>
    <input type="text" class="form-control" id="EndDate" name="endDate" placeholder="EndDate" value="<?php if($config) echo date("Y-m-d H:i", strtotime($config->endDate)); ?>">
  </div>  
   <div class="form-group" id="divc_numStartingTeams">
    <label for="Name"># of Teams</label>
    <input type="text" class="form-control" id="numStartingTeams" name="numStartingTeams" placeholder="# of Starting Teams" <?php if($config) : ?> value="<?= $config->numStartingTeams?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="divc_cardWin">
    <label for="sportCategoryId">Sport Category</label>
    <select class="form-control" id="sportCategoryId" name="sportCategoryId">
        <?php foreach($categories as $category) : ?>
        <option value="<?= $category->id; ?>" <?php if($config && $config->sportCategoryId == $category->id) echo "selected=''"; ?>><?= $category->name; ?></option>
        <?php endforeach; ?>
    </select>
  </div>
   <?php if($count) : ?>
   <div class="form-group" id="divc_count">
    <label>Count</label>
    <input type="text" class="form-control" value="<?= $count?>" readonly="">
  </div>
   <?php endif; ?>
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="add_config" class="btn btn-primary"><?php if($config) : ?>Update<?php else : ?>Add<?php endif; ?> Config</button></div>
</form>
</div>

<?php if($config) : ?>
<div class="panel panel-primary">    
    <div class="panel-heading">Initial Teams</div>
    <div class="panel-body">
        <div class="well">
            <label>Select Division: </label>
            <select id="divisions">
                <option value="">Select Division</option>
                <?php foreach($divisions as $division) : ?>
                <option value="<?= $division; ?>"><?= $division; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div id="teams">
            
        </div>
    </div>
    <div class="panel-footer" style="text-align: right;"><div class="btn btn-success" id="rec_status" style="display:none;">Record Added</div><button class="btn btn-primary" id="add_matchup">Add Matchup</button></div>
</div>
<?php endif; ?>

<?php if($config) : ?>
<div class="panel panel-primary">    
    <div class="panel-heading">Bracket Times</div>
    <div class="panel-body">
        <table class="table table-striped" id="bracket_dates">
            <tr>
                <th>Round (number only)</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Add / Update</th>
            </tr>
            <?php foreach($dates as $date) : ?>
            <tr id="date_<?= $date->id; ?>">
                <td><input type="text" value="<?= $date->round; ?>" id="round_<?= $date->id; ?>"/></td>
                <td><input type="text" class="datePopup" value="<?= $date->startDate; ?>" id="startDate_<?= $date->id; ?>"/></td>
                <td><input type="text" class="datePopup" value="<?= $date->endDate; ?>" id="endDate_<?= $date->id; ?>"/></td>
                <td><button class="updateAddDates" rel="<?= $date->id; ?>" isNew="0">Update</button></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="panel-footer" style="text-align: right;"><button class="btn btn-primary" id="add_dates">Add Date</button></div>
</div>
<?php endif; ?>

<?php if($config) : ?>
<div class="panel panel-primary">    
    <div class="panel-heading">Bracket</div>
    <div class="panel-body">
        <div id="left_bracket" style="float:left;"></div>
        <div id="right_bracket" style="float: right;"></div>
        <?php if($left_winner && $right_winner) : ?>
        <div id="final_score" style="position: relative; top: -150px; left: 680px; background-color: none; width: 300px; height: 200px;">
            <label>Select Final Winner:</label><br/><br/>
            <?= $left_winner->name; ?> <input class="champion" type="radio" name="champion" value="<?= $left_winner->id; ?>" <?php if($left_winner->id == $config->champion_id) echo "checked=''"?>/><br/>
            <?= $right_winner->name; ?> <input class="champion" type="radio" name="champion" value="<?= $right_winner->id; ?>"  <?php if($right_winner->id == $config->champion_id) echo "checked=''"?>/>
        </div>
        <?php endif; ?>
    </div>
    <div class="panel-footer" style="text-align: right;"><button class="btn btn-primary" id="grade_cards">Grade Cards</button></div>
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
                <input type="hidden" name="game_type" id="game_type" value="BG"/>
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
        <input type="hidden" id="game_type_none" value="Bracket"/>
        <input type="hidden" id="serial_type_none" value="<?= $config->serialNumber; ?>"/>
   </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_rule_none" class="btn btn-primary">Create Rules from Template</button></div>
    </div>
<?php endif; ?>

<script>
    
    $(".champion").click(function(){
       $.post("/admin_sports/ajax_update_bracket_champion", {id: $("#id").val(), champion_id: $(this).val()}, function(data){
           if(data.success)
               alert("Champion Selected!");
           else
               alert("An Error Occurred");
       }, 'json');
    });
    
    $("#add_dates").click(function(){
       $.get("/admin_sports/ajax_add_round_date/" + $("#id").val(), {}, function(data){
           $("#bracket_dates").append(data);
       }, 'html'); 
    });
    
    $("#grade_cards").click(function(){
       $.get("/admin_sports/ajax_grad_cards/" + $("#id").val(), {}, function(data){
           alert("Cards Graded");
       }, 'json'); 
    });    
    
    $(document).ready(function(){
        $('body').click(function(e){
            if($(e.target).is(".datePopup"))
            {
                $(".datePopup").datetimepicker({
                    format:'Y-m-d H:i',            
                    step: 60
                  });       
            }
        })
    })
    
    $(document).on('click', '.updateAddDates', function(e){
       e.preventDefault(); 
       var id = $(this).attr('rel');
       var isNew = $(this).attr('isNew');
       var data = {
           id: id,
           isNew: isNew,
           bracketConfigId: $("#id").val(),
           round: $("#round_" + id).val(),
           startDate: $("#startDate_" + id).val(),
           endDate: $("#endDate_" + id).val()
       };
       $.post("/admin_sports/ajax_update_round_date", data, function(ret){
           if(ret.success)
               alert("Date updated/added");
           else
               alert("Record not updated");           
           
           location.reload();          
        }, 'json');
    });
    
    $("#add_matchup").click(function(){
        $.get("/admin_sports/ajax_add_bracket_team/" + $("#id").val(), {}, function(data){
            $("#teams").append(data);
        }, 'html');
    });
    
    $("#divisions").change(function(){
        $.get("/admin_sports/ajax_get_bracket_team/" + $("#id").val() + "/" + $("#divisions").val(), {}, function(data){
            $("#teams").html(data);
        }, 'html');
    });         
     
    $(document).on('click', '.matchup-delete', function(e){
        e.preventDefault();
        var id = $(this).attr('rel');
        var rec_id = $("#bracketId_" + id).val();
        if(!rec_id)
        {
            $("div_" + id).remove();
        }
        else
        {
            $.get("/admin_sports/ajax_delete_bracket_matchup/" . rec_id, {}, function(data){
                if(data.success)
                {
                    $("div_" + id).remove();
                    alert("Record Deleted");
                }
            }, 'json');
        }
    });
    
    $(document).on('click', '.matchup-update', function(e){
        e.preventDefault();
       var id = $(this).attr('rel');
       var data = {
           id: $("#bracketId_" + id).val(),
           bracketConfigId: $("#id").val(),
           division: $("#divisions").val(),
           teamId1: $("#teamId1_" + id).val(),
           teamRank1: $("#teamRank1_" + id).val(),
           teamId2: $("#teamId2_" + id).val(),
           teamRank2: $("#teamRank2_" + id).val()
       };
       $.post("/admin_sports/ajax_add_bracket_matchup", data, function(data){
           if(data.success)
           {
               $("#rec_status").show();
               $.get("/admin_sports/ajax_get_bracket_team/" + $("#id").val() + "/" + $("#divisions").val(), {}, function(data){
                    $("#teams").html(data);
                }, 'html');
                setTimeout('$("#rec_status").fadeOut("slow")', 2000);
           }
       }, 'json');
    });
    
    var leftData = {
    teams : [
      <?= $left_bracket; ?>      
    ],
    results : <?php if($config && $config->left_answers) echo $config->left_answers; else echo "[]"; ?>
  };
  
  var rightData = {
    teams : [
      <?= $right_bracket; ?>
    ],
    results : <?php if($config && $config->right_answers) echo $config->right_answers; else echo "[]"; ?>
  };
 
/* Called whenever bracket is modified
 *
 * data:     changed bracket object in format given to init
 * userData: optional data given when bracket is created.
 */
function saveFn(data, userData) {  
  $.post(userData, data, function(){
      
  }, 'json');  
}
 
$(function() {
    var container = $('#left_bracket')
    container.bracket({
      init: leftData,
      save: saveFn,
      userData: "/admin_sports/ajax_save_bracket_answers/" + $("#id").val() + "/left"});
  });
  $(function() {
    var container = $('#right_bracket')
    container.bracket({
      init: rightData,
      dir: 'rl',
      save: saveFn,
      userData: "/admin_sports/ajax_save_bracket_answers/" + $("#id").val() + "/right"})
  });
  
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
        
    $("#add_config").click(function(){
        $.post("/admin_sports/ajax_add_bracket_config", $("#frm_bgconfig").serialize(), function(data){
            if(data.success)
            {
                location.reload();
            }
            else
            {
                alert("There was an error while saving the config.  Please check the information.");
            }
        }, 'json');
    });
    
    $(".btn-delete-place").click(function(){
        var id = $(this).attr('rel');
        $.get("/admin_sports/ajax_delete_bg_place/" + id, {}, function(data){
                $("#tr_prize_" + id).fadeOut();
        }, 'json');
    });
    
    $(".btn-update-place").click(function(){
            var id = $(this).attr("rel");
            var post = {
                rank: $("#place_rank_" + id).val(),
                prize: $("#place_prize_" + id).val(),            
                id: id
            };

            $.post("/admin_sports/ajax_add_bg_place", post, function(data){
                if(data.success)
                {
                    alert("Place Updated");
                }
            }, 'json');
        });
    
    $(".delete-question").click(function(){
        var id = $(this).attr("rel");
        $.post("/admin_sports/ajax_delete_bg_question", {question_id: id}, function(data){
            $("#tr_" + id).remove();
        });
    });
    
    $("#add_answer").click(function(){
        var answer = prompt("Please Enter in the Answer Below:");
        if(answer)
        {
            var question_id = $(this).attr('rel');
            $.post("/admin_sports/ajax_add_bg_answer", {answer: answer, questionId: question_id}, function(data){
                if(data.success)
                {
                    $("#tbl_answer").append(data.row);
                }
            }, 'json');
        }
    });
    
    $('#StartDate').datetimepicker({
            format:'Y-m-d H:i',            
            step: 5
          });
    $('#EndDate').datetimepicker({
            format:'Y-m-d H:i',            
            step: 5
          });       
    
</script>
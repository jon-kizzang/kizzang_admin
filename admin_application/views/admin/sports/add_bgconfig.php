<link href="/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<script src="/js/jquery.datetimepicker.js"></script>

<div class="panel panel-primary">    
    <div class="panel-heading"><?php if($config) : ?>Edit<?php else : ?>Add<?php endif; ?> Config</div>
    <div class="panel-body">
        <div id="config_message"></div>
   <form role="form" id="frm_bgconfig">
   <?php if($config) : ?><input type="hidden" name="id" value="<?=$config->id?>"/><?php endif; ?>
   <div class="form-group" id="divc_name">
    <label for="Name">Name</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="name" <?php if($config) : ?> value="<?= $config->name?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="divc_startDate">
    <label for="StartDate">Start Date</label>
    <input type="text" class="form-control" id="StartDate" name="startDate" placeholder="StartDate" value="<?php if($config) echo date("Y-m-d H:i", strtotime($config->startDate)); ?>">
  </div>
    <div class="form-group" id="divc_endDate">
    <label for="EndDate">End Date</label>
    <input type="text" class="form-control" id="EndDate" name="endDate" placeholder="EndDate" value="<?php if($config) echo date("Y-m-d H:i", strtotime($config->endDate)); ?>">
  </div>  
   <div class="form-group" id="divc_cardWin">
    <label for="Name">Card Win</label>
    <input type="text" class="form-control" id="cardWin" name="cardWin" placeholder="cardWin" <?php if($config) : ?> value="<?= $config->cardWin?>"<?php endif; ?>>
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
<div class="panel panel-primary" style="margin-bottom: 0px;">    
    <div class="panel-heading">Questions</div>
    <div class="panel-body">
        <table class="table table-striped table-responsive">
            <thead>
                <tr>
                    <th>Question</th>                    
                    <th>Answers</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($questions as $question) : ?>
                <tr id="tr_<?= $question->id; ?>">
                    <td><?= $question->question; ?></td>
                    <td><?= $question->answers; ?></td>
                    <td><a href="/admin_sports/add_bg_question/<?=$config->id?>/<?= $question->id; ?>" class="btn btn-primary" data-toggle="modal" data-target="#modal">Edit</a></td>
                    <td><button type="button" rel="<?= $question->id; ?>" class="btn btn-danger delete-question">Remove</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="panel-footer" style="text-align: right;"><a href="/admin_sports/add_bg_question/<?=$config->id?>" class="btn btn-primary" data-toggle="modal" data-target="#modal">Add Question</a></div>
</div>
<?php endif; ?>

<?php if($config) : ?>
<div class="panel panel-primary">    
    <div class="panel-heading">Prizes</div>
    <div class="panel-body">
        <table class="table table-striped table-responsive" id="tbl_place">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Prize</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($places as $place) : ?>
                <tr id="tr_prize_<?= $place->id; ?>">
                    <td><input type="text" id="place_rank_<?= $place->id; ?>" class="form-control" value="<?= $place->rank; ?>"/></td>
                    <td><input type="text" id="place_prize_<?= $place->id; ?>" class="form-control" value="<?= $place->prize; ?>"/></td>
                    <td><button class="btn btn-primary btn-update-place" rel="<?= $place->id; ?>">Update</button></td>
                    <td><button type="button" rel="<?= $place->id; ?>" class="btn btn-danger btn-delete-place">Remove</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="panel-footer" style="text-align: right;"><a href="/admin_sports/add_bg_place/<?=$config->parlayCardId?>" class="btn btn-primary" data-toggle="modal" data-target="#modal">Add Place</a></div>
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
        <input type="hidden" id="game_type_none" value="BG"/>
        <input type="hidden" id="serial_type_none" value="<?= $config->serialNumber; ?>"/>
   </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_rule_none" class="btn btn-primary">Create Rules from Template</button></div>
    </div>
<?php endif; ?>

<script>
    
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
        $.post("/admin_sports/ajax_add_bg_config", $("#frm_bgconfig").serialize(), function(data){
            if(data.success)
            {
                window.location = "/admin_sports/add_bg_config/" + data.id;
            }
            else
            {
                $("#config_message").addClass("alert alert-danger").html("There were errors. They are listed / highlighted below.");
                for(var key in data.errors)
                    $("#divc_" + key).addClass("alert-danger");
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
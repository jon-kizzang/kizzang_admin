<link href="/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<script src="/js/jquery.datetimepicker.js"></script>

<div class="panel panel-primary">
    <form role="form" id="frm_tournament">
    <div class="panel-heading"><?php if($tournament) : ?>Edit<?php else : ?>Add<?php endif;?> Tournament</div>
    <div class="panel-body">
        <div id="game_message"></div>        
            <?php if($tournament) : ?> <input type="hidden" name="ID" value="<?=$tournament->ID?>"/> <?php endif; ?>    
    <div class="form-group" id="div_startDate">
    <label for="StartDate">Start Date</label>
    <input type="text" class="form-control" id="StartDate" name="StartDate" placeholder="StartDate" value="<?php if($tournament) echo date("Y-m-d H:i", strtotime($tournament->StartDate)); ?>">
  </div>
    <div class="form-group" id="div_endDate">
        <label for="EndDate">End Date</label>
        <input type="text" class="form-control" id="EndDate" name="EndDate" placeholder="EndDate" value="<?php if($tournament) echo date("Y-m-d H:i", strtotime($tournament->EndDate) + 1); ?>">
    </div>  
    <div class="form-group" id="div_type">
        <label for="type">Tournament Type</label>
        <select class="form-control" id="type" name="type">
            <?php foreach($types as $type) : ?>
            <option value="<?= $type; ?>" <?php if($tournament && $type == $tournament->type) echo "selected=''"; ?>><?= $type; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group" id="div_title">
        <label for="StartDate">Title</label>
        <input type="text" class="form-control" id="Title" name="Title" placeholder="Title" value="<?php if($tournament) echo $tournament->Title; ?>">
    </div>
    <div class="form-group" id="div_type">
        <label>Select Games for this Tournament</label>
        <?php foreach($games as $game) : ?>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="games[]" value="<?= $game->Theme; ?>" <?php if(!$tournament || (in_array($game->Theme, $tournament->games))) echo "checked=''"; ?>> <?= $game->Name; ?>
                </label>
            </div>
        <?php endforeach; ?>
    </div>

<div class="panel panel-primary">
    <div class="panel-heading">Game Prizes</div>
    <div class="panel-body">
        <table class="table table-striped" id="tbl_prize">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Prize</th>  
                    <th>Remove</th>
                </tr>
            </thead>
            <?php foreach($prizes as $key => $prize) : ?>
            <tr id="tr_<?= $key; ?>">
                <td>
                    <select class="form-control" name="ranks[]">
                    <?php for($i = 1; $i < 11; $i ++) : ?>
                        <option value="<?= $i; ?>" <?php if($i == $prize['Rank']) echo 'selected=""'; ?>><?= $i; ?></option>
                    <?php endfor; ?>
                    </select>                    
                </td>
                <td><input type="text" name="prizes[]" class="form-control" value="<?= $prize['Prize']?>"/></td>
                <td><button class="btn btn-danger remove-prize" rel="<?= $key; ?>">Remove</button></td>
            </tr>
            <?php endforeach;?>
        </table>
        <input type="hidden" id="row_num" value="<?=count($prizes); ?>"/>
    </div>
    <div class="panel-footer" style="text-align: right;"><a href="javascript:void(0);" class="btn btn-primary" id="btn_add_prize"> Add Prize</a></div>
</div>
</div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_show" class="btn btn-primary"><?php if($tournament) : ?><span class='glyphicon glyphicon-floppy-save'></span> Update<?php else : ?><span class='glyphicon glyphicon-plus'></span> Add<?php endif;?> Slot Tournament</button></div>
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
                <input type="hidden" name="game_type" id="game_type" value="Slots"/>
                <input type="hidden" name="serial_number" id="serial_number" value="<?= $tournament->SerialNumber; ?>"/>
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
<?php elseif($tournament) : ?>
    <div class="panel panel-primary">
    <div class="panel-heading">Rules</div>
    <div class="panel-body">
    <select class="form-control" name="sel_file_name_none" id="sel_fname_none">
        <?php foreach($rules as $row) : ?>
        <option value="<?= $row->ruleURL; ?>"><?= $row->ruleURL; ?></option>
        <?php endforeach; ?>
    </select>
        <textarea class="form-control" readonly="" id="preview_text_none" style="height: 350px;"></textarea>    
        <input type="hidden" id="game_type_none" value="Slots"/>
        <input type="hidden" id="serial_type_none" value="<?= $tournament->SerialNumber; ?>"/>
   </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_rule_none" class="btn btn-primary">Create Rules from Template</button></div>
    </div>
<?php endif; ?>

<script>
   
$(function() {
        $("#update_rule_none").click(function(){
            var data_send = {
                game_type: $("#game_type_none").val(),
                serial_number: $("#serial_type_none").val(),
                file_name: $("#sel_fname_none").val()
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
                $.post("/admin/ajax_add_rule", {file_name: $("#sel_fname").val(), serial_number: $("#serial_number").val(), game_type: $("#game_type").val()}, function(data){
                    if(data.success)
                    {
                        alert("Game Rules saved for this Slot");
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
            $.post("/admin/ajax_add_rule_game", {text: $("#txt_xlat_text").val(), serial_number: $("#serial_number").val(), name: $("#serial_number").val(), startDate: $("#StartDate").val(), endDate: $("#EndDate").val(), game_type: $("#game_type").val()}, function(data){
                if(data.success)
                {
                    alert("Game Rules saved for this Slot Tournament");
                    $("#txt_xlat_text").html(data.text);
                }
                else
                {
                    alert("Game Rules were not saved");
                }
            }, 'json');
        });
        
        $("#btn_add_prize").click(function(){
            var id = $("#row_num").val();
            $("#row_num").val(parseInt(id) + 1);
            var sel = $("<select>").attr("name", "ranks[]").addClass("form-control");
            for(var i = 1; i < 11; i++)
            {
                sel.append($("<option>").attr("value", i).html(i));
            }
            var input = $("<input>").attr("type", "input").addClass("form-control").attr("name", "prizes[]");
            var button = $("<a>").addClass("btn btn-danger remove-prize").html("Remove").attr("rel", id).attr("href", "javascript:void(0);");
            var tr = $("<tr>").attr("id", "tr_" + id).append($("<td>").append(sel)).append($("<td>").append(input)).append($("<td>").append(button));
            $("#tbl_prize").append(tr);
        });
        
        $("body").on("click", ".remove-prize", function(e){
            e.preventDefault();
            var id = $(this).attr("rel");
            $("#tr_" + id).remove();
        });
        
        $("#update_show").click(function(){
                $.post('/admin_slots/ajax_add_tournament', $("#frm_tournament").serialize(), function(data){
                    $("#frm_tournament div").removeClass('alert alert-danger');
                        if(data.success)
                        {
                                $("#game_message").html("Insert / Update of the Slot Tournament was good.").addClass("alert alert-success").removeClass('alert-danger');
                                $('html,body').scrollTop(0);
                                var command = "window.location = '/admin_slots/view_tournaments';";
                                setTimeout(command, 1000);
                        }
                        else
                        {
                                var error = "There were the following errors:\n";
                                for(var key in data.errors)
                                    error = error + data.errors[key] + "\n";
                                alert(error);
                        }
                },'json');
        });
                
        $('#StartDate').datetimepicker({
            format:'Y-m-d H:i',
            value:'<?php if($tournament) echo date("Y-m-d H:i", strtotime($tournament->StartDate)); ?>',
            step: 10
          });
          
          $('#EndDate').datetimepicker({
            format:'Y-m-d H:i',
            value:'<?php if($tournament) echo date("Y-m-d H:i", strtotime($tournament->EndDate) + 1); ?>',
            step: 10
          });
          
          $.post('/admin/get_preview', {file: $("#sel_fname_none").val()}, function(data){
                    $("#preview_text_none").html(data);                    
                }); 
        
    });
</script>
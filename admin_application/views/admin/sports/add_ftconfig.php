<link href="/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<script src="/js/jquery.datetimepicker.js"></script>

<div class="panel panel-primary">    
    <div class="panel-heading"><?php if($config) : ?>Edit<?php else : ?>Add<?php endif; ?> Final Three Config</div>
    <div class="panel-body">
        <div id="config_message"></div>
   <form role="form" id="frm_bgconfig">
   <?php if($config) : ?><input type="hidden" id="config_id" name="id" value="<?=$config->id?>"/><?php endif; ?>
   <div class="form-group" id="divc_theme">
    <label for="theme">Theme (NO SPACES)</label>
    <input type="text" class="form-control" id="theme" name="theme" placeholder="theme" value="<?php if($config) echo $config->theme; ?>">
  </div>      
   <div class="form-group" id="divc_cardDate">
    <label for="StartDate">Start Date</label>
    <input type="text" class="form-control" id="cardDate" name="startDate" placeholder="StartDate" value="<?php if($config) echo date("Y-m-d H:i", strtotime($config->startDate)); ?>">
  </div>
  <div class="form-group" id="divc_endDate">
    <label for="StartDate">End Date</label>
    <input type="text" class="form-control" id="endDate" name="endDate" placeholder="EndDate" value="<?php if($config) echo date("Y-m-d H:i", strtotime($config->endDate)); ?>">
  </div>    
    <div class="form-group" id="divc_cardWin">
    <label for="sportCategoryId">Sport Category</label>
    <select class="form-control" id="sportCategoryId" name="sportCategoryId">
        <?php foreach($categories as $category) : ?>
        <option value="<?= $category->id; ?>" <?php if($config && $config->sportCategoryId == $category->id) echo "selected=''"; ?>><?= $category->name; ?></option>
        <?php endforeach; ?>
    </select>
  </div>
  <div class="form-group" id="divc_cardCount">
    <label for="EndDate">Card Count</label>
    <input type="text" class="form-control" readonly="" value="<?php if($config) echo $config->cardCount; ?>">
  </div> 
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="add_config" class="btn btn-primary"><?php if($config) : ?>Update<?php else : ?>Add<?php endif; ?> Config</button></div>
</form>
</div>

<?php if($config) : ?>
<div class="panel panel-primary">    
    <div class="panel-heading">Prizes</div>
    <div class="panel-body">
        <table class="table table-striped table-responsive" id="tbl_place">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Prize</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($prizes as $key => $place) : ?>
                <tr id="tr_prize_<?= $key + 1; ?>">
                    <td><input type="text" id="place_rank[]" class="form-control" value="<?= $key + 1; ?>"/></td>
                    <td><input type="text" id="place_prize[]" class="form-control" value="<?= $place; ?>"/></td>                    
                    <td><button type="button" rel="<?= $key + 1; ?>" class="btn btn-danger delete-place">Remove</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="panel-footer" style="text-align: right;"><a href="/admin_sports/add_ft_place/<?=$config->id?>" class="btn btn-primary" data-toggle="modal" data-target="#modal">Add Place</a></div>
</div>

<?php if(!$config->cardCount) : ?>
<div class="panel panel-primary">    
    <div class="panel-heading">Games</div>
    <div class="panel-body">
        <table class="table table-striped table-responsive">
            <thead>
                <tr>
                    <th>Category</th>                    
                    <th>Date</th>
                    <th>Team 1</th>
                    <th>Team 2</th>
                    <th>Update</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($games as $game) : ?>
                <tr id="tr_game_<?= $game->id; ?>">                    
                    <td><select class="form-control" id="name_<?= $game->id; ?>">
                            <?php foreach($names as $name) : ?>
                            <option value="<?= $name?>" <?php if($game->gameType == $name) echo "selected=''"; ?>><?= $name; ?></option>
                            <?php endforeach; ?>
                        </select></td>
                    <td><input class="form-control date-time" id="dateTime_<?= $game->id; ?>" value="<?= $game->dateTime; ?>"/></td>
                    <td>
                        <select class="form-control sel-team" id="team1_<?= $game->id; ?>">
                            <option value="">-- Select Team --</option>
                        <?php foreach($teams as $team) : ?>
                            <option value="<?= $team->id; ?>" <?php if($team->id == $game->teamId1) echo 'selected=""'; ?>><?= $team->name; ?></option>
                        <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select class="form-control sel-team" id="team2_<?= $game->id; ?>">
                            <option value="">-- Select Team --</option>
                        <?php foreach($teams as $team) : ?>
                            <option value="<?= $team->id; ?>" <?php if($team->id == $game->teamId2) echo 'selected=""'; ?>><?= $team->name; ?></option>
                        <?php endforeach; ?>
                        </select>
                    </td>
                    <td><button class="btn btn-primary btn-update-game" rel="<?= $game->id; ?>">Update</button></td>
                    <td><button type="button" rel="<?= $game->id; ?>" class="btn btn-danger delete-game">Remove</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="panel-footer" style="text-align: right;">        
        <a href="/admin_sports/add_ft_game/<?=$config->id?>" class="btn btn-primary" data-toggle="modal" data-target="#modal">Add Game</a>
    </div>
</div>
<?php else : ?>
<div class="panel panel-primary">    
    <div class="panel-heading">Games</div>
    <div class="panel-body">
        <table class="table table-striped table-responsive">
            <thead>
                <tr>
                    <th>Category</th>                    
                    <th>Date</th>
                    <th>Team 1</th>
                    <th>Team 1 Score</th>
                    <th>Team 2</th>
                    <th>Team 2 Score</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($games as $game) : ?>       
                <tr>         
                    <td><?= $game->gameType; ?></td>
                    <td><?= $game->dateTime; ?></td>
                    <?php if($game->gameType == "Final") : ?><td id="Final-1"><?= $game->team1Name; ?></td><?php else : ?><td><?= $game->team1Name; ?></td><?php endif; ?>
                    <td>
                        <input type="text" id="<?= $game->gameType . "_1"?>" rel="<?= $game->team1Name; ?>" 
                               alt="<?php if($game->gameType == "Final" && $picksHash) echo $picksHash[$game->gameType . "_1"]["id"]; else echo $game->teamId1;?>" 
                               class="tm-score <?= $game->gameType;?>" value="<?php if($picksHash) echo $picksHash[$game->gameType . "_1"]["val"]; ?>"/> 
                    </td>
                    <?php if($game->gameType == "Final") : ?><td id="Final-2"><?= $game->team2Name; ?></td><?php else : ?><td><?= $game->team2Name; ?></td><?php endif; ?>
                    <td>
                        <input type="text" id="<?= $game->gameType . "_2"?>" rel="<?= $game->team2Name; ?>" 
                               alt="<?php if($game->gameType == "Final" && $picksHash) echo $picksHash[$game->gameType . "_2"]["id"]; else echo $game->teamId2;?>" 
                               class="tm-score <?= $game->gameType;?>" value="<?php if($picksHash) echo $picksHash[$game->gameType . "_2"]["val"]; ?>"/> 
                    </td>                
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="panel-footer" style="text-align: right;">
        <button class="btn btn-primary" style="margin-right: 15px;" id="save_scores">Save Scores</button>        
    </div>
</div>
<?php endif; ?>
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
                <input type="hidden" name="game_type" id="game_type" value="<?= $rule->gameType?>"/>
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
        <input type="hidden" id="game_type_none" value="FT"/>
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
                    alert("Game Rules saved for this Sweepstakes");
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
        $.post("/admin/ajax_add_rule_game", {text: $("#txt_xlat_text").val(), serial_number: $("#serial_number").val(), game_type: $("#game_type").val(), name: $("#name").val()}, function(data){
            if(data.success)
            {
                alert("Game Rules saved for this Final 3");
                $("#txt_xlat_text").html(data.text);
            }
            else
            {
                alert("Game Rules were not saved");
            }
        }, 'json');
    });
        
    $(".Semi1").blur(function(){
        var other = $(".Semi1").not("#" + $(this).attr('id'));
        if(parseInt($(this).val()) < parseInt(other.val()))
        {
            $("#Final_1").attr("alt", parseInt(other.attr("alt")));
            $("#Final-1").html(other.attr("rel"));
        }
        else
        {
            $("#Final_1").attr("alt", parseInt($(this).attr("alt")));
            $("#Final-1").html($(this).attr("rel"));
        }
    });
    
    $(".Semi2").blur(function(){
        var other = $(".Semi2").not("#" + $(this).attr('id'));
        if(parseInt($(this).val()) < parseInt(other.val()))    
        {
            $("#Final_2").attr("alt", parseInt(other.attr("alt")));
            $("#Final-2").html(other.attr("rel"));
        }
        else
        {
            $("#Final_2").attr("alt", parseInt($(this).attr("alt")));
            $("#Final-2").html($(this).attr("rel"));
        }
    });
    
    $("#save_scores").click(function(){
        var data = {
            Semi1_1: {id: $("#Semi1_1").attr("alt"), val: $("#Semi1_1").val()},
            Semi1_2: {id: $("#Semi1_2").attr("alt"), val: $("#Semi1_2").val()},
            Semi2_1: {id: $("#Semi2_1").attr("alt"), val: $("#Semi2_1").val()},
            Semi2_2: {id: $("#Semi2_2").attr("alt"), val: $("#Semi2_2").val()},
            Final_1: {id: $("#Final_1").attr("alt"), val: $("#Final_1").val()},
            Final_2: {id: $("#Final_2").attr("alt"), val: $("#Final_2").val()},
            id: $("#config_id").val()
        };
        $.post("/admin_sports/ajax_save_ft", data, function(data){
            if(data.success)
                alert("Scores Updated");
            else
                alert("Scores Were not Updated Correctly");
        }, 'json');
    });
    
    $("#add_config").click(function(){
        $.post("/admin_sports/ajax_add_ft_config", $("#frm_bgconfig").serialize(), function(data){
            if(data.success)
            {
                window.location = "/admin_sports/add_ft_config/" + data.id;
            }
            else
            {
                $("#config_message").addClass("alert alert-danger").html("There were errors. They are listed / highlighted below.");
                for(var key in data.errors)
                    $("#div_" + key).addClass("alert-danger");
            }
        }, 'json');
    });
    
    $(".btn-update-place").click(function(){
        var id = $(this).attr("rel");
        var post = {
            rank: $("#place_rank_" + id).val(),
            prize: $("#place_prize_" + id).val(),            
            id: id
        };
        
        $.post("/admin_sports/ajax_add_ft_place", post, function(data){
            if(data.success)
            {
                alert("Place Updated");
            }
        }, 'json');
    });
    
    $(".btn-update-game").click(function(){
        var id = $(this).attr("rel");
        var post = {
            finalCategoryId: $("#category_" + id).val(),
            name: $("#name_" + id).val(),
            dateTime: $("#dateTime_" + id).val(),
            team1: $("#team1_" + id).val(),
            team2: $("#team2_" + id).val(),
            team1Name: $("#team1_" + id + " option[value='" + $("#team1_" + id).val() + "']").text(),
            team2Name: $("#team2_" + id + " option[value='" + $("#team2_" + id).val() + "']").text(),
            id: id
        };
        
        $.post("/admin_sports/ajax_add_ft_game", post, function(data){
            if(data.success)
            {
                alert("Game Updated");
            }
        }, 'json');
    });
       
    
    $(".delete-place").click(function(){
        var rank = $(this).attr('rel');
        var id = $("#config_id").val();
        $.post("/admin_sports/ajax_delete_tf_place", {id: id, rank: rank}, function(data){
                $("#tr_prize_" + rank).fadeOut();
        }, 'json');
    });
       
    $(document).function
       $('.date-time').datetimepicker({
            format:'Y-m-d H:i',            
            step: 5
          });
       
       $('#cardDate').datetimepicker({
            format:'Y-m-d H:i',            
            step: 5
          });
          
       $('#endDate').datetimepicker({
            format:'Y-m-d H:i',            
            step: 5
          });
          
</script>
<div class="panel panel-primary">
    <div class="panel-heading"><?php if($game) : ?>Edit<?php else : ?>Add<?php endif;?> Game</div>
    <div class="panel-body">
        <div id="game_message"></div>
        <form role="form" id="frm_game">
            <?php if($game) : ?> <input type="hidden" name="ID" value="<?=$game->ID?>"/> <?php endif; ?>
    <div class="form-group" id="div_Name">
    <label for="Name">Name</label>
    <input type="text" class="form-control" id="Name" name="Name" placeholder="Name" value="<?php if($game) echo $game->Name; ?>">
  </div>
  <div class="form-group" id="div_Theme">
    <label for="Name">Theme</label>
    <input type="text" class="form-control" id="Theme" name="Theme" placeholder="Theme" value="<?php if($game) echo $game->Theme; ?>">
  </div>
  <div class="form-group" id="div_SerialNumber">
    <label for="SerialNumber">Serial Number</label>
    <input type="text" class="form-control" id="SerialNumber" <?php if($game && $game->TotalCards) echo "readonly=''";?> name="SerialNumber" placeholder="SerialNumber" value="<?php if($game) echo $game->SerialNumber; ?>">
  </div>
    <div class="form-group" id="div_CardIncrement">
    <label for="CardIncrement">Card Increment</label>
    <input type="text" class="form-control" id="CardIncrement" name="CardIncrement" placeholder="CardIncrement" value="<?php if($game) echo $game->CardIncrement; ?>">
  </div>    
    <div class="form-group" id="div_WinAmount">
    <label for="WinAmount">Win Amount</label>
    <input type="text" class="form-control" id="WinAmount" name="WinAmount" placeholder="WinAmount" value="<?php if($game) echo $game->WinAmount; ?>">
    </div>
    <?php if($game) : ?>
    <div class="form-group" id="div_TotalCards">
    <label for="TotalCards">Total Cards</label>
    <input type="text" class="form-control" id="TotalCards" readonly="" name="TotalCards" placeholder="TotalCards" value="<?php if($game) echo $game->TotalCards; ?>">
  </div>
    <div class="form-group" id="div_TotalWinningCards">
    <label for="TotalWinningCards">Total Winning Cards</label>
    <input type="text" class="form-control" id="TotalWinningCards" readonly="" name="TotalWinningCards" placeholder="TotalWinningCards" value="<?php if($game) echo $game->TotalWinningCards; ?>">
  </div>
    <div class="form-group" id="div_WinningCardIncrement">
    <label for="WinningCardIncrement">Winning Card Increment</label>
    <input type="text" class="form-control" id="WinningCardIncrement" readonly="" name="WinningCardIncrement" placeholder="WinningCardIncrement" value="<?php if($game) echo $game->WinningCardIncrement; ?>">
  </div>
   <?php endif; ?>
    <div class="form-group" id="div_WinningSpots">
    <label for="WinningSpots">Winning Spots</label>
    <input type="text" class="form-control" id="WinningSpots" <?php if($game && $game->TotalCards) echo "readonly=''";?> name="WinningSpots" placeholder="WinningSpots" value="<?php if($game) echo $game->WinningSpots; ?>">
  </div>
    <div class="form-group" id="div_adPlacement">
    <label for="WinningSpots">Ad Placement</label>
    <select name="adPlacement" id="adPlacement" class="form-control">
        <?php foreach($adPlacements as $adPlacement) : ?>
        <option value="<?= $adPlacement; ?>" <?php if($game && $game->adPlacement == $adPlacement) echo 'selected="selected"'; ?>><?= $adPlacement; ?></option>
        <?php endforeach; ?>
    </select>
    </div>
    <div class="form-group" id="div_CardType">
    <label for="WinningSpots">Card Type</label>
    <select name="CardType" id="CardType" class="form-control">
        <?php foreach($cardTypes as $CardType) : ?>
        <option value="<?= $CardType; ?>" <?php if($game && $game->CardType == $CardType) echo 'selected="selected"'; ?>><?= $CardType; ?></option>
        <?php endforeach; ?>
    </select>
    </div>
    <div class="form-group" id="div_SpotsOnCard">
    <label for="SpotsOnCard">Spots On Card</label>
    <input type="text" class="form-control" id="SpotsOnCard" <?php if($game && $game->TotalCards) echo "readonly=''";?> name="SpotsOnCard" placeholder="SpotsOnCard" value="<?php if($game) echo $game->SpotsOnCard; ?>">
  </div>
    <div class="form-group" id="div_Disclaimer">
    <label for="SpotsOnCard">Disclaimer</label>
    <textarea class="form-control" id="Disclaimer" name="Disclaimer"><?php if($game) echo $game->Disclaimer; ?></textarea>
  </div>
    <div class="form-group" id="div_DeployWeb">
        <label>
                Deploy Web?
            </label>
            <label class="radio-inline">
                <input type="radio" name="DeployWeb" value="0" <?php if($game && $game->DeployWeb == 0) : ?>checked="checked"<?php endif; ?>> No
            </label>
            <label class="radio-inline">
                <input type="radio" name="DeployWeb" value="1" <?php if(!$game || $game->DeployWeb == 1) : ?>checked="checked"<?php endif; ?>> Yes
            </label>
    </div>
    <div class="form-group" id="div_DeployMobile">
        <label>
                Deploy Mobile?
            </label>
            <label class="radio-inline">
                <input type="radio" name="DeployMobile" value="0" <?php if($game && $game->DeployMobile == 0) : ?>checked="checked"<?php endif; ?>>No
            </label>
            <label class="radio-inline">
                <input type="radio" name="DeployMobile" value="1" <?php if(!$game || $game->DeployMobile == 1) : ?>checked="checked"<?php endif; ?>> Yes
            </label>
    </div>
    <div class="form-group" id="div_StartDate">
    <label for="StartDate">Start Date</label>
    <input type="text" class="form-control" id="StartDate" name="StartDate" placeholder="StartDate" value="<?php if($game) echo date("Y-m-d", strtotime($game->StartDate)); ?>">
  </div>
    <div class="form-group" id="div_EndDate">
    <label for="EndDate">End Date</label>
    <input type="text" class="form-control" id="EndDate" name="EndDate" placeholder="EndDate" value="<?php if($game) echo date("Y-m-d", strtotime($game->EndDate)); ?>">
  </div>
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_show" class="btn btn-primary"><?php if($game) : ?>Update<?php else : ?>Add<?php endif;?></button></div>
</form>
</div>

<?php if($game) :?>
<div class="panel panel-primary">
    <div class="panel-heading">Game Payouts</div>
    <div class="panel-body">
        <?php if($payouts) :?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Prize Amount</th>
                    <th>Prize Name</th>
                    <th>Taxable Amount</th>
                    <th>Weight</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <?php foreach($payouts as $payout) : ?>
            <tr>
                <td><?= $payout->Rank?></td>
                <td><?= $payout->PrizeAmount?></td>
                <td><?= $payout->PrizeName?></td>
                <td><?= $payout->TaxableAmount?></td>
                <td><?= $payout->Weight?></td>
                <td><a data-toggle="modal" href="/admin/add_payout/<?= $game->ID?>/<?= $payout->KeyID?>" data-target="#modal" class="btn btn-primary">Edit</a></td>
                <td><a class="btn btn-danger delete-payout" rel="<?= $payout->KeyID?>">Delete</a></td>
            </tr>
            <?php endforeach;?>
        </table>
        <?php endif; ?>
    </div>
    <div class="panel-footer" style="text-align: right;"><a data-toggle="modal" style="margin-right: 15px;" href="/admin/clone_payout/<?= $game->ID?>" data-target="#modal" class="btn btn-primary"><span class='glyphicon glyphicon-asterisk'></span> Clone Payout</a><a data-toggle="modal" href="/admin/add_payout/<?= $game->ID?>" data-target="#modal" class="btn btn-primary"><span class='glyphicon glyphicon-plus'></span> Add Payout</a></div>
</div>
<?php endif; ?>

<?php if($cards) : ?>
<div class="panel panel-primary">
    <div class="panel-heading">Cards Played</div>
    <div class="panel-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Total Cost</th>
                    <th>Cards Played</th>
                    <th>Winners Played</th>
                    <th>Money Played</th>
                    <th>Cards Left</th>
                    <th>Winners Left</th>
                    <th>Money Left</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php foreach($cards as $card) : ?>
                    <td><?= $card->Name?></td>
                    <td><?= $card->TotalCost?></td>
                    <td><?= $card->CardsPlayed?></td>
                    <td><?= $card->WinnersPlayed?></td>
                    <td><?= $card->MoneyPlayed?></td>
                    <td><?= $card->CardsLeft?></td>
                    <td><?= $card->WinnersLeft?></td>
                    <td><?= $card->MoneyLeft?></td>
                    <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
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
                <input type="hidden" name="game_type" id="game_type" value="<?= $rule->gameType?>"/>
                <input type="hidden" name="serial_number" id="serial_number" value="<?= $game->SerialNumber; ?>"/>
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
<?php elseif($game) : ?>
    <div class="panel panel-primary">
    <div class="panel-heading">Rules</div>
    <div class="panel-body">
    <select class="form-control" name="sel_file_name_none" id="sel_fname_none">
        <?php foreach($rules as $row) : ?>
        <option value="<?= $row->ruleURL; ?>"><?= $row->ruleURL; ?></option>
        <?php endforeach; ?>
    </select>
        <textarea class="form-control" readonly="" id="preview_text_none" style="height: 350px;"></textarea>    
        <input type="hidden" id="game_type_none" value="Scratchers"/>
        <input type="hidden" id="serial_type_none" value="<?= $game->SerialNumber; ?>"/>
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
                file_name: $("#sel_fname_none").val(),
                name: $("#Name").val()
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
                $.post("/admin/ajax_add_rule", {file_name: $("#sel_fname").val(), serial_number: $("#serial_number").val(), game_type: $("#game_type").val(), name: $("#Name").val()}, function(data){
                    if(data.success)
                    {
                        alert("Game Rules saved for this Scratcher");
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
            $.post("/admin/ajax_add_rule_game", {text: $("#txt_xlat_text").val(), serial_number: $("#serial_number").val(), game_type: $("#game_type").val(), name: $("#Name").val()}, function(data){
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
        });
    
        $("#update_show").click(function(){
                $.post('/admin/ajax_add_games', $("#frm_game").serialize(), function(data){
                        if(data.success)
                        {
                                $("#game_message").html("Insert / Update was good.").addClass("alert alert-success").removeClass('alert-danger');
                                $('html,body').scrollTop(0);
                                if(data.id)
                                    var command = "window.location = '/admin/edit_game/" + data.id + "';";
                                else
                                    var command = "window.location = '/admin/view_games/';";
                                setTimeout(command, 1000);
                        }
                        else
                        {
                                $("#game_message").addClass("alert alert-danger").html("There were errors. They are listed / highlighted below.");
                                for(var key in data.errors)
                                    $("#div_" + key).addClass("alert-danger");
                                $('html,body').scrollTop(0);
                        }
                },'json');
        });
        
        $("#update_rule").click(function(){
            $.post("/admin/ajax_add_rule", $("#frm_rule").serialize(), function(data){
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
        
        $(".delete-payout").click(function(){
                var id = $(this).attr('rel');
                var r = confirm("Are you sure you want to delete this Payout?");
                if(r)
                {
                        $.get("/admin/ajax_delete_payouts/" + id, {}, function(data) {
                                if(data.success)
                                {
                                        alert("Record Deleted");
                                        window.location.reload();
                                }
                                else
                                {
                                        alert(data.error);
                                }
                        },'json');
                }
        });
        
        $( "#StartDate" ).datepicker({
            dateFormat: "yy-mm-dd",
            setDate: "+1d", 
            changeMonth: true,
            numberOfMonths: 3,
            onClose: function( selectedDate ) {
                $( "#EndDate" ).datepicker( "option", "minDate", selectedDate );
            }
        });
        
        $( "#EndDate" ).datepicker({
            dateFormat: "yy-mm-dd",
            setDate: "+1w",
            changeMonth: true,
            numberOfMonths: 3,
            onClose: function( selectedDate ) {
                $( "#StartDate" ).datepicker( "option", "maxDate", selectedDate );
            }
        });
        
        $.post('/admin/get_preview', {file: $("#sel_fname_none").val()}, function(data){
                    $("#preview_text_none").html(data);                    
                }); 
    });
</script>
<div class="panel panel-primary">
    <div class="panel-heading"><?php if($slot) : ?>Edit<?php else : ?>Add<?php endif;?> Slot</div>
    <div class="panel-body">
        <div id="game_message"></div>
        <form role="form" id="frm_slot">
            <?php if($slot) : ?> <input type="hidden" name="ID" value="<?=$slot->ID?>"/> <?php endif; ?>
    <div class="form-group" id="div_Name">
    <label for="Name">Name</label>
    <input type="text" class="form-control" id="Name" name="Name" placeholder="Name" value="<?php if($slot) echo $slot->Name; ?>">
  </div>
  <div class="form-group" id="div_Theme">
    <label for="Theme">Theme</label>
    <input type="text" class="form-control" id="Theme" name="Theme" placeholder="Theme" value="<?php if($slot) echo $slot->Theme; ?>">
  </div>
    <div class="form-group" id="div_Math">
    <label for="Math">Math</label>
    <input type="text" class="form-control" id="Math" name="Math" placeholder="Math" value="<?php if($slot) echo $slot->Math; ?>">
  </div>
  <div class="form-group" id="div_StartTime">
    <label for="StartTime">Start Time</label>
    <input type="text" class="form-control" id="StartTime" name="StartTime" placeholder="StartTime" value="<?php if($slot) echo $slot->StartTime; else echo '00:00:00';?>">
  </div>
  <div class="form-group" id="div_EndTime">
    <label for="EndTime">End Time</label>
    <input type="text" class="form-control" id="EndTime" name="EndTime" placeholder="EndTime" value="<?php if($slot) echo $slot->EndTime; else echo '23:59:59';?>">
  </div>
  <div class="form-group" id="div_SpinsTotal">
    <label for="SpinsTotal">Spins Total</label>
    <input type="text" class="form-control" id="SpinsTotal" name="SpinsTotal" placeholder="SpinsTotal" value="<?php if($slot) echo $slot->SpinsTotal; ?>">
  </div>
  <div class="form-group" id="div_SecsTotal">
    <label for="SecsTotal">Seconds Total</label>
    <input type="text" class="form-control" id="SecsTotal" name="SecsTotal" placeholder="SecsTotal" value="<?php if($slot) echo $slot->SecsTotal; ?>">
  </div>
    <div class="form-group" id="div_adPlacement">
    <label for="adPlacement">Ad Placement</label>
    <select name="adPlacement" id="adPlacement" class="form-control">
        <?php foreach($adPlacements as $adPlacement) : ?>
        <option value="<?= $adPlacement; ?>" <?php if($slot && $slot->adPlacement == $adPlacement) echo 'selected="selected"'; ?>><?= $adPlacement; ?></option>
        <?php endforeach; ?>
    </select>
    </div>
    <div class="form-group" id="div_SlotType">
    <label for="SlotType">Slot Type</label>
    <select name="SlotType" id="SlotType" class="form-control">
        <?php foreach($SlotTypes as $SlotType) : ?>
        <option value="<?= $SlotType; ?>" <?php if($slot && $slot->SlotType == $SlotType) echo 'selected="selected"'; ?>><?= $SlotType; ?></option>
        <?php endforeach; ?>
    </select>
    </div>
  <div class="form-group" id="div_Disclaimer">
    <label for="Disclaimer">Disclaimer</label>
    <textarea class="form-control" id="Disclaimer" name="Disclaimer" placeholder="Disclaimer"> <?php if($slot) echo $slot->Disclaimer; ?></textarea>
  </div>
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_show" class="btn btn-primary"><?php if($slot) : ?>Update<?php else : ?>Add<?php endif;?></button></div>
</form>
</div>

<script>
$(function() {
        $("#update_show").click(function(){
                $.post('/admin_slots/ajax_add_slot', $("#frm_slot").serialize(), function(data){
                    $("#frm_slot div").removeClass('alert-danger');
                        if(data.success)
                        {
                                $("#game_message").html("Insert / Update was good.").addClass("alert alert-success").removeClass('alert-danger');
                                $('html,body').scrollTop(0);
                                var command = "window.location = '/admin_slots';";
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
        
        $(".delete-prize").click(function(){
                var id = $(this).attr('rel');
                var slot_id = $(this).attr('nom');
                var r = confirm("Are you sure you want to delete this Prize?");
                if(r)
                {
                        $.get("/admin_slots/ajax_delete_prize/" + slot_id + "/" + id, {}, function(data) {
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
        
    });
</script>
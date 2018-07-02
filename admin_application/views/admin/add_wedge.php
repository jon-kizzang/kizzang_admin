<div class="modal-header"><?php if($wedge) : ?>Edit<?php else : ?>Add<?php endif; ?> Wedge</div>
<div class="modal-body">
    <div id="wedge_message"></div>
   <form role="form" id="frm_wedge">
   <?php if($wedge) : ?><input type="hidden" name="id" value="<?=$wedge->id?>"/><?php endif; ?>
   <input type="hidden" name="wheelId" value="<?= $wheel_id; ?>"/>
    <div class="form-group" id="div_ValueType">
    <label for="Name">Value Type</label>
    <select class="form-control" id="ValueType" name="ValueType" placeholder="Place">
        <?php foreach($values as $value) : ?>
        <option value="<?= $value; ?>" <?php if($wedge && $wedge->value == $value) : ?>selected="selected"<?php endif; ?>><?= $value; ?></option>
        <?php endforeach; ?>        
    </select>
  </div>
  <div class="form-group" id="div_value">
    <label for="Name">Value</label>
    <input type="text" class="form-control" id="value" name="value" placeholder="value" <?php if($wedge) : ?> value="<?= $wedge->displayString?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="div_color">
    <label for="Name">Color (#XXXXXX)</label>
    <input type="text" class="form-control" id="color" name="color" placeholder="color" <?php if($wedge) : ?> value="<?= $wedge->color?>"<?php endif; ?>>
  </div>
  <div class="form-group" id="div_weight">
    <label for="Name">Weight</label>
    <select class="form-control" id="weight" name="weight">
        <?php for($i =1; $i < 11; $i++) : ?>
        <option value="<?= $i; ?>" <?php if($wedge && $wedge->weight == $i) : ?>selected="selected"<?php endif; ?>><?= $i; ?></option>
        <?php endfor; ?>        
    </select>
  </div>
   <div class="form-group" id="div_sponsorId">
    <label for="Name">Sponor Campaign</label>
    <select class="form-control" id="sponsorCampaignId" name="sponsorCampaignId">
        <?php foreach($sponsors as $sponsor) : ?>
        <option value="<?= $sponsor->id; ?>" <?php if($wedge && $wedge->sponsorCampaignId == $sponsor->id) : ?>selected="selected"<?php endif; ?>><?= $sponsor->name . " - " . $sponsor->campaign_name; ?></option>
        <?php endforeach; ?>        
    </select>
  </div>
</form>
<div class="modal-footer"><button class="btn btn-default" data-dismiss="modal" type="button">Close</button><button type="button" id="update_wedge" class="btn btn-primary"><?php if($wedge) : ?>Update<?php else : ?>Add<?php endif; ?> Wedge</button></div>
</div>
<script>
$(function() {
        $("#update_wedge").click(function(){
                $.post('/admin/ajax_add_wedge', $("#frm_wedge").serialize(), function(data){
                        $("#frm_wedge div").removeClass('alert-danger');
                        if(data.success)
                        {
                                $("#wedge_message").html("Insert / Update was good.").addClass("alert alert-success").removeClass('alert-danger');
                                $('html,body').scrollTop(0);
                                var command = "window.location.reload();";
                                setTimeout(command, 1000);
                        }
                        else
                        {
                                $("#wedge_message").addClass("alert alert-danger").html("There were errors. They are listed / highlighted below.");
                                for(var key in data.errors)
                                    $("#div_" + key).addClass("alert-danger");
                        }
                },'json');
        });
    });
</script>
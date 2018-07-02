<div class="modal-header"><?php if($prize) : ?>Edit<?php else : ?>Add<?php endif; ?> Prize</div>
<div class="modal-body">

        <div id="payout_message"></div>
        <form role="form" id="frm_prize">
   <input type="hidden" name="SlotGameId" value="<?=$game->ID?>"/>
    <div class="form-group" id="div_Place">
    <label for="Name">Place</label>
    <select class="form-control" id="Place" name="Place" placeholder="Place">
        <?php for($i=1; $i <= 10; $i++) : ?>                                
            <option value="<?=$i?>" <?php if($prize && $i == $prize->Place) echo 'Selected=""'; ?>><?=$i?></option>                
        <?php endfor; ?>
    </select>
  </div>
  <div class="form-group" id="div_Amount">
    <label for="Name">Amount</label>
    <input type="text" class="form-control" id="Amount" name="Amount" placeholder="Amount" <?php if($prize) : ?> value="<?= $prize->Amount?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="div_Prize">
    <label for="Name">Prize</label>
    <input type="text" class="form-control" id="Prize" name="Prize" placeholder="Prize" <?php if($prize) : ?> value="<?= $prize->Prize?>"<?php endif; ?>>
  </div>

</form>
</div>
</div>
<div class="modal-footer"><button class="btn btn-default" data-dismiss="modal" type="button">Close</button><button type="button" id="update_prize" class="btn btn-primary"><?php if($prize) : ?>Update<?php else : ?>Add<?php endif; ?> Prize</button></div>
<script>
$(function() {
        $("#update_prize").click(function(){
                $.post('/admin_slots/ajax_add_prize', $("#frm_prize").serialize(), function(data){
                        $("#frm_prize div").removeClass('alert-danger');
                        if(data.success)
                        {
                                $("#payout_message").html("Insert / Update was good.").addClass("alert alert-success").removeClass('alert-danger');
                                $('html,body').scrollTop(0);
                                var command = "window.location.reload();";
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
    });
</script>
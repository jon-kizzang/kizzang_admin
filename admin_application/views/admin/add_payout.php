<div class="panel panel-primary" style="margin-bottom: 0px;">
    <div class="panel-heading"><?php if($payout) : ?>Edit<?php else : ?>Add<?php endif; ?> Payout</div>
    <div class="panel-body">
        <div id="payout_message"></div>
        <form role="form" id="frm_payout">
   <input type="hidden" name="PayoutID" value="<?=$game->PayoutID?>"/>
   <?php if($payout) : ?><input type="hidden" name="KeyID" value="<?=$payout->KeyID?>"/><?php endif; ?>
    <div class="form-group" id="div_Rank">
    <label for="Name">Rank</label>
    <select class="form-control" id="Rank" name="Rank" placeholder="Rank">
        <?php if($payout) : ?><option value="<?= $payout->Rank?>" selected=""><?= $payout->Rank; ?></option><?php endif; ?>
        <?php foreach($game->rank_array as $rank) : ?>                
                <?php if(!$payout || $payout->Rank != $rank) : ?>
                        <option value="<?=$rank?>"><?=$rank?></option>
                <?php endif; ?>
        <?php endforeach; ?>
    </select>
  </div>
  <div class="form-group" id="div_PrizeAmount">
    <label for="Name">Prize Amount</label>
    <input type="text" class="form-control" id="PrizeAmount" name="PrizeAmount" placeholder="PrizeAmount" <?php if($payout) : ?> value="<?= $payout->PrizeAmount?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="div_PrizeName">
    <label for="Name">Prize Name</label>
    <input type="text" class="form-control" id="PrizeName" name="PrizeName" placeholder="PrizeName" <?php if($payout) : ?> value="<?= $payout->PrizeName?>"<?php endif; ?>>
  </div>
    <div class="form-group" id="div_TaxableAmount">
    <label for="Name">Taxable Amount</label>
    <input type="text" class="form-control" id="TaxableAmount" name="TaxableAmount" placeholder="TaxableAmount" <?php if($payout) : ?> value="<?= $payout->TaxableAmount?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="div_Weight">
    <label for="Name">Weight</label>
    <input type="text" class="form-control" id="Weight" name="Weight" placeholder="Weight" <?php if($payout) : ?> value="<?= $payout->Weight?>"<?php endif; ?>>
  </div>   
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_payout" class="btn btn-primary"><?php if($payout) : ?>Update<?php else : ?>Add<?php endif; ?> Payout</button><button style="margin-left: 20px;" class="btn btn-default" type="button" data-dismiss="modal">Close</button></div>
</form>
</div>

<script>
$(function() {
        $("#update_payout").click(function(){
                $.post('/admin/ajax_add_payouts', $("#frm_payout").serialize(), function(data){
                        $("#frm_payout div").removeClass('alert-danger');
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
<div class="panel panel-primary" style="margin-bottom: 0px;">
    <div class="panel-heading">Clone Payouts</div>
    <div class="panel-body">
        <div id="payout_message"></div>
        <form role="form" id="frm_payout">
            <input type="hidden" name="id" id="id" value="<?=$id?>"/>
    <div class="form-group" id="div_Rank">
    <label for="Name">Select Payment</label>
    <select class="form-control" id="clone" name="clone" placeholder="Rank">
        <option value="">Select game to Clone</option>
        <?php foreach($payments as $payment) : ?>                                
            <option value="<?=$payment->id?>"><?=$payment->name?></option>                
        <?php endforeach; ?>
    </select>
  </div>  
   <div class="well-lg" id="payments"></div>
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="clone_payout" class="btn btn-primary">Clone Payout</button></div>
</form>
</div>

<script>
$(function() {
        $("#clone_payout").click(function(){
                $.post('/admin_sports/ajax_clone_payouts', {cur_parlay_id: $("#id").val(), new_parlay_id: $("#clone").val()}, function(data){                        
                        if(data.success)
                        {
                                $("#payout_message").html("Cloning was successful").addClass("alert alert-success").removeClass('alert-danger');
                                $('html,body').scrollTop(0);
                                var command = "location.reload();";
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
        
        $("#clone").change(function(){
            $.get('/admin_sports/ajax_get_payout/' + $("#clone").val(), {}, function(data){
                $("#payments").html(data);
            });
        });
        
    });
</script>
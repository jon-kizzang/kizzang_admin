<div class="panel panel-primary" style="margin-bottom: 0px;">    
    <div class="panel-heading">Sweepstakes Winner for <?=$game->name; ?></div>
    <div class="panel-body">
    </div>
    <div class="panel-footer" style="text-align: right;"><button data-dismiss="modal" type="button" id="update_prize" class="btn btn-primary close">Close</button></div>
</form>
</div>

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
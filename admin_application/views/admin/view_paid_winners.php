<style>
    #mask {
            opacity: .3;
            background-color: #000;
            display: none;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            position: fixed;
            z-index: 1000;
    }
    
    #mask_message {
            z-index: 1001;
            height: 100px;
            width: 200px;
            background-color: #FFF;
            position: fixed;
            display: none;
            padding: 40px;
            top: 0px;
            padding: 20px;
    }
</style>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>Screen Name</th>            
            <th>Prize Name</th>
            <th>Prize Amount</th>
            <th>Game Type</th>            
            <th>Date</th>      
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php $total_money = 0; ?>
            <?php foreach($winners as $winner) : ?>
        <tr id="tr_<?= $winner->id; ?>">
            <td><?= $winner->screenName . " (" . $winner->player_id . ")"; ?></td>
            <td><?= $winner->prize_name; ?></td>
            <td><?= $winner->amount; ?></td>
            <td><?= $winner->game_type; ?></td>            
            <td><?= $winner->created; ?></td>
            <td><button class="btn btn-success process-payment" rel="<?= $winner->id; ?>">Process</button></td>
            <?php $total_money += $winner->amount; ?>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<div id="mask">
</div>
<div id="mask_message">Generating Cards</div>
<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 100, order: [[ 5, "asc" ]]});
                $("#process_all").click(function(){
                    var r = confirm("Are you sure you want to process $<?= number_format($total_money, 2); ?> of winners?");
                    if(r)
                    {
                        $("#mask").show();
                        $("#mask_message").html("Processing All.  This may take up to 10 Minutes.").show();
                        $.get("/admin/ajax_process_all", {}, function(data){
                             if(data.success)
                             {
                                 $("#mask").hide();
                                 $("#mask_message").hide();
                                 location.reload();
                             }
                        }, 'json');
                    }
                });
                
                $(".process-payment").click(function(){
                    $(this).attr('disabled', 'disabled');
                    var id = $(this).attr("rel");
                    $.get('/admin/ajax_process_payment/' + id, {}, function(data){
                        if(data.success)
                        {
                            $("#tr_" + id).fadeOut();
                            alert(data.message);
                        }
                        else
                        {
                            alert(data.message);
                        }
                    }, 'json');
                });
                
                $(".update-winner").click(function(){
                    var id = $(this).attr("rel");
                    if(!$("#txt_" + id).val())
                    {
                        alert("Please enter in an order number / serial number to complete transaction.");
                    }
                    else
                    {
                        $.post('/admin/ajax_process_sweepstakes', {id: id, text: $("#txt_" + id).val()}, function(data){
                            if(data.success)
                            {
                                $("#tr_" + id).fadeOut();
                                alert("Information Saved / Record Processed");
                            }
                        }, 'json');
                    }
                });
        });
</script>

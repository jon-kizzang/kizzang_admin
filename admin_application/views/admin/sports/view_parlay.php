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
            padding: 20px;
            text-align: center;
    }
    
    #frm_power_rank div {
        margin-left: 10px;
    }
</style>
<div class="well">
    <form class="form-inline" id="frm_power_rank" method="POST" role="form">
    <button class="btn btn-primary" id="update_powerranks">Update Power Rankings</button>
    <div class="checkbox">
    <label>
        <input type="checkbox" name="pr_type[]" value="Pro Hockey"> Pro Hockey
    </label>
    </div>
    <div class="checkbox">
    <label>
        <input type="checkbox" name="pr_type[]" value="Pro Football"> Pro Football
    </label>
    </div>
    <div class="checkbox">
    <label>
        <input type="checkbox" name="pr_type[]" value="Pro Baseball"> Pro Baseball
    </label>
    </div>
    <div class="checkbox">
    <label>
        <input type="checkbox" name="pr_type[]" value="Pro Basketball"> Pro Basketball
    </label>
    </div>
    <div class="checkbox">
    <label>
        <input type="checkbox" name="pr_type[]" value="Pro Soccer"> Pro Soccer
    </label>
    </div>
    <div class="checkbox">
    <label>
        <input type="checkbox" name="pr_type[]" value="College Basketball"> College Basketball
    </label>
    </div>
    <div class="checkbox">
    <label>
        <input type="checkbox" name="pr_type[]" value="College Football"> College Football 
    </label>
    </div>
    </form>
    <br/><br/>
    <label>Start / End Dates:</label>
    <form action="/admin_sports/view_parlay" method="POST">
        <input type="text" name="startDate" id="StartDate" value="<?= $startDate; ?>" style="margin-left: 20px;"/>
        <input type="text" name="endDate" id="EndDate" value="<?= $endDate; ?>" style="margin-left: 20px;"/>
        <button class="btn btn-primary" id="btn_dates" style="margin-left: 20px;">Search Dates</button>
    </form>
    <button class="btn btn-primary" id="btn_emails" style="margin-left: 20px;">Manually Send Emails</button>
</div>
<div id="message"></div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>       
            <th>ID</th>
            <th>Card Date</th>
            <th>End Date </th>
            <th>Type</th>
            <th>Win Amount</th>
            <th># of Questions</th> 
            <th>Questions Answered</th>
            <th>Is Active</th>
            <th>Edit</th> 
            <th>View Cards</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($parlays as $parlay) : ?>
        <tr>       
            <td><?= $parlay->parlayCardId?></td>
            <td><?= $parlay->cardDate; ?></td>
            <td><?= $parlay->endDate; ?></td>
            <td><?= $parlay->type; ?></td>
            <td><?= $parlay->cardWin; ?></td>           
            <td><?= $parlay->cnt; ?></td>     
            <td><?= $parlay->questions; ?></td>
            <td><b><?php if($parlay->isActive) : ?>YES<?php else : ?>NO<?php endif; ?></b></td>
            <td><?php if($parlay->cnt != 0 && $parlay->questions != 0) : ?><a data-toggle="modal" href="/admin/get_parlay_winners/<?= $parlay->parlayCardId?>" data-target="#modal" class="btn btn-primary">View Winners</a>
                <?php endif; ?>
                <a href="/admin_sports/add_parlay/<?= $parlay->parlayCardId?>" class="btn btn-primary">Edit</a>
                <a href="javascript:void(0);" rel="<?= $parlay->parlayCardId?>" class="btn btn-danger delete-parlay">Delete</a>
            </td>    
            <td><?php if($parlay->cnt) : ?><a href="/admin_sports/view_parlay_cards/<?= $parlay->parlayCardId?>" class="btn btn-primary">View Cards</a><?php endif; ?></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<div id="mask">
</div>
<div id="mask_message">Updating Power Rankings</div>
<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 1, "asc" ]]});
                
                $( "#StartDate" ).datepicker({
                    dateFormat: "yy-mm-dd",
                    setDate: "<?= $startDate; ?>", 
                    changeMonth: true,
                    numberOfMonths: 3,
                    maxDate: "<?= $endDate; ?>",
                    onClose: function( selectedDate ) {
                        $( "#EndDate" ).datepicker( "option", "minDate", selectedDate );
                    }
                });

                $( "#EndDate" ).datepicker({
                    dateFormat: "yy-mm-dd",
                    setDate: "<?= $endDate; ?>",
                    changeMonth: true,
                    numberOfMonths: 3,
                    minDate: "<?= $startDate; ?>",
                    onClose: function( selectedDate ) {
                        $( "#StartDate" ).datepicker( "option", "maxDate", selectedDate );
                    }
                });  
                
                $(".delete-parlay").click(function(){
                    var id = $(this).attr("rel");
                    var r = confirm("Are you sure you want to delete this card?");
                    if(r)
                    {
                        $.get("/admin_sports/ajax_delete_parlay/" + id, {}, function(data){
                            if(data.success)
                            {
                                alert("Card Deleted");
                                location.reload();
                            }
                            else
                            {
                                alert("Card cannot be deleted because it has cards attached to it.");
                            }
                        }, 'json');
                    }
                });
                
                $("#btn_emails").click(function(e){
                    e.preventDefault();
                    $("#mask").show();
                    $("#mask_message").html("Sending emails... Please wait.");
                    $("#mask_message").css({left: window.innerWidth / 2 - 100, top: window.innerHeight / 2 -50}).show();
                    $(this).prop('disabled', true);
                    $.get("/admin_sports/send_emails", {}, function(data){
                        if(data.success)
                        {
                            $("#mask_message").html("Emails Sent!!");
                            $("#mask").hide();
                            setTimeout('$("#mask_message").hide();', 2000);
                            $("#btn_emails").prop('disabled', false);
                        }
                    }, 'json');
                });
                
                $("#update_powerranks").click(function(e){
                    e.preventDefault();
                    $("#mask").show();
                    $("#mask_message").css({left: window.innerWidth / 2 - 100, top: window.innerHeight / 2 -50}).show();
                    $(this).prop('disabled', true);
                    $.post('/admin_sports/update_powerranks', $("#frm_power_rank").serialize(), function(data){
                            $("#mask").hide();
                            $("#mask_message").html("Update Completed!");
                            var command = '$("#mask_message").fadeOut();';
                            setTimeout(command, 1000);
                            $("#update_powerranks").prop('disabled', false);
                    }, 'json');
                });
        } );               
</script>
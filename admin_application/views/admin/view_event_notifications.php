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
    <button class="btn btn-danger" id="btn_clear_notifications">Clear Non-Pending / Expired Notifications</button>
    <a class="btn btn-primary" href="/admin/add_event_notification" style="float: right;" data-toggle="modal" data-target="#modal" id="btn_add_notifications">Add Event Notifications</a>
</div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>            
            <th>Screen Name</th>  
            <th>Type</th>
            <th>Data</th>
            <th>Added</th>
            <th>Expires</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($notifications as $notification) : ?>
        <tr>
            <td><?= $notification->id; ?></td>
            <td><?= $notification->screenName . " (" . $notification->playerId . ")"; ?></td>                        
            <td><?= $notification->type ?></td>
            <td><?= $notification->data; ?></td>
            <td><?= date("D M d, Y h:i A", strtotime($notification->added)); ?></td>
            <td><?php if($notification->expireDate) echo date("D M d, Y h:i A", strtotime($notification->expireDate)); else echo "N/A"; ?></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<div id="mask"></div>
<div id="mask_message"></div>

<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 100, order: [[ 0, "desc" ]]});   
                
                $("#btn_clear_notifications").click(function(){
                    $("#mask").show();
                    $("#mask_message").css({left: window.innerWidth / 2 - 100, top: window.innerHeight / 2 -50}).html("Removing old events").show();
                    $.get("/admin/ajax_clear_notifications", {}, function(data){                        
                        if(data.success)
                            $("#mask_message").html("Update Completed!").addClass("alert-success");
                        else
                            $("#mask_message").html("Event Purge Failed!").addClass("alert-danger");
                        
                        var command = '$("#mask_message").fadeOut();$("#mask").hide(); location.reload();';
                        setTimeout(command, 2000);
                    }, 'json');
                });                               
                
        } );
</script>
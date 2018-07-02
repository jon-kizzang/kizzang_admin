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
</style>
<div id="message"></div>
<div class="well">
    <button class="btn btn-success" id="archive_tables">Archive Session / Log Tables</button><br/><br/>
    <label>Start / End Dates:</label>
    <form action="/admin_slots/view_tournaments" method="POST">
        <input type="text" name="startDate" id="StartDate" value="<?= $startDate; ?>" style="margin-left: 20px;"/>
        <input type="text" name="endDate" id="EndDate" value="<?= $endDate; ?>" style="margin-left: 20px;"/>
        <button class="btn btn-primary" id="btn_dates" style="margin-left: 20px;">Search Dates</button>
    </form>    
</div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>            
            <th>ID</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Type</th>
            <th>Prizes</th>
            <th>Session Table?</th>
            <th>Log Table?</th>
            <th>Edit</th>    
            <th>Replicate</th>
            <td>Stats</td>
        </tr>
    </thead>
    <tbody>
            <?php foreach($slots as $slot) : ?>
        <tr>            
            <td><?= $slot->ID; ?></td>
            <td><?= $slot->StartDate; ?></td>
            <td><?= $slot->EndDate; ?></td>
            <td><?= $slot->Type; ?></td>
            <td>
                <?php foreach($slot->Prizes as $key => $prize): ?>
                <?= ($key + 1) . '. ' . $prize . '<br>'; ?>
                <?php endforeach; ?>
            </td>            
            <td><?php if($slot->Session) echo "Yes"; else echo "NO"; ?></td>
            <td><?php if($slot->Log) echo "Yes"; else echo "NO"; ?></td>
            <td><a href="/admin_slots/add_tournament/<?= $slot->ID?>" class="btn btn-primary">Edit</a></td>
            <td><a data-toggle="modal" data-target="#modal" href="/admin_slots/replicate_tournament/<?= $slot->ID?>" class="btn btn-primary">Replicate</a></td>
            <td><a data-toggle="modal" data-target="#modal" href="/admin_slots/slot_stats/<?= $slot->ID?>" class="btn btn-primary">Stats</a></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<div id="mask">
</div>
<div id="mask_message">Cleaning up tables...</div>
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
                
                $(".add_cards").click(function(){
                        $("#mask").show();
                        $("#mask_message").css({left: window.innerWidth / 2 - 100, top: window.innerHeight / 2 -50}).show();;
                        $(this).prop('disabled', true);
                        var id = $(this).attr('rel');
                        $.get("/admin/ajax_increment_cards/" + id, {}, function(data){
                                $("#mask").hide();
                                $("#mask_message").hide();
                                if(data.success)
                                {
                                        $("#message").html("Cards Created Successfully!").addClass("alert alert-success");
                                        setTimeout("window.location.reload();", 3000);
                                }
                                else
                                {
                                        
                                }
                        }, 'json');
                });
                
                $("#archive_tables").click(function(){
                    $("#mask").show();
                    $("#mask_message").css({left: window.innerWidth / 2 - 100, top: window.innerHeight / 2 -50}).show();;
                    $(this).prop('disabled', true);
                    
                    $.get("/admin_slots/archive_tables", {}, function(data){
                        if(data.success)
                        {
                            $("#mask_message").html(data.message);
                            $("#mask").hide();
                            var command = '$("#mask_message").fadeOut();';
                            setTimeout(command, 1000);
                            command = '$("#mask_message").html("Cleaning up tables...");';
                            setTimeout(command, 3000);
                            $("#archive_tables").prop('disabled', false);
                        }
                    }, 'json');
                });
        } );
</script>
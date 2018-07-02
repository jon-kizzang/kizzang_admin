<div id="message"></div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>            
            <th>Name</th>
            <th>Theme</th>
            <th>Math</th>
            <th>Spins Total</th>
            <th>Secs Total</th>            
            <th>Edit</th>            
        </tr>
    </thead>
    <tbody>
            <?php foreach($slots as $slot) : ?>
        <tr>            
            <td><?= $slot->Name; ?></td>
            <td><?= $slot->Theme; ?></td>
            <td><?= $slot->Math; ?></td>
            <td><?= $slot->SpinsTotal; ?></td>
            <td><?= $slot->SecsTotal; ?></td>            
            <td><a href="/admin_slots/add_slot/<?= $slot->ID?>" class="btn btn-primary">Edit</a></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50});
                
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
        } );
</script>
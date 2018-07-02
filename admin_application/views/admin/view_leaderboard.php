<div class="well">
    <button class="btn btn-success" id="btn_update_lb">Update the Leaderboard</button>
    <span style="text-align: right; margin-left: 100px;">
    <label>Update WinOdometer: </label>
    <input type="text" id="winodometer"/>
    <button class="btn btn-primary" id="btn_winodometer">Update</button>
    </span>
</div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>Screen Name</th>            
            <th>Total Prize</th>
            <th>Location</th>
            <th>Image</th>  
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($winners as $winner) : ?>
        <tr id="tr_<?= $winner->id; ?>">
            <td><?= $winner->screenName; ?></td>
            <td><?= $winner->prize; ?></td>            
            <td><?= $winner->location; ?></td>            
            <td><img src="<?= $winner->imageURL; ?>"/></td>
            <td><?= $winner->date; ?></td>
            <td><button class="btn btn-danger btn-delete-entry" rel="<?= $winner->id; ?>">Delete</button></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script>
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 4, "desc" ]]});
                $(".btn-delete-entry").click(function(){
                    var id = $(this).attr("rel");
                    $.post("/admin/ajax_delete_lb_entry", {id: id}, function(data){
                        if(data.success)
                            $("#tr_" + id).fadeOut();
                    }, 'json');
                });
                
                $("#btn_update_lb").click(function(){
                    $.post("/admin/ajax_update_leaderboard", {}, function(data){
                        if(data.success)
                        {
                            alert("LeaderBoard Updated");
                            location.reload();
                        }
                        else
                        {
                            alert("LeaderBoard Update FAILED!!");
                        }
                    }, 'json');
                });
                $("#btn_winodometer").click(function(){
                    $.post("/admin/ajax_update_winodometer", {winodometer: $("#winodometer").val()}, function(data){
                        if(data.success)
                            alert("WinOdometer Updated!");
                        else
                            alert("Invalid Number");
                    }, 'json');
                });
        } );
</script>
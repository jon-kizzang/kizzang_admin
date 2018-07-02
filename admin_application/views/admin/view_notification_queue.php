<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>            
            <th>Headings</th>
            <th>Contents</th>                        
            <th>Template</th>
            <th>Crons Left</th>
            <th>Created</th>             
            <th>Edit</th>
            <th>Delete</th>
            <th>Schedule</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($pns as $pn) : ?>
        <tr id="row_<?= $pn['id']; ?>">
            <td><?= $pn['id']; ?></td>
            <td><?= $pn['headings']; ?></td>            
            <td><?= $pn['contents']; ?></td>
            <td><?= $pn['template']; ?></td>
            <td><?= $pn['cnt']; ?></td>
            <td><?=$pn['updated']; ?></td>
            <td><a class="btn btn-primary" href="/admin/add_notifications/<?= $pn['id']; ?>">Edit</a></td>
            <td><button class="btn btn-danger delete-pn" rel="<?= $pn['id']; ?>">Delete</button></td>
            <td><a data-toggle="modal" data-target="#modal" class="btn btn-primary" href="/admin/add_pn_cron/<?= $pn['id']; ?>">Add Schedule</a></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 0, "asc" ]]});
                $("#btn_update_pn").click(function(){
                    $.get("/admin/ajax_update_pns", {}, function(){
                        alert("Update Complete");
                        location.reload();
                    }, 'json');
                });
                
                $(".delete-pn").click(function(){
                    var id = $(this).attr("rel");
                    var r = confirm("Are you sure you want to Delete this Job from queue?");
                    if(r)
                    {
                        $.get("/admin/ajax_delete_pn/" + id, {}, function(data){
                            $("#row_" + id).fadeOut();
                        });
                    }
                });
        } );
</script>
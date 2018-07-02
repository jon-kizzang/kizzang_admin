<div class="well">
    <button class="btn btn-success" id="btn_update_pn">Update History</button>
</div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>            
            <th>Headings</th>
            <th>Contents</th>  
            <th>Successful</th>
            <th>Failed</th>
            <th>Converted</th>
            <th>Remaining</th>
            <th>Created</th> 
        </tr>
    </thead>
    <tbody>
            <?php foreach($pns as $pn) : ?>
        <tr>
            <td><?= $pn->id; ?></td>
            <td><?= $pn->headings; ?></td>            
            <td><?= $pn->contents; ?></td>
            <td><?= $pn->successful; ?></td>
            <td><?= $pn->failed; ?></td>
            <td><?= $pn->converted; ?></td>
            <td><?= $pn->remaining; ?></td>
            <td><?=$pn->queued_at; ?></td>            
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 7, "desc" ]]});
                $("#btn_update_pn").click(function(){
                    $.get("/admin/ajax_update_pns", {}, function(){
                        alert("Update Complete");
                        location.reload();
                    }, 'json');
                });
        } );
</script>
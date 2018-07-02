<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>            
            <th>Name</th>
            <th>String</th>            
            <th>Created</th> 
            <th>Last Ran</th>
            <th>Next Run</th>
            <th>Edit</th>
            <th>Logs</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($crons as $cron) : ?>
        <tr>
            <td><?= $cron->id; ?></td>
            <td><?= $cron->name; ?></td>            
            <td><?= $cron->string; ?></td>
            <td><?=$cron->created; ?></td>
            <td><?=date("D M j, Y (h:i:s A)", strtotime($cron->lastRan)); ?></td>
            <td><?=date("D M j, Y (h:i:s A)", strtotime($cron->nextRun)); ?></td>
            <td><a class="btn btn-primary" href="/admin/add_cron/<?= $cron->id; ?>">Edit</a></td>
            <td><a class="btn btn-primary" data-target="#modal" data-toggle="modal" href="/admin/ajax_cron_history/<?= $cron->id; ?>">View</a></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 0, "asc" ]]});                
        } );
</script>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>Game Type</th>            
            <th># of Records</th>            
            <th>Edit</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($payouts as $payout) : ?>
        <tr>
            <td><?= $payout->gameType; ?></td>
            <td><?= $payout->cnt; ?></td>                        
            <td><a class="btn btn-primary" href="/admin/add_game_payout/<?= $payout->gameType; ?>">Edit</a></td>            
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 0, "asc" ]]});                
        } );
</script>
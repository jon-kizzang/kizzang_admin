<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>            
            <th>Serial Number</th>  
            <th>User Status</th>
            <th>Claim Status</th>
            <th>Entry</th>
            <th>Prize Name</th>
            <th>Prize Amount</th>            
            <th>Edit</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($rows as $row) : ?>
        <tr>            
            <td><?= $row->id; ?></td>
            <td><?= $row->serialNumber; ?></td>
            <td><?= $user_statuses[$row->playerActionChoice]; ?></td>
            <td><?= $claim_statuses[$row->status]; ?></td>
            <td><?= $row->entry; ?></td>
            <td><?= $row->prizeName; ?></td>
            <td><?= $row->prizeAmount; ?></td>
            <td><a class="btn btn-primary" href="/home/edit/<?= $row->id; ?>">Edit</a></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script>
    $(document).ready(function() {
        $('#show_games').dataTable({pageLength: 25, order: [[ 0, "desc" ]]});        
    });
</script>
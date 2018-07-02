<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>            
            <th>Name</th>  
            <th>Address</th>
            <th>Won YTD</th>            
            <th>Edit</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($players as $row) : ?>
        <tr>            
            <td><?= $row->playerId; ?></td>
            <td><?php if(isset($row->first_name)) echo $row->first_name . " " . $row->last_name; else echo "N/A"; ?></td>
            <td><?php if(isset($row->address)) echo $row->address . " " . $row->city . "," . $row->state; else echo "N/A"; ?></td>
            <td>$<?= number_format($row->total, 2); ?></td>                        
            <td><a class="btn btn-primary" href="/home/player/<?= $row->playerId; ?>">Edit</a></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script>
    $(document).ready(function() {
        $('#show_games').dataTable({pageLength: 25, order: [[ 0, "asc" ]]});        
    });
</script>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>Name</th>            
            <th>Wedge Count</th>  
            <th>Type</th>
            <th>Is Deleted</th>
            <th>Edit</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($wheels as $wheel) : ?>
        <tr>
            <td><?= $wheel->name; ?></td>
            <td><?= $wheel->numberOfWedges; ?></td>
            <td><?= $wheel->wheelType; ?></td>
            <td><?php if($wheel->isDeleted) echo 'Yes'; else echo 'No'; ?></td>
            <td><a class="btn btn-primary" href="/admin/add_wheel/<?= $wheel->id; ?>">Edit</a></td>            
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 0, "asc" ]]});                
        } );
</script>
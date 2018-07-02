<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>Name</th>            
            <th>Abbreviation</th>
            <th>Description</th>            
            <th>Panel</th>            
            <th>Edit</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($states as $state) : ?>
        <tr>
            <td><?= $state->name; ?></td>
            <td><?= $state->abbreviation; ?></td>            
            <td><?= $state->description; ?></td>
            <td><?=$state->panelColumn . " x " . $state->panelRow; ?></td>
            <td><a class="btn btn-primary" href="/admin/edit_state/<?= $state->id; ?>">Edit</a></td>            
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 0, "asc" ]]});                
        } );
</script>
<div id="message"></div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>            
            <th>Name</th>
            <th>Theme</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th># of Teams</th>
            <th>Edit</th> 
            <th>Delete</th>            
        </tr>
    </thead>
    <tbody>
            <?php foreach($brackets as $bracket) : ?>
        <tr id="tr_<?= $bracket->id; ?>">  
            <td><?= $bracket->name; ?></td>
            <td><?= $bracket->theme; ?></td>
            <td><?= date("D M j, Y", strtotime($bracket->startDate)); ?></td>
            <td><?= date("D M j, Y", strtotime($bracket->endDate)); ?></td>            
            <td><?= $bracket->numStartingTeams; ?></td>
            <td><a href="/admin_sports/add_bracket/<?= $bracket->id?>" class="btn btn-primary">Edit</a></td>
            <td><button type="button" rel="<?= $bracket->id; ?>" class="btn btn-danger delete-bgbracket">Delete</button></td>            
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50});                                                
        });
</script>
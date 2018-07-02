<div class="well">
    <label>Sporting Type: </label>
    <select id="sel_game_type">
        <option value="">All</option>
        <?php foreach($game_types as $type) : ?>
        <option value="<?= $type->id; ?>" <?php if($type->id == $cat_sel) echo 'Selected=""'; ?>><?= $type->name; ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div id="message"></div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>            
            <th>Sport</th>
            <th>Team 1</th>
            <th>Team 2</th>
            <th>Date</th>            
            <th>Edit</th>            
        </tr>
    </thead>
    <tbody>
            <?php foreach($sports as $sport) : ?>
        <tr>            
            <td><?= $sport->sport; ?></td>
            <td><?= $sport->team1; ?></td>
            <td><?= $sport->team2; ?></td>
            <td><?= $sport->date; ?></td>            
            <td><a href="/admin_sports/add_sports_schedule/<?= $sport->id?>" class="btn btn-primary">Edit</a></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 3, "asc" ]]});
                
                $("#sel_game_type").change(function(){
                    window.location = "/admin_sports/" + $(this).val();
                });
        } );
</script>
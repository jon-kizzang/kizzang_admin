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
            <th>Name</th>
            <th>Abbr</th>
            <th>Category</th>
            <th>Wins</th>
            <th>Losses</th>
            <th>Edit</th>            
        </tr>
    </thead>
    <tbody>
            <?php foreach($sports as $sport) : ?>
        <tr>            
            <td><?= $sport->name; ?></td>
            <td><?= $sport->abbr; ?></td>
            <td><?= $game_types[$sport->sportCategoryID]->name; ?></td>
            <td><?= $sport->wins; ?></td>
            <td><?= $sport->losses; ?></td>
            <td><a href="/admin_sports/edit_sports_teams/<?= $sport->sportCategoryID . "/" . $sport->id?>" data-target="#modal" data-toggle="modal" class="btn btn-primary">Edit</a></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 0, "asc" ]]});
                
                $("#sel_game_type").change(function(){
                    window.location = "/admin_sports/view_sports_teams/" + $(this).val();
                });
        } );
</script>
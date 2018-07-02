<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>Sponsor Name</th>            
            <th>Campaign ID</th>
            <th>StartDate</th>            
            <th>EndDate</th> 
            <th>Code</th>
            <th># of Games</th>
            <th>Edit Campaign</th>
            <th>Edit Games</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($affiliates as $affiliate) : ?>
        <tr>
            <td><?= $affiliate->name; ?></td>
            <td><?= $affiliate->id; ?></td>            
            <td><?=date("D M j, Y (h:i:s A)", strtotime($affiliate->start_date)); ?></td>
            <td><?=date("D M j, Y (h:i:s A)", strtotime($affiliate->end_date)); ?></td>
            <td><?= $affiliate->code; ?></td>
            <td><?=$affiliate->num_games; ?></td>
            <td><a class="btn btn-primary" target="_blank" href="/admin/advertising_campaign/<?= $affiliate->id; ?>">Edit Campaign</a></td>
            <td><a class="btn btn-primary" data-target="#modal" data-toggle="modal" href="/admin/affiliate/<?= $affiliate->id; ?>">Edit Games</a></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 0, "asc" ]]});                
        } );
</script>
<style>
    #mask {
            opacity: .3;
            background-color: #000;
            display: none;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            position: fixed;
            z-index: 1000;
    }
    
    #mask_message {
            z-index: 1001;
            height: 100px;
            width: 200px;
            background-color: #FFF;
            position: fixed;
            display: none;
            padding: 40px;
    }
</style>
<div id="message"></div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>Name</th>  
            <th>Type</th>
            <th>Contact Name</th>
            <th>Email</th>
            <th>Location</th>
            <th>Action</th>                        
        </tr>
    </thead>
    <tbody>
            <?php foreach($sponsors as $sponsor) : ?>
        <tr>
            <td><?= $sponsor->name; ?></td>
            <td><?= $sponsor->sponsorType; ?></td>
            <td><?= $sponsor->contactName; ?></td>
            <td><?= $sponsor->contactEmail; ?></td>
            <td><?= $sponsor->address . " " . $sponsor->city . ", " . $sponsor->state . " " . $sponsor->zip; ?></td>            
            <td><a href="/admin/edit_sponsor/<?= $sponsor->id; ?>" class="btn btn-primary">Edit</a></td>       
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<div id="mask">
</div>
<div id="mask_message">Generating Cards</div>
<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 0, "asc" ]]});
        } );
</script>
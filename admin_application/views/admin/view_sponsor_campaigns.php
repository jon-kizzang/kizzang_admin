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
            <th>Sponsor Name</th>            
            <th>Name</th>
            <th>Gender</th>
            <th>Type</th>
            <th>Date Range</th>
            <th>Age Range</th>
            <th>Image</th>
            <th>Action</th>                        
        </tr>
    </thead>
    <tbody>
            <?php foreach($sponsors as $sponsor) : ?>
        <tr id="tr_<?= $sponsor->id; ?>">
            <td><?= $sponsor->sponsor; ?></td>            
            <td><?= $sponsor->name; ?></td>
            <td><?= $sponsor->gender_name; ?></td>
            <td><?= $sponsor->campaign_type; ?></td>
            <td><?= $sponsor->startDate . " - " . $sponsor->endDate; ?></td>
            <td><?= $sponsor->ageMin . " - " . $sponsor->ageMax; ?></td>
            <td style="background-color:#F9F9F9;"><img style="max-height: 100px;" src="<?= $sponsor->artAssetUrl; ?>"/></td>            
            <td><a href="/admin/edit_sponsor_campaign/<?= $sponsor->id; ?>" class="btn btn-primary">Edit</a>
                <button class="btn btn-danger btn-delete-campaign" rel="<?= $sponsor->id; ?>">Delete</button>
            </td>       
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
                $("body").on("click", ".btn-delete-campaign", function(){
                    var r = confirm("Are you sure you want to delete this campaign?");
                    if(r)
                    {
                        var id = $(this).attr("rel");
                        $.get("/admin/ajax_delete_sponsor_campaign/" + id, {}, function(data){
                            if(data.success)
                            {
                                alert("Campaign Removed");
                                $("#tr_" + id).remove();
                            }
                        }, 'json');
                    }
                });
        } );
</script>
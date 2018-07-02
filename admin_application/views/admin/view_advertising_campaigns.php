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
<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>            
            <th>Source</th>
            <th>Medium</th>
            <th>A/B Test</th>
            <th>Campaign ID</th>
            <th>Created</th>
            <th>Action</th>                        
        </tr>
    </thead>
    <tbody>
            <?php foreach($campaigns as $campaign) : ?>
        <tr>
            <td><?= $campaign->id; ?></td>            
            <td><?= $campaign->name; ?></td>
            <td><?= $campaign->advertising_medium_id; ?></td>
            <td><?= $campaign->utm_content; ?></td> 
            <td><?= $campaign->utm_campaign; ?></td>
            <td><?= $campaign->created; ?></td>
            <td><a href="/admin/advertising_campaign/<?= $campaign->id; ?>" class="btn btn-primary">Edit</a></td>       
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<div id="mask">
</div>
<div id="mask_message">Generating Cards</div>
<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 5, "desc" ]]});
                
                $("#btn_update_db").click(function(){
                    $.post("/marketing_campaigns/update_db", {}, function(data){
                        if(data.success)
                        {
                            $("#message").html("DB Updated");
                            location.reload();
                        }
                    }, 'json');
                });
        } );
</script>
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
<div class="well"><button class="btn btn-success" id="btn_update_db">Update from EMercury</button></div>
<div id="message"></div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>            
            <th>Subject</th>
            <th>From Email</th>
            <th># Subscribers</th>
            <th>Action</th>                        
        </tr>
    </thead>
    <tbody>
            <?php foreach($campaigns as $campaign) : ?>
        <tr>
            <td><?= $campaign->id; ?></td>            
            <td><?= $campaign->subject; ?></td>
            <td><?= $campaign->from_address; ?></td>
            <td><?= $campaign->cnt; ?></td>
            <td><a href="/marketing_campaigns/add/<?= $campaign->id; ?>" class="btn btn-primary">Edit</a></td>       
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
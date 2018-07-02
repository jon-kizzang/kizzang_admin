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
    
    .status {
        border: 1px solid;
        padding: 5px;
        margin: auto;
        width: 50%;
        border-radius: 4px;
        text-align: center;
    }
</style>
<div class="well">
    <form method="POST" action="/admin/players">
        <label>Search:</label>
        <input type="text" name="search" id="search"/>
        <button class="btn btn-primary" id="btn_search" style="margin-left: 20px;">Search</button>
    </form>
</div>
<div id="message"></div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Status</th>
            <th>Screen Name</th>            
            <th>First Name</th>
            <th>Last Name</th>
            <th>DOB</th>
            <th>FBID</th>
            <th>Account Name</th>
            <th>Status</th>
            <th>Date</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($players as $player) : ?>
        <tr>
            <td><?= $player->id; ?></td>
            <td><div class="status <?= $css[$player->status]; ?>"><?= $player->status; ?></div></td>
            <td><?= $player->screenName; ?></td>
            <td><?= $player->firstName; ?></td>            
            <td><?= $player->lastName; ?></td>
            <td><?= date("Y-m-d", strtotime($player->dob)); ?></td>
            <td><?= $player->fbId; ?></td>
            <td><?= $player->accountName; ?></td>
            <td><?= $player->accountStatus; ?></td>
            <td><?= $player->created; ?></td>
            <td><a class="btn btn-primary" href="/admin/edit_player/<?= $player->id; ?>">Edit</a></td>            
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<div id="mask">
</div>
<div id="mask_message">Generating Cards</div>
<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 0, "desc" ]]});
                
                $(".add_cards").click(function(){
                        $("#mask").show();
                        $("#mask_message").css({left: window.innerWidth / 2 - 100, top: window.innerHeight / 2 -50}).show();;
                        $(this).prop('disabled', true);
                        var id = $(this).attr('rel');
                        $.get("/admin/ajax_increment_cards/" + id, {}, function(data){
                                $("#mask").hide();
                                $("#mask_message").hide();
                                if(data.success)
                                {
                                        $("#message").html("Cards Created Successfully!").addClass("alert alert-success");
                                        setTimeout("window.location.reload();", 3000);
                                }
                                else
                                {
                                        
                                }
                        }, 'json');
                });
        } );
</script>
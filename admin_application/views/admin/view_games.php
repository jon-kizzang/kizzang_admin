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
            <th>Serial #</th>
            <th>Name</th>
            <th>Total Cards</th>
            <th>Total Winning Cards</th>
            <th>Cards Played</th>
            <th>Cards Left</th>
            <th>Start Date</th>
            <th>End Date</th>            
            <th>Edit</th>
            <th>Add Cards</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($games as $game) : ?>
        <tr>
            <td><?= $game->SerialNumber; ?></td>
            <td><?= $game->Name; ?></td>
            <td><?= $game->TotalCards; ?></td>
            <td><?= $game->TotalWinningCards; ?></td>
            <td><?= $game->played; ?></td>
            <td><?= $game->TotalCards - $game->played; ?></td>
            <td><?= $game->StartDate; ?></td>
            <td><?= $game->EndDate; ?></td>
            <td><a href="/admin/edit_game/<?= $game->ID?>" class="btn btn-primary">Edit</a></td>
            <td><a href="javascript:void(0);" rel="<?= $game->ID?>" class="btn btn-danger add_cards">Add Cards</a></td>
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
                                        setTimeout("window.location.reload();", 1000);
                                }
                                else
                                {
                                        
                                }
                        }, 'json');
                });
        } );
</script>
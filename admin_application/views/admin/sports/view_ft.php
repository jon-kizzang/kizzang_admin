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
            padding: 20px;
            text-align: center;
    }
    
    #frm_power_rank div {
        margin-left: 10px;
    }
</style>
<div id="message"></div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>            
            <th>ID</th>
            <th>Name</th>
            <th>Serial Number</th>
            <th>Card Prize</th>            
            <th>Date</th>
            <th>Card Count</th>
            <th>Grade Cards</th>
            <th>View Results</th>
            <th>Edit</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($fts as $ft) : ?>
        <tr id="tr_config_<?= $ft->id; ?>">            
            <td><?= $ft->id; ?></td>
            <td><?= $ft->theme; ?></td>
            <td><?= $ft->serialNumber; ?></td>
            <td><?= $ft->prizes; ?></td>
            <td><?= $ft->startDate; ?></td>   
            <td><?= $ft->cnt; ?></td>
            <td><button class="btn btn-primary" id="grade_cards" rel="<?= $ft->id; ?>">Grade Cards</button></td>
            <td><?php if($ft->pickHash) : ?><a data-target="#big-modal" data-toggle="modal" href="/admin_sports/view_ft_winners/<?= $ft->id?>" class="btn btn-success">View Winners</a><?php else : ?>N/A<?php endif; ?></td>
            <td><a href="/admin_sports/add_ft_config/<?= $ft->id?>" class="btn btn-primary">Edit</a></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<div id="mask">
</div>
<div id="mask_message">Grading F3 Cards</div>

<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 1, "asc" ]]});                                                
                $("#grade_cards").click(function(){
                    $("#mask").show();
                    $("#mask_message").html("Grading Cards");
                    $("#mask_message").css({left: window.innerWidth / 2 - 100, top: window.innerHeight / 2 -50}).show();
                    var id = $(this).attr('rel');
                    $.get("/admin_sports/ajax_grade_ft_cards/" + id, {}, function(data){
                        if(data.success)
                        {
                            $("#mask_message").html("Cards Graded");
                            $("#mask").hide();
                            setTimeout('$("#mask_message").hide();', 2000);
                        }
                        else
                        {
                            $("#mask_message").html("Error in Grading Cards!!");
                            $("#mask").hide();
                            setTimeout('$("#mask_message").hide();', 4000);
                        }
                    }, 'json');
                });
        } );
</script>
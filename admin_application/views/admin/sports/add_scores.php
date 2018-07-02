<div id="message"></div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>        
            <th>Date</th>
            <th>Category</th>
            <th>Team 1</th>           
            <th>Team 2</th>            
            <th>Tie</th>            
        </tr>
    </thead>
    <tbody>
            <?php foreach($events as $event) : ?>
        <tr id="tr_<?= $event->sportScheduleId;?>">       
            <td><?= $event->date; ?> <input type="hidden" id="<?= $event->sportScheduleId;?>_parlay_id" value="<?= $event->parlay_ids; ?>"/><input type="hidden" id="<?= $event->sportScheduleId;?>_event_id" value="<?= $event->sportScheduleId; ?>"/></td>
            <td><?= $event->category; ?><input type="hidden" id="<?= $event->sportScheduleId;?>_team2" value="<?= $event->team2;?>"/><input type="hidden" id="<?= $event->sportScheduleId;?>_team1" value="<?= $event->team1;?>"/></td>
            <td><button class="btn btn-primary" onclick="pick_winner(<?= $event->sportScheduleId;?>, <?= $event->team1;?>)"><?= $event->team1Name; ?></button></td>            
            <td><button class="btn btn-primary" onclick="pick_winner(<?= $event->sportScheduleId;?>, <?= $event->team2;?>)"><?= $event->team2Name; ?></button></td>           
            <td><a href="javascript:void(0);" class="btn btn-danger" onclick="pick_winner(<?= $event->sportScheduleId;?>, 0)">Tie</a></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript" charset="utf-8">
    function pick_winner(id, team_id)
    {
        var team1 = 1;
        var team2 = 1;
        
        if(team_id === parseInt($("#" + id + "_team1").val()))
            team1 = 2;
        else if(team_id === parseInt($("#" + id + "_team2").val()))
            team2 = 2;
        var data = {
            parlay_id: $("#" + id + "_parlay_id").val(),
            event_id: $("#" + id + "_event_id").val(),
            team1_score: team1,
            team2_score: team2,
            team1: $("#" + id + "_team1").val(),
            team2: $("#" + id + "_team2").val(),
            sportScheduleId: id
        };

        $.post("/admin_sports/ajax_add_event_scores", data, function(data){
            if(data.success)
            {
                $("#tr_" + id).html("<td colspan=7 style='text-align:center;'>Added</td>");
                var command = '$("#tr_' + id + '").remove();';
                setTimeout(command, 500);
            }
            else
            {
                var mess = "";
                for(var key in data.errors)
                    mess = mess + key + ": " + data.errors[key] + "\n";
                alert("Errors were as follows:\n" + mess);
            }
        }, 'json');
    }
    
    $(document).ready(function() {
            $('#show_games').dataTable({pageLength: 50, order: [[ 0, "asc" ]]});
    } );
</script>
<div class="well">    
    <a href="/admin/pick_winners" class="btn btn-primary">Sweepstakes</a>    
    <a href="/admin/pick_winners/Slots" class="btn btn-primary">Slots</a>
    <a href="/admin/pick_winners/Parlay" class="btn btn-primary">Parlay</a>
    <a href="/admin/pick_winners/FT" class="btn btn-primary">Final Three</a>    
    <a href="javascript:void(0);" class="btn btn-success">Big Game 21</a>
    <a href="/admin/pick_winners/Lottery" class="btn btn-primary">Lottery</a>
</div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>            
            <th>Start Date</th>
            <th>End Date</th>
            <th>Prize</th>            
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($winners as $winner) : ?>
        <tr id="tr_<?= $winner->parlayCardId; ?>">
            <td><?= $winner->parlayCardId; ?></td>
            <td><?= $winner->name; ?></td>            
            <td><?= date("D M j, Y", strtotime($winner->startDate)); ?></td>
            <td><?= date("D M j, Y", strtotime($winner->endDate)); ?></td>
            <td><?= $winner->cardWin; ?></td>                        
            <td><button class="btn btn-primary pick-winner" rel="<?= $winner->parlayCardId; ?>">Pick Winner(s)</button></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script>
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 2, "asc" ]]});
                $(".pick-winner").click(function(){
                    $(this).attr("disabled", "disabled");
                    var id = $(this).attr("rel");
                    $.post("/admin/ajax_pick_winner", {id: id, type: 'BG'}, function(data){
                        if(data.success)
                        {
                            $("#tr_" + id).fadeOut();
                            alert(data.message);
                        }
                        else
                        {
                            alert("Something Went wrong with Creating These winners");
                        }
                    }, 'json');
                });
        } );
</script>
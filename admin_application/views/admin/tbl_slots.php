<div class="well">    
    <a href="/admin/pick_winners" class="btn btn-primary">Sweepstakes</a>
    <a href="javascript:void(0);" class="btn btn-success">Slots</a>
    <a href="/admin/pick_winners/Parlay" class="btn btn-primary">Parlay</a>
    <a href="/admin/pick_winners/FT" class="btn btn-primary">Final Three</a>
    <a href="/admin/pick_winners/BG" class="btn btn-primary">Big Game 21</a>
    <a href="/admin/pick_winners/Lottery" class="btn btn-primary">Lottery</a>
</div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Start Date</th>            
            <th>End Date</th>
            <th>Type</th>
            <th>Prize List</th>            
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($winners as $winner) : ?>
        <tr id="tr_<?= $winner->id; ?>">
            <td><?= $winner->id; ?></td>
            <td><?= date("D M j, Y H:i:s", strtotime($winner->StartDate)); ?></td>            
            <td><?= date("D M j, Y H:i:s", strtotime($winner->EndDate)); ?></td>            
            <td><?= $winner->type; ?></td>
            <td><?= $winner->PrizeList; ?></td>                        
            <td><button class="btn btn-primary pick-winner" rel="<?= $winner->id; ?>">Pick Winner(s)</button></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script>
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 100, order: [[ 1, "asc" ]]});
                $("body").on('click',".pick-winner", function(){
                    $(this).attr("disabled", "disabled");
                    var id = $(this).attr("rel");
                    $.post("/admin/ajax_pick_winner", {id: id, type: 'Slots'}, function(data){
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
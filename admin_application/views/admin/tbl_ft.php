<div class="well">    
    <a href="/admin/pick_winners" class="btn btn-primary">Sweepstakes</a>    
    <a href="/admin/pick_winners/Slots" class="btn btn-primary">Slots</a>
    <a href="/admin/pick_winners/Parlay" class="btn btn-primary">Parlay</a>
    <a href="javascript:void(0);" class="btn btn-success">Final Three</a>
    <a href="/admin/pick_winners/BG" class="btn btn-primary">Big Game 21</a>
    <a href="/admin/pick_winners/Lottery" class="btn btn-primary">Lottery</a>
</div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Serial Number</th>            
            <th>Card Date</th>
            <th>Prize</th>            
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($winners as $winner) : ?>
        <tr id="tr_<?= $winner->id; ?>">
            <td><?= $winner->id; ?></td>
            <td><?= $winner->serialNumber; ?></td>            
            <td><?= date("D M j, Y", strtotime($winner->endDate)); ?></td>            
            <td><?= $winner->prizes; ?></td>                        
            <td><button class="btn btn-primary pick-winner" rel="<?= $winner->id; ?>">Pick Winner(s)</button></td>
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
                    $.post("/admin/ajax_pick_winner", {id: id, type: 'FT'}, function(data){
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
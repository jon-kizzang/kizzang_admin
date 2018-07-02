<div class="well">
    <a href="javascript:void(0);" class="btn btn-success">Sweepstakes</a>
    <a href="/admin/pick_winners/Slots" class="btn btn-primary">Slots</a>
    <a href="/admin/pick_winners/Parlay" class="btn btn-primary">Parlay</a>
    <a href="/admin/pick_winners/FT" class="btn btn-primary">Final Three</a>
    <a href="/admin/pick_winners/BG" class="btn btn-primary">Big Game 21</a>
    <a href="/admin/pick_winners/Lottery" class="btn btn-primary">Lottery</a>
</div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>Contest Name</th>
            <th>Max Winners</th>            
            <th># of Entries</th>
            <th>Value of Prize</th>            
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($winners as $winner) : ?>
        <tr id="tr_<?= $winner->id; ?>">
            <td><?= $winner->name; ?></td>            
            <td><?= $winner->maxWinners; ?></td>            
            <td><?= $winner->num_entries; ?></td>            
            <td><?= $winner->taxValue; ?></td>
            <td><button class="btn btn-primary pick-winner" rel="<?= $winner->id; ?>">Pick Winner(s)</button></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script>
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 4, "desc" ]]});
                $(".pick-winner").click(function(){
                    $(this).attr("disabled", "disabled");
                    var id = $(this).attr("rel");
                    $.post("/admin/ajax_pick_winner", {id: id, type: 'Sweepstakes'}, function(data){
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
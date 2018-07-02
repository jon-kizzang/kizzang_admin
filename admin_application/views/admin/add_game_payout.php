<div class="panel panel-primary" style="margin-bottom: 0px;">
    <div class="panel-heading">Add / Edit Game Payout</div>
    <div class="panel-body">
        <div class="form-group">
            <label>Select Game Type</label>
            <select class="form-control" id="game_type">
                <option>Select Game Type to add Payout To</option>
                <?php foreach($gameTypes as $gameType) : ?>
                <option value="<?= $gameType; ?>" <?php if($gameType == $currentGameType) echo "selected='selected'"; ?>><?= $gameType; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="panel panel-primary" style="margin-bottom: 0px;">
            <div class="panel-heading">Payouts</div>
            <div class="panel-body" id="game_types">
                <?= $tbl; ?>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
        $(document).on("click", ".add-payout", function(){
           var id = $(this).attr("rel");
           $.post("/admin/ajax_add_game_payout", $("#frm_" + id).serialize(), function(data){
               if(data.success)
                   location.reload();
               else
                   alert("Record not updated / added");
           }, "json");
        });
        
        $("#game_type").change(function(){
            location.href = "/admin/add_game_payout/" + $(this).val();
        });
    });
</script>
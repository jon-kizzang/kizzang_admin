<div class="panel panel-primary" style="margin-bottom: 0px;">
    <div class="panel-heading">Affiliate Games</div>
    <form id="frmGames">
        <div class="panel-body" id="game_body">
        <input type="hidden" name="id" value="<?= $id; ?>"/>
        <?php $index = 0; ?>
        <?php foreach($games as $index => $game) : ?>        
            <div class="form-inline" id="div<?= $index; ?>" style="margin-top: 10px;">
            <div class="form-group">
              <label for="GameType">Game Type</label>
              <select name="game[<?= $index; ?>][GameType]" id="GameType<?= $index; ?>" class="form-control game-type" rel="<?= $index; ?>">
                  <?php foreach($gameTypes as $gameType) : ?>
                  <option value="<?= $gameType; ?>" <?php if($game->GameType == $gameType) echo 'selected=""'; ?>><?= $gameType; ?></option>
                  <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="GameType">Theme</label>
              <select name="game[<?= $index; ?>][Theme]" id="Theme<?= $index; ?>" class="form-control">
                  <?php foreach($themes[$game->GameType] as $theme) : ?>
                  <option value="<?= $theme; ?>" <?php if($game->Theme == $theme) echo 'selected=""'; ?>><?= $theme; ?></option>
                  <?php endforeach; ?>
              </select>
            </div>
                <button type="submit" class="btn btn-danger remove-game" style="float: right;" rel="<?= $index; ?>">Remove Game</button>
            </div>
        <?php endforeach; ?>
    </div>
    </form>
    <input type="hidden" id="cnt" value="<?= $index + 1; ?>"/>
    <div class="panel-footer" style="text-align: right;">
        <button type="button" id="add_game" class="btn btn-primary">Add Game</button>
        <button type="button" id="save_game" class="btn btn-success">Save Games</button>
        <button style="margin-left: 20px;" id="close_game" class="btn btn-default" type="button" data-dismiss="modal">Close</button>
    </div>
</div>

<script>
var themes = <?= json_encode($themes); ?>;
$(function() {
        $(document).on('click', '.remove-game', function(e){
            e.preventDefault();
            var id = $(this).attr('rel');
            $("#div" + id).remove();
        });
        
        $(document).on('change','.game-type',function(e){
            e.preventDefault();
            var id = $(this).attr('rel');
            var type = $(this).val();
            var options = [];
            $.each(themes[type], function(key,val){
               options.push($("<option>").val(val).html(val));
            });
            $("#Theme" + id).html(options);
        });
        
        $("#save_game").click(function(e){
            $.post("/admin/ajax_update_affiliate_games", $("#frmGames").serialize(), function(data){
                if(data.success)
                {
                    alert('Games Added');
                    $("#close_game").click();
                }
            }, 'json');
        });
        
        $("#add_game").click(function(e){
            e.preventDefault();
            var id = $("#cnt").val();
            $.get("/admin/ajax_add_affiliate_game/" + id, {}, function(data){
                $("#game_body").append(data);
                $("#cnt").val(parseInt(id) + 1);
            }, 'html');
        });
        
    });
</script>
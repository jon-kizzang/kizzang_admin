<div class="panel panel-green">
<div class="panel-heading"><?php if($game) : ?>Edit<?php else : ?>Add<?php endif; ?> Bottom Bar Game</div>
<div class="panel-body">
    <div id="game_message"></div>
   <form role="form" id="frm_game">
   <?php if($game) : ?><input type="hidden" name="id" value="<?= $game->id; ?>"/><?php endif; ?>
  <div class="form-group" id="div_gameType">
    <label for="gameType">Game Type</label>
    <input type="text" class="form-control" id="gameType" name="gameType" placeholder="gameType" <?php if($game) : ?> value="<?= $game->gameType?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="div_maxGames">
    <label for="maxGames">Max Games</label>
    <input type="text" class="form-control" id="maxGames" name="maxGames" placeholder="maxGames" <?php if($game) : ?> value="<?= $game->maxGames?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="div_displayName">
    <label for="displayName">Display Name</label>
    <input type="text" class="form-control" id="displayName" name="displayName" placeholder="displayName" <?php if($game) : ?> value="<?= $game->displayName?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="div_theme">
    <label for="theme">Theme</label>
    <input type="text" class="form-control" id="theme" name="theme" placeholder="theme" <?php if($game) : ?> value="<?= $game->theme?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="div_displayOrder">
    <label for="displayOrder">Display Order</label>
    <input type="text" class="form-control" id="displayOrder" name="displayOrder" placeholder="displayOrder" <?php if($game) : ?> value="<?= $game->displayOrder?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="div_comingSoon">
        <label>
                Coming Soon?
            </label>
            <label class="radio-inline">
                <input type="radio" name="comingSoon" value="0" <?php if($game && $game->comingSoon == 0) : ?>checked="checked"<?php endif; ?>> No
            </label>
            <label class="radio-inline">
                <input type="radio" name="comingSoon" value="1" <?php if(!$game || $game->comingSoon == 1) : ?>checked="checked"<?php endif; ?>> Yes
            </label>
    </div>   
</form>
</div>
<div class="panel-footer" style="text-align: right;"><button type="button" id="update_game" class="btn btn-success"><?php if($game) : ?>Update<?php else : ?>Add<?php endif; ?> Game</button></div>
</div>

<script>
$(function() {
        $("#update_game").click(function(){
                $.post('/admin/ajax_add_game', $("#frm_game").serialize(), function(data){
                        $("#frm_game div").removeClass('alert-danger');
                        if(data.success)
                        {
                                $("#game_message").html("Insert / Update was good.").addClass("alert alert-success").removeClass('alert-danger');
                                $('html,body').scrollTop(0);
                                var command = "window.location.href = '/admin/games';";
                                setTimeout(command, 1000);
                        }
                        else
                        {
                                $("#game_message").addClass("alert alert-danger").html("There were errors. They are listed / highlighted below.");
                                for(var key in data.errors)
                                    $("#div_" + key).addClass("alert-danger");
                        }
                },'json');
        });
    });
</script>
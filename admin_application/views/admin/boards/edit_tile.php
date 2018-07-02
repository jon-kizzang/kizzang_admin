<div class="panel panel-primary">
    <div class="panel-heading"><?php if($tile) : ?>Edit<?php else : ?>Add<?php endif;?> Game</div>
    <div class="panel-body">
        <div id="game_message"></div>
        <form role="form" id="frm_tile">
            <?php if($tile) : ?> <input type="hidden" name="id" value="<?=$tile->id?>"/> <?php endif; ?>
    <div class="form-group" id="div_name">
    <label for="Name">Name</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="name" value="<?php if($tile) echo $tile->name; ?>">
  </div>
  <div class="form-group" id="div_SerialNumber">
    <label for="SerialNumber">Tile Type</label>
    <select class="form-control" name="type" id="type">
        <option value="">Select Type</option>
        <?php foreach($types as $type) : ?>
        <option value="<?=$type; ?>" <?php if($tile && $tile->type == $type) echo "selected='selected'"; ?>><?= $type;?></option>
        <?php endforeach; ?>
    </select>
  </div>
    <div class="form-group" id="div_x">
    <label for="x">X Value</label>
    <input type="text" class="form-control" id="x" name="x" placeholder="x" value="<?php if($tile) echo $tile->x; ?>">
  </div>   
    <div class="form-group" id="div_y">
    <label for="y">Y Value</label>
    <input type="text" class="form-control" id="y" name="y" placeholder="y" value="<?php if($tile) echo $tile->y; ?>">
    </div>
    <div class="form-group" id="div_width">
    <label for="width">Width</label>
    <input type="text" class="form-control" id="width" name="width" placeholder="width" value="<?php if($tile) echo $tile->width; ?>">
  </div>
    <div class="form-group" id="div_height">
    <label for="height">Height</label>
    <input type="text" class="form-control" id="height" name="height" placeholder="height" value="<?php if($tile) echo $tile->height; ?>">
  </div>
    <div class="form-group">
        <label>Image</label>
        <?php if($tile) : ?><div style="background: url('/images/citySquaresTileAtlas.png') -<?= $tile->x?>px -<?= $tile->y?>px; height:<?= $tile->height; ?>px; width:<?= $tile->width;?>px;"></div><?php endif; ?>
    </div>
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_tile" class="btn btn-primary"><?php if($tile) : ?>Update<?php else : ?>Add<?php endif;?></button></div>
</form>
</div>

<script>
$(function() {
    $("#update_tile").click(function(){
        $.post("/boards/ajax_update_tile", $("#frm_tile").serialize(), function(data){
            $("#game_message").html("Information Updated").addClass("alert alert-success");                                
            var command = "location.reload();";
            $('html,body').scrollTop(0);
            setTimeout(command, 1000);
        }, 'json');
    });
});
</script>
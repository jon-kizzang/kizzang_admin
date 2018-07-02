<?php if($state) : ?>
<div class="panel panel-primary">
    <div class="panel-heading">Map</div>
    <div class="panel-body" id="div_map">
      <?php foreach($spots as $spot) : ?>
    <img id="<?= $spot->id . '_spot_' . $spot->orig_x . '_' . $spot->orig_y; ?>" class="draggable" src="https://kizzang-resources-admin.s3.amazonaws.com/map/day_spot.gif" style="position: absolute; z-index: 1000; top: <?= $spot->y + 72 ; ?>px; left: <?= $spot->x + 250; ?>px;"/>
    <?php endforeach; ?>
    
    <?php foreach($ads as $ad) : ?>
    <img id="<?= $ad->id . '_ad_' . $ad->orig_x . '_' . $ad->orig_y; ?>" class="draggable" src="<?= $ad->image; ?>" style="position: absolute; z-index: 1000; top: <?= $ad->y + 108; ?>px; left: <?= $ad->x + 297; ?>px;"/>
    <?php endforeach; ?>
    <img src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_<?= sprintf("%02d", $state->panelRow * 13 + $state->panelColumn + 1); ?>.png" />
    <button class="btn btn-primary" id="btn_dayspot">Add Day Spot</button>
    <input type="hidden" id="counter" value="1"/>
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_map" class="btn btn-primary">Update Map</button></div>
</div>
<?php endif; ?>

<div class="panel panel-primary">
    <div class="panel-heading"><?php if($state) : ?>Edit<?php else : ?>Add<?php endif;?> Game</div>
    <div class="panel-body">
        <div id="game_message"></div>
        <form role="form" id="frm_state">
            <?php if($state) : ?> <input type="hidden" name="id" id="id" value="<?=$state->id?>"/> <?php endif; ?>
    <div class="form-group" id="div_name">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="name" value="<?php if($state) echo $state->name; ?>">
  </div>
  <div class="form-group" id="div_abbreviation">
    <label for="abbreviation">Abbreviation</label>
    <input type="text" class="form-control" id="abbreviation" name="abbreviation" placeholder="abbreviation" value="<?php if($state) echo $state->abbreviation; ?>">
  </div>
    <div class="form-group" id="div_description">
    <label for="description">Description</label>
    <textarea class="form-control" id="description" name="description" placeholder="description"><?php if($state) echo $state->description; ?></textarea>
  </div>    
    <div class="form-group" id="div_panelColumn">
    <label for="panelColumn">Panel Column</label>
    <input type="text" class="form-control" id="panelColumn" name="panelColumn" placeholder="panelColumn" value="<?php if($state) echo $state->panelColumn; ?>">
  </div>
    <div class="form-group" id="div_panelRow">
    <label for="panelRow">Panel Row</label>
    <input type="text" class="form-control" id="panelRow" name="panelRow" placeholder="panelRow" value="<?php if($state) echo $state->panelRow; ?>">
  </div>    
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_state" class="btn btn-primary"><?php if($state) : ?>Update<?php else : ?>Add<?php endif;?> State</button></div>
</form>
</div>

<script>
var updates = {};
  $(document).ready(function(){
      $(".draggable").draggable({       
              stop: function(e, ui){
                  var id = ui.helper.attr("id");
                  var data = {
                      id: id,
                      x: ui.position.left,
                      y: ui.position.top
                  };
                  
                  //Check to see if the image is completely in one panel
                  var image_height, image_width;
                  $("<img/>").attr('src', $("#" + id).attr('src')).load(function(){
                      image_height = this.height;
                      image_width = this.width;
                      console.log(this.width);
                      if(Math.floor((data.x - 300) / 960) != Math.floor((data.x - 300 + image_width)/960) || Math.floor((data.y - 112) / 720) != Math.floor((data.y - 112 + image_height)/720))
                        {                      
                            alert("Invalid image placement!");
                            $("#" + id).css('top', ui.originalPosition.top).css('left', ui.originalPosition.left);
                        }
                        else
                        {    
                            updates[id] = data;
                        }
                  });                                    
                  
                 console.log(updates);
              }
          });
          
          $("#update_state").click(function(){
               $.post("/admin/ajax_update_state", $("#frm_state").serialize(), function(data){
                   $("#frm_parlay div").removeClass('alert-danger');
                        if(data.success)
                        {
                                $("#game_message").html("State Info Saved").addClass("alert alert-success").removeClass('alert-danger');                                
                                var command = "window.location = '/admin/view_states';";
                                setTimeout(command, 1000);
                        }
                        else
                        {
                                $("#game_message").addClass("alert alert-danger").html("There were errors. They are listed / highlighted below.");
                                for(var key in data.errors)
                                    $("#div_" + key).addClass("alert-danger");
                        }
               }, 'json');
          });
          
          $("#update_map").click(function(){
            if(updates)
            {
                $.post('/admin/ajax_map_entry', {entries: updates, type: 'state'}, function(data){
                    if(data.success)
                    {
                        alert("Changes Saved");
                    }
                    else
                    {
                        alert("Changes were not saved.");
                    }
                }, 'json');
            }
          });
          
          $("#btn_dayspot").click(function(){
              var spot = $("<img>").attr('src', 'https://kizzang-resources-admin.s3.amazonaws.com/map/day_spot.gif').attr('id', 'new_spot_' + $("#id").val() + '_' + $("#counter").val()).addClass('draggable ui-draggable ui-draggable-handle').css('top', 432).css('left', 1150).css('position', 'absolute');
              $("#counter").val(parseInt($("#counter").val()) + 1);
              $("#div_map").append(spot);
              $(".draggable").draggable({       
              stop: function(e, ui){
                  var id = ui.helper.attr("id");
                  var data = {
                      id: id,
                      x: ui.position.left,
                      y: ui.position.top
                  };
                  
                  //Check to see if the image is completely in one panel
                  var image_height, image_width;
                  $("<img/>").attr('src', $("#" + id).attr('src')).load(function(){
                      image_height = this.height;
                      image_width = this.width;
                      console.log(this.width);
                      if(Math.floor((data.x - 300) / 960) != Math.floor((data.x - 300 + image_width)/960) || Math.floor((data.y - 112) / 720) != Math.floor((data.y - 112 + image_height)/720))
                        {                      
                            alert("Invalid image placement!");
                            $("#" + id).css('top', ui.originalPosition.top).css('left', ui.originalPosition.left);
                        }
                        else
                        {    
                            updates[id] = data;
                        }
                  });                                    
                  
                  //console.log(updates);
              }
          });
          });
  });
  
</script>
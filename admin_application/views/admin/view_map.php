<button id="btn_update_positions" class="btn btn-primary" style="position: fixed; top: 52px; right: 0px; z-index: 11000"><?php if($type == "Placement") : ?>Update Positions<?php else : ?>Update Beziers<?php endif; ?></button>
<a class="btn btn-primary" style="position: fixed; top: 110px; right: 0px; z-index: 11000" href="<?php if($type == "Placement") : ?>/admin/map_sponsor_campaign/bezier<?php else : ?>/admin/map_sponsor_campaign<?php endif; ?>"><?php if($type == "Placement") : ?>View Bezier Curves<?php else : ?>View Placements<?php endif; ?></a>
<div id="map_div">
    <?php foreach($spots as $spot) : ?>
    <img id="<?= $spot->id . '_spot_' . $spot->orig_x . '_' . $spot->orig_y; ?>" class="draggable day-spot" <?php if($spot->day) : ?>rel="<?=$spot->day; ?>"<?php endif; ?> src="https://kizzang-resources-admin.s3.amazonaws.com/map/day_spot.gif" style="position: absolute; z-index: 1000; top: <?= $spot->y + 17 ; ?>px; left: <?= $spot->x + 235; ?>px;"/>
    <?php if($spot->day) : ?>
    <label style="position: absolute; z-index: 1001; top: <?= $spot->y + 17 ; ?>px; left: <?= $spot->x + 235; ?>px; width: 90px; font-size: 24px; text-align: center;">
        <?= $spot->day; ?>
            <?php if(isset($config[$spot->day])) : ?>
                <?php if(isset($config[$spot->day]['multiplier'])) print "(" . $config[(int)$spot->day]['multiplier']['multiplier'] . "X)";?>
                <?php if(isset($config[(int)$spot->day]['sponsor_wheel'])) print "(SW)";?>
            <?php endif; ?>
    </label>
    <?php endif; ?>
    <?php endforeach; ?>
    
    <?php foreach($ads as $ad) : ?>
    <img id="<?= $ad->id . '_ad_' . $ad->orig_x . '_' . $ad->orig_y; ?>_lbl" class="draggable" src="<?= $ad->image; ?>" style="position: absolute; z-index: 1000; top: <?= $ad->y + 50; ?>px; left: <?= $ad->x + 280; ?>px;"/>
    <?php endforeach; ?>
<table>
    <tr>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_01.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_02.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_03.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_04.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_05.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_06.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_07.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_08.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_09.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_10.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_11.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_12.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_13.png"/></td>
    </tr>
    <tr>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_14.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_15.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_16.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_17.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_18.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_19.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_20.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_21.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_22.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_23.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_24.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_25.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_26.png"/></td>
    </tr>
    <tr>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_27.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_28.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_29.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_30.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_31.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_32.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_33.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_34.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_35.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_36.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_37.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_38.png"/></td>
        <td><img id="img_1" src="https://kizzang-resources-admin.s3.amazonaws.com/map/master_no_spots_39.png"/></td>
    </tr>
</table>
</div>

<?php if($type == "Placement") : ?>
<script>
  var updates = {};
  $(document).ready(function(){
      $(".day-spot").dblclick(function(){
            var id = $(this).attr('id');
            var r = prompt("Enter in the number for the Day Spot:");          
            var left = $(this).css('left');
            var top = $(this).css('top');
            var width = $(this).css('width');
            var label = $("<label>").css({left: left, top:top, position: 'absolute', width: width, "font-size": "24px", "text-align": "center", "z-index": 1001}).html(r);
            $("#map_div").append(label);
            $(this).attr('rel', r);
            var data = {
                id: id,
                x: parseInt(left),
                y: parseInt(top),
                day: r
            };
            updates[id] = data;            
      });
      
      $(".draggable").draggable({       
              stop: function(e, ui){
                  var id = ui.helper.attr("id");
                  var day = ui.helper.attr("rel");
                  var data = {
                      id: id,
                      x: ui.position.left,
                      y: ui.position.top,
                      day: day
                  };
                  
                  //Check to see if the image is completely in one panel
                  var image_height, image_width;
                  $("<img/>").attr('src', $("#" + id).attr('src')).load(function(){
                      image_height = this.height;
                      image_width = this.width;
                      console.log(this.width);
                      /*if(Math.floor((data.x - 280) / 960) != Math.floor((data.x - 280 + image_width)/960) || Math.floor((data.y - 50) / 720) != Math.floor((data.y - 50 + image_height)/720))
                        {                      
                            alert("Invalid image placement!  You can't put an image covering 2 panels.");
                            $("#" + id).css('top', ui.originalPosition.top).css('left', ui.originalPosition.left);
                        }
                        else
                        {*/    
                            updates[id] = data;
                        //}
                  });                                                                        
              }
          });
          
          $("#btn_update_positions").click(function(){
            if(updates)
            {
                $.post('/admin/ajax_map_entry', {entries: updates, type: 'map'}, function(data){
                    if(data.success)
                    {
                        alert("Changes Saved");
                        updates = {};
                    }
                    else
                    {
                        alert("Changes were not saved.");
                    }
                }, 'json');
            }
          });
  });
</script>
<?php else : ?>

    <canvas id="canvas_2d" width="12480" height="2160" style="position: relative; top: -2175px; z-index: 10000;">Your browser does not support the HTML5 canvas tag.</canvas>
	<script>
	var POINT_SIZE = 12;
	var POINT_HALFSIZE = POINT_SIZE / 2.0;
	// TODO: implement an enum var/class for this

	// get the canvas HTML element and generate the context
	var canvas = document.getElementById("canvas_2d");
	var context = canvas.getContext("2d");
	var CANVAS_WIDTH = canvas.width;
	var CANVAS_HEIGHT = canvas.height;

	// setup the animation frame for updating the canvas
	var animFrame = window.requestAnimationFrame ||
		window.webkitRequestAnimationFrame ||
		window.mozRequestAnimationFrame    ||
		window.oRequestAnimationFrame      ||
		window.msRequestAnimationFrame     ||
		null;

	function GetMousePosition(e)
	{
		var rect = canvas.getBoundingClientRect();
		return {
			x: e.clientX - rect.left,
			y: e.clientY - rect.top
		}
	}
    
       var bez_array = [];
       
       $("#btn_update_positions").click(function(){           
            if(bez_array.length)
            {
                $.post('/admin/ajax_update_beziers', {data: bez_array}, function(data){
                    if(data.success)                        
                            alert("Bezier Curves Updated.");                        
                }, 'json');                           
            }
        });

	var CubicBezierCurve = function(id, stateId, x1, y1, cx1, cy1, cx2, cy2, x2, y2)
	{
		this.id = id;
               this.stateId = stateId;
		this.pointIndex = -1; // -1 = none, 0 = start, 1 = end, 2 = 1st handle, 3 = 2nd handle
		this.lockEndPoints = true;

		this.x1 = x1;
		this.y1 = y1;
		this.x2 = x2;
		this.y2 = y2;
		this.cx1 = cx1;
		this.cy1 = cy1;
		this.cx2 = cx2;
		this.cy2 = cy2;

		// called during the mousedown event to check if a handle was selected
		this.MouseDown = function(e)
		{
			var mousePos = GetMousePosition(e);
			
			// otherwise... determine if the player has clicked on the control handle 1
			if(	mousePos.x >= (this.cx1 - POINT_HALFSIZE) && mousePos.y >= (this.cy1 - POINT_HALFSIZE) &&
						mousePos.x <= (this.cx1 + POINT_HALFSIZE) && mousePos.y <= (this.cy1 + POINT_HALFSIZE))
						this.pointIndex = 2;

			// otherwise... determine if the player has clicked on the control handle 2
			else if(	mousePos.x >= (this.cx2 - POINT_HALFSIZE) && mousePos.y >= (this.cy2 - POINT_HALFSIZE) &&
						mousePos.x <= (this.cx2 + POINT_HALFSIZE) && mousePos.y <= (this.cy2 + POINT_HALFSIZE))
				this.pointIndex = 3;
			else
				this.pointIndex = -1;
		}

		// called during the mouseup event to release unset the handle
		this.MouseUp = function(e)
		{
                    this.pointIndex = -1;
                    bez_array[this.id] = {id: this.id, stateId: this.stateId, x1: this.cx1, y1: this.cy1, x2: this.cx2, y2: this.cy2};
		}

		// called during the mousemove event to move any selected points
		this.MouseMoved = function(e)
		{
			var mousePos = GetMousePosition(e);                      

			// move handles based on the resolved index when the mouse was down
			switch(this.pointIndex)
			{
				// move the start point to the mouse's position
				case 0:
					this.x1 = mousePos.x;
					this.y1 = mousePos.y;
					break;

				case 1:
					this.x2 = mousePos.x;
					this.y2 = mousePos.y;
					break;

				// move the 1st control handle to the mouse's position
				case 2:
					this.cx1 = mousePos.x;
					this.cy1 = mousePos.y;
					break;

				// move the 2nd control handle to the mouse's position
				case 3:
					this.cx2 = mousePos.x;
					this.cy2 = mousePos.y;
					break;
			} // the only other state should be "-1", which means that the grab was invalid, or the mouse isn't down
		}

		// draw the curve
		this.Render = function()
		{
			// draw the bezier curve
			context.lineWidth = 5;
			{
				context.beginPath();
				context.moveTo(this.x1, this.y1);
				context.bezierCurveTo(this.cx1, this.cy1, this.cx2, this.cy2, this.x2, this.y2);
                             context.strokeStyle = "#ffff00";
                             context.stroke();

                             context.strokeStyle = "#000000";
				// draw control handle 1
				context.moveTo(this.x1, this.y1);
				context.lineTo(this.cx1, this.cy1);

				// draw control handle 2
				context.moveTo(this.x2, this.y2);
				context.lineTo(this.cx2, this.cy2);
			}
			context.lineWidth = 1;
			context.stroke();

			// draw "clickable" boxes around the start/end and two control points
			context.rect(this.x1 - POINT_HALFSIZE, this.y1 - POINT_HALFSIZE, POINT_SIZE, POINT_SIZE);
			context.rect(this.x2 - POINT_HALFSIZE, this.y2 - POINT_HALFSIZE, POINT_SIZE, POINT_SIZE);
			context.fillRect(this.cx1 - POINT_HALFSIZE, this.cy1 - POINT_HALFSIZE, POINT_SIZE, POINT_SIZE);
			context.fillRect(this.cx2 - POINT_HALFSIZE, this.cy2 - POINT_HALFSIZE, POINT_SIZE, POINT_SIZE);
			context.stroke();
		}

		// retrieve control points
		this.GetControlPoints = function()
		{
			return {
				x1: this.cx1,
				y1: this.cy1,
				x2: this.cx2,
				y2: this.cy2
			}
		}
	}

	function CubeBezierController()
	{
	}
        <?php foreach($beziers as $bezier) : ?>
           <?= "var curve" . $bezier['id'] . " = new CubicBezierCurve(" . $bezier['id'] . "," . $bezier['stateId'] . "," . $bezier['x1'] . ".0," . $bezier['y1'] . ".0," . $bezier['x2'] . ".0," . $bezier['y2'] . ".0," . $bezier['x3'] . ".0," . $bezier['y3'] . ".0," . $bezier['x4'] . ".0," . $bezier['y4'] . ".0);\n"; ?>
        <?php endforeach;?>	

	onload = function()
	{
	}

	// detect mouse down event
	canvas.addEventListener("mousedown", function(e)
	{
        <?php foreach($beziers as $bezier) : ?>
                <?= "curve" . $bezier['id'] . ".MouseDown(e);\n"?>
        <?php endforeach; ?>		
	}, false);

	// detect mouse up event
	canvas.addEventListener("mouseup", function(e)
	{
        <?php foreach($beziers as $bezier) : ?>
                <?= "curve" . $bezier['id'] . ".MouseUp(e);\n"?>
        <?php endforeach; ?>		
	}, false);

	// detect mouse move event
	canvas.addEventListener("mousemove", function(e)
	{
        <?php foreach($beziers as $bezier) : ?>
                <?= "curve" . $bezier['id'] . ".MouseMoved(e);\n"?>
        <?php endforeach; ?>		
	}, false);

	function Update()
	{
	}

	function Render()
	{
		context.clearRect(0, 0, CANVAS_WIDTH, CANVAS_HEIGHT);
        <?php foreach($beziers as $bezier) : ?>
                <?= "curve" . $bezier['id'] . ".Render();\n"?>
        <?php endforeach; ?>		
	}

	// main loop variable to process the each frame
	var mainLoop = function()
	{
	     Update();
	     Render();
	     animFrame(mainLoop);
	};
	animFrame(mainLoop); // start the mainloop
	</script> 

<?php endif; ?>

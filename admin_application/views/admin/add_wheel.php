<link href="/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<script src="/js/jquery.datetimepicker.js"></script>

<style>
    .edit-btn {
        width: 40px;
        height: 20px;
        position: absolute;
        background-color: #FFFFFF;
        z-index: 1000;
        text-align: center;
    }
</style>
<div class="panel panel-primary">
    <div class="panel-heading"><?php if($wheel) : ?>Edit<?php else : ?>Add<?php endif;?> Wheel</div>
    <div class="panel-body">
        <div id="game_message"></div>
        <form role="form" id="frm_wheel">
            <?php if($wheel) : ?> <input type="hidden" name="id" value="<?=$wheel->id?>"/> <?php endif; ?>    
    <div class="form-group" id="div_name">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="name" value="<?php if($wheel) echo $wheel->name; ?>">
  </div>
    <div class="form-group" id="div_wheelType">
        <label for="name">Wheel Type</label>
        <select class="form-control" id="wheelType" name="wheelType">
            <?php foreach($types as $type) : ?>
            <option value="<?= $type; ?>" <?php if($wheel && $wheel->wheelType == $type) echo "selected=''"; ?>><?= $type; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group" id="div_radius">
    <label for="name">Radius</label>
    <input type="text" class="form-control" id="radius" name="radius" placeholder="radius" value="<?php if($wheel) echo $wheel->radius; ?>">
  </div>
    <div class="form-group" id="div_isDelete">
        <label>
                Is Deleted?
            </label>
            <label class="radio-inline">
                <input type="radio" name="isDeleted" value="0" <?php if($wheel && $wheel->isDeleted == 0) : ?>checked="checked"<?php endif; ?>> No
            </label>
            <label class="radio-inline">
                <input type="radio" name="isDeleted" value="1" <?php if(!$wheel || $wheel->isDeleted == 1) : ?>checked="checked"<?php endif; ?>> Yes
            </label>
    </div>
        </form>
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_wheel" class="btn btn-primary"><?php if($wheel) : ?>Update<?php else : ?>Add<?php endif;?></button></div>
</div>

<?php if($wheel) : ?>
  <div class="panel panel-primary">
    <div class="panel-heading">Current Wheel</div>
    <div class="panel-body" style="height: <?= ($wheel->radius * 2) + 25; ?>px;">
        <?php if($wedges) : ?>
        <div style="height: <?= $wheel->radius * 2; ?>px; width: <?= $wheel->radius * 2; ?>px; position: relative; z-index: 200; float: left;" id="wheel_div"></div>
        <canvas id="piechart" width="<?= $wheel->radius * 2; ?>" height="<?= $wheel->radius * 2; ?>" style="position: relative; left: -<?= $wheel->radius * 2; ?>px; float: left;"></canvas>
        <div class="panel panel-primary" style="width: 300px; float: right;">
            <div class="panel-heading">Configuration</div>
            <div class="panel-body">
                <div class="form-group" id="div_reduction_factor">
                    <label>Reduction Factor:</label>
                    <input type="text" id="reduction_factor" class="form-control" value="1"/>
                </div>
                <div class="form-group" id="div_padding">
                    <label>Padding:</label>
                    <input type="text" id="padding" class="form-control" value="10"/>
                </div>
                <div class="form-group" id="div_gheight">
                    <label>Height:</label>
                    <input type="text" id="gheight" class="form-control" value="-22"/>
                </div>                                
            </div>            
            <div class="panel-footer" style="text-align: right;">
                <button type="button" id="update_db" class="btn btn-success">Update Database</button>
                <button type="button" id="update_config" class="btn btn-primary">Update Config</button>
            </div>
        </div>
        </div>
        <?php endif; ?>
    </div>
    <div class="panel-footer" style="text-align: right;"><a class="btn btn-primary" data-toggle="modal" data-target="#modal" href="/admin/add_wedge/<?= $wheel->id; ?>">Add Wedge</a></div>
  </div>
<?php endif; ?>
<?php if($wheel->wheelType != "Basic") : ?>
<?php foreach($wedges as $wedge) : ?>
<img src="<?= $wedge->image_url;?>" style="display: none" id="img_<?= $wedge->id; ?>"/>
<?php endforeach; ?>
<?php endif; ?>
<script>

       $("#update_wheel").click(function(){
                $.post('/admin/ajax_add_wheel', $("#frm_wheel").serialize(), function(data){
                    $("#frm_wheel div").removeClass('alert alert-danger');
                        if(data.success)
                        {
                                $("#game_message").html("Insert / Update of the Wheel was good.").addClass("alert alert-success").removeClass('alert-danger');
                                $('html,body').scrollTop(0);
                                var command = "window.location = '/admin/add_wheel/" + data.id + "';";
                                setTimeout(command, 1000);
                        }
                        else
                        {
                                var error = "There were the following errors:\n";
                                for(var key in data.errors)
                                    error = error + data.errors[key] + "\n";
                                alert(error);
                        }
                },'json');
        });
        
        $("body").on("click", ".del_wedge", function(){
           var wheelId = $(this).attr('wheelId');
           var id = $(this).attr("ref");
           var r = confirm("Are you sure you want to delete this wedge?");
           if(r)
           {
                $.get("/admin/ajax_delete_wedge/" + wheelId + "/" + id, {}, function(data){
                    if(data.success)
                    {
                        alert("Wedge Deleted");
                        location.reload();
                    }
                }, 'json');
            }
        });
        
        $("#update_db").click(function(){
            $.post("/admin/ajax_update_wedges", {points: points, wheelId: <?php if($wheel) echo $wheel->id; ?>}, function(data){
                if(data.success)
                {
                    alert("Wedges have been updated.");
                }
                else
                {
                    alert("The information was not saved.")
                }
            }, 'json')
        })
        
        $("#update_config").click(function(){
            reduce_factor = $("#reduction_factor").val();
            padding = parseInt($("#padding").val());
            gheight = parseInt($("#gheight").val());
            for (var i = 0; i < data.length; i++) {
                drawSegment(canvas, context, i);            
            }
            console.log(points);
        });
        
   <?php if($wheel && $wedges) : ?>
    <?php $data = $labels = $ids = $colors = array(); ?>
    <?php foreach($wedges as $wedge) : ?>
            <?php
            $data[] = 360 / count($wedges);
            if($wheel->wheelType == "Basic")
                $labels[] = $wedge->displayString;
            else
                $labels[] = $wedge->image_url;
            $colors[] = $wedge->color;
            $ids[] = $wedge->id;
            $magnitudes[] = $wedge->magnitude;
            $rads[] = $wedge->angle_radians;
            ?>
    <?php endforeach; ?>
        
    var points = [];
    var gheight = -22;
    var padding = 10;
    var grad = 0;
    var data = [<?= implode(",", $data); ?>];
    var labels = ["<?= implode('","', $labels); ?>"];
    var colors = ["<?= implode('","', $colors); ?>"];
    var ids = [<?= implode(",", $ids); ?>];
    var rads = [<?= implode(",", $rads); ?>];
    var magnitudes = [<?= implode(",", $magnitudes); ?>];
    var radius = <?= $wheel->radius; ?>;
    var reduce_factor = 1;
    var max_height = Math.floor(Math.sqrt(Math.pow(<?= $wheel->radius; ?>, 2) + Math.pow(<?= $wheel->radius; ?>, 2) - (2 * Math.pow(<?= $wheel->radius; ?>, 2)) * Math.cos(degreesToRadians(data[0]))));
    var min_angle = Math.cos(degreesToRadians(data[0]));
    
    canvas = document.getElementById("piechart");
    var context = canvas.getContext("2d");
    for (var i = 0; i < data.length; i++) {
        drawSegment(canvas, context, i);
    }
       
    function drawSegment(canvas, context, i) 
    {
        context.save();
        var centerX = Math.floor(canvas.width / 2);
        var centerY = Math.floor(canvas.height / 2);
        radius = Math.floor(canvas.width / 2);

        var startingAngle = degreesToRadians(sumTo(data, i));
        var arcSize = degreesToRadians(data[i]);
        var endingAngle = startingAngle + arcSize;

        context.beginPath();
        context.moveTo(centerX, centerY);
        context.arc(centerX, centerY, radius, 
                    startingAngle, endingAngle, false);
        context.closePath();

        context.fillStyle = colors[i];
        context.fill();

        context.restore();

        drawSegmentLabel(canvas, context, i);
        
        btn_x = 0;
        btn_y = 0;
        endingAngle = radiansToDegrees(endingAngle);
        startingAngle = radiansToDegrees(startingAngle);
        endingAngle = Math.floor((endingAngle + startingAngle) / 2);

        if(endingAngle % 90 == 0)
        {
            switch(endingAngle)
            {
                case 360: btn_x = centerX + radius; btn_y = centerY; break;
                case 90: btn_x = centerX; btn_y = centerY + radius; break;
                case 180: btn_x = centerX - radius; btn_y = centerY; break;
                case 270: btn_x = centerX; btn_y = centerY - radius; break;
            }
        }
        else
        {
            otherAngle = 180 - (90 + endingAngle);
            side1 = (radius * Math.sin(degreesToRadians(endingAngle))) / Math.sin(degreesToRadians(90));
            side2 = (radius * Math.sin(degreesToRadians(otherAngle))) / Math.sin(degreesToRadians(90));
            btn_x = centerX + side2;
            btn_y = centerY + side1;  
        }
        btn_x -= 20;
        btn_y -= 10;
        var div = $("<div>").css("top", btn_y).css("left", btn_x).addClass('edit-btn');
        var c = $("<a>").attr("href", "/admin/add_wedge/<?=$wheel->id?>/" + ids[i]).html("Edit").attr("data-toggle","modal").attr("data-target","#modal");
        var d = $("<a>").attr({href: "javascript:void(0);",  wheelId: "<?=$wheel->id?>", ref: ids[i]}).html("Delete").addClass("del_wedge");
        var space = $("<span>").css("width","10px").html(" ");
        
        div.append(c);
        div.append(space);
        div.append(d);        
        $("#wheel_div").append(div);
    }
    
    function degreesToRadians(degrees) 
    {
        return (degrees * Math.PI)/180;
    }
    
    function radiansToDegrees(radians)
    {
        return Math.floor((180 * radians) / Math.PI);
    }
    
    function sumTo(a, i) 
    {
        var sum = 0;
        for (var j = 0; j < i; j++) {
          sum += a[j];
        }
        return sum;
    }
    
    function drawSegmentLabel(canvas, context, i) 
    {
        context.save();
        var x = Math.floor(canvas.width / 2);
        var y = Math.floor(canvas.height / 2);
        var angle = degreesToRadians(sumTo(data, i));

        context.translate(x, y);
        context.rotate(angle);
        var dx = Math.floor(canvas.width * 0.5) - 10;
        var dy = Math.floor(canvas.height * 0.05);

        context.textAlign = "right";
        var fontSize = Math.floor(canvas.height / 25);
        context.font = fontSize + "pt Helvetica";

        <?php if($wheel->wheelType == "Basic") : ?>
        context.fillText(labels[i], dx, dy);        
        <?php else : ?>
                
        var image = new Image(); 
        image.src = $("#img_" + ids[i]).attr('src');     
        var height = $("#img_" + ids[i]).height();
        var width = $("#img_" + ids[i]).width();
        
        if(height > (max_height * reduce_factor))
        {
            var aspect_ratio = (max_height * reduce_factor / height);
            width = Math.floor(width * aspect_ratio);
            height = Math.floor(height * aspect_ratio);
        }

        if(width > (0.5 * radius))
        {
            var aspect_ratio = ((0.5 * radius) / width);
            width = Math.floor(width * aspect_ratio);
            height = Math.floor(height * aspect_ratio);
        }
        
        var angleInRad = ((min_angle / (max_height)) * ((max_height - height) / 2));
        context.rotate( angleInRad );        

        var positionX = radius - (width + padding);
        var positionY = gheight;
        var axisY = 0;       
        var axisX = 0;             
        //alert("Height: " + height + " Width: " + width + " Max Height: " + axisY + " New Angle: " + radiansToDegrees(angleInRad));                    
       context.translate( positionX, positionY );        
       context.drawImage( image, -axisX, -axisY, width, height );                
       var magnitude = Math.sqrt(Math.pow(positionX, 2) + Math.pow(positionY, 2));
       points.push({id: ids[i], magnitude: magnitude, angle_radians: angleInRad + angle, height: height, width: width});
            
        <?php endif; ?>        
        context.restore();        
     }
          
     <?php endif; ?>
</script>
<div class="panel panel-primary">
    <div class="panel-heading"><?php if($testimonial) : ?>Edit<?php else : ?>Add<?php endif;?> Testimonial</div>
    <div class="panel-body">
        <div id="testimonial_message"></div>
        <form role="form" id="frm_testimonial" enctype="multipart/form-data">
            <?php if($testimonial) : ?> <input type="hidden" name="id" value="<?=$testimonial->id?>"/> <?php endif; ?>
            <div class="form-group" id="div_name">
              <label for="name">Name</label>
              <input type="text" class="form-control" id="name" name="name" placeholder="name" value="<?php if($testimonial) echo $testimonial->name; ?>">
            </div>
            <div class="form-group" id="div_state">
              <label for="name">State Abbreviation</label>
              <input type="text" class="form-control" maxlength="2" id="state" name="state" placeholder="state" value="<?php if($testimonial) echo $testimonial->state; ?>">
            </div>  
            <div class="form-group" id="div_description">
              <label for="name">Prize</label>
              <input type="text" class="form-control" id="description" name="description" placeholder="description" value="<?php if($testimonial) echo $testimonial->description; ?>">
            </div>  
            <div class="form-group" id="div_testimonial">
              <label for="name">Testimonial</label>
              <input type="text" class="form-control" id="testimonial" name="testimonial" placeholder="testimonial" value="<?php if($testimonial) echo $testimonial->testimonial; ?>">
            </div>  
            <div class="form-group" id="div_winDate">
              <label for="startDate">Win Date</label>
              <input type="text" class="form-control" id="winDate" name="winDate" placeholder="winDate" value="<?php if($testimonial) echo $testimonial->winDate; ?>">
            </div>
            <div class="form-group" id="div_image">
              <label for="SerialNumber">Image (320x320)</label>
              <div id="img_image"><img id="imageImg" src="<?php if($testimonial) : ?><?= str_replace("https://d1vksrhd974otw.cloudfront.net/", "https://kizzang-campaigns.s3.amazonaws.com/", $testimonial->image); ?><?php endif; ?>"/></div>
              <input type="hidden" name="image" id="image" value="<?php if($testimonial) echo $testimonial->image; ?>"/>
              <a href="#" id="uploadImage">Select File</a>
                <input style="display:none" type="file" id="uploadImageBtn" accept="image/*" onchange="handleFile(this.files, 'image')"/>
            </div>  
        </form>
    </div>  
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_testimonial" class="btn btn-primary"><?php if($testimonial) : ?>Update<?php else : ?>Add<?php endif;?> Testimonial</button></div>    
</div>

<script>
   
    function handleFile(files, img_name)
    {        
        var fileReader=new FileReader();
        var file=files[0];
        console.log(file);
        var imageElem=document.getElementById(img_name + "Img");//var imageElem=$("<img>");

        fileReader.onload = (function(img) { return function(e) { img.src = e.target.result; }; })(imageElem);
        fileReader.readAsDataURL(file);
       
        uploadFile(files[0], img_name, 'testimonials');
    }
    
    function uploadFile(file, img_name, folder)
    {
        var xhr = new XMLHttpRequest();
        var formData = new FormData();
        
        formData.append('file',file);
        formData.append('name', folder);
        formData.append('bucket', 'kizzang-campaigns');
       
        xhr.open("POST", "/admin/file_upload");
        xhr.overrideMimeType('text/plain; charset=x-user-defined-binary');
        xhr.onload = function(){console.log(this.responseText);};
        xhr.onreadystatechange = function() {
            $("#" + img_name).val(this.responseText).trigger('change');            
            if (xhr.readystate != 4) { return; }
            alert(xhr.responsetext);
        };
        xhr.send(formData);
    }
  
$(function() {
        $("#uploadImage").click(function(e){
            e.preventDefault(); //prevent default action             
            $("#uploadImageBtn").click();           
        });
        
        $("#update_testimonial").click(function(){
                $.post('/admin/ajax_add_testimonial', $("#frm_testimonial").serialize(), function(data){
                    $("#frm_testimonial div").removeClass('alert-danger');
                        if(data.success)
                        {
                                $("#testimonial_message").html("Insert / Update was good.").addClass("alert alert-success").removeClass('alert-danger');
                                $('html,body').scrollTop(0);
                                var command = "window.location = '/admin/view_testimonials';";
                                setTimeout(command, 1000);
                        }
                        else
                        {
                                $("#testimonial_message").addClass("alert alert-danger").html("There were errors. They are listed / highlighted below.");
                                for(var key in data.errors)
                                    $("#div_" + key).addClass("alert-danger");
                                $('html,body').scrollTop(0);
                        }
                },'json');
        }); 
        
        $( "#winDate" ).datepicker({
            dateFormat: "yy-mm-dd",
            setDate: "+1d", 
            changeMonth: true,
            numberOfMonths: 3            
        });
        
        
    });
</script>
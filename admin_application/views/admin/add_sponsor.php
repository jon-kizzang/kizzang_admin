<div class="panel panel-primary">
    <div class="panel-heading"><?php if($sponsor) : ?>Edit<?php else : ?>Add<?php endif;?> Sponsor</div>
    <div class="panel-body">
        <div id="sponsor_message"></div>
        <form role="form" id="frm_sponsor">
            <?php if($sponsor) : ?> <input type="hidden" name="id" value="<?=$sponsor->id?>"/> <?php endif; ?>
    <div class="form-group" id="div_name">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="name" value="<?php if($sponsor) echo $sponsor->name; ?>">
  </div>
  <div class="form-group" id="div_contactName">
    <label for="contactName">Contact Name</label>
    <input type="text" class="form-control" id="contactName" name="contactName" placeholder="contactName" value="<?php if($sponsor) echo $sponsor->contactName; ?>">
  </div>
  <div class="form-group" id="div_sponsorType">
    <label for="sponsorType">Sponsor Type</label>
    <select type="text" class="form-control" id="sponsorType" name="sponsorType">
        <?php foreach($sponsorTypes as $type) : ?>
        <option value="<?= $type; ?>" <?php if($sponsor && $sponsor->sponsorType == $type) echo "selected=''"; ?>><?= $type; ?></option>
        <?php endforeach;?>
    </select>
  </div>
  <div class="form-group" id="div_contactEmail">
    <label for="contactEmail">Contact Email</label>
    <input type="text" class="form-control" id="contactEmail" name="contactEmail" placeholder="contactEmail" value="<?php if($sponsor) echo $sponsor->contactEmail; ?>">
  </div>
  <div class="form-group" id="div_contactPhone">
    <label for="contactPhone">Contact Phone</label>
    <input type="text" class="form-control" id="contactPhone" name="contactPhone" placeholder="contactPhone" value="<?php if($sponsor) echo $sponsor->contactPhone; ?>">
  </div>
  <div class="form-group" id="div_address">
    <label for="address">Address</label>
    <input type="text" class="form-control" id="address" name="address" placeholder="address" value="<?php if($sponsor) echo $sponsor->address; ?>">
  </div>
  <div class="form-group" id="div_city">
    <label for="city">City</label>
    <input type="text" class="form-control" id="city" name="city" placeholder="city" value="<?php if($sponsor) echo $sponsor->city; ?>">
  </div>
  <div class="form-group" id="div_state">
    <label for="state">State</label>
    <input type="text" class="form-control" id="state" name="state" placeholder="state" value="<?php if($sponsor) echo $sponsor->state; ?>">
  </div>
  <div class="form-group" id="div_zip">
    <label for="zip">Zip</label>
    <input type="text" class="form-control" id="zip" name="zip" placeholder="zip" value="<?php if($sponsor) echo $sponsor->zip; ?>">
  </div>
  <div class="form-group" id="div_hexColor">
    <label for="hexColor">Hex Color</label>
    <input type="text" class="form-control" id="hexColor" name="hexColor" placeholder="hexColor" value="<?php if($sponsor) echo $sponsor->hexColor; ?>">
  </div>
  <div class="form-group" id="div_imageURL">
    <label for="SerialNumber">Art Repo (Must be 240px x 240px) </label>
    <img id="artRepoImg" <?php if($sponsor) echo 'src="' . $sponsor->artRepo . '"'; else "style='display:none:'" ?>/>
    <input type="hidden" name="artRepo" id="artRepo" value="<?php if($sponsor) echo $sponsor->artRepo; ?>"/>
    <a href="#" id="uploadImage">Select File</a>
      <input style="display:none" type="file" id="uploadImageBtn" accept="image/*" onchange="handleFile(this.files, 'artRepo')"/>
  </div>  
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_sponsor" class="btn btn-primary"><?php if($sponsor) : ?>Update<?php else : ?>Add<?php endif;?> Sponsor</button></div>
</form>
</div>

<script>
$(function() {
        $("#update_sponsor").click(function(){
                $.post('/admin/ajax_add_sponsor', $("#frm_sponsor").serialize(), function(data){
                    $("#frm_sponsor div").removeClass('alert-danger');
                        if(data.success)
                        {
                                $("#sponsor_message").html("Insert / Update was good.").addClass("alert alert-success").removeClass('alert-danger');
                                $('html,body').scrollTop(0);
                                var command = "window.location = '/admin/view_sponsors';";
                                setTimeout(command, 1000);
                        }
                        else
                        {
                                $("#sponsor_message").addClass("alert alert-danger").html("There were errors. They are listed / highlighted below.");
                                for(var key in data.errors)
                                    $("#div_" + key).addClass("alert-danger");
                                $('html,body').scrollTop(0);
                        }
                },'json');
        });
        
        $("#uploadImage").click(function(e){
            e.preventDefault(); //prevent default action             
            $("#uploadImageBtn").click();           
        });
        
    });
    
    function handleFile(files, img_name)
    {        
        var fileReader=new FileReader();
        var file=files[0];
        console.log(file);
        var imageElem=document.getElementById(img_name + "Img");//var imageElem=$("<img>");

        fileReader.onload = (function(img) { return function(e) { img.src = e.target.result; }; })(imageElem);
        fileReader.readAsDataURL(file);
       
        uploadFile(files[0], img_name);
    }
    
    function uploadFile(file, img_name)
    {
        var xhr = new XMLHttpRequest();
        var formData = new FormData();
        
        formData.append('file',file);
        formData.append('name', $("#name").val());
        formData.append('bucket', 'kizzang-resources-sweepstakes');
       
        xhr.open("POST", "/admin/file_upload");
        xhr.overrideMimeType('text/plain; charset=x-user-defined-binary');
        xhr.onload = function(){console.log(this.responseText);};
        xhr.onreadystatechange = function() {
            $("#" + img_name).val(this.responseText);            
            if (xhr.readystate != 4) { return; }
            alert(xhr.responsetext);
        };
        xhr.send(formData);
    }
</script>
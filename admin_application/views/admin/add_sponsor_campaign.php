<script src="http://jwpsrv.com/library/XfPOMuUeEeSt5RJtO5t17w.js"></script>
<div class="panel panel-primary">
    <div class="panel-heading"><?php if($sponsor) : ?>Edit<?php else : ?>Add<?php endif;?> Sponsor Campaign</div>
    <div class="panel-body">
        <div id="sponsor_message"></div>
        <form role="form" id="frm_sponsor_campaign" enctype="multipart/form-data">
            <?php if($sponsor) : ?> <input type="hidden" name="id" value="<?=$sponsor->id?>"/> <?php endif; ?>
  <div class="form-group" id="div_name">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="name" value="<?php if($sponsor) echo $sponsor->name; ?>">
  </div>
  <div class="form-group" id="div_sponsorID">
    <label for="contactName">Sponsor</label>
    <select id="sponsorID" class="form-control" name="sponsorID">
        <?php foreach($snames as $sname) : ?>
        <option value="<?= $sname->id; ?>" <?php if($sponsor && $sname->id == $sponsor->sponsorID) echo 'selected=""'; ?>><?= $sname->name; ?></option>
        <?php endforeach; ?>
    </select>    
  </div>
  <div class="form-group" id="div_type">
    <label for="contactName">Type</label>
    <select id="type" class="form-control" name="type">
        <?php foreach($types as $type) : ?>
        <option value="<?= $type->id; ?>" <?php if($sponsor && $type->id == $sponsor->type) echo 'selected=""'; ?>><?= $type->name; ?></option>
        <?php endforeach; ?>
    </select>    
  </div>
  <div class="form-group" id="div_startDate">
    <label for="startDate">Start Date</label>
    <input type="text" class="form-control" id="StartDate" name="startDate" placeholder="startDate" value="<?php if($sponsor) echo $sponsor->startDate; ?>">
  </div>
  <div class="form-group" id="div_endDate">
    <label for="endDate">End Date</label>
    <input type="text" class="form-control" id="EndDate" name="endDate" placeholder="endDate" value="<?php if($sponsor) echo $sponsor->endDate; ?>">
  </div>
  <div class="form-group" id="div_gender">
    <label for="contactName">Gender</label>
    <select id="gender" class="form-control" name="gender">
        <?php foreach($genders as $gender) : ?>
        <option value="<?= $gender->id; ?>" <?php if($sponsor && $gender->id == $sponsor->gender) echo 'selected=""'; ?>><?= $gender->name; ?></option>
        <?php endforeach; ?>
    </select>    
  </div>
  <div class="form-group" id="div_day">
    <label for="day">Map Day</label>
    <input type="text" class="form-control" id="day" name="day" placeholder="day" value="<?php if($sponsor) echo $sponsor->day; ?>">
  </div>
  <div class="form-group" id="div_numGames">
    <label for="numGames"># of Games Played on Day to Trigger</label>
    <input type="text" class="form-control" id="numGames" name="numGames" placeholder="numGames" value="<?php if($sponsor) echo $sponsor->numGames; ?>">
  </div>
  <div class="form-group" id="div_totalOffers">
    <label for="totalOffers">Total Offers</label>
    <input type="text" class="form-control" id="totalOffers" name="totalOffers" placeholder="totalOffers" value="<?php if($sponsor) echo $sponsor->totalOffers; ?>">
  </div>
  <div class="form-group" id="div_offersClaimed">
    <label for="offersClaimed">Offers Claimed</label>
    <input type="text" class="form-control" id="offersClaimed" name="offersClaimed" readonly="readonly" placeholder="offersClaimed" value="<?php if($sponsor) echo $sponsor->offersClaimed; ?>">
  </div>
  <div class="form-group" id="div_ageMin">
    <label for="ageMin">Minimum Age</label>
    <input type="text" class="form-control" id="ageMin" name="ageMin" placeholder="ageMin" value="<?php if($sponsor) echo $sponsor->ageMin; ?>">
  </div>
  <div class="form-group" id="div_ageMax">
    <label for="ageMax">Maximum Age</label>
    <input type="text" class="form-control" id="ageMax" name="ageMax" placeholder="ageMax" value="<?php if($sponsor) echo $sponsor->ageMax; ?>">
  </div>
  <div class="form-group" id="div_offerMessage">
    <label for="offerMessage">Offer Message</label>
    <textarea class="form-control" name="offerMessage"><?php if($sponsor) echo $sponsor->offerMessage; ?></textarea>
  </div>
  <div class="form-group" id="div_artAssetUrl">
    <label for="SerialNumber">Art Asset</label>
    <div id="img_artAssetUrl"><img id="artAssetUrlImg" src="<?php if($sponsor) : ?><?= $sponsor->artAssetUrl; ?><?php endif; ?>"/></div>
    <input type="hidden" name="artAssetUrl" id="artAssetUrl" value="<?php if($sponsor) echo $sponsor->artAssetUrl; ?>"/>
    <a href="#" id="uploadImage">Select File</a>
      <input style="display:none" type="file" id="uploadImageBtn" accept="image/*" onchange="handleFile(this.files, 'artAssetUrl')"/>
  </div>
  <div class="form-group" id="div_modalAssetUrl">
    <label for="SerialNumber">Modal Art Asset</label>
    <div id="img_modalAssetUrl"><img id="modalAssetUrlImg" src="<?php if($sponsor) : ?><?= $sponsor->modalAssetUrl; ?><?php endif; ?>"/></div>
    <input type="hidden" name="modalAssetUrl" id="modalAssetUrl" value="<?php if($sponsor) echo $sponsor->modalAssetUrl; ?>"/>
    <a href="#" id="uploadModalImage">Select File</a>
      <input style="display:none" type="file" id="uploadModalImageBtn" accept="image/*" onchange="handleFile(this.files, 'modalAssetUrl')"/>
  </div>
  <div class="form-group" id="div_videoUrl">
    <label for="SerialNumber">Video Upload</label>
    <div id="vid_videoUrl">        
    </div>
    <input type="hidden" name="videoUrl" id="videoUrl" value="<?php if($sponsor) echo $sponsor->videoUrl; ?>"/>
    <a href="#" id="uploadVideo">Select File</a>
    <input style="display:none" type="file" id="uploadVideoBtn" name="videoFile" accept="video/*"  onchange="uploadFile(this.files[0], 'videoUrl', 'videos')"/>
  </div>
  <div class="form-group" id="div_stateID">
    <label for="contactName">Map State</label>
    <select id="stateID" class="form-control" name="stateID">
        <?php foreach($states as $state) : ?>
        <option value="<?= $state->abbreviation; ?>" <?php if($sponsor && $state->abbreviation == $sponsor->stateID) echo 'selected=""'; ?>><?= $state->name; ?></option>
        <?php endforeach; ?>
    </select>    
  </div>
  <div class="form-group" id="div_Active">
        <label>
                Active?
            </label>
            <label class="radio-inline">
                <input type="radio" name="Active" value="0" <?php if($sponsor && $sponsor->Active == 0) : ?>checked="checked"<?php endif; ?>>No
            </label>
            <label class="radio-inline">
                <input type="radio" name="Active" value="1" <?php if(!$sponsor || $sponsor->Active == 1) : ?>checked="checked"<?php endif; ?>> Yes
            </label>
    </div>
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_sponsor_campaign" class="btn btn-primary"><?php if($sponsor) : ?>Update<?php else : ?>Add<?php endif;?> Sponsor Campaign</button></div>
</form>
</div>

<script>
    <?php if($sponsor && $sponsor->videoUrl) :?>
     jwplayer("vid_videoUrl").setup({
            file: "<?= $sponsor->videoUrl; ?>",            
            width: 320,
            height: 180
        });
    <?php else : ?>
    $("#videoUrl").change(function(){
        jwplayer("vid_videoUrl").setup({
            file: $(this).val(),            
            width: 320,
            height: 180
        });
    });
    <?php endif; ?>
    function handleFile(files, img_name)
    {        
        var fileReader=new FileReader();
        var file=files[0];
        console.log(file);
        var imageElem=document.getElementById(img_name + "Img");//var imageElem=$("<img>");

        fileReader.onload = (function(img) { return function(e) { img.src = e.target.result; }; })(imageElem);
        fileReader.readAsDataURL(file);
       
        uploadFile(files[0], img_name, 'map-sponsors');
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
        
        $("#uploadModalImage").click(function(e){
            e.preventDefault(); //prevent default action             
            $("#uploadModalImageBtn").click();           
        });
        
        $("#uploadVideo").click(function(e){
            e.preventDefault(); //prevent default action             
            $("#uploadVideoBtn").click();           
        });
        
        $("#update_sponsor_campaign").click(function(){
                $.post('/admin/ajax_add_sponsor_campaign', $("#frm_sponsor_campaign").serialize(), function(data){
                    $("#frm_sponsor div").removeClass('alert-danger');
                        if(data.success)
                        {
                                $("#sponsor_message").html("Insert / Update was good.").addClass("alert alert-success").removeClass('alert-danger');
                                $('html,body').scrollTop(0);
                                var command = "window.location = '/admin/view_sponsor_campaigns';";
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
        
        $( "#StartDate" ).datepicker({
            dateFormat: "yy-mm-dd",
            setDate: "+1d", 
            changeMonth: true,
            numberOfMonths: 3,
            onClose: function( selectedDate ) {
                $( "#EndDate" ).datepicker( "option", "minDate", selectedDate );
            }
        });
        
        $( "#EndDate" ).datepicker({
            dateFormat: "yy-mm-dd",
            setDate: "+1w",
            changeMonth: true,
            numberOfMonths: 3,
            onClose: function( selectedDate ) {
                $( "#StartDate" ).datepicker( "option", "maxDate", selectedDate );
            }
        });
        
    });
</script>
<div class="panel panel-primary">
    <div class="panel-heading"><?php if($sweepstakes) : ?>Edit<?php else : ?>Add<?php endif;?> Sweepstakes</div>
    <div class="panel-body">
        <div id="game_message"></div>
        <form role="form" id="frm_sweepstakes">
            <?php if($sweepstakes) : ?> <input type="hidden" name="id" value="<?=$sweepstakes->id?>"/> <?php endif; ?>
    <div class="form-group" id="div_name">
    <label for="Name">Name</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="<?php if($sweepstakes) echo $sweepstakes->name; ?>">
  </div>
    <div class="form-group" id="div_description">
    <label for="Name">Description</label>
    <textarea class="form-control" id="description" name="description" placeholder="Description"><?php if($sweepstakes) echo $sweepstakes->description; ?></textarea>
  </div>
    <div class="form-group" id="div_startDate">
    <label for="StartDate">Start Date</label>
    <input type="text" class="form-control" id="StartDate" name="startDate" placeholder="StartDate" value="<?php if($sweepstakes) echo date("Y-m-d", strtotime($sweepstakes->startDate)); ?>">
  </div>
    <div class="form-group" id="div_endDate">
    <label for="EndDate">End Date</label>
    <input type="text" class="form-control" id="EndDate" name="endDate" placeholder="EndDate" value="<?php if($sweepstakes) echo date("Y-m-d", strtotime($sweepstakes->endDate)); ?>">
  </div>
  <div class="form-group" id="div_imageURL">
    <label for="SerialNumber">Image (Must be 240px x 240px) </label>
    <img id="imageURLImg" <?php if($sweepstakes) echo 'src="' . $sweepstakes->imageURL . '"'; else "style='display:none:'" ?>/>
    <input type="hidden" name="imageURL" id="imageURL" value="<?php if($sweepstakes) echo $sweepstakes->imageURL; ?>"/>
    <a href="#" id="uploadImage">Select File</a>
      <input style="display:none" type="file" id="uploadImageBtn" accept="image/*" onchange="handleFile(this.files, 'imageURL')"/>
  </div>
    <div class="form-group" id="div_titleImageURL">
    <label for="SerialNumber">Title Image (Must be 215px x 90px) </label>
    <img id="titleImageURLImg" <?php if($sweepstakes) echo 'src="' . $sweepstakes->titleImageURL . '"'; else "style='display:none:'" ?>/>
    <input type="hidden" id="titleImageURL" name="titleImageURL" value="<?php if($sweepstakes) echo $sweepstakes->titleImageURL; ?>"/>
    <a href="#" id="uploadTitleImage">Select File</a>
      <input style="display:none" type="file" id="uploadTitleImageBtn" accept="image/*" onchange="handleFile(this.files, 'titleImageURL')"/>
    </div>
    
    <div class="form-group" id="div_sweepstakeType">
    <label for="CardIncrement">Type</label>
    <select class="form-control" id="sweepstakeType" name="sweepstakeType">
        <option value="open" <?php if($sweepstakes && $sweepstakes->sweepstakeType == "open") echo "selected='selected'"; ?>>Open</option>
        <option value="closed" <?php if($sweepstakes && $sweepstakes->sweepstakeType == "closed") echo "selected='selected'"; ?>>Closed</option>
    </select>
    </div>
    
    <div class="form-group" id="div_maxEntrants">
    <label for="TotalCards">Max Entrants</label>
    <input type="text" class="form-control" id="maxEntrants" name="maxEntrants" placeholder="maxEntrants" value="<?php if($sweepstakes) echo $sweepstakes->maxEntrants; else echo "999999";?>">
    </div>
    
    <div class="form-group" id="div_maxWinners">
    <label for="maxWinners">Total Winners</label>
    <input type="text" class="form-control" id="maxWinners" name="maxWinners" placeholder="max Winners" value="<?php if($sweepstakes) echo $sweepstakes->maxWinners; else echo "1"; ?>">
    </div>
            
    <div class="form-group" id="div_displayValue">
    <label for="displayValue">Display Value</label>
    <input type="text" class="form-control" id="displayValue" name="displayValue" placeholder="Display Value" value="<?php if($sweepstakes) echo $sweepstakes->displayValue; ?>">
    </div>
            
    <div class="form-group" id="div_taxValue">
    <label for="taxValue">Tax Value</label>
    <input type="text" class="form-control" id="taxValue" name="taxValue" placeholder="Tax Value" value="<?php if($sweepstakes) echo $sweepstakes->taxValue; ?>">
    </div>
            
    <div class="form-group" id="div_ratioTicket">
    <label for="taxValue">Ratio Ticket</label>
    <input type="text" class="form-control" id="ratioTicket" name="ratioTicket" placeholder="Ticket Ratio" value="<?php if($sweepstakes) echo $sweepstakes->ratioTicket; else echo "1";?>">
    </div>
            
    <div class="form-group" id="div_color">
    <label for="color">Background Color</label>
    <input type="text" class="form-control" id="color" name="color" placeholder="Color" value="<?php if($sweepstakes) echo $sweepstakes->color; ?>">
    </div>
            
    <div class="form-group" id="div_textColor">
    <label for="color">Date Text Color</label>
    <input type="text" class="form-control" id="textColor" name="textColor" placeholder="Color" value="<?php if($sweepstakes) echo $sweepstakes->textColor; ?>">
    </div>
            
    <div class="form-group" id="div_displayName">
    <label for="taxValue">Dialog Display Name</label>
    <input type="text" class="form-control" id="displayName" name="displayName" placeholder="Display Name" value="<?php if($sweepstakes) echo $sweepstakes->displayName;;?>">
    </div>
            
    <div class="form-group" id="div_cardUrl">
    <label for="taxValue">Card URL</label>
    <input type="text" class="form-control" id="cardUrl" name="cardUrl" placeholder="Card URL" value="<?php if($sweepstakes) echo $sweepstakes->cardUrl;?>">
    </div>
    
    <div class="form-group" id="div_isDeleted">
        <label>
                Delete?
            </label>
            <label class="radio-inline">
                <input type="radio" name="isDeleted" value="0" <?php if(!$sweepstakes || $sweepstakes->isDeleted == 0) : ?>checked="checked"<?php endif; ?>> No
            </label>
            <label class="radio-inline">
                <input type="radio" name="isDeleted" value="1" <?php if($sweepstakes && $sweepstakes->isDeleted == 1) : ?>checked="checked"<?php endif; ?>> Yes
            </label>
    </div>
            
    <div class="form-group" id="div_dialogType">
        <label>
                Dialog Type
            </label>
            <label class="radio-inline">
                <input type="radio" name="dialogType" value="1" <?php if($sweepstakes && $sweepstakes->dialogType == 1) : ?>checked="checked"<?php endif; ?>> Donate / Claim Dialog
            </label>
            <label class="radio-inline">
                <input type="radio" name="dialogType" value="2" <?php if(!$sweepstakes || $sweepstakes->dialogType == 2) : ?>checked="checked"<?php endif; ?>> Forfeit / Claim Dialog
            </label>
    </div>
            
    <div class="form-group" id="div_isImportant">
        <label>
                Important (This makes it stay on the top of the list)?
            </label>
            <label class="radio-inline">
                <input type="radio" name="isImportant" value="0" <?php if(!$sweepstakes || $sweepstakes->isImportant == 0) : ?>checked="checked"<?php endif; ?>> No
            </label>
            <label class="radio-inline">
                <input type="radio" name="isImportant" value="1" <?php if($sweepstakes && $sweepstakes->isImportant == 1) : ?>checked="checked"<?php endif; ?>> Yes
            </label>
    </div>
            
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_show" class="btn btn-primary"><?php if($sweepstakes) : ?>Update<?php else : ?>Add<?php endif;?></button></div>
</form>
</div>

<?php if($rule) : ?>
<div class="panel panel-primary">
    <div class="panel-heading">Rules</div>
    <div class="panel-body">
        <div class="panel panel-default col-lg-6 col-md-6 col-sm-6" style="padding: 0;">
            <div class="panel-heading">Template</div>
            <div class="panel-body">
                <form id="frm_rule">                    
                    <div style="height: 400px;" id="sel_file_name">
                        <select class="form-control" name="sel_file_name" id="sel_fname">
                        <?php foreach($rules as $row) : ?>
                        <option value="<?= $row->ruleURL; ?>"><?= $row->ruleURL; ?></option>
                        <?php endforeach; ?>
                    </select>
                        <textarea class="form-control" id="preview_text" name="text" style="height: 350px;"><?= $rule->template; ?></textarea>
                    </div>
                <div class="form-group" id="div_DeployMobile">
                    <label>
                            Saving Options?
                        </label>
                        <label class="radio-inline">
                            <input type="radio" class="save-options" name="save_options" value="0" checked="">Create New Template
                        </label>
                        <label class="radio-inline">
                            <input type="radio" class="save-options" name="save_options" value="1"> Overwrite Existing Template
                        </label>
                        <label class="radio-inline">
                            <input type="radio" class="save-options" name="save_options" value="2"> Choose Existing Template 
                        </label>
                </div>                
                <input type="hidden" name="rule_id" value="<?= $rule->id; ?>"/>
                <input type="hidden" name="game_type" id="game_type" value="Sweepstakes"/>
                <input type="hidden" name="serial_number" id="serial_number" value="<?= $sweepstakes->serialNumber; ?>"/>
                </form>
            </div>
            <div class="panel-footer" style="text-align: right;"><button type="button" id="update_rule_template" class="btn btn-primary">Save Template File</button></div>
        </div>
        <div class="panel panel-default  col-lg-6 col-md-6 col-sm-6" style="padding: 0;">
            <div class="panel-heading">Game Rule</div>
            <div class="panel-body">
                <textarea class="form-control" id="txt_xlat_text" style="height: 400px;"><?= $rule->text; ?></textarea>
            </div>
            <div class="panel-footer" style="text-align: right;"><button type="button" id="update_rule_game" class="btn btn-primary">Save Rule File</button></div>
        </div>
    </div>
</div>
<?php elseif($sweepstakes) : ?>
    <div class="panel panel-primary">
    <div class="panel-heading">Rules</div>
    <div class="panel-body">
    <select class="form-control" name="sel_file_name_none" id="sel_fname_none">
        <?php foreach($rules as $row) : ?>
        <option value="<?= $row->ruleURL; ?>"><?= $row->ruleURL; ?></option>
        <?php endforeach; ?>
    </select>
        <textarea class="form-control" readonly="" id="preview_text_none" style="height: 350px;"></textarea>    
        <input type="hidden" id="game_type_none" value="Sweepstakes"/>
        <input type="hidden" id="serial_type_none" value="<?= $sweepstakes->serialNumber; ?>"/>
   </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_rule_none" class="btn btn-primary">Create Rules from Template</button></div>
    </div>
<?php endif; ?>

<script>
    $("#update_rule_none").click(function(){
            var data_send = {
                game_type: $("#game_type_none").val(),
                serial_number: $("#serial_type_none").val(),
                file_name: $("#sel_fname_none").val(),
                name: $("#name").val()
            };
            $.post("/admin/ajax_add_rule", data_send, function(data){
                if(data.success)
                {
                    alert('Rules Updated!');
                    location.reload();
                }
                else
                {
                    alert ("Rules Update Failed");
                }
            }, 'json');
        });
    
        $("#sel_fname").change(function(){
             $.post('/admin/get_preview', {file: $(this).val()}, function(data){
                    $("#preview_text").html(data);                    
                });
        });
        
        $(".save-options").click(function(){
            var val = $(this).val();
            if(val == 2)
                $("#update_rule_template").html("Generate Game Rule File from Template");            
            else
                $("#update_rule_template").html("Save Template File");
        });
        
        $("#update_rule_template").click(function(){
            var selected = $(".save-options:checked");
            if(selected.val() == 2)
            {
                $.post("/admin/ajax_add_rule", {file_name: $("#sel_fname").val(), serial_number: $("#serial_number").val(), game_type: $("#game_type").val(), name: $("#name").val()}, function(data){
                    if(data.success)
                    {
                        alert("Game Rules saved for this Sweepstakes");
                        $("#txt_xlat_text").html(data.text);
                    }
                    else
                    {
                        alert("Game Rules were not saved");
                    }
                }, 'json');
            }
            else
            {
                $.post("/admin/ajax_add_rule_template", $("#frm_rule").serialize(), function(data){
                    if(data.success)
                    {
                        alert('Rules Template Updated!');
                        location.reload();
                    }
                    else
                    {
                        alert ("Rules Update Failed");
                    }
                }, 'json');
            }
        });
        
        $("#update_rule_game").click(function(){
            $.post("/admin/ajax_add_rule_game", {text: $("#txt_xlat_text").val(), endDate: $("#EndDate").val(), startDate: $("#StartDate").val(), serial_number: $("#serial_number").val(), game_type: $("#game_type").val(), name: $("#name").val()}, function(data){
                if(data.success)
                {
                    alert("Game Rules saved for this Sweepstakes");
                    $("#txt_xlat_text").html(data.text);
                }
                else
                {
                    alert("Game Rules were not saved");
                }
            }, 'json');
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
    
$(function() {
        $("#uploadImage").click(function(e){
            e.preventDefault(); //prevent default action             
            $("#uploadImageBtn").click();           
        });
        
        $("#uploadTitleImage").click(function(e){
            e.preventDefault(); //prevent default action             
            $("#uploadTitleImageBtn").click();           
        });
        
        $("#update_show").click(function(){
                $.post('/admin_sweepstakes/ajax_add_sweepstakes', $("#frm_sweepstakes").serialize(), function(data){
                    $("#frm_sweepstakes div").removeClass('alert alert-danger');
                        if(data.success)
                        {
                                $("#game_message").html("Insert / Update was good.").addClass("alert alert-success").removeClass('alert-danger');
                                $('html,body').scrollTop(0);
                                var command = "window.location = '/admin_sweepstakes/edit/" + data.id + "';";
                                setTimeout(command, 1000);
                        }
                        else
                        {
                                $("#game_message").addClass("alert alert-danger").html("There were errors. They are listed / highlighted below.");
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
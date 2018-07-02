<div class="panel panel-primary">
    <div class="panel-heading"><?php if($storeItem) : ?>Edit<?php else : ?>Add<?php endif;?> Store Item</div>
    <div class="panel-body">
        <div id="store_item_message"></div>
        <form role="form" id="frm_store_item" enctype="multipart/form-data">
            <?php if($storeItem) : ?> <input type="hidden" name="id" value="<?=$storeItem->id?>"/> <?php endif; ?>
            <div class="form-group" id="div_shortTitle">
              <label for="name">Short Title</label>
              <input type="text" class="form-control" id="shortTitle" name="shortTitle" placeholder="shortTitle" value="<?php if($storeItem) echo $storeItem->shortTitle; ?>">
            </div>
            <div class="form-group" id="div_longTitle">
              <label for="name">Long Title</label>
              <input type="text" class="form-control" id="longTitle" name="longTitle" placeholder="longTitle" value="<?php if($storeItem) echo $storeItem->longTitle; ?>">
            </div>
            <div class="form-group" id="div_summary">
              <label for="name">Summary</label>
              <textarea class="form-control" id="summary" name="summary" placeholder="summary"><?php if($storeItem) echo $storeItem->summary; ?></textarea>
            </div>
            <div class="form-group" id="div_chedda">
              <label for="name">Chedda</label>
              <input type="text" class="form-control" id="chedda" name="chedda" placeholder="chedda" value="<?php if($storeItem) echo $storeItem->chedda; ?>">
            </div>
            <div class="form-group" id="div_amount">
              <label for="name">Amount (in dollars)</label>
              <input type="text" class="form-control" id="amount" name="amount" placeholder="amount" value="<?php if($storeItem) echo $storeItem->amount; ?>">
            </div>
            <div class="form-group" id="div_imageUrl">
              <label for="SerialNumber">Image (500px X 500px)</label>
              <div id="img_image"><img id="imageUrlImg" src="<?php if($storeItem) : ?><?= $storeItem->imageUrl; ?><?php endif; ?>"/></div>
              <input type="hidden" name="imageUrl" id="imageUrl" value="<?php if($storeItem) echo $storeItem->imageUrl; ?>"/>
              <a href="#" id="uploadImage">Select File</a>
                <input style="display:none" type="file" id="uploadImageBtn" accept="image/*" onchange="handleFile(this.files, 'imageUrl')"/>
            </div>
            <div class="form-group" id="div_startDate">
              <label for="startDate">Start Date</label>
              <input type="text" class="form-control" id="StartDate" name="startDate" placeholder="startDate" value="<?php if($storeItem) echo $storeItem->startDate; ?>">
            </div>
            <div class="form-group" id="div_endDate">
              <label for="endDate">End Date</label>
              <input type="text" class="form-control" id="EndDate" name="endDate" placeholder="endDate" value="<?php if($storeItem) echo $storeItem->endDate; ?>">
            </div>
        </form>
    </div>  
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_store_item" class="btn btn-primary"><?php if($storeItem) : ?>Update<?php else : ?>Add<?php endif;?> Store Item</button></div>    
</div>

<script>
   
    function handleFile(files, img_name)
    {        
        $("#update_store_item").prop("disabled", true);
        var fileReader=new FileReader();
        var file=files[0];
        console.log(file);
        var imageElem=document.getElementById(img_name + "Img");//var imageElem=$("<img>");

        fileReader.onload = (function(img) { return function(e) { img.src = e.target.result; }; })(imageElem);
        fileReader.readAsDataURL(file);
       
        uploadFile(files[0], img_name, 'store_items');
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
            $("#update_store_item").prop("disabled", false);
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
        
        $("#update_store_item").click(function(){
                $.post('/admin/ajax_add_store_item', $("#frm_store_item").serialize(), function(data){
                    $("#frm_store_item div").removeClass('alert-danger');
                        if(data.success)
                        {
                                $("#store_item_message").html("Insert / Update was good.").addClass("alert alert-success").removeClass('alert-danger');
                                $('html,body').scrollTop(0);
                                var command = "window.location = '/admin/view_store_items';";
                                setTimeout(command, 1000);
                        }
                        else
                        {
                                $("#store_item_message").addClass("alert alert-danger").html("There were errors. They are listed / highlighted below.");
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
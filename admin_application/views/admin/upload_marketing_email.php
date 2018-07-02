<div class="panel panel-primary">
    <div class="panel-heading">Select which email campaign to assign emails to</div>
    <div class="panel-body">
        <?php foreach($campaigns as $campaign) : ?>
        <input type="checkbox" class="campaigns" name="campaigns" value="<?= $campaign->id; ?>"> <?= $campaign->subject; ?><br/>
        <?php endforeach; ?>
    </div>
    <div class="panel-footer"></div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">Upload Email CSV</div>
    <div class="panel-body">
        <div class="form-group" id="div_emailUpload">
            <label for="SerialNumber">User Information Upload: </label>                        
            <a href="#" id="uploadCsv" class="btn btn-success">Select File</a>
            <input style="display:none" type="file" id="uploadEmailCsv" accept="*.csv" name="emailCsv" onchange="uploadFile(this.files[0])"/>
        </div>
    </div>
    <div class="panel-footer"></div>
</div>

<script>
    $("#uploadCsv").click(function(e){
        e.preventDefault(); //prevent default action             
        $("#uploadEmailCsv").click();           
    });
    
    function uploadFile(file)
    {
        var xhr = new XMLHttpRequest();
        var formData = new FormData();
        var ids = [];
        
        $(".campaigns").each(function(){
            if(this.checked)
                ids.push($(this).val());
        });
        
        formData.append('file',file);
        formData.append('ids', ids);        
       
        xhr.open("POST", "/marketing_campaigns/ajax_update_emails");
        xhr.overrideMimeType('text/plain; charset=x-user-defined-binary');
        xhr.onload = function(){console.log(this.responseText);};
        xhr.onreadystatechange = function() {
            if(this.responseText)
            {
                
            }
            if (xhr.readystate != 4) { return; }
            alert(xhr.responsetext);
        };
        xhr.send(formData);
    }
</script>
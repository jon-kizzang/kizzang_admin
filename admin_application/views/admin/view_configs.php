<div class="panel panel-primary">
    <div class="panel-heading">Select File for Editting</div>
    <div class="panel-body">
        <label>Select File:</label>
        <select id="s3_file" name="file">
            <option value="">Select File</option>
            <?php foreach($configs as $key => $config) : ?>
            <?php if($config['file']) : ?>
            <option value="<?= $config['url']; ?>"><?= $config['file'];?></option>
            <?php else : ?>
            <option disabled="disabled" value="<?= $config['url']; ?>"><?= str_replace($base, '', $config['url']);?></option>
            <?php endif; ?>
            <?php endforeach; ?>
        </select>
        
        <form action="/admin/view_configs" method="POST" style="margin-top: 20px; margin-bottom: 20px;">
        <label>Base</label>        
        <input name="base" id="base" value="<?= $base; ?>"/>
        <input type="submit" value="Update" class="btn btn-success"/>
        <button class="btn btn-default" style="margin-left: 20px;" id="btn_upload">Upload File to S3</button>
        <input style="display:none" type="file" style="margin-left: 20px;" id="S3File" name="S3File" onchange="uploadFile(this.files[0])"/>
        </form>        
        <textarea class="form-control" id="file_text" style="height: 500px; width: 100%;"></textarea>
        <img style="width: 100%;" src="" id="img_view"/>
    </div>
    <div class="panel-footer" style="text-align:right;"><button class="btn btn-success" id="btn_save">Save File to S3</button></div>
</div>

<script>    
    $(function () 
    { 
       $("#s3_file").change(function(){
           if(!/png|jpg|gif/.test($("#s3_file").val()))
           {
               $("#file_text").show();
               $("#img_view").hide();
                $.post("/admin/ajax_get_config", {file: $("#s3_file").val(), bucket: 'kizzang-resources'}, function(data){
                    $("#file_text").val(data);
                });
            }
            else
            {
                $("#file_text").hide();
                $("#img_view").attr('src', 'https://kizzang-resources.s3.amazonaws.com/' + $("#s3_file").val());
                $("#img_view").show();
            }
       });
       
       $("#btn_save").click(function(){
           $.post("/admin/ajax_save_config", {file: $("#s3_file").val(), bucket: 'kizzang-resources', text: $("#file_text").val()}, function(data){               
                   alert(data.message);               
           }, 'json');
       });
       
        $("#btn_upload").click(function(e){
            e.preventDefault(); //prevent default action             
            $("#S3File").click();
        });              
    });
    
    function uploadFile(file)
    {
        var xhr = new XMLHttpRequest();
        var formData = new FormData();

        formData.append('file',file);
        formData.append('url',$("#base").val());

        xhr.open("POST", "/admin/config_file_upload");
        xhr.overrideMimeType('text/plain; charset=x-user-defined-binary');
        xhr.onload = function(){
            alert(this.responseText);
            location.reload();
        };
        xhr.onreadystatechange = function() {            
            if (xhr.readystate != 4) { return; }
            alert(xhr.responsetext);
        };
        xhr.send(formData);
    }
</script>
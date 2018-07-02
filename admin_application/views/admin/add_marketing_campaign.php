<style>
#dragandrophandler
{
border:2px dotted #0B85A1;
width:100%;
text-align: center;
color:#92AAB0;
vertical-align:middle;
padding:10px 10px 10 10px;
margin-bottom:10px;
font-size:200%;
}
.progressBar {
    width: 200px;
    height: 22px;
    border: 1px solid #ddd;
    border-radius: 5px; 
    overflow: hidden;
    display:inline-block;
    margin:0px 10px 5px 5px;
    vertical-align:top;
}
 
.progressBar div {
    height: 100%;
    color: #fff;
    text-align: right;
    line-height: 22px; /* same as #progressBar height if we want text middle aligned */
    width: 0;
    background-color: #0ba1b5; border-radius: 3px; 
}
.statusbar
{
    border-top:1px solid #A9CCD1;
    min-height:25px;
    width:700px;
    padding:10px 10px 0px 10px;
    vertical-align:top;
}
.statusbar:nth-child(odd){
    background:#EBEFF0;
}
.filename
{
display:inline-block;
vertical-align:top;
width:250px;
}
.filesize
{
display:inline-block;
vertical-align:top;
color:#30693D;
width:100px;
margin-left:10px;
margin-right:5px;
}
.abort{
    background-color:#A8352F;
    -moz-border-radius:4px;
    -webkit-border-radius:4px;
    border-radius:4px;display:inline-block;
    color:#fff;
    font-family:arial;font-size:13px;font-weight:normal;
    padding:4px 15px;
    cursor:pointer;
    vertical-align:top
    }
</style>
<script src="//cdn.ckeditor.com/4.4.7/standard/ckeditor.js"></script>
<form id="frm_campaign">    
<div class="panel panel-primary">
    <div class="panel-heading"><?php if($config) : ?>Edit<?php else : ?>Add<?php endif;?> Marketing Email</div>
    <div class="panel-body">
        <div id="game_message"></div>
        <?php if($config) : ?> <input type="hidden" name="id" id="id" value="<?=$config->id?>"/> <?php endif; ?>        
        <div class="form-group" id="div_subject">
        <label for="subject">Subject</label>
        <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" value="<?php if($config) echo $config->subject; ?>">
      </div>
      <div class="form-group" id="div_from_name">
        <label for="from_name">Preview</label>
        <input type="text" class="form-control" id="preview" name="preview" placeholder="Preview" value="<?php if($config) echo $config->preview; ?>">
      </div>
      <div class="form-group" id="div_from_email">
        <label for="from_email">From Address</label>
        <input type="text" class="form-control" id="from_address" name="from_address" placeholder="From Address" value="<?php if($config) echo $config->from_address; ?>">
      </div>          
      <div class="form-group" id="div_body">
        <label for="html_body">Body</label>
        <textarea id="body" name="body" class="form-control" style="min-height: 400px;"><?php if($config) echo $config->body; ?></textarea>        
      </div>      
    </div>
    <div class="panel-footer" style="text-align: right;">
        <button type="button" id="update_campaign" class="btn btn-primary"><?php if($config) : ?>Update<?php else : ?>Add<?php endif;?> Campaign</button>
        <?php if($config) : ?><button type="button" id="send_campaign" class="btn btn-danger">Send Email Campaign</button><?php endif; ?>
    </div>
</div>    
</form>
<?php if($config) : ?>
<div class="panel panel-primary">
    <div class="panel-heading">Add in Emails</div>
    <div class="panel-body">
        <div id="dragandrophandler">Drag & Drop Files Here</div>
        <br><br>
        <div id="status1"></div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">Status</div>
    <div class="panel-body">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th># of Emails Sent</th>
                    <th># of Emails Left</th>
                    <th># Opened</th>
                    <th>% Opened</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $stats->sent; ?></td>
                    <td><?= $stats->left; ?></td>
                    <td><?= $stats->opened; ?></td>
                    <td><?php if($stats->opened_percent) echo number_format($stats->opened_percent, 2); else echo "0" ?> %</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">Current Emails in Campaign</div>
    <div class="panel-body" style="max-height: 400px; overflow: auto;">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($emails as $email) : ?>
                <tr>
                    <td><?= $email->first_name; ?></td>
                    <td><?= $email->last_name; ?></td>
                    <td><?= $email->email; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<script>
    var editor;
$(function() {
    
        $("#update_campaign").click(function(){            
                $.post('/marketing_campaigns/ajax_add_campaign', $("#frm_campaign").serialize(), function(data){
                    $("#frm_campaign div").removeClass('alert-danger');
                        if(data.success)
                        {
                                $("#game_message").html("Insert / Update was good.").addClass("alert alert-success").removeClass('alert-danger');
                                $('html,body').scrollTop(0);
                                var command = "$('#game_message').fadeOut();";
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
        
        $("#send_campaign").click(function(){
           var r = confirm("Are you sure you want to send this out?");
           if(r)
           {
               $.get("/marketing_campaigns/ajax_send_campaign/<?php if($config) echo $config->id; ?>", {}, function(data){
                   if(data.success)
                   {
                       alert(data.message);
                   }
                   else
                   {
                       alert("Something Failed during this process!");
                   }
               }, 'json');
           }
        });
        
        function sendFileToServer(formData,status)
        {
            var uploadURL ="/marketing_campaigns/ajax_add_emails/<?php if($config) echo $config->id; else echo "";?>"; //Upload URL
            var extraData ={}; //Extra Data.
            var jqXHR=$.ajax({
                    xhr: function() {
                    var xhrobj = $.ajaxSettings.xhr();
                    if (xhrobj.upload) {
                            xhrobj.upload.addEventListener('progress', function(event) {
                                var percent = 0;
                                var position = event.loaded || event.position;
                                var total = event.total;
                                if (event.lengthComputable) {
                                    percent = Math.ceil(position / total * 100);
                                }
                                //Set progress
                                status.setProgress(percent);
                            }, false);
                        }
                    return xhrobj;
                },
            url: uploadURL,
            type: "POST",
            contentType:false,
            processData: false,
                cache: false,
                data: formData,
                dataType: 'json',
                success: function(data){
                    status.setProgress(100);
                    alert(data.message);
                    $("#status1").append("File upload Done<br>");         
                }
            }); 

            status.setAbort(jqXHR);
        }

        var rowCount=0;
        function createStatusbar(obj)
        {
             rowCount++;
             var row="odd";
             if(rowCount %2 ==0) row ="even";
             this.statusbar = $("<div class='statusbar "+row+"'></div>");
             this.filename = $("<div class='filename'></div>").appendTo(this.statusbar);
             this.size = $("<div class='filesize'></div>").appendTo(this.statusbar);
             this.progressBar = $("<div class='progressBar'><div></div></div>").appendTo(this.statusbar);
             this.abort = $("<div class='abort'>Abort</div>").appendTo(this.statusbar);
             obj.after(this.statusbar);

            this.setFileNameSize = function(name,size)
            {
                var sizeStr="";
                var sizeKB = size/1024;
                if(parseInt(sizeKB) > 1024)
                {
                    var sizeMB = sizeKB/1024;
                    sizeStr = sizeMB.toFixed(2)+" MB";
                }
                else
                {
                    sizeStr = sizeKB.toFixed(2)+" KB";
                }

                this.filename.html(name);
                this.size.html(sizeStr);
            }
            this.setProgress = function(progress)
            {       
                var progressBarWidth =progress*this.progressBar.width()/ 100;  
                this.progressBar.find('div').animate({ width: progressBarWidth }, 10).html(progress + "% ");
                if(parseInt(progress) >= 100)
                {
                    this.abort.hide();
                }
            }
            this.setAbort = function(jqxhr)
            {
                var sb = this.statusbar;
                this.abort.click(function()
                {
                    jqxhr.abort();
                    sb.hide();
                });
            }
        }
        function handleFileUpload(files,obj)
        {
           for (var i = 0; i < files.length; i++) 
           {
                var fd = new FormData();
                fd.append('file', files[i]);

                var status = new createStatusbar(obj); //Using this we can set progress.
                status.setFileNameSize(files[i].name,files[i].size);
                sendFileToServer(fd,status);

           }
        }
        $(document).ready(function()
        {
            var obj = $("#dragandrophandler");
            obj.on('dragenter', function (e) 
            {
                e.stopPropagation();
                e.preventDefault();
                $(this).css('border', '2px solid #0B85A1');
            });
            obj.on('dragover', function (e) 
            {
                 e.stopPropagation();
                 e.preventDefault();
            });
            obj.on('drop', function (e) 
            {

                 $(this).css('border', '2px dotted #0B85A1');
                 e.preventDefault();
                 var files = e.originalEvent.dataTransfer.files;

                 //We need to send dropped files to Server
                 handleFileUpload(files,obj);
            });
            $(document).on('dragenter', function (e) 
            {
                e.stopPropagation();
                e.preventDefault();
            });
            $(document).on('dragover', function (e) 
            {
              e.stopPropagation();
              e.preventDefault();
              obj.css('border', '2px dotted #0B85A1');
            });
            $(document).on('drop', function (e) 
            {
                e.stopPropagation();
                e.preventDefault();
            });

        });

    });
</script>
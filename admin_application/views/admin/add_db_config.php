<div class="panel panel-primary">
    <div class="panel-heading"><?php if($config) : ?>Edit<?php else : ?>Add<?php endif;?> Config</div>
    <div class="panel-body">
        <div id="config_message"></div>
        <form role="form" id="frm_config">
            <?php if($config) : ?> <input type="hidden" name="id" value="<?=$config->id?>"/> <?php endif; ?>
    <div class="form-group" id="div_main_type">
        <label for="Name">Main Type</label>
        <select class="form-control" name="main_type" id="main_type">
        <?php foreach($main_types as $type) : ?>
            <option value="<?= $type; ?>" <?php if($config && $config->main_type == $type) echo "selected=''"; ?>><?= $type;?></option>
        <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group" id="div_sub_type">
        <label for="Name">Sub Type</label>
        <input type="text" class="form-control" name="sub_type" id="sub_type" value="<?php if($config) echo $config->sub_type; ?>"/>            
    </div>
    <div class="form-group" id="div_action">
        <label for="Name">Action</label>
        <select class="form-control" name="action" id="action">
            <option value="">Select Action</option>
        <?php foreach($actions as $action) : ?>
            <option value="<?= $action; ?>" <?php if($config && $config->action == $action) echo "selected=''"; ?>><?= $action;?></option>
        <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group" id="div_data_type">
        <label for="Name">Data Type</label>
        <select class="form-control" name="data_type" id="data_type">
        <?php foreach($data_types as $type) : ?>
            <option value="<?= $type; ?>" <?php if($config && $config->data_type == $type) echo "selected=''"; ?>><?= $type;?></option>
        <?php endforeach; ?>
        </select>
    </div>  
    <div class="panel panel-primary">
        <div class="panel-heading">Information</div>
        <div class="panel-body" id="info_div">
            <?php if($config) : ?>
                <?php if(is_array($config->info)) : ?>
                    <?php foreach($config->info as $key => $value) : ?>
                    <div class="form-group" id="div_data_type">
                        <label for="Name"><?= ucwords(str_replace("_", " ", $key)); ?></label>
                        <input type="text" class="form-control" name="info_<?= $key; ?>" value="<?php if(is_array($value)) print json_encode($value); else print $value; ?>"/>
                    </div>  
                    <?php endforeach;?>
                <?php else : ?>
                    <div class="form-group" id="div_data_type">
                        <label for="Name"><?= $config->data_type; ?></label>
                        <textarea class="form-control" name="info" cols="30"><?= $config->info; ?></textarea>
                    </div>  
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_config" class="btn btn-primary"><?php if($config) : ?>Update<?php else : ?>Add<?php endif;?> Config</button></div>
</form>
</div>
<script>
    $(document).ready(function(){
        $("#action").change(function(){
            getInfoFields();
        });
        
        $("#main_type").change(function(){
            getInfoFields();
        });
        
        $("#sub_type").change(function(){
            getInfoFields();
        });
        
        $("#data_type").change(function(){
            getInfoFields();
        });
        
        $("#update_config").click(function(){
            $.post("/admin/ajax_add_db_config", $("#frm_config").serialize(), function(data){
                if(data.success)
                {
                    $("#config_message").addClass("well").addClass("success").html("Config Added / Updated Correctly");
                    setTimeout("location.href = '/admin/configs'", 2000);
                }
                else
                {
                    $("#config_message").addClass("well").addClass("danger").html("Information Not Saved!!");
                }
            }, 'JSON')
        })
    });
    
    function getInfoFields()
    {
        var data = {main_type: $("#main_type").val(), sub_type: $("#sub_type").val(), action: $("#action").val(), data_type: $("#data_type").val()};
        $.post("/admin/ajax_get_config_db_info", data, function(data){
            if(data.success)
            {
                $("#info_div").html(data.html);
            }
            else
            {
                $("#info_div").html("");
            }
        }, 'JSON');
    }
</script>
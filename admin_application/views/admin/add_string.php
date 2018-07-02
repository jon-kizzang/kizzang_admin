<div class="panel panel-primary" style="margin-bottom: 0px;">
    <div class="panel-heading"><?php if($string) : ?>Edit<?php else : ?>Add<?php endif; ?> String</div>
    <div class="panel-body">
        <div id="payout_message"></div>
        <div class="form-group" id="div_DeployWeb">
        <label>
                Identifier Type
            </label>
            <label class="radio-inline">
                <input type="radio" class="identifier_type" name="it" value="0"> New
            </label>
            <label class="radio-inline">
                <input type="radio" class="identifier_type" name="it" value="1" checked=""> Existing
            </label>
    </div>
        <form role="form" id="frm_string">   
   <?php if($string) : ?><input type="hidden" name="id" value="<?=$string->id?>"/><?php endif; ?>
   <input type="hidden" name="identifier" id="identifier" value="<?php if($string) echo $string->identifier; ?>"/>
    <div class="form-group" id="div_identifier">
    <label for="Name">Identifier</label>
    <select class="form-control" id="identifier_sel" placeholder="identifier">  
        <option value="">Select Identifier</option>
        <?php foreach($identifiers as $identifier) : ?>                                
            <option value="<?=$identifier->identifier?>" <?php if($string && $string->identifier == $identifier->identifier) echo 'selected=""'; ?>><?=$identifier->identifier?></option>                
        <?php endforeach; ?>
    </select>
    <input type="text" id="identifier_text" class="form-control" style="display: none;"/>
    </div>
   <div class="form-group" id="div_languageCode">
    <label for="Name">Language</label>
    <select class="form-control" id="languageCode" name="languageCode">
        <?php foreach($languages as $language) : ?>                                
            <option value="<?=$language->id?>" <?php if(($string && $string->languageCode == $language->id) || (!$string && $language->id == $cur_language)) echo 'selected=""'; ?>><?=$language->language?></option>                
        <?php endforeach; ?>
    </select>    
    </div>
  <div class="form-group" id="div_description">
    <label for="description">Description</label>
    <textarea class="form-control" id="description" name="description" placeholder="description"><?php if($string)  echo $string->description;?></textarea>
  </div>
   <div class="form-group" id="div_translation">
    <label for="translation">Translation</label>
    <textarea class="form-control" id="translation" name="translation" placeholder="translation"><?php if($string)  echo $string->translation;?></textarea>
  </div>
   
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_string" class="btn btn-primary"><?php if($string) : ?>Update<?php else : ?>Add<?php endif; ?> String</button></div>
</form>
</div>

<script>
$(function() {
        $("#update_string").click(function(){
                $.post('/admin/ajax_add_string', $("#frm_string").serialize(), function(data){
                        $("#frm_string div").removeClass('alert-danger');
                        if(data.success)
                        {
                                $("#payout_message").html("Insert / Update of the String was good.").addClass("alert alert-success").removeClass('alert-danger');
                                $('html,body').scrollTop(0);
                                var command = "window.location = '/admin/view_strings';";
                                setTimeout(command, 1000);
                        }
                        else
                        {
                                $("#payout_message").addClass("alert alert-danger").html("There were errors. They are listed / highlighted below.");
                                for(var key in data.errors)
                                    $("#div_" + key).addClass("alert-danger");
                        }
                },'json');
        });      
        
        $(".identifier_type").click(function(){
            var val = $(this).val();
            if(val == 1)
            {
                $("#identifier_sel").show();
                $("#identifier_text").hide();
                $("#identifier").val($("#identifier_sel").val());
            }
            else
            {
                $("#identifier_sel").hide();
                $("#identifier_text").show();
                $("#identifier").val($("#identifier_text").val());
            }
        });
        
        $("#identifier_sel").change(function(){
            $("#identifier").val($(this).val());
        });
        
        $("#identifier_text").change(function(){
            $("#identifier").val($(this).val());
        });
        
    });
</script>
<div class="panel panel-primary">
    <div class="panel-heading">Add Push Notification</div>
    <div class="panel-body">
        <div id="player_message"></div>    
   <form role="form" id="frm_pn">      
       <?php if($pn) : ?><input type="hidden" name="id" value="<?= $pn['id']; ?>"/><?php endif; ?>
    <div class="form-group" id="div_contents">
    <label for="contents">Content (Content overrides the templates)</label>
    <textarea type="text" class="form-control" id="contents" name="contents" placeholder="contents"><?php if($pn) echo $pn['contents']; ?></textarea>
    </div>
    <div class="form-group" id="div_headings">
    <label for="headings">Headings</label>
    <textarea type="text" class="form-control" id="headings" name="headings" placeholder="headings"><?php if($pn) echo $pn['headings']; ?></textarea>
    </div>
    <div id="filters">
       <input type="hidden" id="filter_count" value="<?php if($pn) echo count($pn['tags']); else echo "1"; ?>"/>
    <?php if(isset($pn)) : ?>
       <?php foreach($pn['tags'] as $index => $rule) : ?>
        <div class="form-group" id="filter_<?= $index; ?>">
         <label for="Name">Filter: </label>
         <select class="form-inline" id="key" name="key[<?= $index; ?>]">
             <?php foreach($tags as $tag) : ?>                                
                    <option value="<?=$tag?>" <?php if($rule['key'] == $tag) echo "selected=''"; ?>><?=$tag?></option>
             <?php endforeach; ?>
         </select>
         <select class="form-inline" id="relation" name="relation[<?= $index; ?>]" placeholder="included_segments">                
             <?php foreach($relations as $relation) : ?>                                
                    <option value="<?=$relation?>" <?php if($rule['relation'] == $relation) echo "selected=''"; ?>><?=$relation?></option>
             <?php endforeach; ?>
         </select>
         <input type="text" class="form-inline" placeholder="value" name="value[<?= $index; ?>]" value="<?= $rule['value']; ?>"/>
         <button class="btn btn-primary add-filter">Add Filter</button>
         <?php if($index) : ?><button class="btn btn-danger delete-filter" onClick="$('#filter_<?= $index; ?>').remove();">Remove Filter</button><?php endif; ?>
         </div>
       <?php endforeach; ?>
    <?php else : ?>    
        <div class="form-group" id="filter_0">
        <label for="Name">Filter: </label>
        <select class="form-inline" id="key" name="key[0]">
            <?php foreach($tags as $tag) : ?>                                
                   <option value="<?=$tag?>"><?=$tag?></option>
            <?php endforeach; ?>
        </select>
        <select class="form-inline" id="relation" name="relation[0]" placeholder="included_segments">                
            <?php foreach($relations as $relation) : ?>                                
                   <option value="<?=$relation?>"><?=$relation?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" class="form-inline" placeholder="value" name="value[0]"/>
        <button class="btn btn-primary add-filter">Add Filter</button>
        </div>
   <?php endif; ?>
   </div>
   <div class="form-group" id="div_devices">
    <label for="contents">Select Device Group(s)</label>
        <div class="checkbox">
         <label>
             <input type="checkbox" name="isIos" <?php if(isset($pn['isIos']) && $pn['isIos']) echo "checked=''";?>> iOS
         </label>
       </div>
       <div class="checkbox">
         <label>
             <input type="checkbox" name="isAndroid" <?php if(isset($pn['isAndroid']) && $pn['isAndroid']) echo "checked=''";?>> Android
         </label>
       </div>
   </div>
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="run_pn" class="btn btn-primary">Run</button><button style="margin-left: 20px;" type="button" id="queue_pn" class="btn btn-success">Save to Queue</button></div>
</form>
</div>

<script>
$(function() {
       $("#run_pn").click(function(e)
       {
            e.preventDefault();
            var p = confirm("Are you sure you want to run this job right now?");
            if(p)
            {
                $.post("/admin/ajax_add_notifications", $("#frm_pn").serialize(), function(data)
                {              
                    alert(data.message);               
                }, 
                'json');
            }           
       });
       
       $("#queue_pn").click(function(e){
           e.preventDefault();
           $.post("/admin/ajax_add_notification_queue", $("#frm_pn").serialize(), function(data){              
               alert(data.message);  
               if(data.success)
                    location.href = "/admin/view_notifications";
           }, 'json');
       });       
       
       $("#filters").on("click", ".add-filter", function(e){
           e.preventDefault();
           var id = $("#filter_count").val();           
          $.get("/admin/add_notification_filter/" + id, {}, function(data){
              $("#filters").append(data);
              $("#filter_count").val(parseInt(id) + 1);
          },'html') 
       });              
});
</script>
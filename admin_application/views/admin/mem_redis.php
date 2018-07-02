<div class="panel panel-primary">
    <div class="panel-heading">Memcache</div>
    <div class="panel-body">
        <div class="panel panel-primary" style="padding: 0px;">
            <div class="panel-heading">Local Keys</div>
            <div class="panel-body">
                <select id="local_keys" class="form-control">
                    <option value="all">All</option>
                    <?php foreach($keys as $key) : ?>
                    <?php if(stristr($key, "KEY") != FALSE) : ?>
                    <option value="<?= $key; ?>"><?= $key; ?></option>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <div style="margin-top: 10px; text-align: right;">                    
                    <button id="btn_local_check" class="btn btn-primary">Check</button>
                    <button id="btn_local_delete" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
        <div class="panel panel-primary" style="padding: 0px;">
            <div class="panel-heading">Manual</div>
            <div class="panel-body">
                <input type="text" class="form-control" id="general" value="<?= "KEY-Sweepstake-Active" . md5( "getAll_sweepsstake" ); ?>"/>
                <div style="margin-top: 10px; text-align: right;">
                    <button id="btn_sweep_check" class="btn btn-primary">Check</button>
                    <button id="btn_sweep_delete" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>        
        <div class="panel panel-primary" style="padding: 0px;">
            <div class="panel-heading">Results</div>
            <div class="panel-body">
                <textarea class="form-control" style="height: 400px;" id="results"></textarea>
            </div>
        </div>
    </div>    
</div>

<script>
  
  $("#btn_local_delete").click(function(){      
      $.post("/admin/ajax_memcache_local_delete", {key: $("#local_keys").val()}, function(data){
          if(data.success)
          {             
             alert("Key Deleted from Memcache");            
          }
          else
          {
              alert("Something Went Wrong");
          }
      }, 'json');
  });
  
  $("#btn_local_check").click(function(){      
      $.post("/admin/ajax_memcache_local_get", {key: $("#local_keys").val()}, function(data){
          if(data.success)
          {             
             $("#results").val(data.message);
          }
          else
          {
              alert("Something Went Wrong");
          }
      }, 'json');
  });
  
  $("#btn_sweep_check").click(function(){      
      $.post("/admin/ajax_memcache_check", {key: $("#general").val()}, function(data){
          if(data.success)
          {
              $("#results").val(data.message);
          }
          else
          {
              alert("Something Went Wrong");
          }
      }, 'json');
  });
  
  $("#btn_sweep_delete").click(function(){      
      $.post("/admin/ajax_memcache_delete", {key: $("#general").val()}, function(data){
          if(data.success)
          {
              if(data.message)
                  alert("Key Deleted from Memcache");
              else
                  alert("Key wasn't in Memcache");
          }
          else
          {
              alert("Something Went Wrong");
          }
      }, 'json');
  });
  
  $("#btn_scratcher_check").click(function(){      
      $.post("/admin/ajax_memcache_check", {key: $("#scratchers").val(), type: 'scratcher'}, function(data){
          if(data.success)
          {
              $("#results").val(data.message);
          }
          else
          {
              alert("Something Went Wrong");
          }
      }, 'json');
  });
  
  $("#btn_scratcher_delete").click(function(){      
      $.post("/admin/ajax_memcache_delete", {key: $("#scratchers").val()}, function(data){
          if(data.success)
          {
              if(data.message)
                  alert("Key Deleted from Memcache");
              else
                  alert("Key wasn't in Memcache");
          }
          else
          {
              alert("Something Went Wrong");
          }
      }, 'json');
  });
</script>
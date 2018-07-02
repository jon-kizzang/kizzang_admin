<link href="/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<script src="/js/jquery.datetimepicker.js"></script>
<div class="modal-header">Add Event Notification</div>
<div class="modal-body">
    
<div class="panel panel-primary">    
    <div class="panel-heading">General Notification</div>
    <div class="panel-body">
        <form id="frm_en">
            <div class="form-group" id="div_title">
                <label>
                    Notification Type
                </label>
                <label class="radio-inline">
                    <input type="radio" class="notification" name="notification_type" value="notice" checked=""> Notice
                </label>                
            </div>
            <div class="form-group" id="div_title">
            <label for="screen_name">Title</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="">
            </div>
            <div class="form-group" id="div_body">
            <label for="screen_name">Description</label>
            <textarea class="form-control" id="description" name="description" placeholder="description"></textarea>
            </div>            
            <div class="form-group" id="div_endDate">
            <label for="screen_name">End Date</label>
            <input type="text" class="form-control" id="EndDate" name="end_date" value="">
            </div>           
            <div class="form-group" id="div_users">
            <label for="screen_name">Users to Distribute to</label>
            <select name="users" id="users" class="form-control">
                <?php foreach($users as $user) : ?>
                <option value="<?= $user; ?>"><?= $user; ?></option>
                <?php endforeach; ?>
            </select>
            </div>
        </form>
    </div>
    <div class="panel-footer" style="text-align: right;"><button class="btn btn-success" id="add_notification">Add Notification</button></div>
</div>
<div class="modal-footer"><button data-dismiss="modal" type="button" id="update_prize" class="btn btn-primary">Close</button></div>
<script>
    $("#add_notification").click(function(e){
        e.preventDefault();
        $.post("/admin/ajax_add_event_notification", $("#frm_en").serialize(), function(data){
            if(data.success)
            {
                alert(data.message);
            }
            else
            {
                alert("An Error Occurred");
            }
        }, 'json');
    });
        
    $('#StartDate').datetimepicker({
        format:'Y-m-d H:i',
        value:'<?= date("Y-m-d H:i", strtotime($current_date)); ?>',
        step: 10
      });

      $('#EndDate').datetimepicker({
        format:'Y-m-d H:i',
        value:'<?= date("Y-m-d H:i", strtotime($current_date)); ?>',
        step: 10
      });
</script>
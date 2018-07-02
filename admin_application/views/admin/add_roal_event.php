<link href="/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<script src="/js/jquery.datetimepicker.js"></script>
<div class="modal-header">Add Event</div>
<div class="modal-body">
    <div class="well">
        <form role="form" id="frm_parlay_search" class="form-inline">
            <input type="hidden" name="parlay_id" id="id" value="<?= $id; ?>"/>
            <label>Category: </label>
            <select id="sel_parlay_cat" name="sel_parlay_cat">
                <option value="">All</option>
                <?php foreach($categories as $category) : ?>
                <option value="<?=$category->id; ?>"><?=$category->name; ?></option>
                <?php endforeach; ?>
            </select>
            <label>Team</label>
            <select id="sel_parlay_team" name="sel_parlay_team">
                <option value="">All</option>                
            </select>
            <label>Date</label>
            <input type="text" id="parlay_date" class="form-control" name="parlay_date" value="<?=$config->cardDate; ?>"/>
            <button id="parlay_search" class="btn btn-primary">Search</button>               
        </form>
    </div>
    <div class="well-sm"># of Events: <span id="counter"><?= $count; ?></span></div>
    <div id="parlay_results" style="width: 100%; height: 400px; overflow: auto;">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Team 1</th>
                    <th>Team 2</th>
                    <th>Diff</th>
                    <th>Date</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($events as $event) : ?>
                <tr id="new_event_<?= $event->event_id; ?>">
                    <td><?= $event->category; ?></td>
                    <td><?= $event->team1; ?> (<?= $event->pr1; ?>)</td>
                    <td><?= $event->team2; ?> (<?= $event->pr2; ?>)</td>
                    <td><?= $event->diff; ?></td>
                    <td><?= $event->date; ?></td>
                    <td><input type="text" class="form-control dates" id="sd_<?= $event->event_id; ?>"/></td>
                    <td><input type="text" class="form-control dates" id="ed_<?= $event->event_id; ?>"/></td>
                    <td><button class="btn btn-default" onClick="add_parlay_event(<?= $event->event_id; ?>);">Add</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal-footer"><button class="btn btn-default" id="close-btn" data-dismiss="modal" type="button">Close</button></div>

<script>
$(function() {
        $("#sel_parlay_cat").change(function(){
            var id = $(this).val();
            $.get('/admin_sports/ajax_category_change/' + id, {}, function(data){
                $("#sel_parlay_team").html("<option value=''>ALL</option>");
                $("#sel_parlay_team").append(data);
            });
        });
        
        $( "#parlay_date" ).datepicker({
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            numberOfMonths: 1
        });
             
        $('.dates').datetimepicker({
            format:'Y-m-d H:i',
            minDate:'<?= date('Y/m/d', strtotime($config->cardDate)); ?>',
            maxDate:'<?= date('Y/m/d', strtotime($config->cardDate)); ?>',
            step: 10
          });
        
        $("#parlay_search").click(function(e){
            e.preventDefault();
            $.post("/admin/search_roal_events", $("#frm_parlay_search").serialize(), function(data){
              $("#parlay_results").html(data);
            });
        });  
        
        $("#close-btn").click(function(){
           window.location.reload(); 
        });
        
    });
    
    function add_parlay_event(event_id)
    {
        var id = $("#id").val();
        $.post("/admin/add_event_roal", {id: id, event_id: event_id, startDate: $("#sd_" + event_id).val(), endDate: $("#ed_" + event_id).val()}, function(data){
            if(data.success)
            {
                $("#new_event_" + event_id).remove();
                $("#counter").html(data.count);
            }
            else
            {
                alert(data.message);
            }
        }, 'json');
    }
</script>
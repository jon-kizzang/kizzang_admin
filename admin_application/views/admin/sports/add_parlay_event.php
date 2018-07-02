<div class="modal-header">Add Event</div>
<div class="modal-body">
    <div class="well">
        <form role="form" id="frm_parlay_search" class="form-inline">
            <input type="hidden" name="parlay_id" id="parlay_id" value="<?= $parlay_id; ?>"/>
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
            <input type="hidden" name="parlay_type" value="<?= $config->type; ?>"/>
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
                    <th>Over/Under</th>
                    <th>Spread</th>
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
                    <td><input type="text" style="width: 50px;" class="form-control" id="ou_<?= $event->event_id; ?>"/></td>
                    <td><input type="text" style="width: 50px;" class="form-control" id="spread_<?= $event->event_id; ?>"/></td>
                    <td><button class="btn btn-default" onClick="add_parlay_event(<?= $event->event_id; ?>);">Add</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>        
    </div>
    <div class="well-lg">
        <form class="form-inline" id="frmQuestion" method="POST">
            <div class="form-group">
              <label for="exampleInputName2">Question</label>
              <input type="text" class="form-control" id="question" name="question" placeholder="Question">
            </div>
            <div class="form-group">
              <label for="exampleInputName2">Answer 1</label>
              <input type="text" class="form-control" id="answer1" name="answer1" placeholder="First Answer">
            </div>
            <div class="form-group">
              <label for="exampleInputName2">Answer 2</label>
              <input type="text" class="form-control" id="answer2" name="answer2" placeholder="Second Answer">
            </div>
            <button type="submit" id="btn-save-question" class="btn btn-default">Save</button>
        </form>
    </div>
</div>
<div class="modal-footer"><button class="btn btn-default" data-dismiss="modal" type="button">Close</button></div>

<script>
$(function() {
    
        $("#btn-save-question").click(function(e){
            e.preventDefault();
            $.post("/admin_sports/ajax_add_question_parlay/" + $("#parlay_id").val(), $("#frmQuestion").serialize(), function(data){
                if(data.success)
                {
                    $("#counter").html(data.count);
                    $("#question").val("");
                    $("#answer1").val("");
                    $("#answer2").val("");
                    alert("Question Added");
                }
            }, 'json');
        });
        
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
        
        $("#parlay_search").click(function(e){
            e.preventDefault();
            $.post("/admin_sports/search_parlay_events", $("#frm_parlay_search").serialize(), function(data){
              $("#parlay_results").html(data);
            });
        });                
        
    });
    
    function add_parlay_event(event_id)
    {
        var parlay_id = $("#parlay_id").val();
        $.post("/admin_sports/add_event_parlay", {parlay_id: parlay_id, event_id: event_id, ou: $("#ou_" + event_id).val(), spread: $("#spread_" + event_id).val()}, function(data){
            if(data.success)
            {
                $("#new_event_" + event_id).remove();
                $("#counter").html(data.count);
            }
            else
            {
                alert(data.error);
            }
        }, 'json');
    }
</script>
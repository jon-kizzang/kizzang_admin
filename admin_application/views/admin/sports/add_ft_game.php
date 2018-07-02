<div class="modal-header">Add Event</div>
<div class="modal-body">
    <div class="well">
        <form role="form" id="frm_ft_game">
            <input type="hidden" name="finalConfigId" value="<?= $id; ?>"/>
            <div class="form-group" id="divc_rank">
            <label>Category: </label>
            <select id="rank" name="gameType" class="form-control">                
                <?php foreach($categories as $category) : ?>
                <option value="<?=$category; ?>"><?=$category; ?></option>
                <?php endforeach; ?>
            </select>
            </div>
            
            <div class="form-group" id="divc_dateTime">
                <label for="dateTime">Date</label>
                <input type="text" class="form-control" id="dateTime" name="dateTime" placeholder="dateTime" value="">
              </div>
            
            <div class="form-group" id="divc_rank">
            <label>Team 1: </label>
            <select id="teamId1" name="teamId1" class="form-control">                
                <?php foreach($teams as $team) : ?>
                <option value="<?=$team->id; ?>"><?=$team->name; ?></option>
                <?php endforeach; ?>
            </select>
            </div>
            
            <div class="form-group" id="divc_rank">
            <label>Team 2: </label>
            <select id="teamId2" name="teamId2" class="form-control">                
                <?php foreach($teams as $team) : ?>
                <option value="<?=$team->id; ?>"><?=$team->name; ?></option>
                <?php endforeach; ?>
            </select>
            </div>            
        </form>
    </div>   
    </div>
<div class="modal-footer"><button class="btn btn-primary" id="btn_add_game" type="button">Add</button><button class="btn btn-default" data-dismiss="modal" type="button">Close</button></div>

<script>
$(function() {
    $("#btn_add_game").click(function(){
        $.post("/admin_sports/ajax_add_ft_game", $("#frm_ft_game").serialize(), function(data){
            location.reload();
        }, 'json');
    });
        
    $('#dateTime').datetimepicker({
            format:'Y-m-d H:i',            
            step: 5
          });
});
</script>
<div class="panel panel-primary">    
    <div class="panel-heading">Add in Manual Payment</div>
    <div class="panel-body">
        <div id="payout_message"></div>
        <form role="form" id="frm_manual_payment">
            <div class="form-group" id="div_type">
             <label for="Name">Type</label>
             <select class="form-control" id="type" name="type">
                 <?php foreach($types as $type) : ?>
                 <option value="<?= $type; ?>"><?= $type; ?></option>
                 <?php endforeach; ?>
             </select>
           </div>
            <div class="form-group" id="div_playerId">
             <label for="Name">Player ID</label>
             <input type="text" class="form-control" id="playerId" name="playerId" placeholder="1">
             <input type="text" class="form-control" id="playerName">
           </div>
            <div class="form-group" id="div_serialNumber">
             <label for="Name">Serial Number</label>
             <input type="text" class="form-control" id="serialNumber" name="data[serialNumber]" placeholder="serialNumber">
           </div>
            <div class="form-group" id="div_prizeAmount">
             <label for="Name">Prize Amount</label>
             <input type="text" class="form-control" id="prizeAmount" name="data[prizeAmount]" placeholder="prizeAmount">
           </div>
            <div class="form-group" id="div_prizeName">
             <label for="Name">Prize Name</label>
             <input type="text" class="form-control" id="prizeName" name="data[prizeName]" placeholder="prizeName">
           </div>
            <div class="form-group" id="div_gameName">
             <label for="Name">Game Name</label>
             <input type="text" class="form-control" id="gameName" name="data[gameName]" placeholder="gameName">
           </div>
        </form>
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="add_manual_payment" class="btn btn-primary">Add Payment</button></div>
</div>

<script>
    $("#add_manual_payment").click(function(){
        if(confirm("Are you sure you want to create this potential winner?"))
        {
            $.post("/admin/ajax_add_manual_payment", $("#frm_manual_payment").serialize(), function(data){
                if(data.status)
                {
                    alert("Record Added to player's notifications.");
                    window.location.reload();
                }
                else
                {
                    alert(data.errors);
                }
            }, 'json');
        }
    });
    
    $("#playerId").blur(function(){
       $.get('/admin/ajax_get_player_name/' + $("#playerId").val(), {}, function(data){
           $("#playerName").val(data);
       }, 'html');
    });
</script>
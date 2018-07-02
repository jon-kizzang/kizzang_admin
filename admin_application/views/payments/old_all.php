<style>
    #mask {
            opacity: .3;
            background-color: #000;
            display: none;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            position: fixed;
            z-index: 1000;
    }
    
    #mask_message {
            z-index: 1001;
            height: 100px;
            width: 200px;
            background-color: #FFF;
            position: fixed;
            display: none;
            padding: 40px;
    }
</style>

<div class="well">
    <label>Balance: </label> Unknown 
    <label style="margin-left: 20px;">Days Back:</label>
    <select id="days_back">
        <option value="7" <?php if($days_back == 7) echo "selected";?>>1 Week</option>
        <option value="14" <?php if($days_back == 14) echo "selected";?>>2 Weeks</option>
        <option value="21" <?php if($days_back == 21) echo "selected";?>>3 Weeks</option>
        <option value="28" <?php if($days_back == 28) echo "selected";?>>4 Weeks</option>
        <option value="60" <?php if($days_back == 60) echo "selected";?>>2 Months</option>
        <option value="90" <?php if($days_back == 90) echo "selected";?>>3 Months</option>
        <option value="120" <?php if($days_back == 120) echo "selected";?>>4 Months</option>
        <option value="150" <?php if($days_back == 150) echo "selected";?>>5 Months</option>
        <option value="180" <?php if($days_back == 180) echo "selected";?>>6 Months</option>
        <option value="180" <?php if($days_back == 365) echo "selected";?>>1 Year</option>
    </select>
    <label style="margin-left: 20px;">Unpaid: </label> $<?= number_format($unpaid, 2); ?>        
    <br/>    
    <label>Dollar Amount: </label>
    <input type="radio" class="dollar-amount" name="dollar_amount" <?php if($dollar_amount == "0") echo "checked='checked'";?> value="0" style="margin-left: 10px;"/> All
    <?php foreach($dollar_amounts as $key => $value) : ?>
    <input type="radio" class="dollar-amount" name="dollar_amount" value="<?= $key; ?>" <?php if($key == $dollar_amount && $dollar_amount !=  '0') echo 'checked="checked"'; ?> style="margin-left: 10px;"/> <?= $value; ?>
    <?php endforeach; ?>
    <br/>
    <button class="btn btn-warning" id="mass_pay">Mass Pay Checked</button>
    <!--<button class="btn btn-danger" id="inactive_accounts">Forfeit Inactive Account</button>-->
</div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>Mass Pay</th>
            <th>ID</th>            
            <th>Player ID</th>
            <th>Name</th>
            <th>Address</th>
            <th>Serial Number</th>
            <th>Prize</th>
            <th>Win Date</th>                        
            <th>Edit</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($recs as $row) : ?>
        <tr id="row_<?= $row->id; ?>">
            <td><?php if($row->amountPaid == 0) : ?><input type="checkbox" class="mass-pay" name="mass_pay[]" value="<?= $row->id; ?>"/><?php endif;?></td>
            <td><?= $row->id; ?></td>
            <td><?= $row->playerId; ?></td>
            <td><?= $row->firstName . " " . $row->lastName; ?></td>
            <td><?= $row->city . ", " . $row->state . " " . $row->zipcode; ?></td>
            <td><?= $row->serialNumber; ?></td>
            <td><?= '$' . number_format($row->amount, 2); ?></td>
            <td><?= date("D M d, Y", strtotime($row->dateWon)); ?></td>
            <td><a class="btn btn-primary pay-btn" rel="<?= $row->id; ?>">Pay</a>
                <a class="btn btn-warning manual-pay-btn" rel="<?= $row->id; ?>">Manual Pay</a>
                <a class="btn btn-danger forfeit-btn" rel="<?= $row->id; ?>">Forfeit</a> 
            </td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<div id="mask">
</div>
<div id="mask_message">Processing Orders</div>
<script>
    $(document).ready(function() {
        var ids = [];
        $("#mass_pay").click(function(){            
            $("#mask").show();
            $("#mask_message").css({left: window.innerWidth / 2 - 100, top: window.innerHeight / 2 -50}).show();;
            $(this).prop('disabled', true);
           
            $.post("/payment/mass_pay_old", {ids: ids}, function(data){
                alert(data.message);
                window.location.reload();
            }, 'json');
        });
        
        $(".mass-pay").click(function(){
            if($(this).is(":checked"))
            {
                ids.push($(this).val())
            }
            else
            {
                var removeItem = $(this).val();
                ids = $.grep(ids, function(value) {
                    return value != removeItem;
                  });
            }
            console.log(ids);
        });
        
        $(".pay-btn").click(function(){
           var id = $(this).attr('rel');
           $("#mask").show();
           $("#mask_message").css({left: window.innerWidth / 2 - 100, top: window.innerHeight / 2 -50}).show();;
           $(this).prop('disabled', true);
           $.get('/payment/ajax_old_pay/' + id, {}, function(data){
               if(data.success)
               {
                   alert(data.message);
                   $("#row_" + id).fadeOut();
                   $("#mask").hide();
                   $("#mask_message").hide();
               }
               else
               {
                   alert("Error Creating Payment: " . data.error);
               }
           }, 'json');
        });
        
        $(".manual-pay-btn").click(function(){
           var id = $(this).attr('rel');
           $("#mask").show();
           $("#mask_message").css({left: window.innerWidth / 2 - 100, top: window.innerHeight / 2 -50}).show();;
           $(this).prop('disabled', true);
           $.get('/payment/ajax_old_manual_pay/' + id, {}, function(data){
               if(data.success)
               {
                   $("#mask").hide();
                   $("#mask_message").hide();
                   $("#row_" + id).fadeOut();
               }
               else
               {
                   alert("Error Creating Payment: " . data.error);
               }
           }, 'json');
        });
        
        $(".forfeit-btn").click(function(){
           var id = $(this).attr('rel');
           $("#mask").show();
           $("#mask_message").css({left: window.innerWidth / 2 - 100, top: window.innerHeight / 2 -50}).show();;
           $(this).prop('disabled', true);
           $.get('/payment/ajax_old_forfeit/' + id, {}, function(data){
               if(data.success)
               {
                   $("#mask").hide();
                   $("#mask_message").hide();               
                   $("#row_" + id).fadeOut();
               }
               else
               {
                   alert("Error Creating Payment: " . data.error);
               }
           }, 'json');
        });
        
        $('#show_games').dataTable({pageLength: 50, order: [[ 1, "desc" ]]});
        $("#days_back").change(function(){                 
            var dollar_amount = $(".dollar-amount:checked").val();
            location.href = "/payment/old_all/" + $(this).val() + "/" + dollar_amount;
        });            
        $(".dollar-amount").change(function(){
            var db = $("#days_back").val();          
            var dollar_amount = $(".dollar-amount:checked").val();
            location.href = "/payment/old_all/" + db + "/" + dollar_amount;
        });        
    });
</script>
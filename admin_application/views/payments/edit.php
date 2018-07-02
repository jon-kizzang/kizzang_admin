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
            padding: 20px;
            text-align: center;
    }
    
    #frm_power_rank div {
        margin-left: 10px;
    }
    
    .modal-lg {
        width: 1200px !important;
    }
</style>
<div class="panel panel-primary">
    <div class="panel-heading">Claim - <?= $rec->id; ?></div>
    <input type="hidden" id="id" value="<?= $rec->id; ?>"/>
    <div class="panel-body">
        <div class="panel panel-primary col-lg-12 col-md-12 col-sm-12" style="padding: 0px;">
            <div class="panel-heading">Win Information</div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Claim Status</th>
                        <td><?= $rec->status; ?></td>
                    </tr>                    
                    <tr>
                        <th>Player ID</th>
                        <td><?= $rec->playerId; ?></td>
                    </tr>                    
                    <tr>
                        <th>Entry ID</th>
                        <td><?= $rec->winnerId; ?></td>
                    </tr>
                    <tr>
                        <th>Prize Name</th>
                        <td><?= $rec->prizeName; ?></td>
                    </tr>
                    <tr>
                        <th>Prize Amount</th>
                        <td><?= $rec->amount; ?></td>
                    </tr>
                    <tr>
                        <th>Win Date</th>
                        <td><?= $rec->created; ?></td>
                    </tr>                    
                    <tr>
                        <th>Win YTD</th>
                        <td><?= $rec->ytd; ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="panel panel-primary col-lg-12 col-md-12 col-sm-12" style="padding: 0px;">
            <div class="panel-heading">Player Information</div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <tr>
                        <th>First Name</th>
                        <td><?= $rec->firstName; ?></td>
                    </tr>
                    <tr>
                        <th>Last Name</th>
                        <td><?= $rec->lastName; ?></td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td><?= $rec->address; ?></td>
                    </tr>
                    <tr>
                        <th>City, State, Zip</th>
                        <td><?= $rec->city . " " . $rec->state . ", " . $rec->zip; ?></td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td><?= $rec->phone; ?></td>
                    </tr>                    
                    <tr>
                        <th>Email</th>
                        <td><?= $rec->email; ?></td>
                    </tr>                    
                </table>
            </div>
            <div class="panel-footer" style="text-align: right;"></div>
        </div>
 
        <div class="panel panel-primary col-lg-12 col-md-12 col-sm-12" style="padding: 0px;">
            <div class="panel-heading">Paypal Information</div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Email</th>
                        <td><?= $rec->payPalEmail; ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><?= $rec->payPalStatus; ?></td>
                    </tr>
                    <tr>
                        <th>Transaction ID</th>
                        <td><?= $rec->payPalTransactionId; ?></td>
                    </tr>
                    <tr>
                        <th>Item ID</th>
                        <td><?= $rec->payPalItemId; ?></td>
                    </tr>
                    <tr>
                        <th>Error</th>
                        <td><?= $rec->payPalError; ?></td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td><?= $rec->updated; ?></td>
                    </tr>                    
                </table>
            </div>
        </div>
    </div>
    <div class="panel-footer" style="text-align: right;">        
        <button class="btn btn-success" id="btn_pay">Pay</button>
        <button class="btn btn-warning" id="btn_manual_pay">Manual Pay</button>
        <button class="btn btn-danger" id="btn_forfeit">Forfeit</button>
    </div>
</div>
<div id="mask"></div>
<div id="mask_message"></div>
<script>

  $(document).ready(function(){
      function showMask(message)
      {
          $("#mask").show();
          $("#mask_message").css({left: window.innerWidth / 2 - 100, top: window.innerHeight / 2 -50}).show();
          $("#mask_message").html(message);
      }
      
      function hideMask()
      {
          $("#mask").hide();
          setTimeout('$("#mask_message").fadeOut()', 2000);
      }
      
     $("#btn_pay").click(function(){
         $(this).prop("disabled", true);
         showMask("Processing Payment");
         $.get("/payment/ajax_pay/" + $("#id").val(), {}, function(data){             
             if(data.success)
             {
                 $("#mask_message").html(data.message);
                 location.reload();
             }
             else
             {
                 $("#mask_message").html(data.message);
             }
             hideMask();
         }, 'json');
     });     
     
     $("#btn_manual_pay").click(function(){
         $(this).prop("disabled", true);
         showMask("Processing Manual Payment");
         $.get("/payment/ajax_manual_pay/" + $("#id").val(), {}, function(data){             
             if(data.success)
             {
                 $("#mask_message").html(data.message);
                 location.reload();
             }
             else
             {
                 $("#mask_message").html(data.message);
             }
             hideMask();
         }, 'json');
     });
     
     $("#btn_forfeit").click(function(){
         $(this).prop("disabled", true);
         showMask("Processing Payment Forfeit");
         $.get("/payment/ajax_forfeit/" + $("#id").val(), {}, function(data){             
            if(data.success)
             {
                 $("#mask_message").html(data.message);
                 location.reload();
             }
             else
             {
                 $("#mask_message").html(data.message);
             }
             hideMask();
         }, 'json');
     });          
     
  });

</script>
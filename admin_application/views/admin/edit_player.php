<div class="panel panel-primary">
    <div class="panel-heading"><?php if($player) : ?>Edit<?php else : ?>Add<?php endif;?> Player</div>
    <div class="panel-body">
        <div id="player_message"></div>
    <?php if($player && $player['fbId']) : ?>
        <img src="https://graph.facebook.com/v2.2/<?=$player['fbId']; ?>/picture?width=140&height=140" style="margin-bottom: 30px;"/>
    <?php endif; ?>
    <div class="form-group" id="div_screenName">
    <label for="screenName">Available Chedda</label>
    <input type="text" class="form-control" readonly="" value="<?= $chedda ?>">
    </div>
    <?php if($player && $daily_action) : ?>
        <label>View Day Activites: </label>
        <select id="daily_action" class="form-control">
            <option value="">Select Date</option>
            <?php foreach($daily_action as $action) : ?>
            <option value="<?= $action->date; ?>"><?= $action->date; ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-success" id="btn_show_activity">Show Activity</button>        
    <?php endif; ?>
   <form role="form" id="frm_player">
   <?php if($player) : ?> <input type="hidden" id="player_id" name="id" value="<?=$player['id']?>"/> <?php endif; ?>
   <div class="form-group" id="div_roleId">
    <label for="Name">Role</label>
    <select class="form-control" id="userType" name="userType">        
        <?php foreach($roles as $role) : ?>                                
               <option value="<?=$role?>" <?php if($role == $player['userType']) echo 'selected=""';?>><?=$role?></option>               
        <?php endforeach; ?>
    </select>
  </div>
   <div class="form-group" id="div_staus">
    <label for="Name">Status (1 is Bad and 5 is Good)</label>
    <select class="form-control" id="status" name="status">        
        <?php for($i = 1; $i < 6; $i++) : ?>                                
               <option value="<?=$i?>" <?php if($i == $player['status']) echo 'selected=""';?>><?=$i?></option>               
        <?php endfor; ?>
    </select>
    <a class="btn btn-primary" href="/admin/view_player_notes/<?= $player['id']; ?>" data-target="#modal" data-toggle="modal">Add / Read Notes</a>
  </div>
    <div class="form-group" id="div_screenName">
    <label for="screenName">Screen Name</label>
    <input type="text" class="form-control" id="screenName" name="screenName" placeholder="screenName" value="<?php if($player) echo $player['screenName']; ?>">
    </div>
    <div class="form-group" id="div_firstName">
    <label for="firstName">First Name</label>
    <input type="text" class="form-control" id="firstName" name="firstName" placeholder="firstName" value="<?php if($player) echo $player['firstName']; ?>">
    </div>
    <div class="form-group" id="div_lastName">
    <label for="lastName">Last Name</label>
    <input type="text" class="form-control" id="lastName" name="lastName" placeholder="lastName" value="<?php if($player) echo $player['lastName']; ?>">
    </div>
    <div class="form-group" id="div_gender">
    <label for="Name">Gender</label>
    <select class="form-control" id="gender" name="gender" placeholder="gender">        
        <?php foreach($genders as $gender) : ?>                                
               <option value="<?=$gender?>" <?php if($gender == $player['gender']) echo 'selected=""';?>><?=$gender?></option>
        <?php endforeach; ?>
    </select>
  </div>
    <div class="form-group" id="div_address">
    <label for="address">Address</label>
    <input type="text" class="form-control" id="address" name="address" placeholder="address" value="<?php if($player) echo $player['address']; ?>">
    </div>    
    <div class="form-group" id="div_city">
    <label for="city">City</label>
    <input type="text" class="form-control" id="city" name="city" placeholder="city" value="<?php if($player) echo $player['city']; ?>">
    </div>
    <div class="form-group" id="div_state">
    <label for="state">State</label>
    <input type="text" class="form-control" id="state" name="state" placeholder="state" value="<?php if($player) echo $player['state']; ?>">
    </div>
    <div class="form-group" id="div_zip">
    <label for="zip">Zip</label>
    <input type="text" class="form-control" id="zip" name="zip" placeholder="zip" value="<?php if($player) echo $player['zip']; ?>">
    </div>
    <div class="form-group" id="div_phone">
    <label for="phone">Cell Phone</label>
    <input type="text" class="form-control" id="phone" name="phone" placeholder="phone" value="<?php if($player) echo $player['phone']; ?>">
    </div>    
    <div class="form-group" id="div_email">
    <label for="email">Email</label>
    <input type="text" class="form-control" id="email" name="email" placeholder="email" value="<?php if($player) echo $player['email']; ?>">
    </div>
   <div class="form-group" id="div_payPal">
    <label for="payPal">Paypal Email</label>
    <input type="text" class="form-control" name="payPalEmail" placeholder="payPal" value="<?php if($player) echo $player['payPalEmail']; ?>">
    </div>
   <?php if($player && $player['fbId']) : ?>
   <div class="form-group" id="div_payPal">
    <label for="payPal">Facebook ID</label>
    <input type="text" class="form-control" readonly="readonly" placeholder="payPal" value="<?php if($player) echo $player['fbId']; ?>">
    </div>
   <?php endif; ?>
    <div class="form-group" id="div_dob">
    <label for="dob">Date of Birth</label>
    <input type="text" class="form-control" id="dob" name="dob" placeholder="dob" value="<?php if($player) echo $player['dob']; ?>">
    </div>
   <div class="form-group" id="div_password">
    <label for="password">Password (Leave blank to not change)</label>
    <input type="password" class="form-control" id="password" name="password" placeholder="password" value="">
    </div>
    <div class="form-group" id="div_accountStatus">
    <label for="Name">Account Status</label>
    <select class="form-control" id="accountStatus" name="accountStatus" placeholder="accountStatus">        
        <?php foreach($accountStatuses as $accountStatus) : ?>                                
               <option value="<?=$accountStatus?>" <?php if($accountStatus == $player['accountStatus']) echo 'selected=""';?>><?=$accountStatus?></option>               
        <?php endforeach; ?>
    </select>
    </div>
    <div class="form-group" id="div_emailStatus">
    <label for="Name">Email Status</label>
    <select class="form-control" id="emailStatus" name="emailStatus" placeholder="emailStatus">        
        <?php foreach($emailStatuses as $emailStatus) : ?>                                
               <option value="<?=$emailStatus?>" <?php if($emailStatus == $player['emailStatus']) echo 'selected=""';?>><?=$emailStatus?></option>               
        <?php endforeach; ?>
    </select>
    </div>
   <div class="form-group" id="div_newUserFlow">
        <label>
                Did New User Flow?
            </label>
            <label class="radio-inline">
                <input type="radio" name="newUserFlow" value="0" <?php if($player && $player['newUserFlow'] == 0) : ?>checked="checked"<?php endif; ?>> Completed
            </label>
            <label class="radio-inline">
                <input type="radio" name="newUserFlow" value="1" <?php if(!$player || $player['newUserFlow'] == 1) : ?>checked="checked"<?php endif; ?>> Pending
            </label>
    </div>
   <div class="form-group" id="div_profileComplete">
        <label>
                Profile Complete?
            </label>
            <label class="radio-inline">
                <input type="radio" name="profileComplete" value="0" <?php if($player && $player['profileComplete'] == 0) : ?>checked="checked"<?php endif; ?>> No
            </label>
            <label class="radio-inline">
                <input type="radio" name="profileComplete" value="1" <?php if(!$player || $player['profileComplete'] == 1) : ?>checked="checked"<?php endif; ?>> Yes
            </label>
    </div>   
    </div>
    <div class="panel-footer" style="text-align: right;">
        <button type="button" id="force_logout" class="btn btn-danger">Force Logout</button>
        <button type="button" id="update_address" class="btn btn-success">Update Address via SmartyStreets</button>
        <button type="button" id="update_show" class="btn btn-primary"><?php if($player) : ?>Update<?php else : ?>Add<?php endif;?></button>        
    </div>
</form>
</div>

<?php if($devices) : ?>
<div class="panel panel-primary">
    <div class="panel-heading">Device</div>
    <div class="panel-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Device Type</th>
                    <th>OS Version</th>
                    <th>Device Model</th>
                    <th>Timezone</th>                    
                </tr>
            </thead>   
            <?php foreach($devices as $device) : ?>
            <tr>
                <td><?= $device->device_type; ?></td>
                <td><?= $device->device_os; ?></td>
                <td><?php if(isset($device->tooltip)) : ?>                   
                    <div class="dropdown">
                        <a class="dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false">
                            <i><?= $device->device_model; ?></i>
                            <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:void(0);"><strong>Description: </strong><?= $device->tooltip->description; ?></a></li>
                            <li class="divider"></li>
                            <li><a href="javascript:void(0);"><strong>Firmware: </strong><?= $device->tooltip->firmware; ?></a></li>
                            <li class="divider"></li>
                            <li><a href="javascript:void(0);"><strong>Inches: </strong><?= $device->tooltip->inches; ?></a></li>
                            <li class="divider"></li>
                            <li><a href="javascript:void(0);"><strong>Pixels: </strong><?= $device->tooltip->pixels; ?></a></li>
                            <li class="divider"></li>
                            <li><a href="javascript:void(0);"><strong>Points: </strong><?= $device->tooltip->points; ?></a></li>
                        </ul>
                    </div <?php else : ?><?= $device->device_model; ?> <?php endif; ?></td>
                <td><?=$device->timezone; ?></td>
            </tr>                        
            <?php endforeach; ?>
        </table>        
    </div>
    <div class="panel-footer" style="text-align: right;"></div>
</div>
<?php endif; ?>

<?php if($versions) : ?>
<div class="panel panel-primary">
    <div class="panel-heading">Versions</div>
    <div class="panel-body" style="max-height: 400px; overflow: auto;">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Login Type</th>
                    <th>Login Source</th>
                    <th>Mobile Type</th>
                    <th>Last Build Version</th>                    
                </tr>
            </thead>         
            <?php foreach($versions as $version) : ?>            
            <tr>
                <td><?= $version->loginType; ?></td>
                <td><?= $version->loginSource; ?></td>
                <td><?= $version->mobileType; ?></td>
                <td><?= $version->appId; ?></td>                
            </tr>            
            <?php endforeach;?>
        </table>        
    </div>
    <div class="panel-footer" style="text-align: right;"></div>
</div>
<?php endif; ?>

<?php if($winners) : ?>
<div class="panel panel-primary">
    <div class="panel-heading">Winnings</div>
    <div class="panel-body" style="max-height: 400px; overflow: auto;">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Serial Number</th>
                    <th>Game Type</th>
                    <th>Prize Name</th>
                    <th>Amount</th>
                    <th>Win Date</th>
                    <th>Winner Status</th>
                    <th>Payment Status</th>
                    <th>System of Origin</th>
                </tr>
            </thead>         
            <?php foreach($winners as $winner) : ?>            
            <tr>
                <td><?= $winner->serialNumber; ?></td>
                <td><?= $winner->game_type; ?></td>
                <td><?= $winner->prize_name; ?></td>
                <td>$<?= number_format($winner->amount, 2); ?></td>
                <td><?= date("D M d, Y", strtotime($winner->win_date)); ?></td>
                <td><?= $winner->winStatus; ?></td>
                <td><?php if($winner->status) echo $winner->status; else echo 'N/A'; ?></td>
                <td><?php if($winner->prize_email == "Imported from Old System") echo "Old"; else echo "New"; ?></td>
            </tr>            
            <?php endforeach;?>
        </table>        
    </div>
    <div class="panel-footer" style="text-align: right;"></div>
</div>
<?php endif; ?>

<div class="panel panel-primary">
    <div class="panel-heading">Games Played in the Last 2 weeks</div>
    <div class="panel-body" style="max-height: 400px; overflow: auto;">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Game Type</th>
                    <th># Played</th>                    
                </tr>
            </thead>         
            <?php foreach($stats as $stat) : ?>
            <?php if(!$stat->playerId) continue; ?>
            <tr>
                <td><?php if($stat->date) echo $stat->date; else echo "TOTAL";?></td>
                <td><?php if($stat->gameType) echo $stat->gameType; else echo "TOTAL"; ?></td>
                <td><?= $stat->count?></td>                
            </tr>            
            <?php endforeach;?>
        </table>        
    </div>
    <div class="panel-footer" style="text-align: right;"></div>
</div>

<script>
$(function() {
        $("#update_show").click(function(){
                $.post('/admin/ajax_update_player', $("#frm_player").serialize(), function(data){
                        if(data.success)
                        {
                                $("#player_message").html("Insert / Update was good.").addClass("alert alert-success").removeClass('alert-danger');
                                $('html,body').scrollTop(0);                                
                                var command = "window.location = '/admin/players/';";
                                setTimeout(command, 1000);
                        }
                        else
                        {
                                $("#player_message").addClass("alert alert-danger").html("There were errors. They are listed / highlighted below.");
                                for(var key in data.errors)
                                    $("#div_" + key).addClass("alert-danger");
                                $('html,body').scrollTop(0);
                        }
                },'json');
        });    
        
        $("#update_address").click(function(){
            $.get("https://api.smartystreets.com/street-address?street=" + $("#address").val().replace("#", "unit ") + " " + $("#address2").val().replace("#", "unit ") + "," + $("#city").val() + "," + $("#state").val() + "," + $("#zip").val() + "&auth-id=f7867503-3e93-da8e-bdc5-453c9fe5776c&auth-token=3uLgEY76G3kN580RN9Q8", {}, function(data){
                console.log(data);
                
                $.each(data, function(index, row){
                    var r = confirm("Do you want to use this address?\n" + row.delivery_line_1 + " " + row.last_line);
                    if(r)
                    {
                        var address = address2 = city = zip = state = "";                                                        
                        address = row.delivery_line_1;                                                        
                        city = row.components.city_name;                            
                        state = row.components.state_abbreviation;
                        zip = row.components.zipcode;                                

                        $("#address").val(address);                            
                        $("#city").val(city);
                        $("#zip").val(zip);
                        $("#state").val(state);
                        alert("Address Updated.  Please click the 'Update' button to save to the database.");
                    }
                });
                
            }, 'json');
        });
        
        $("#force_logout").click(function(){
           $.get("/admin/force_logout/" + $("#player_id").val(), {}, function(data){
               if(data.code == 0)
               {
                   alert("Successful Logout of Player!");
               }
               else
               {
                    if(data.message)
                        alert(data.message);
                    else
                        alert("Player is currently not logged in.");
               }
           }, 'json') ;
        });
        
        $("#btn_show_activity").click(function(){
            $("#modal .modal-content").load("/admin/get_player_daily/" + $("#player_id").val() + "/" + $("#daily_action").val());
            $("#modal").modal("show");
        });        
        
    });
</script>
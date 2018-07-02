<div id="winner_message"></div>
<div class="panel panel-primary">
    <div class="panel-heading">Basic Win Information</div>
    <div class="panel-body">
        <table class="table table-striped table-responsive">
            <tbody>
            <tr>
                <th>Game Type</th>
                <td><?= $winner->game_type; ?></td>
                <th>Serial Number</th>
                <td><?= $winner->serial_number; ?></td>            
                <th>Prize Name</th>
                <td><?= $winner->prize_name; ?></td>
                <th>Amount</th>
                <td><?= $winner->amount; ?></td>
            </tr>
            <tr>
                <th>First Name</th>
                <td><?= $user->firstName; ?></td>
                <th>Last Name</th>
                <td><?= $user->lastName; ?></td>
                <th>Email</th>
                <td><?= $user->email; ?></td>
                <th>PayPal Email</th>
                <td><?= $user->payPalEmail; ?></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?= $user->address; ?></td>
                <th>City</th>
                <td><?= $user->city; ?></td>
                <th>State</th>
                <td><?= $user->state; ?></td>
                <th>Zip Code</th>
                <td><?= $user->zip; ?></td>
            </tr>
            <tr>
                <th>Phone</th>
                <td><?= $user->phone; ?></td>
                <th>Mobile Phone</th>
                <td><?= $user->mobilePhone; ?></td>
                <th>Gender</th>
                <td><?= $user->gender; ?></td>
                <th>Account Status</th>
                <td><?= $user->accountStatus; ?></td>
            </tr>
            </tbody>
        </table>        
    </div>
    <div class="panel-footer" style="text-align: right;"><a href="/payment/dups/<?= $user->id; ?>" class="btn btn-success" data-target="#big-modal" data-toggle="modal">Check Dups</a></div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">Documents</div>
    <div class="panel-body">
        <?php if($document) : ?>
        <table class="table table-striped table-responsive">
            <tbody>
                <tr>
                    <th>Document Link</th>
                    <td><a href="<?= $document->signedUrl; ?>" target="_blank"><img style="width: 300px;" src="<?= $document->thumbUrl; ?>"/></a></td>
                </tr>
                <?php foreach($attachments as $attachment) : ?>
                <tr>
                    <th>Attachment Link</th>
                    <td><a href="<?= $attachment->downloadUrl; ?>" target="_blank"><?= $attachment->action; ?></a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <div class="panel-footer" style="text-align: right;"><button class="btn btn-default" id="btnUpdateDocs">Grab latest Documents</button></div>
</div>

<form id="frmInfo" method="POST">
    <input type="hidden" name="id" value="<?= $winner->id; ?>"/>
    <input type="hidden" name="playerId" value="<?= $winner->player_id; ?>"/>
<div class="panel panel-primary col-lg-4" style="padding: 0px;">
    <div class="panel-heading">Call 1</div>
    <div class="panel-body">
        <div class="form-group" id="div_StartTime">
            <label for="StartTime">Start Time</label>
            <input type="text" class="form-control" name="WinnerCalls[0][callDate]" id="winnerCall0" placeholder="" value="<?php if(isset($calls[0])) echo $calls[0]->callDate;?>">
            <button onclick="$('#winnerCall0').val(formatDate()); return false;" class="btn btn-success">Now</button>
        </div>
        <div class="form-group" id="div_StartTime">
            <label for="StartTime">Start Time</label>
            <select class="form-control" name="WinnerCalls[0][result]" id="winnerResult0">
                <option value="">Select Result</option>
                <?php foreach($callResults as $result) : ?>
                <option value="<?= $result; ?>" <?php if(isset($calls[0]) && $calls[0]->result == $result) echo "selected='selected'";?>><?= $result; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<div class="panel panel-primary col-lg-4" style="padding: 0px;">
    <div class="panel-heading">Call 2</div>
    <div class="panel-body">
        <div class="form-group" id="div_StartTime">
            <label for="StartTime">Start Time</label>
            <input type="text" class="form-control" name="WinnerCalls[1][callDate]" id="winnerCall1" placeholder="" value="<?php if(isset($calls[1])) echo $calls[1]->callDate;?>">
            <button onclick="$('#winnerCall1').val(formatDate()); return false;" class="btn btn-success">Now</button>
        </div>
        <div class="form-group" id="div_StartTime">
            <label for="StartTime">Start Time</label>
            <select class="form-control" name="WinnerCalls[1][result]" id="winnerResult1">
                <option value="">Select Result</option>
                <?php foreach($callResults as $result) : ?>
                <option value="<?= $result; ?>" <?php if(isset($calls[1]) && $calls[1]->result == $result) echo "selected='selected'";?>><?= $result; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<div class="panel panel-primary col-lg-4" style="padding: 0px;">
    <div class="panel-heading">Call 3</div>
    <div class="panel-body">
        <div class="form-group" id="div_StartTime">
            <label for="StartTime">Start Time</label>
            <input type="text" class="form-control" name="WinnerCalls[2][callDate]" id="winnerCall2" placeholder="" value="<?php if(isset($calls[2])) echo $calls[2]->callDate;?>">
            <button onclick="$('#winnerCall2').val(formatDate()); return false;" class="btn btn-success">Now</button>
        </div>
        <div class="form-group" id="div_StartTime">
            <label for="StartTime">Start Time</label>
            <select class="form-control" name="WinnerCalls[2][result]" id="winnerResult2">
                <option value="">Select Result</option>
                <?php foreach($callResults as $result) : ?>
                <option value="<?= $result; ?>" <?php if(isset($calls[2]) && $calls[2]->result == $result) echo "selected='selected'";?>><?= $result; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<div class="panel panel-primary col-lg-12" style="padding: 0px;">
    <div class="panel-heading">Questions</div>
    <div class="panel-body">
        <table class="table table-striped">
            <tr><th style="width: 80%;">Questions:</th><th>Pass</th><th>Fail</th></tr>
        <?php foreach ($questions as $index => $question) : ?>
            <tr>
                <td><?php if($question->required) echo "<b>"; ?><?= ($index + 1) . ". " . $question->question; ?><?php if($question->required) echo "</b>"; ?></td>
                <td><input type="radio" value="1" name="question[<?= $question->id; ?>]" <?php if(isset($answers[$question->id]) && $answers[$question->id]) print "checked=''"; ?>/></td>
                <td><input type="radio" value="0" name="question[<?= $question->id; ?>]" <?php if(isset($answers[$question->id]) && !$answers[$question->id]) print "checked=''"; ?>/></td>
            </tr>
        <?php endforeach; ?>
        </table>
    </div>    
</div>
    
<div class="panel panel-primary" style="padding: 0px;">
    <div class="panel-heading">Win Status</div>
    <div class="panel-body">
        <div class="form-group" id="div_StartTime">
            <label for="StartTime">Status</label>
            <select class="form-control" name="status" id="status">
                <?php foreach($statuses as $result) : ?>
                <option value="<?= $result; ?>" <?php if($winner->status == $result) echo "selected='selected'";?>><?= $result; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" id="div_comments">
            <label for="comments">Comments</label>
            <textarea class="form-control" name="comments" id="comments"><?= $winner->comments; ?></textarea>
        </div>
    </div>
    <div class="panel-footer" style="text-align: right;"><button class="btn btn-success" id="btn_save">Save All</button></div>
</div>
</form>

<script>
       
    function formatDate() 
    {
        var date = new Date();
        var hours = date.getHours();
        var minutes = date.getMinutes();
        if(minutes < 10)
            minutes = "0" + minutes;
        var strTime = hours + ':' + minutes + ':00';        
        return date.getFullYear() + "-" + (date.getMonth()+1) + "-" + date.getDate() + " " + strTime;
    }       
  
    $(function() 
    {        
        $("#btn_save").click(function(e)
        {
            e.preventDefault();
            $.post('/admin/ajax_add_validate_winner', $("#frmInfo").serialize(), function(data){                    
                if(data.success)
                {
                        $("#winner_message").html("Insert / Update was good.").addClass("alert alert-success").removeClass('alert-danger');
                        $('html,body').scrollTop(0);
                        var command = "window.location = '/admin/view_winners';";
                        setTimeout(command, 1000);
                }
                else
                {
                        $("#winner_message").addClass("alert alert-danger").html("There were errors. They are listed / highlighted below.");
                        for(var key in data.errors)
                            $("#div_" + key).addClass("alert-danger");
                        $('html,body').scrollTop(0);
                }
            },'json');
        });
        
        $("#btnUpdateDocs").click(function(){
            $(this).attr('disabled', true);
           $.get("/admin/ajax_update_winner_docs/<?= $winner->player_id; ?>", {}, function(data){
               location.reload();
           }, 'json' );
        });
        
        $( "#winDate" ).datepicker({
            dateFormat: "yy-mm-dd",
            setDate: "+1d", 
            changeMonth: true,
            numberOfMonths: 3            
        });
        
        
    });
</script>
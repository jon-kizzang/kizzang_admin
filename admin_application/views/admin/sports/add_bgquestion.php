<div class="modal-header">Add Question</div>
<div class="modal-body">
<div class="panel panel-primary">    
    <div class="panel-heading"><?php if($question) : ?>Edit<?php else : ?>Add<?php endif; ?> Question</div>
    <div class="panel-body">
        <div id="question_message"></div>
   <form role="form" id="frm_bgquestion">
   <?php if($question) : ?><input type="hidden" name="id" value="<?=$question->id?>"/><?php endif; ?>
   <input type="hidden" name="parlayCardId" value="<?=$config_id?>"/>
   <div class="form-group" id="div_question">
    <label for="Name">Question</label>
    <input type="text" class="form-control" id="question" name="question" placeholder="question" <?php if($question) : ?> value="<?= $question->question?>"<?php endif; ?>>
  </div>
   <div class="form-group" id="div_startDate">
    <label for="StartDate">Start Date</label>
    <input type="text" class="form-control" id="StartDateQ" name="startDate" placeholder="StartDate" value="<?php if($question) echo date("Y-m-d", strtotime($question->startDate)); ?>">
  </div>
    <div class="form-group" id="div_endDate">
    <label for="EndDate">End Date</label>
    <input type="text" class="form-control" id="EndDateQ" name="endDate" placeholder="EndDate" value="<?php if($question) echo date("Y-m-d", strtotime($question->endDate)); ?>">
  </div>   
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_question" class="btn btn-primary update-question"><?php if($question) : ?>Update<?php else : ?>Add<?php endif; ?> Question</button></div>
</form>
</div>

<div class="panel panel-primary" style="margin-bottom: 0px;">    
    <div class="panel-heading">Answers</div>
    <div class="panel-body">
        <div id="answer_message"></div>
        <table id="tbl_answer" class="table table-striped table-responsive">
            <thead>
                <tr>
                    <th>Answer</th>                    
                    <th>Update</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($answers as $answer) : ?>
                <tr id="tr_<?= $answer->id; ?>">
                    <td><input type="text" id="answer_<?= $answer->id; ?>" value="<?= $answer->answer; ?>"/></td>
                    <td><button type="button" rel="<?= $answer->id; ?>" class="btn btn-success update-answer">Update</button></td>
                    <td><button type="button" rel="<?= $answer->id; ?>" class="btn btn-danger delete-answer">Remove</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="add_answer" <?php if($question) : ?> rel="<?= $question->id; ?>" <?php else : ?>disabled=""<?php endif; ?> class="btn btn-primary">Add Answer</button></div>
</div>

</div>
<div class="modal-footer"><button class="btn btn-default modal-close" data-dismiss="modal" type="button">Close</button></div>
<script>
    $(".update-question").click(function(){
        $.post("/admin_sports/ajax_add_bg_question", $("#frm_bgquestion").serialize(), function(data){
            if(data.success)
            {
                $("#add_answer").attr('rel', data.id).prop('disabled', false);
                $("#frm_bgquestion").append($("<input>").attr('type', 'hidden').attr('value', data.id));
                $("#question_message").html("Question Saved").addClass("alert alert-success");
                setTimeout('$("#question_message").removeClass("alert alert-success").html("")', 2000);
            }
            else
            {
                $("#question_message").addClass("alert alert-danger").html("There were errors. They are listed / highlighted below.");
                for(var key in data.errors)
                    $("#div_" + key).addClass("alert-danger");
            }
        },'json');
    });
    
    $(".modal-close").click(function(){location.reload();});
    
    $(".update-answer").click(function(){
        var id = $(this).attr("rel");
        var answer = $("#answer_" + id).val();
        $.post("/admin_sports/ajax_update_bg_answer", {id: id, answer: answer}, function(data){
            if(data.success)
                $("#answer_message").addClass("alert alert-success").html("Answer Updated");
                setTimeout('$("#answer_message").removeClass("alert alert-success").html("");', 2000);
        }, 'json');
    });
    
    $('body').on('click', ".delete-answer", function(){
        var id = $(this).attr("rel");      
        var r = confirm("Are you sure you want to delete this answer?");
        if(r)
        {
            $.post("/admin_sports/ajax_delete_bg_answer", {answer_id: id}, function(data){
                if(data.success)
                    $("#tr_" + id).remove();
                    $("#answer_message").addClass("alert alert-success").html("Answer Deleted");
                    setTimeout('$("#answer_message").removeClass("alert alert-success").html("");', 2000);
            }, 'json');
        }
    });
    
    $("#add_answer").click(function(){
        var answer = prompt("Please Enter in the Answer Below:");
        if(answer)
        {
            var question_id = $(this).attr('rel');
            $.post("/admin_sports/ajax_add_bg_answer", {answer: answer, questionId: question_id}, function(data){
                if(data.success)
                {
                    $("#tbl_answer").append(data.row);
                }
            }, 'json');
        }
    });
    
       $( "#StartDateQ" ).datepicker({
            dateFormat: "yy-mm-dd",
            setDate: "+1d", 
            changeMonth: true,
            numberOfMonths: 3,
            onClose: function( selectedDate ) {
                $( "#EndDateQ" ).datepicker( "option", "minDate", selectedDate );
            }
        });
        
        $( "#EndDateQ" ).datepicker({
            dateFormat: "yy-mm-dd",
            setDate: "+1w",
            changeMonth: true,
            numberOfMonths: 3,
            onClose: function( selectedDate ) {
                $( "#StartDateQ" ).datepicker( "option", "maxDate", selectedDate );
            }
        });
    
</script>
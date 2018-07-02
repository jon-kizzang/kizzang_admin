<style>
    .trigger-answer {
        margin-left: 3px;
        margin-top: 3px;
    }
</style>
<div class="modal-header">Lottery Answers</div>
<div class="modal-body">
    
<div class="panel panel-primary">    
    <div class="panel-heading">Pick Answers</div>
    <div class="panel-body">
        <input type="hidden" id="config_id" value="<?= $config->id; ?>"/>
        <input type="hidden" id="config_max" value="<?= $config->numAnswerBalls; ?>"/>
        <?php for($i = 1; $i <= $config->numTotalBalls; $i++) : ?>
        <button rel="<?= $i; ?>" class="btn trigger-answer <?php if(in_array($i, $answers)) echo 'btn-success'; else echo 'btn-danger'; ?>"><?php if($i < 10) echo  "0$i"; else echo $i; ?></button>
        <?php endfor; ?>
    </div>
    <div class="panel-footer" style="text-align: right;"><button id="update_answers" class="btn btn-primary">Update</button></div>
</div>
</div>
<div class="modal-footer"><button data-dismiss="modal" type="button" id="update_prize" class="btn btn-primary">Close</button></div>
<script>
    $(".trigger-answer").click(function(){
        if($(this).hasClass("btn-success"))
        {
            $(this).removeClass("btn-success");
            $(this).addClass("btn-danger");
        }
        else
        {
            $(this).removeClass("btn-danger");
            $(this).addClass("btn-success");
        }
    });
    
    $("#update_answers").click(function(){
        var answers = [];
        var ansNum = $("#config_max").val();
        var id = $("#config_id").val();
        $(".btn-success").each(function(i, e){
            if($(e).attr("rel") !== undefined)
                answers.push($(e).attr("rel"));
        });
        if(answers.length != ansNum)
        {
            alert("You need to select exactly " + ansNum + " buttons not " + answers.length);
            console.log(answers);
        }
        else
        {
            $.post("/lottery/ajax_update_answers", {id: id, answers: answers}, function(data){
                if(data.success)
                    alert("Answers Updated!");
                else
                    alert("Answers were NOT Updated!");
            }, 'JSON');
        }
    });
</script>
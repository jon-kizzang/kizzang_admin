<div class="modal-header">Grade Questions for <strong><?= $config->name; ?></strong></div>
<div class="modal-body">
<div id="grade_message"></div>
<input type="hidden" id="config_id" value="<?= $config->id; ?>"/>
<table id="tbl_grades" class="table table-striped">
    <thead>
        <tr>            
            <th>Question</th>
            <th>Answers</th>            
        </tr>
    </thead>
    <tbody>
            <?php foreach($questions as $question) : ?>
        <tr>            
            <td><?= $question->question; ?></td>
            <td id="tr_grade_<?= $question->id; ?>">
                <?php foreach($question->answers as $id => $answer) : ?>
                <button class="btn btn-answer <?php if(in_array($id, $selected)) : ?>btn-success<?php else : ?>btn-default<?php endif; ?>" rel="<?= $id; ?>" id="btn_<?= $id; ?>" qid="<?= $question->id; ?>"><?= $answer; ?></button>
                <?php endforeach; ?>
            </td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
</div>
<div class="modal-footer"><button class="btn btn-primary" id="btn_save_grades">Save</button><button class="btn btn-default modal-close" data-dismiss="modal" type="button">Close</button></div>
<script type="text/javascript" charset="utf-8">        
    $(document).ready(function(){
        $(".btn-answer").click(function(){
            var qid = $(this).attr("qid");
            var id = $(this).attr("rel");
            $("#tr_grade_" + qid + " button").removeClass("btn-default btn-success").addClass("btn-default");
            $(this).addClass("btn-success").removeClass("btn-default");
        });
        
        $("#btn_save_grades").click(function(){
            var ids = "";
            $("#tbl_grades .btn-success").each(function(index){
                if(!index)
                    ids = $(this).attr("rel");
                else
                    ids = ids + ":" + $(this).attr("rel");
            });
            $.post('/admin_sports/ajax_update_bg_grades', {id: $("#config_id").val(), answerHash: ids}, function(data){
                if(data.success)
                    alert("Answers Saved!");
            }, 'json');
        });
    });
</script>
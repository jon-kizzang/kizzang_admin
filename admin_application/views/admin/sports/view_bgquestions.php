<div id="message"></div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>            
            <th>Questions</th>
            <th>Answers</th>
            <th>Edit</th> 
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($questions as $question) : ?>
        <tr id="tr_<?= $question->id; ?>">            
            <td><?= $question->question; ?></td>
            <td><?= $question->answers; ?></td>            
            <td><a href="/admin_sports/add_bg_question/<?= $question->id?>" class="btn btn-primary">Edit</a></td>
            <td><button type="button" rel="<?= $question->id; ?>" class="btn btn-danger delete-question">Delete</button></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50});
                
                $("#sel_game_type").change(function(){
                    window.location = "/admin_sports/" + $(this).val();
                });
                
                $(".delete-question").click(function(){
                    var id = $(this).attr('rel');
                    $.post("/admin_sports/ajax_delete_bg_question", {question_id: id}, function(data){
                        if(data.success)
                            $("#tr_" + id).remove();
                    }, 'json');
                });
        } );
</script>
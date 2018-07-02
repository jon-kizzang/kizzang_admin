<div id="message"></div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>            
            <th>Name</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th># of Questions</th>
            <th>Edit</th> 
            <th>Delete</th>
            <th>Grade</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($configs as $config) : ?>
        <tr id="tr_<?= $config->id; ?>">  
            <td><?= $config->name; ?></td>
            <td><?= $config->startDate; ?></td>
            <td><?= $config->endDate; ?></td>            
            <td><?= $config->questions; ?></td>
            <td><a href="/admin_sports/add_bg_config/<?= $config->id?>" class="btn btn-primary">Edit</a></td>
            <td><button type="button" rel="<?= $config->id; ?>" class="btn btn-danger delete-bgconfig">Delete</button></td>
            <td><a href="/admin_sports/grade_bg_config/<?=$config->id?>" class="btn btn-primary" data-toggle="modal" data-target="#modal">Grade</a></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50});
                                
                $(".delete-bgconfig").click(function(){
                    var id = $(this).attr('rel');
                    var r = confirm("Are you sure you want to delete this?");
                    if(r)
                    {
                        $.post("/admin_sports/ajax_delete_bg_config", {question_id: id}, function(data){
                            if(data.success)
                                $("#tr_" + id).remove();
                        }, 'json');
                    }
                });
        });
</script>
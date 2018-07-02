<table id="show_games" class="table table-striped">
    <thead>
        <tr>        
            <th>ID</th>
            <th>Name</th>
            <th>Date</th>            
            <th>Status</th> 
            <th>Edit</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($crons as $cron) : ?>
        <tr>
            <td><?= strtotime($cron->schedule_date); ?></td>
            <td><?= $cron->name; ?></td>
            <td><?= date('l, F j (h:i:s A)', strtotime($cron->schedule_date)); ?></td>            
            <td><?= $cron->status; ?></td>
            <td><button class="btn btn-activate <?php if($cron->is_active) echo "btn-danger"; else echo "btn-success"; ?>" recid="<?=$cron->id . "-" . strtotime($cron->schedule_date); ?>" rel="<?=$cron->is_active; ?>"><?php if($cron->is_active): ?>Deactivate<?php else : ?>Activate<?php endif; ?></button></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50});
                $(document).on('click',".btn-activate", function(){
                    var state = parseInt($(this).attr("rel"));
                    var id = $(this).attr("recid");
                    var obj = $(this);
                    $.get("/admin/ajax_cron_sched_active/" + id, {}, function(data){
                        if(data.success)
                        {
                            if(state)
                            {
                                obj.attr("rel", 0);
                                obj.removeClass("btn-danger");
                                obj.addClass("btn-success");
                                obj.html("Activate");
                            }
                            else
                            {
                                obj.attr("rel", 1);
                                obj.removeClass("btn-success");
                                obj.addClass("btn-danger");
                                obj.html("Deactivate");
                            }
                        }
                    }, 'json');
                });
        } );
</script>
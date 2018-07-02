<div class="well">
    <button class="btn btn-primary" id="update_acls">Update ACLS List</button>
</div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>            
            <th>Link</th>
            <th>Administrator</th>
            <th>Sports</th>  
            <th>Sweepstakes</th>
            <th>Scratch Cards</th>
            <th>Slots</th>
            <th>Sponsors</th>
            <th>Reports</th>
            <th>Payments</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($acls as $acl) : ?>
        <tr>
            <td><?= $acl->id; ?></td>
            <td><?= $acl->name; ?></td>
            <td><?= $acl->link ?></td>            
            <td id="<?= $acl->id . "_1" ;?>"><input type="checkbox" value="1" class="acl-check" rel="<?= $acl->id; ?>_1" <?php if($acl->Administrator) : ?>checked="checked"<?php endif; ?>/></td>            
            <td id="<?= $acl->id . "_3" ;?>"><input type="checkbox" value="1" class="acl-check" rel="<?= $acl->id; ?>_3" <?php if($acl->Sports) : ?>checked="checked"<?php endif; ?>/></td>
            <td id="<?= $acl->id . "_4" ;?>"><input type="checkbox" class="acl-check" rel="<?= $acl->id; ?>_4" <?php if($acl->Sweepstakes) : ?>checked="checked"<?php endif; ?>/></td>  
            <td id="<?= $acl->id . "_5" ;?>"><input type="checkbox" class="acl-check" rel="<?= $acl->id; ?>_5" <?php if($acl->ScratchCards) : ?>checked="checked"<?php endif; ?>/></td>
            <td id="<?= $acl->id . "_6" ;?>"><input type="checkbox" class="acl-check" rel="<?= $acl->id; ?>_6" <?php if($acl->Slots) : ?>checked="checked"<?php endif; ?>/></td>
            <td id="<?= $acl->id . "_7" ;?>"><input type="checkbox" class="acl-check" rel="<?= $acl->id; ?>_7" <?php if($acl->Sponsors) : ?>checked="checked"<?php endif; ?>/></td>
            <td id="<?= $acl->id . "_2" ;?>"><input type="checkbox" class="acl-check" rel="<?= $acl->id; ?>_2" <?php if($acl->Reports) : ?>checked="checked"<?php endif; ?>/></td>
            <td id="<?= $acl->id . "_8" ;?>"><input type="checkbox" class="acl-check" rel="<?= $acl->id; ?>_8" <?php if($acl->Payments) : ?>checked="checked"<?php endif; ?>/></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script>
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 25, order: [[ 0, "asc" ]]});   
                $("body").on('change', ".acl-check", function(){
                    var id = $(this).attr("rel");
                    var checked = $(this).is(':checked');
                    $.post("/admin/ajax_change_acl", {id: id, checked: checked}, function(data){
                        if(data.success)
                        {                            
                            if(checked)
                                $("#" + id).css("background-color", "#00FF00");
                            else
                                $("#" + id).css("background-color", "#FF0000");
                            setTimeout('$("#' + id + '").css("background-color", "#FFFFFF")', 1000);
                        }
                        else
                        {
                            alert("Error changing ACL");
                        }
                    }, 'json');
                });
                $("#update_acls").click(function(){
                    $.get('/admin/ajax_get_acls', {}, function(data){
                        if(data.success)
                        {
                            alert(data.count + " Records added.");
                            location.reload();
                        }
                    }, 'json');
                })
        } );
</script>
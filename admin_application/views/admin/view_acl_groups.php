<div id="show_message"></div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>                        
            <th>Name</th>
            <th>Email</th>                        
            <th>Current Groups</th> 
            <th>Add</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($players as $player) : ?>
        <?php if(!isset($player->firstName)) continue; ?>
        <tr>                    
            <td><?= $player->firstName . " " . $player->lastName; ?></td>
            <td><?= $player->email; ?></td>
            <td>
                <?php if(isset($player->groups)) : ?>
                <ul>
                    <?php foreach($player->groups as $group) : ?>
                    <li id="li_<?= $group; ?>_<?= $player->id; ?>" style="height: 25px;"><?= $groups[$group]; ?><button class="btn-danger btn-delete" style="margin-right: 10px; float: right;" rel="<?= $group; ?>" alt="<?= $player->id; ?>">Delete</button></li>
                    <?php endforeach;?>
                </ul>
                <?php else : ?>
                None
                <?php endif; ?>
            </td>                   
            <td>
                <select id="sel_<?= $player->id; ?>">
                    <?php foreach($groups as $id => $group) : ?>
                    <?php if(isset($player->groups) && in_array($id, $player->groups)) : ?>
                    <?php else : ?>
                    <option value="<?= $id; ?>"><?= $group; ?></option>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-primary add-acl" rel="<?= $player->id; ?>">Add User to Group</button>
            </td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50});
                $(document).on("click", ".add-acl", function(){
                    var id = $(this).attr("rel");
                    var group = $("#sel_" + id).val();
                    $.post("/admin/ajax_add_acl_group", {player_id: id, group_id: group}, function(data){
                        if(data.success)
                        {
                            $("#show_message").html("ACL Added").addClass("alert alert-success");
                            $('html,body').scrollTop(0);
                            setTimeout("location.reload();", 1000);
                        }
                        else
                        {
                            alert("ACL Assignment Failed!");
                        }
                    }, 'json');
                });
                $(document).on("click",".btn-delete", function(){
                    var player_id = $(this).attr("alt");
                    var group_id = $(this).attr("rel");
                    $.get("/admin/ajax_delete_acl_group/" + player_id + "/" + group_id, {}, function(data){
                        if(data.success)
                        {
                            $("#li_" + group_id + "_" + player_id).remove();
                        }
                    }, 'json');
                });
        } );                        
        
</script>
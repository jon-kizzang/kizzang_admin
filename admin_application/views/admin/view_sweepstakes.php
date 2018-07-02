<style>
    #mask {
            opacity: .3;
            background-color: #000;
            display: none;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            position: fixed;
            z-index: 1000;
    }
    
    #mask_message {
            z-index: 1001;
            height: 100px;
            width: 200px;
            background-color: #FFF;
            position: fixed;
            display: none;
            padding: 40px;
    }
</style>
<div class="well">
    <label>Select Time Frame:</label>
    <input type="radio" name="time" class="time" value="past" <?php if($type == "past") echo "checked";?> style="margin-left: 20px;"/> Past
    <input type="radio" name="time" class="time" value="current" <?php if($type == "current") echo "checked";?> style="margin-left: 20px;"/> Current
    <input type="radio" name="time" class="time" value="future" <?php if($type == "future") echo "checked";?> style="margin-left: 20px;"/> Future
</div>
<div id="message"></div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Dates</th>
            <th>Image</th>
            <th>Title Image</th>
            <th>Type</th>
            <th># of Entries</th>
            <th>Edit</th>
            <th>Delete</th>           
        </tr>
    </thead>
    <tbody>
            <?php foreach($sweepstakes as $sweepstake) : ?>
        <tr>
            <td><?= $sweepstake->name; ?></td>
            <td><?php if(strlen($sweepstake->description) > 50) echo substr($sweepstake->description, 0, 50) . "..."; else echo $sweepstake->description; ?></td>
            <td><?= $sweepstake->dates; ?></td>
            <td><img style="max-width: 240px; max-height: 240px;" src="<?= $sweepstake->imageUrl; ?>"/></td>
            <td><img style="max-width: 240px; max-height: 240px;" src="<?= $sweepstake->titleImageUrl; ?>"/></td>
            <td><?= $sweepstake->sweepstakeType; ?></td>
            <td><?= $sweepstake->num_entries; ?></td>
            <td><a href="/admin_sweepstakes/edit/<?= $sweepstake->id?>" class="btn btn-primary">Edit</a></td>
            <td><?php if($sweepstake->num_entries) : ?>N/A<?php else : ?><a href="javascript:void(0);" rel="<?= $sweepstake->id?>" class="btn btn-danger delete-sweepstakes">Delete</a><?php endif; ?></td>            
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<div id="mask">
</div>
<div id="mask_message">Generating Cards</div>
<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 0, "asc" ]]});
                
                $(".time").change(function(){
                    var type = $(".time:checked").val();
                    location.href = "/admin_sweepstakes/" + type;
                });
                
               $(".delete-sweepstakes").click(function(){
                   var r = confirm("Are you sure you want to delete this Sweepstakes?");
                   if(r)
                   {
                        var id = $(this).attr('rel');
                        $.get("/admin_sweepstakes/delete/" + id, {}, function(data)
                        {
                            if(data.success)
                            {
                                $("#message").html("Sweepstakes Deleted").addClass("alert alert-success");
                                $('html,body').scrollTop(0);
                                setTimeout("window.location.reload()", 1000);
                            }
                        }, 'json');
                    }
               });
        } );
</script>
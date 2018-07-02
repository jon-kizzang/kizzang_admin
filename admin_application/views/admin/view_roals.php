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
            padding: 20px;
            text-align: center;
    }
    
    #frm_power_rank div {
        margin-left: 10px;
    }
</style>
<div class="well">
    <form class="form-inline" id="frm_power_rank" method="POST" role="form">
    <button class="btn btn-primary" id="update_powerranks">Update Power Rankings</button>
    <div class="checkbox">
    <label>
        <input type="checkbox" name="pr_type[]" value="Pro Hockey"> Pro Hockey
    </label>
    </div>
    <div class="checkbox">
    <label>
        <input type="checkbox" name="pr_type[]" value="Pro Football"> Pro Football
    </label>
    </div>
    <div class="checkbox">
    <label>
        <input type="checkbox" name="pr_type[]" value="Pro Baseball"> Pro Baseball
    </label>
    </div>
    <div class="checkbox">
    <label>
        <input type="checkbox" name="pr_type[]" value="Pro Basketball"> Pro Basketball
    </label>
    </div>
    <div class="checkbox">
    <label>
        <input type="checkbox" name="pr_type[]" value="Pro Soccer"> Pro Soccer
    </label>
    </div>
    <div class="checkbox">
    <label>
        <input type="checkbox" name="pr_type[]" value="College Basketball"> College Basketball
    </label>
    </div>
    <div class="checkbox">
    <label>
        <input type="checkbox" name="pr_type[]" value="College Football"> College Football 
    </label>
    </div>
    </form>
    <br/><br/>    
</div>
<div id="message"></div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>       
            <th>ID</th>
            <th>Card Date</th>
            <th>Theme</th>
            <th># of Questions</th>
            <th># of Questions Answered</th>
            <th>Edit</th>             
        </tr>
    </thead>
    <tbody>
            <?php foreach($configs as $config) : ?>
        <tr>       
            <td><?= $config->id?></td>
            <td><?= $config->cardDate; ?></td>
            <td><?= $config->theme; ?></td>
            <td><?= $config->cnt; ?></td>
            <td><?= $config->qsum; ?></td>
            <td><a href="/admin/edit_roal/<?= $config->id?>" class="btn btn-primary">Edit</a></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<div id="mask">
</div>
<div id="mask_message">Updating Power Rankings</div>
<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 1, "desc" ]]});
                                                               
                $("#update_powerranks").click(function(e){
                    e.preventDefault();
                    $("#mask").show();
                    $("#mask_message").css({left: window.innerWidth / 2 - 100, top: window.innerHeight / 2 -50}).show();
                    $(this).prop('disabled', true);
                    $.post('/admin_sports/update_powerranks', $("#frm_power_rank").serialize(), function(data){
                            $("#mask").hide();
                            $("#mask_message").html("Update Completed!");
                            var command = '$("#mask_message").fadeOut();';
                            setTimeout(command, 1000);
                            $("#update_powerranks").prop('disabled', false);
                    }, 'json');
                });
        } );               
</script>
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
            top: 0px;
            padding: 20px;                    
        }
        .radio-type {
            margin-left: 15px !important;
            margin-right: 5px !important;
        }    
</style>
<div class="well">
    <form class="form-inline">
        <label>Select Type:</label>
        <input class="radio-type" type="radio" name="type" <?php if($type == "Claimed") echo 'checked="checked"'; ?> value="Claimed"/>Claimed
        <input class="radio-type" type="radio" name="type" <?php if($type == "Document") echo 'checked="checked"'; ?> value="Document"/>Document
        <input class="radio-type" type="radio" name="type" <?php if($type == "Expired") echo 'checked="checked"'; ?> value="Expired"/>Expired
        <input class="radio-type" type="radio" name="type" <?php if($type == "New") echo 'checked="checked"'; ?> value="New"/>New
    </form>
</div>
<div id="message"></div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>Name</th>            
            <th>Address</th>
            <th>Serial Number</th>
            <th>Prize Name</th>
            <th>Prize Amount</th>
            <th>Game Type</th>            
            <th>Date</th>
            <th>Expiration Date</th>
            <th>Time Left</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php $total_money = 0; ?>
            <?php foreach($winners as $winner) : ?>
        <tr id="tr_<?= $winner->id; ?>">
            <td><?= $winner->player['firstName'] . " " . $winner->player['lastName'] . " (" . $winner->player_id . ")"; ?></td>
            <td><?= $winner->player['address'] . ", " . $winner->player['city'] . "," . $winner->player['state'] . " " . $winner->player['zip']?></td>
            <td><?= $winner->serial_number; ?></td>
            <td><?= $winner->prize_name; ?></td>
            <td><?= $winner->amount; ?></td>
            <td><?= $winner->game_type; ?></td>            
            <td><?= $winner->createdDate; ?></td>
            <td><?= $winner->expirationDate; ?></td>
            <td><?php if(strtotime("now") < strtotime($winner->expirationDate)) : ?>
                <?php 
                $time = strtotime($winner->expirationDate) - strtotime("now");
                $days = $hours = $minutes = 0;
                if($time > 86400)
                {
                    $days = floor($time / 86400);
                    $time = $time % 86400;
                }
                if($time > 3600)
                {
                    $hours = floor($time / 3600);
                    $time = $time % 3600;
                }
                if($time > 60)
                {
                    $minutes = floor($time / 60);
                    $time = $time % 60;
                }
                print $days . " Day(s) " . $hours . " Hour(s) " . $minutes . " Minute(s)";
                ?>
                <?php else: ?>
                Expired
                <?php endif; ?>
            </td>
            <td><a href="/admin/validate_winner/<?= $winner->id; ?>" class="btn btn-success">Validate</button></td>
            <?php $total_money += $winner->amount; ?>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<div id="mask">
</div>
<div id="mask_message">Generating Cards</div>
<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 100, order: [[ 6, "desc" ]]});
                $(".radio-type").change(function(){
                   location.href = "/admin/view_winners/" + $(this).val(); 
                });
        });
</script>

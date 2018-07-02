<div class="well">
    <label>Names:</label>
    <select name="name" id="names">
        <option value="0">All People (Top 50)</option>
        <?php foreach($names as $name) : ?>
        <option value="<?= $name->id; ?>" <?php if($name->id == $playerId): ?> selected=""<?php endif; ?>><?= $name->firstName . " " . $name->lastName . " (" . $name->id . ")"; ?></option>
        <?php endforeach ;?>
    </select>
</div>
<div class="panel panel-primary">
    <div class="panel-heading"><?= $config->serialNumber . ' - ' . date('l F j, Y', strtotime($config->cardDate)); ?></div>
    <div class="panel-body">
        <?php foreach($cards as $card) : ?>
        <div class="col-md-6">
            <div class="well"><?= $card['title']; ?></div>
            <table class="table table-condensed table-bordered">
                <tr>
                    <th>Win/Loss</th>
                    <th>Team1</th>
                    <th>Team2</th>
                    <th>O/U</th>
                </tr>
                <?php foreach($card['cards'] as $key => $value) : ?>
                <tr class="<?php if(!isset($answers[$key]) || !$answers[$key]->winner) echo "warning"; 
                    elseif(isset($answers[$key]) && $answers[$key]->winner == $value) echo "success"; 
                    else echo "danger"; ?>">
                    <td><?php if(!isset($answers[$key]) || !$answers[$key]->winner) echo "Unknown"; 
                    elseif(isset($answers[$key]) && $answers[$key]->winner == $value) echo "Win"; 
                    else echo "Loss"; ?></td>
                    <?php if(isset($answers[$key])) : ?>
                    <td><?= $answers[$key]->team1Name;?> <?php if($value == $answers[$key]->team1) echo "(*)"; ?></td>
                    <td><?= $answers[$key]->team2Name;?> <?php if($value == $answers[$key]->team2) echo "(*)"; ?></td>
                    <td><?php if($answers[$key]->overUnderScore) : ?> <?php if($value == $answers[$key]->team1) echo "Under"; else echo "Over"; ?> <?= $answers[$key]->overUnderScore;?><?php endif; ?></td>
                    <?php else :?>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>N/A</td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>  
        <?php endforeach; ?>
    </div>
</div>
<script>
    $("#names").change(function(){
        location.href = "/admin_sports/view_parlay_cards/<?= $config->parlayCardId; ?>/" + $(this).val();
    });
</script>
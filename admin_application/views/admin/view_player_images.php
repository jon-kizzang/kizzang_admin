<style>
    .player-image
    {
        float: left;
        height: 150px;
        width: 150px;
    }
</style>
<?php foreach($players as $player) : ?>
<img class="player-image" src="https://graph.facebook.com/v2.2/<?=$player['fbid']; ?>/picture?width=140&height=140" style="margin-bottom: 30px;" title="<?= $player['first_name'] . " " . $player['last_name'] . " (" . $player['id'] .  ") - " . $player['city'] . ", " . $player['state']; ?> "/>
<?php endforeach; ?>

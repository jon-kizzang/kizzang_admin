<?php if($places) :?>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Rank</th>
            <th>Prize Amount</th>                    
        </tr>
    </thead>
    <?php foreach($places as $place) : ?>
    <tr>
        <td><?= $place->rank?></td>
        <td><?= $place->prize?></td>        
    </tr>
    <?php endforeach;?>
</table>
<?php endif; ?>
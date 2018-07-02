<table class="table table-striped table-condensed table-responsive">
    <thead>
        <tr>
            <?php foreach($audits[0] as $key => $value) : ?>
            <td><?= ucwords(str_replace("_", " ", $key)); ?></td>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>                
        <?php foreach($audits as  $row) : ?>
        <tr>
            <?php foreach($row as $name => $field) : ?>
            <td>
                <?= $field; ?>
            </td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>                
    </tbody>
</table>
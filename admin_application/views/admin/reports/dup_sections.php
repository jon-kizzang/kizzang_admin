<?php foreach($recs as $rec) : ?>
<div class="panel panel-primary">
    <div class="panel-heading"><?php if(count($duplicates)) : ?>Duplicate <?php foreach($duplicates as $duplicate) echo ucwords(str_replace("_", " ", $duplicate)) . ", "; ?><?php else : ?>Results<?php endif; ?></div>
    <div class="panel-body" id="results">
        <table class="table table-striped table-condensed table-responsive">
            <thead>
                <tr>
                    <?php foreach($rec[0] as $key => $value) : ?>
                    <td><?= ucwords(str_replace("_", " ", $key)); ?></td>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>                
                <?php foreach($rec as  $row) : ?>
                <tr>
                    <?php foreach($row as $name => $field) : ?>
                    <td>
                        <?php switch($name) {
                            case 'id': echo "<a href='/admin/edit_player/$field' target='_blank'>View Player</a>"; break;
                            case 'is_deleted':
                            case 'is_suspended': echo $field ? "Yes" : "No"; break;
                            case 'ip_address': echo long2ip($field); break;
                            default: echo $field;
                        }?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>                
            </tbody>
        </table>
    </div>
</div>
<?php endforeach; ?>
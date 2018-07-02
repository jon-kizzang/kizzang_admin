<div class="well">
    <a class="btn btn-primary" href="/admin/add_config">Add Config Setting</a>
</div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>            
            <th>Main Type</th>
            <th>Sub Type</th>
            <th>Data Type</th>
            <th>Info</th>
            <th>Created</th>
            <th>Updated</th>
            <th>Edit</th>            
        </tr>
    </thead>
    <tbody>
            <?php foreach($configs as $config) : ?>
        <tr>
            <td><?= $config->id; ?></td>
            <td><?= $config->main_type; ?></td>            
            <td><?= $config->sub_type; ?></td>
            <td><?= $config->data_type; ?></td>
            <td>
                <ul>
                 <?php if($config->data_type == "File") : ?>
                    <li><i>File Contents</i></li>
                <?php elseif(is_array($config->info)) : ?>
                    <?php foreach($config->info as $key => $value) : ?>
                        <li><?= $key; ?> -> <?php if(is_array($value)) print json_encode($value); else print $value; ?></li>
                    <?php endforeach;?>                    
                <?php else : ?>
                        <?= $config->info; ?>
                <?php endif; ?>
                </ul>
            </td>
            <td><?=date("D M j, Y (h:i:s A)", strtotime($config->created)); ?></td>            
            <td><?=date("D M j, Y (h:i:s A)", strtotime($config->updated)); ?></td>
            <td><a class="btn btn-primary" href="/admin/add_config/<?= $config->id; ?>">Edit</a></td>            
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 0, "asc" ]]});                
        } );
</script>
<div id="message"></div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>            
            <th>Short Title</th>
            <th>Long Title</th>
            <th>Image</th>
            <th>Chedda</th>
            <th>Amount</th>
            <th>Edit</th>            
        </tr>
    </thead>
    <tbody>
            <?php foreach($storeItems as $storeItem) : ?>
        <tr>            
            <td><?= $storeItem->shortTitle; ?></td>
            <td><?= $storeItem->longTitle; ?></td>
            <td><img src="<?= $storeItem->imageUrl; ?>" height="200px;"/></td>
            <td><?= $storeItem->chedda; ?></td>
            <td><?= $storeItem->amount; ?></td>
            <td><a href="/admin/add_store_item/<?= $storeItem->id?>" class="btn btn-primary">Edit</a></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 1, "desc" ]]});                
        } );
</script>
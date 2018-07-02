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
<div id="message"></div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Type</th>
            <th>SubType</th>
            <th>Image</th>            
            <th>Edit</th>            
        </tr>
    </thead>
    <tbody>
            <?php foreach($tiles as $tile) : ?>
        <tr>
            <td><?= $tile->id; ?></td>
            <td><?= $tile->name; ?></td>
            <td>
                <select id="type_<?= $tile->id; ?>">
                    <?php foreach($types as $type) : ?>
                    <option value="<?= $type; ?>" <?php if($type == $tile->type) echo 'selected="selected"'; ?>><?= $type; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <select id="sub_type_<?= $tile->id; ?>">
                    <?php foreach($sub_types as $type) : ?>
                    <option value="<?= $type; ?>" <?php if($type == $tile->sub_type) echo 'selected="selected"'; ?>><?= $type; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><div style="background: url('/images/citySquaresLargeTileAtlas.png') -<?= $tile->x?>px -<?= $tile->y?>px; height:<?= $tile->height; ?>px; width:<?= $tile->width;?>px;"></div></td>            
            <td>
                <button rel="<?= $tile->id; ?>" class="update-rec">Update</button>
                <a href="/boards/edit_tile/<?= $tile->id?>" class="btn btn-primary">Edit</a>
            </td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<div id="mask">
</div>
<div id="mask_message">Generating Cards</div>
<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50});
                
                $(document).on('click', '.update-rec', function(){
                    var id = $(this).attr("rel");
                    var data = {id: id,
                        type: $("#type_" + id).val(),
                        sub_type: $("#sub_type_" + id).val()};
                    $.post("/boards/ajax_update_tile", data, function(d){
                        if(d.success)
                        {
                            alert("Updated");
                        }
                    }, 'json');
                });
        });
</script>
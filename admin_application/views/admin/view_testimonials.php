<div id="message"></div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>        
            <th>Name</th>
            <th>State</th>
            <th>Prize</th>
            <th>Testimonial</th>
            <th>Win Date</th>
            <th>Image</th>                      
            <th>Edit</th>            
        </tr>
    </thead>
    <tbody>
            <?php foreach($testimonials as $testimonial) : ?>
        <tr>            
            <td><?= $testimonial->name; ?></td>
            <td><?= $testimonial->state; ?></td>
            <td><?= $testimonial->description; ?></td>
            <td><?= $testimonial->testimonial; ?></td>
            <td><?= $testimonial->winDate; ?></td>
            <td><img src="<?= str_replace("https://d1vksrhd974otw.cloudfront.net/", "https://kizzang-campaigns.s3.amazonaws.com/", $testimonial->image); ?>" height="200px;"/></td>            
            <td><a href="/admin/add_testimonial/<?= $testimonial->id?>" class="btn btn-primary">Edit</a></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 1, "desc" ]]});                
        } );
</script>
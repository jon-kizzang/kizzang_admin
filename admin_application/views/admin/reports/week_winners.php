<script src="/js/tableTools.js"></script>
<link href="/css/tableTools.css" rel="stylesheet">
<h1>Winners from Last Week</h1><br/><br/>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>            
            <th>Player ID</th>
            <th>Amount</th>
            <th>First Name</th>
            <th>Last Name</th>            
            <th>City</th>    
            <th>State</th>
            <th>Phone</th>            
        </tr>
    </thead>
    <tbody>
            <?php foreach($winners as $winner) : ?>
        <tr>
            <td><?= $winner->player_id; ?></td>
            <td><?= $winner->amount; ?></td>
            <td><?= $winner->firstName; ?></td>
            <td><?= $winner->lastName; ?></td>
            <td><?= $winner->city; ?></td>
            <td><?= $winner->state; ?></td>
            <td><?= $winner->phone; ?></td>            
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({
                    pageLength: 10, 
                    order: [[ 0, "asc" ]],
                    dom: 'T<"clear">lfrtip',
                    tableTools: {
                        sSwfPath: "/swf/copy_csv_xls_pdf.swf"
                }});
                
                $("#num_recs").change(function(){
                    var order_by = $('input[name=order_by]:checked').val();
                    location.href = "/admin_reports/top_ten/" + $(this).val() + "/" + order_by;
                });
                
                $(".order-by").click(function(){
                    var count = $("#num_recs").val();
                    var order_by = $('input[name=order_by]:checked').val();
                    location.href = "/admin_reports/top_ten/" + count + "/" + order_by;
                })
        });
</script>
<script src="/js/tableTools.js"></script>
<link href="/css/tableTools.css" rel="stylesheet">

<table id="show_games" class="table table-striped">
    <thead>
        <tr>            
            <th>ID</th>
            <th>WinDate</th>
            <th>WinConfirmed</th>
            <th>PlayerId</th>
            <th>Name</th>
            <th>Address</th>
            <th>City</th>
            <th>State</th>
            <th>Zip</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Paypal Email</th>
            <th>SerialNumber</th>    
            <th>Entry</th>
            <th>PrizeAmount</th>
            <th>PrizeName</th>
            <th>PayPalPayDate</th>
            <th>PayPalPaymentStatus</th>
            <th>YTD</th>
            <th>NeedTaxDocuments</th>
            <th>HaveTaxDocuments</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($wins as $key => $win) : ?>
        <tr>
            <td><?= $win->id; ?></td>
            <td><?= date("n/j/y H:i", strtotime($win->winDate)); ?></td>
            <td><?= $win->winConfirmed ? date("n/j/y H:i", strtotime($win->winConfirmed)) : ""; ?></td>
            <td><?= $win->playerId; ?></td>
            <td><?= $win->name; ?></td>
            <td><?= $win->address; ?></td>
            <td><?= $win->city; ?></td>
            <td><?= $win->state; ?></td>
            <td><?= $win->zip; ?></td>
            <td><?= $win->phone; ?></td>
            <td><?= $win->email; ?></td>
            <td><?= $win->payPalEmail; ?></td>
            <td><?= $win->serialNumber; ?></td>
            <td><?= $win->entry; ?></td>
            <td><?= $win->prizeAmount; ?></td>
            <td><?= $win->prizeName; ?></td>
            <td><?= $win->payPalPayDate; ?></td>
            <td><?= $win->payPalPaymentStatus; ?></td>
            <td><?= $win->ytd; ?></td>
            <td><?php if($win->ytd >= 600) echo "Y"; else echo "N"; ?></td>
            <td><?= $win->HaveTaxDocuments; ?></td>
            <td><?= $win->status; ?></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({
                    pageLength: 50, 
                    order: [[ 0, "desc" ]],
                    dom: 'T<"clear">lfrtip',
                    tableTools: {
                        sSwfPath: "/swf/copy_csv_xls_pdf.swf"
                }});
                
                $("#num_recs").change(function(){
                    location.href = "/admin_reports/top_ten/" + $(this).val();
                });
        });
</script>
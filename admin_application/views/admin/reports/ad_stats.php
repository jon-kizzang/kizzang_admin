<div class="well">
    <label>Start Date: </label>
    <input style="margin-left: 20px;" id="StartDate" value="<?= $startDate; ?>"/>
    <label style="margin-left: 20px;">End Date: </label>
    <input style="margin-left: 20px;" id="EndDate" value="<?= $endDate; ?>"/>
</div>
<h3>Ad Overview</h3>
<table class="table table-bordered table-striped">
     <thead>
            <tr>            
                <th>Unique People</th>
                <th>Type</th>
                <th>Status</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>  
            <?php foreach($ad_overview as $ad) : ?>
            <tr>                    
                <td><?= $ad->unq_people; ?></td>
                <td><?= $ad->type; ?></td>
                <td><?= $ad->status; ?></td>
                <td><?= $ad->cnt; ?></td>                
            </tr>
            <?php endforeach; ?>
        </tbody>
</table>

<h3>Ads by Game</h3>
<table class="table table-bordered table-striped">
     <thead>
            <tr>            
                <th>Game Type</th>
                <th>Viewed</th>
                <th>Clicked</th>
                <th>Closed</th>
                <th>Empty</th>
                <th>Error</th>
            </tr>
        </thead>
        <tbody>  
            <?php foreach($ad_games as $ad) : ?>
            <tr>    
                <td><?= $ad->gameType; ?></td>
                <td><?= $ad->Viewed; ?></td>
                <td><?= $ad->Clicked; ?></td>
                <td><?= $ad->Closed; ?></td>
                <td><?= $ad->Empty; ?></td>
                <td><?= $ad->Error; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
</table>

<h3>Ads Sumary</h3>
<table class="table table-bordered table-striped">
     <thead>
            <tr>     
                <th>Type</th>
                <th>Guest</th>
                <th>Registered</th>
                <th>Online</th>
                <th>iOS</th>
                <th>Android</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>  
            <tr>    
                <td>Average per User</td>
                <td><?= number_format($ad_summary->sum_guest / $ad_summary->cnt_guest, 1); ?></td>
                <td><?= number_format($ad_summary->sum_user / $ad_summary->cnt_user, 1); ?></td>
                <td><?= number_format($ad_summary->sum_online / $ad_summary->cnt_online, 1); ?></td>
                <td><?= number_format($ad_summary->sum_ios / $ad_summary->cnt_ios, 1); ?></td>
                <td><?= number_format($ad_summary->sum_android / ($ad_summary->cnt_android ? $ad_summary->cnt_android : 1), 1); ?></td>
                <td><?= number_format($ad_summary->sum_total / $ad_summary->cnt_total, 1); ?></td>
            </tr>
            <tr>    
                <td>Totals (<?= $ad_summary->cnt_total; ?> Users)</td>
                <td><?= number_format($ad_summary->sum_guest, 0); ?></td>
                <td><?= number_format($ad_summary->sum_user, 0); ?></td>
                <td><?= number_format($ad_summary->sum_online, 0); ?></td>
                <td><?= number_format($ad_summary->sum_ios, 0); ?></td>
                <td><?= number_format($ad_summary->sum_android, 0); ?></td>
                <td><?= number_format($ad_summary->sum_total, 0); ?></td>
            </tr>
        </tbody>
</table>
<script>
$(document).ready(function() {
    $( "#StartDate" ).datepicker({
            dateFormat: "yy-mm-dd",
            setDate: "<?= $startDate; ?>", 
            changeMonth: true,
            numberOfMonths: 3,
            maxDate: "<?= $endDate; ?>",
            onClose: function( selectedDate ) {
                $( "#EndDate" ).datepicker( "option", "minDate", selectedDate );
                location.href = '/admin_reports/ad_stats/' + $("#StartDate").val() + "/" + $("#EndDate").val();
            }
        });

        $( "#EndDate" ).datepicker({
            dateFormat: "yy-mm-dd",
            setDate: "<?= $endDate; ?>",
            changeMonth: true,
            numberOfMonths: 3,
            minDate: "<?= $startDate; ?>",
            onClose: function( selectedDate ) {
                $( "#StartDate" ).datepicker( "option", "maxDate", selectedDate );
                location.href = '/admin_reports/ad_stats/' + $("#StartDate").val() + "/" + $("#EndDate").val();
            }
        });          
});
</script>
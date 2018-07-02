<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
<h1>Dashboard</h1>
<div class="well-lg">
    <form id="frmDashBoard">
    <label>From:</label>
    <input type="text" id="StartDate" name="startDate" value="<?= $startDate; ?>"/>
    <label> To </label>
    <input type="text" id="EndDate" name="endDate" value="<?= $endDate; ?>"/>
    <br/><br/>
    <label>User Type:</label>
    <?php foreach($userTypes as $userType) : ?>
    <input type="radio" name="userType" style="margin-left: 10px; margin-right: 5px;" value="<?= $userType; ?>" <?php if($userType == "All") echo 'checked=""'; ?> /><?= $userType; ?>
    <?php endforeach; ?>
    <br/>
    <label>Login Type:</label>
    <?php foreach($loginTypes as $loginType) : ?>
    <input type="radio" name="loginType" style="margin-left: 10px; margin-right: 5px;" value="<?= $loginType; ?>" <?php if($loginType == "All") echo 'checked=""'; ?> /><?= $loginType; ?>
    <?php endforeach; ?>
    <br/>
    <label>Login Source:</label>
    <?php foreach($loginSources as $loginSource) : ?>
    <input type="radio" name="loginSource" style="margin-left: 10px; margin-right: 5px;" value="<?= $loginSource; ?>" <?php if($loginSource == "All") echo 'checked=""'; ?> /><?= $loginSource; ?>
    <?php endforeach; ?>
    <br/>
    <label>Mobile Type:</label>
    <?php foreach($mobileTypes as $mobileType) : ?>
    <input type="radio" name="mobileType" style="margin-left: 10px; margin-right: 5px;" value="<?= $mobileType; ?>" <?php if($mobileType == "All") echo 'checked=""'; ?> /><?= $mobileType; ?>
    <?php endforeach; ?>
    <br/><br/>
    <button class="btn btn-success" id="btnQuery">Get Information</button>
    </form>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">Users</div>
    <div class="panel-body">
        <div id="user_info" style="height: 250px;"></div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">Retention</div>
    <div class="panel-body">
        <div id="ret_info" style="height: 250px;"></div>
    </div>
</div>

<script>
$(document).ready(function() {
    
    $("#btnQuery").click(function(e){
        e.preventDefault();
        getData();
    });
    
    $( "#StartDate" ).datepicker({
        dateFormat: "yy-mm-dd",
        setDate: "<?= $startDate; ?>", 
        changeMonth: true,
        numberOfMonths: 2,
        maxDate: "<?= $endDate; ?>",
        onClose: function( selectedDate ) {
            $( "#EndDate" ).datepicker( "option", "minDate", selectedDate );
        }
    });

    $( "#EndDate" ).datepicker({
        dateFormat: "yy-mm-dd",
        setDate: "<?= $endDate; ?>",
        changeMonth: true,
        numberOfMonths: 2,
        minDate: "<?= $startDate; ?>",
        onClose: function( selectedDate ) {
            $( "#StartDate" ).datepicker( "option", "maxDate", selectedDate );
        }
    });          
});

    var user_info = Morris.Line({  
    element: 'user_info',  
    data: [
        <?php $i = 0; ?>
        <?php foreach($user_info as $key => $row) : ?>
                    {date: '<?= $row['date']; ?>', dau: '<?= $row['dau']; ?>', newuser: '<?= $row['newuser']; ?>', conversion: '<?= $row['conversion']; ?>'}<?php if($i++ != count($user_info)) echo ",\n"; ?>
        <?php endforeach;?>
    ],  
    xkey: 'date',
    ykeys: ['dau', 'newuser','conversion'],
    labels: ['Daily Average Users', 'New Users','Converted Users']
    });
    
    var ret_info = Morris.Line({  
    element: 'ret_info',  
    data: [
        <?php $i = 0; ?>
        <?php foreach($ret_info as $key => $row) : ?>
                    {date: '<?= $row['date']; ?>', day1: '<?= $row['day1']; ?>', day3: '<?= $row['day3']; ?>', day5: '<?= $row['day5']; ?>', day7: '<?= $row['day7']; ?>', day14: '<?= $row['day14']; ?>', day30: '<?= $row['day30']; ?>'}<?php if($i++ != count($ret_info)) echo ",\n"; ?>
        <?php endforeach;?>
    ],  
    xkey: 'date',
    ykeys: ['day1', 'day3', 'day5','day7','day14','day30'],
    labels: ['Day 1', 'Day 3', 'Day 5','Day 7','Day 14','Day 30']
    });
    
    function getData()
    {
        $.post("/admin/ajax_dashboard", $("#frmDashBoard").serialize(), function(data){            
                user_info.setData(data.user_info);
                ret_info.setData(data.ret_info);
        }, 'json')
    }
</script>
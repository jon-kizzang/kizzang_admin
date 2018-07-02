<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<title>Kizzang - Password Change</title>
<!-- Bootstrap Core CSS -->
<link href="/css/bootstrap.min.css" rel="stylesheet">
<!-- MetisMenu CSS -->
<link href="/css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">
<!-- Custom CSS -->
<link href="/css/sb-admin-2.css" rel="stylesheet">
<!-- Custom Fonts -->
<link href="/font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>
<div class="container">
<div class="row">
<div class="col-md-4 col-md-offset-4">
<div class="login-panel panel panel-default">
<div class="panel-heading">
<h3 class="panel-title">Please Change Your Password</h3>
</div>
<div class="panel-body">
    <div class="well alert-warning">
        <h4>Password Requirements</h4>
        <ul>
            <li>Must contain 1 Uppercase Letter</li>
            <li>Must contain 1 Lowercase Letter</li>
            <li>Must contain 1 Number</li>
            <li>Must be at least 8 characters long</li>
        </ul>
    </div>
    <form role="form" action="/admin/login">
<fieldset>
<div class="form-group">
<input class="form-control" placeholder="Password" id="password1" type="password" autofocus>
</div>
<div class="form-group">
<input class="form-control" placeholder="Password Again" id="password2" type="password" value="">
</div>
    <div class="well alert-danger" id="message"></div>
<!-- Change this to a button or input when using this as a form -->
<button id="btn_change" class="btn btn-lg btn-success btn-block">Change Password</button>
</fieldset>
</form>
</div>
</div>
</div>
</div>
</div>
<!-- jQuery -->
<script src="/js/jquery.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="/js/bootstrap.min.js"></script>
<!-- Metis Menu Plugin JavaScript -->
<script src="/js/plugins/metisMenu/metisMenu.min.js"></script>
<!-- Custom Theme JavaScript -->
<script src="/js/sb-admin-2.js"></script>
<script>
    $("#btn_change").click(function(e){
        e.preventDefault();
        if($("#password1").val() === $("#password2").val())
        {
            $.post("/admin/change_password", {password: $("#password1").val()}, function(data){
                    if(data.success)
                    {
                        alert("Password Updated");
                        location.href = "/admin";
                    }
                    else
                    {
                        $("#message").html("<pre>" + data.message + "</pre>");
                    }
            }, 'json');
        }
        else
        {
            alert("Passwords don't match!");
        }
    });
</script>
</body>
</html>
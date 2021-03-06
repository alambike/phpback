<!DOCTYPE html>
<html>
<head>
  <title>PHPBack Installation</title>
  <!-- Latest compiled and minified JavaScript -->
  <script src="../public/bootstrap/js/bootstrap.min.js"></script> 
  <script src="../public/bootstrap/js/bootstrap.js"></script> 

    <!-- Loading Bootstrap -->
    <link href="../public/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="../public/bootstrap/css/prettify.css" rel="stylesheet">

    <!-- Loading Flat UI -->
    <link href="../public/css/flat-ui.css" rel="stylesheet">
    <link href="../public/css/demo.css" rel="stylesheet">


  <style type="text/css">
  a:hover{
    text-decoration: none;
  }
  body{
		background-color: #1ABC9C;
  }
  .phpback_logo{
		text-align: center;
		margin-bottom: 15px;
	}
  .configfile{
  	text-align: center;
  	background-color: #d3dfe0;
  	border-radius: 5px;
  	border-style: solid;
  	border-color: #e66161;
  	border-width: 2px;
  	padding: 5px 5px 5px 5px;
  	margin-top: 20px;
  }
  .phpcode{
  	text-align: left;
  	background-color: #f9f2f4;
  	border-radius: 5px;
  }
  </style>
</head>
<body>
	<div class="login-screen">
		<div class="login-form">
		<form action="install1.php" method="POST">
			<div class="phpback_logo">
				<img src="../public/img/logo_free.png" />
			</div>
			<h5>PHPBack installation</h5>
		<?php if(isset($_POST['error'])): ?>
			<div style="color:#C0392B;font-size:20px"><?php echo $_POST['error']; ?></div>
			<div style="color:#3C6E8F;font-size:15px;margin-bottom: 15px;">
                Need help? Please, visit our
                <a href="https://github.com/ivandiazwm/phpback/wiki/How-To-install#frequent-installation-issues" target="_blank">
                    frequent issues documentation.
                </a>
            </div>
		<?php endif; ?>
      <div class="form-group">
              <input type="text" class="form-control login-field" value="" placeholder="MySQL hostname" id="hostname" name="hostname" />
              <label class="login-field-icon" for="hostname"></label>
            </div>
            <div class="form-group">
              <input type="text" class="form-control login-field" value="" placeholder="MySQL username" id="username" name="username" />
              <label class="login-field-icon" for="username"></label>
            </div>
            <div class="form-group">
              <input type="password" class="form-control login-field" value="" placeholder="MySQL password" id="password" name="password" />
              <label class="login-field-icon fui-lock" for="password"></label>
            </div>
            <div class="form-group">
              <input type="text" class="form-control login-field" value="" placeholder="MySQL database*" id="database" name="database" />
              <label class="login-field-icon" for="database"></label>
            </div>
            <small>* Left in blank for automatic database creation</small>
            <hr>

            <h6>PHPBack installation</h6>
            <div class="form-group">
              <input type="text" class="form-control login-field" value="" placeholder="Admin Name" id="adminname" name="adminname" />
              <label class="login-field-icon" for="adminname"></label>
            </div>
			   <div class="form-group">
              <input type="text" class="form-control login-field" value="" placeholder="Admin Email" id="adminemail" name="adminemail" />
              <label class="login-field-icon" for="adminemail"></label>
            </div>
			   <div class="form-group">
              <input type="password" class="form-control login-field" value="" placeholder="Admin password" id="adminpass" name="adminpass" />
              <label class="login-field-icon fui-lock" for="adminpass"></label>
            </div>
            <div class="form-group">
              <input type="password" class="form-control login-field" value="" placeholder="Repeat admin password" id="adminrpass" name="adminrpass" />
              <label class="login-field-icon fui-lock" for="adminrpass"></label>
            </div>

            
            <input type="submit" class="btn btn-primary btn-lg btn-block" value="Proceed">
        </form>
        </div>
        </div>
</div>
</body>
</html>

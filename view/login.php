<?php 
	session_start();
	include_once("../config/config.php");
	include_once("../include/common_functions.php");
	if(isset($_SESSION['LOGGED_IN']) && $_SESSION['LOGGED_IN'] == true && $_SESSION['domain']==DOMAIN) {
        header("Location: ".DOMAIN."dashboard");
	}
	else if(isset($_SESSION['LOGGED_IN']) && $_SESSION['domain']!=DOMAIN) {
        header("Location: ".DOMAIN."logout");
	
	}
	$token = md5(rand(1000,9999));
	$_SESSION['token'] = $token;
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once('../include/include_css_file.php');?>
</head>
 <body class="login-body">	
    <div class="container">
    <!--  <form class="form-signin" id="signin" method="post" action="javascript:;">
        <h2 class="form-signin-heading">sign in now</h2>
        <div class="login-wrap">
		<div class="form-group ">
		  		 
			  <input type="text" class="form-control" name="username" id="username" placeholder="Email" autofocus value="<?=$_COOKIE['remember_me']?>" />
		  
		</div>
         <div class="form-group">
		  	<input type="password" name="password" value="<?=$_COOKIE['password']?>" id="password" class="form-control" placeholder="Password" />
		  
		</div>   
            
            <label class="checkbox" style="margin-left:21px;">
				<? $ck=''; 
				if(!empty($_COOKIE['remember_me']))
				{
				$ck='checked="checked"';
				}?>
                <input <?=$ck?> type="checkbox"  name="remember"  value="1"> Remember me
				<span class="pull-right">
                  <!--  <a data-toggle="modal" href="#myModal"> Forgot Password?</a>-->
            <!--      </span>
            </label>
			<div id="message"></div>
			<input type='hidden' name='token' id='token' value='<?php echo $token; ?>' />
			<input type='hidden' name='redirect' id='redirect' value='<?php echo (isset($_GET['redirect']))?$dbcon->real_escape_string($_GET['redirect']):''; ?>' />
            <button class="btn btn-lg btn-login btn-block" type="submit">Sign in</button>
		</form> -->          

        </div>

          <!-- Modal -->
          <div class="modal colored-header info " id="myModal" role="dialog" data-keyboard="false" data-backdrop="static">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Forgot Password ?</h4>
                      </div>
                      <div class="modal-body">
                          <p>Enter Question.</p>
									<select class="form-control" name="forgotquestion_id" id="forgotquestion_id">	
										<?=getquestion($dbcon,$rel['question_id'])?>
									<select>
									<label class="error" id="error_companyid"></label>
						  <p>Your Answer.</p>
									<input type="text" class="form-control" placeholder="Give Answer" name="forgotgive_answer" id="forgotgive_answer"  value="" />
							<label class="error" id="error_answer"></label>
							<p><div id="forgot_message"></div></p>
                      </div>
					  
                      <div class="modal-footer">
                          
				<input type="hidden" name="forgot_companyid" id="forgot_companyid"  value="" />
				<input type="hidden" name="forgot_usertype" id="forgot_usertype"  value="" />
							
						  <button data-dismiss="modal" class="btn btn-default" onclick="close_forgetpass()" type="button">Cancel</button>
                          <button class="btn btn-success" onclick="check_forgotpass()" type="button">Submit</button>
                      </div> 
                  </div>
              </div>
          </div>
          <!-- modal -->
      
    </div>
	<?php include_once('../include/include_js_file.php');?> 
</body>
<script src="<?=ROOT?>js/app/login.js"></script>

</html>

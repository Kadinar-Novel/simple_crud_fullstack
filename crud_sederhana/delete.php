<?php
	$id = $_GET['id'];
	$url = "http://localhost:9999/api/karyawan/".$id;
	$url_get = file_get_contents($url);
	$data = json_decode($url_get, true);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Use API</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
</head>
<body>
	<div class="container">
		<form class="form-siswa" id="form" method="post">
		  <div class="form-group">
		    <label>NIK</label>
		    <input type="text" class="form-control" name="nik_karyawan" id="nik_karyawan" value="<?php echo $data['data']['nik_karyawan']?>">
		    <input type="text" class="form-control" name="id" id="id" value="<?php echo $data['data']['id']?>">
		  </div>
		  <div class="form-group">
		    <label>Nama</label>
		    <input type="text" class="form-control" name="nama" id="nama" value="<?php echo $data['data']['nama']?>">
		  </div>
		  <div class="form-group">
		    <label>Alamat</label>
		    <input type="text" class="form-control" name="alamat" id="alamat" value="<?php echo $data['data']['alamat']?>">
		  </div>
		  <div class="form-group">
		    <label>Telepon</label>
		    <input type="text" class="form-control" name="phone" id="phone" value="<?php echo $data['data']['phone']?>">
		  </div>
		  <div class="form-group">
		    <label>Email</label>
		    <input type="text" class="form-control" name="email" id="email" value="<?php echo $data['data']['email']?>">
		  </div>
		  <div class="form-group">
		    <label>Jenis Kelamin</label><br/>
		    <input type="radio" name="jenis_kelamin" id="jenis_kelamin" value="L" <?php echo $data['data']['jenis_kelamin']=='L' ? 'checked': '' ?>> Laki-Laki
		    <input type="radio" name="jenis_kelamin" id="jenis_kelamin" value="P" <?php echo $data['data']['jenis_kelamin']=='P' ? 'checked': '' ?>> Perempuan
		  </div>
		</form>
		<button class="btn btn-primary" id="btn" onclick="return confirm('Hapus data ini?')">Delete</button>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			$(".btn").click(function(){
				var data = $('#id').val();
				$.ajax({
					method: 'DELETE',
					url: "http://localhost:9999/api/karyawan/"+data,
					dataType: 'json',
					data: data,
					success: function(data){
						if(data.code==200){
							alert(data.message);
							$("#form")[0].reset();
							location.href='index.php';
						}else{
							alert(data.message);
						}
					}
				});
			});
		});
	</script>
</body>
</html>
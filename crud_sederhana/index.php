<?php
	$url = "http://localhost:9999/api/karyawan";
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
		<form class="form-karyawan" id="form" method="post">
		  <div class="form-group">
		    <label>NIK</label>
		    <input type="text" class="form-control" name="nik_karyawan" id="nik_karyawan">
		  </div>
		  <div class="form-group">
		    <label>Nama</label>
		    <input type="text" class="form-control" name="nama" id="nama">
		  </div>
		  <div class="form-group">
		    <label>Alamat</label>
		    <input type="text" class="form-control" name="alamat" id="alamat">
		  </div>
		  <div class="form-group">
		    <label>Telepon</label>
		    <input type="text" class="form-control" name="phone" id="phone">
		  </div>
		  <div class="form-group">
		    <label>Email</label>
		    <input type="text" class="form-control" name="email" id="email">
		  </div>
		  <div class="form-group">
		    <label>Jenis Kelamin</label><br/>
		    <input type="radio" name="jenis_kelamin" id="jenis_kelamin" value="L"> Laki-Laki
		    <input type="radio" name="jenis_kelamin" id="jenis_kelamin" value="P"> Perempuan
		  </div>
		</form>
		<button class="btn btn-primary" id="btn">Submit</button>
		<hr>
		<div class="tampildata"></div>
	  	<table class="table table-striped">
			<thead class="thead-dark">
			<tr>
				<th>NIK</th>
				<th>Nama</th>
				<th>Alamat</th>
				<th>Telepon</th>
				<th>Email</th>
				<th>Jenis Kelamin</th>
				<th>Action</th>
			</tr>
			</thead>
			<?php
				if(isset($data['code'])=='200'){
					for($i=0;$i<count($data['data']);$i++) {					
			?>
				<tr>
					<td><?php echo $data['data'][$i]['nik_karyawan']?></td>
					<td><?php echo $data['data'][$i]['nama']?></td>
					<td><?php echo $data['data'][$i]['alamat']?></td>
					<td><?php echo $data['data'][$i]['phone']?></td>
					<td><?php echo $data['data'][$i]['email']?></td>
					<td><?php echo $data['data'][$i]['jenis_kelamin']?></td>
					<td>
						<a href="edit.php?id=<?php echo $data['data'][$i]['id']?>">Edit</a> || 
						<a href="delete.php?id=<?php echo $data['data'][$i]['id']?>">Delete</a>
					</td>
				</tr>
			<?php
					}
				} else {
					echo "<tr><td>".$data['message']."</td></tr>";
				}
			?>
		</table>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			$(".btn").click(function(){				
				var data = $('.form-karyawan').serializeArray();
				var postData = jQFormSerializeArrToJson(data);
				var jsonData = JSON.stringify(postData);
				
				$.ajax({
					url: "http://localhost:9999/api/karyawan",
					method: 'POST',
					dataType: 'json',
					data: jsonData,
					success: function(data){
						if(data.code==200){
							alert(data.message);
							$("#form")[0].reset();
							location.reload();
						}else{
							alert(data.message);
						}
					}
				});
			});
		});

		function jQFormSerializeArrToJson(formSerializeArr){
			var jsonObj = {};
			jQuery.map( formSerializeArr, function( n, i ) {
				jsonObj[n.name] = n.value;
			});

			return jsonObj;
		}
	</script>
</body>
</html>
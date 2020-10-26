<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Conexion</title>
</head>
<body>
	Hola
	<script>
		var conn = new WebSocket('ws://chessmake-it.com:8080');
		conn.onopen = function(e) {
		    console.log("Connection established!");
		};

		conn.onmessage = function(e) {
		    console.log(e.data);
		};
	</script>
</body>

</html>
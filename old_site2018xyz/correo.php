<?php
	
	$name=$_POST['name'];
	$email=$_POST['email'];
	$message=$_POST['message'];
	
	$fecha = time();
	$ff = date("j/n/Y", $fecha);
	$para ='soporte@chessmake-it.com'. ', '; 
		$mensaje = '
	<html>
	<head>
	  <title>contacto</title>
	  <style>
	  	table{width: 100%;}
		table, th, td {
		    border: 1px solid black;
		    border-collapse: collapse;
		}
		th, td {
		    padding: 5px;
		    text-align: left;
		}
		table#t01 {
		    width: 100%;
		    background-color: #f1f1c1;
		}
		</style>
	</head>
	<body>
	<h3> Contacto</h3>

	<table>
    <tr><th>Nombre: </th><td colspan="3">'. $name .'</tdh></tr>  
    <tr>   <th>Correo: </th><td>' . $email  .'</tdh><th>Fecha: </th><td>'. $ff       .'</tdh>    </tr>	
    <tr> <th>Mensaje: </th><td colspan="3">' . $message       .'</tdh>    </tr>
  	</table>

  	
	</body>
	</html>
	';
	
	$mensaje = wordwrap($mensaje, 70, "\r\n");
	
	// Para enviar un correo HTML, debe establecerse la cabecera Content-type
	$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
	$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	
      $cabeceras .= 'From: soporte@chessmake-it.com  ' . "\r\n";
		
	mail( $para, 'Test - '.$vdcc, $mensaje, $cabeceras);

	header('Location: index.html');
	
?>



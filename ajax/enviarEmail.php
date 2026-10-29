<?php
	require("../class.phpmailer.php");

	$nombre = $_POST['nombre'];
	$empresa = $_POST['empresa'];
	$email = $_POST['email'];
	$telefono = $_POST['telefono'];
	$mensaje = $_POST['mensaje'];

	$mail = new PHPMailer();
	$mail->Host = "localhost";
	$mail->Username = "no-reply@cubedata.io"; 
	$mail->Password = "2?1vf?_.oe;?";	
	//$mail->SMTPDebug = 2;
	
	$mail->From = "no-reply@cubedata.io";
	$mail->FromName = "Info Web CubeData";
	$mail->Subject = "Consulta via sitio web CubeData";

	$mail->AddAddress("juan@cubedata.io","CubeData");
	$mail->IsHTML(true);

	$cuerpo = "<table style='font-size:1rem;margin-left: auto;margin-right: auto;font-family:arial;border-radius:10px;background-color: #f3f3ff;border:30px solid #f3f3ff;text-align:center; '>";
	$cuerpo.="<tr><td><img src='https://cubedata.io/assets/img/logo-cube.png' style='width:300px;margin-left:auto;margin-right:auto;display:block'></td></tr>";
	$cuerpo .="<tr>";
	$cuerpo .="<td style='text-align:center'>";
	$cuerpo .= "<b>Nombre</b>: ".$nombre."<br>";
	$cuerpo .= "<b>Empresa</b>: ".$empresa."<br>";
	$cuerpo .= "<b>Email</b>: ".$email."<br>";
	$cuerpo .= "<b>Telefono</b>: ".$telefono."<br>";
	$cuerpo .= "<b>Comentarios</b>: ".$mensaje."<br>";
	$cuerpo .="</td>";
	$cuerpo .="</tr>";
	$cuerpo.="</table>";

	$mail->Body = $cuerpo;
	$mail->AltBody = "";

	// Notificamos al usuario del estado del mensaje
	if(!$mail->Send()){
		echo "No se pudo enviar el Mensaje.";
	}else{
		echo "Mensaje enviado";
	}
?>
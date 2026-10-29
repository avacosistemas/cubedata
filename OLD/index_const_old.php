<!DOCTYPE html>
<html>
<head>
	<title>CubeData</title>
	<meta charset="utf-8"/>
	<meta name="description" content="CUBEDATA - P&aacute;gina web en mantenimiento"/>
	<meta name="author" content="Avaco.com.ar"/>
	<style>
		/*TIPOGRAFÍAS*/
		@import url('https://fonts.googleapis.com/css?family=Noto+Sans');
		/*INICIALIZACIÓN DE ESTILOS*/
		*{
			margin:0;
			padding:0;
			box-sizing:border-box;
		}

		body{background-color:#D27429;}

		/*PERSONALIZACIÓN DE P.MANTENIMIENTO*/
		/**.mantenimiento{
			width:600px;
			height:340px;
			padding:32px;
			border:1px solid #BF2228;
			border-radius:10px;
			margin-top:-200px;
			margin-left:-300px;
			background-color:#fff;
			position:fixed;
			top:50%;
			left:50%;
		}*/
		.mantenimiento{
			text-align:center;
			width:40%;
			height:40%;
			border:1px solid #BF2228;
			border-radius:10px;
			background-color:#fff;
			position:fixed;
			top:20%;
			left:35%;
			display: flex;
			align-items: center;
			justify-content: center;
		}
		.mantenimiento h1{
			font-size:3em;
			text-align:center;
			padding:16px;
			color:#BF2228;
		}

		@media (max-width: 999px) {
			.mantenimiento{
				width:70%;
				height:20%;
				left:15%;
			}

			.mantenimiento h1{
				font-size:4em;
			}
		}

		.mantenimiento h1, .mantenimiento h2, .mantenimiento p{
			font-family:"noto sans", sans-serif;
		}

		.mantenimiento h2{
			font-size:2em;
			font-style:italic;
			text-align:center;
			color:#BF2228;
		}
		.mantenimiento p{
			margin:16px 0;
			line-height:1.5em;
			text-align:center;
			color:#BF2228;
		}

	</style>
</head>
<body>
	<div class="mantenimiento">
		<!--img style="width:40%;" src="https://cubedata.io/assets/img/logo-cube-color.png" alt="Logo CubeData"-->
		<h1>Estamos mejorando para vos!!</h1>
		<!--p>La Web CubeData se encuentra en construcci&oacute;n, volveremos pronto</p-->
	</div>
</body>
</html>
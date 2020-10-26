<!DOCTYPE html>

<head>
	<base href="{{config('app.url')}}">
	<title>Chess Make It</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name=”viewport” content=”width=1024, minimal-ui”>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="theme-color" content="#3f51b5">

	<link href="css/main.css" rel="stylesheet" type="text/css" />
	<link rel="apple-touch-icon" href="img/favicon.png">
	<link rel="icon" type="image/png" href="img/favicon.png">

	<!-- This tells the page to watch for special styling for IE9 -->
	<!--[if IE 9 ]>    <html class= "ie9"> <![endif]-->
	<!-- Important external stylesheets -->
	<link rel="stylesheet" href="{{ url('css/owl.carousel.min.css') }}">
	<link rel="stylesheet" href="{{ url('css/owl.theme.min.css') }}">
	<link rel="stylesheet" href="{{ url('css/fontello.min.css') }}">
	<link rel="stylesheet" href="{{ url('css/jquery.fancybox8cbb.min.css?v=2.1.5') }}" type="text/css" media="screen" />
	<!-- First we will load jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

	<!-- Now we load the JS files for the fancy things on the page -->
	<script type="text/javascript" src="{{ url('js/headroom.min.js') }}"></script>
	<script type="text/javascript" src="{{ url('js/jQuery.headroom.min.js') }}"></script>
	<script type="text/javascript" src="{{ url('js/owl.carousel.min.js') }}"></script>
	<script type="text/javascript" src="{{ url('js/jquery.fitvids.min.js') }}"></script>
	<script type="text/javascript" src="{{ url('js/jquery.fancybox.pack8cbb.min.js?v=2.1.5') }}"></script>
	<!--<script type="text/javascript" src="js/retina.min.js"></script>-->
	<script type="text/javascript" src="{{ url('js/jquery.scrollToTop.min.js') }}"></script>
	<!-- Finally we will load the 2 fonts from Google Fonts -->
	<link href='https://fonts.googleapis.com/css?family=Raleway:400,200,300,500,600,700|Merriweather:400,300,300italic,400italic'
	 rel='stylesheet' type='text/css'>
	<style>
		.content {
			width: 90%;
			max-width: 900px;
		}

		.header {
			background: rgba(0, 0, 0, 0.75);
		}

		.wrapper {
			padding-top: 90px;
		}
	</style>
</head>

<body>

	<a href="#top" id="toTop"></a>
	<header>
		<div class="header navbar-fixed-top headroom">
			<div class="header-container">
				<div class="logo">
					<a href="{{config('app.url')}}">
						<img src="img/logo.png" style="max-width: 250px;" alt="Chess Make It" />
					</a>
				</div>
				<div class="menu">
					<ul>
						@if (Route::has('login'))
						<div class="top-right links">
							@auth
							<li>
								<a class="cta" href="{{ route('home') }}">Inicio</a>
							</li>
							@else
							<li>
								<a class="cta" href="{{ route('login') }}">Iniciar Sesión</a>
							</li>
							<li>
								<a href="{{ route('register') }}">Registrarse</a>
							</li>
							@endauth
						</div>
						@endif
					</ul>
				</div>
			</div>
		</div>
	</header><br><br>
	<div class="wrapper">
		<div class="container">
			<div class="content">
				<p style="text-align: justify;">La Política de Tratamiento de la Información es un documento dispuesto por la Ley 1581 y el Decreto 1377 de 2013 en el
					cual se comunica los lineamientos bajo los cuales será tratada y protegida su información, asegurando el respeto de
					los principios y normas contenidas en la legislación vigente aplicable.</p>

				<p style="text-align: justify;">En consecuencia según lo dispuesto en el artículo 13 del decreto referido esta política será aplicable para cualquier
					persona natural que entregue su información a CHESSMAKEIT en el ejercicio de sus actividades económicas.</p>

				<p style="text-align: justify;">&nbsp;</p>

				<p style="text-align: justify;"><strong>NUESTROS DATOS DE CONTACTO SON RELACIONADOS A CONTINUACIÓN:</strong></p>

				<p style="text-align: justify;"><strong>Nombre o razón social: CHESSMAKEIT</strong></p>

				<p style="text-align: justify;"><strong>Nit:</strong>&nbsp;900.262.711-7</p>

				<p style="text-align: justify;"><strong>Teléfono:</strong>&nbsp;(7) 6575641- 6960908</p>

				<p style="text-align: justify;"><strong>Domicilio:</strong>&nbsp;Calle 58 No. 32-75</p>

				<p style="text-align: justify;"><strong>Sitio Web:&nbsp;</strong>www.chessmake-it.com</p>

				<p style="text-align: justify;">&nbsp;</p>

				<p style="text-align: justify;"><strong>DEFINICIONES</strong></p>

				<p style="text-align: justify;">Para la correcta interpretación de los lineamientos contenidos en la presente política, a continuación se definen los
					principales conceptos relacionados con el tratamiento de datos personales:</p>

				<p style="text-align: justify;"><strong>AVISO DE PRIVACIDAD:</strong>&nbsp;Comunicación verbal o escrita generada por el Responsable, dirigida al Titular
					para el tratamiento de sus datos personales, mediante la cual se le informa acerca de la existencia de las políticas
					de&nbsp; tratamiento de información que le serán aplicables, la forma de acceder a ellas y las finalidades del Tratamiento
					que se pretende dar a los datos personales.</p>

				<p style="text-align: justify;"><strong>DATO PERSONAL:</strong>&nbsp;Cualquier información vinculada o que pueda asociarse a una o varias personas naturales
					determinadas o determinables.</p>

				<p style="text-align: justify;"><strong>DATO PRIVADO:&nbsp;</strong>Es la información personal relacionada con el ámbito privado de las personas: Libros
					de los comerciantes, datos contenidos en documentos privados, gustos o datos de contacto personales.</p>

				<p style="text-align: justify;"><strong>DATO PÚBLICO:&nbsp;</strong>Entre otros, son los datos relativos al estado civil de las personas, a su profesión
					u oficio y a su calidad de comerciante o de servidor público.&nbsp; Por su naturaleza, los datos públicos, gacetas y
					boletines oficiales y sentencias judiciales debidamente ejecutoriadas que no estén sometidas a reserva.</p>

				<p style="text-align: justify;"><strong>TRANSFERENCIA:&nbsp;</strong>La transferencia de datos tiene lugar cuando el Responsable y/o Encargado del Tratamiento
					de datos personales, ubicado en Colombia, envía la información o los datos personales a un receptor, que a su vez es
					responsable del tratamiento y se encuentra dentro o fuera del país.</p>

				<p style="text-align: justify;"><strong>BASE DE DATOS:&nbsp;</strong>Conjunto organizado de datos personales que sea objeto de tratamiento.</p>

				<p style="text-align: justify;"><strong>ENCARGADO DEL TRATAMIENTO:</strong>&nbsp;La persona natural o jurídica, pública o privada que por sí misma o
					conjuntamente con otros, trate datos personales por cuenta del responsable del tratamiento.</p>

				<p style="text-align: justify;"><strong>RESPONSABLE DEL TRATAMIENTO:</strong>&nbsp;Persona natural o jurídica, pública o privada, que por sí misma o
					en asocio con otros, decida sobre la base de datos y/o el tratamiento de datos.</p>

				<p style="text-align: justify;">&nbsp;</p>

				<p style="text-align: justify;"><strong>PRINCIPIOS APLICABLES AL TRATAMIENTO DE DATOS PERSONALES</strong></p>

				<p style="text-align: justify;">Como lo establece la Ley 1581 y las normas que lo complementan y como compromiso con el trato responsable de la información,
					las actuaciones y decisiones adoptadas&nbsp;<strong>CHESSMAKEIT</strong>&nbsp;aplicará los siguientes principios:</p>

				<ol>
					<li style="text-align: justify;"><strong>Principio de legalidad en materia de tratamiento de datos:&nbsp;</strong>El tratamiento de datos es una actividad
						reglada, la cual deberá estar sujeta a lo establecido en la ley 1581 de 2012 y en las demás disposiciones legales vigentes
						que la desarrollen.</li>
					<li style="text-align: justify;"><strong>Principio de finalidad:&nbsp;</strong>La actividad del tratamiento de datos personales que realice&nbsp;<strong>CHESSMAKEIT</strong>&nbsp;obedece
						a una finalidad legítima de acuerdo a lo establecido con la Constitución Política de Colombia la cual deberá ser informada
						con antelación al titular de los datos personales.</li>
					<li style="text-align: justify;"><strong>Principio de libertad:&nbsp;</strong>El tratamiento de la información personal del titular sólo puede realizarse
						con su previo y expreso consentimiento. Los datos personales no podrán ser obtenidos o divulgados sin autorización
						o en ausencia del mandato legal o judicial que releve el consentimiento.</li>
					<li style="text-align: justify;"><strong>Principio de veracidad o calidad:</strong>&nbsp;La información sujeta a tratamiento debe ser veraz, completa,
						exacta, actualizada, comprobable y comprensible.&nbsp; Se prohíbe el tratamiento de datos parciales, incompletos, o
						que induzcan a error.</li>
					<li style="text-align: justify;"><strong>Principio de transparencia:&nbsp;</strong>En el tratamiento de datos personales debe garantizarse el derecho
						del titular a obtener del responsable del tratamiento en&nbsp;&nbsp; cualquier momento y sin restricciones información
						acerca de la existencia de datos o cualquier tipo de información que sea de su interés.</li>
					<li style="text-align: justify;"><strong>Principio de acceso y circulación restringida:&nbsp;</strong>El tratamiento de la información de los titulares
						sólo podrá hacerse por personas autorizadas por el titular o por las personas previstas en la presente ley. &nbsp;Los
						datos personales, salvo la información pública, no podrá estar disponible en internet u otros medios de divulgación
						o comunicación masiva, salvo que el acceso sea técnicamente controlable para brindar conocimiento restringido sólo
						a los titulares o terceros autorizados conforme a la ley 1581 de 2012.&nbsp; Para estos propósitos la obligación de
						CHESSMAKEIT será de medio.</li>
					<li style="text-align: justify;"><strong>Principio de seguridad:&nbsp;</strong>La información sujeta a tratamiento por CHESSMAKEIT se manejará con las
						medidas técnicas, humanas y administrativas que sean necesarias para otorgar seguridad a los registros evitando su
						adulteración, pérdida, consulta, uso o acceso no autorizado o fraudulento.</li>
					<li style="text-align: justify;"><strong>Principio de confidencialidad:</strong>&nbsp;Todas las personas que intervengan en el tratamiento de datos personales
						que no tengan la naturaleza de públicos están obligadas a garantizar la reserva de la información por lo que se comprometen
						a conservar y mantener de manera estrictamente confidencial y no revelar a terceros, toda la información que se llegase
						a conocer durante la ejecución y ejercicio de sus funciones; salvo cuando se trate de actividades autorizadas por la
						ley de protección de datos.&nbsp; Esta obligación persiste inclusive después de finalizada su relación con alguna de
						las labores que comprende el tratamiento.</li>
				</ol>

				<p style="text-align: justify;">&nbsp;</p>

				<p style="text-align: justify;"><strong>POLITICAS DE SEGURIDAD INFORMÁTICA</strong></p>

				<p style="text-align: justify;">En los procedimientos internos, CHESSMAKEIT adoptará medidas de seguridad con el fin de:</p>

				<ol>
					<li style="text-align: justify;">Evitar el daño, pérdida, alteración, hurto o destrucción de los datos personales, lo cual implica velar por la correcta
						operación de los procesos operativos y tecnológicos relacionados con esta materia.</li>
					<li style="text-align: justify;">Prevenir el uso, acceso o tratamiento no autorizado de los mismos, para lo cual se preverán niveles de acceso y circulación
						restringida de dicha información.</li>
					<li style="text-align: justify;">Incorporar los criterios de seguridad de los datos personales como parte integral de la adquisición, desarrollo y mantenimiento
						de los sistemas de información.</li>
				</ol>

				<p style="text-align: justify;">Las políticas internas de seguridad bajo las cuales se conserva la información del titular para impedir su adulteración,
					pérdida, consulta, uso o acceso no autorizado son las siguientes:</p>

				<ol>
					<li style="text-align: justify;">Controles en la infraestructura tecnológica perimetral en la red de datos como es el manejo de firewalls, correos corporativos,
						seguro e instalación de antivirus.</li>
					<li style="text-align: justify;">Acuerdos de confidencialidad con proveedores, clientes, empleados y terceros.</li>
					<li style="text-align: justify;">Niveles de seguridad en el acceso de las bases de datos.</li>
				</ol>

				<p style="text-align: justify;">&nbsp;</p>

				<p style="text-align: justify;"><strong>DERECHOS DE LOS TITULARES</strong></p>

				<p style="text-align: justify;">Las personas cuya información personal u objeto de tratamiento por parte de&nbsp;<strong>CHESSMAKEIT</strong>ostentan
					la calidad de titulares, en virtud de la cual podrán disfrutar y ejercer los siguientes derechos reconocidos por la
					Constitución y la Ley:</p>

				<ul>
					<li style="text-align: justify;">Conocer, actualizar y rectificar los datos personales.&nbsp; Para garantizar este derecho deberá acreditarse la identidad
						del titular o la calidad de legitimado, con el fin de impedir que terceros no autorizados accedan a la información
						personal.
					</li>
					<li style="text-align: justify;">Obtener copia de la autorización que hayan otorgado en calidad de titulares de los datos.</li>
					<li style="text-align: justify;">Conocer el tratamiento que se está efectuando sobre los datos personales por parte de&nbsp;<strong>CHESSMAKEIT</strong></li>
					<li style="text-align: justify;">Formular consultas y reclamos para salvaguardar su derecho a la protección de datos personales de acuerdo con las pautas
						establecidas en la ley y en los términos de la presente política.</li>
					<li style="text-align: justify;">Solicitar la supresión de los datos personales o revocar la autorización concedida cuando mediante un proceso judicial
						o administrativo se determine que en el tratamiento de su información se vulneraron las disposiciones legales y constitucionales
						sobre la materia.</li>
					<li style="text-align: justify;">Acceder en forma gratuita a sus datos personales.&nbsp; La información solicitada por el titular podrá ser suministrada
						por cualquier medio que le permita conocerla, incluyendo los electrónicos.</li>
				</ul>

				<p style="text-align: justify;">En la atención y trámite de las consultas de información que eleve el titular, se tendrá en cuenta lo ordenado frente
					al particular en el artículo 21 del decreto 1377 de 2013, el cual consagra:</p>

				<p style="text-align: justify;"><em>“El titular podrá consultar de forma gratuita sus datos personales: (i) al menos una vez cada mes calendario, y (ii) cada vez que existan modificaciones sustanciales de las Políticas de Tratamiento de la información que motiven nuevas consultas."</em></p>

				<p style="text-align: justify;">Para consultas cuya periodicidad sea mayor a una por cada mes calendario, el responsable solo podrá cobrar al titular
					los gastos de envío, reproducción y, en su caso, certificación de documentos.&nbsp; Los costos de reproducción no podrán
					ser mayores a los costos de recuperación del material correspondiente.&nbsp; Para tal efecto, el responsable deberá
					demostrar a la Superintendencia de Industria y Comercio, cuando está así lo requiera, el soporte de dichos gastos.</p>

				<p style="text-align: justify;">&nbsp;</p>

				<p style="text-align: justify;"><strong>¡EJERZA SUS DERECHOS!</strong></p>

				<p style="text-align: justify;"><strong>CHESSMAKEIT</strong>&nbsp;en aras de garantizar el ejercicio de los derechos del titular, dispone de los siguientes
					canales de atención para recepcionar las consultas y reclamos que formule el titular en relación con la protección de
					sus datos personales:</p>

				<ol>
					<li style="text-align: justify;">En la sede principal, ubicada en la dirección Calle 58 No. 32-75 Local II Barrio Conucos, Bucaramanga, para la recepción
						física de los documentos que contengan la consulta o el reclamo.</li>
					<li style="text-align: justify;">En la dirección de correo electrónico: info@chessmake-it.com bajo el Asunto “Protección de datos personales”, para la
						recepción digital de los documentos que contengan la consulta o el reclamo.</li>
				</ol>

				<p style="text-align: justify;">Los canales de comunicación relacionados son operados por personal capacitado para la gestión oportuna de las consultas
					y reclamos, quienes cuentan con sistemas de control para el registro de novedades relativas al tratamiento de la información
					personal del titular y para documentar los procedimientos de atención a los requerimientos y solicitudes que éstos presenten.</p>

				<p style="text-align: justify;">&nbsp;</p>

				<p style="text-align: justify;"><strong>LEGITIMACIÓN PARA EJERCER LOS DERECHOS DEL TITULAR</strong></p>

				<p style="text-align: justify;">La información personal sólo podrá ser entregada cuando la solicitud sea elevada por las siguientes personas:</p>

				<ol>
					<li style="text-align: justify;">Al titular del dato, sus causahabientes o sus representantes legales, siempre y cuando acrediten esta calidad.</li>
					<li style="text-align: justify;">A las personas autorizadas por el titular del dato.</li>
					<li style="text-align: justify;">A las personas autorizadas por orden judicial o legal.</li>
				</ol>

				<p style="text-align: justify;">En relación con el último supuesto, debe advertirse que las entidades públicas o administrativas que realicen solicitudes
					de información personal deberán justificar la relación existente entre la necesidad de obtener dicha información y el
					cumplimiento de sus funciones constitucionales o legales.</p>

				<p style="text-align: justify;">En cualquier evento, el suministro de información a las entidades públicas o administrativas hará extensivo para éstas
					el deber de cumplir con las exigencias y disposiciones normativas establecidas en la ley 1581 de 2012, ya sea en calidad
					de Responsables o Encargados del tratamiento según el caso.&nbsp; De este modo, la información personal que la entidad
					administrativa llegue a conocer deberá ser tratada y protegida conforme a los principios contenidos en la ley y en la
					presente política, en especial los de finalidad, uso legítimo, circulación restringida, seguridad y confidencialidad.</p>

				<p style="text-align: justify;">&nbsp;</p>

				<p style="text-align: justify;"><strong>CONTENIDO MÍNIMO DE LA SOLICITUD</strong></p>

				<p style="text-align: justify;">Las solicitudes que presente el titular con el fin de realizar una consulta o reclamo sobre el uso y manejo de sus datos
					personales deberán contener unas especificaciones mínimas, en aras de brindar al titular una respuesta clara y coherente
					con lo peticionado.&nbsp; Los requisitos de la solicitud son:</p>

				<ul>
					<li style="text-align: justify;">Estar dirigida a&nbsp;<strong>CHESSMAKEIT</strong></li>
					<li style="text-align: justify;">Contener la identificación del Titular (nombre y documento de identificación)</li>
					<li style="text-align: justify;">Contener la descripción de los hechos que motivan la consulta o el reclamo en relación con la protección de datos personales.</li>
					<li style="text-align: justify;">Indicar la dirección de notificación del Titular, tanto física como electrónica (e-mail).</li>
					<li style="text-align: justify;">Anexar los documentos que se quieren hacer valer (Especialmente para reclamos)</li>
				</ul>

				<p style="text-align: justify;">&nbsp;</p>

				<p style="text-align: justify;"><strong>PROCEDIMIENTOS PARA LA ATENCIÓN DE CONSULTAS Y RECLAMOS</strong></p>

				<p style="text-align: justify;">Las consultas serán atendidas en un término máximo de diez (10) días hábiles contados a partir del día siguiente a la
					fecha en que la misma haya sido recibida mediante los canales dispuestos para el efecto.&nbsp; Cuando no fuere posible
					atender la consulta dentro de dicho término, se informará al interesado la razón del aplazamiento y se señalará la nueva
					fecha en que será resuelta su consulta, la cual en ningún caso superará a los cinco (5) días hábiles siguientes al vencimiento
					del primer término.</p>

				<p style="text-align: justify;">Los reclamos serán atendidos en un término máximo de quince (15) días hábiles contados a partir del día siguiente a la
					fecha en que la misma haya sido recibida mediante los canales dispuestos para el efecto.&nbsp; Si el reclamo no cumple
					con los requisitos establecidos en el acápite precedente o se encontrará incompleto, se requerirá al interesado dentro
					de los cinco (5) días hábiles siguientes a la recepción del mismo para que subsane las falencias identificadas.&nbsp;
					Si transcurridos dos (2) meses desde la fecha del requerimiento, el titular o interesado no hubiere corregido la solicitud
					o aportado la información correspondiente se entenderá abandonado el reclamo y se procederá al archivo definitivo del
					mismo.
				</p>

				<p style="text-align: justify;">En el caso contrario, cuando el requerimiento hubiese sido atendido por el titular en el término previsto, subsanando
					los defectos del reclamo y allegando la documentación necesaria,&nbsp;<strong>CHESSMAKEIT&nbsp;</strong>emitirá la respuesta
					pertinente, pudiendo prorrogar el término en casos especiales con previa comunicación al interesado.&nbsp; Este nuevo
					plazo no será superior a ocho (8) días hábiles.</p>

				<p style="text-align: justify;">Cuando&nbsp;<strong>CHESSMAKEIT</strong>&nbsp;determine que no es competente para resolver una consulta o reclamo, dará
					respuesta en un término no mayor a cinco (5) días hábiles explicando al peticionario los motivos por los cuales no se
					encuentra facultada para resolverlo.</p>

				<p style="text-align: justify;">Si el titular se halla inconforme con la respuesta recibida o considera que la misma no satisface sus necesidades, cuenta
					con un término de quince (15) días a partir de la notificación de la respuesta para pedir que sea revaluada en los casos
					en que haya sido desfavorable a sus intereses.</p>

				<p style="text-align: justify;">&nbsp;</p>

				<p style="text-align: justify;"><strong>AUTORIZACIÓN A TERCEROS</strong></p>

				<p style="text-align: justify;">Cuando el titular desee formular una consulta o solicitar la actualización y rectificación de sus datos personales por
					intermedio de un tercero, deberá remitir a&nbsp;<strong>CHESSMAKEIT</strong>., de manera física o por correo electrónico,
					la debida autorización mediante la cual lo faculta para el ejercicio de sus derechos como titular.&nbsp; La presentación
					de la autorización constituye un requisito obligatorio para garantizar la reserva de la información frente a terceros
					no autorizados.</p>

				<p style="text-align: justify;">La autorización, deberá contener como mínimo lo siguiente:</p>

				<ul>
					<li style="text-align: justify;">Identificación del titular que autoriza</li>
					<li style="text-align: justify;">Copia de la cédula de ciudadanía del titular</li>
					<li style="text-align: justify;">Nombre y datos de identificación de la persona autorizada</li>
					<li style="text-align: justify;">Tiempo por el cual puede consultar, actualizar o rectificar la información (solo una vez, por un año, por la duración
						de la relación jurídica, o hasta nueva orden, etc.).</li>
					<li style="text-align: justify;">Carácter voluntario y libre de la autorización.</li>
				</ul>

				<p style="text-align: justify;">&nbsp;</p>

				<p style="text-align: justify;"><strong>ÁREA RESPONSABLE DE LA ATENCIÓN DE CONSULTAS</strong></p>

				<p style="text-align: justify;">La persona designada para recepcionar, direccionar y dar respuesta a las consultas y reclamos que eleven los titulares
					es el asistente administrativo.</p>

				<p style="text-align: justify;">&nbsp;</p>

				<p style="text-align: justify;"><strong>FINALIDADES DEL TRATAMIENTO</strong></p>

				<p style="text-align: justify;">La información sometida a tratamiento es empleada para el correcto desarrollo de nuestros procesos y operaciones internas,
					con el fin de brindar servicios de alta calidad que fomenten la satisfacción y confianza de nuestros clientes hacia
					la labor que el equipo humano realiza con esfuerzo y dedicación.&nbsp; Las finalidades para las cuales recolectamos
					y tratamos la información personal se describen a continuación para conocimiento de los titulares:</p>

				<ol>
					<li style="text-align: justify;">Realizar la gestión contable y administrativa, incluyendo la gestión de cobros y pagos.</li>
					<li style="text-align: justify;">Dar respuesta a los requerimientos judiciales y administrativos debidamente fundamentados de cualquier titular del cual
						se tenga información.</li>
					<li style="text-align: justify;">Envío/Recepción de mensajes con fines comerciales, publicitarios y/o de atención al cliente.</li>
					<li style="text-align: justify;">Recolección de datos para el cumplimiento de los deberes que&nbsp;como Responsable de la información y datos personales,
						le corresponde a la empresa.</li>
					<li style="text-align: justify;">Iniciar el proceso de selección de personal junto con los procedimientos señalados para el efecto: entrevista, aplicación
						de pruebas de ingreso, entre otros.</li>
					<li style="text-align: justify;">Efectuar la liquidación del talento humano que superó satisfactoriamente.</li>
					<li style="text-align: justify;">Efectuar la liquidación y el retiro del trabajador.</li>
					<li style="text-align: justify;">Preservar la seguridad de los visitantes a la sede física de&nbsp;<strong>CHESSMAKEIT</strong>&nbsp;y los bienes que
						allí reposan.</li>
					<li style="text-align: justify;">Reportar y almacenar la información tributaria a las entidades competentes.</li>
					<li style="text-align: justify;">Consultar la información del Titular que repose en Centrales de Riesgo o Bancos de Datos de Información Financiera,
						además en caso de incumplir con las obligaciones realizar el reporte en dicha central o base de datos.</li>
					<li style="text-align: justify;">Prestar el servicio de soporte y acompañamiento a los clientes que hayan contratado con&nbsp;<strong>CHESSMAKEIT</strong></li>
					<li style="text-align: justify;">Cualquier otra finalidad que resulte en el desarrollo del contrato o la relación entre el cliente y&nbsp;<strong>CHESSMAKEIT</strong></li>
				</ol>

				<p style="text-align: justify;">&nbsp;</p>

				<p style="text-align: justify;"><strong>CHESSMAKEIT&nbsp;</strong>&nbsp;realiza el presente aviso de privacidad con el fin de solicitar autorización
					a los titulares que entregaron sus datos antes de la expedición del Decreto 1377 de 2013. De igual manera, socializar
					la existencia de sus políticas de tratamiento de la información personal, en la cual se podrán consultar todos los procedimientos
					para que los titulares ejerzan sus derechos ante&nbsp;<strong>CHESSMAKEIT</strong></p>

				<p style="text-align: justify;">Los cambios sustanciales a la política de tratamiento de la información, serán debidamente informados a los titulares.</p>

				<p style="text-align: justify;">&nbsp;</p>

				<p style="text-align: justify;">Atentamente,</p>

				<p style="text-align: justify;"><strong>CHESSMAKEIT</strong></p>
			</div>

			<footer>

				<div class="bottom-logo">
					<p>Diseñado y hospedado por: <a href="https://www.damos.co" target="_blank">DAMOS SOLUCIONES</a></p>
				</div>
			</footer>
		</div>
	</div>
	<script>
		jQuery(document).ready(function () {
				jQuery("#toTop").scrollToTop(1000);
			});
	</script>

</body>

</html>
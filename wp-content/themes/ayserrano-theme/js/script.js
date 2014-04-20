$(document).ready(function() {

console.log('ready');

	$('.carousel').carousel({
  		interval: 8000,
  		pause: "false"
	});

	Shadowbox.init();

	



});


function openPrivacyBox()
{

	
	//console.log('privacyBox');
	// open a welcome message as soon as the window loads
    Shadowbox.open({
        content:    '<div class="shadowbox-nfo">' + strVar + '</div>',
        player:     "html",
        title:      '<div class="shadowbox-txt">Aviso de privacidad</div>',
        height:     350,
        width:      350
    });
}



var strVar="";
strVar += "<p>Con fundamento en los artículos 15 y 16 de la Ley Federal de Protección de Datos Personales en Posesión de Particulares hacemos de su conocimiento que AySerrano Prestadora de Servicios Integrales, S.C., con domicilio en Insurgentes Sur 377, P.H 1, Colonia Hipódromo, Delegación Cuauhtémoc, C.P. 06100, en México, D.F., es responsable de recabar sus datos personales, del uso que se le dé a los mismos y de su protección. Su información personal será utilizada para proveer los servicios que ha solicitado, informarle sobre cambios en los mismos y evaluar la calidad del servicio que le brindamos.";
strVar += " <\/p>";
strVar += "";
strVar += " <p>";
strVar += "Para las finalidades antes mencionadas, requerimos obtener los siguientes datos que podrán ser recabados a través de formato impreso o utilizando medios electrónicos: Nombre completo, Edad, Estado civil, Teléfono fijo y\/o celular, Correo electrónico, Dirección Particular, RFC y\/o CURP, Referencias personales, Ocupación, Profesión, Domicilio laboral.Es importante informarle que usted tiene derecho al Acceso, Rectificación y Cancelación de sus datos personales, a Oponerse al tratamiento de los mismos o a revocar el consentimiento que para dicho fin nos haya otorgado. Nos comprometemos a que los mismos serán tratados bajo las más estrictas medidas de seguridad que garanticen su confidencialidad. Asimismo, le informamos que sus datos personales no serán transferidos a personas distintas al suscrito y de igual manera le informamos que no recabamos datos personales sensibles, también le informamos que no hacemos envío alguno de comunicados y promociones distintos al servicio solicitado. Su información puede ser compartida únicamente con diversas autoridades o dependencias gubernamentales.";
strVar += " <\/p>";
strVar += "";
strVar += " <p>";
strVar += "Importante: Cualquier modificación a este Aviso de Privacidad podrá consultarlo en www.ayserrano.com";
strVar += "<\/p>";

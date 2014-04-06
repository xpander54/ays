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

var strVar='';
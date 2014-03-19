jQuery(document).ready(function() {
	var uploadID = ''; /*setup the var*/
	jQuery('.upimg').click(function() {
		uploadID = jQuery(this).prev('input'); /*grab the specific input*/
		var formfield = jQuery('.upload').attr('name');
		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		return false;
	});

	window.send_to_editor = function(html) {
		var imgurl = jQuery('img',html).attr('src');
		uploadID.val(imgurl); 
		/*assign the value to the input*/
		tb_remove();
        jQuery("#logo-img-prv").attr("src",imgurl).show();
	};
});







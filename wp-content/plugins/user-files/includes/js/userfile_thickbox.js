jQuery(document).ready(function() {

jQuery('#upload_image_button').click(function() {
 formfield = jQuery('#upload_image').attr('name');
 tb_show('','media-upload.php?type=image&TB_iframe=true');
 return false;
});
// send url back to plugin editor

window.send_to_editor = function(html) {
 imgurl = jQuery('img',html).attr('src');
 jQuery('#dfrads_textarea').val(imgurl);
 tb_remove();
}

});
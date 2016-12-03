//	wembo forms JS for frontend




jQuery(document).ready( function() {

	//Date picker initialization
	jQuery('.date-picker').datetimepicker({
		timepicker:false,
		format:'d/m/Y',
		formatDate:'Y/m/d',
		
	});
	
	// Setting submit button position according to label
	// width in inline styles
	jQuery(".wpdevart-forms").each(function() {
	   if(jQuery(this).hasClass("green_inline_small-skin")||jQuery(this).hasClass("green_inline_medium-skin")||jQuery(this).hasClass("green_inline_large-skin")||jQuery(this).hasClass("light_inline_small-skin")||jQuery(this).hasClass("light_inline_medium-skin")||jQuery(this).hasClass("light_inline_large-skin")||jQuery(this).hasClass("default_inline-skin")||jQuery(this).hasClass("dark_inline_small-skin")||jQuery(this).hasClass("dark_inline_medium-skin")||jQuery(this).hasClass("dark_inline_large-skin")||jQuery(this).hasClass("purple_inline_small-skin")||jQuery(this).hasClass("purple_inline_medium-skin")||jQuery(this).hasClass("purple_inline_large-skin")){
		   window.labelWidth= jQuery(this).find(".wpdevart-input-field >.input-text").position().left;
		   jQuery(this).find("button[name='btn_send_form_email']").css("margin-left",labelWidth+2 +"px");
		  
	   }
	});
	
	//Setting form submit loader position from top 
	// according to submit button's height
	jQuery("button[name='btn_send_form_email']").each(function() {
		window.paddingTop = jQuery(this).css("padding-top");
		jQuery(this).parent().find(".form-loader").css("padding-top", paddingTop);
	});

	// Adding/Removing active class to label on focusing in relevant field
	jQuery(document).on("focusin",".wpdevart-input-field .input-field-inner input",function(){
		jQuery(this).parent().parent().find("label").addClass("active");
		jQuery(this).css("text-indent","0px");
	});
	jQuery(document).on("focusout",".wpdevart-input-field .input-field-inner input",function(){
		if(jQuery(this).val()==""){
		jQuery(this).parent().parent().find("label").removeClass("active");
		}
	});
	jQuery(document).on("focusin",".date-picker",function(){
		jQuery(this).parent().parent().find("label").addClass("active");
		jQuery(this).css("text-indent","0px");
	});
	jQuery(document).on("focusout",".date-picker",function(){
		if(jQuery(this).val()==""){
		jQuery(this).parent().parent().find("label").removeClass("active");
		}
	});
	jQuery(document).on("focusin",".wpdevart-textarea textarea",function(){
		jQuery(this).parent().find("label").addClass("active");
		jQuery(this).css("text-indent","0px");
	});
	jQuery(document).on("focusout",".wpdevart-textarea textarea",function(){
		if(jQuery(this).val()==""){
		jQuery(this).parent().find("label").removeClass("active");
		}
	});
	jQuery(document).on("focusin",".wpdevart-input-field .input-number input",function(){
		jQuery(this).parent().parent().find("label").addClass("active");
		jQuery(this).css("text-indent","0px");
	});
	jQuery(document).on("focusout",".wpdevart-input-field .input-number input",function(){
		if(jQuery(this).val()==""||jQuery(this).val()==0){
		jQuery(this).parent().parent().find("label").removeClass("active");
		
		}
	});

});

// Will get file name after uploading file 
function readURLImage(input) {
	var currentUpload =jQuery(input);
	var inputUrl = jQuery(input).val();
	var fileName = inputUrl.split('\\').pop();
	
	// remove file name if no file selected
	jQuery(currentUpload).parent().find(".file-upload-btn + span").text("");
	
	if (input.files && input.files[0]) {// This function will show preview of uploaded image  
		var reader = new FileReader();
		reader.onload = function (e) {
			jQuery(currentUpload).parent().find(".file-upload-btn + span").text(fileName);
		}
		reader.readAsDataURL(input.files[0]);
	}	  
}
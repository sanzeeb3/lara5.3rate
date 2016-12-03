// adding accordion active state
jQuery.noConflict()(function ($) { 
$(".nav-tabs > li.active >ul").fadeIn();
$(".nav-tabs > li >a").on("click", function(){
	$(".nav-tabs > li").each(function(index, value){
		$(".nav-tabs li").find("ul").fadeOut();
	});
	$(this).parent().closest("li").children("ul").fadeIn();
	
});
jQuery('.wpdevart_pro').click(function(){
	alert("If you want to use this feature upgrade to pro version");
	return false;
})
// Show/hide modal windows on button clicks
$(".update-status-btn").on("click", function(){
	setTimeout(function(){
		$(".message").html("Settings has been saved");
		$("#update-status").modal('hide');
	}, 3000);
});
$(".resset-default").on("click", function(){
	setTimeout(function(){
		$(".message").html("Settings have been reset");
		$("#reset-status").modal('hide');
	}, 3000);
});

var color    = $('.colorpicker').val();
hexcolor = $('.hexcolor');
hexcolor.html("Select Color");
$('.colorpicker').on('change', function() {
	$(this).parents().closest(".color-picker").find(".hexcolor").html(this.value); 
});


//On image upload show file name in relevant place
function readURL(input) {
	if (input.files && input.files[0]) {
		var inputUrl = $(input).val();
		var fileName = inputUrl.split('\\').pop();
		var reader = new FileReader();
		reader.onload = function (e) {
			$(input).parents().closest(".image-input-area").find(".preview-image").find("img").attr('src', e.target.result);
			$(input).parents().closest(".image-input-area").find(".preview-image").fadeIn();
			$(input).parents().closest(".image-input-area").find(".image-url").val(fileName);
		}
		reader.readAsDataURL(input.files[0]);
	}
}
    
$(".image-input").on("change",function(){
	readURL(this);
});

/*
	IMPORTANT NOTE: 
	==============
	please conside the scenarion for drag and drop, Let say we are creating a form and need to reposition our fields
	Here we have  two rows in our form  . First row does not have subfields where as row two does have subfields
	ROW 1. label_1 fieldtype_1 isRequired_1
	ROW 2. label_2 fieldtype_2 isRequired_2
			2(I) sublabel_1 isDefaultRadio_2 / isDefaultOption_2 / isCheckedCheckbox_2[]
			2(II) sublabel_2 isDefaultRadio_2 / isDefaultOption_2 / isCheckedCheckbox_2[]
	
	if user want to move Row 2 to Row 1 position the following changes will take place e.g 
	
	ROW 1. label_1 fieldtype_1 isRequired_1
			1(I) sublabel_1 isDefaultRadio_1 / isDefaultOption_1 / isCheckedCheckbox_1[]
			1(II) sublabel_1 isDefaultRadio_1 / isDefaultOption_1 / isCheckedCheckbox_1[]
	ROW 2. label_2 fieldtype_2 isRequired_2
	
	Same case for editing form .  sublabel_1_dbId postion changes but dbId do not change
	where dbId is database id of the field 
	
*/


jQuery( document ).ready(function($) {
	$("#toplevel_page_wpdevart-options").removeClass("wp-not-current-submenu");
	$("#toplevel_page_wpdevart-options").addClass("wp-has-current-submenu");
	$("#toplevel_page_wpdevart-options > a").removeClass("wp-not-current-submenu");
	$("#toplevel_page_wpdevart-options > a ").addClass("wp-has-current-submenu");
	$("#toplevel_page_wpdevart-options .wp-submenu li:nth-child(3)").addClass("current");
	$('[data-toggle="tooltip"]').tooltip();
		
	//This function will handle the names and ordering of elements on drag drop
	$(".sortable").sortable({
        stop : function(event, ui){
			var currentFormBoxes = $(this).parents().find('.addForms .sortable');
			var iterationValue = 1;
			
			$(currentFormBoxes).children("li").each(function(){
					
				///This will change the order number shown on the most left side of the box accordingly
				 $(this).find(".addFormBox > .order >  span").text(iterationValue);
				 
				 //we have to check whter the field was newly created or was of database 
				 // newly created field do not have index[2] in name which referes to database id of the field
				if(iterationValue%2==0){
					$(this).find(".addFormBox").removeClass("bg-odd");
					$(this).find(".addFormBox").addClass("bg-even");
					
				}else {
					$(this).find(".addFormBox").removeClass("bg-even");
					$(this).find(".addFormBox").addClass("bg-odd");
				}
			  	positionName=$(this).find(".addFormBox > .order >  input").attr("name");
			  	postionSplit=positionName.split('_');
				if(postionSplit[2]){ 
				   position="position_"+ iterationValue+'_'+postionSplit[2];
				}else {
					position ="position_"+ iterationValue;
				}
				
				
				// note : add new form page and edit page structure differs for html <select></select> element
				
				//fieldtype 
				
			  	//fieldtypeName = $(this).find(".addFormBox  .form-group  > select ").attr("name"); 
				fieldtypeName = $(this).find(".addFormBox  .form-group  select ").attr("name");
			  	fieldtypeSplit=fieldtypeName.split('_');
			  	if(fieldtypeSplit[2]){
				  fieldtype="fieldtype_"+ iterationValue+'_'+fieldtypeSplit[2];
			  	}else{
				  fieldtype ="fieldtype_"+iterationValue;
			  	}	
				
				
				
			 	//label field
				labelName=$(this).find(".addFormBox  .form-group  > input[placeholder='Label']").attr("name");
				labelNameSplit=labelName.split('_');
				if(labelNameSplit[2]){
					label="label_"+ iterationValue+'_'+labelNameSplit[2];
				}else{
					label ="label_"+iterationValue;
				}	
			  
			   //placeholder field
			  
				placeholderName=$(this).find(".addFormBox   input[data-attr='Placeholder']").attr("name");
				placeholderNameSplit=placeholderName.split('_');
				if(placeholderNameSplit[2]){
					placeholder="placeholder_"+ iterationValue+'_'+placeholderNameSplit[2];
				} else {
					placeholder ="placeholder_"+iterationValue;
				}
			  
			    //required checkbox
				requiredName=$(this).find(".addFormBox .is-required").attr("name");
				requiredNameSplit=requiredName.split('_');
				if(requiredNameSplit[2]){
					required="isRequired_"+ iterationValue+'_'+requiredNameSplit[2];
				} else {
					required ="isRequired_"+iterationValue;
				}
				
			
				//	assing new Names to postion,label,fieldtype,required fields	
				
				//This will change the hidden input filed's name according to order position 
				$(this).find(".addFormBox > .order >  input").attr("name",position); 
				// by tqr
				//$(this).find(".addFormBox  .form-group  > select ").attr("name",fieldtype);
				$(this).find(".addFormBox  .form-group   select ").attr("name",fieldtype);
				$(this).find(".addFormBox  .form-group  > input[placeholder='Label']").attr("name",label);
				$(this).find(".addFormBox   input[data-attr='Placeholder']").attr("name",placeholder);
				$(this).find(".addFormBox .is-required").attr("name",required);
				
				// This will change the ordering and naming of internal elements of main label
			 	$(this).find(".formField").each(function() { 
					var textOrder= $(this).find(".order >  span").text();
					var newText = textOrder.split("-");
					newText [0] = iterationValue;
					$(this).find(".order >  span").text(newText[0]+"-"+newText[1]);	
					if ($(this).find("input[placeholder='Radio Button Label']").length > 0){
						var textPlaceholder= $(this).find("input[placeholder='Radio Button Label']").attr("name");
						var radioPlaceholder = textPlaceholder.split("_");
						if(radioPlaceholder[2]){
							var finalRadio = "sublabel_"+iterationValue+"_"+radioPlaceholder[2];
						} else {
							var finalRadio = "sublabel_"+iterationValue+"[]";
						}
						$(this).find("input[placeholder='Radio Button Label']").attr("name",finalRadio)
					}
					if ($(this).find("input[placeholder='Checkbox Label']").length > 0){
						var textPlaceholder1= $(this).find("input[placeholder='Checkbox Label']").attr("name");
						var checkPlaceholder = textPlaceholder1.split("_");
						if(checkPlaceholder[2]){
						  var finalCheck = "sublabel_"+iterationValue+"_"+checkPlaceholder[2];
						} else {
							var finalCheck = "sublabel_"+iterationValue+"[]";
						}
						$(this).find("input[placeholder='Checkbox Label']").attr("name",finalCheck)
					}
					if ($(this).find("input[placeholder='Option Label']").length > 0){
						var textPlaceholder2= $(this).find("input[placeholder='Option Label']").attr("name");
						var optionPlaceholder = textPlaceholder2.split("_");
						if(optionPlaceholder[2]){
							var finalOption = "sublabel_"+iterationValue+"_"+optionPlaceholder[2];
						} else {
							var finalOption = "sublabel_"+iterationValue+"[]";
						}
						$(this).find("input[placeholder='Option Label']").attr("name",finalOption)
					}
					if($(this).find(':radio').length > 0){
						$(this).find(":radio").each(function() {
							var defaultOption1 = $(this).attr("name");
							var newDefaultOption1 = defaultOption1.split("_");
							var finalDefaultOption1 = newDefaultOption1[0]+"_" +iterationValue;
							$(this).attr("name", finalDefaultOption1);
						});
					}
					if($(this).find(':checkbox').length > 0){
						$(this).find(":checkbox").each(function() {
							var defaultOption = $(this).attr("name");
							var newDefaultOption = defaultOption.split("_");
							var finalDefaultOption = newDefaultOption[0]+"_"+iterationValue+"[]";
							$(this).attr("name", finalDefaultOption);
						});
					}	
            	});	

		     	
				
					 
				++iterationValue;
			});
			
			
			var data = $(this).sortable('toArray');
			jQuery.each(data, function(index, value) {}); 
     	}
    });
 
});

//	This code assings radio buttons value of index of (subfields)
$(document).on("click",".formField :radio",function(e){
	 $(this).val($(this).parents().closest(".formField").index());
});
//	This code assings check buttons value of index of (subfields)
$(document).on("click",".formField :checkbox",function(e){
	 $(this).val($(this).parents().closest(".formField").index());
});


//	This clicking link in tab it will scroll to the relevant section
$(document).on('click', '.nav.nav-tabs >li >ul >li > a[href^="#"]', function(e) {
    var id = $(this).attr('href');
    var $id = $(id);
    if ($id.length === 0) {
        return;
    }
    e.preventDefault();
    var pos = $(id).offset().top-60;
    $('body, html').animate({scrollTop: pos});
});

$('.panel-group').on('shown.bs.collapse', function (e) {
	var offset = $(this).find('.collapse.in').prev('.panel-heading');
    if(offset) {
		$('html,body').animate({
			scrollTop: $(offset).offset().top -20
		}, 500); 
	}
}); 

//This snipper of code will add new form field row to the form
$(document).on("click", ".addNewFormRow", function(e){
	e.preventDefault();
	window.currentFormBoxes = $(this).parents().closest("#addNewForm").find('.addForms .sortable');
	var formsArray =[];
	$(currentFormBoxes).children("li").each(function(){
		var formId = $(this).find(".addFormBox").attr("id");
		var newformId =formId.substring(8);
		formsArray.push(newformId);
		
	});
	var lastIdForm=Math.max.apply(Math,formsArray);
	var newId = ++lastIdForm;
	var className= $(currentFormBoxes).find("li:last-child .addFormBox").attr("class");
	
	if(newId%2==0){
		newClassName = "bg-even";
	} else{
		newClassName = "bg-odd";
	}
	$(currentFormBoxes).append("<li id='li_"+newId+"'><div class='addFormBox " + newClassName  + "' id='addForm-"+newId+"'><div class='col-sm-1 order'><span>"+newId+"</span><input type='hidden' name='position_"+newId+"'/></div><div class='col-sm-3 col-md-3 padding-left-0'><div class='form-group'><input type='text' class='form-control' name='label_"+newId+"' placeholder='Label'/></div><!-- form-group--></div><div class='col-md-3 col-sm-3 padding-left-0 padding-right-0'><input type='text' class='form-control' name='placeholder_"+newId+"' data-attr = 'Placeholder' placeholder='Placeholder'  ></div><div class='col-md-3 col-sm-3 padding-0'><div class='form-group wpdevart-select'><select name='fieldtype_"+newId+"' class='form-control bs-select'><!-- <option>Select Input Type</option> --><option value='text'>Text Field</option><option value='email'>Email</option><option value='url'>URL</option><option value='number'>Number</option><option value='tel'>Telephone Number</option><option value='date'>Date/Calendar</option><option disabled value='file'>Upload File(PRO)</option><option value='password'>Password</option><option value='textarea'>Text Area</option><option value='radio'>Radio Buttons</option><option value='checkbox'>Check Boxes</option><option value='options_list'>Dropdown List</option><option value='heading'>Heading</option><option value='separator'>Separator/Divider</option><option value='recaptcha'>reCaptcha</option><option disabled value='googlemap'>Google Map(PRO)</option></select></div><!-- form-group--></div><div class='col-md-1 col-sm-1 padding-right-0'><div class='form-group'><input type='checkbox'  name='isRequired_"+newId+"' id='addForm-check-"+newId+"' value='1' class='is-required'/><label for='addForm-check-"+newId+"'><span></span> Yes</label><span class='btn-actions pull-right'><a class='btn green removeFormRow' href='javascript:void(0)'><i class='fa fa-trash'></i></a></span></div><!-- form-group--></div><div class='draggable-handle'><i class='fa fa-arrows-v'></i></div><div class='clearfix'></div> <div class='newFields'></div></div><!-- addFormBox --></li>");// this will append the new row to form with updated id and order
	$(".sortable").sortable();
});

// This function will remove the parent row of the current button which was clicked
$(document).on("click", ".removeFormRow", function(e){
	e.preventDefault();
	if ( $('.addForms .sortable').children().length > 1 ) {
		$(this).parents().closest("li").remove();
	}else {
		alert("You can not remove the last element.");
	}
});

// This will show success message on submitting form
$(document).on("click", ".addFormBtn",function(e){
	e.preventDefault();
	$(".successMessage").slideDown("slow");
	setTimeout(function(){
		$(".successMessage").slideUp("slow");        
  	}, 5000);
});

// This function will get the input type selected through dropdown and will add sub fields accordingly
$(document).on("change", ".bs-select",function(e){
	e.preventDefault();
	window.currentDropdown =$(this).parents().closest(".addFormBox").attr("id");
	window.orderValue =$(this).parents().closest(".addFormBox").find(" .order span").html();
	var currentDropdownId =$(".addForms").find(".addFormBox:last-child").attr("id");
	var inputType= $(this).find("option:selected").val();
	var inputText= $(this).find("option:selected").text();
	if(inputText == "Select Input Type"){
		if(!$("#"+currentDropdown).find(".newFields").is(':empty') ){
			$("#"+currentDropdown).find(".formField").remove();
		}
	}
	
	// If selected input type is radio buttons then this will add sub field for radio button
	if(inputType == "radio"){
		if(!$("#"+currentDropdown).find(".newFields").is(':empty') ){
			$("#"+currentDropdown).find(".formField").remove();
		}
		$("#"+currentDropdown).find(".newFields").append("<div  class='formField'><div class='col-sm-1'></div><div class='col-sm-1 col-xs-1 order'><span>"+ orderValue +"-1</span></div><div class='col-xs-5'><div class='form-group'><input type='text' id='formField-"+ orderValue +"-1' class='form-control' name='sublabel_"+ orderValue +"[]' placeholder='Radio Button Label'/></div><!-- form-group--></div><div class='col-sm-3 padding-left-0'><div class='form-group' ><input type='radio'  name='isDefaultRadio_"+ orderValue +"' id='newForm-radio-"+ orderValue +"-1' value='"+ orderValue +"-1'/><label for='newForm-radio-"+ orderValue +"-1'><span></span>Default checked</label></div></div><div class='col-md-2 col-sm-2 col-xs-12 btn-actions'><button class='pull-left btn red removeField'><i class='fa fa-minus'></i></button><button class='pull-left btn blue addNewRadio'><i class='fa fa-plus'></i></button></div><div class='clearfix'></div></div>");
		
	}
	
	// If selected input type is checkbox then this will add sub field for checkbox
	if(inputType == "checkbox"){
		if(!$("#"+currentDropdown).find(".newFields").is(':empty') ){
			$("#"+currentDropdown).find(".googlemapatts").remove();
			$("#"+currentDropdown).find(".googlemaphr").remove();
			$("#"+currentDropdown).find(".formField").remove();
		}
		$("#"+currentDropdown).find(".newFields").append("<div  class='formField'><div class='col-sm-1'></div><div class='col-sm-1 col-xs-1 order'><span>"+ orderValue +"-1</span></div><div class='col-xs-5'><div class='form-group'><input type='text' id='formField-"+ orderValue +"-1' class='form-control' name='sublabel_"+ orderValue +"[]' placeholder='Checkbox Label'/></div><!-- form-group--></div><div class='col-sm-3 padding-left-0'><div class='form-group'><input type='checkbox'  name='isCheckedCheckbox_"+ orderValue +"[]' id='newForm-check-"+ orderValue +"-1' value=''"+ orderValue +"-1''/><label for='newForm-check-"+ orderValue +"-1'><span></span>Default checked</label></div></div><div class='col-md-2 col-sm-2 col-xs-12 btn-actions'><button class='pull-left btn red removeField'><i class='fa fa-minus'></i></button><button class='pull-left btn blue addNewCheck'><i class='fa fa-plus'></i></button></div><div class='clearfix'></div></div>");
	  
	  $('[data-toggle="tooltip"]').tooltip();
	}
	
	// If selected input type is dropdown list then this will add sub field for dropdown options
	if(inputType == "options_list"){
		if(!$("#"+currentDropdown).find(".newFields").is(':empty') ){
            $("#"+currentDropdown).find(".googlemapatts").remove();
			$("#"+currentDropdown).find(".googlemaphr").remove();
			$("#"+currentDropdown).find(".formField").remove();
		}
		$("#"+currentDropdown).find(".newFields").append("<div  class='formField'><div class='col-sm-1'></div><div class='col-sm-1 col-xs-1 order'><span>"+ orderValue +"-1</span></div><div class='col-xs-5'><div class='form-group'><input type='text' id='formField-"+ orderValue +"-1' class='form-control' name='sublabel_"+ orderValue +"[]' placeholder='Option Label'/></div><!-- form-group--></div><div class='col-sm-3 padding-left-0'><div class='form-group' ><input type='radio'  name='isDefaultOption_"+ orderValue +"' id='newForm-option-"+ orderValue +"-1' value=''"+ orderValue +"-1''/><label for='newForm-option-"+ orderValue +"-1'><span></span>Default selected</label></div></div><div class='col-md-2 col-sm-2 btn-actions'><button class='pull-left btn red removeField'><i class='fa fa-minus'></i></button><button class='pull-left btn blue addNewOption'><i class='fa fa-plus'></i></button></div><div class='clearfix'></div></div>");
	  
	  $('[data-toggle="tooltip"]').tooltip();
	}
    
    // If selected input type is googlemap then this will add sub field for googlemap options
	if(inputType == "googlemap"){
		if(!$("#"+currentDropdown).find(".newFields").is(':empty') ){
            $("#"+currentDropdown).find(".googlemapatts").remove();
			$("#"+currentDropdown).find(".googlemaphr").remove();
			$("#"+currentDropdown).find(".formField").remove();
		}
		$("#"+currentDropdown).find(".newFields").append("<div class='googlemapatts'><div class='col-sm-1 order'></div><div class='col-sm-3 col-md-3 padding-left-0'><input type='text' id='formField-width-"+ orderValue +"-1' class='form-control' name='mapwidth_"+ orderValue +"[]' placeholder='Width i.e. 100% or 400px' /></div><div class='col-md-3 col-sm-3 padding-right-0 padding-left-0'><input type='text' id='formField-height-"+ orderValue +"-1' class='form-control' name='mapheight_"+ orderValue +"[]' placeholder='Height i.e. 400px'  /></div><div class='col-md-3 col-sm-3  padding-right-0'><input type='text' id='formField-zoom-"+ orderValue +"-1' class='form-control' name='mapzoom_"+ orderValue +"[]' placeholder='Zoom i.e. 12' /></div><div class='col-md-1 col-sm-1 padding-right-0'></div><div class='clearfix'></div></div><hr class='googlemaphr' /><div  class='formField'><div class='col-sm-1'></div><div class='col-sm-1 col-xs-1 order'><span>"+ orderValue +"-1</span></div><div class='col-xs-3'><div class='form-group'><input type='text' id='formField-"+ orderValue +"-1' class='form-control' name='submaptitle_"+ orderValue +"[]' placeholder='Title'/></div><!-- form-group--></div><div class='col-xs-3 padding-left-0'><div class='form-group'><input type='text' id='formField-lon-"+ orderValue +"-1' class='form-control' name='submaplon_"+ orderValue +"[]' placeholder='Longitude'/></div><!-- form-group--></div><div class='col-xs-3 padding-left-0 padding-right-25'><div class='form-group'><input type='text' id='formField-lat-"+ orderValue +"-1' class='form-control' name='submaplat_"+ orderValue +"[]' placeholder='Latitude'/></div><!-- form-group--></div><div class='col-xs-2 col-sm-1 btn-actions btngm'><button class='pull-left btn red removeField'><i class='fa fa-minus'></i></button><button class='pull-left btn blue addNewMap'><i class='fa fa-plus'></i></button></div><div class='clearfix'></div></div>");
	  
	  $('[data-toggle="tooltip"]').tooltip();
	}
	if(inputType == "textarea" || inputType == "text" || inputType == "email" || inputType == "url" || inputType == "number"  || inputType == "date"  || inputType == "password" || inputType == "file" ){
		if(!$("#"+currentDropdown).find(".newFields").is(':empty') ){
			$("#"+currentDropdown).find(".formField").remove();
		}
	}
});

//	This code adds a new subfield row for radio button 
$(document).on("click", ".addNewRadio", function(e){
	e.preventDefault();
	currentOrder1 =$(this).parents().closest(".addFormBox").attr("id");
	orderValue1 = currentOrder1.substring(8);
	var lastRadioId = $(this).parents().closest(".newFields").children(".formField").last().find(".order > span").text();
	var newRadioId = lastRadioId.split("-");
	var newRadioIdConcat = ++newRadioId[1];
	$(this).closest(".newFields").append("<div  class='formField'><div class='col-sm-1'></div><div class='col-sm-1 col-xs-1 order'><span>"+ newRadioId[0]+"-"+ newRadioIdConcat +"</span></div><div class='col-xs-5'><div class='form-group'><input type='text' id='formField-"+ orderValue1 +"-"+ newRadioIdConcat +"' class='form-control' name='sublabel_"+ newRadioId[0]+"[]' placeholder='Radio Button Label'/></div><!-- form-group--></div><div class='col-sm-3 padding-left-0'><div class='form-group'><input type='radio'  name='isDefaultRadio_"+ newRadioId[0]+"' id='newForm-radio-"+ orderValue1 +"-"+ newRadioIdConcat +"' value='"+ newRadioId[0]+"-"+ newRadioIdConcat +"'/><label for='newForm-radio-"+ orderValue1 +"-"+ newRadioIdConcat +"'><span></span>Default checked</label></div></div><div class='col-md-2 col-sm-2 col-xs-12 btn-actions'><button class='pull-left btn red removeField'><i class='fa fa-minus'></i></button><button class='pull-left btn blue addNewRadio'><i class='fa fa-plus'></i></button></div><div class='clearfix'></div></div>");		
});

//	This code adds a new subfield row for checkbox 
$(document).on("click", ".addNewCheck", function(e){
	e.preventDefault();
	currentOrder2 =$(this).parents().closest(".addFormBox").attr("id");
	orderValue2 = currentOrder2.substring(8);
	var lastCheckId = $(this).parents().closest(".newFields").children(".formField").last().find(".order > span").text();
	var newCheckId = lastCheckId.split("-");
	var newCheckIdConcat = ++newCheckId[1];
	$(this).closest(".newFields").append("<div  class='formField'><div class='col-sm-1'></div><div class='col-sm-1 col-xs-1 order'><span>"+ newCheckId[0] +"-"+ newCheckIdConcat +"</span></div><div class='col-xs-5'><div class='form-group'><input type='text' id='formField-"+ orderValue2 +"-"+ newCheckIdConcat +"' class='form-control' name='sublabel_"+ newCheckId[0] +"[]' placeholder='Checkbox Label'/></div><!-- form-group--></div><div class='col-sm-3 padding-left-0'><div class='form-group'><input type='checkbox'  name='isCheckedCheckbox_"+ newCheckId[0] +"[]' id='newForm-check-"+ orderValue2 +"-"+ newCheckIdConcat +"' value='"+ newCheckId[0] +"-"+ newCheckIdConcat +"'/><label for='newForm-check-"+ orderValue2 +"-"+ newCheckIdConcat +"'><span></span>Default checked</label></div></div><div class='col-md-2 col-sm-2 col-xs-12 btn-actions'><button class='pull-left btn red removeField'><i class='fa fa-minus'></i></button><button class='pull-left btn blue addNewCheck'><i class='fa fa-plus'></i></button></div><div class='clearfix'></div></div>");		
});

//	This code adds a new subfield row for drop-down 
$(document).on("click", ".addNewOption", function(e){
	e.preventDefault();
	currentOrder3 =$(this).parents().closest(".addFormBox").attr("id");
	orderValue3 = currentOrder3.substring(8);
	var lastOptionId = $(this).parents().closest(".newFields").children(".formField").last().find(".order > span").text();
	var newOptionId = lastOptionId.split("-");
	var newOptionConcat = ++newOptionId[1];
	$(this).closest(".newFields").append("<div  class='formField'><div class='col-sm-1'></div><div class='col-sm-1 col-xs-1 order'><span>"+ newOptionId[0] +"-"+ newOptionConcat +"</span></div><div class='col-xs-5'><div class='form-group'><input type='text' id='formField-"+ orderValue3 +"-"+ newOptionConcat +"' class='form-control' name='sublabel_"+ newOptionId[0] +"[]' placeholder='Option Label'/></div><!-- form-group--></div><div class='col-sm-3 padding-left-0'><div class='form-group'><input type='radio'  name='isDefaultOption_"+ newOptionId[0] +"' id='newForm-option-"+ orderValue3 +"-"+ newOptionConcat +"' value='"+ newOptionId[0] +"-"+ newOptionConcat +"'/><label for='newForm-option-"+ orderValue3 +"-"+ newOptionConcat +"'><span></span>Default selected</label></div></div><div class='col-md-2 col-sm-2 col-xs-12 btn-actions'><button class='pull-left btn red removeField'><i class='fa fa-minus'></i></button><button class='pull-left btn blue addNewOption'><i class='fa fa-plus'></i></button></div><div class='clearfix'></div></div>");	
});
    
//	This code adds a new subfield row for google map
$(document).on("click", ".addNewMap", function(e){
	e.preventDefault();
	currentOrder4 =$(this).parents().closest(".addFormBox").attr("id");
	orderValue4 = currentOrder4.substring(8);
	var lastMapId = $(this).parents().closest(".newFields").children(".formField").last().find(".order > span").text();
	var newMapId = lastMapId.split("-");
	var newMapConcat = ++newMapId[1];
	$(this).closest(".newFields").append("<div  class='formField'><div class='col-sm-1'></div><div class='col-sm-1 col-xs-1 order'><span>"+ newMapId[0] +"-"+ newMapConcat +"</span></div><div class='col-xs-3'><div class='form-group'><input type='text' id='formField-"+ orderValue4 +"-"+ newMapConcat +"' class='form-control' name='submaptitle_"+ newMapId[0] +"[]' placeholder='Title'/></div><!-- form-group--></div><div class='col-xs-3 padding-left-0'><div class='form-group'><input type='text' id='formField-lon-"+ orderValue4 +"-"+ newMapConcat +"' class='form-control' name='submaplon_"+ newMapId[0] +"[]' placeholder='Longitude'/></div><!-- form-group--></div><div class='col-xs-3 padding-left-0 padding-right-25'><div class='form-group'><input type='text' id='formField-lat-"+ orderValue4 +"-"+ newMapConcat +"' class='form-control' name='submaplat_"+ newMapId[0] +"[]' placeholder='Latitude'/></div><!-- form-group--></div><div class='col-xs-2 col-sm-1 btn-actions btngm'><button class='pull-left btn red removeField'><i class='fa fa-minus'></i></button><button class='pull-left btn blue addNewMap'><i class='fa fa-plus'></i></button></div><div class='clearfix'></div></div>");	
});

//	This code runs as user click on removing(just frontend not backend(database))  a mainField row 
$(document).on("click", ".removeField", function(e){
	e.preventDefault();
	var closestNewField = $(this).parents().closest(".newFields");  
	$(this).parents().closest(".formField").remove();	
	var checkedRadio = $(closestNewField).find(":radio:checked");
	var newValue = $(checkedRadio).parents().closest(".formField").index();
	$(checkedRadio).val(newValue);
	$(closestNewField).find(":checkbox:checked").each(function(index, element) {
		var newValue = $(this).parents().closest(".formField").index();  
		$(this).val(newValue);
	});  
});

// This function will stop submitting form on enter key 	
function stopRKey(evt) { 
  var evt = (evt) ? evt : ((event) ? event : null); 
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null); 
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;} 
} 

document.onkeypress = stopRKey;
$("input[data-toggle='tooltip']").on('focus', function() {
    $(this).tooltip('show');
});

//	If url redirection after email submission is required , add 'required' attribute,else remove 'required' attribute
$("input:radio[name=isRequiredEmailSubmitUrlRedirect]").on("click",function(){
	if($(this).val()==0){
		$("input[name=email_submit_redirect_url]").removeAttr("required");
	}else {
		$("input[name=email_submit_redirect_url]").attr("required","required");
	}
});

//
$("input:checkbox[name=isRequiredAutoResponder]").on("change",function(){
    if (this.checked){
		alert('NOTE: There must be atleast one input type as "Email" on this form to send auto responder.');
		$('.autoResponderToggle').slideToggle();
    } else {
		$('.autoResponderToggle').slideToggle();
    }
});
//
$("input:checkbox[name=isRequiredEmailSubmitUrlRedirect]").on("change",function(){
    if (this.checked){
		
		$('.email_submit_redirect_url').slideToggle();
		$('.after_submit_hide_form').slideToggle();
    } else {
		$('.email_submit_redirect_url').slideToggle();
		$('.after_submit_hide_form').slideToggle();
    }
});



// Hide  .notify-message.notify-error (frontend)
$(document).ready(function(e) {
    setTimeout(function(){
    	$(".notify-message.notify-error").slideUp(2000);
	}, 2000);
});


});
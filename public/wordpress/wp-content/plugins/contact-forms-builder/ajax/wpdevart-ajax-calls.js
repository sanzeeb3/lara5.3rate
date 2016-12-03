/**
 *	This file  sends ajax calls to  ajax/wpdevart-ajax-handling.php for form ,fields,subfields deletion
 *  please note that if you add a new slug page , mention it in  ajax/wpdevart-ajax-handling.php , so that ajax call for that page works
*/	

jQuery(document).ready(function($) {
	/*	
		=============
		Deleting form
		=============  
	*/
	
	// DONOT PROCESS ON ENTER KEY SUBMIT
	jQuery(document).on("keypress", ":input:not(textarea)", function(event) {
    		return event.keyCode != 13;
	});
	
	//	As user clicks on delete from , the id of the form [to be deleted] is assined to 
	//	data-attribute of confirm-delete button
	jQuery(document).on("click", ".assingIdToConfirmDelBtn", function(e) {
		var delFormId = jQuery(this).attr('data-attr');
		jQuery("#delFormOk").attr("data-attr",delFormId);
		
	});
	
	//	as user clicks on cofirm button for deleting  form  , the follwing script runs
	jQuery(document).on("click", "#delFormOk", function(e) {
		e.preventDefault();
		jQuery('#loader-icon-delete-form').show();
		var formId = jQuery(this).attr('data-attr'); 
		var wp_nonce = jQuery('#wpdevart_form_list_actions_nonce_name').val();
		
		/*
		NOTE :
		====== 
		action": 'get_post_information' will call AJAX_get_post_information() function 
		defined in ajax/wpdevart_ajax_handling.php file
		*/ 
		jQuery.ajax({
			
			// IMPORTATN : url:ajaxurl is wordpress built-in functionality for using ajax
			url:ajaxurl, //do not change this
			data:{"delForm":"1","formId":formId,"wpdevart_form_list_actions_nonce_name":wp_nonce,"action": 'get_post_information'},
			success: function(data) {
				jQuery('#loader-icon-delete-form').hide();
				
				currentUrl= window.location.href;
				jQuery("#addForm-"+formId).remove();
				jQuery("#delete-form").modal('hide');
				jQuery("#form-deleted-modal").modal('show');
				
				setTimeout(function(){ 
					jQuery("#form-deleted-modal").modal('hide');
				}, 1000);
				
				
				
				if ( jQuery('.addFormBox').length >= 1 ){
					e.preventDefault();
				} else {
					// get all url parts splited by & , to get the same structure like $_GET
					var parts = window.location.search.substr(1).split("&");
					var $_GET = {};
					for (var i = 0; i < parts.length; i++) {
						var temp = parts[i].split("=");
						$_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
					}
					
					//	$_GET here is js var
					if($_GET['cpage']) {
						if($_GET['cpage'] > 1 ) {

							var findStr = "cpage="+$_GET['cpage'];
							var newPageNumber = parseInt($_GET['cpage'])-1;
							var replaceWithStr = "cpage="+newPageNumber;
							var newUrl = currentUrl.replace(findStr,replaceWithStr);
							location.href = newUrl;
						} else {
							location.href = currentUrl;
						}
					} else {
						location.href = currentUrl;
					}
				} // if ( jQuery('.addFormBox').length >= 1 )
			} // success: function(data) 
		}); // jQuery.ajax({
	}); // jQuery(document).on("click", "#delFormOk", function(e) 
	
	
	/*	
		==================
		Deleting form field
		===================
	*/
	
	//	As user clicks on delete form field, the id of the form field[to be deleted] is assined to 
	//	data-attribute of confirm-delete button
	jQuery(document).on("click", ".deleteMainField", function(event){
		var btnName = jQuery(this).attr('name');//  e.g ,	deleteMainField_1
		var btnNameAry = btnName.split("_");// e.g ,	deleteMainField,1
		var fieldId = btnNameAry[1];// e.g	,	1		
		jQuery("#delMainFieldOk").attr("data-attr",fieldId);
		
	});
		
	//	as user clicks on  deleting  form  field after confirmation  the follwing script runs
	jQuery(document).on("click", "#delMainFieldOk", function(event) {
		event.preventDefault();
		jQuery('#loader-icon-delete-field-modal').show();
		var countFieldDivs = jQuery("ul.sortable.list-unstyled > li").length;
		
		var fieldId = jQuery(this).attr('data-attr');
		var delBtn = "delMainField_"+fieldId;
		var currentBoxes = jQuery("["+delBtn+"]").parents().closest(".addForms .sortable");
        var wp_nonce = jQuery("#wpdevart_form_add_edit_new_nonce_name").val();
		//	Delete form field only if its there are more than 1 fields
		if ( countFieldDivs > 1 ) {
			// NOTE :event.preventDefault() is been commented , becuase new field not appending if field is deleted
		
			/*
			NOTE :
			====== 
			action": 'get_post_information' will call AJAX_get_post_information() function 
			defined in ajax/wpdevart_ajax_handling.php file
			*/
			
			jQuery.ajax({
				// IMPORTATN : url:ajaxurl is wordpress built-in functionality for using ajax
				url:ajaxurl,//	do not change this
				data:{"deleteMainField":"1","fieldId":fieldId,"wpdevart_form_add_edit_new_nonce_name":wp_nonce,"action": 'get_post_information'},
				success: function(data){
					jQuery('#loader-icon-delete-field-modal').hide();
					jQuery("#delete-field-modal").modal("hide");
					jQuery("#deleted-field-modal").modal("show");
					
					setTimeout(function() {jQuery("#deleted-field-modal").modal("hide");}, 1000);
					jQuery("[name=delMainField_"+fieldId+"]").parents().closest(".addFormBox").parent().remove();
				}
			});
		} else {
			alert("Cant remove ! There should be al least one field in form ");
		}
	});
    
    
    
    /*	
		==================
		Deleting google map sub fields
		===================
	*/
	
	//  deleting google map fields row
    //	as user clicks on  deleting  google map fields row after confirmation  the follwing script runs
	jQuery(document).on("click", ".delGMSubFields", function(event){
		var btnName = jQuery(this).attr('name');//  e.g ,	delGMSubField_1
		var btnNameAry = btnName.split("_");// e.g ,	delGMSubFields, 1
		var fieldId = btnNameAry[1];// e.g	,	1
		
		jQuery("#delGMSubFields").attr("data-attr",fieldId);
		
	});
    
	jQuery(document).on("click", "#delGMSubFields", function(event) {
		event.preventDefault();
		jQuery('#loader-icon-delete-gm-subfield-modal').show();
		var countSubFields = jQuery("ul.sortable.list-unstyled > li").length;
		
		var fieldId = jQuery(this).attr('data-attr');
		var delBtn = "delGMSubField_"+fieldId;
		var currentBoxes = jQuery("["+delBtn+"]").parents().closest(".newFields .formField");
		var wp_nonce = jQuery("#wpdevart_form_add_edit_new_nonce_name").val();
		//	Delete form field only if its there are more than 1 fields
		if ( countSubFields > 1 ) {
			// NOTE :event.preventDefault() is been commented , becuase new field not appending if field is deleted
		
			/*
			NOTE :
			====== 
			action": 'get_post_information' will call AJAX_get_post_information() function 
			defined in ajax/wpdevart_ajax_handling.php file
			*/
			
			jQuery.ajax({
				// IMPORTATN : url:ajaxurl is wordpress built-in functionality for using ajax
				url:ajaxurl,//	do not change this
				data:{"delGMSubFields":"1","fieldId":fieldId,"wpdevart_form_add_edit_new_nonce_name":wp_nonce,"action": 'get_post_information'},
				success: function(data){
					jQuery('#loader-icon-delete-gm-subfield-modal').hide();
					jQuery("#delete-gm-subfield-modal").modal("hide");
					jQuery("#deleted-gm-subfield-modal").modal("show");
					
					setTimeout(function() {jQuery("#deleted-gm-subfield-modal").modal("hide");}, 1000);
					jQuery("[name=delGMSubField_"+fieldId+"]").parents().closest(".formField").remove();
				}
			});
		} else {
			alert("Cant remove! There should be atleast one sub-field for google map field");
		}
	});
	
	/*	
		=========================
		Deleting field's subfield
		=========================
	*/
	
	
	//	As user clicks on delete  field's subfield, the id of the  field's subfield [to be deleted] is assined to 
	//	data-attribute of confirm-delete button
	jQuery(document).on("click", ".deleteSubField", function(e){
		var clickedButton = jQuery(this);
		var btnName = jQuery(this).attr('name');//deleteSubField_1
		var btnNameAry = btnName.split("_");//deleteSubField,1
		var subFieldId = btnNameAry[1];//1
		jQuery("#delSubFieldOk").attr("data-attr",subFieldId);
	});
	
	
	/*
	As user clicks on deleting field's subfield ,after confirmation the follwing script runs
	e.g ,(deleteing Gender field's subfield male/female)
	*/
	
	jQuery(document).on("click", "#delSubFieldOk", function(e){
		e.preventDefault();
		jQuery('#loader-icon-delete-subfield-modal').show();
		
		var subFieldId = jQuery(this).attr('data-attr');
		var delBtn = "delSubField_"+subFieldId;
		var wp_nonce = jQuery("#wpdevart_form_add_edit_new_nonce_name").val();
		var currentBoxes1 = jQuery(".deleteSubField[name='"+delBtn+"']").parents().closest(".newFields");
		//	delete subfield only if there are more than one subfields related to the parent field(mainField)
		if ( jQuery(currentBoxes1).children().length > 1 ) {
			/*
			NOTE :
			====== 
			action": 'get_post_information' will call AJAX_get_post_information() function 
			defined in ajax/wpdevart_ajax_handling.php file
			*/
			
			jQuery.ajax({
				// IMPORTATN : url:ajaxurl is wordpress built-in functionality for using ajax
				url:ajaxurl,//	do not change this
				data:{"deleteSubField":"1","subfield_id":subFieldId,"wpdevart_form_add_edit_new_nonce_name":wp_nonce,"action": 'get_post_information'},
				success: function(data){
					jQuery('#loader-icon-delete-subfield-modal').hide();
					jQuery("#delete-subfield-modal").modal("hide");
					jQuery("#deleted-subfield-modal").modal("show");
					
					setTimeout(function() {jQuery("#deleted-subfield-modal").modal("hide");}, 2000);
					jQuery(".deleteSubField[name='"+delBtn+"']").parents().closest(".formField").remove();
					event.preventDefault();		
				}
			});
		} else {
			alert("Can't remove! There should be al least one child field of parent field.");
		}  			  
	}); 
	//	duplicate form 
	
	//	As user clicks on duplicate from , the id of the form [to be duplicated] is assined to 
	//	data-attribute of confirm duplicate button
	jQuery(document).on("click", "[name='duplicate_form']", function(e) {
		var duplicateFormId = jQuery(this).attr('data-attr');
		jQuery("#duplicateFormOk").attr("data-attr",duplicateFormId);
	});
	
	jQuery(document).on("click", "#duplicateFormOk", function(e) {
		jQuery('#loader-icon-duplicate-form-modal').show();
		var formId = jQuery(this).attr('data-attr');
		var loadingTimeout = 0;
		jQuery.ajax({
			// IMPORTATN : url:ajaxurl is wordpress built-in functionality for using ajax
			url:ajaxurl, //do not change this
			method:"post",
			data:{"duplicateForm":"1","formId":formId,"action": 'get_post_information'},
			beforeSend:function(){	
				 jQuery('#duplicate-form-modal').modal('hide');
				  
			   /**  
				  // show loading image only if ajax call has taken more than x seconds
				  loadingTimeout = setTimeout(function() 
				  {
					 jQuery('#wait-msg-modal').modal('show');
				  }, 2000); // 1e3 = 1000
			   **/
			},
			success: function(data){ 
				jQuery('#loader-icon-duplicate-form-modal').hide();
				/***
				//clearTimeout(loadingTimeout);
				//jQuery('#wait-msg-modal').modal('hide');
			    ***/
				if(jQuery.isNumeric(data) && data == 0) {
					 jQuery('#form-duplicated-modal').modal('show');
					 setTimeout(function(){
						location.href = window.location.href;
					},1000);
					 
				} else {
					alert("something went wrong ,please try later thanks");
				}
			}
		});
	});
	
	
	// Delete all submissions for a given form
	 jQuery(document).on("click",'#del-all-records-ok',function(e){
		e.preventDefault();
		
		jQuery('#loader-icon-delete-all-submissions').show();
		
		var formId= jQuery("#del-all-records-ok").attr("data-attr");
		var wp_nonce= jQuery("#wpdevart_form_submision_update_name").val();
		var url = window.location.href;
		var urlToRedirect = url.split("&cpage=");
		
		/*
		NOTE :
		====== 
		action": 'get_post_information' will call AJAX_get_post_information() function 
		defined in ajax/wpdevart_ajax_handling.php file
		*/ 
		jQuery.ajax({
			// IMPORTATN : url:ajaxurl is wordpress built-in functionality for using ajax
			url:ajaxurl, //do not change this
			data:{"delAllSubmissions":"1","formId":formId,"wpdevart_form_submision_update_name":wp_nonce,"action": 'get_post_information'},
			method:"post",
			success: function(data) {
				jQuery('#loader-icon-delete-all-submissions').hide();
				if(data > 0) {
					//	data=no.of submissions deleted
					jQuery('#del-all-records').modal('hide');
					// show success message
					setTimeout(function(){ jQuery('#record-deleted').modal('show'); },500);
					// hide message
					setTimeout(function(){ jQuery('#record-deleted').modal('hide'); }, 3000);
					setTimeout(function(){ location.href = urlToRedirect[0];},2000);
				} else {
					console.log(data);
					alert("There was some error in deleting the record");
				}
			}
		});
		 e.preventDefault();
     });
	
	  
	 // Assign ids for deleting  record of submited form TO delete b button
	 jQuery(document).on("click",'.delFormRecord',function(e){
		e.preventDefault();
		jQuery("#del-record-ok").attr("attachment-exists",jQuery(this).attr("attachment-exists")) ;
		jQuery("#del-record-ok").attr("data-id",jQuery(this).attr("data-id"));
     });
	 
	 //Deleting  record of submited form
	 
	 jQuery(document).on("click",'#del-record-ok',function(e){
		e.preventDefault();
		jQuery('#loader-icon-delete-submission').show();
		currentElement = jQuery(this);
		submitedFormRecordId = jQuery(this).attr("data-id");
		attachmentExists = jQuery(this).attr("attachment-exists");
		var wp_nonce= jQuery("#wpdevart_form_submision_update_name").val();
		/*
		NOTE :
		====== 
		action": 'get_post_information' will call AJAX_get_post_information() function 
		defined in ajax/wpdevart_ajax_handling.php file
		*/ 
		jQuery.ajax({
			// IMPORTATN : url:ajaxurl is wordpress built-in functionality for using ajax
			url:ajaxurl, //do not change this
			data:{"delSubmitedFormRecord":"1","submitedFormRecordId":submitedFormRecordId,"wpdevart_form_submision_update_name":wp_nonce,"attachmentExists":attachmentExists,"action": 'get_post_information'},
			method:"POST",
			success: function(data){
				jQuery('#loader-icon-delete-submission').hide();
				if(data == 1){
					currentUrl= window.location.href;
					// hide row in table
					jQuery("tr#"+submitedFormRecordId).remove();
					// hide modal box 
					jQuery('#delete-record').modal('hide');
					// show success message
					setTimeout(function(){ jQuery('#record-deleted').modal('show'); }, 500);
					// hide message
					setTimeout(function(){ jQuery('#record-deleted').modal('hide'); },3000);
					
					var  rowCount= jQuery('#show_submissions_table tr').length;
					if(rowCount > 1 ){
					 	e.preventDefault();
					 } else { 
					    // get all url parts splited by & , to get the same structure like $_GET
					 	var parts = window.location.search.substr(1).split("&");
						var $_GET = {};
						for (var i = 0; i < parts.length; i++) {
							  var temp = parts[i].split("=");
							  $_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
						}
					 	 
						//	$_GET here is js var
						if($_GET['cpage']) {
						    if($_GET['cpage'] > 1 ) {
								var findStr = "cpage="+$_GET['cpage'];
								var newPageNumber = parseInt($_GET['cpage'])-1;
								var replaceWithStr = "cpage="+newPageNumber;
								var newUrl = currentUrl.replace(findStr,replaceWithStr);
								jQuery('#record-deleted').modal('show');
							
								setTimeout(function(){ 
								location.href = newUrl;
								
							 	}, 1000);
							} else {
								jQuery('#record-deleted').modal('show');
								setTimeout(function(){ 
								location.href = currentUrl;
							 }, 1000);
							}
						} else {
							jQuery('#record-deleted').modal('show');
								setTimeout(function(){ 
								location.href = currentUrl;
							 }, 1000);
							 
						} // if($_GET['cpage']) {
					 }
				} else {
					console.log(data);
					alert("There was some Error deleting the Record ");
				} // if(data == 1){
			} //success: function(data){
		}); // ajax
		 e.preventDefault();
     }); // jQuery(document).on("click",'#del-record-ok',function(e){
	 
	 // save form styling tab options 
	  jQuery(document).on("submit", "form[name='styling_tab_form']", function(e) {
		  
		 // there are two possibilities in which form can be submited ,
		// 1.ajax based , which update settings 
	   	// 2.refresh , which handles with restoring settings to their default
		 var btn = jQuery(document.activeElement);
		 name =  btn.attr("name");
		 if(name == "btn_save_form_style") {
		  e.preventDefault();
		  // show loader icone
		  jQuery('.loader-icon-styling').css("display", "inline"); 
		  
		  //currentSubmitedForm = jQuery(this);
		  formId = jQuery(this).attr("data-attr");
		  
		  var options = { 
						 url:ajaxurl,        // override for form's 'action' attribute 
						 data:{'save_form_style':'1','formId':formId,'action':'get_post_information'},
						 success:showResponse	 , // post-submit callback 
					    }; 
			 
			// inside event callbacks 'this' is the DOM element so we first 
			// wrap it in a jQuery object and then invoke ajaxSubmit 
			jQuery(this).ajaxSubmit(options);
			
			function showResponse(responseText, statusText, xhr, $form) {
				jQuery('.loader-icon-styling').css("display", "none"); 
				if(responseText == 1) {
					 isDirty = 0;
					 changesSaved = 1;
					 
					 jQuery('#update-status').modal('show');
		 			 setTimeout(function() { 
						jQuery("#update-status").modal('hide');
		  			 }, 3000);
				} else {
					alert("There was some problem saving the record");
				}
			}
			
		  return false;
		 } //if(name == "btn_save_form_style") {
	  }); //jQuery(document).on("submit", "form[name='styling_tab_form']", function(e) 
}); // jQuery(document).ready(function($) {
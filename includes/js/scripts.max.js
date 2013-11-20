jQuery(function($){

	$("#pig_image_desc").focus(function() {
		if($(this).val() == "Describe your image") {
			$(this).val("");
		}
	});
	$("#pig_gallery_submission").submit(function() {
		$(this).attr("disabled", "disabled");
		return true;
	});

	$.validator.addMethod("notEqual", function(value, element, param) {
		return this.optional(element) || value != param;
	});

	$("#pig_image_desc").focus(function() {
		if($(this).val() == "Image Description") {
			$(this).val("");
		}
	});
	$(".pig_checkbox_wrapper a").click(function(e) {
		e.preventDefault();
		if($(this).hasClass("okay")) {
			$(this).removeClass("okay");
			$(this).parent().find("input").val(0);
		} else {
			$(this).addClass("okay");
			$(this).parent().find("input").val(1);
		}
	});
	$("#pig_gallery_submission").validate({
		ignore: ".ignore",
		debug: false,
		success: function(label) {
			label.addClass("valid");
		},
		rules: {
			pig_image_name: {
				required: true,
				maxlength: 55
			},
			pig_image_desc: {
				required: true,
				minlength: 15,
				maxlength: 1000,
				notEqual: "Image Description"
			},
			pig_image_file: {
				required: true
			},
			pig_agreement: {
				required: true,
				notEqual: 0
			},
			pig_3d_embed_type: {
				required: false
			},
			pig_3d_url: {
				required: false
			}
		},
		submitHandler: function(form) {
			$("#pig_submit").attr("disabled", "disabled");
			form.submit();
		}
	});

	$('select[name="pig_3d_embed_type"]').change(function(){
		var val = $(this).val();
		var $target = $('input[name="pig_3d_url"]');
		if( val ){
			$target.show();
		} else {
			$target.hide();
		}
	}).change();
});
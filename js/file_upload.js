		function show_upload_form(action, area_id, params) {
			if ($("#upload_form").length>0) {
				return false;
			}

			if (typeof params == 'undefined') {
				params="";
			}

			if (action=="documents") {
				accept_types="";
			} else {
				accept_types="image/*";
			}

			txt='<div id="upload_form">';
			txt+='<div class="form-container">';
			txt+='<div>';
			txt+='<form action="php/upload_file.php" id="uploadForm" name="frmupload" method="post" enctype="multipart/form-data">';
			txt+='<input type="file" id="uploadImage" name="uploadImages[]" accept="'+accept_types+'" multiple/>';
			txt+='<input type="hidden" id="action" name="action" value="'+action+'"/>';
			txt+='<input type="hidden" id="action" name="area_id" value="'+area_id+'"/>';
			txt+='<input type="hidden" id="action" name="params" value="'+params+'"/>';
			txt+='<input type="hidden" id="hash" name="hash" value="'+hash+'"/>';
			txt+='<input type="hidden" id="files_count" name="files_count" value=""/>';
			txt+='<input id="submitButton" type="submit" name="btnSubmit" value="Загрузить" disabled/>';
			txt+='</form>';

			txt+='<img class="close_btn" src="img/cancel.png" title="Закрыть"/>';
			txt+='</div>';
		    txt+='<div class="progress" id="progressDivId">';
		    txt+='<div style="text-align:center">';
		    txt+='<div class="progress-bar" id="progressBar"></div>';
		    txt+='<div class="percent" id="percent"></div>';
		    txt+='<div style="height: 10px;"></div>';
		    txt+='<div id="error_msg"></div>';
		    txt+='</div>';
		    txt+='</div>';
		    txt+='</div>';
			txt+='</div>';

			$("body").append(txt);

			$("#upload_form").draggable();

			$('#uploadImage').on('change', function() {
				if (this.files[0].size / 1024 / 1024>20) {
					$("#error_msg").html("Размер файла не должен превышать 20 Мб");
			  		
			  		$("#uploadImage").val("");
			  	} else {
			  		$("#submitButton").prop("disabled", false);
			  	}
			});

			$("#upload_form .close_btn").click(function(){
				$("#upload_form").remove();
			});

			$('#submitButton').click(function () {
				files_count=$("#uploadImage")[0].files.length;
				$("#files_count").val(files_count);
				
			   	$('#uploadForm').ajaxForm({
			    	url: 'php/upload_file.php',
			    	uploadProgress: function (event, position, total, percentComplete) {
			    	    var percentValue = percentComplete + '%';
			    	    $("#progressBar").animate({width: (Math.ceil(percentComplete)*0.9) + '%'}, {duration: 1000, easing: "linear", step: function (x) {
			    	        $("#submitButton").prop("disabled", true);

			    	        $("#upload_form .close_btn").hide();

			    	        $("#progressDivId").show();

			    	        $("#percent").text(percentComplete + "%");

			    	        if (parseInt(percentComplete)==100) {
						    	$("#upload_form").remove();
			    	        }
			    	    }});
			    	},

			    	error: function (response, status, e) {
			    	    $("#error_msg").html('Ошибка подключения к серверу');

					    $("#submitButton").prop("disabled", false);

					    $("#progressBar").animate({width:'0%'}, 0);

					    $("#upload_form .close_btn").show();

					    $("#percent").text("");
			    	},
			    	        
			    	complete: function (xhr) {
			    		console.log(xhr);

			    		if (action=="documents") {
							get_docs_list("all");
						} else if (action=="counter_doc") {
							$("#new_period_form .error_msg").html("Квитанция загружена");
						} else {
							data=JSON.parse(xhr.responseText);

							if (data["result"]=="OK") {
								$("#"+action+"_img").attr("src", window.location.origin+"/users_files/"+data["file"]);
								$("#"+action+"_img").show();
							} else if (data["result"]=="SESSION_ERROR") {
								window.location.href=window.location.origin+"/auth.php";
							} else {
								$("#error_msg").html(data["desc"]);
							}
						}
			    	}
			    });
			});			
		}
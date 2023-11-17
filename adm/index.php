<html>
	<title>Администрирование</title>

	<link href="../css/adm.css" rel="stylesheet" type="text/css">
	<link href="../css/jquery-ui.css" rel="stylesheet" type="text/css">
	<link href="/js/contextMenu/jquery.contextMenu.css" rel="stylesheet" type="text/css">

	<link rel="icon" type="vnd.microsoft.icon" href="/img/gerb.gif">

	<script type="text/javascript" src="/js/jquery-2.1.3.min.js"></script>
	<script type="text/javascript" src="/js/jquery-ui.js"></script>
	<script type="text/javascript" src="/js/contextMenu/jquery.contextMenu.js"></script>
	<script type="text/javascript" src="/js/jquery.cookie.js"></script>
	<script type="text/javascript" src="/js/jquery.table2excel.js"></script>

	<script>
		$(document).ready(function() {
			body_height=document.body.clientHeight;
			$(".right_frame").css("height", body_height-100);

			$(".main_btns").click(function(){
				action=$(this).attr("action");

				$(".main_btns").not(":first").css("background","yellow");

				$(this).css("background","aqua");

				$(this).animate({width:"-=5px"}, 150, function() {
					$(this).animate({width:"+=5px"}, 150, function() {

						if (action=="back") {
							window.history.go(-1);
						} else if (action=="users_data") {
							$(".right_frame").hide();
							$("#users_data").show();

							get_lk_changes();
						} else if (action=="documents") {
							$(".right_frame").hide();
							$("#documents").show();

							get_new_docs_list(false, 0);
						} else if (action=="users") {
							$(".right_frame").hide();
							$("#users").show();

							get_users();
						} else if (action=="counters") {
							$(".right_frame").hide();
							$("#counters").show();

							get_counters_list();
						} else if (action=="votes_list") {
							$(".right_frame").hide();
							$("#votes_list").show();

							get_votes_list();
						} else if (action=="create_new_vote") {
							$(".right_frame").hide();
							$("#create_new_vote").show();
							$("#create_new_vote").attr("vote_id", 0);
							$("#new_vote_maket").html('<div class="vote_caption"></div><div class="vote_question"></div><table class="vote_tbl">');
							$("#create_new_vote .vote_caption_input").val("");
							$("#create_new_vote .vote_question_input").val("");
							$("#create_new_vote .answers_list").empty();
						}
					});
				});

				current_date=new Date();
				current_year=current_date.getFullYear();

				$("#user_data_frame .birth_date").datepicker({
					dateFormat: 'yy-mm-dd', 
					currentText: 'Сейчас',
					closeText: 'Закрыть',
					monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
					monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'],
					dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
					dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
					dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
					prevText: '<Пред месяц',
					nextText: 'След месяц>',
					firstDay: 1,
					showButtonPanel: true,
				    changeMonth: true,
				    changeYear: true,
				    showAnim: "clip",
				    yearRange: '1930:'+(current_year-18),
				    defaultDate: '2000-01-01'
				});		
			});

			get_counts();
		});

		function get_counts() {
			hash=$.cookie("hash");

			$.ajax({
				url:"php/get_counts.php",
				data: {hash:hash},
				type: "POST",
				timeout:10000,
				success: function(data) {
					data=JSON.parse(data);

					if (data["new_lk_changes"]==0) {
						txt="Личные данные";
					} else {
						txt="Личные данные <span class='red'>("+data["new_lk_changes"]+")</span>";
					}

					$(".main_btns[action='users_data']").html(txt);

					if (data["new_documents"]==0) {
						txt="Документы";
					} else {
						txt="Документы <span class='red'>("+data["new_documents"]+")</span>";
					}

					$(".main_btns[action='documents']").html(txt);

					if (data["new_lk_changes"]==0 && data["new_documents"]>0) {
						$("#users_data").hide();
						$("#documents").show();

						$(".main_btns[action='documents']").css("background","aqua");

						get_new_docs_list(false, 0);
					} else {
						$("#users_data").show();
						$("#documents").hide();

						$(".main_btns[action='users_data']").css("background","aqua");

						get_lk_changes();
					}

					$("#main_tbl").animate({opacity:1},1000);
				}
			});
		}

		function get_lk_changes() {
			hash=$.cookie("hash");

			$.ajax({
				url:"php/get_lk_changes.php",
				data: {hash:hash},
				type: "POST",
				timeout:10000,
				success: function(data) {
					data=JSON.parse(data);

					if (data["result"]=="NOT_AUTH") {
						window.location.href=window.location.origin+"/auth.php";

						return false;
					}

					if (data["result"]=="NO_CHANGES") {
						$("#no_changes").show();

						$("#changes_list").html("");

						return false;
					}

					$("#changes_list .accept").off();
					$("#changes_list .reject").off();
					$("#changes_list .foto_prev").off();

					txt="<thead><th><div>Пользователь</div></th><th><div>Что менял</div></th><th><div>Значение</div></th><th><div>Дата</div></th><th></th></thead>";

					for (i=0; i<data["changes"].length; i++) {
						if (data["changes"][i]["status"]==1) {
							class_name="accepted";
						} else if (data["changes"][i]["status"]==2) {
							class_name="rejected";
						} else {
							class_name="";
						}

						txt+="<tr>";
						txt+="<td><div class='"+class_name+"'>&nbsp;&nbsp;"+data["changes"][i]["user"]+"</div></td>";
						txt+="<td><div class='"+class_name+"'>&nbsp;&nbsp;"+data["changes"][i]["changed"]+"</div></td>";
						txt+="<td><div class='"+class_name+"'>&nbsp;&nbsp;"+data["changes"][i]["value"]+"</div></td>";
						txt+="<td><div class='"+class_name+"'>"+data["changes"][i]["date"]+"</div></td>";

						if (data["changes"][i]["status"]==0) {
							txt+="<td><img class='accept' change_id="+data["changes"][i]["id"]+" src='../img/OK.png' title='Подтвердить'>";
							txt+="<img class='reject' change_id="+data["changes"][i]["id"]+" src='../img/cancel.png' title='Отклонить'></td>";
						} else if (data["changes"][i]["status"]==1) {
							txt+="<td><img class='reject' change_id="+data["changes"][i]["id"]+" src='../img/cancel.png' title='Отклонить'></td>";
						} else {
							txt+="<td><img class='accept' change_id="+data["changes"][i]["id"]+" src='../img/OK.png' title='Подтвердить'></td>";
						}

						txt+="</tr>";
					}

					$("#changes_list").html(txt);

					$("#changes_list .foto_prev").on("click", function() {
						path=$(this).attr("path");
						foto_prev_show(path);
					});

					$("#changes_list .accept").on("click", function() {
						change_id=$(this).attr("change_id");
						lk_changes_status(change_id, "accept");
					});

					$("#changes_list .reject").on("click", function() {
						change_id=$(this).attr("change_id");
						reject_div_show(change_id, "reject");
					});
				}
			});
		}

		function foto_prev_show(path) {
			txt="<div id='foto_prev_div'>";
			txt+="<img src='https://snt-morskoy.ru/"+path+"' style='width:360px'/>";
			txt+="<div>";

			$("body").append(txt);
			
			$("#foto_prev_div").click(function(){
				$("#foto_prev_div").remove();
			});
		}

		function reject_div_show(change_id, action) {
			txt="<div id='reject_div' change_id='"+change_id+"' action='"+action+"'>";
			txt+="<div syle='width:100%'>";
			txt+="<img class='close_btn' src='../img/cancel.png' style='width:25px; margin-left:350px' title='Закрыть'/>";
			txt+="</div>";
			txt+="Комментарий<BR>";
			txt+="<textarea>";
			txt+="</textarea><BR><BR>";
			txt+="<button>Сохранить</button>";
			txt+="</div>";

			$("body").append(txt);

			$("#reject_div button").click(function(){
				change_id=$("#reject_div").attr("change_id");
				action=$("#reject_div").attr("action");
				comment=$("#reject_div textarea").val();

				$("#reject_div").remove();

				lk_changes_status(change_id, action, comment);
			});

			$("#reject_div .close_btn").click(function(){
				$("#reject_div").remove();
			});
		}

		function lk_changes_status(change_id, action) {
			hash=$.cookie("hash");

			if (typeof comment == 'undefined') {
				comment="";
			}

			$.ajax({
				url:"php/lk_changes_status.php",
				data: {hash:hash, change_id:change_id, action:action, comment:comment},
				type: "POST",
				timeout:10000,
				success: function(data) {
					data=JSON.parse(data);

					if (data["result"]=="NOT_AUTH") {
						window.location.href=window.location.origin+"/auth.php";

						return false;
					}

					get_lk_changes();
				}
			});
		}

		function get_new_docs_list(ids, active) {
			hash=$.cookie("hash");

			$.ajax({
				url:"php/get_new_docs_list.php",
				data: {hash:hash, active:active},
				type: "POST",
				timeout:10000,
				success: function(data) {
					data=JSON.parse(data);

					if (data["result"]=="NOT_AUTH") {
						window.location.href=window.location.origin+"/auth.php";

						return false;
					}

					$("#docs_list span").off();

					if (data["result"]=="NO_DOCS") {
						$("#options_tbl").hide();
						$("#no_docs").show();

						$("#docs_list").html("");

						return false;
					} else {
						$("#options_tbl").show();
						$("#no_docs").hide();
					}

					if (ids!==false) {
						ids=ids.split(",");
					} else {
						ids=new Array();
					}

					txt="<thead><th></th><th><div>Добавил</div></th><th><div>Файл</div></th><th><div>Группа</div></th><th><div>Категория</div></th><th><div>Дата</div></th></thead>";

					for (i=0; i<data["files"].length; i++) {
						txt+="<tr>";

						if (data["files"][i]["category"]=="ads") {
							category="Оъявления";
						} else if (data["files"][i]["category"]=="orders") {
							category="Отчёты";
						} else if (data["files"][i]["category"]=="regulations") {
							category="Уставные";
						} else if (data["files"][i]["category"]=="protocols") {
							category="ОС";
						} else if (data["files"][i]["category"]=="others") {
							category="Прочее";
						} else {
							category="";
						}

						if (ids.indexOf(data["files"][i]["id"])>-1) {
							txt+="<td><div><input type='checkbox' doc_id='"+data["files"][i]["id"]+"' checked/></div></td>";
						} else {
							txt+="<td><div><input type='checkbox' doc_id='"+data["files"][i]["id"]+"'/></div></td>";
						}

						txt+="<td><div>&nbsp;&nbsp;"+data["files"][i]["user"]+"</div></td>";
						txt+="<td class='doc_name' doc_id='"+data["files"][i]["id"]+"' title='"+data["files"][i]["file_name_rus"]+"'><div>&nbsp;&nbsp;<span file='"+data["files"][i]["file_name_rus"]+"'>"+data["files"][i]["file_name_rus"]+"</span></div></td>";
						txt+="<td><div>"+data["files"][i]["groups"]+"</div></td>";
						txt+="<td><div>"+category+"</div></td>";
						txt+="<td><div>"+data["files"][i]["date_time"]+"</div></td>";

						txt+="</tr>";
					}

					$("#docs_list").html(txt);

					$("#docs_list").attr("published", active);

					$("#docs_list span").on("click", function() {
						file=$(this).attr("file");

						file_download(file);
					});

					add_context_menu();
				}
			});
		}

		function add_context_menu() {
			$.contextMenu({
		        selector: '#docs_list .doc_name', 
		        build: function ($trigger, e) {
	                return {
				        callback: function(key, options) {
				        	old_doc_name=$(e.target).text().trim();

				        	file_rename_form_show(old_doc_name);
						},
				   		items: {
				        	"rename": {name: "Переименовать", icon: ""} 
				        }
				    };
			    }
		    });
		}	

		function file_rename_form_show(old_doc_name) {
			txt="<div id='file_rename_form' old_doc_name='"+old_doc_name+"'>";
			txt+="<input placeholder='Новое имя без расширения' size=30><BR>&nbsp;<BR>";
			txt+="<button>Переименовать</button>";
			txt+="<button style='margin-left:20px' class='close_btn'>Закрыть</button>";
			txt+="<div class='error_msg'></div>";
			txt+="</div>";

			$("body").append(txt);

			$("#file_rename_form .close_btn").on("click", function(){
				$("#file_rename_form").remove();
			});

			$("#file_rename_form button").on("click", function(){
				new_name=$("#file_rename_form input").val();
				old_doc_name=$("#file_rename_form").attr("old_doc_name");

				$.ajax({
					url:"php/doc_rename.php",
					data: {old_doc_name:old_doc_name, new_name:new_name},
					type: "POST",
					timeout:10000,
					success: function(data) {
						if (data=="OK") {
							$("#file_rename_form").remove();

							published=$("#docs_list").attr("published");
							get_new_docs_list(false, published);
						} else {
							$("#file_rename_form .error_msg").text(data);	
						}
					},
					error: function() {
						$("#file_rename_form .error_msg").text("Ошибка связи с сервером");
					}
				});
			});
		}

		function file_download(file) {
			var link = document.createElement('a');
			link.setAttribute('href', window.location.origin+"/documents/"+file);
			link.setAttribute('download', file);
			link.click();
		}

		function save_new_doc(action) {
			i=0;
			ids=Array();

			$("#docs_list").find("input:checkbox").each(function(){
				if ($(this).prop("checked")) {
					ids[i]=$(this).attr("doc_id");

					i++;
				}
			});

			if (ids.length>0) {
				ids=ids.join(",");
			} else {
				ids=0;
			}

			if (action=="change_category") {
				param=$("#new_doc_category option:selected").val();
			} else if (action=="change_groups") {
				param=$("#new_group_name").val();
			} else {
				param=1;
			}

			$.ajax({
				url:"php/save_new_doc.php",
				data: {hash:hash, ids:ids, param:param, action:action},
				type: "POST",
				timeout:10000,
				success: function(data) {
					data=JSON.parse(data);

					if (data["result"]=="NOT_AUTH") {
						window.location.href=window.location.origin+"/auth.php";

						return false;
					} else if (data["result"]=="OK") {
						$(".error_msg").text("Сохранено");

						setTimeout(function(){
							$(".error_msg").text("");
						}, 5000);

						published=$("#docs_list").attr("published");

						get_new_docs_list(ids, published);

						if (action=="change_groups") {
							$("#new_group_name").val("");
						}

						if (action=="publish") {
							get_counts();
						}
					} else {
						$(".error_msg").text("НЕ сохранено");

						setTimeout(function(){
							$(".error_msg").text("");
						}, 5000);
					}
				}
			});
		}

		function get_users() {
			$.ajax({
				url:"php/get_users.php",
				data: {hash:hash},
				type: "POST",
				timeout:10000,
				success: function(data) {
					data=JSON.parse(data);

					if (data["result"]=="NOT_AUTH") {
						window.location.href=window.location.origin+"/auth.php";

						return false;
					}

					txt="<thead><th><div>Номер</div></th><th><div>Фамилия</div></th><th><div>Имя</div></th><th><div>Отчетсво</div><th><div>Тип</div></th><th><div>Активен</div></th><th></th></thead>";

					for (i=0; i<data["users"].length; i++) {
						txt+="<tr user_id='"+data["users"][i]["id"]+"'>";
						txt+="<td><div>&nbsp;&nbsp;"+data["users"][i]["tel_nom"]+"</div></td>";
						txt+="<td><div>&nbsp;&nbsp;"+data["users"][i]["sirname"]+"</div></td>";
						txt+="<td><div>&nbsp;&nbsp;"+data["users"][i]["name"]+"</div></td>";
						txt+="<td><div>&nbsp;&nbsp;"+data["users"][i]["middle_name"]+"</div></td>";

						txt+="<td><div>";
						txt+="<select class='account_type' onchange='user_settings("+data["users"][i]["id"]+", 10)'>";
						
						if (data["users"][i]["account_type"]=="user") {
							txt+="<option value='user' selected>Юзер</option>";
							txt+="<option value='admin'>Админ</option>";
						} else {
							txt+="<option value='user'>Юзер</option>";
							txt+="<option value='admin' selected>Админ</option>";

						}
						
						txt+="</select>";
						txt+="</div></td>";

						if (data["users"][i]["active"]==1) {
							active="Да";
						} else {
							active="Нет";
						}

						txt+="<td><div>"+active+"</div></td>";

						if (data["users"][i]["active"]==1) {
							txt+="<td><button onclick='user_settings("+data["users"][i]["id"]+", 0)'>Блокировать</button></td>";
						} else {
							txt+="<td><button onclick='user_settings("+data["users"][i]["id"]+", 1)'>Активировать</button></td>";
						}

						txt+="<td><button class='goto_user_lk' user_id='"+data["users"][i]["id"]+"'>Личный кабинет</button></td>";

						txt+="</tr>";
					}

					$("#users_list").html(txt);

					$("#users_list .goto_user_lk").on("click", function(){
						user_id=$(this).attr("user_id");

						get_user_data(user_id);
					});
				}
			});
		}

		function get_user_data(user_id) {
			$.ajax({
				url:"../php/get_user_data.php",
				data: {hash:hash, user_id:user_id},
				type: "POST",
				timeout:10000,
				success: function(data) {
					data=JSON.parse(data);

					if (data["result"]=="error") {
						if (data["desc"]=="SESSION_ERROR") {
							window.location.href=window.location.origin+"/auth.php";
						}

						return false;
					}

					$("#user_data_frame").show();

					$("#user_data_frame").attr("user_id", user_id);

					$("#fio .sirname").val(data["user_data"]["sirname"]);
					$("#fio .name").val(data["user_data"]["name"]);
					$("#fio .middle_name").val(data["user_data"]["middle_name"]);

					$("#user_data_frame .birth_date").val(data["user_data"]["birth_date"]);

					$("#passport .seria").val(data["user_data"]["passport_seria"]);
					$("#passport .number").val(data["user_data"]["passport_number"]);
					$("#reg_address input").val(data["user_data"]["reg_address"]);
					$("#email input").val(data["user_data"]["email"]);

					$("#passport .error_msg").text("");
					if (data["passport_status"]==1) {
						$("#passport .accept").show();
						$("#passport .reject").hide();
					} else if (data["passport_status"]==2) {
						$("#passport .accept").hide();
						$("#passport .reject").show();
						$("#passport .error_msg").text(data["passport_comment"]);
					} else {
						$("#passport .accept").hide();
						$("#passport .reject").hide();
					}

					$("#reg_address .error_msg").text("");
					if (data["reg_address_status"]==1) {
						$("#reg_address .accept").show();
						$("#reg_address .reject").hide();
					} else if (data["reg_address_status"]==2) {
						$("#reg_address .accept").hide();
						$("#reg_address .reject").show();
						$("#reg_address .error_msg").text(data["reg_address_comment"]);
					} else {
						$("#reg_address .accept").hide();
						$("#reg_address .reject").hide();
					}

					$("#email .error_msg").text("");
					if (data["email_status"]==1) {
						$("#email .accept").show();
						$("#email .reject").hide();
					} else if (data["email_status"]==2) {
						$("#email .accept").hide();
						$("#email .reject").show();
						$("#email .error_msg").text(data["email_comment"]);
					} else {
						$("#email .accept").hide();
						$("#email .reject").hide();
					}

					$("#passport_img .error_msg").text("");
					if (data["passport1_img_status"]==1) {
						$("#passport1_img_status .accept").show();
						$("#passport1_img_status .reject").hide();
					} else if (data["passport1_img_status"]==2) {
						$("#passport1_img_status .accept").hide();
						$("#passport1_img_status .reject").show();
						$("#passport_img .error_msg").text(data["passport1_img_comment"]);
					} else {
						$("#passport1_img_status .accept").hide();
						$("#passport1_img_status .reject").hide();
					}

					if (data["passport2_img_status"]==1) {
						$("#passport2_img_status .accept").show();
						$("#passport2_img_status .reject").hide();
					} else if (data["passport2_img_status"]==2) {
						$("#passport2_img_status .accept").hide();
						$("#passport2_img_status .reject").show();
						$("#passport_img .error_msg").append("<BR>"+data["passport2_img_comment"]);
					} else {
						$("#passport2_img_status .accept").hide();
						$("#passport2_img_status .reject").hide();
					}

					if (data["user_data"]["passport1"]=="") {
						$("#passport1_img").hide();
					} else {
						$("#passport1_img").show();
						$("#passport1_img").attr("src", window.location.origin+"/users_files/"+data["user_data"]["passport1"]);
					}

					if (data["user_data"]["passport2"]=="") {
						$("#passport2_img").hide();
					} else {
						$("#passport2_img").show();
						$("#passport2_img").attr("src", window.location.origin+"/users_files/"+data["user_data"]["passport2"]);
					}
				}
			});
		}

		function save_user_data() {
			user_id=$("#user_data_frame").attr("user_id");

			sirname=$("#fio .sirname").val();
			name=$("#fio .name").val();
			middle_name=$("#fio .middle_name").val();
			birth_date=$("#user_data_frame .birth_date").val();

			if (name.length<2) {
				$("#reg_form .error").html("Укажите Ваше имя");

				return false;
			}

			if (sirname.length<2) {
				$("#reg_form .error").html("Укажите Вашу фамилию");

				return false;
			}

			if (middle_name.length<2) {
				$("#reg_form .error").html("Укажите Ваше отчетво");

				return false;
			}

			if (/^\d{4}[-]\d{2}[-]\d{2}$/.test(birth_date)==false) {
				$("#reg_form .error").html("Неверная дата рождения");

				return false;
			}

			passport_seria=$("#passport .seria").val();
			passport_number=$("#passport .number").val();

			if (passport_seria.length>0) {
				if (/^\d{4}$/.test(passport_seria)==false) {
					$("#passport .seria").css("background", "red");
					$("#main_tbl .error").text("Неправильная серия паспорта");

					return false;
				}
			} else {
				$("#passport .seria").css("background", "red");
				$("#main_tbl .error").text("Не указана серия паспорта");

				return false;
			}

			if (passport_number.length>0) {
				if (/^\d{6}$/.test(passport_number)==false) {
					$("#passport .number").css("background", "red");
					$("#main_tbl .error").text("Неправильный номер паспорта");

					return false;
				}
			} else {
				$("#passport .number").css("background", "red");
				$("#main_tbl .error").text("Не указаномер паспорта");

				return false;
			}

			reg_address=$("#reg_address input").val();

			if (email.length==0) {
				$("#reg_address input").css("background", "red");
				$("#main_tbl .error").text("Не указан адрес регистрации");

				return false;
			}

			email=$("#email input").val();

			if (email.length>0) {
				if (/^[\w-\.]+@[\w-]+\.[a-z]{2,10}$/.test(email)==false) {
					$("#email input").css("background", "red");
					$("#main_tbl .error").text("Неверный формат почты");

					return false;
				}
			} else {
				$("#email input").css("background", "red");
				$("#main_tbl .error").text("Не указана почта");

				return false;
			}

			$("#main_tbl input").css("background", "yellow");

			$.ajax({
				url:"../php/save_user_data.php",
				data: {hash:hash, user_id:user_id, sirname:sirname, name:name, middle_name:middle_name, birth_date:birth_date, passport_seria:passport_seria, passport_number:passport_number, reg_address:reg_address, email:email},
				type: "POST",
				timeout:10000,
				success: function(data) {
					data=JSON.parse(data);

					if (data["result"]=="OK") {
						$(".error_msg").text("");

						$("#main_tbl .error").text("Сохранено");

						setTimeout(function(){
							$("#main_tbl .error").text("");
						}, 5000);
					} else {
						$("#main_tbl .error").text("НЕ сохранено");

						setTimeout(function(){
							$("#main_tbl .error").text("");
						}, 5000);
					}
				},
				error: function(data) {
					$("#main_tbl .error").html("Какая-то ошибка. Проверьте подключение");	
				}
			});
		}

		function user_settings(user_id, value) {
			account_type=$("#users_list tr[user_id='"+user_id+"']").find(".account_type option:selected").val();

			$.ajax({
				url:"php/user_settings.php",
				data: {hash:hash, user_id:user_id, account_type:account_type, value:value},
				type: "POST",
				timeout:10000,
				success: function(data) {
					data=JSON.parse(data);

					if (data["result"]=="NOT_AUTH") {
						window.location.href=window.location.origin+"/auth.php";

						return false;
					}

					get_users();
				}
			});
		}

		function get_counters_list() {
			$.ajax({
				url:"php/get_counters_list.php",
				data: {hash:hash},
				type: "POST",
				timeout:10000,
				success: function(data) {
					data=JSON.parse(data);

					monthes=Array("Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");

					txt="";
					for (i=0; i<data["counters_list"].length; i++) {
						fio=data["users"][data["counters_list"][i]["user_id"]][0]["sirname"]+" "+data["users"][data["counters_list"][i]["user_id"]][0]["name"];

						year=data["counters_list"][i]["period"].substring(0,4);
						month=parseInt(data["counters_list"][i]["period"].substring(4));

						period=monthes[month-1]+" "+year;

						txt+="<tr user_id='"+data["counters_list"][i]["user_id"]+"' period='"+data["counters_list"][i]["period"]+"' area_nom='"+data["counters_list"][i]["area_nom"]+"'>";
						txt+="<td><div>"+fio+"</div></td>";
						txt+="<td><div>"+period+"</div></td>";
						txt+="<td><div>"+data["counters_list"][i]["date"]+"</div></td>";
						txt+="<td><div>"+data["counters_list"][i]["area_nom"]+"</div></td>";
						txt+="<td><div>"+data["counters_list"][i]["counter_type"]+"</div></td>";
						txt+="<td><div>"+data["counters_list"][i]["value"]+"</div></td>";
						txt+="<td><div>"+data["counters_list"][i]["for_pay"]+"</div></td>";
						txt+="<td><div>"+data["counters_list"][i]["paid"]+"</div></td>";

						//Проверка на наличие в папке квитанций квитанции за выводимый период
						if (data["files"].indexOf(data["counters_list"][i]["area_nom"]+"-"+data["counters_list"][i]["period"])>-1) {
							txt+="<td class='noExcel'><div><a class='foto_prev_show' href='javascript:'>Квитанция</a></div></td>";
						} else {
							txt+="<td class='noExcel'><div></div></td>";
						}

						txt+="</tr>";
					}

					$("#counters_list tbody").html(txt);

					$("#counters_list tbody .foto_prev_show").on("click", function(){
						period=$(this).parent().parent().parent().attr("period");
						area_nom=$(this).parent().parent().parent().attr("area_nom");
						user_id=$(this).parent().parent().parent().attr("user_id");

						path="users_files/"+user_id+"/payment_docs/"+area_nom+"-"+period;

						foto_prev_show(path);
					});
				}
			});
		}

		function get_votes_list() {
			hash=$.cookie("hash");

			$.ajax({
				url:"../vote/php/get_votes_list.php",
				data: {hash:hash, from_adm:1},
				type: "POST",
				timeout:10000,
				success: function(data) {
					data=JSON.parse(data);

					txt="";
					for (i=0; i<data.length; i++) {
						txt+="<tr class='clickable' vote_id='"+data[i]["id"]+"'>";

						txt+="<td><div>"+data[i]["name"]+"</div></td>";
						txt+="<td><div>"+data[i]["date"]+"</div></td>";

						if (data[i]["active"]==0) {
							txt+="<td class='active'><div>Создано</div></td>";
						} else if (data[i]["active"]==1) {
							txt+="<td class='active'><div>Активно</div></td>";
						} else {
							txt+="<td class='active'><div>Завершено</div></td>";
						}

						txt+="</tr>";
					}

					$("#votes_list_tbl").html(txt);

					$("#votes_list .clickable").on("click", function(){
						vote_id=$(this).attr("vote_id");

						$("#create_new_vote").attr("vote_id", vote_id);

						$(".main_btns").not(":first").css("background","yellow");
						$(".main_btns[action='create_new_vote']").css("background","aqua");

						$(".right_frame").hide();
						$("#create_new_vote").show();

						get_new_vote_maket();
					});
				}
			});
		}

		function add_new_vote_answer() {
			txt="<input class='answer' size=40/><BR>";

			$("#create_new_vote .answers_list").append(txt);
		}

		function make_new_vote_maket() {
			vote_caption=$("#create_new_vote .vote_caption_input").val();
			vote_question=$("#create_new_vote .vote_question_input").val();

			$("#create_new_vote .vote_caption").text(vote_caption);
			$("#create_new_vote .vote_question").text(vote_question);

			answers_txt="";
			i=0;
			$("#create_new_vote .answers_list").find(".answer").each(function(){
				i++;

				answer_txt=$(this).val();

				answers_txt+="<tr><td>"+i+"</td><td class='answer_txt'>"+answer_txt+"</td><td><input type='radio' name='one_answer' answer_nom='"+i+"'/></td></tr>";
			});

			$("#create_new_vote .vote_tbl").html(answers_txt);
		}

		function save_new_vote_maket(action) {
			vote_id=$("#create_new_vote").attr("vote_id");

			vote_caption=$("#create_new_vote .vote_caption_input").val();
			vote_question=$("#create_new_vote .vote_question_input").val();

			$("#create_new_vote .vote_caption").text(vote_caption);
			$("#create_new_vote .vote_question").text(vote_question);

			vote_maket=$("#new_vote_maket").html();

			$.ajax({
				url:"php/save_new_vote_maket.php",
				data: {vote_id:vote_id, vote_caption:vote_caption, vote_question:vote_question, vote_maket:vote_maket, action:action},
				type: "POST",
				timeout:10000,
				success: function(data) {
					data=JSON.parse(data);

					if (data["result"]=="OK") {
						$("#create_new_vote").attr("vote_id", data["vote_id"]);

						$("#create_new_vote .error_msg").text("Сохранено");
					} else {
						$("#create_new_vote .error_msg").text(data["result"]);
					}

					$("#create_new_vote .error_msg").show();

					setTimeout(function(){
						$("#create_new_vote .error_msg").hide();
					},5000);
				}
			})
		}

		function remove_new_vote_maket() {
			vote_id=$("#create_new_vote").attr("vote_id");

			$.ajax({
				url:"php/remove_new_vote_maket.php",
				data: {vote_id:vote_id},
				type: "POST",
				timeout:10000,
				success: function(data) {
					if (data=="OK") {
						$(".right_frame").hide();
						$("#votes_list").show();

						$(".main_btn[action='votes_list']").css("background", "aqua");

						get_votes_list();
					} else {
						$("#create_new_vote .error_msg").text("НЕ удалено!!! Голосование ещё не сохранено");

						$("#create_new_vote .error_msg").show();

						setTimeout(function(){
							$("#create_new_vote .error_msg").hide();
						},5000);
					}
				}
			})			
		}

		function get_new_vote_maket() {
			vote_id=$("#create_new_vote").attr("vote_id");

			$("#create_new_vote .answers_list").empty();

			$.ajax({
				url:"php/get_new_vote_maket.php",
				data: {vote_id:vote_id},
				type: "POST",
				timeout:10000,
				success: function(data) {
					data=JSON.parse(data);

					$("#create_new_vote .vote_caption_input").val(data["name"]);
					$("#create_new_vote .vote_question_input").val(data["vote_question"]);
					$("#new_vote_maket").html(data["vote_maket"]);

					$("#new_vote_maket .vote_tbl").find("tr").each(function(){
						answer_txt=$(this).find(".answer_txt").text();

						txt="<input class='answer' size=40 value='"+answer_txt+"'/><BR>";

						$("#create_new_vote .answers_list").append(txt);
					});
				}
			});
		}

		function to_excel(table_name) {
			var data = new Date();
			current_date = data.toLocaleDateString();

			if (table_name=="counters_list") {
				elem="#counters_list";
				filename="Показания счётчиков_"+current_date+".xls";
			}

			$(elem).table2excel({
				exclude: ".noExcel",
				name: filename,
				filename: filename
			});
		}
	</script>

	<body>
		<table id="main_tbl">
			<tr>
				<td style="width:225px">
					<div id="main_menu">
						<div class='main_btns' style="background: aliceblue" action="back">
							<- Назад
						</div>

						<div class='main_btns' action="users_data">
							Личные данные
						</div>

						<div class='main_btns' action="documents">
							Документы
						</div>

						<div class='main_btns' action="users">
							Пользователи
						</div>

						<div class='main_btns' action="counters">
							Показания
						</div>

						<div class='main_btns' action="votes_list">
							Голосования
						</div>

						<div class='main_btns' action="create_new_vote">
							Создать госование
						</div>						
					</div>
				</td>
				<td>
					<div class="right_frame" id="users_data">
						<div id="changes_list_div">
							<table id="changes_list">

							</table>
						</div>

						<div id="no_changes">
							Новых изменений нет
						</div>
					</div>

					<div class="right_frame" id="documents">
						<button onclick="get_new_docs_list(false, 1)">Опубликованные</button>
						<button onclick="get_new_docs_list(false, 0)">НЕ опубликованные</button>

						<div id="docs_list_div">
							<table id="docs_list">

							</table>
						</div>

						<table id="options_tbl">
							<tr>
								<td>
									<B>Определить в категорию&nbsp;&nbsp;</B>

									<select id="new_doc_category">
										<option value="ads">Оъявления</option>
										<option value="orders">Отчёты</option>
										<option value="regulations">Уставные</option>
										<option value="protocols">ОС</option>
										<option value="others">Прочее</option>
									</select>

									<button onclick="save_new_doc('change_category')">Сохранить</button>
								</td>
							</tr>
							<tr>
								<td>
									<B>Объединить в группу&nbsp;&nbsp;</B><input id="new_group_name" size=20/>&nbsp;&nbsp;<button onclick="save_new_doc('change_groups')">Сохранить</button>
								</td>
							</tr>
							<tr>
								<td>
									<button onclick="save_new_doc('publish')">Опубликовать</button>
									<button onclick="if (confirm('Точно удалить?')) {save_new_doc('remove')}" style="margin-left:50px">Удалить</button>
								</td>
							</tr>
							<tr>
								<td>
									<div class="error_msg"></div>
								</td>
							</tr>							
						</table>

						<div id="no_docs">
							Новых документов нет
						</div>
					</div>

					<div class="right_frame" id="users">
						<div id="users_list_div">
							<table id="users_list">

							</table>
						</div>
					</div>

					<div class="right_frame" id="counters">
						<img src="../img/to_excel.png" style="cursor:pointer" onclick="to_excel('counters_list')">

						<div id="counters_list_div">
							<table id="counters_list">
								<thead>
									<th>ФИО</th><th>Период</th><th>Изменён</th><th>Номер участка</th><th>Тип счётчика</th><th>Значение</th><th>К оплате</th><th>Оплачено</th><th></th>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>

					<div class="right_frame" id="votes_list">
						<div id="votes_list_div">
							<table id="votes_list_tbl">

							</table>
						</div>
					</div>

					<div class="right_frame" id="create_new_vote">
						<div id='new_vote_maket'>
							<div class="vote_caption"></div>

							<div class="vote_question"></div>

							<table class="vote_tbl">

							</table>
						</div>

						<input class='vote_caption_input' placeholder='Название голосования' size=40/><BR>
						<input class='vote_question_input' placeholder='Тект вопроса' size=40/><BR>

						<button onclick='add_new_vote_answer()'>Добавить вариант ответа</button><BR>

						<div class='answers_list'></div>

						<HR>

						<table style="margin: 0 auto;">
							<tr>
								<td>
									<button onclick='make_new_vote_maket()'>Создать макет</button>
								</td>
								<td>
									<button onclick='save_new_vote_maket(0)'>Сохранить макет</button>
								</td>
							</tr>
							<tr>
								<td>
									<button onclick='save_new_vote_maket(1)'>Активировать</button>
								</td>
								<td>
									<button onclick='save_new_vote_maket(2)'>Закрыть</button>
								</td>
							</tr>
							<tr>
								<td colspan=2 style="text-align: center">
									<button onclick='remove_new_vote_maket()'>Удалить</button>
								</td>
							</tr>							
						</table>

						<div class='error_msg'></div>
					</div>

					<div id="user_data_frame" style="display:none">
						<table class="form">
							<tr>
								<td>
									<table id="fio" style="width:100%">
										<tr>
											<td>
												Фамилия
											</td>
											<td>
												Имя
											</td>
											<td>
												Отчество
											</td>
										</tr>
										<tr>
											<td style="width:33%">
												<input class="sirname" size=12 placeholder="Фамилия">
											</td>
											<td style="width:33%">
												<input class="name" size=12 placeholder="Имя">
											</td>
											<td style="width:33%">
												<input class="middle_name" size=12 placeholder="Отчество">
											</td>
										</tr>
									</table>
								</td>
							</tr>

							<tr>
								<td>
									Дата рождения
								</td>
							</tr>
							<tr>
								<td>
									<input class="birth_date"/>
								</td>
							</tr>

							<tr>
								<td>
									Паспорт
								</td>
							</tr>
							<tr>
								<td>
									<table id="passport" style="width:100%">
										<tr>
											<td style="width:67%; text-align:right">
												<input class="seria" size=4 placeholder="Серия">
												<input class="number" size=6 placeholder="Номер">
											</td>
											<td>
												<img class="accept" src="https://snt-morskoy.ru/img/OK.png" style="display:none">
												<img class="reject" src="https://snt-morskoy.ru/img/cancel.png" style="display:none">
											</td>
										</tr>
										<tr>
											<td class="error_msg" colspan=2>
											</td>
										</tr>
									</table>
								</td>
							</tr>

							<tr>
								<td>
									Адрес регистрации
								</td>
							</tr>
							<tr>
								<td>
									<table id="reg_address" style="width:100%">
										<tr>
											<td style="width:87%; text-align:right">
												<input size=40 placeholder="Владивосток, Хабаровская 20, д.11, кв.12">
											</td>
											<td>
												<img class="accept" src="https://snt-morskoy.ru/img/OK.png" style="display:none">
												<img class="reject" src="https://snt-morskoy.ru/img/cancel.png" style="display:none">
											</td>
										</tr>
										<tr>
											<td class="error_msg" colspan=2>
											</td>
										</tr>
									</table>
								</td>
							</tr>

							<tr>
								<td>
									E-MAIL
								</td>
							</tr>
							<tr>
								<td>
									<table id="email" style="width:100%">
										<tr>
											<td style="width:71%; text-align:right">
												<input size=20>
											</td>
											<td>
												<img class="accept" src="https://snt-morskoy.ru/img/OK.png" style="display:none">
												<img class="reject" src="https://snt-morskoy.ru/img/cancel.png" style="display:none">
											</td>
										</tr>
										<tr>
											<td class="error_msg" colspan=2>
											</td>
										</tr>
									</table>
								</td>
							</tr>

							<tr>
								<td>
									<table>
										<tr>
											<td>
												<div class="button" onclick="save_user_data()" style="width:200px">Сохранить</div>
											</td>
											<td>
												<div class="button" onclick="$('#user_data_frame').hide()" style="width:200px">Закрыть</div>
											</td>
										</tr>
									</table>
								</td>
							</tr>

							</tr>

							<tr>
								<td>
									Скан/Фото паспорта
								</td>
							</tr>
							<tr>
								<td>
									<table id="passport_img">
										<tr>
											<td style="width:200px">
												Страницы 1-2
											</td>
											<td style="width:225px">
												Страница с регистрацией
											</td>
										</tr>
										<tr>
											<td>
												<img id="passport1_img" style="height:150px">
											</td>
											<td>
												<img id="passport2_img" style="height:150px">
											</td>
										</tr>
										<tr>
											<td id="passport1_img_status" style="text-align:center">
												<img class="accept" src="https://snt-morskoy.ru/img/OK.png" style="display:none">
												<img class="reject" src="https://snt-morskoy.ru/img/cancel.png" style="display:none">
											</td>
											<td id="passport2_img_status" style="text-align:center">
												<img class="accept" src="https://snt-morskoy.ru/img/OK.png" style="display:none">
												<img class="reject" src="https://snt-morskoy.ru/img/cancel.png" style="display:none">
											</td>
										</tr>
										<tr>
											<td class="error_msg" colspan=2>
											</td>
										</tr>
									</table>
								</td>
							</tr>

							<tr>
								<td class="error">
									
								</td>
							</tr>																		
						</table>
					</div>
				</td>
			</tr>
		</table>
	</body>
</html>
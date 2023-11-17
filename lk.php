<html>
	<title>Личный кабинет</title>

	<link href="/css/lk.css" rel="stylesheet" type="text/css">
	<link href="/css/file_upload.css" rel="stylesheet" type="text/css">

	<link rel="icon" type="vnd.microsoft.icon" href="/img/gerb.gif">

	<script type="text/javascript" src="/js/jquery-2.1.3.min.js"></script>
	<script type="text/javascript" src="/js/jquery-ui.js"></script>
	<script type="text/javascript" src="/js/jquery.cookie.js"></script>
	<script type="text/javascript" src="/js/jquery.form.js"></script>
	<script type="text/javascript" src="/js/file_upload.js"></script>

	<script>
		$(document).ready(function() {
			body_height=document.body.clientHeight;
			$(".right_frame").css("height", body_height-100);

			$(".main_btns").click(function(){
				action=$(this).attr("action");

				$(this).css("background","aqua");

				$(this).animate({width:"-=5px"}, 150, function() {
					$(this).animate({width:"+=5px"}, 150, function() {
						$(this).css("background","yellow");

						if (action=="back") {
							window.history.go(-1);
						} else if (action=="my_data") {
							get_user_data();
						} else if (action=="add_area") {
							$(".right_frame").hide();
							$("#area_data_frame").show();

							add_area();
						}
					});
				});
			});

			$("#save_area_data").click(function(){
				$(this).css("background","aqua");

				save_area_data();

				$(this).animate({width:"-=5px"}, 150, function() {
					$(this).animate({width:"+=5px"}, 150, function() {
						$(this).css("background","yellow");
					});
				});
			});

			auth();		
		});

		function auth() {
			hash=$.cookie("hash");

			if (typeof hash == 'undefined') {
				$("#authorized_btns").hide();
				$("#not_authorized_btns").show();
				
				return false;
			}

			$.ajax({
				url:"php/auth.php",
				data: {hash:hash},
				type: "POST",
				timeout:10000,
				success: function(data) {
					data=JSON.parse(data);

					if (data["result"]=="OK") {
						$.cookie('hash', data["hash"], { expires: 365, path: '/' });
						
						get_user_data(0);

						$("#main_tbl").animate({opacity:1},1000);
					} else {
						window.location.href=window.location.origin+"/auth.php";
					}
				}
			});
		}

		var max_area_id;

		function get_user_data() {
			$.ajax({
				url:"php/get_user_data.php",
				data: {hash:hash},
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

					$(".right_frame").hide();
					$("#user_data_frame").show();

					$("#fio").html(data["user_data"]["sirname"]+" "+data["user_data"]["name"]+" "+data["user_data"]["middle_name"]);
					$("#birth_date").html(data["user_data"]["birth_date"]);

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

					max_area_id=data["max_area_id"];

					$(".sub_btns").off("click");

					$(".sub_btns").remove();

					for (i=0; i<data["areas"].length; i++) {
						txt="<div class='sub_btns' area_id='"+data["areas"][i]["id"]+"'>";
						txt+=data["areas"][i]["area_name"];
						txt+="</div>";

						$("#main_menu").append(txt);
					}

					$(".sub_btns").on("click", function(){
						$(this).css("background","aqua");

						$(this).animate({width:"-=5px"}, 150, function() {
							$(this).animate({width:"+=5px"}, 150, function() {
								$(this).css("background","yellow");
						
								area_id=$(this).attr("area_id");

								$("#main_menu").attr("current_area", area_id);

								get_area_data(area_id);
							});
						});
					});

					$("#main_tbl").find("input").click(function(){
						$(this).css("background", "yellow");
					});
				}
			});
		}

		function save_user_data() {
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
				url:"php/save_user_data.php",
				data: {hash:hash, passport_seria:passport_seria, passport_number:passport_number, reg_address:reg_address, email:email},
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

		function get_area_data(area_id) {
			if (area_id==0) {//Новый участок
				$(".right_frame").hide();
				$("#area_data_frame").show();

				$("#area_nom").val("");
				$("#area_name").val("");
				$("#kadastr").val("");

				return false;
			}

			$.ajax({
				url:"php/get_area_data.php",
				data: {hash:hash, area_id:area_id},
				type: "POST",
				timeout:10000,
				success: function(data) {
					data=JSON.parse(data);

					if (data["result"]=="error") {
						if (data["desc"]=="SESSION_ERROR") {
							window.location.href=window.location.origin+"/auth.php";
						} else if (data["desc"]=="AREA_NOT_FOUND") {
							get_user_data();
						}
					} else {
						$(".right_frame").hide();
						$("#area_data_frame").show();

						$("#area_nom input").val(data["area_nom"]);
						$("#area_name input").val(data["area_name"]);
						$("#kadastr input").val(data["kadastr"]);

						$("#area_nom .error_msg").text("");
						if (data["area_nom_status"]==1) {
							$("#area_nom .accept").show();
							$("#area_nom .reject").hide();
						} else if (data["area_nom_status"]==2) {
							$("#area_nom .accept").hide();
							$("#area_nom .reject").show();
							$("#area_nom .error_msg").text(data["area_nom_comment"]);
						} else {
							$("#area_nom .accept").hide();
							$("#area_nom .reject").hide();
						}

						$("#area_name .error_msg").text("");
						if (data["area_name_status"]==1) {
							$("#area_name .accept").show();
							$("#area_name .reject").hide();
						} else if (data["area_name_status"]==2) {
							$("#area_name .accept").hide();
							$("#area_name .reject").show();
							$("#area_name .error_msg").text(data["area_name_comment"]);
						} else {
							$("#area_name .accept").hide();
							$("#area_name .reject").hide();
						}

						$("#kadastr .error_msg").text("");
						if (data["kadastr_status"]==1) {
							$("#kadastr .accept").show();
							$("#kadastr .reject").hide();
						} else if (data["kadastr_status"]==2) {
							$("#kadastr .accept").hide();
							$("#kadastr .reject").show();
							$("#kadastr .error_msg").text(data["area_kadastr_comment"]);
						} else {
							$("#kadastr .accept").hide();
							$("#kadastr .reject").hide();
						}

						$("#docs_img .error_msg").text("");
						if (data["sobstvennost_status"]==1) {
							$("#sobstvennost_img_status .accept").show();
							$("#sobstvennost_img_status .reject").hide();
						} else if (data["sobstvennost_status"]==2) {
							$("#sobstvennost_img_status .accept").hide();
							$("#sobstvennost_img_status .reject").show();
							$("#docs_img .error_msg").text(data["sobstvennost_comment"]);
						} else {
							$("#sobstvennost_img_status .accept").hide();
							$("#sobstvennost_img_status .reject").hide();
						}

						if (data["egrn_status"]==1) {
							$("#egrn_img_status .accept").show();
							$("#egrn_img_status .reject").hide();
						} else if (data["egrn_status"]==2) {
							$("#egrn_img_status .accept").hide();
							$("#egrn_img_status .reject").show();
							$("#docs_img .error_msg").append("<BR>"+data["egrn_comment"]);
						} else {
							$("#egrn_img_status .accept").hide();
							$("#egrn_img_status .reject").hide();
						}

						if (data["sobstvennost"]=="") {
							$("#sobstvennost_img").hide();	
						} else {
							$("#sobstvennost_img").show();
							$("#sobstvennost_img").attr("src", window.location.origin+"/users_files/"+data["sobstvennost"]);
						}

						if (data["egrn"]=="") {
							$("#egrn_img").hide();	
						} else {
							$("#egrn_img").show();
							$("#egrn_img").attr("src", window.location.origin+"/users_files/"+data["egrn"]);
						}
					}
				}
			});
		}

		function save_area_data() {
			area_name=$("#area_name input").val();
			area_nom=$("#area_nom input").val();
			kadastr=$("#kadastr input").val();
			area_id=$("#main_menu").attr("current_area");

			if (area_name.length==0) {
				$("#area_name input").css("background", "red");
				$("#main_tbl .error").text("Назовите как-нибудь участок");

				return false;
			}

			if (area_nom.length==0) {
				$("#area_nom input").css("background", "red");
				$("#main_tbl .error").text("Укажите номер участка");

				return false;
			}

			if (kadastr.length==0) {
				$("#kadastr input").css("background", "red");
				$("#main_tbl .error").text("Укажите кадастровый номер участка");

				return false;
			}

			$.ajax({
				url:"php/save_area_data.php",
				data: {hash:hash, area_id:area_id, area_nom:area_nom, area_name:area_name, kadastr:kadastr},
				type: "POST",
				timeout:10000,
				success: function(data) {
					data=JSON.parse(data);

					if (data["result"]=="OK") {
						$("#main_tbl .error").text("Сохранено");

						txt="<div class='sub_btns' area_id='"+area_id+"'>";
						txt+=area_nom;
						txt+="</div>";

						$(".sub_btns[area_id='"+area_id+"']").text(area_name)

						setTimeout(function(){
							$("#main_tbl .error").text("");
						}, 5000);
					} else if (data["result"]=="error" && data["desc"]=="not_auth") {
						window.location.href=window.location.origin+"/auth.php";
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

		function add_area() {
			if ($("#main_menu").find('div:contains("Новый участок")').length>0) {
				return false;
			}

			max_area_id++;

			$("#main_menu").attr("current_area", max_area_id);

			$("#area_nom").val("");
			$("#area_name").val("");
			$("#kadastr").val("");

			$("#sobstvennost_img").attr("src", "");
			$("#egrn_img").attr("src", "");

			$(".right_frame").hide();
			$("#area_data_frame").show();

			txt="<div class='sub_btns' area_id='"+max_area_id+"'>";
			txt+="Новый участок";
			txt+="</div>";

			$("#main_menu").append(txt);

			$(".sub_btns[area_id='"+max_area_id+"']").on("click", function(){
				$(this).css("background","aqua");
						
				$(this).animate({width:"-=5px"}, 150, function() {
					$(this).animate({width:"+=5px"}, 150, function() {
						$(this).css("background","yellow");

						area_id=$(this).attr("area_id");

						$("#main_menu").attr("current_area", area_id);

						get_area_data(0);
					});
				});
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

						<div class='main_btns' action="my_data">
							Личные данные
						</div>

						<div class='label'>
							Участки
						</div>

						<div class='main_btns' action="add_area">
							Добавить участок
						</div>
					</div>
				</td>

				<td class="sections" id="my_data_td">
					<div class="right_frame" id="user_data_frame">
						<div style="width:100%; color:red; font-weight:bold">* Все поля обязательные</div>

						<table class="form">
							<tr>
								<td>
									ФИО
								</td>
							</tr>
							<tr>
								<td id="fio" style="color:black">
									
								</td>
							</tr>

							<tr>
								<td>
									Дата рождения
								</td>
							</tr>
							<tr>
								<td id="birth_date" style="color:black">
									
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
									<div class="button" onclick="save_user_data()" style="width:200px">Сохранить</div>
								</td>
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
											<td>
												<div class="button" onclick="show_upload_form('passport1', 0)" style="width:200px">Страницы 1-2</div>
											</td>
											<td>
												<div class="button" onclick="show_upload_form('passport2', 0)" style="width:200px">Страница с регистрацией</div>
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

				<td class="sections" id="area_data_td">
					<div class="right_frame" id="area_data_frame">
						<div style="width:100%; color:red; font-weight:bold">* Все поля обязательные</div>

						<table class="form">
							<tr>
								<td>
									Название участка
								</td>
							</tr>
							<tr>
								<td>
									<table id="area_name" style="width:100%">
										<tr>
											<td style="width:68%; text-align:right">
												<input size=20 placeholer="Назовите как угодно">
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
									Номер участка
								</td>
							</tr>
							<tr>
								<td>
									<table id="area_nom" style="width:100%">
										<tr>
											<td style="width:56%; text-align:right">
												<input size=3 placeholer="123">
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
									Кадастровый номер
								</td>
							</tr>
							<tr>
								<td>
									<table id="kadastr" style="width:100%">
										<tr>
											<td style="width:68%; text-align:right">
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
									<div id="save_area_data" class="button" style="width:200px">Сохранить</div>
								</td>
							</tr>

							<tr>
								<td>
									Скан/Фото документов
								</td>
							</tr>
							<tr>
								<td>
									<table id="docs_img">
										<tr>
											<td>
												<div class="button" onclick="show_upload_form('sobstvennost', $('#main_menu').attr('current_area'))" style="width:230px">Выписка о праве собственности</div>
											</td>
											<td>
												<div class="button" onclick="show_upload_form('egrn', $('#main_menu').attr('current_area'))" style="width:230px">Выписка ЕГРН или свидетельство о регистрации</div>
											</td>
										</tr>
										<tr>
											<td>
												<img id="sobstvennost_img" style="height:150px">
											</td>
											<td>
												<img id="egrn_img" style="height:150px">
											</td>
										</tr>
										<tr>
											<td id="sobstvennost_img_status" style="text-align:center">
												<img class="accept" src="https://snt-morskoy.ru/img/OK.png" style="display:none">
												<img class="reject" src="https://snt-morskoy.ru/img/cancel.png" style="display:none">
											</td>
											<td id="egrn_img_status" style="text-align:center">
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
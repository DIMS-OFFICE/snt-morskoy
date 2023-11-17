<html>
	<title>Регистрация</title>

	<link href="css/reg.css" rel="stylesheet" type="text/css">
	<link href="css/jquery-ui.css" rel="stylesheet" type="text/css">

	<link rel="icon" type="vnd.microsoft.icon" href="../img/gerb.gif">

	<script type="text/javascript" src="js/jquery-2.1.3.min.js"></script>
	<script type="text/javascript" src="js/jquery.cookie.js"></script>
	<script type="text/javascript" src="js/jquery-ui.js"></script> 

	<script>
		$(document).ready(function(){
			current_date=new Date();
			current_year=current_date.getFullYear();

			$("#reg_form .birth_date").datepicker({
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

			$("#regulations").click(function(){
				$("#regulations_div").show();
			});

			$("#regulations_div .close_btn").click(function(){
				$("#regulations_div").hide();
			});

			$("#reg_btn").click(function(){
				tel_nom=$("#reg_form .tel_nom").val();
				password=$("#reg_form .password").val();
				name=$("#reg_form .name").val();
				sirname=$("#reg_form .sirname").val();
				middle_name=$("#reg_form .middle_name").val();
				birth_date=$("#reg_form .birth_date").val();

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
					$("#reg_form .error").html("Неверный формат даты рождения");

					return false;
				}

				if (password.length<6) {
					$("#reg_form .error").html("Пароль должен сожержать минимум 6 символов");

					return false;
				}

				$(this).prop("disabled", true);

				$.ajax({
					url:"php/reg.php",
					data: {tel_nom:tel_nom, password:password, name:name, sirname:sirname, middle_name:middle_name, birth_date:birth_date},
					type: "POST",
					timeout:10000,
					success: function(data) {
						try {
							data=JSON.parse(data);
						} catch (e) {
							$("#reg_form .error").html("Какая-то ошибка");

							$("#reg_btn").prop("disabled", false);

							return false;
						}

						if (data["result"]=="OK") {
							$.cookie('hash', data["hash"], { expires: 365, path: '/' });

							window.location.href="/";
						} else {
							$("#reg_form .error").html(data["result"]);	

							$("#reg_btn").prop("disabled", false);
						}
					},
					error: function(data) {
						$("#reg_form .error").html("Какая-то ошибка. Проверьте подключение");

						$("#reg_btn").prop("disabled", false);
					}
				});
			});

			$("#reg_form").animate({opacity:1},1000);
		});

		function tel_nom_length() {
			$("#reg_form .validate_code").hide();

			tel_nom=$("#reg_form .tel_nom").val();

			if (tel_nom.length==10) {
				if (/^\d{10}$/.test(tel_nom)==false) {
					$("#reg_form .error").html("Неправильно указан номер телефона. В номере должно быть 10 цифр без пробелов и первого плюса");
				} else {
					$("#reg_form .error").html("");
					$("#reg_form .validate_tel_nom_btn").show();
				}
			} else {
				$("#reg_form .validate_tel_nom_btn").hide();
			}
		}

		function validate_code_length() {
			validate_code=$("#reg_form .validate_code").val();

			$("#reg_form .error").html("");

			if (validate_code.length==4) {
				validate_tel_nom("validate");
			}		
		}

		function validate_tel_nom(action) {
			tel_nom=$("#reg_form .tel_nom").val();
			validate_code=$("#reg_form .validate_code").val();

			$("#reg_form .regenerate_validate_code").hide();

			$.ajax({
				url:"php/validate_tel_nom.php",
				data: {tel_nom:tel_nom, validate_code:validate_code, action:action},
				type: "POST",
				timeout:10000,
				success: function(data) {
					try {
						data=JSON.parse(data);
					} catch (e) {
						$("#reg_form .error").html("Какая-то ошибка");

						return false;
					}

					if (action=="code_generate") {
						if (data["result"]=="OK") {
							$("#reg_form .validate_tel_nom_btn").hide();
							$("#reg_form .validate_code").show();
							$("#reg_form .validate_code").focus();
							$("#reg_form .error").html("Отправлено");
						} else if (data["result"]=="alredy_registered") {
							$("#reg_form .validate_code").hide();
							$("#reg_form .error").html("Номер телефона уже зарегистрирован")
						} else if (data["result"]=="sms_error") {
							$("#reg_form .regenerate_validate_code").show();
							$("#reg_form .error").html("Ошибка отправки кода кода");
						} else {
							$("#reg_form .error").html("Ошибка создания кода");
						}
					} else if (action=="validate") {
						if (data["result"]=="OK") {
							$("#reg_form .validate_code").hide();
							$("#reg_form .regenerate_validate_code").hide();
							$("#reg_form .validate_tel_nom_OK").show();
							$("#reg_form .tel_nom").prop("disabled", true);
							$("#reg_form .error").html("");

							$("#reg_form .name").parent().show();
							$("#reg_form .sirname").parent().show();
							$("#reg_form .middle_name").parent().show();
							$("#reg_form .birth_date").parent().show();
							$("#reg_form .password").parent().show();
							$("#regulations").parent().show();
							$("#reg_btn").parent().show();
						} else {
							$("#reg_form .error").html("Неверный код");
						}
					}
				}
			});
		}

		function addZero(i) {
			if (i<10) {
				return "0"+i;
			} else {
				return i;
			}
		}
	</script>

	<div id="regulations_div">
		<div syle='width:100%'>
		<img class='close_btn' src='img/cancel.png' style='width:25px; margin-left:95%; cursor:pointer' title='Закрыть'/>
		</div>
		<div style='font-size:20px'>Пользовательское соглашение</div>
		<iframe src="regulations.php" style="width:95%; height: 80%; border:0; border-radius:5px"></iframe>
	</div>

	<div id="reg_form">
		<table class="reg_tbl">
			<tr>
				<td style="display:none">
					<span>Имя</span><BR>
					<input class="name" placeholder="Иван"/>
				</td>
			</tr>
			<tr>
				<td style="display:none">
					<span>Фамилия</span><BR>
					<input class="sirname" placeholder="Иванов"/>
				</td>
			</tr>
			<tr>
				<td style="display:none">
					<span>Отчество</span><BR>
					<input class="middle_name" placeholder="Иванович"/>
				</td>
			</tr>					
			<tr>
				<td style="display:none">
					<span>Дата рождения</span><BR>
					<input class="birth_date" placeholder="В формате 2000-12-31"/>
			<tr>
				<td>
					<span>Номер телефона</span><BR>
					<input class="tel_nom" placeholder="9141223344" onkeyup="tel_nom_length()"/>
				</td>
			</tr>			
			<tr>
				<td>
					<button class="validate_tel_nom_btn" onclick="validate_tel_nom('code_generate')">ПОДТВЕРДИТЬ</button>
					<input class="validate_code" onkeyup="validate_code_length()" size=4 placeholder="СМС код"/>
				</td>
			</tr>
			<tr>
				<td>
					<a class="regenerate_validate_code" href="javascript:" onclick="validate_tel_nom('code_generate')">Переотправить код</a>
					<span class="validate_tel_nom_OK">OK</span>
				</td>
			</tr>
			<tr>
				<td style="display:none">
					<span>Пароль</span><BR>
					<input class="password" placeholder="Минимум 6 знаков"/><BR>
				</td>
			</tr>
			<tr>
				<td style="font-style: italic; display:none">
					Нажимая на кнопку "Регистрация" Вы даёте согласие на обработку персональных данных<BR>
					<span id="regulations" style="text-decoration:underline; cursor:pointer">Пользовательское соглашение</span>
				</td>
			</tr>
			<tr>
				<td style="display:none">
					<button id="reg_btn">РЕГИСТРАЦИЯ</button><BR>
				</td>
			</tr>
			<tr>
				<td>
					<span class="error"></span>
				</td>
			</tr>					
		</table>
	</div>
</html>
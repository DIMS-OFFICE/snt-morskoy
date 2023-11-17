<html>
	<title>Восстановление пароля</title>

	<link href="../css/repair.css" rel="stylesheet" type="text/css">

	<link rel="icon" type="vnd.microsoft.icon" href="../img/gerb.gif">

	<script type="text/javascript" src="../js/jquery-2.1.3.min.js"></script>
	<script type="text/javascript" src="../js/jquery.cookie.js"></script>

	<script>
		$(document).ready(function(){
			$("#repair_form").animate({opacity:1},1000);
		});
		
		function tel_nom_length() {
			$("#reg_form .validate_code").hide();
			
			tel_nom=$("#repair_form .tel_nom").val();

			if (tel_nom.length==10) {
				if (/^\d{10}$/.test(tel_nom)==false) {
					$("#reg_form .error").html("Неправильно указан номер телефона. В номере должно быть 10 цифр без пробелов и первого плюса");
				} else {
					$("#repair_form .error").html("");
					$("#repair_form .validate_tel_nom_btn").show();
				}
			} else {
				$("#repair_form .validate_tel_nom_btn").hide();
			}
		}

		function validate_code_length() {
			validate_code=$("#repair_form .validate_code").val();

			$("#repair_form .error").html("");

			if (validate_code.length==4) {
				validate_tel_nom("validate");
			}		
		}

		function validate_tel_nom(action) {
			tel_nom=$("#repair_form .tel_nom").val();
			validate_code=$("#repair_form .validate_code").val();

			$("#repair_form .regenerate_validate_code").hide();

			$.ajax({
				url:"php/validate_tel_nom.php",
				data: {tel_nom:tel_nom, validate_code:validate_code, action:action},
				type: "POST",
				timeout:10000,
				success: function(data) {
					try {
						data=JSON.parse(data);
					} catch (e) {
						$("#repair_form .error").html("Какая-то ошибка");

						return false;
					}

					if (action=="code_generate") {
						if (data["result"]=="OK") {
							$("#repair_form .validate_tel_nom_btn").hide();
							$("#repair_form .validate_code").show();
							$("#repair_form .validate_code").focus();
							$("#repair_form .error").html("Отправлено");
						} else if (data["result"]=="sms_error") {
							$("#repair_form .regenerate_validate_code").show();
							$("#repair_form .error").html("Ошибка отправки кода кода");
						} else {
							$("#repair_form .error").html("Ошибка создания кода");
						}
					} else if (action=="validate") {
						if (data["result"]=="OK") {
							$("#repair_form .validate_code").hide();
							$("#repair_form .regenerate_validate_code").hide();
							$("#repair_form .validate_tel_nom_OK").show();
							$("#repair_form .tel_nom").prop("disabled", true);
							$("#repair_form .enter_password_div").show();
							$("#repair_form .error").html("");
						} else {
							$("#repair_form .error").html("Неверный код");
						}
					} else if (action=="password_repair") {
						if (data["result"]=="OK") {
							$("#repair_form .validate_tel_nom_btn").hide();
							$("#repair_form .validate_code").show();
							$("#repair_form .validate_code").focus();
							$("#repair_form .error").html("Отправлено");
						} else if (data["result"]=="sms_error") {
							$("#repair_form .regenerate_validate_code").show();
							$("#repair_form .error").html("Ошибка отправки кода кода");
						} else if (data["result"]=="no_registration") {
							$("#repair_form .error").html("Номер не зарегистрирован в системе.<BR><a href='/reg.php'>Пожалуйста, зарегистрируйтесь</a>");
						} else {
							$("#repair_form .error").html("Ошибка создания кода");
						}
					}
				}
			});
		}

		function change_password() {
			tel_nom=$("#repair_form .tel_nom").val();
			password=$("#repair_form .password").val();

			$.ajax({
				url:"php/change_password.php",
				data: {tel_nom:tel_nom, password:password},
				type: "POST",
				timeout:10000,
				success: function(data) {
					try {
						data=JSON.parse(data);
					} catch (e) {
						$("#repair_form .error").html("Какая-то ошибка");

						return false;
					}

					if (data["result"]=="OK") {
						window.location.href="/auth.php";
					} else {
						$("#repair_form .error").html("Ошибка сохранения пароля");
					}
				}
			});
		}
	</script>
			
	<div id="repair_form">
		<table class="repair_tbl">
			<tr>
				<td>
					<span>Номер телефона</span><BR>
					<input class="tel_nom" placeholder="9141223344" onkeyup="tel_nom_length()"/><BR>
					<button class="validate_tel_nom_btn" onclick="validate_tel_nom('password_repair')">ОТПРАВИТЬ КОД</button>
				</td>
			</tr>	
			<tr>
				<td>
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
				<td>
					<div class="enter_password_div">
						<span>Новый пароль</span><BR>
						<input class="password" placeholder="Минимум 6 знаков"/><BR>
						<button class="change_password_btn" onclick="change_password()">СМЕНИТЬ ПАРОЛЬ</button>
					</div>
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
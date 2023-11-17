<html>
	<title>Авторизация</title>

	<link href="../css/auth.css" rel="stylesheet" type="text/css">

	<link rel="icon" type="vnd.microsoft.icon" href="../img/gerb.gif">

	<script type="text/javascript" src="../js/jquery-2.1.3.min.js"></script>
	<script type="text/javascript" src="../js/jquery.cookie.js"></script>

	<script>
		<?php
			if (isset($_GET["from_page"])) {
				echo 'from_page="'.$_GET["from_page"].'"';
			} else {
				echo 'from_page="/"';
			}
		?>

		$(document).ready(function(){
			$("#auth_form").animate({opacity:1},1000);

			$("#enter_btn").click(function(){
				tel_nom=$("#auth_form .tel_nom").val();
				password=$("#auth_form .password").val();

				if (/^\d{10}$/.test(tel_nom)==false) {
					$("#auth_form .error").html("Неправильно указан номер телефона. В номере должно быть 10 цифр без пробелов и первого плюса");

					return false;
				}

				$(this).prop("disabled", true);

				$.ajax({
					url:"php/auth.php",
					data: {tel_nom:tel_nom, password:password},
					type: "POST",
					timeout:10000,
					success: function(data) {
						try {
							data=JSON.parse(data);
						} catch (e) {
							$("#auth_form .error").html("Какая-то ошибка");

							$("#enter_btn").prop("disabled", false);

							return false;
						}

						if (data["result"]=="OK") {
							$.cookie('hash', data["hash"], { expires: 365, path: '/' });

							window.location.href=from_page;
						} else {
							$("#auth_form .error").html(data["result"]);

							$("#enter_btn").prop("disabled", false);	
						}
					},
					error: function(data) {
						$("#auth_form .error").html("Какая-то ошибка. Проверьте подключение");

						$("#enter_btn").prop("disabled", false);	
					}
				});
			});
		});
	</script>

	<div id="auth_form">
		<span>Номер телефона</span><BR>
		<input class="tel_nom" placeholder="9141223344"/><BR>
		<span>Пароль</span><BR>
		<input class="password"/><BR>
		<button id="enter_btn">ВХОД</button><BR><BR>
		<a href="/reg.php">Регистрация</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/password_repair.php">Забыли пароль?</a><BR>
		<span class="error"></span>
	</div>
</html>
<html>
	<title>СНТ "Морской"</title>

	<link href="/css/start_page.css" rel="stylesheet" type="text/css">

	<link rel="icon" type="vnd.microsoft.icon" href="/img/gerb.gif">

	<script type="text/javascript" src="/js/jquery-2.1.3.min.js"></script>
	<script type="text/javascript" src="/js/jquery.cookie.js"></script>

	<script>
		$(document).ready(function(){
			$(".main_btns").click(function(){
				action=$(this).attr("action");

				$(this).css("background","aqua");

				$(this).animate({width:"-=5px"}, 150, function() {
					$(this).animate({width:"+=5px"}, 150, function() {
						$(this).css("background","yellow");

						if (action=="documents") {
							window.location="documents.php";
						} else if (action=="auth") {
							window.location="auth.php?from_page="+window.location.href;
						} else if (action=="exit") {
							exit();
						} else if (action=="lk") {
							window.location="lk.php";							
						} else if (action=="adm") {
							window.location="/adm";							
						} else if (action=="votes") {
							window.location="/votes.php";							
						} else if (action=="counters") {
							window.location="/counters.php";							
						}
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

				$("#main_div").animate({opacity:1},1000);
				
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

						$("#authorized_btns").show();
						$("#not_authorized_btns").hide();

						$("#hello_txt").html("Здравствуйте, "+data["user_name"]+" "+data["user_sirname"]);

						if (data["account_type"]=="admin") {
							$(".main_btns[action='adm']").show();
						} else {
							$(".main_btns[action='adm']").hide();
						}

						$("#authorized_btns .account_status").html(data["account_status"]);

						if (data["account_status"]=="Ваша регистрация подтверждена") {
							$("#authorized_btns .account_status").css("color", "green");							
						} else {
							$("#authorized_btns .account_status").css("color", "red");							
						}
					} else {
						$("#authorized_btns").hide();
						$("#not_authorized_btns").show();

						$("#not_authorized_btns .account_status").css("color", "red");
						$("#not_authorized_btns .account_status").html(data["result"]);
					}

					$("#main_div").animate({opacity:1},1000);
				}
			});
		}

		function exit() {
			hash=$.cookie("hash");

			$.ajax({
				url:"php/exit.php",
				data: {hash:hash},
				type: "POST",
				timeout:10000,
				success: function(data) {
					data=JSON.parse(data);

					if (data["result"]=="OK") {
						$.removeCookie('hash');

						window.location.reload();
					}
				}
			});
		}		
	</script>

	<body>
		<div id="main_div">
			<div id="not_authorized_btns">
				<div class="main_btns" action="auth">
					Вход/Регистрация
				</div>

				<div class="main_btns" action="documents">
					Документы
				</div>

				<!--<div class="main_btns" action="cams">
					Камеры
				</div>-->

				<div class="account_status"></div>	
			</div>
			<div id="authorized_btns">
				<div id="hello_txt"></div>

				<div class="main_btns" action="lk">
					Личный кабинет
				</div>

				<div class="main_btns" action="adm" style="display:none;">
					Администрирование
				</div>

				<div class="main_btns" action="exit">
					Выход
				</div>

				<div class="main_btns" action="documents">
					Документы
				</div>

				<div class="main_btns" action="votes">
					Голосования
				</div>

				<div class="main_btns" action="counters">
					Показания
				</div>

				<div class="account_status"></div>						
			</div>
		</div>
	</body>
</html>
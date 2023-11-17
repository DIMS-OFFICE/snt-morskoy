<html>
	<title>Голосование</title>

	<link href="../css/style.css" rel="stylesheet" type="text/css">
	<link href="../css/votes.css" rel="stylesheet" type="text/css">

	<link rel="icon" type="vnd.microsoft.icon" href="../img/gerb.gif">

	<script type="text/javascript" src="../js/jquery-2.1.3.min.js"></script>
	<script type="text/javascript" src="../js/jquery.cookie.js"></script>

	<script>
		<?php
			echo "vote_id='".$_GET["vote_id"]."';";
		?>

		$(document).ready(function(){
			$(".back_btn").click(function(){
				action=$(this).attr("action");

				$(this).css("background","aqua");

				$(this).animate({width:"-=5px"}, 150, function() {
					$(this).animate({width:"+=5px"}, 150, function() {
						$(this).css("background","yellow");

						window.history.go(-1);
					});
				});
			});

			get_vote_maket();
		});

		function get_vote_maket() {
			hash=$.cookie("hash");

			$.ajax({
				url:"php/get_vote_maket.php",
				data: {hash:hash, vote_id:vote_id},
				type: "POST",
				timeout:10000,
				success: function(data) {
					data=JSON.parse(data);

					if (data["result"]=="SESSION_ERROR") {
						window.location.href=window.location.origin+"/auth.php";
					} else if (data["result"]=="VOTE_NOT_FOUND") {
						$("#vote_maket").html("ГОЛОСОВАНИЕ НЕ НАЙДЕНО");
					} else {
						$("#header_text").html(data["vote"]["name"]);

						$("#vote_maket").html(data["vote"]["vote_maket"]);

						$("#vote_maket .vote_caption").remove();

						if (data["already_voted"]==0) {
							if (data["vote"]["active"]==1) {//Голосование активно
								$("#vote_controls").html('<div class="button" onclick="vote()" style="width:200px">Голосовать</div>');
							} else if (data["vote"]["active"]==0) {
								$("#vote_controls").html('<div class="results">Голосование не началось</div>');
							} else if (data["vote"]["active"]==2) {//Голосование закрыто
								get_vote_results();
							}
						} else {
							get_vote_results();
						}
					}
				}
			});
		}


		function vote() {
			selected=0;
			$("#vote_maket .vote_tbl").find("tr").each(function(){
				if ($(this).find("td:eq(2)").find("input").is(":checked")) {
					selected=$(this).find("td:eq(0)").text();

					return false;
				}
			});

			if (selected==0) {
				alert_form("Пожалуйста, сделайте выбор");

				return false;
			}

			$("#vote_controls").find(".button").remove();

			$.ajax({
				url:"php/vote.php",
				data: {hash:hash, vote_id:vote_id, selected:selected},
				type: "POST",
				timeout:10000,
				success: function(data) {
					if (data=="OK") {
						$("#vote_controls").html('<div class="results">Ваш голос учтён</div>');

						setTimeout(function(){
							get_vote_results();
						}, 3000);
					} else if (data=="SESSION_ERROR") {
						$("#vote_controls").html('<div class="results">Ошибка авторизации. Авторизуйтесь ещё раз</div>');

						setTimeout(function(){
							window.location.href=window.location.origin+"/auth.php";
						}, 3000);
					} else {
						$("#vote_controls").html('<div class="results">Какая-то ошибка. Голос не учтён</div>');

						setTimeout(function(){
							$("#vote_controls").html('<div class="button" onclick="vote()" style="width:200px">Голосовать</div>');
						}, 3000);
					}
				},
				error: function() {
					$("#vote_controls").html("Проверьте подключение. Голос не учтён");

					setTimeout(function(){
						$("#vote_controls").html('<div class="button" onclick="vote()" style="width:200px">Голосовать</div>');
					}, 3000);
				}
			});
		}

		function get_vote_results() {
			$.ajax({
				url:"php/get_vote_results.php",
				data: {vote_id:vote_id},
				type: "POST",
				timeout:10000,
				success: function(data) {
					data=JSON.parse(data);

					i=1;
					$("#vote_maket .vote_tbl").find("tr").each(function(){
						$(this).find("td:eq(2)").text(number_format(data[i]/data["total_votes"]*100, 2, ",", "")+"% ("+data[i]+")");

						i++;
					});

					$("#vote_controls").html('<div class="results">Всего голосов: '+data["total_votes"]+'</div>');
				}
			});
		}

		function alert_form(txt) {
			$("#alert_dialog").remove();

			str="<div id='alert_dialog'>";
			str+=txt;
			str+="<BR><button>OK</button>";
			str+="<div>";

			$("body").append(str);

			$("#alert_dialog button").click(function(){
				$("#alert_dialog").remove();
			});
		}

		function number_format(number, decimals, dec_point, separator ) {
			  number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
			  var n = !isFinite(+number) ? 0 : +number,
			    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
			    sep = (typeof separator === 'undefined') ? ',' : separator ,
			    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
			    s = '',
			    toFixedFix = function(n, prec) {
			      var k = Math.pow(10, prec);
			      return '' + (Math.round(n * k) / k)
			        .toFixed(prec);
			    };
			  // Фиксим баг в IE parseFloat(0.55).toFixed(0) = 0;
			  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
			    .split('.');
			  if (s[0].length > 3) {
			    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
			  }
			  if ((s[1] || '')
			    .length < prec) {
			    s[1] = s[1] || '';
			    s[1] += new Array(prec - s[1].length + 1)
			      .join('0');
			  }
			  return s.join(dec);
		}		
	</script>

	<body>
		<div class='back_btn' style="background: aliceblue" action="back">
			<- Назад
		</div>

		<!--<div id="header_text">Выборы, выборы.<BR>Кандидаты...<BR><div id='shnur'>С.Шнуров</div></div>-->

		<div id="header_text"></div>

		<div id="vote_form">
			<div id="vote_maket">

			</div>

			<div id="vote_controls">
				
			</div>
		</div>

		<!--<div id="info">
			Вы можете выбрать в правление неограниченное число кандидатов, но председатель должен быть только один
		</div>-->

		<div id="post_text">
			ВСЯ ПРЕДСТАВЛЕННАЯ ИНФОРМАЦИЯ НОСИТ ПРЕДВАРИТЕЛЬНЫЙ ХАРАКТЕР И НЕ ОТРАЖАЕТ ДЕЙСТВИТЕЛЬНОСТИ
		</div>
	</body>
</html>
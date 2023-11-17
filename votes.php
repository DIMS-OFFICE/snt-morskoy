<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">

		<title>Голосования</title>

		<link rel="icon" type="vnd.microsoft.icon" href="/img/gerb.gif">
		<link href="/css/votes.css" rel="stylesheet" type="text/css">

		<script type="text/javascript" src="/js/jquery-2.1.3.min.js"></script>
		<script type="text/javascript" src="/js/jquery-ui.js"></script>
		<script type="text/javascript" src="/js/jquery.cookie.js"></script>

		<script>
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

				get_votes_list();
			});

			function get_votes_list() {
				hash=$.cookie("hash");

				$.ajax({
					url:"vote/php/get_votes_list.php",
					data: {hash:hash},
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
								txt+="<td class='active'><div>Не началось</div></td>";
							} else if (data[i]["active"]==1) {
								txt+="<td class='active'><div>Активно</div></td>";
							} else {
								txt+="<td class='active'><div>Завершено</div></td>";
							}

							txt+="</tr>";
						}

						$("#votes_list").html(txt);

						$("#votes_list .clickable").on("click", function(){
							vote_id=$(this).attr("vote_id");

							window.location="vote/?vote_id="+vote_id;
						});
					}
				});
			}
		</script>
	</head>

	<body>
		<div class='back_btn' style="background: aliceblue" action="back">
			<- Назад
		</div>

		<div id="votes_list_div">
			<table id="votes_list">

			</table>
		</div>
	</body>
</html>
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">

		<title>Документы</title>

		<link href="/css/documents.css" rel="stylesheet" type="text/css">
		<link href="/css/file_upload.css" rel="stylesheet" type="text/css">

		<link rel="icon" type="vnd.microsoft.icon" href="/img/gerb.gif">

		<script type="text/javascript" src="/js/jquery-2.1.3.min.js"></script>
		<script type="text/javascript" src="/js/jquery-ui.js"></script>
		<script type="text/javascript" src="/js/jquery.cookie.js"></script>
		<script type="text/javascript" src="/js/jquery.form.js"></script>
		<script type="text/javascript" src="/js/file_upload.js"></script>

		<script>
			$(document).ready(function(){
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
							} else {
								get_docs_list(action);
							}
						});
					});
				});

				$("#add_new_document_btn").click(function() {
					$(this).css("background","aqua");

					$(this).animate({width:"-=7px"}, 150, function() {
						$(this).animate({width:"+=7px"}, 150, function() {
							$(this).css("background","yellow");
						
							$("#upload_form").show();
						});
					});
				});

				get_docs_list("all");
			});

			function get_docs_list(category) {
				hash=$.cookie("hash");

				$.ajax({
					url:"php/get_docs_list.php",
					data: {hash:hash, category:category},
					type: "POST",
					timeout:10000,
					success: function(data) {
						data=JSON.parse(data);

						if (data["result"]=="AUTHORIZED") {
							$("#add_new_document_btn").show();
						}

						$("#docs_list span").off();

						txt="";
						for (i=0; i<data["files"].length; i++) {
							txt+="<tr>";

							if (data["files"][i]["groups"].indexOf(".")>-1) {
								parts=data["files"][i]["groups"].split(".");
								parts.pop();
								file_name=parts.join(".");
							} else {
								file_name=data["files"][i]["groups"];
							}

							txt+="<td title='"+file_name+"'><div>&nbsp;&nbsp;<span file='"+data["files"][i]["file_name_rus"]+"' count='"+data["files"][i]["c"]+"'>"+file_name+"</span></div></td>";
							txt+="<td><div>"+data["files"][i]["date_time"]+"</div></td>";

							if (data["files"][i]["active"]==0) {
								txt+="<td class='transparent'>Проверяется</td>";
							}

							txt+="</tr>";
						}

						$("#docs_list").html(txt);

						$("#docs_list span").on("click", function() {
							count=$(this).attr("count");

							if (count=="1") {
								file=$(this).attr("file");

								file_download(file);
							} else {
								group=$(this).text();

								show_file_download_form(group);
							}
						});
					}
				});
			}

			function file_download(file) {
				var link = document.createElement('a');
				link.setAttribute('href', window.location.origin+"/documents/"+file);
				link.setAttribute('download', file);
				link.click();
			}

			function show_file_download_form(group) {
				$.ajax({
					url:"php/get_docs_list.php",
					data: {group:group},
					type: "POST",
					timeout:10000,
					success: function(data) {
						data=JSON.parse(data);

						$("#document_group_tbl span").off();

						txt="";
						for (i=0; i<data.length; i++) {
							txt+="<tr>";
							txt+="<td title='"+data[i]["file_name_rus"]+"'><div>&nbsp;&nbsp;<span file='"+data[i]["file_name_rus"]+"'>"+data[i]["file_name_rus"]+"</span></div></td>";
							txt+="</tr>";
						}

						$("#document_group_tbl").html(txt);

						$("#document_group_div").show();

						$("#document_group_tbl span").on("click", function() {
							file=$(this).attr("file");

							file_download(file);
						});
					}
				});
			}
		</script>
	</head>

	<body>
		<div id="document_group_div">
			<img class="close_btn" src="img/cancel.png" onclick="$('#document_group_div').hide();" title="Закрыть"/>

			<table id="document_group_tbl">

			</table>
		</div>

		<table id="main_tbl">
			<tr>
				<td style="width:225px">
					<div id="main_menu">
						<div class='main_btns' style="background: aliceblue" action="back">
							<- Назад
						</div>

						<div class='main_btns' action="all">
							ВСЕ
						</div>

						<div class='main_btns' action="ads">
							Объявления
						</div>

						<div class='main_btns' action="orders">
							Отчёты
						</div>

						<div class='main_btns' action="regulations">
							Уставные
						</div>

						<div class='main_btns' action="protocols">
							ОС
						</div>

						<div class='main_btns' action="others">
							Прочие
						</div>
					</div>
				</td>
				<td>
					<div class="right_frame">
						<div id="add_new_document_btn" onclick="show_upload_form('documents', 0)">
							Загрузить новый документ
						</div>

						<div id="docs_list_div">
							<table id="docs_list">

							</table>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</body>
</html>

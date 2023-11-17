<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">

		<title>Показания</title>

		<link href="/css/counters.css" rel="stylesheet" type="text/css">
		<link href="/css/file_upload.css" rel="stylesheet" type="text/css">

		<link rel="icon" type="vnd.microsoft.icon" href="/img/gerb.gif">

		<script type="text/javascript" src="/js/jquery-2.1.3.min.js"></script>
		<script type="text/javascript" src="/js/jquery-ui.js"></script>
		<script type="text/javascript" src="/js/jquery.cookie.js"></script>
		<script type="text/javascript" src="/js/jquery.form.js"></script>
		<script type="text/javascript" src="/js/file_upload.js"></script>

		<script>
			hash=$.cookie("hash");

			$(document).ready(function(){
				get_counters_list();

				date=new Date();
				current_year=date.getFullYear();

				monthes=Array("Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");

				for (year=2023; year<current_year+1; year++) {
					for (month=0; month<12; month++) {
						if (month<=date.getMonth()) {
							period=year+""+addZero(month+1);

							period_name=monthes[month]+" "+year;

							$("#new_period_form .period").prepend("<option value='"+period+"'>"+period_name+"</option>");
						}
					}
				}

				$("#new_period_form .counter_type").on("change", function(){
					counter_type=$(this).find("option:selected").val();

					if (counter_type=="Однотарифный") {
						$("#new_period_form .value").html("<input placeholder='Значение'/>");
					} else {
						$("#new_period_form .value").html("<input placeholder='День'/><input placeholder='Ночь'/>");
					}
				});

				$("#new_period_form .counter_doc_btn").on("click", function(){
					period=$("#new_period_form .period").find("option:selected").val();
					area_nom=$('#new_period_form .area_nom').val();
					
					if (area_nom.length==0) {
						$("#new_period_form .error_msg").html("Укажите номер участка");

						return false;
					} else {
						$("#new_period_form .error_msg").html("");
					}

					show_upload_form('counter_doc', area_nom, period);
				});
			});

			function addZero(nom) {
				if (nom<10) {
					return "0"+nom;
				} else {
					return nom;
				}
			}

			function new_period_form_show() {
				$("#new_period_form").attr("counter_id", 0);

				date=new Date();
				current_year=date.getFullYear();
				period=current_year+""+addZero(date.getMonth()+1);

				console.log(period+"/"+date.getMonth());
				$("#new_period_form .period").find("option[value='"+period+"']").prop("selected", true);
				$("#new_period_form .period").prop('disabled', false);
				
				$("#new_period_form .area_nom").val("");
				$("#new_period_form .counter_type").find("option[value='Однотарифный']").prop("selected", true);
				$("#new_period_form .value").html("<input value='' placeholder='Значение'/>");
				$("#new_period_form .for_pay").val("0.00");
				$("#new_period_form .paid").val("");

				$("#new_period_form .error_msg").html("");
				$("#new_period_form").show();
			}

			function save_new_period() {
				counter_id=$("#new_period_form").attr("counter_id");

				period=$("#new_period_form .period").find("option:selected").val();

				counter_type=$("#new_period_form .counter_type").find("option:selected").val();

				if (counter_type=="Однотарифный") {
					value=$("#new_period_form .value").find("input").val();
				} else {
					day_value=$("#new_period_form .value").find("input:eq(0)").val();
					night_value=$("#new_period_form .value").find("input:eq(1)").val();

					value=day_value+"/"+night_value;
				}

				area_nom=$("#new_period_form .area_nom").val();
				for_pay=$("#new_period_form .for_pay").val();
				paid=$("#new_period_form .paid").val().replace(",",".");

				if (area_nom.length==0) {
					$("#new_period_form .error_msg").html("Укажите номер участка");

					return false;
				}

				if (value.length==0) {
					$("#new_period_form .error_msg").html("Укажите значение счётчика");

					return false;
				}

				if (paid.length==0) {
					paid=0;
				}

				console.log(period);

				$.ajax({
					url:"php/save_new_period.php",
					data: {hash:hash, counter_id:counter_id, period:period, area_nom:area_nom, counter_type:counter_type, value:value, for_pay:for_pay, paid:paid},
					type: "POST",
					timeout:10000,
					success: function(data) {
						if (data=="NOT_AUTH") {
							window.location.href="/auth.php";
						} else if (data=="WRONG_VALUE") {
							$("#new_period_form .error_msg").html("Значение счётчика не должно быть меньше предыдущего");
						} else if (data=="OK") {
							$("#new_period_form").hide();
							get_counters_list();
						} else {
							$("#new_period_form .error_msg").html("Какая-то ошибка");
						}
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
							year=data["counters_list"][i]["period"].substring(0,4);
							month=parseInt(data["counters_list"][i]["period"].substring(4));

							period=monthes[month-1]+" "+year;

							txt+="<tr counter_id="+data["counters_list"][i]["id"]+" user_id='"+data["counters_list"][i]["user_id"]+"' period='"+data["counters_list"][i]["period"]+"' area_nom='"+data["counters_list"][i]["area_nom"]+"' counter_type='"+data["counters_list"][i]["counter_type"]+"' value='"+data["counters_list"][i]["value"]+"' for_pay='"+data["counters_list"][i]["for_pay"]+"' paid='"+data["counters_list"][i]["paid"]+"'>";

							txt+="<td><div>"+period+"</div></td>";
							txt+="<td><div>"+data["counters_list"][i]["date"]+"</div></td>";
							txt+="<td><div>"+data["counters_list"][i]["area_nom"]+"</div></td>";
							txt+="<td><div>"+data["counters_list"][i]["counter_type"]+"</div></td>";
							txt+="<td><div>"+data["counters_list"][i]["value"]+"</div></td>";
							txt+="<td><div>"+data["counters_list"][i]["for_pay"]+"</div></td>";
							txt+="<td><div>"+data["counters_list"][i]["paid"]+"</div></td>";

							//Проверка на наличие в папке квитанций квитанции за выводимый период
							if (data["files"].indexOf(data["counters_list"][i]["area_nom"]+"-"+data["counters_list"][i]["period"])>-1) {
								txt+="<td><div><a class='foto_prev_show' href='javascript:'>Квитанция</a></div></td>";
							} else {
								txt+="<td><div></div></td>";
							}

							txt+="<td><div><img src='/img/edit1.png' style='width:20px; cursor:pointer' class='edit_img'></div></td>";

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

						$("#counters_list tbody .edit_img").on("click", function(){
							counter_id=$(this).parent().parent().parent().attr("counter_id");
							period=$(this).parent().parent().parent().attr("period");
							area_nom=$(this).parent().parent().parent().attr("area_nom");
							counter_type=$(this).parent().parent().parent().attr("counter_type");
							value=$(this).parent().parent().parent().attr("value");
							for_pay=$(this).parent().parent().parent().attr("for_pay");
							paid=$(this).parent().parent().parent().attr("paid");

							$("#new_period_form").attr("counter_id", counter_id);
							$("#new_period_form .period").find("option[value='"+period+"']").prop("selected", true);
							$("#new_period_form .period").prop('disabled', 'disabled');
							$("#new_period_form .area_nom").val(area_nom);
							$("#new_period_form .counter_type").find("option[value='"+counter_type+"']").prop("selected", true);

							if (counter_type=="Однотарифный") {
								$("#new_period_form .value").html("<input value='"+value+"' placeholder='Значение'/>");
							} else {
								parts=value.split("/");

								$("#new_period_form .value").html("<input value='"+parts[0]+"' placeholder='День'/><input value='"+parts[1]+"' placeholder='Ночь'/>");
							}

							$("#new_period_form .for_pay").val(for_pay);
							$("#new_period_form .paid").val(paid);

							$("#new_period_form .error_msg").html("");

							$("#new_period_form").show();
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
		</script>
	</head>

	<body>
		<div id="add_new_period_btn" onclick="new_period_form_show()">
			Добавить новый период
		</div>

		<div id="counters_list_div">
			<table id="counters_list">
				<thead>
					<th>Период</th><th>Изменён</th><th>Номер участка</th><th>Тип счётчика</th><th>Значение</th><th>К оплате</th><th>Оплачено</th><th></th>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>

		<div id="new_period_form">
			<img class="close_btn" src="img/cancel.png" onclick="$('#new_period_form').hide();" title="Закрыть"/>

			<table>
				<thead>
					<th>Период</th><th>Номер участка</th><th>Тип счётчика</th><th>Значение</th><th>К оплате</th><th>Оплачено</th><th></th>
				</thead>
				<tr>
					<td>
						<select class='period'></select>
					</td>
					<td>
						<input class='area_nom' placeholder='Номер участка'/>
					</td>
					<td>
						<select class='counter_type'>
							<option value='Однотарифный'>Однотарифный</option>
							<option value='Двухтарифный'>Двухтарифный</option>
						</select>
					</td>
					<td class='value' style='width:125px'>
						<input placeholder='Значение'/>
					</td>
					<td>
						<input class='for_pay' value='0.00' disabled style='background:aliceblue'/>
					</td>
					<td>
						<input class='paid' placeholder='Оплачено'/>
					</td>
					<td>
						<div class="counter_doc_btn">
							Квитанция
						</div>
					</td>
				</tr>
			</table>

			<div class='save_btn' onclick="save_new_period()">
				Сохранить
			</div>

			<div class='error_msg'></div>
		</div>
	</body>
</head>
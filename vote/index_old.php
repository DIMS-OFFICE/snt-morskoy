<html>
	<title>Голосование</title>

	<link href="../css/style.css" rel="stylesheet" type="text/css">

	<link rel="icon" type="vnd.microsoft.icon" href="../img/gerb.gif">

	<script type="text/javascript" src="../js/jquery-2.1.3.min.js"></script>
	<script type="text/javascript" src="../js/jquery.cookie.js"></script>

	<script>
		<?php
			echo "tur_number='".$_GET["tur_number"]."';";
		?>

		$(document).ready(function(){
			$.cookie('vote','');
			localStorage["vote"]='';

			vote_result=$.cookie('vote');

			if (tur_number==1) {
				get_candidates('vote', 'bottom', 'DESC');
			} else {
				if (vote_result=="done" || localStorage["vote"]=="done") {
					get_candidates('results', 'bottom', 'DESC');
				} else {
					get_candidates('vote', 'bottom', 'DESC');
				}
			}
		});

		function get_candidates(action, sort_order, sort_direction) {
			$.ajax({
				url:"php/get_candidates.php",
				data: {sort_order, sort_direction, tur_number:tur_number},
				type: "POST",
				timeout:10000,
				success: function(data) {
					data=JSON.parse(data);

					if (action=="vote") {
						txt="<table>";
						txt+="<tr class='ths'>";
						txt+="<td rowspan=2>Кандидат</td>";
						txt+="<td colspan=3>В правление</td>";
						txt+="<td colspan=2>Председатель</td>";
						txt+="</tr>";
						txt+="<tr class='ths'>";
						txt+="<td class='vibor'>За</td>";
						txt+="<td class='vibor'>Против</td>";
						txt+="<td class='vibor'>Воздерж.</td>";
						txt+="<td class='vibor'>За</td>";
						txt+="<td class='vibor'>Против</td>";
						txt+="</tr>";

						for (i=0; i<data["results"].length; i++) {
							if (i%2==0) {
								class_name="even_tr";
							} else {
								class_name="odd_tr";
							}

							if (data["results"][i]["name"]!="МНЕ ВСЁ РАВНО") {
								txt+="<tr class='"+class_name+"' candidat_id='"+data["results"][i]["id"]+"'>";
								txt+="<td>"+data["results"][i]["name"]+"</td>";
								txt+="<td style='text-align:center'><input class='pravl_za' type='radio' name='"+data["results"][i]["id"]+"'></td>";
								txt+="<td style='text-align:center'><input class='pravl_protiv' type='radio' name='"+data["results"][i]["id"]+"'></td>";
								txt+="<td style='text-align:center'><input class='pravl_vozd' type='radio' checked name='"+data["results"][i]["id"]+"'></td>";
								txt+="<td style='text-align:center'><input class='preds_za' type='radio' name='preds_za' style='display:none'></td>";
								txt+="<td style='text-align:center'><input class='preds_protiv' type='checkbox'></td>";
								txt+="</tr>";
							} else {
								txt+="<tr class='"+class_name+"' candidat_id='"+data["results"][i]["id"]+"'>";
								txt+="<td>"+data["results"][i]["name"]+"</td>";
								txt+="<td colspan=5 style='text-align:center'><input class='against_all' type='checkbox' name='"+data["results"][i]["id"]+"'></td>";
								txt+="</tr>";
							}
						}

						txt+="</table>";

						if (tur_number==1) {
							txt+="<div style='font-size:22px; width:100%; text-align:center'>Голосование завершено</div>";	
						} else {
							txt+="<div style='font-size:22px; width:100%; text-align:center'>Голосование ещё не началось</div>";
							//txt+="<button onclick='vote()'>Проголосовать</button>";
						}

						$("#vote_form").html(txt);

						$(".against_all").click(function(){
							if ($(this).is(":checked")) {
								$(".preds_za").hide();
								$(".preds_protiv").hide();
								$(".pravl_za").hide();
								$(".pravl_vozd").hide();
								$(".pravl_protiv").hide();
							} else {
								$(".preds_protiv").show();
								$(".pravl_za").show();
								$(".pravl_vozd").show();
								$(".pravl_protiv").show();
							}
						});

						$(".pravl_za").click(function () {
							against_all_show_hide();

							if ($(this).is(':checked')) {
								$(this).parent().parent().find(".preds_za").show();
								$(this).parent().parent().find(".preds_protiv").show();
							} else {
								$(this).parent().parent().find(".preds_za").hide();
								$(this).parent().parent().find(".preds_protiv").hide();
							}
						});

						$(".pravl_protiv, .pravl_vozd").click(function () {
							against_all_show_hide();

							if ($(this).is(':checked')) {
								$(this).parent().parent().find(".preds_za").hide();
								$(this).parent().parent().find(".preds_protiv").show();
							} else {
								$(this).parent().parent().find(".preds_za").show();
								$(this).parent().parent().find(".preds_protiv").hide();
							}
						});

						$(".preds_za").click(function () {
							against_all_show_hide();

							if ($(this).is(':checked')) {
								$(".preds_protiv").show();
								$(this).parent().parent().find(".preds_protiv").hide();
							} else {
								$(this).parent().parent().find(".preds_protiv").show();
							}
						});
					} else {
						txt="<table>";
						txt+="<tr class='ths'>";
						txt+="<td rowspan=2>Кандидат</td>";
						txt+="<td colspan=3>В правление</td>";
						txt+="<td colspan=2>Председатель</td>";
						txt+="</tr>";
						txt+="<tr class='ths'>";
						txt+="<td class='vibor' sort_order='pravl_za' sort_direction='DESC'>За</td>";
						txt+="<td class='vibor' sort_order='pravl_protiv' sort_direction='DESC'>Против</td>";
						txt+="<td class='vibor' sort_order='pravl_vozd' sort_direction='DESC'>Воздерж.</td>";
						txt+="<td class='vibor' sort_order='preds_za' sort_direction='DESC'>За</td>";
						txt+="<td class='vibor' sort_order='preds_protiv' sort_direction='DESC'>Против</td>";
						txt+="</tr>";

						for (i=0; i<data["results"].length; i++) {
							if (i%2==0) {
								class_name="even_tr";
							} else {
								class_name="odd_tr";
							}

							if (data["results"][i]["name"]!="МНЕ ВСЁ РАВНО") {
								txt+="<tr class='"+class_name+"' candidat_id='"+data["results"][i]["id"]+"'>";
								txt+="<td>"+data["results"][i]["name"]+"</td>";

								if (data["totals_votes"]["pravl_za"]>0) {
									pravl_za_txt=number_format(data["results"][i]["pravl_za"]/data["totals_votes"]["pravl_za"]*100, 1, ",", "")+"% ("+data["results"][i]["pravl_za"]+")";
								} else {
									pravl_za_txt="-";
								}

								if (data["totals_votes"]["pravl_protiv"]>0) {
									pravl_protiv_txt=number_format(data["results"][i]["pravl_protiv"]/data["totals_votes"]["pravl_protiv"]*100, 1, ",", "")+"% ("+data["results"][i]["pravl_protiv"]+")";
								} else {
									pravl_protiv_txt="-";
								}

								if (data["totals_votes"]["pravl_vozd"]>0) {
									pravl_vozd_txt=number_format(data["results"][i]["pravl_vozd"]/data["totals_votes"]["pravl_vozd"]*100, 1, ",", "")+"% ("+data["results"][i]["pravl_vozd"]+")";
								} else {
									pravl_vozd_txt="-";
								}

								if (data["totals_votes"]["preds_za"]>0) {
									preds_za_txt=number_format(data["results"][i]["preds_za"]/data["totals_votes"]["preds_za"]*100, 1, ",", "")+"% ("+data["results"][i]["preds_za"]+")";
								} else {
									preds_za_txt="-";
								}

								if (data["totals_votes"]["preds_protiv"]>0) {
									preds_protiv_txt=number_format(data["results"][i]["preds_protiv"]/data["totals_votes"]["preds_protiv"]*100, 1, ",", "")+"% ("+data["results"][i]["preds_protiv"]+")";
								} else {
									preds_protiv_txt="-";
								}

								if (data["leaders"]["pravl_za"].indexOf(data["results"][i]["id"])>-1) {
									class_name1="green";
								} else {
									class_name1="";
								}

								if (data["leaders"]["pravl_protiv"].indexOf(data["results"][i]["id"])>-1) {
									class_name2="red";
								} else {
									class_name2="";
								}

								if (data["leaders"]["preds_za"].indexOf(data["results"][i]["id"])>-1) {
									class_name3="green";
								} else {
									class_name3="";
								}

								if (data["leaders"]["preds_protiv"].indexOf(data["results"][i]["id"])>-1) {
									class_name4="red";
								} else {
									class_name4="";
								}

								txt+="<td style='text-align:center' class='"+class_name1+"'>"+pravl_za_txt+"</td>";
								txt+="<td style='text-align:center' class='"+class_name2+"'>"+pravl_protiv_txt+"</td>";
								txt+="<td style='text-align:center'>"+pravl_vozd_txt+"</td>";
								txt+="<td style='text-align:center' class='"+class_name3+"'>"+preds_za_txt+"</td>";
								txt+="<td style='text-align:center' class='"+class_name4+"'>"+preds_protiv_txt+"</td>";
								txt+="</tr>";
							} else {
								txt+="<tr style='background:white' candidat_id='"+data["results"][i]["id"]+"'>";
								txt+="<td>"+data["results"][i]["name"]+"</td>";

								if (data["votes_count"]>0) {
									against_txt=number_format(data["against_all"]/data["votes_count"]*100, 1, ",", "")+"% ("+data["against_all"]+")";
								} else {
									against_txt="-";
								}

								txt+="<td colspan=5 style='text-align:center'>"+against_txt+"</td>";
								txt+="</tr>";
							}
						}

						txt+="</table>";

						if (tur_number==1) {
							txt+="<div class='votes_count_div'>Всего проголосовало: "+data["votes_count"]+" (Голосование завершено)</div>";	
						} else {
							txt+="<div class='votes_count_div'>Всего проголосовало: "+data["votes_count"]+" (Голосование ещё не началось)</div>";
							//txt+="<button onclick='vote()'>Проголосовать</button>";
						}

						$("#vote_form").html(txt);

						if (sort_direction=="DESC") {
							$(".vibor[sort_order='"+sort_order+"']").attr("sort_direction", "ASC");
						} else {
							$(".vibor[sort_order='"+sort_order+"']").attr("sort_direction", "DESC");
						}

						$(".vibor").click(function () {
							sort_order=$(this).attr("sort_order");
							sort_direction=$(this).attr("sort_direction");

							get_candidates("results", sort_order, sort_direction);
						});
					}
				},
				error: function() {
					alert_form("Какая-то ошибка при загрузке кандидатов");
				}
			});
		}

		function against_all_show_hide() {
			checked=0;
			$(".pravl_za, .pravl_protiv, .pravl_vozd, .preds_za, .preds_protiv").each(function() {
				checked++;
			});

			if (checked>0) {
				$(".against_all").hide();
			} else {
				$(".against_all").show();
			}
		}

		function vote() {
			pravl_za=Array();
			pravl_protiv=Array();
			pravl_vozd=Array();
			preds_protiv=Array();

			against_all=0;
			preds_za=0;

			$(".pravl_za").each(function(){
				if (this.checked && $(this).is(":visible")) {
					pravl_za.push($(this).parent().parent().attr("candidat_id"));
				}
			});

			$(".pravl_protiv").each(function(){
				if (this.checked && $(this).is(":visible")) {
					pravl_protiv.push($(this).parent().parent().attr("candidat_id"));
				}
			});

			$(".pravl_vozd").each(function(){
				if (this.checked && $(this).is(":visible")) {
					pravl_vozd.push($(this).parent().parent().attr("candidat_id"));
				}
			});			

			$(".preds_za").each(function(){
				if (this.checked && $(this).is(":visible")) {
					preds_za=$(this).parent().parent().attr("candidat_id");
				}
			});
			
			$(".preds_protiv").each(function(){
				if (this.checked && $(this).is(":visible")) {
					preds_protiv.push($(this).parent().parent().attr("candidat_id"));
				}
			});

			if ($(".against_all").is(":checked")==false) {
				if (pravl_za.length==0) {
					alert_form("Укажите хотя бы одного члена правления");

					return false;
				}

				if (preds_za==0) {
					alert_form("А кто будет председателем?<BR>Нужно указать");

					return false;
				}
			} else {
				against_all=1;
			}

			$("#vote_form").find("button").prop("disabled", true);

			$.ajax({
				url:"php/vote.php",
				data: {pravl_za:JSON.stringify(pravl_za), pravl_protiv:JSON.stringify(pravl_protiv), pravl_vozd:JSON.stringify(pravl_vozd), preds_protiv:JSON.stringify(preds_protiv), preds_za:preds_za, against_all:against_all, tur_number:tur_number},
				type: "POST",
				timeout:10000,
				success: function(data) {
					if (data=="already_vote") {
						alert_form("Вы уже голосовали...");
					} else {
						$.cookie('vote', 'done');

						localStorage["vote"]='done';

						get_candidates('results','bottom','DESC');
					}
				},
				error: function() {
					alert_form("Какая-то ошибка. Голос не учтён");

					$("#vote_form").find("button").prop("disabled", false);
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
		<div id="header_text">Выборы, выборы.<BR>Кандидаты...<BR><div id='shnur'>С.Шнуров</div></div>

		<div id="vote_form">

		</div>

		<div id="info">
			Вы можете выбрать в правление неограниченное число кандидатов, но председатель должен быть только один
		</div>

		<div id="post_text">
			ВСЯ ПРЕДСТАВЛЕННАЯ ИНФОРМАЦИЯ НОСИТ ПРЕДВАРИТЕЛЬНЫЙ ХАРАКТЕР И НЕ ОТРАЖАЕТ ДЕЙСТВИТЕЛЬНОСТИ
		</div>
	</body>
</html>
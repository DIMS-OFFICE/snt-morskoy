<html>
	<title>
		TEST
	</title>
	<head>
		<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/start/jquery-ui.css"rel="stylesheet" />

		<style>
			.ui-resizable-helper {
			    border: 1px dotted gray;
			}
			.resizable {
			    display: block;
			    width: 480px;
			    height: 800px;
			    padding: 30px;
			    border: 2px solid gray;
			    overflow: hidden;
			    position: relative;
			}

			iframe {
			    width: 100%;
			    height: 100%;
			}

			#menu_div div {
				display:inline;
				cursor: pointer;
				background: lightgray;
				margin-left: 10px;
			}
		</style>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>

		<script>
			$(function () {
				width=$("iframe").width();
				height=$("iframe").height();

				$("#size").text(width+"X"+height);

				setInterval(function(){
					width=$("iframe").width();
					height=$("iframe").height();

					$("#size").text(width+"X"+height);
				},1000);

			    $(".resizable").resizable();

			    page_open(1);
			});

			function page_open(action) {
				$("iframe").attr("src", "golos.php");
			}
		</script>
	</head>

	<body>
		<div id="menu_div">
			<div id="size"></div>
		</div>

		<div class="resizable">
			<iframe src="" style="width:100%; height:100%"></iframe>
		</div>
	</body>
</html>
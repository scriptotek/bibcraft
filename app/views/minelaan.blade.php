<?php
error_reporting(E_ERROR);

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>BibCraft - Samling</title>
	<script type="text/javascript" src="/jquery-1.9.1.min.js"></script>
	<link href="/bootstrap/css/bootstrap.min.css" media="screen" type="text/css" rel="stylesheet" />
	<link href="/selvbetjening.css" type="text/css" rel="stylesheet" />
	<script type="text/javascript">

		cmd = "minelan";


        function found_item(item) {
            console.log('Fant et nytt kort!')
            console.log(item);
            $('#card_id').html(item.id);
            if (item.usage_type == 'patron-card') {
                $('#card_type').html('Lånekortid');
            } else if (item.usage_type == 'for-circulation') {
                $('#card_type').html('Id');
            }
        }


		function listener() {

			if (inputKort!=$("#inputkort").val() && $("#inputkort").val()!="" && $("#inputkort").val()!=null) {
					//alert('text|'+$("#inputkort").val()+'|----var|'+inputKort+'|');
					ajaxCall($("#inputkort").val());
			}
			if ($("#inputkort").val()!="" && $("#inputkort").val()!=null) inputKort = $("#inputkort").val();

			//sjekk pilene
			if (parseInt($("#maxpage").val())<=6) $("#arrow_right").hide();
			if (parseInt($("#maxpage").val())>=7) $("#arrow_right").show();

			if (parseInt($("#page").val())<=6) $("#arrow_left").hide();
			if (parseInt($("#page").val())>=7) $("#arrow_left").show();

			//$("#lane").text($("#maxpage").val());

		}

		function ajaxCall(ik){
			 if(typeof pg === 'undefined') pg=0;

			/*
			ik=1;
			pg=0;
			cmd = "minelan";
			alert(pg+" "+ik+" "+cmd);
*/
			$.ajax({
				type: "POST",
				url: "../klass/server.php",
				contentType : "application/x-www-form-urlencoded; charset=iso-8859-1",
				data: { command: cmd, page: pg, userid: ik}
				}).done(function( serverData ) {

					processFromServer(serverData);
			});
		}

		function processFromServer(serverData){

			if (serverData!="") {
				$("#output").html(serverData);

			}
		}

		$(document).ready(function() {

			<?php
				if ($_GET["userid"]) echo "ajaxCall('".$_GET["userid"]."')";

			?>

			var connection = new WebSocket('ws://labs.biblionaut.net:8080');

            connection.onopen = function () {
                console.log('Connection open!');
                // Identify us as a frontend client
                connection.send(JSON.stringify({
                    'msg': 'hello',
                    'role': 'frontend'
                }));
            };

            connection.onmessage = function (e) {
                var data = JSON.parse(e.data);
                switch (data.msg) {
                    case 'new-item':
                       // found_item(data.item);
						 console.log(data.item);
						ajaxCall(data.item.id);
                        break;
                }
            };

			$("#arrow_left").hide();
			$("#arrow_right").hide();
			inputKort = $("#inputkort").val();

			$('body').on('mousedown', function (e) {
				if ($(e.target).is('a') || $(e.target).is('input') || $(e.target).is('button') || $(e.target).is('label')) {
			  // pass
				} else {
					e.preventDefault();
				}
			//e.stopPropagation();
			});

			inputKort = $("#inputkort").val();

			////////////Listener
			setInterval("listener()", 500);
			///////////////////////

			$("#inputkort").focus();

			$("#lane").click(function(){

				$("#lane").css('backgroundColor','#3869FF');
				window.location = "/selvbetjening";

			});

			$("#saml").click(function(){

				$("#saml").css('backgroundColor','#3869FF');
				window.location = "/samling";

			});


			pg = 0;

			//$("#inputtext").on("propertychange, change, keyup, paste, input", function(){
			$("#arrow_right").click(function(){

				pg = (parseInt($("#page").val())+6);

				ajaxCall(inputKort);

			});

			$("#arrow_left").click(function(){

				pg = (parseInt($("#page").val())-6);

				ajaxCall(inputKort);

			});

		});

	</script>

	<style>
		#title{
			/*border: white 1px solid;*/
			float:left;

		}

		#item {
			border: white 1px solid;
			padding:15px;
			background-color:#3869FF;
			height:300px;
			width:370px;
			margin:0px 10px 30px 8px;
			float:left;
			-moz-border-radius: 15px;
			border-radius: 15px;
			/*
			-moz-box-shadow: inset 0px 0px 47px 3px #4c3f37;
			-webkit-box-shadow: inset 0px 0px 47px 3px #4c3f37;
			box-shadow: inset 0px 0px 277px 3px #4c3f37;
			*/
		}

		#item img {
			float:left;
			height:300px;
			margin-right:15px;

		}

		#arrow_left {
			position:absolute;
			left:500px;
			padding:10px;
			/*border: white 1px solid;*/
			float:left;
			width:70px;
		}

		#arrow_right{
			position:absolute;
			/*border: white 1px solid;*/
			padding:10px;
			right:500px;
			float:left;
			width:70px;
		}

		#output {
		/*	border: white 1px solid;*/
			height:700px;
			width:1260px;
			overflow: none;
		}

		#content {

			/*border: white 1px solid;*/
			margin-left:10px;
			height:800px;
			width:1260px;
		}

		#header{
			/*border: white 1px solid;*/
			padding:15px;
			position:relative;
			height:80px;
			width:1230px;
		}

		#foter{
			/*border: white 1px solid;*/
			padding:15px;
			position:relative;
			height:80px;
			width:1230px;
			clear:both;
		}

		.tab{
			margin-top:40px;
			/*border: white 1px solid;*/
			float:right;
			padding-left:10px;
			padding-right:10px;
			-moz-border-radius: 15px;
			border-radius: 15px;
			height:60px;
			width:100px;
			margin-left:10px;
		}
		#saml{
			margin-top:20px;
			border: white 1px solid;
			float:right;
			padding-top:10px;
			padding-left:40px;
			padding-right:10px;
			-moz-border-radius: 15px;
			border-radius: 15px;
			height:70px;
			width:110px;
			margin-right:10px;
		}

		#lane{
			margin-top:20px;
			border: white 1px solid;
			float:right;
			padding-top:10px;
			padding-left:40px;
			padding-right:10px;
			-moz-border-radius: 15px;
			border-radius: 15px;
			height:70px;
			width:110px;
			margin-right:10px;
		}

		#lesekort{
			margin-left:17px;

		}

	</style>

</head>

<body>

<form>
<div id="content">
	<div id="header">

		<div id="title" style="float:left"><h1>BibCraft</h1></div>
		<div style="float:left; font-size:22px; margin-top:24px; margin-left:20px;"><b>Realfagsbiblioteket</b></div>
		<div style="clear:left; font-size:18px; margin-top:6px;">Oversikt over egne lån</div>

	</div>
	<div style="clear:both">&nbsp;</div>

	<div id="output">

		<div id="lesekort">Før lånekortet mot RFID-leseren</div>
		<input type="hidden" id="page" value="0">

	</div>
	<div id="foter">
		<div id="arrow_left"><img src="/img/arrow_left.gif"></div>
		<div id="arrow_right"><img src="/img/arrow_right.gif"></div>
		<div id="saml"><h1>Søk<h1></div>
		<div id="lane"><h1>Lån<h1></div>
	</div>

</div>
</form>
</body>
</html>
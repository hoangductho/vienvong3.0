<!DOCTYPE html>
<html>
<head>
	<title>Vienvong.com</title>
	<style type="text/css">
		* {
			margin: 0;
			padding: 0;
			font-family: 'Helvetica', 'arial', 'nimbussansl', 'liberationsans', 'freesans', 'clean', 'sans-serif', "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
		}

		body {
			float: left;
			width: 100%;
			border: 1px solid #ccc;
		}

		header {
			height: 50px;
			box-shadow: 1px 1px 1px #cecece;
			line-height: 50px;
			background-color: #ececec;
		}

		main {
			min-height: 320px;
		}

		main p {
			text-align: justify;
			padding: 7px;
		}

		footer {
			height: 50px;
			line-height: 50px;
			border-top: 1px solid #ccc;
		}

		.row {
			float: left;
			width: 100%;
		}

		.left {
			float: left;
		}

		.right {
			float: right;
		}

		.inline {
			display: inline-block;
		}

		.fixed-width {
			-webkit-box-sizing: border-box; /* Safari/Chrome, other WebKit */
			-moz-box-sizing: border-box;    /* Firefox, other Gecko */
			box-sizing: border-box;         /* Opera/IE 8+ */
		}

		.horzi-pad {
			padding-left: 10px;
			padding-right: 10px;
		}

		.verti-pad {
			padding-top: 10px;
			padding-bottom: 10px;
		}

		.large-text {
			font-size: 24px
		}

		.center-text {
			text-align: center;
		}

		.blur-text {
			color: #777;
		}

		.text-link {
			color: #333;
			text-decoration: none;
		}
	</style>
</head>
<body>
	<header class='row horzi-pad fixed-width'>
		<div class="logo left inline">
			<a href="//vienvong.com" class="text-link"><h2>Vienvong</h2></a>
		</div>
		<div class="homepage right inline">
			<a href="//vienvong.com" class="text-link"><span class='large-text'>&#8962;</span> Trang chủ</a>
		</div>
	</header>
	<main class='row horiz-pad fixed-width'>
		<?=$ViewContentHTML?>
	</main>
	<footer class='row center-text blur-text'>
		<div class="center-text">@Vienvong Team</div>
	</footer>
</body>
</html>

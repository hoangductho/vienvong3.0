<!DOCTYPE html>
<html>
<head>
	<title>Vienvong.com</title>
	<style>
		* {
			margin: 0;
			padding: 0;
			font-family: 'Helvetica', 'arial', 'nimbussansl', 'liberationsans', 'freesans', 'clean', 'sans-serif', "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
		}

		header {
			float: left;
			width: 100%;
			height: 50px;
			line-height: 50px;
			background-color: #ececec;
			border-bottom: 1px solid #cecece;
		}

		section {
			float: left;
			width: 100%;
			min-height: 320px;
			text-align: justify;
		}

		section p {
			text-align: justify;
			padding: 7px;
		}

		footer {
			float: left;
			width: 100%;
			height: 50px;
			line-height: 50px;
			border-top: 1px solid #ccc;
			text-align: center;
			color: #777;
		}
	</style>
</head>
<body>
	<div style="-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; float: left; border: 1px solid #ccc;">
		<header>
			<div style="-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; padding: 0 10px; height: 50px; box-shadow: 1px 1px 1px #cecece;">
				<span style="float: left; display: inline-block;">
					<a href="//vienvong.com" style="color: #333; text-decoration: none;"><h2>Vienvong</h2></a>
				</span>
				<span style="float: right; display: inline-block;">
					<a href="//vienvong.com" style="color: #333; text-decoration: none;"><span class='large-text'>&#8962;</span> Trang chủ</a>
				</span>
			</div>
		</header>
		<section style="-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;">
			<div  style="-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; padding 10px">
				<?=$ViewContentHTML?>
			</div>
		</section>
		<footer>
			<div  style="-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;">@Vienvong Team</div>
		</footer>
	</div>
</body>
</html>

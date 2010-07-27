<html>
	<head>
		<title>EasyOpenID Authentication Example</title>
		<base href="<?php echo $this->config->item('base_url'); ?>" />
	</head>
	<style type="text/css">
			* {
				font-family: verdana, sans-serif;
				color: #333;
			}
			a {
				font-size: 0.8em;
				color: #668;
				text-decoration: none;
			}
			a:hover {
				color: #448;
				text-decoration: underline;
			}
			body {
				width: 50em;
				margin: 1em;
			}
			div {
				padding: .5em;
			}
			.data {
				border: 1px solid #666666;
				background: #888888;
			}
			.data pre {
				color: #fff;
				font-family: monospace;
			}
			#verify-form {
				border: 1px solid #777777;
				background: #dddddd;
				margin-top: 1em;
			}
	</style>
	<body>
		<h1>EasyOpenID Authentication Example</h1>
		<p>
			This example consumer uses the <a href="http://github.com/kbjr/EasyOpenID">
			EasyOpenID class</a> built on top of the <a href="http://github.com/openid/php-openid">
			PHP OpenID</a> library. It just verifies that the URL that you enter is your identity URL.
		</p>

		<?php if (isset($data) && ! empty($data)) {
			print '<div class="data"><pre>'.print_r($data, true).'</pre></div>';
		} ?>

		<div id="verify-form">
			<a href="test/third_party/openid">Sign in with OpenID</a><br />
			<?php if (isset($openid) && $openid) : ?>
			<form action="test/third_party/openid-form" method="post">
				<input type="text" name="id" value="" />
				<input type="submit" value="Sign in" />
			</form>
			<?php endif; ?>
			<a href="test/third_party/google">Sign in with Google Accounts</a><br />
			<a href="test/third_party/yahoo">Sign in with Yahoo!</a><br />
			<a href="test/third_party/aol">Sign in with AOL</a>
		</div>
	</body>
</html>

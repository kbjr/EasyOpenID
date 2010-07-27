<?php
$links = $this->openid->build_auth(
	'test/third_party',
	array('openid', 'google', 'yahoo'),
	'test/load_icon'
);
?>
<html>
	<head>
		<title>EasyOpenID Authentication Example</title>
		<base href="<?php echo $this->config->item('base_url'); ?>" />
	</head>
	<style type="text/css">
			* {
				font-family: helvetica, verdana, sans-serif;
				font-weight: 100;
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
				padding: .8em;
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
			#verify-form a[rel="openid"] img {
				position: relative;
				top: 3px;
				left: -3px;
			}
			form {
				margin: 0.5em;
				padding: 0;
			}
	</style>
	<body>
		<h1>EasyOpenID Authentication Example</h1>
		<p>
			This example consumer uses the <a href="http://github.com/kbjr/EasyOpenID">
			EasyOpenID class</a> built on top of the <a href="http://github.com/openid/php-openid">
			PHP OpenID</a> library. It just verifies that the URL that you enter is your identity URL.
		</p>

		<?php if (isset($data) && ! empty($data)) :
			print '<div class="data"><pre>'.print_r($data, true).'</pre></div>';
		endif; ?>

		<div id="verify-form">
			<?php
				foreach ($links as $link) :
					echo $link->anchor."<br />\n";
					if ($link->provider == 'openid' && isset($openid) && $openid) : ?>
						<form action="test/third_party/openid" method="post">
							<input type="text" name="openid" value="" />
							<input type="submit" value="Sign In" />
						</form>
					<?php endif;
				endforeach;
			 ?>
		</div>
	</body>
</html>

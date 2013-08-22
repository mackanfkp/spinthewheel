<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
	<link href="/css/main.css" rel="stylesheet" media="screen">

	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>

	<script type="text/javascript">
		ci_base_url = '<?php echo base_url(); ?>index.php/';
	</script>

	<script type="text/javascript" src="/js/main.js"></script>

	<title>Spin the Wheel</title>
</head>
<body>
	<div id="wrap">
		<header class="jumbotron" id="heading">
			<div class="container">
				<h1>Spin the Wheel</h1>
			</div>
		</header>

		<div class="container">
			<div id="menu">
				<ul class="inline pull-left">
					<li><?php echo anchor('', 'Home', 'title="Home"'); ?></li>
					<li><?php echo anchor('main/players', 'Players', 'title="Players"'); ?></li>
					<li><?php echo anchor('main/bonuses', 'Bonuses', 'title="Bonuses"'); ?></li>
					<li><?php echo anchor('game', 'Attach bonus', 'title="Attach bonus"'); ?></li>
					<li><?php echo anchor('game/play', 'Play game', 'title="Play"'); ?></li>
				</ul>
				<?php if ($_loginid = $this->session->userdata('id')): ?>
					<ul class="inline pull-right">
						<li>
							Logged in as
							<?php echo anchor('main/players/'. $_loginid, $this->session->userdata('username')); ?>
							[<?php echo anchor('main/logout', 'Logout', 'title="Log out"'); ?>]
						</li>
					</ul>
				<?php endif; ?>
				<div class="clearfix"></div>
			</div>

			<?php if ($flash = $this->session->flashdata('success')): ?>
				<div class="alert alert-success">
					<h4>Congratulations!</h4>
					<?php echo $flash; ?>
				</div>
			<?php elseif ($flash = $this->session->flashdata('error')): ?>
				<div class="alert alert-error">
					<h4>Warning!</h4>
					<?php echo $flash; ?>
				</div>
			<?php endif; ?>

			<div id="main">
				<?php echo isset($__content) ? $__content : ''; ?>
			</div>
		</div>
	</div>
</body>
</html>
<div class="container">
	<h2><?php printf('%s, %s', $player->get('lastname'), $player->get('firstname')); ?></h2>

	<table class="table">
	<tr>
		<th>Email</th>
		<th>Age</th>
		<th>Gender</th>
	</tr>
	<tr>
		<td><?php echo $player->get('username'); ?></td>
		<td><?php echo $player->get('age'); ?></td>
		<td><?php echo $player->get('gender'); ?></td>
	</tr>
	</table>

	<br>

	<h3>Real money wallet</h3>
	<table class="table">
	<tr>
		<th>Initial value</th>
		<th>Current value</th>
		<th>Created</th>
		<th>Updated</th>
		<th>Status</th>
	</tr>

	<tr>
		<td><?php echo $realmoneywallet->get('initial_value'); ?></td>
		<td><?php echo $realmoneywallet->get('current_value'); ?></td>
		<td><?php echo $realmoneywallet->get('date_create'); ?></td>
		<td><?php echo $realmoneywallet->get('date_update'); ?></td>
		<td><?php echo $realmoneywallet->get('status'); ?></td>
	</tr>
	</table>

	<br>

	
	<h3>Bonus wallets</h3>

	<table class="table">
	<tr>
		<th>Initial value</th>
		<th>Current value</th>
		<th>Created</th>
		<th>Updated</th>
		<th>Status</th>
	</tr>
	<?php if (! $bonuswallets): ?>
	
		<tr><td colspan="5"><em>No bonus wallets found...</em></td></tr>

	<?php else: ?>
		<?php foreach ($bonuswallets as $wallet): ?>
			<tr>
				<td><?php echo $wallet->get('initial_value'); ?></td>
				<td><?php echo $wallet->get('current_value'); ?></td>
				<td><?php echo $wallet->get('date_create'); ?></td>
				<td><?php echo $wallet->get('date_update'); ?></td>
				<td><?php echo $wallet->get('status'); ?></td>
			</tr>
		<?php endforeach; ?>

	<?php endif; ?>
	</table>

	<br>


	<p><a href="javascript:;" onclick="history.go(-1);">&laquo; back</a></p>
</div>
<div class="form-left">
	<h2>Add Bonus</h2>

	<?php echo form_open('main/bonuses',  array('class' => 'form')) ?>

		<div class="errors">
			<?php echo validation_errors(); ?>
		</div>

		<div class="control-group">
			<?php echo form_label('Name', 'name'); ?>

			<div class="controls">
				<?php echo form_input($inputs['name']); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo form_label('Trigger', 'trigger'); ?>

			<div class="controls">
				<?php echo form_dropdown($inputs['trigger']['name'],
					$inputs['trigger']['options'],
					$inputs['trigger']['selected'],
					$inputs['trigger']['extra']); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo form_label('Reward wallet type', 'reward_wallet_type'); ?>

			<div class="controls controls-row">
				<?php echo form_dropdown($inputs['reward_wallet_type']['name'],
					$inputs['reward_wallet_type']['options'],
					$inputs['reward_wallet_type']['selected'],
					$inputs['reward_wallet_type']['extra']); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo form_label('Value of reward', 'value_of_reward'); ?>

			<div class="controls controls-row">
				<?php echo form_input($inputs['value_of_reward']); ?>

				<?php echo form_dropdown($inputs['value_of_reward_type']['name'],
					$inputs['value_of_reward_type']['options'],
					$inputs['value_of_reward_type']['selected'],
					$inputs['value_of_reward_type']['extra']); ?>
			</div>
		</div>

		<div class="control-group" id="multiplier-holder">
			<?php echo form_label('Wagering multiplier (Not applied if reward wallet type is realmoney)', 'multiplier'); ?>

			<div class="controls">
				<?php echo form_input($inputs['multiplier']); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo form_label('Status', 'status'); ?>

			<div class="controls">
				<?php echo form_dropdown($inputs['status']['name'],
					$inputs['status']['options'],
					$inputs['status']['selected'],
					$inputs['status']['extra']); ?>
			</div>
		</div>

		<div class="control-group">
			<div class="controls">
				<input type="submit" class="btn-primary" name="addBonus" value="Add bonus" />
			</div>
		</div>
	</form>

</div>

<div class="form-right">
	<h2>Current Bonuses</h2>

	<table class="table">
	<tr>
		<th>Name</th>
		<th>Trigger</th>
		<th>Wallet</th>
		<th>Value</th>
		<th>Multiplier</th>
		<th>Status</th>
	</tr>

	<?php if (! $bonuslist): ?>
		<tr><td colspan="4"><em>No bonuses found...</em></td></tr>
	<?php endif; ?>

	<?php foreach ($bonuslist as $bonus): ?>

		<tr>
			<td><?php echo $bonus->get('name'); ?></td>
			<td><?php echo $bonus->get('trigger'); ?></td>
			<td><?php echo $bonus->get('reward_wallet_type'); ?></td>
			<td>
				<?php if ($bonus->get('value_of_reward_type') == 'PERCENT'): ?>
					<?php echo $bonus->get('value_of_reward'); ?>%
				<?php else: ?>
					&euro; <?php echo $bonus->get('value_of_reward'); ?>
				<?php endif; ?>
			</td>
			<td><?php echo $bonus->get('multiplier'); ?></td>
			<td><?php echo $bonus->get('status'); ?></td>
		</tr>

	<?php endforeach; ?>

	</table>
</div>
<div class="clearfix"></div>
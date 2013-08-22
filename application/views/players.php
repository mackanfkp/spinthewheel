<div class="form-left">
	<h2>Add Player</h2>

	<?php echo form_open('main/players',  array('class' => 'form')) ?>

		<div class="errors">
			<?php echo validation_errors(); ?>
		</div>

		<div class="control-group">
			<?php echo form_label('Email', 'username'); ?>

			<div class="controls">
				<?php echo form_input($inputs['username']); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo form_label('Password', 'password'); ?>

			<div class="controls">
				<?php echo form_password($inputs['password']); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo form_label('Firstname', 'firstname'); ?>

			<div class="controls">
				<?php echo form_input($inputs['firstname']); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo form_label('Lastname', 'lastname'); ?>

			<div class="controls">
				<?php echo form_input($inputs['lastname']); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo form_label('Age', 'age'); ?>

			<div class="controls">
				<?php echo form_dropdown($inputs['age']['name'],
					$inputs['age']['options'],
					$inputs['age']['selected'],
					$inputs['age']['extra']); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo form_label('Gender', 'gender'); ?>

			<div class="controls">
				<label for="gender_m" class="radio">
					<?php
					echo form_radio($inputs['gender_m']); ?>
					Male
				</label>

				<label for="gender_f" class="radio">
					<?php echo form_radio($inputs['gender_f']); ?>
					Female
				</label>
			</div>
		</div>

		<div class="control-group">
			<div class="controls">
				<input type="submit" class="btn-primary" name="addPlayer" value="Add player" />
			</div>
		</div>
	</form>
</div>

<div class="form-right">
	<h2>Current Players</h2>

	<table class="table">
	<tr>
		<th>Name</th>
		<th>Age</th>
		<th>Gender</th>
		<th>&nbsp;</th>
	</tr>

	<?php if (! $userlist): ?>
		<tr><td colspan="4"><em>No users found...</em></td></tr>
	<?php endif; ?>

	<?php foreach ($userlist as $user): ?>

		<tr>
			<td><?php printf('%s, %s', $user->get('lastname'), $user->get('firstname')); ?></td>
			<td><?php echo $user->get('age'); ?></td>
			<td><?php echo $user->get('gender'); ?></td>
			<td><?php echo anchor('main/players/' . $user->get('id'), '&raquo; info', 'title="More"'); ?></td>
		</tr>

	<?php endforeach; ?>

	</table>
</div>

<div class="clearfix"></div>
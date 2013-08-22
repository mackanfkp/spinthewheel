<div class="container">
	<div id="wallet-bar">
		<ul class="inline pull-left">
			<li>
				Balance: 
				<span class="balance">
					&euro;<?php echo $realmoney->get('current_value') + ($bonus ? $bonus->get('current_value') : 0); ?>
				</span>
			</li>
			<li class="">|</li>
			<li>
				Real money wallet:
				<span class="rm_current_value">
					&euro;<?php echo $realmoney->get('current_value'); ?>
				</span>
			</li>
		</ul>
		<ul class="inline bonus pull-left">
			<?php if ($bonus): ?>
				<li class="">|</li>
				<li class="type">
					Bonus wallet:
					<span class="bm_current_value">
						&euro;<?php echo $bonus->get('current_value'); ?>
					</span>
				</li>
				<li class="">|</li>
				<li>
					Bet to clear:
					<span class="bm_wagered_value">
						&euro;<?php echo $bonus->get('wagered_value'); ?>
					</span>
				</li>
				<li class="">|</li>
				<li class="forfeit"><?php echo anchor('game/forfeit', 'Forfeit bonus', 'onclick="return confirm(\'Are you sure\');"'); ?>
			<?php endif; ?>
		</ul>
		<div class="clearfix"></div>
	</div>

	<br>

	<h2>Attach bonus</h2>

	<?php echo form_open('game/',  array('class' => 'form')) ?>

		<div class="errors">
			<?php echo validation_errors(); ?>
		</div>

		<div class="control-group">
			<?php echo form_label('Attach a login bonus', 'bonus_id'); ?>

			<div class="controls controls-row pull-left">
				<?php echo form_dropdown($inputs['login_bonus_id']['name'],
					$inputs['login_bonus_id']['options'],
					$inputs['login_bonus_id']['selected'],
					$inputs['login_bonus_id']['extra']); ?>
			</div>

			<div class="controls controls-row pull-left">
				&nbsp;&nbsp;<input type="submit" class="btn" name="addLoginBonus" value="Attach">
			</div>
			<div class="clearfix"></div>
		</div>
	</form>

	<div>OR</div>

	<?php echo form_open('game/',  array('class' => 'form')) ?>

		<div class="control-group">
			<?php echo form_label('Attach a deposit bonus', 'deposit_bonus_id'); ?>

			<div class="controls controls-row">
				<?php echo form_dropdown($inputs['deposit_bonus_id']['name'],
					$inputs['deposit_bonus_id']['options'],
					$inputs['deposit_bonus_id']['selected'],
					$inputs['deposit_bonus_id']['extra']); ?>
			</div>

			<div class="control-group pull-left">
				<?php echo form_label('Amount', 'deposit_amount'); ?>

				<div class="controls">
					<?php echo form_input($inputs['deposit_amount']); ?>
				</div>
			</div>

			<div class="controls controls-row pull-left">
				<label>&nbsp;</label>
				&nbsp;&nbsp;<input type="submit" class="btn" name="addDepositBonus" value="Attach">
			</div>
			<div class="clearfix"></div>
		</div>
	</form>
</div>

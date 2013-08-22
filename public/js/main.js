$('document').ready(function () {

	/**
	 * Bonus page
	 */
	$('#trigger').change(function () {
		if ('LOGIN' == $(this).val()) {
			$('#value_of_reward_type').val('EURO');
			$('#value_of_reward_type option[value="PERCENT"]').prop('disabled', true);
		} else {
			$('#value_of_reward_type option[value="PERCENT"]').prop('disabled', false);
		}
	});

	$('#reward_wallet_type').change(function () {
		if ('REALMONEY' == $(this).val()) {
			$('#multiplier').val(1);
		} else {
			//$('#multiplier').prop('readonly', false);
		}
	});

	$('#reward_wallet_type').trigger('change');
	$('#trigger').trigger('change');
	

	/**
	 * Spin Game
	 */
	$('a.clear').click(function () {
		$('#result p').fadeOut('fast').empty();
	});

	$('#spin').click(function (e) {
		var csrf = $('input[name="csrf_test_name"]').val();
		var amount = $('#bet').val();

		$.post(ci_base_url + 'game/spin/', {amount: amount, csrf_test_name: csrf}, function (data) {
			var rm = parseFloat(data.realmoney.current_value);
			var bm = data.bonus ? parseFloat(data.bonus.current_value) : 0;
			var wm = data.bonus ? parseFloat(data.bonus.wagered_value) : 0;
console.log(data);
			$('.balance').html('&euro;'+ (rm+bm));
			$('.rm_current_value').html('&euro;'+ rm);
			$('.bm_current_value').html('&euro;'+ bm);
			$('.bm_wagered_value').html('&euro;'+ wm);

			if (wm <= 0) {
				$('ul.bonus').hide();
				$('li.forfeit').remove();
			}

			// Set history
			var html = '';
			html += '<ul>';
			for (var i=0; i<data.msg.length; ++i) {
				html += '<li>['+ (new Date().toUTCString()) +']: '+ data.msg[i] +'</li>';
			}
			html += '</ul>';

			$('#result p').prepend(html).slideDown('fast');
		}, 'json');

		e.preventDefault();
	});
});
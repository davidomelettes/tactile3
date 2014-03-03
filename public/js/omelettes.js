// Handles browsers with no consoles or bad consoles
(function consoleStub() {
	if (!window.console) {
		window.console = {};
	}
	var console = window.console;
	var noop = function () {};
	var methods = ['assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 
	               'error', 'exception', 'group', 'groupCollapsed',
	               'groupEnd', 'info', 'log', 'markTimeline', 'profile',
	               'profileEnd', 'markTimeline', 'table', 'time',
	               'timeEnd', 'timeStamp', 'trace', 'warn'];
	for (var i = 0; i < methods.length; i++) {
		if (!console[methods[i]]) {
			console[methods[i]] = noop;
		}
	}
}());

var Om = Om || {};

$(function () {
	$(document).ready(function () {
		console.group('Page head');
		
		// Tables
		$('table.table').each(function() {
			var $t = $(this);
			$t.find('thead th.cb input').click(function() {
				$t.find('tbody td.cb input').prop('checked', $(this).prop('checked'));
			});
			$t.find('.cb').click(function() {
				$(this).find('input').click();
			}).find('input').click(function(ev) {
				ev.stopPropagation();
			});
		});
		
		// Autocomplete inputs
		$('.autocomplete').each(function() {
			var $key = $($(this).data('target'));
			$(this).parents('.form-group').find('label').attr('for', $(this).attr('id'));
			$(this).autocomplete({
				select: function(event, ui) {
					console.log('select');
					var $ac = $(event.target);
					var item = ui.item;
					if (item.value) {
						var $s = $('<p>').addClass('form-control-static bound').text(item.label+' ').append($('<span>').addClass('glyphicon glyphicon-remove text-danger')).click(function(){
							$ac.val('').parent().show();
							$key.val('');
							$(this).remove();
						});
						$ac.parent().hide().before($s);
						$key.val(item.value);
					} else {
						$ac.val('');
						$key.val('');
					}
					return false;
				}
			}).click(function () {
				if ($(this).is('.bound')) {
					$(this).val('').removeClass('bound').next().val('');
				}
			});
			if ($key.val() != '') {
				$(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', {item:{value:$key.val(),label:$(this).val()}});
			}
		});
		
		console.groupEnd();
	});
});

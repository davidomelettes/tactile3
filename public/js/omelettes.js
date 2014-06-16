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

//Useful for adjusting absolutely-positioned elements
function _omCheckOffset($container, offset, $relative) {
	var containerWidth = $container.outerWidth();
	var containerHeight = $container.outerHeight();
	var scrollLeft = $(document).scrollLeft(); // offset of viewport left edge
	var scrollTop = $(document).scrollTop(); // offset of viewport top edge
	var viewWidth = document.documentElement.clientWidth + scrollLeft; // offset of viewport right edge
	var viewHeight = document.documentElement.clientHeight + scrollTop; // offset of viewport bottom edge
	var relativeLeft = $relative ? $relative.offset().left : 0;
	var relativeTop = $relative ? $relative.offset().top : 0;
	var relativeWidth = $relative ? $relative.outerWidth() : 0;
	var relativeHeight = $relative ? $relative.outerHeight() : 0;

	var rightOfRelative = (offset.left >= relativeLeft);
	var belowRelative = (offset.top >= relativeTop);
	
	var deltaLeft = 0;
	// Check there's any point to adjusting x
	if (viewWidth > containerWidth) {
		// Ensure container left-edge is inside viewport
		if (offset.left < scrollLeft) {
			// Move it right
			deltaLeft = scrollLeft - offset.left;
		}
		if (!deltaLeft) {
			// Ensure container right-edge is inside viewport
			if (offset.left + containerWidth > viewWidth) {
				// Move it left
				deltaLeft = -(offset.left + containerWidth - viewWidth);
			}
		}
	}
	
	var deltaTop = 0;
	// Check there's any point to adjusting y
	if (viewHeight > containerHeight) {
		// Ensure container top-edge is inside viewport
		if (offset.top < scrollTop) {
			// Move it down
			if (!belowRelative) {
				// Position it beneath relative instead
				// TODO: Check bottom edge will still be on screen first!
				deltaTop = relativeTop + relativeHeight - offset.top;
			} else {
				deltaTop = scrollTop - offset.top;
			}
		}
		if (!deltaTop) {
			// Ensure container bottom-edge is inside viewport
			if (offset.top + containerHeight > viewHeight) {
				// Move it up
				if (belowRelative) {
					// Position above
					// TODO: Check top edge will still be on screen first!
					deltaTop = relativeTop - (offset.top + containerHeight);
				}
				deltaTop = offset.top + containerHeight - viewHeight;
			}
		}
	}
	
	offset.left += deltaLeft;
	offset.top += deltaTop;
	
	return offset;
};

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
		
		// Forms
		$('.btn').button();
		$('form').each(function () {
			var $form = $(this);
			
			// Date inputs
			$form.find('input.datepick').datepicker();
			
			// Time inputs
			$form.find('input.timepick').timepicker();
			
			// Autocomplete inputs
			$('input.autocomplete').each(function() {
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
			
			// Submission
			$form.find('.submit .btn').click(function () {
				var canSubmit = true;
				$(this).parents('form').find('input[required], select[required]').each(function () {
					if ($(this).val() === '') {
						canSubmit = false;
					}
				});
				if (canSubmit) {
					$(this).button('loading');
				}
			});
		});
		
		console.groupEnd();
	});
});

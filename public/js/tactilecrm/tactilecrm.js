$(function () {
	$(document).ready(function () {
		// Resource form
		$('#form-resourceSingularName').bind('keyup', function () {
			var s = $(this).val();
			var p = s === '' ? '' : (s.replace(/([^aeiou])y$/, '$1ie') + 's');
			$('#form-resourcePluralName').val(p);
			$('#form-resourceName').val(p.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/, ''));
		});
	});
});

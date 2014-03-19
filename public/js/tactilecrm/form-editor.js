$(function () {
	$(document).ready(function () {
		var addField = function (form, fieldType) {
			var $li = $('<li>');
			var $group = $('<div class="form-group">').appendTo($li);
			var $label = $('<label class="control-label col-md-3">').text('Field Name').appendTo($group);
			
			switch (fieldType) {
				default:
					var $input = $('<input type="text" class="form-control">');
			}
			$group.append($('<div class="col-md-5">').append($input));
			
			var btnPrototype = '<button class="btn btn-default">';
			var glyphProtype = '<span class="glyphicon">';
			var $buttons = $('<div class="btn-group">');
			$buttons.append($(btnPrototype).append($(glyphProtype).addClass('glyphicon-chevron-up')));
			$buttons.append($(btnPrototype).append($(glyphProtype).addClass('glyphicon-chevron-down')));
			$buttons.append($(btnPrototype).append($(glyphProtype).addClass('glyphicon-edit')));
			$buttons.append($(btnPrototype).append($(glyphProtype).addClass('glyphicon-remove')));
			$group.append($('<div class="col-md-4 text-right">').append($buttons));
			
			$(form).find('ul.list-unstyled').append($li);
		};
		
		var removeField = function () {
			
		};
		
		$('.form-editor-add button').click(function (e) {
			e.preventDefault();
			addField('.form-editor-form');
		});
		
	});
});
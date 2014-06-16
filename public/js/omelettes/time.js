
var tpuuid = new Date().getTime();

function Timepicker() {
	this._currentInstance = null;
	this._lastInput = null;
	this._divId = 'ui-timepicker-div';
	this._markerClass = 'hasTimepicker';
	this._timepickerShowing = false;
	this.tpDiv = $('<div id="'+this._divId+'" class="abs timepicker"/>');
}
$.extend(Timepicker.prototype, {
	// Create a new picker instance for the input
	_newInstance: function(target, options) {
		var id = target[0].id;
		return {id: id, input: target, tpDiv: this.tpDiv, opts: options};
	},
	// Fetch an existing picker instance
	_getInstance: function(target) {
		try {
			return $.data(target, 'timepicker');
		} catch (err) {
			throw 'Missing instance data for this timepicker';
		}
	},
	// Return a Date object using the specified time of day
	_dateForTime: function(hours, minutes) {
		var today = new Date();
		return new Date(today.getFullYear(), today.getMonth(), today.getDay(), hours, minutes);
	},
	// Parse the time 
	_setTimeFromField: function(instance) {
		fieldValue = instance.input.val();
		var time = $.timepicker._parseTime(instance.opts.format, fieldValue);
		if (!time) {
			// User current time as default
			time = new Date();
		}
		instance.currentHour = time.getHours();
		instance.currentMinute = time.getMinutes();
		this._adjustInstanceTime(instance);
	},
	// Set the current time used by the instance 
	_adjustInstanceTime: function(instance) {
		var h = instance.currentHour;
		var m = instance.currentMinute;
		// Round time backwards to previous half-hour
		m = m < 30 ? 0 : 30;
		var time = this._dateForTime(h, m);
		instance.selectedTime = time;
	},
	// Calculate position based on picker's input
	_findPosition: function(obj) {
        var position = $(obj).offset();
	    return {left:position.left, top:position.top};
	},
	// Calculate if the picker is offscreen and adjust accordingly
	_checkOffset: function(instance, offset) {
		offset = _omCheckOffset(instance.tpDiv, offset, instance.input);
		return offset;
	},
	// Display the picker
	_showTimepicker: function(input) {
		input = input.target || input;
		var instance = $.timepicker._getInstance(input);
		if ($.timepicker._currentInstance && $.timepicker._currentInstance != instance) {
			// Another instance is still open (probably in the process of closing)
			// Kill it now
			$.timepicker._currentInstance.tpDiv.stop(true, true);
		}
		
		// Set the current time from the input
		$.timepicker._lastInput = input;
		$.timepicker._setTimeFromField(instance);
		
		// Position the picker
		var offset = $.timepicker._findPosition(input);
		offset.top += $(input).outerHeight();
		instance.tpDiv.css('top','-1000px');
		$.timepicker._updateTimepicker(instance);
		offset = $.timepicker._checkOffset(instance, offset);
		instance.tpDiv.css({display:'none', top: offset.top+'px', left:offset.left+'px'});
		
		// Actually show it
		instance.tpDiv.fadeIn('fast');
		$.timepicker._timepickerShowing = true;
		$.timepicker._currentInstance = instance;
		
		// Scroll to selected option, if any
		instance.tpDiv.scrollTop(0);
		if (instance.tpDiv.find('.selected').length) {
			var $selected = instance.tpDiv.find('.selected');
			instance.tpDiv.scrollTop(($selected.position().top - (instance.tpDiv.height()/2))+$selected.height());
		}
	},
	// Hide the picker
	_hideTimepicker: function(input) {
		var instance = this._currentInstance;
		if (!instance || (input && instance != $.data(input, 'timepicker'))) {
			return;
		}
		var tidyUp = function() {
			this._currentInstance = null;
			instance.tpDiv.empty();
			$.timepicker._timepickerShowing = false;
		};
		if (this._timepickerShowing) {
			instance.tpDiv.fadeOut('fast', tidyUp);
		}
	},
	// Handle time picking
	_selectTime: function(id, hours, minutes) {
		var target = $(id);
		var instance = this._getInstance(target[0]);
		timeString = this._formatTime(instance, hours, minutes);
		if (instance.input) {
			instance.input.val(timeString);
			this._hideTimepicker();
			this._lastInput = null;
		}
	},
	// Update the picker's state 
	_updateTimepicker: function(instance) {
		instance.tpDiv.empty().append(this._generateHTML(instance));
	},
	// Build the contents of the picker
	_generateHTML: function(instance) {
		var html = '';
		var h,m = 0;
		for (h=0;h<24;h++) {
			for (m=0;m<60;m+=30) {
				var printTime = this._dateForTime(h, m);
				var selected = (instance.selectedTime && printTime.getTime() == instance.selectedTime.getTime());
				html += '<div class="'+(selected?'selected':'')+'" id="TP_jQuery_t'+h+m+'" onclick="TP_jQuery_'+tpuuid+'.timepicker._selectTime(\'#'+instance.id+'\','+printTime.getHours()+','+printTime.getMinutes()+');return false;">'+this.formatTime(instance.opts.format, printTime)+'</div>';
			}
		}
		return html;
	},
	// Add a new picker to an input
	_attachTimepicker: function(target, opts) {
		var instance = this._newInstance($(target), opts);
		this._connectTimepicker(target, instance);
	},
	// Associate a picker with an input
	_connectTimepicker: function(target, instance) {
		var $input = $(target);
		if ($input.hasClass(this._markerClass)) {
			return;
		}
		this._attachments($input, instance);
		$input.addClass(this._markerClass)
			.keydown(this._keyDown).keypress(this._keyPress).keyup(this._keyUp);
		$.data(target, 'timepicker', instance);
	},
	// Set listeners for input
	_attachments: function($input, instance) {
		$input.unbind('focus', this._showTimepicker);
		$input.bind('focus', this._showTimepicker);
	},
	// Monitors for clicks outside the picker
	_checkExternalClick: function(ev) {
		if (!$.timepicker._currentInstance) {
			return;
		}
		var $target = $(ev.target);
		if ($target[0].id != $.timepicker._divId) {
			$.timepicker._hideTimepicker();
		}
	},
	// Handle a keyDown event on the input
	_keyDown: function(ev) {
		
	},
	// Handle a keyPress event on the input
	_keyPress: function(ev) {
		
	},
	// Handle a keyUp event on the input
	_keyUp: function(ev) {
		
	},
	// Calculate time from string
	_parseTime: function(format, value) {
		var matches = value.match(/(\d{0,2}):?(\d{0,2})(am|pm)?/i);
		if (matches[0]) {
			var h = parseInt(matches[1],10);
			var m = parseInt(matches[2],10);
			var p = matches[3] ? matches[3].toLowerCase():'';
			if (h < 13 && p == 'pm') {
				h += 12;
			}
			return this._dateForTime(h, m);
		} else {
			return null;
		}
	},
	// Format time for display
	_formatTime: function(instance, hours, minutes) {
		var time = typeof hours == 'object' ? hours : this._dateForTime(parseInt(hours,10), parseInt(minutes,10));
		return this.formatTime(instance.opts.format, time);
	},
	// Format a time string according to time formatting settings
	formatTime: function(format, time) {
		if (!time) {
			return '';
		}
		
		var output = '';
		var h = time.getHours();
		var m = time.getMinutes();
		var hh = (h < 10 && format === 'H:i') ? ('0'+h) : h;
		var mm = m < 10 ? ('0'+m) : m;
		var a = 'am';
		if (format !== 'H:i' && parseInt(hh) > 12) {
			a = 'pm';
			hh = parseInt(hh) - 12;
		} else if (format !== 'H:i' && hh === 0) {
			hh = '12';
		}
		output += hh;
		output += ':';
		output += mm;
		if (format !== 'H:i') {
			output += a;
		}
		
		return output;
	}
});

$.fn.timepicker = function(options) {
	if (!this.length) {
		return this;
	}
	
	if (!$.timepicker.initialized) {
		// Monitor all clicks
		$(document).mousedown($.timepicker._checkExternalClick)
			.find('body').append($.timepicker.tpDiv);
		$.timepicker.initialized = true;
	}
	
	var opts = $.extend({}, $.fn.timepicker.defaults, options);
	
	return this.each(function(){
		$.timepicker._attachTimepicker(this, opts);
	});
};
$.fn.timepicker.defaults = {
	format: 'H:i'
};

$.timepicker = new Timepicker(); // Establish as singleton
$.timepicker.initialized = false;

// Provides a reference to the jQuery object for inline javascript
// Don't really understand why we do things this way
window['TP_jQuery_' + tpuuid] = $;

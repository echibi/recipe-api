/**
 * Created by echibi on 22/01/17.
 */

// TODO:: Make this more modular so that we can use it on more stuff
var repeatable = {
	fieldAdd   : null,
	fieldRemove: null,
	init       : function () {
		repeatable.fieldAdd = $('.repeater-add');
		repeatable.fieldRemove = $('.repeater-remove');

		repeatable.fieldAdd.click(function () {
			repeatable.method.add($(this));

			return false;
		});

		repeatable.fieldRemove.click(function () {
			repeatable.method.remove($(this));

			return false;
		});
	},
	method     : {
		add   : function ($this) {
			var theField = $this.closest('.repeatable-wrap').find('.repeatable.list-group-item:last').clone(true);
			var theLocation = $this.closest('.repeatable-wrap').find('.repeatable.list-group-item:last');
			$('input, select, textarea', theField).attr('name', function (index, name) {
				return name.replace(/(\d+)/, function (fullMatch, n) {
					return Number(n) + 1;
				});
			});
			$('input, textarea', theField).val('');

			theField.insertAfter(theLocation, $(this).closest('div.repeatable-wrap'));

		},
		remove: function ($this) {
			$this.parent().remove();
		},

	}
};

$(function () {

	tinyMCE.init({
			selector: '.mce-tinymce',
			plugins : ['code', 'link'],
			themes  : 'inlite'
		}
	);

	// Init our repeatable fields
	repeatable.init();

	// Remove empty ingredients from edit-recipe form
	$('#edit-recipe').on('submit', function (e) {
		var $ingredients = $('.ingredients-list .repeatable.list-group-item');

		$.each($ingredients, function (i, obj) {
			var $valueInput = $('input.value', obj),
				$nameInput = $('input.name', obj),
				nameVal = $nameInput.val(),
				valueVal = $valueInput.val();

			if ((undefined === nameVal || '' === nameVal) && (undefined === valueVal || '' === valueVal)) {
				$valueInput.attr('name', null);
				$nameInput.attr('name', null);
				$('select.unit', obj).attr('name', null);
			}
		});
	});


	$('.delete-recipe').on('click', function (e) {
		e.preventDefault();

		if (window.confirm("Are you sure?")) {
			$this = $(this);

			var url = $this.data('url');

			$.ajax({
				url    : url,
				type   : 'DELETE',
				success: function (result) {
					// Do something with the result
					$this.parent().remove();
				}
			});
		}

	});

	// Get all "navbar-burger" elements
	var $navbar_burgers = $('.navbar-burger');
	if (0 < $navbar_burgers.length) {
		$navbar_burgers.on('click', function (event) {
			// Get the target from the "data-target" attribute
			var $el = $(this);
			var target = $el.data('target');
			var $target = $('#' + target);

			console.log('target:',target);
			// Toggle the class on both the "navbar-burger" and the "navbar-menu"
			$el.toggleClass('is-active');
			$target.toggleClass('is-active');
		});
	}
});
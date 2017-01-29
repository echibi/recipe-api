/**
 * Created by echibi on 22/01/17.
 */

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
			$('input, select, textarea', theField).val('').attr('name', function (index, name) {
				return name.replace(/(\d+)/, function (fullMatch, n) {
					return Number(n) + 1;
				});
			});
			theField.insertAfter(theLocation, $(this).closest('div.repeatable-wrap'));

			console.log(theField);

		},
		remove: function ($this) {
			$this.parent().remove();
		},

	}
};

$(function () {

	tinyMCE.init({
			'selector': '.mce-tinymce'
		}
	);

	repeatable.init();

	//All options are optional.
	/*
	 new Repeater($('.repeatable'), {
	 addSelector: '.repeater-add', //The css selector for the add button.
	 addSelectorOut: false, // Set to true if the add selector is outside the repeater wrap.
	 removeSelector: '.repeater-remove', //The css selector for the remove button.
	 withDataAndEvents: false, //Should data and events on repeatable sections be cloned?
	 deepWithDataAndEvents: false, //Should data and events of repeatable sections descendants be cloned?
	 addCallback: function(){
	 // console.log("Am I repeating myself?");
	 console.log(this);
	 return false;
	 }, //A callback function that generated repeatable sections will be passed into.
	 wrapperHtml: "<div class='repeater-wrap'></div>" //HTML for an element to wrap all repeatable sections in.
	 });
	 */

	$('.delete-recipe').on('click', function (e) {
		e.preventDefault();
		$this = $(this);

		var recipeId = $this.data('id');

		$.ajax({
			url    : '/recipes/' + recipeId,
			type   : 'DELETE',
			success: function (result) {
				// Do something with the result
				console.log(result);
			}
		});
	});

	$('.update-recipe').on('click', function (e) {
		e.preventDefault();
		$this = $(this);

		var recipeId = $this.data('id'),
			updateTitle = $this.data('title');

		$.ajax({
			url    : '/recipes/' + recipeId,
			type   : 'PUT',
			data   : {
				'title': updateTitle
			},
			success: function (result) {
				// Do something with the result
				console.log(result);
			}
		});
	});
});
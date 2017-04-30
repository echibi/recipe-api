/**
 * Created by echibi on 12/03/17.
 */
var search = {
	inputElement    : null,
	resultElement   : null,
	inputSearchDelay: 500, // Delay before search triggers after keyup in ms
	inputMinLength  : 3, // Min length before a search activates
	init            : function (selector) {
		search.inputElement = selector;
		search.method.attachEventListeners();
	},
	method          : {
		search              : function () {
			var searchString = search.inputElement.val(),
				destinationUrl = search.inputElement.data('url');
			console.log('search start', searchString);
			$.ajax(destinationUrl, {
				data    : {
					q: searchString
				},
				method  : 'POST',
				dataType: 'html'
			}).done(function (response) {
				var $resultWrap = $('.search-results-wrap');
				$resultWrap.replaceWith(response);
			});
		},
		attachEventListeners: function () {
			search.inputElement.on('keyup', global.debounce(function (e) {
				// console.log(e);
				if (search.inputElement.val().length >= search.inputMinLength) {
					search.method.search();
				}

			}, search.inputSearchDelay));
		}
	}
};

/**
 * Created by echibi on 12/03/17.
 */
var search = {
	inputElement : null,
	resultElement: null,
	init         : function (selector) {
		search.inputElement = selector;

		search.method.attachEventListeners();
	},
	method       : {
		search              : function () {
			console.log('search start');
		},
		attachEventListeners: function () {
			search.inputElement.on('keyup', function (e) {
				// console.log(e);
			});
		}
	}
};

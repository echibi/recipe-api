/**
 * Created by echibi on 22/01/17.
 */

$(function () {
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
<? if (count(LATER_DATALIST_OPTIONS)): ?>
	<datalist id="sdl">
		<? foreach (LATER_DATALIST_OPTIONS as $opt): ?>
			<option value="<?= html($opt) ?>">
		<? endforeach ?>
	</datalist>
<? endif ?>

<form class="inline-filter">
	<input
		name="url_filter"
		placeholder="Filter URL..."
		value="<?= html(@$_GET['url_filter']) ?>"
		class="search"
		autocomplete="off"
		<? if (count(LATER_DATALIST_OPTIONS)): ?>
			list="sdl"
		<? endif ?>
	/>
	<input type="submit" class="submit" />
</form>

<script>
(function(el) {
	// Datalist selection
	var timer;
	el.addEventListener('change', function(e) {
		timer = setTimeout(function() {
			el.form.submit();
		}, 1);
	});
	el.addEventListener('blur', function(e) {
		clearTimeout(timer);
	});

	// Filter size/display
	el.addEventListener('focus', function(e) {
		setTimeout(function() {
			el.classList.add('focus');
			el.select();
		}, 50);
	});
	el.addEventListener('blur', function(e) {
		el.classList.remove('focus');
	});
})(document.querySelector('input[name="url_filter"]'));
</script>

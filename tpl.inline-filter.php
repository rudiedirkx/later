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
		onfocus="setTimeout(
			function(el) {
				el.classList.add('focus');
				el.select();
			},
			'ontouchstart' in document ? 200 : 0,
			this
		)"
		onblur="this.classList.remove('focus')"
		<? if (count(LATER_DATALIST_OPTIONS)): ?>
			list="sdl"
		<? endif ?>
	/>
	<input type="submit" class="submit" />
</form>

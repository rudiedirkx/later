<form class="inline-filter">
	<input name="url_filter" placeholder="Filter URL..." value="<?= html(@$_GET['url_filter']) ?>" class="search" autocomplete="off" onfocus="setTimeout(function(el) { el.classList.add('focus'); el.select(); }, 200, this)" onblur="this.classList.remove('focus')" />
	<input type="submit" class="submit" />
</form>

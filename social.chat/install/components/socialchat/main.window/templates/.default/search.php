<?php


?>
<div>
	<form action="<?php echo POST_FORM_ACTION_URI?>" id="searchForm" method="GET">
		<input type="text" name="key" value="" id="searchKey">
		<input type="hidden" name="action" value="search">
		<span id="clear_search_result">X</span>
	</form>
	<div id="searchResult">
		
	</div>
</div>
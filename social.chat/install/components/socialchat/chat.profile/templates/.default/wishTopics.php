<?php

?>

<div class="row">
	<div class="col-xs-12">
		<h3><?php echo GetMessage("ALI_PROFILE_WISH_TOPICS");?></h3>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<div class="topic_search">
			<form action="<?php echo POST_FORM_ACTION_URI?>" id="searchTopicForm" method="GET">
				<input type="text" name="key" value="" id="searchKey" placeholder="<?php echo GetMessage("ALI_PROFILE_SEARCH");?>" class="search_topic">
				<input type="hidden" name="action" value="searchtopic" >
				<span id="clear_search_result">X</span>
			</form>
			<div id="searchResult">
				
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<div id="myWishTopics">
			<?php $this->getComponent()->includeComponentTemplate("wishTopicsList");?>
		</div>
	</div>
</div>
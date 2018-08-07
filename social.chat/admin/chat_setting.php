<?php

require("top.php");
use \Social\Chat\TopicTable;


$APPLICATION->SetTitle(GetMessage("ADMIN_ALI_CHAT_SETTING_TITLE"));

$filter = isset($_GET['Topic']) && count($_GET['Topic']) ? $_GET['Topic'] : null;
if($filter){
	$topics = TopicTable::getThemes($filter);
}else{
	$topics = TopicTable::getThemes();
}
?>
<h2>Topics</h2>
<a href="/bitrix/admin/social.chat_topic_form.php">Add Topic</a>

<form method="GET">
	<div>
		<ul>
			<li>
				<label>Title</label>
				<input type="text" name="Topic[TITLE]" value="<?php echo $filter && isset($filter['TITLE']) ? $filter['TITLE'] : ""; ?>">
			</li>
			<li>
				<label>Sort</label>
				<?php

					$a_s = $filter && isset($filter['ORDER']) && $filter['ORDER'] == "ASC" ? "selected" : "";
					$a_d = $filter && isset($filter['ORDER']) && $filter['ORDER'] == "DESC" ? "selected" : "";
				?>
				<select name="Topic[ORDER]">
					<option value="ASC" <?php echo $a_s;?>>Up</option>
					<option value="DESC" <?php echo $a_d;?>>Down</option>
				</select>
			</li>
			<li>
				<input type="submit" value="Filter">
			</li>
		</ul>
	</div>
</form>

<table class="table" border="1px" style="width: 100%;border:1px solid #ccc;">
	<tr>
		<th>#</th>
		<th>Title</th>
		<th>Owner</th>
		<th>Sort</th>
		<th>actions</th>
	</tr>

<?php 
	if(is_array($topics)){
		foreach ($topics as $key => $topic) {

		?>
		<tr>
			<td><?php echo ++$key;?></td>
			<td><?php echo $topic['TITLE']?></td>
			<td><?php echo $topic['OWNER_ID'] ? $topic['MEMBER_USER_NAME'] : "Administrator"?></td>
			<td><?php echo $topic['ORDER'] ? $topic['ORDER'] : 0?></td>
			<td>
				<a href="/bitrix/admin/social.chat_topic_form.php?id=<?php echo $topic['ID']?>">Update</a>
				<a href="/bitrix/admin/social.chat_topic_delete.php?id=<?php echo $topic['ID']?>">Delete</a>
			</td>
		</tr>
		<?php
		}
	}
?>
</table>
<?
require("bottom.php");
?>
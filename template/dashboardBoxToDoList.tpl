{hascontent}
	<ul class="sidebarBoxList">
		{content}
			{foreach from=$toDoList item=item}
				<li class="box24">
					<div class="sidebarBoxHeadline">
						<h3>
							<a href="{link controller='ToDo' id=$item.id}{/link}">{$item.title}</a>
						</h3>
						<small>
							<a href="{link controller='User' id=$item.submitter}{/link}" class="userLink" data-user-id="{$item.submitter}" title="{$item.username}">{$item.username}</a>
							 - 
							{@$item.timestamp|time}
						</small>
					</div>
				</li>
			{/foreach}
		{/content}
	</ul>
{/hascontent}
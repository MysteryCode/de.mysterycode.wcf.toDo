{hascontent}
	<ul class="sidebarBoxList">
		{content}
			{foreach from=$todoList item=item}
				<li class="box24">
					<a href="{link controller='ToDo' object=$item}{/link}" class="framed jsTooltip" title="{lang}wcf.toDo.goToTodo{/lang}">
						<span class="framed icon icon-tasks"></span>
					</a>
					<div class="sidebarBoxHeadline">
						<h3>
							<a href="{link controller='ToDo' id=$item->todoID}{/link}">{$item->title}</a>
						</h3>
						<small>
							<a href="{link controller='User' id=$item->submitter}{/link}" class="userLink" data-user-id="{$item->submitter}" title="{$item->username}">{$item->username}</a>
							 - 
							{@$item->timestamp|time}
						</small>
					</div>
				</li>
			{/foreach}
		{/content}
	</ul>
{/hascontent}
{hascontent}
	<ul class="sidebarBoxList">
		{content}
			{foreach from=$latestTodosList item=todo}
				<li class="box24">
					<a href="{link controller='ToDo' object=$todo}{/link}" class="framed">{@$todo->getUser()->getAvatar()->getImageTag(24)}</a>
					
					<div class="sidebarBoxHeadline">
						<h3><a href="{link controller='ToDo' object=$todo}{/link}" data-todo-id="{@$todo->todoID}" data-sort-order="DESC" title="{$todo->getTitle()}">{$todo->getTitle()}</a></h3>
						<small>{if $todo->submitter}<a href="{link controller='User' object=$todo->getUser()}{/link}" class="userLink" data-user-id="{@$todo->getUser()->userID}">{$todo->username}</a>{else}{$todo->username}{/if} - {@$todo->time|time}</small>
					</div>
				</li>
			{/foreach}
		{/content}
	</ul>
{/hascontent}

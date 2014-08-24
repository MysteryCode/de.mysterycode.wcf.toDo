{if $unknownTodo|isset}
	<p>{lang}wcf.todo.task.unknownTodo{/lang}</p>
{else}
	<div class="box128 todoPreview">
		<a href="{link controller='ToDo' object=$todo}{/link}" title="{$todo->title}">{$todo->title}</a>
		
		<div class="todoInformation">
			{* {include file='userInformation'} *}
		</div>
	</div>
{/if}

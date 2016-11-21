<div class="marginTop tabularBox tabularBoxTitle messageGroupList">
	<header>
		<h2>{lang}wcf.toDo.taskList{/lang}</h2>
	</header>
	
	{if $items}
		{include file='todoListContainer' todoList=$objects todoListItems=$items}
	{/if}
</div>

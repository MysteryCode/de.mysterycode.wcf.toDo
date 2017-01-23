<section class="section tabularBox tabularBoxTitle messageGroupList">
	<h2 class="sectionTitle">{lang}wcf.toDo.taskList{/lang}</h2>
	
	{if $items}
		{include file='todoListContainer' todoList=$objects todoListItems=$items}
	{/if}
</section>

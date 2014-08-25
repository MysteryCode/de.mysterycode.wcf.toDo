{capture assign='sidebar'}
	{* project menu *}
	<fieldset>
		<legend>{lang}wcf.toDo.taskList{/lang}</legend>
		
		<nav>
			<ul>
				<li{if $templateName == 'toDoList'} class="active"{/if}><a href="{link controller='ToDoList'}{/link}">{lang}wcf.header.menu.toDo.active{/lang}</a></li>
				<li{if $templateName == 'toDoArchive'} class="active"{/if}><a href="{link controller='ToDoArchive'}{/link}">{lang}wcf.header.menu.toDo.archive{/lang}</a></li>
				<li{if $templateName == 'toDoTrash'} class="active"{/if}><a href="{link controller='ToDoTrash'}{/link}">{lang}wcf.header.menu.toDo.trash{/lang}</a></li>

				{event name='sidebarToDoMenu'}
			</ul>
		</nav>
	</fieldset>
	
	{@$__boxSidebar}

	{event name='sidebarBoxes'}
{/capture}
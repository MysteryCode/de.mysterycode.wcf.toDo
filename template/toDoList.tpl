{include file='documentHeader'}

<head>
	<title>{lang}wcf.toDo.taskList{/lang} - {PAGE_TITLE|language}</title>
	
	{include file='headInclude'}
	
	<link rel="canonical" href="{link controller='ToDo'}{/link}" />
</head>

<body id="tpl{$templateName|ucfirst}">

{include file='header'}

<header class="boxHeadline">
	<h1>{lang}wcf.toDo.taskList{/lang}</h1>
</header>

{include file='userNotice'}

<div class="contentNavigation">
	{hascontent}
		<nav>
			<ul>
				{content}
					{if $__wcf->getSession()->getPermission('user.toDo.toDo.canAdd')}<li><a href="{link controller='ToDoAdd'}{/link}" title="{lang}wcf.toDo.task.add{/lang}" class="button"><span class="icon icon16 icon-asterisk"></span> <span>{lang}wcf.toDo.task.add{/lang}</span></a></li>{/if}
					{event name='contentNavigationButtonsTop'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</div>

<div class="tabularBox tabularBoxTitle marginTop toDoContainer">
	<header>
		<h2>{lang}wcf.toDo.taskList.tasks{/lang} <span class="badge badgeInverse">{$entryCount}</span></h2>
	</header>
	
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" class="columnID">ID</th>
				{if TODO_CATEGORY_ENABLE}
					<th class="tiny">{lang}wcf.toDo.category{/lang}</th>
				{/if}
				<th colspan="2" class="columnTitle">{lang}wcf.toDo.task.title{/lang}</th>
				{if $__wcf->getSession()->getPermission('user.toDo.responsible.canView')}
					<th class="columnText">{lang}wcf.toDo.task.responsible{/lang}</th>
				{/if}
				<th class="columnDate">{lang}wcf.toDo.task.submitTime{/lang}</th>
				<th class="columnDate">{lang}wcf.toDo.task.endTime{/lang}</th>
			</tr>
		</thead>
		
		<tbody>
			{foreach from=$tasks item=task}
				<tr>
					<td class="columnIcon">
						{if $__wcf->getSession()->getPermission('user.toDo.toDo.canEdit')}<a href="{link controller='ToDoEdit' id=$task.id}{/link}" title="{lang}wcf.toDo.task.edit{/lang}"><span class="icon icon16 icon-pencil pointer" title="{lang}wcf.toDo.task.edit{/lang}"></span></a>{else}<span class="icon icon16 icon-pencil disabled"></span>{/if}
						{if $__wcf->getSession()->getPermission('user.toDo.toDo.canDelete')}<a href="{link controller='ToDoDelete' id=$task.id}{/link}" title="{lang}wcf.toDo.task.delete{/lang}"><span class="icon icon16 icon-remove jsDeleteButton pointer" title="{lang}wcf.toDo.task.delete{/lang}"></span></a>{else}<span class="icon icon16 icon-remove disabled"></span>{/if}</td>
					<td class="columnID">
						{$task.id}</td>
					{if TODO_CATEGORY_ENABLE}
						<td class="tiny">
							<span class="label badge {$task.categorycolor}">{$task.categoryname}</span></td>
					{/if}
					<td class="columnText columnToDoStatus">
						{if $task.status == 1}
							<span class="label badge red">{lang}wcf.toDo.task.unsolved{/lang}</span>
						{elseif $task.status == 2}
							<span class="label badge yellow">{lang}wcf.toDo.task.work{/lang}</span>
						{elseif $task.status == 3}
							<span class="label badge green">{lang}wcf.toDo.task.solved{/lang}</span>
						{elseif $task.status == 4}
							<span class="label badge gray">{lang}wcf.toDo.task.canceled{/lang}</span>
						{else}
							<span class="label badge red">{lang}wcf.toDo.task.error{/lang}</span>
						{/if}</td>
					<td class="columnTitle columnToDoTitle {if $task.important == 1}importantToDo{/if}">
						{if $__wcf->getSession()->getPermission('user.toDo.toDo.canViewDetail')}<a href="{link controller='ToDo' id=$task.id}{/link}">{$task.title}</a>{else}{$task.title}{/if}</td>
					{if $__wcf->getSession()->getPermission('user.toDo.responsible.canView')}
						<td class="columnText columnToDoResponsible">
							<ul>
								{foreach from=$task.responsible item=responsible}
									<li><a href="{link controller='User' id=$responsible.id}{/link}" class="userLink" data-user-id="{$responsible.id}">{$responsible.username}</a></li>
								{/foreach}
							</ul>
						</td>
					{/if}
					<td class="columnDate">
						{@$task.submitTime|time}</td>
					<td class="columnDate">
						{if $task.endTime>0}{@$task.endTime|time}{/if}</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
</div>

<div class="contentNavigation">
	{hascontent}
		<nav>
			<ul>
				{content}
					{if $__wcf->getSession()->getPermission('user.toDo.toDo.canAdd')}<li><a href="{link controller='ToDoAdd'}{/link}" title="{lang}wcf.toDo.task.add{/lang}" class="button"><span class="icon icon16 icon-asterisk"></span> <span>{lang}wcf.toDo.task.add{/lang}</span></a></li>{/if}
					{event name='contentNavigationButtonsBottom'}
				{/content}
				
			</ul>
		</nav>
	{/hascontent}
</div>

{include file='footer'}

</body>
</html>
{include file='documentHeader'}

<head>
	<title>{lang}wcf.toDo.taskList{/lang} - {PAGE_TITLE|language}</title>
	
	{include file='headInclude'}
	
	{capture assign='canonicalURLParameters'}sortField={@$sortField}&sortOrder={@$sortOrder}{/capture}
	
	<link rel="canonical" href="{link controller='ToDoList'}{/link}" />
	
	<script data-relocate="true" src="{@$__wcf->getPath()}js/WCF.Moderation{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@$__wcfVersion}"></script>
	<script data-relocate="true" src="{@$__wcf->getPath()}js/WCF.Todo{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@$__wcfVersion}"></script>

	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			WCF.Language.addObject({
				'wcf.todo.confirmDelete': '{lang}wcf.toDo.confirmDelete{/lang}',
				'wcf.todo.confirmTrash': '{lang}wcf.toDo.confirmTrash{/lang}',
				'wcf.todo.confirmTrash.reason': '{lang}wcf.toDo.confirmTrash.reason{/lang}',
				'wcf.todo.edit': '{lang}wcf.toDo.edit{/lang}',
				'wcf.todo.edit.delete': '{lang}wcf.toDo.edit.delete{/lang}',
				'wcf.todo.edit.disable': '{lang}wcf.toDo.edit.disable{/lang}',
				'wcf.todo.edit.enable': '{lang}wcf.toDo.edit.enable{/lang}',
				'wcf.todo.edit.restore': '{lang}wcf.toDo.edit.restore{/lang}',
				'wcf.todo.edit.trash': '{lang}wcf.toDo.edit.trash{/lang}'
			});
			
			var $updateHandler = new WCF.Todo.UpdateHandler.List();
			var $inlineEditor = new WCF.Todo.InlineEditor('.todoContainer');
			$inlineEditor.setUpdateHandler($updateHandler);
			$inlineEditor.setEnvironment('list', 0, '{link controller='ToDoList'}{/link}');
			$inlineEditor.setPermissions({
				canEnableTodo: {if $__wcf->getSession()->getPermission('mod.toDo.canEnable')}1{else}0{/if},
				canDeleteTodo: {if $__wcf->getSession()->getPermission('mod.toDo.canDelete')}1{else}0{/if},
				canDeleteTodoCompletely: {if $__wcf->getSession()->getPermission('mod.toDo.canDeleteCompletely')}1{else}0{/if},
				canRestoreTodo: {if $__wcf->getSession()->getPermission('mod.toDo.canRestore')}1{else}0{/if}
			});
			
			new WCF.Todo.Clipboard($updateHandler);
			WCF.Clipboard.init('wcf\\page\\ToDoListPage', {@$hasMarkedItems}, { });
		});
		//]]>
	</script>
</head>

<body id="tpl{$templateName|ucfirst}">

{capture assign='headerNavigation'}{/capture}

{include file='toDoListSidebar'}

{include file='header' sidebarOrientation='left'}

<header class="boxHeadline">
	<h1>{lang}wcf.toDo.taskList{/lang}</h1>
	
	{event name='headlineData'}
</header>

{include file='userNotice'}

<div class="contentNavigation">
	{pages print=true assign=pagesLinks controller='ToDoList' link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
	
	{hascontent}
		<nav>
			<ul>
				{content}
					{if $__wcf->getSession()->getPermission('user.toDo.toDo.canAddCategory')}<li><a href="{link controller='ToDoCategoryAdd'}{/link}" title="{lang}wcf.toDo.task.add{/lang}" class="button"><span class="icon icon16 icon-asterisk"></span> <span>{lang}wcf.toDo.category.add{/lang}</span></a></li>{/if}
					{if $__wcf->getSession()->getPermission('user.toDo.toDo.canAdd')}<li><a href="{link controller='ToDoAdd'}{/link}" title="{lang}wcf.toDo.task.add{/lang}" class="button"><span class="icon icon16 icon-asterisk"></span> <span>{lang}wcf.toDo.task.add{/lang}</span></a></li>{/if}
					{event name='contentNavigationButtonsTop'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</div>

{if $items}
	{foreach from=$categories item=cat}
		<div class="marginTop">
			{include file='toDoListNode'}
		</div>
	{/foreach}
{else}
	<p class="info">{lang}wcf.toDo.taskList.noTasks{/lang}</p>
{/if}

<div class="contentNavigation">
	{@$pagesLinks}
	
	{hascontent}
		<nav>
			<ul>
				{content}
					{if $__wcf->getSession()->getPermission('user.toDo.toDo.canAddCategory')}<li><a href="{link controller='ToDoCategoryAdd'}{/link}" title="{lang}wcf.toDo.task.add{/lang}" class="button"><span class="icon icon16 icon-asterisk"></span> <span>{lang}wcf.toDo.category.add{/lang}</span></a></li>{/if}
					{if $__wcf->getSession()->getPermission('user.toDo.toDo.canAdd')}<li><a href="{link controller='ToDoAdd'}{/link}" title="{lang}wcf.toDo.task.add{/lang}" class="button"><span class="icon icon16 icon-asterisk"></span> <span>{lang}wcf.toDo.task.add{/lang}</span></a></li>{/if}
					{event name='contentNavigationButtonsBottom'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
	
	<nav class="jsClipboardEditor" data-types="[ 'de.mysterycode.wcf.toDo.toDo' ]"></nav>
</div>

<div class="container marginTop">
	<ul class="containerList infoBoxList">
		<li class="box32">
			<span class="icon icon32 icon-pushpin"></span>
			
			<div>
				<div class="containerHeadline">
					<h3>{lang}wcf.toDo.task.legend{/lang}</h3><br />
					
					<p>
						<strong>{lang}wcf.toDo.task.priority{/lang}:</strong> <span class="label badge green">{lang}wcf.toDo.task.priority.low{/lang}</span> <span class="label badge red">{lang}wcf.toDo.task.priority.high{/lang}</span> | 

						<strong>{lang}wcf.toDo.task.status{/lang}:</strong>
						<span class="label badge orange unsolvedBadge">{lang}wcf.toDo.task.unsolved{/lang}</span>
						<span class="label badge yellow workBadge">{lang}wcf.toDo.task.work{/lang}</span>
						<span class="label badge green solvedBadge">{lang}wcf.toDo.task.solved{/lang}</span>
						<span class="label badge gray canceledBadge">{lang}wcf.toDo.task.canceled{/lang}</span>
						<span class="label badge gray pendingBadge">{lang}wcf.toDo.task.preparation{/lang}</span>
						<span class="label badge gray pausedBadge">{lang}wcf.toDo.task.paused{/lang}</span>
					</p>
				</div>
			</div>
		</li>
	</ul>
</div>

{include file='footer'}

</body>
</html>
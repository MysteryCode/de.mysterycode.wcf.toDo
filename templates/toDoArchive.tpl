{include file='documentHeader'}

<head>
	<title>{lang}wcf.toDo.archive{/lang} - {PAGE_TITLE|language}</title>
	
	{include file='headInclude'}
	
	{capture assign='canonicalURLParameters'}sortField={@$sortField}&sortOrder={@$sortOrder}{/capture}
	
	<link rel="canonical" href="{link controller='ToDoArchive'}{/link}" />
	
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
			$inlineEditor.setEnvironment('list', 0, '{link controller='ToDoArchive'}{/link}');
			$inlineEditor.setPermissions({
				canEnableTodo: {if $__wcf->getSession()->getPermission('mod.toDo.canEnable')}1{else}0{/if},
				canDeleteTodo: {if $__wcf->getSession()->getPermission('mod.toDo.canDelete')}1{else}0{/if},
				canDeleteTodoCompletely: {if $__wcf->getSession()->getPermission('mod.toDo.canDeleteCompletely')}1{else}0{/if},
				canRestoreTodo: {if $__wcf->getSession()->getPermission('mod.toDo.canRestore')}1{else}0{/if}
			});
			
			new WCF.Todo.Clipboard($updateHandler);
			WCF.Clipboard.init('wcf\\page\\ToDoArchivePage', {@$hasMarkedItems}, { });
		});
		//]]>
	</script>
</head>

<body id="tpl{$templateName|ucfirst}">

{capture assign='headerNavigation'}{/capture}

{include file='toDoListSidebar'}

{include file='header' sidebarOrientation='left'}

<header class="boxHeadline">
	<h1>{lang}wcf.toDo.archive{/lang}</h1>
	
	{event name='headlineData'}
</header>

{include file='userNotice'}

<div class="contentNavigation">
	{pages print=true assign=pagesLinks controller='ToDoArchive' link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
	
	{hascontent}
		<nav>
			<ul>
				{content}
					{event name='contentNavigationButtonsTop'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</div>

{if $items}
	<div class="marginTop">
		<ul class="wbbBoardList todoList">
			<li class="wbbCategory wbbDepth1 tabularBox tabularBoxTitle todoDepth1">
				<header>
					<h2>{lang}wcf.toDo.taskList.tasks{/lang} <span class="badge badgeInverse">{#$items}</span></h2>
				</header>
				<ul class="jsClipboardContainer"  data-type="de.mysterycode.wcf.toDo.toDo">
					{assign var=anchor value=$__wcf->getAnchor('top')}
					{foreach from=$objects item=task}
						<li class="message wbbBoardContainer todoContainer wbbDepth2 todoDepth2 {cycle values='todoNode1,todoNode2'} jsClipboardObject" id="todo{$task->id}" data-todo-id="{@$task->id}"{if $task->canEdit()} data-can-edit="{if $task->canEdit()}1{else}0{/if}" data-edit-url="{link controller='ToDoEdit' id=$task->id}{/link}"{/if}  data-user-id="{@$task->submitter}"
							{if $task->canEdit()}
								data-is-disabled="{if $task->isDisabled}1{else}0{/if}" data-is-deleted="{if $task->isDeleted}1{else}0{/if}"
								data-can-enable="{@$task->canEnable()}" data-can-delete="{@$task->canDelete()}" data-can-delete-completely="{@$task->canDeleteCompletely()}" data-can-restore="{@$task->canRestore()}"
							{/if}>
							{if $task->canEdit()}
								<ul class="messageQuickOptions">
									<li class="jsOnly"><input type="checkbox" class="jsClipboardItem" data-object-id="{@$task->id}" /></li>
								</ul>
							{/if}
							<div class="wbbBoard todo box32">
								<div>
									<div class="containerHeadline">
										<h3 class="{if $task->important == 1}importantToDo{/if}">{if $task->canEnter()}{if $task->private}<span class="icon icon16 icon-key"></span> {/if}<a href="{link controller='ToDo' object=$task}{/link}">{$task->title}</a>{else}{$task->title}{/if}</h3>
										
										<p class="wbbBoardDescription todoDescription">
											{if $task->status == 1}
												<span class="label badge red unsolvedBadge">{lang}wcf.toDo.task.unsolved{/lang}</span>
											{elseif $task->status == 2}
												<span class="label badge yellow workBadge">{lang}wcf.toDo.task.work{/lang}</span>
											{elseif $task->status == 3}
												<span class="label badge green solvedBadge">{lang}wcf.toDo.task.solved{/lang}</span>
											{elseif $task->status == 4}
												<span class="label badge gray canceledBadge">{lang}wcf.toDo.task.canceled{/lang}</span>
											{elseif $task->status == 5}
												<span class="label badge gray pendingBadge">{lang}wcf.toDo.task.preparation{/lang}</span>
											{elseif $task->status == 6}
												<span class="label badge gray pausedBadge">{lang}wcf.toDo.task.paused{/lang}</span>
											{/if}
											
											{if $task->categorytitle != ''}
												<a href="{link controller='ToDoCategory' id=$task->category}{/link}"><span class="label badge {$task->categorycolor}">{$task->categorytitle}</span></a>
											{else}
												<span class="label badge gray">{lang}wcf.toDo.category.notAvailable{/lang}</span>
											{/if}
										</p>
									</div>
									<div class="wbbStats todoStats">
										<dl class="plain statsDataList">
											<dt>{lang}wcf.toDo.task.submitTime{/lang}</dt>
											<dd>{@$task->timestamp|time}</dd>
										</dl>
										{if $task->endTime>0}
											<dl class="plain statsDataList">
												<dt>{lang}wcf.toDo.task.endTime{/lang}</dt>
												<dd>{@$task->endTime|time}</dd>
											</dl>
										{/if}
									</div>
									{if $task->getResponsiblePreview() && $__wcf->getSession()->getPermission('user.toDo.responsible.canView')}
										<aside class="wbbLastPost todoResponsible">
											<div>
												<div>
													<small>{lang}wcf.toDo.task.responsibles{/lang}</small>
													<ul>
														{foreach from=$task->getResponsiblePreview() item=responsible}
															<li><a href="{link controller='User' id=$responsible.userID}{/link}" class="userLink" data-user-id="{$responsible.userID}">{$responsible.username}</a></li>
														{/foreach}
													</ul>
												</div>
											</div>
										</aside>
									{/if}
								</div>
							</div>
						</li>
					{/foreach}
				</ul>
			</li>
		</ul>
	</div>
{else}
	<p class="info">{lang}wcf.toDo.taskList.noTasks{/lang}</p>
{/if}

<div class="contentNavigation">
	{@$pagesLinks}
	
	{hascontent}
		<nav>
			<ul>
				{content}
					{event name='contentNavigationButtonsBottom'}
				{/content}
				
			</ul>
		</nav>
	{/hascontent}
	
	<nav class="jsClipboardEditor" data-types="[ 'de.mysterycode.wcf.toDo.toDo' ]"></nav>
</div>

{include file='footer'}

</body>
</html>
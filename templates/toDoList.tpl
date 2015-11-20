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
			
			{if MODULE_LIKE && $__wcf->getSession()->getPermission('user.like.canViewLike')}new WCF.Todo.Like(1, {@LIKE_ENABLE_DISLIKE}, {@LIKE_SHOW_SUMMARY}, {@LIKE_ALLOW_FOR_OWN_CONTENT});{/if}
		});
		//]]>
	</script>
</head>

<body id="tpl_{$templateNameApplication}_{$templateName}" data-template="{$templateName}" data-application="{$templateNameApplication}">

{capture assign='headerNavigation'}
	
{/capture}

{capture assign='sidebar'}
	<div>
		<form method="get" action="{link controller='ToDoList'}{/link}">
			<fieldset>
				<legend><label for="sortField">{lang}wcf.toDo.task.sort{/lang}</label></legend>
				
				<dl>
					<dt></dt>
					<dd>
						<select id="sortField" name="sortField">
							<option value="title"{if $sortField == 'title'} selected="selected"{/if}>{lang}wcf.toDo.task.title{/lang}</option>
							<option value="categoryID"{if $sortField == 'categoryID'} selected="selected"{/if}>{lang}wcf.toDo.category{/lang}</option>
							<option value="endTime"{if $sortField == 'endTime'} selected="selected"{/if}>{lang}wcf.toDo.task.endTime{/lang}</option>
							<option value="status"{if $sortField == 'status'} selected="selected"{/if}>{lang}wcf.toDo.task.status{/lang}</option>
							<option value="submitter"{if $sortField == 'submitter'} selected="selected"{/if}>{lang}wcf.toDo.task.submitter{/lang}</option>
							<option value="timestamp"{if $sortField == 'timestamp'} selected="selected"{/if}>{lang}wcf.toDo.task.submitTime{/lang}</option>
							<option value="updatetimestamp"{if $sortField == 'updatetimestamp'} selected="selected"{/if}>{lang}wcf.toDo.task.updatetimestamp{/lang}</option>
							<option value="important"{if $sortField == 'important'} selected="selected"{/if}>{lang}wcf.toDo.task.priority{/lang}</option>
							<option value="remembertime"{if $sortField == 'remembertime'} selected="selected"{/if}>{lang}wcf.toDo.task.remembertime{/lang}</option>
							{event name='sortField'}
						</select>
						<select name="sortOrder">
							<option value="ASC"{if $sortOrder == 'ASC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.ascending{/lang}</option>
							<option value="DESC"{if $sortOrder == 'DESC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.descending{/lang}</option>
						</select>
					</dd>
				</dl>
			</fieldset>
			
			<div class="formSubmit">
				<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
				{@SECURITY_TOKEN_INPUT_TAG}
			</div>
		</form>
	</div>
	
	{@$__boxSidebar}
{/capture}

{include file='header' sidebarOrientation='right'}

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
					{event name='contentNavigationButtonsTop'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</div>

{include file='todoCategoryNodeList'}

{if $items}
	{include file='todoListContainer' todoList=$objects todoListItems=$items}
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

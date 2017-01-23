{capture assign='headContent'}
	<script data-relocate="true" src="{@$__wcf->getPath()}js/WCF.Todo{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@LAST_UPDATE_TIME}"></script>
	{capture assign='canonicalURLParameters'}sortField={@$sortField}&sortOrder={@$sortOrder}{/capture}
{/capture}

{capture assign='sidebarRight'}
	<form method="get" action="{link controller='ToDoTrash'}{/link}">
		<section class="box">
			<h2 class="boxTitle"><label for="sortField">{lang}wcf.toDo.task.sort{/lang}</label></h2>

			<dl>
				<dt></dt>
				<dd>
					<select id="sortField" name="sortField">
						<option value="title"{if $sortField == 'title'} selected="selected"{/if}>{lang}wcf.toDo.task.title{/lang}</option>
						<option value="endTime"{if $sortField == 'endTime'} selected="selected"{/if}>{lang}wcf.toDo.task.endTime{/lang}</option>
						<option value="status"{if $sortField == 'status'} selected="selected"{/if}>{lang}wcf.toDo.task.status{/lang}</option>
						<option value="submitter"{if $sortField == 'submitter'} selected="selected"{/if}>{lang}wcf.toDo.task.submitter{/lang}</option>
						<option value="time"{if $sortField == 'time'} selected="selected"{/if}>{lang}wcf.toDo.task.submitTime{/lang}</option>
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
		</section>

		<section class="box">
			<h2 class="boxTitle"><label for="responsibleFilter">{lang}wcf.toDo.filter.responsible{/lang}</label></h2>

			<dl>
				<dt></dt>
				<dd>
					<input type="text" name="responsibleFilter" id="responsibleFilter" value="{$responsibleFilter}" class="long" placeholder="{lang}wcf.toDo.filter.responsible.placeholder{/lang}" />
				</dd>
			</dl>
		</section>
	</form>
{/capture}

{include file='header'}

{hascontent}
	<div class="paginationTop">
		{content}{pages print=true assign=pagesLinks controller='ToDoTrash' link="pageNo=%d$canonicalURLParameters"}{/content}
	</div>
{/hascontent}

{include file='todoListContainer' todoList=$objects todoListItems=$items showCategory=true}

<footer class="contentFooter">
	{hascontent}
		<div class="paginationBottom">
			{content}{@$pagesLinks}{/content}
		</div>
	{/hascontent}

	{hascontent}
		<nav class="contentFooterNavigation">
			<ul>
				{content}
					{event name='contentFooterNavigation'}
				{/content}
			</ul>
		</nav>
	{/hascontent}

	<nav class="jsClipboardEditor" data-types="[ 'de.mysterycode.wcf.toDo.toDo' ]"></nav>
</footer>

<script data-relocate="true">
	require(['Language'], function (Language) {
		Language.addObject({
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
		$inlineEditor.setEnvironment('list', 0, '{link controller='ToDoTrash'}{/link}');
		$inlineEditor.setPermissions({
			canEnableTodo: {if $__wcf->getSession()->getPermission('mod.toDo.canEnable')}1{else}0{/if},
			canDeleteTodo: {if $__wcf->getSession()->getPermission('mod.toDo.canDelete')}1{else}0{/if},
			canDeleteTodoCompletely: {if $__wcf->getSession()->getPermission('mod.toDo.canDeleteCompletely')}1{else}0{/if},
			canRestoreTodo: {if $__wcf->getSession()->getPermission('mod.toDo.canRestore')}1{else}0{/if}
		});

		new WCF.Search.User('#responsibleFilter', null, true, [ ], true);

		new WCF.Todo.Clipboard($updateHandler);
		WCF.Clipboard.init('wcf\\page\\ToDoTrashPage', {@$hasMarkedItems}, { });
	});
</script>

{include file='footer'}

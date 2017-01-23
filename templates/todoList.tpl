{capture assign='headContent'}
	<script data-relocate="true" src="{@$__wcf->getPath()}js/WCF.Todo{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@LAST_UPDATE_TIME}"></script>
	{capture assign='canonicalURLParameters'}sortField={@$sortField}&sortOrder={@$sortOrder}{/capture}
{/capture}

{capture assign='sidebarRight'}
	<form method="get" action="{link controller='TodoList'}{/link}">
		{include file='todoSidebarForm'}
	</form>
{/capture}

{include file='header'}

{hascontent}
	<div class="paginationTop">
		{content}{pages print=true assign=pagesLinks controller='TodoList' link="pageNo=%d$canonicalURLParameters"}{/content}
	</div>
{/hascontent}

{include file='todoCategoryNodeList'}

{if $items}
	{include file='todoListContainer' todoList=$objects todoListItems=$items}
{/if}

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
		$inlineEditor.setEnvironment('list', 0, '{link controller='TodoList'}{/link}');
		$inlineEditor.setPermissions({
			canEnableTodo: {if $__wcf->getSession()->getPermission('mod.toDo.canEnable')}1{else}0{/if},
			canDeleteTodo: {if $__wcf->getSession()->getPermission('mod.toDo.canDelete')}1{else}0{/if},
			canDeleteTodoCompletely: {if $__wcf->getSession()->getPermission('mod.toDo.canDeleteCompletely')}1{else}0{/if},
			canRestoreTodo: {if $__wcf->getSession()->getPermission('mod.toDo.canRestore')}1{else}0{/if}
		});

		new WCF.Todo.Clipboard($updateHandler);
		WCF.Clipboard.init('wcf\\page\\TodoListPage', {@$hasMarkedItems}, { });

		new WCF.Search.User('#responsibleFilter', null, true, [ ], true);

		{if MODULE_LIKE && $__wcf->getSession()->getPermission('user.like.canViewLike')}new WCF.Todo.Like(1, {@LIKE_ENABLE_DISLIKE}, {@LIKE_SHOW_SUMMARY}, {@LIKE_ALLOW_FOR_OWN_CONTENT});{/if}
	});
</script>

{include file='footer'}

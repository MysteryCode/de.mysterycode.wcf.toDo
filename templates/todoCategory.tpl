{capture assign='headContent'}
	<script data-relocate="true" src="{@$__wcf->getPath()}js/WCF.Todo{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@LAST_UPDATE_TIME}"></script>
	{capture assign='canonicalURLParameters'}sortField={@$sortField}&sortOrder={@$sortOrder}{/capture}
{/capture}

{if $category->canAddTodo()}
	{capture assign='contentHeaderNavigation'}
		<li><a href="{link controller='TodoAdd' id=$category->categoryID}{/link}" title="{lang}wcf.toDo.task.add{/lang}" class="button"><span class="icon icon16 fa-asterisk"></span> <span>{lang}wcf.toDo.task.add{/lang}</span></a></li>
	{/capture}
{/if}

{capture assign='pageTitle'}{$title}{/capture}
{capture assign='contentTitle'}{$title}{/capture}

{capture assign='sidebarRight'}
	<form method="get" action="{link controller='TodoCategory' id=$id}{/link}">
		{include file='todoSidebarForm'}
	</form>
{/capture}

{include file='header'}

{hascontent}
	<div class="paginationTop">
		{content}{pages print=true assign=pagesLinks controller='TodoCategory' object=$category link="pageNo=%d$canonicalURLParameters"}{/content}
	</div>
{/hascontent}

{if $categoryNodeList|count > 0}
	{include file='todoCategoryNodeList'}
{/if}

{include file='todoListContainer' todoList=$objects todoListItems=$items}

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
					{if $category->canAddTodo() && $items}<li><a href="{link controller='TodoAdd' id=$category->categoryID}{/link}" title="{lang}wcf.toDo.task.add{/lang}" class="button"><span class="icon icon16 fa-asterisk"></span> <span>{lang}wcf.toDo.task.add{/lang}</span></a></li>{/if}
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
		$inlineEditor.setEnvironment('list', 0, '{link controller='TodoCategory'}{/link}');
		$inlineEditor.setPermissions({
			canEnableTodo: {if $__wcf->getSession()->getPermission('mod.toDo.canEnable')}1{else}0{/if},
			canDeleteTodo: {if $__wcf->getSession()->getPermission('mod.toDo.canDelete')}1{else}0{/if},
			canDeleteTodoCompletely: {if $__wcf->getSession()->getPermission('mod.toDo.canDeleteCompletely')}1{else}0{/if},
			canRestoreTodo: {if $__wcf->getSession()->getPermission('mod.toDo.canRestore')}1{else}0{/if}
		});

		new WCF.Search.User('#responsibleFilter', null, true, [ ], true);

		new WCF.Todo.Clipboard($updateHandler);
		WCF.Clipboard.init('wcf\\page\\TodoCategoryPage', {@$hasMarkedItems}, { }, {$category->categoryID});
	});
	//]]>
</script>

{include file='footer'}

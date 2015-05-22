<ul class="wcfTodoList messageList jsClipboardContainer"  data-type="de.mysterycode.wcf.toDo.toDo">
	{assign var=anchor value=$__wcf->getAnchor('top')}
	{foreach from=$objects item='todo'}
		<li id="todo{@$todo->todoID}" class="marginTop">
			<article class="wcfTodo message messageSidebarOrientationLeft dividers{if $todo->isDeleted} messageDeleted{/if}{if $todo->isDisabled} messageDisabled{/if} jsClipboardObject" data-todo-id="{@$todo->todoID}"{if $todo->canEdit()} data-can-edit="{if $todo->canEdit()}1{else}0{/if}" data-edit-url="{link controller='ToDoEdit' id=$todo->todoID}{/link}"{/if}  data-user-id="{@$todo->submitter}"
				{if $todo->canEdit()}
					data-is-disabled="{if $todo->isDisabled}1{else}0{/if}" data-is-deleted="{if $todo->isDeleted}1{else}0{/if}"
					data-can-enable="{@$todo->canEnable()}" data-can-delete="{@$todo->canDelete()}" data-can-delete-completely="{@$todo->canDeleteCompletely()}" data-can-restore="{@$todo->canRestore()}"
				{/if}>
				<div>
					<aside class="messageSidebar">
						<div>
							<header>
								<fieldset>
									<legend class="invisible">{lang}wcf.todo.thumbnail{/lang}</legend>

									<div class="wcfTodoThumbnail">
										<a href="{$todo->getLink()}" class="framed">
											<span class="icon icon-picture"></span>
										</a>
									</div>
								</fieldset>

								{event name='header'}
							</header>

							{event name='sidebar'}
						</div>
					</aside>

					<section class="messageContent">
						<div>
							<header class="messageHeader">
								<div class="messageHeadline">
									<h1><a href="{$todo->getLink()}">{$todo->title}</a></h1>

									<p>
										{if $todo->submitter}<a href="{link controller='User' id=$todo->submitter title=$todo->getUser()->username}{/link}" class="userLink" data-user-id="{@$todo->submitter}">{$todo->getUser()->username}</a>{else}{$todo->getUser()->username}{/if} - {@$todo->timestamp|time}
										{if MODULE_LIKE && $__wcf->getSession()->getPermission('user.like.canViewLike') && ($todo->likes || $todo->dislikes)}<span class="likesBadge badge jsTooltip {if $todo->cumulativeLikes > 0}green{elseif $todo->cumulativeLikes < 0}red{/if}" title="{lang likes=$todo->likes dislikes=$todo->dislikes}wcf.like.tooltip{/lang}">{if $todo->cumulativeLikes > 0}+{elseif $todo->cumulativeLikes == 0}&plusmn;{/if}{#$todo->cumulativeLikes}</span>{/if}
									</p>
								</div>

								{event name='messageHeader'}
							</header>

							<div class="messageBody">
								<div>
									{event name='beforeTeaserText'}

									<div class="messageText">
										{@$todo->getExcerpt()}
									</div>

									{event name='afterTeaserText'}
								</div>

								{event name='messageBody'}

								<div class="messageFooter">
									{if $todo->isDeleted}
										<p class="wcfTodoDeleteNote messageFooterNote">{lang}wcf.toDo.deleteNote{/lang}</p>
									{/if}

									{event name='messageFooterNotes'}
								</div>

								<footer class="messageOptions">
									<nav class="jsMobileNavigation buttonGroupNavigation">
										<ul class="smallButtons buttonGroup">{*
											*}<li><a href="{$todo->getLink()}" title="{lang}wcf.toDo.goToTodo{/lang}" class="button"><span class="icon icon16 icon-arrow-right"></span> <span>{lang}wcf.toDo.goToTodo{/lang}</span></a></li>{*
											*}{if $todo->canEdit()}<li><a href="{link controller='ToDoEdit' object=$todo}{/link}" title="{lang}wcf.toDo.task.edit{/lang}" class="button jsTodoInlineEditor"><span class="icon icon16 icon-pencil"></span> <span>{lang}wcf.global.button.edit{/lang}</span></a></li>{/if}{*
											*}<li class="toTopLink"><a href="{@$anchor}" title="{lang}wcf.global.scrollUp{/lang}" class="button jsTooltip"><span class="icon icon16 icon-arrow-up"></span> <span class="invisible">{lang}wcf.global.scrollUp{/lang}</span></a></li>{*
											*}{event name='messageOptions'}{*
										*}</ul>
									</nav>
								</footer>
							</div>
						</div>
					</section>
				</div>
			</article>
		</li>
	{/foreach}
</ul>

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
		var $inlineEditor = new WCF.Todo.InlineEditor('.wcfTodo');
		$inlineEditor.setUpdateHandler($updateHandler);
		$inlineEditor.setPermissions({
			canEnableTodo: 0,
			canDeleteTodo: {if $__wcf->getSession()->getPermission('mod.toDo.canDelete')}1{else}0{/if},
			canDeleteTodoCompletely: {if $__wcf->getSession()->getPermission('mod.toDo.canDeleteCompletely')}1{else}0{/if},
			canRestoreTodo: {if $__wcf->getSession()->getPermission('mod.toDo.canRestore')}1{else}0{/if}
		});
		
		new WCF.Todo.Clipboard($updateHandler);
		WCF.Clipboard.init('wcf\\page\\DeletedContentListPage', {@$objects->getMarkedItems()}, { });
	});
	//]]>
</script>
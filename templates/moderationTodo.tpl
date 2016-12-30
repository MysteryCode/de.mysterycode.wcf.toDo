<article class="wcfTodo message messageSidebarOrientationLeft todo jsTodo dividers{if $todo->isDeleted} messageDeleted{/if}{if $todo->isDisabled} messageDisabled{/if} jsClipboardObject" data-todo-id="{@$todo->todoID}"{if $todo->canEdit()} data-can-edit="{if $todo->canEdit()}1{else}0{/if}" data-edit-url="{link controller='ToDoEdit' id=$todo->todoID}{/link}"{/if} data-user-id="{@$todo->submitter}"
	{if $todo->canEdit()}
		data-is-disabled="{if $todo->isDisabled}1{else}0{/if}"
		data-is-deleted="{if $todo->isDeleted}1{else}0{/if}"
		data-can-enable="{@$todo->canEnable()}"
		data-can-delete="{@$todo->canDelete()}"
		data-can-delete-completely="{@$todo->canDeleteCompletely()}"
		data-can-restore="{@$todo->canRestore()}"
	{/if}>
	<div>
		<section class="messageContent">
			<div>
				<header class="messageHeader">
					<div class="box32">
						<a href="{link controller='ToDo' object=$todo}{/link}" class="framed jsTooltip" title="{lang}wcf.toDo.goToTodo{/lang}">
							<span class="framed icon icon32 icon-tasks"></span>
						</a>

						<div class="messageHeadline">
							<h1><a href="{link controller='ToDo' object=$todo}{/link}">{$todo->title}</a></h1>
							<p>
								<span class="username">{if $todo->getUser()->userID}<a href="{link controller='User' object=$todo->getUser()}{/link}">{$todo->getUser()->username}</a>{else}{$todo->getUser()->username}{/if}</span>
								{@$todo->time|time}
							</p>
						</div>
					</div>
				</header>

				<div class="messageBody">
					<div>
						<div class="messageText">
							{@$todo->getExcerpt()}
						</div>
					</div>

					<footer class="messageOptions">
						<nav class="jsMobileNavigation buttonGroupNavigation">
							<ul class="smallButtons buttonGroup">{*
								*}{if $todo->canEdit()}<li><a href="{link application='wcf' controller='ToDoEdit' id=$todo->todoID}{/link}" title="{lang}wcf.toDo.task.edit{/lang}" class="button jsTodoEditButton"><span class="icon icon16 icon-pencil"></span> <span>{lang}wcf.global.button.edit{/lang}</span></a></li>{/if}{*
								*}<li class="toTopLink"><a href="{@$__wcf->getAnchor('top')}" title="{lang}wcf.global.scrollUp{/lang}" class="button jsTooltip"><span class="icon icon16 icon-arrow-up"></span> <span class="invisible">{lang}wcf.global.scrollUp{/lang}</span></a></li>{*
								*}{event name='messageOptions'}{*
							*}</ul>
						</nav>
					</footer>
				</div>
			</div>
		</section>
	</div>
</article>

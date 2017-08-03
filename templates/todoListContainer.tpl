{if !$showCategory|isset}{assign var='showCategory' value=false}{/if}

{if $todoListItems}
	<div class="section tabularBox jsClipboardContainer" data-type="de.mysterycode.wcf.toDo.toDo">
		{if $__wcf->session->getPermission('mod.toDo.canEdit')}
			<div class="todoListBeforeActions">
				<span class="jsMarkAll messageClipboardCheckbox">
					<input type="checkbox" id="todoMarkAll" class="jsClipboardMarkAll" style="display: none;">
					<label for="todoMarkAll" class="jsTooltip" title="{lang}wcf.toDo.task.markAll{/lang}"></label>
				</span>
				{event name='todoListBeforeActions'}
			</div>
		{/if}
		<ul class="todoList">
			{foreach from=$todoList item=todo}
				<li id="todo{$todo->todoID}" class="message jsClipboardObject jsTodo{if $todo->isDeleted} messageDeleted{/if}{if $todo->isDisabled} messageDisabled{/if}"
				    data-todo-id="{@$todo->todoID}"
				    data-element-id="{@$todo->todoID}"
				    data-user-id="{@$todo->userID}"
						{if $todo->canEdit()}
							data-can-edit="1"
							data-edit-url="{link controller='TodoEdit' id=$todo->todoID}{/link}"
							data-is-disabled="{if $todo->isDisabled}1{else}0{/if}"
							data-is-deleted="{if $todo->isDeleted}1{else}0{/if}"
							data-can-enable="{@$todo->canEnable()}"
							data-can-delete="{@$todo->canDelete()}"
							data-can-delete-completely="{@$todo->canDeleteCompletely()}"
							data-can-restore="{@$todo->canRestore()}"
						{/if}
				>

					<div class="todoData">
						<div>
							<div class="containerHeadline">
								{if TODO_PROGRESS_ENABLE}
									<span class="todoProgress label badge {if $todo->progress > 33}{if $todo->progress > 66}green{else}yellow{/if}{else}red{/if}">{$todo->progress}%</span>
								{/if}
								{if $showCategory}
									<a href="{$todo->getCategory()->getLink()}"><span class="label badge {$todo->getCategory()->cssClass}">{$todo->getCategory()->getTitle()}</span></a>
								{/if}
								<h3{if $todo->important == 1} class="importantToDo"{/if}>
									{if $todo->canEnter()}
										{if $todo->private}<span class="icon icon16 fa-key"></span> {/if}
										<a href="{$todo->getLink()}">{$todo->title}</a>
									{else}
										{$todo->title}
									{/if}
									{if MODULE_LIKE && $__wcf->getSession()->getPermission('user.like.canViewLike')}
										<small class="wcfLikeCounter{if $todo->cumulativeLikes > 0} likeCounterLiked{elseif $todo->cumulativeLikes < 0} likeCounterDisliked{/if}">{if $todo->likes || $todo->dislikes}<span class="icon icon16 fa-thumbs-o-{if $todo->cumulativeLikes < 0}down{else}up{/if} jsTooltip" title="{lang likes=$todo->likes dislikes=$todo->dislikes}wcf.like.tooltip{/lang}"></span><span class="wcfLikeValue">{if $todo->cumulativeLikes > 0}+{elseif $todo->cumulativeLikes == 0}&plusmn;{/if}{#$todo->cumulativeLikes}</span>{/if}</small>
									{/if}
								</h3>

								<p class="todoDescription">
									{if $__wcf->getSession()->getPermission('user.toDo.status.canView') && $todo->statusID}
										<span class="label badge {$todo->getStatus()->cssClass}" id="todoStatus{$todo->getStatus()->statusID}">{$todo->getStatus()->getTitle()}</span>
									{/if}
								</p>
							</div>

							{if $todo->isDeleted}
								<small>
									{lang}wcf.toDo.deleteNote{/lang}
								</small>
							{/if}
						</div>

						<div class="todoStats">
							<dl class="plain statsDataList">
								<dt>{lang}wcf.toDo.category{/lang}</dt>
								<dd>{$todo->getCategory()->getTitle()}</dd>
							</dl>
							<dl class="plain statsDataList">
								<dt>{lang}wcf.toDo.task.submitTime{/lang}</dt>
								<dd>
									{if $todo->time < TIME_NOW - (24*60*60)}
										{@$todo->time|date}
									{else}
										{@$todo->time|time}
									{/if}
								</dd>
							</dl>
							{if $todo->canViewDeadline() && $todo->endTime>0}
								<dl class="plain statsDataList">
									<dt>{lang}wcf.toDo.task.endTime{/lang}</dt>
									<dd>
										{if $todo->endTime > TIME_NOW + (48*60*60)}
											{@$todo->endTime|date}
										{else}
											{@$todo->endTime|time}
										{/if}
									</dd>
								</dl>
							{/if}
						</div>

						{if $todo->getResponsiblePreview()}
							<aside class="todoResponsible">
								<div>
									<small>{lang}wcf.toDo.task.responsibles{/lang}</small>
									<ul class="inlineList commaSeparated">
										{foreach from=$todo->getResponsiblePreview() item=responsible}
											<li>{@$responsible}</li>
										{/foreach}
									</ul>
								</div>
							</aside>
						{/if}
					</div>

					{if $todo->canEdit()}
						<ul class="messageQuickOptions">
							<li class="jsOnly"><label class="messageClipboardCheckbox"><input type="checkbox" class="jsClipboardItem" data-object-id="{@$todo->todoID}" /></label></li>
						</ul>
					{/if}
				</li>
			{/foreach}
		</ul>
	</div>
{else}
	<p class="info">{lang}wcf.toDo.taskList.noTasks{/lang}</p>
{/if}

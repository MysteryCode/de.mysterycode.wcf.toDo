{if !$showCategory|isset}{assign var='showCategory' value=false}{/if}

{if $todoListItems}
	<div class="marginTop">
		<ul class="todoList">
			<li class="tabularBox tabularBoxTitle todoDepth1">
				<header>
					<h2>{lang}wcf.toDo.taskList.tasks{/lang} <span class="badge badgeInverse">{#$items}</span></h2>
				</header>
				<ul class="jsClipboardContainer"  data-type="de.mysterycode.wcf.toDo.toDo">
					{assign var=anchor value=$__wcf->getAnchor('top')}
					{foreach from=$todoList item=todo}
						<li id="todo{$todo->todoID}" class="message todoContainer todoDepth2 {cycle values='todoNode1,todoNode2'} jsClipboardObject jsTodo{if $todo->isDeleted} messageDeleted{/if}{if $todo->isDisabled} messageDisabled{/if}" data-todo-id="{@$todo->todoID}" data-element-id="{@$todo->todoID}"{if $todo->canEdit()} data-can-edit="{if $todo->canEdit()}1{else}0{/if}" data-edit-url="{link controller='ToDoEdit' id=$todo->todoID}{/link}"{/if}  data-user-id="{@$todo->userID}"
							{if $todo->canEdit()}
								data-is-disabled="{if $todo->isDisabled}1{else}0{/if}" data-is-deleted="{if $todo->isDeleted}1{else}0{/if}"
								data-can-enable="{@$todo->canEnable()}" data-can-delete="{@$todo->canDelete()}" data-can-delete-completely="{@$todo->canDeleteCompletely()}" data-can-restore="{@$todo->canRestore()}"
							{/if}>
							{if $todo->canEdit()}
								<ul class="messageQuickOptions">
									<li class="jsOnly"><input type="checkbox" class="jsClipboardItem" data-object-id="{@$todo->todoID}" /></li>
								</ul>
							{/if}
							<div class="todo box32">
								<div>
									<div class="containerHeadline">
										<h3 class="{if $todo->important == 1}importantToDo{/if}">
											{if TODO_PROGRESS_ENABLE}
												<span class="todoProgress label badge {if $todo->progress > 33}{if $todo->progress > 66}green{else}yellow{/if}{else}red{/if}">{$todo->progress}%</span>
											{/if}
											{if $showCategory}
												<span class="label badge {$todo->getCategory()->cssClass}">{$todo->getCategory()->getTitle()}</span>
											{/if}
											{if $todo->canEnter()}{if $todo->private}<span class="icon icon16 icon-key"></span> {/if}<a href="{link controller='ToDo' object=$todo}{/link}">{$todo->title}</a>{else}{$todo->title}{/if}
											{if MODULE_LIKE && $__wcf->getSession()->getPermission('user.like.canViewLike') && ($todo->likes || $todo->dislikes)}<span class="likesBadge badge jsTooltip {if $todo->cumulativeLikes > 0}green{elseif $todo->cumulativeLikes < 0}red{/if}" title="{lang likes=$todo->likes dislikes=$todo->dislikes}wcf.like.tooltip{/lang}">{if $todo->cumulativeLikes > 0}+{elseif $todo->cumulativeLikes == 0}&plusmn;{/if}{#$todo->cumulativeLikes}</span>{/if}
										</h3>
										
										<p class="todoDescription">
											{if $__wcf->getSession()->getPermission('user.toDo.status.canView') && $todo->statusID}
												<span class="label badge {$todo->getStatus()->cssClass}" id="todoStatus{$todo->getStatus()->statusID}">{$todo->getStatus()->getTitle()}</span>
											{/if}
										</p>
										
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
											<dd>{@$todo->timestamp|time}</dd>
										</dl>
										{if $todo->canViewDeadline() && $todo->endTime>0}
											<dl class="plain statsDataList">
												<dt>{lang}wcf.toDo.task.endTime{/lang}</dt>
												<dd>{@$todo->endTime|time}</dd>
											</dl>
										{/if}
									</div>
									{if $todo->getResponsiblePreview()}
										<aside class="todoResponsible">
											<div>
												<div>
													<small>{lang}wcf.toDo.task.responsibles{/lang}</small>
													<ul>
														{foreach from=$todo->getResponsiblePreview() item=responsible}
															<li>{@$responsible}</li>
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

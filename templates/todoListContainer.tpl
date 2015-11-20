{if $todoListItems}
	<div class="marginTop">
		<ul class="todoList">
			<li class="tabularBox tabularBoxTitle todoDepth1">
				<header>
					<h2>{lang}wcf.toDo.todoList.todos{/lang} <span class="badge badgeInverse">{#$items}</span></h2>
				</header>
				<ul class="jsClipboardContainer"  data-type="de.mysterycode.wcf.toDo.toDo">
					{assign var=anchor value=$__wcf->getAnchor('top')}
					{foreach from=$todoList item=todo}
						<li id="todo{$todo->todoID}" class="message todoContainer todoDepth2 {cycle values='todoNode1,todoNode2'} jsClipboardObject jsTodo{if $todo->isDeleted} messageDeleted{/if}" data-todo-id="{@$todo->todoID}" data-element-id="{@$todo->todoID}"{if $todo->canEdit()} data-can-edit="{if $todo->canEdit()}1{else}0{/if}" data-edit-url="{link controller='ToDoEdit' id=$todo->todoID}{/link}"{/if}  data-user-id="{@$todo->submitter}"
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
										<h3 class="{if $todo->important == 1}importantToDo{/if}">{if $todo->canEnter()}{if $todo->private}<span class="icon icon16 icon-key"></span> {/if}<a href="{link controller='ToDo' object=$todo}{/link}">{$todo->title}</a>{else}{$todo->title}{/if}</h3>
										
										<p class="todoDescription">
											{if $__wcf->getSession()->getPermission('user.toDo.status.canView') && $todo->status}
												<span class="label badge {$todo->getStatus()->cssClass}" id="todoStatus{$todo->getStatus()->statusID}">{$todo->getStatus()->getTitle()}</span>
											{/if}
										</p>
										
										{if TODO_PROGRESS_ENABLE}
											<span class="label badge {if $todo->progress > 33}{if $todo->progress > 66}green{else}yellow{/if}{else}red{/if}">{$todo->progress}%</span>
										{/if}
										
										{if $todo->isDeleted}
											<small>
												{lang}wcf.toDo.deleteNote{/lang}
											</small>
										{/if}
									</div>
									<div class="todoStats">
										<dl class="plain statsDataList">
											<dt>{lang}wcf.toDo.todo.submitTime{/lang}</dt>
											<dd>{@$todo->timestamp|time}</dd>
										</dl>
										{if $todo->endTime>0}
											<dl class="plain statsDataList">
												<dt>{lang}wcf.toDo.todo.endTime{/lang}</dt>
												<dd>{@$todo->endTime|time}</dd>
											</dl>
										{/if}
									</div>
									{if $todo->getResponsiblePreview() && $__wcf->getSession()->getPermission('user.toDo.responsible.canView')}
										<aside class="todoResponsible">
											<div>
												<div>
													<small>{lang}wcf.toDo.todo.responsibles{/lang}</small>
													<ul>
														{foreach from=$todo->getResponsiblePreview() item=responsible}
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

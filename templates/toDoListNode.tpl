{hascontent}
<ul class="todoList">
	<li class="tabularBox tabularBoxTitle todoDepth1">
		<header>
			<h2>{if $cat->title|isset}{@$cat->title}{else}{lang}wcf.toDo.taskList.tasks{/lang}{/if} <span class="badge badgeInverse">{#$items}</span></h2>
		</header>
		<ul id="category{@$cat->id}" class="jsClipboardContainer" data-type="de.mysterycode.wcf.toDo.toDo">
			{assign var=anchor value=$__wcf->getAnchor('top')}
			{content}
				{foreach from=$objects item=task}
					{if $task->category == $cat->id}
						<li class="message todoContainer todoDepth2 {cycle values='todoNode1,todoNode2'} jsClipboardObject" id="todo{$task->id}" data-todo-id="{@$task->id}"{if $task->canEdit()} data-can-edit="{if $task->canEdit()}1{else}0{/if}" data-edit-url="{link controller='ToDoEdit' id=$task->id}{/link}"{/if}  data-user-id="{@$task->submitter}"
							{if $task->canEdit()}
								data-is-disabled="{if $task->isDisabled}1{else}0{/if}" data-is-deleted="{if $task->isDeleted}1{else}0{/if}"
								data-can-enable="{@$task->canEnable()}" data-can-delete="{@$task->canDelete()}" data-can-delete-completely="{@$task->canDeleteCompletely()}" data-can-restore="{@$task->canRestore()}"
							{/if}>
							{if $task->canEdit()}
								<ul class="messageQuickOptions">
									<li class="jsOnly"><input type="checkbox" class="jsClipboardItem" data-object-id="{@$task->id}" /></li>
								</ul>
							{/if}
							<div class="todo box32 priority-{$task->priority}">
								<!-- <div style="min-height:22px;width:12px;background-color:grey;"></div> -->
								<div>
									<div class="priority">
										{if $task->priority == 0}
											<span class="label badge green">{lang}wcf.toDo.task.priority.low{/lang}</span>
										{elseif $task->priority == 2}
											<span class="label badge red">{lang}wcf.toDo.task.priority.high{/lang}</span>
										{/if}
									</div>
									<div class="status">
										{if $task->status == 1}
											<span class="label badge orange unsolvedBadge">{lang}wcf.toDo.task.unsolved{/lang}</span>
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
									</div>
									<div class="containerHeadline" style="float:left;">
										<h3 class="{if $task->important == 1}importantToDo{/if}">{if $task->canEnter()}{if $task->private}<span class="icon icon16 icon-key"></span> {/if}<a href="{link controller='ToDo' object=$task}{/link}" class="todoLink" data-todo-id="{$task->id}">{$task->title}</a>{else}{$task->title}{/if}</h3>
									</div>
									<div style="clear:both;"></div>
									<!--
									<div class="todoStats">
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
										<aside class="todoResponsible">
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
									-->
								</div>
							</div>
						</li>
					{/if}
				{/foreach}
			{/content}
		</ul>
	</li>
</ul>
{/hascontent}
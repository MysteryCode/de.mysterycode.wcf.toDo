<article class="message messageReduced">
	<div>
		<section class="messageContent">
			<div>
				<header class="messageHeader">
					<div class="box32">
						<a href="{link controller='ToDo' object=$todo}{/link}" class="framed jsTooltip" title="{lang}wcf.toDo.goToTodo{/lang}">
							<span class="framed icon icon-tasks"></span>
						</a>

						<div class="messageHeadline">
							<h1><a href="{link controller='ToDo' object=$todo}{/link}">{$todo->title}</a></h1>
							<p>
								<span class="username">{if $todo->getUser()->userID}<a href="{link controller='User' object=$todo->getUser()}{/link}">{$todo->getUser()->username}</a>{else}{$todo->getUser()->username}{/if}</span>
								{@$todo->timestamp|time}
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

{capture assign='pageTitle'}{lang}wcf.toDo.task.detail{/lang}{/capture}

{capture assign='headContent'}
	<script data-relocate="true" src="{@$__wcf->getPath()}js/WCF.Todo{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@LAST_UPDATE_TIME}"></script>
{/capture}

{capture assign='contentHeader'}
	<header class="contentHeader messageGroupContentHeader todo{if $todo->isDisabled} messageDisabled{/if}{if $todo->isDeleted} messageDeleted{/if}" data-todo-id="{@$todo->todoID}"{if $todo->canEdit()} data-is-deleted="{@$todo->isDeleted}" data-is-disabled="{@$todo->isDisabled}"{/if}>
		<div class="contentHeaderTitle">
			<h1 class="contentTitle">{lang}wcf.toDo.task.detail{/lang}</h1>
			<ul class="inlineList contentHeaderMetaData">
				{if $todo->hasLabels()}
					<li>
						<span class="icon icon16 fa-tags"></span>
						<ul class="labelList">
							{foreach from=$todo->getLabels() item=label}
								<li><span class="label badge{if $label->getClassNames()} {$label->getClassNames()}{/if}">{lang}{$label->label}{/lang}</span></li>
							{/foreach}
						</ul>
					</li>
				{/if}
			</ul>
		</div>

		{hascontent}
			<nav class="contentHeaderNavigation">
				<ul class="jsTodoInlineEditorContainer" data-todo-id="{@$todo->todoID}" data-is-disabled="{@$todo->isDisabled}" data-is-deleted="{@$todo->isDeleted}">
					{content}
						{if $todo->canEdit()}<li><a class="button jsTodoInlineEditor jsOnly"><span class="icon icon16 fa-pencil"></span> <span>{lang}wcf.global.button.edit{/lang}</span></a></li>{/if}
						<li class="jsReportTodo jsOnly" data-object-id="{@$todo->todoID}"><a title="{lang}wcf.moderation.report.reportContent{/lang}" class="button jsTooltip"><span class="icon icon16 fa-exclamation-triangle"></span> <span class="invisible">{lang}wcf.moderation.report.reportContent{/lang}</span></a></li>
						{if $todo->canParticipate() && $todo->statusID != 1}<li class="jsParticipateTodo jsOnly" data-object-id="{@$todo->todoID}" data-user-id="{$__wcf->user->userID}"><a title="{lang}wcf.toDo.task.participate{/lang}" class="button jsTooltip"><span class="icon icon16 fa-signin"></span> <span class="invisible">{lang}wcf.toDo.task.participate{/lang}</span></a></li>{/if}
						{if $todo->canEditProgress() && $todo->statusID != 1}<li class="updateProgress jsOnly" data-object-id="{@$todo->todoID}" data-user-id="{$__wcf->user->userID}"><a title="{lang}wcf.toDo.task.progress.update{/lang}" class="button jsTooltip"><span class="icon icon16 fa-refresh"></span> <span class="invisible">{lang}wcf.toDo.task.progress.update{/lang}</span></a></li>{/if}
						{if $todo->canEditStatus() && $todo->statusID != 1}<li class="jsMarkSolvedTodo jsOnly" data-object-id="{@$todo->todoID}" data-user-id="{$__wcf->user->userID}"><a title="{lang}wcf.toDo.task.solve{/lang}" class="button jsTooltip"><span class="icon icon16 fa-check"></span> <span class="invisible">{lang}wcf.toDo.task.solve{/lang}</span></a></li>{/if}
						{event name='contentHeaderNavigation'}
					{/content}
				</ul>
			</nav>
		{/hascontent}
	</header>
{/capture}

{include file='header'}

{assign var='objectID' value=$todo->todoID}

<div class="todoQuoteContainer jsTodo"
	data-object-id="{@$todo->todoID}"
	data-todo-id="{@$todo->todoID}"
	data-is-disabled="{@$todo->isDisabled}"
	data-is-deleted="{@$todo->isDeleted}"
	{if MODULE_LIKE}data-object-id="{@$package->todoID}"
		data-object-type="de.mysterycode.wcf.toDo.toDo"
		data-like-liked="{if $todoLikeData[$todo->todoID]|isset}{@$todoLikeData[$todo->todoID]->liked}{/if}"
		data-like-likes="{if $todoLikeData[$todo->todoID]|isset}{@$todoLikeData[$todo->todoID]->likes}{else}0{/if}"
		data-like-dislikes="{if $todoLikeData[$todo->todoID]|isset}{@$todoLikeData[$todo->todoID]->dislikes}{else}0{/if}"
		data-like-users='{ {if $todoLikeData[$todo->todoID]|isset}{implode from=$todoLikeData[$todo->todoID]->getUsers() item=likeUser}"{@$likeUser->userID}": "{$likeUser->username|encodeJSON}"{/implode}{/if} }'
	{/if}
>
	<section class="section todoContainer{if $todo->isDeleted} todoDeleted{/if}{if $todo->isDisabled} todoDisabled{/if}">
		{event name='beforeInfo'}
		<h2 class="sectionTitle">{$todo->title}</h2>
		<dl>
			<dt>{lang}wcf.toDo.task.title{/lang}</dt>
			<dd>{$todo->title}</dd>

			<dt>{lang}wcf.toDo.category{/lang}</dt>
			<dd>
				{if $todo->getCategory()}
					<a href="{$todo->getCategory()->getLink()}"><span class="label badge" style="background-color: {$todo->getCategory()->color};">{$todo->getCategory()->getTitle()}</span></a>
				{else}
					<span class="label badge gray">{lang}wcf.toDo.category.notAvailable{/lang}</span>
				{/if}
			</dd>

			{if $todo->status}
				<dt>{lang}wcf.toDo.task.status{/lang}</dt>
				<dd>
					<span class="label badge {$todo->getStatus()->cssClass}" id="todoStatus{$todo->getStatus()->statusID}">{$todo->getStatus()->getTitle()}</span>
				</dd>
			{/if}

			{if TODO_PROGRESS_ENABLE}
				<dt>{lang}wcf.toDo.task.progress{/lang}</dt>
				<dd>
					<div class="progressbar_main">
						<div class="progressbar_inner" style="width:{if $todo->progress == 100}0%{else}calc(100% - {$todo->progress}% + 2px){/if};"></div>
						<span class="progressbar_text">{$todo->progress} {lang}wcf.toDo.task.progress.percent{/lang}</span>
					</div>
				</dd>
			{/if}

			<dt>{lang}wcf.toDo.task.priority{/lang}</dt>
			<dd>
				{if $todo->important == 3}<span class="label badge grey">{lang}wcf.toDo.task.priority.low{/lang}</span>{/if}
				{if $todo->important == 2 || $todo->important == 0}<span class="label badge blue">{lang}wcf.toDo.task.priority.normal{/lang}</span>{/if}
				{if $todo->important == 1}<span class="label badge red">{lang}wcf.toDo.task.priority.high{/lang}</span>{/if}
			</dd>

			<dt>{lang}wcf.toDo.task.privacy{/lang}</dt>
			<dd><span class="icon fa-{if $todo->private == 0}un{/if}lock"></span></dd>

			{if $todo->time > 0}
				<dt>{lang}wcf.toDo.task.submitTime{/lang}</dt>
				<dd>{@$todo->time|time}</dd>
			{/if}

			{if $todo->canViewDeadline() && $todo->endTime > 0}
				<dt>{lang}wcf.toDo.task.endTime{/lang}</dt>
				<dd>{@$todo->endTime|time}</dd>
			{/if}

			{if $todo->canViewReminder() && $todo->remembertime > 0}
				<dt>{lang}wcf.toDo.task.remembertime{/lang}</dt>
				<dd>{@$todo->remembertime|date}</dd>
			{/if}

			<dt>{lang}wcf.toDo.task.submitter{/lang}</dt>
			<dd>{if $todo->submitter != 0 && $todo->username != ''}<a href="{link controller='User' id=$todo->submitter}{/link}" class="userLink" data-user-id="{$todo->submitter}">{$todo->username}</a>{else}{lang}wcf.user.guest{/lang}{/if}</dd>

			{if $todo->getResponsibles() && $todo->canViewResponsibleUsers()}
				<dt>{lang}wcf.toDo.task.responsible.users{/lang}</dt>
				<dd>
					<ul class="inlineList commaSeparated">
						{foreach from=$todo->getResponsibles() item=responsible}
							<li><a href="{link controller='User' object=$responsible}{/link}" class="userLink" data-user-id="{$responsible->userID}">{$responsible->username}</a></li>
						{/foreach}
					</ul>
				</dd>
			{/if}

			{if $todo->getResponsibleGroups() && $todo->canViewResponsibleGroups()}
				<dt>{lang}wcf.toDo.task.responsible.groups{/lang}</dt>
				<dd>
					<ul class="inlineList commaSeparated">
						{foreach from=$todo->getResponsibleGroups() item=responsible}
							<li>{@'%s'|str_replace:$responsible->getName():$responsible->userOnlineMarking}</li>
						{/foreach}
					</ul>
				</dd>
			{/if}
		</dl>

		{event name='additionalInfo'}

		{if MODULE_LIKE}<div class="section"><span class="todoLikesSummery"></span><ul class="todoLikeButtons buttonGroup"></ul></div>{/if}
	</section>

	{if $todo->description != ''}
		<section class="section todoDescription">
			<h2 class="sectionTitle">{lang}wcf.toDo.task.description{/lang}</h2>

			{@$todo->getFormattedMessage()}

			{include file='attachments'}
		</section>
	{/if}

	{if $todo->note != ''}
		<section class="section todoNotes">
			<h2 class="sectionTitle">{lang}wcf.toDo.task.note{/lang}</h2>

			{@$todo->getFormattedNote()}
		</section>
	{/if}
</div>

{event name='sections'}

{if TODO_COMMENTS_ENABLE}
	<section class="section" id="comments">
		<h2 class="sectionTitle">{lang}wcf.toDo.comments{/lang} <span class="badge">{@$commentList->countObjects()}</span></h2>

		{include file='__commentJavaScript' commentContainerID='toDoCommentList'}

		{if $commentCanAdd}
			<ul id="toDoCommentList" class="commentList containerList" data-can-add="true" data-object-id="{@$todo->todoID}" data-object-type-id="{@$commentObjectTypeID}" data-comments="{@$commentList->countObjects()}" data-last-comment-time="{@$lastCommentTime}">
				{include file='commentList'}
			</ul>
		{else}
			{hascontent}
				<ul id="toDoCommentList" class="commentList containerList" data-can-add="false" data-object-id="{@$todo->todoID}" data-object-type-id="{@$commentObjectTypeID}" data-comments="{@$commentList->countObjects()}" data-last-comment-time="{@$lastCommentTime}">
					{content}
						{include file='commentList'}
					{/content}
				</ul>
			{hascontentelse}
				<p class="info">{lang}wcf.toDo.comments.noEntries{/lang}</p>
			{/hascontent}
		{/if}

	</section>
{/if}

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
			'wcf.todo.edit.trash': '{lang}wcf.toDo.edit.trash{/lang}',
			'wcf.moderation.report.reportContent': '{lang}wcf.moderation.report.reportContent{/lang}',
			'wcf.moderation.report.success': '{lang}wcf.moderation.report.success{/lang}',
			'wcf.toDo.task.participate': '{lang}wcf.toDo.task.participate{/lang}',
			'wcf.toDo.task.participate.success': '{lang}wcf.toDo.task.participate.success{/lang}',
			'wcf.toDo.task.participate.shure': '{lang}wcf.toDo.task.participate.shure{/lang}',
			'wcf.toDo.task.solve': '{lang}wcf.toDo.task.solve{/lang}',
			'wcf.toDo.task.solve.success': '{lang}wcf.toDo.task.solve.success{/lang}',
			'wcf.toDo.task.solve.shure': '{lang}wcf.toDo.task.solve.shure{/lang}',
			'wcf.toDo.task.progress.percent': '{lang}wcf.toDo.task.progress.percent{/lang}',
			'wcf.toDo.task.progress.update': '{lang}wcf.toDo.task.progress.update{/lang}'

		});

		var $updateHandler = new WCF.Todo.UpdateHandler.Todo();
		var $inlineEditor = new WCF.Todo.InlineEditor('.jsTodoInlineEditorContainer');
		$inlineEditor.setUpdateHandler($updateHandler);
		$inlineEditor.setEnvironment('todo', {@$todo->todoID}, '{$todo->getLink()}');
		$inlineEditor.setPermissions({
			canEnableTodo: {if $todo->canEnable()}1{else}0{/if},
			canDeleteTodo: {if $todo->canDelete()}1{else}0{/if},
			canDeleteTodoCompletely: {if $todo->canDeleteCompletely()}1{else}0{/if},
			canRestoreTodo: {if $todo->canRestore()}1{else}0{/if}
		});

		new WCF.Moderation.Report.Content('de.mysterycode.wcf.toDo.toDo', '.jsReportTodo');

		{include file='__messageQuoteManager' wysiwygSelector='text'}
		new WCF.Todo.QuoteHandler($quoteManager);

		new WCF.Todo.Participate('.jsParticipateTodo');
		new WCF.Todo.MarkSolved('.jsMarkSolvedTodo');
		new WCF.Todo.UpdateProgress({$todo->todoID});

		{if MODULE_LIKE}
			require(['WoltLabSuite/Core/Ui/Like/Handler'], function(UiLikeHandler) {
				new UiLikeHandler('de.mysterycode.wcf.toDo.toDo', {
					// settings
					isSingleItem: true,

					// permissions
					canDislike: {if LIKE_ENABLE_DISLIKE}true{else}false{/if},
					canLike: {if $__wcf->getUser()->userID && $todo->getCategory()->getPermission('user.canLikeTodo')}true{else}false{/if},
					canLikeOwnContent: {if LIKE_ALLOW_FOR_OWN_CONTENT}true{else}false{/if},
					canViewSummary: {if LIKE_SHOW_SUMMARY}true{else}false{/if},

					// selectors
					badgeContainerSelector: '.jsTodo',
					buttonAppendToSelector: '.todoLikeButtons',
					containerSelector: '.jsTodo',
					summarySelector: '.todoLikesSummery'
				});
			});
		{/if}
	});
</script>

{include file='footer'}

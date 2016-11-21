{include file='documentHeader'}

<head>
	<title>{lang}wcf.toDo.task.detail{/lang} - {PAGE_TITLE|language}</title>
	
	{include file='headInclude'}

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
			var $inlineEditor = new WCF.Todo.InlineEditor('.todo');
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
			
			{if $todo->isDisabled || $todo->isDeleted}
				$('.sidebar').addClass('{if $todo->isDeleted}deleted{else}disabled{/if}');
			{/if}
			
			{if MODULE_LIKE && $__wcf->getSession()->getPermission('user.like.canViewLike')}
				new WCF.Todo.Like.Detail({if $__wcf->getUser()->userID && $todo->getCategory()->getPermission('user.canLikeTodo')}1{else}0{/if}, {@LIKE_ENABLE_DISLIKE}, {@LIKE_SHOW_SUMMARY}, {@LIKE_ALLOW_FOR_OWN_CONTENT});
			{/if}
			
			{event name='javascriptInit'}
		});
		//]]>
	</script>
</head>

<body id="tpl_{$templateNameApplication}_{$templateName}" data-template="{$templateName}" data-application="{$templateNameApplication}">

{capture assign='headerNavigation'}
	
{/capture}

{if $__boxSidebar|isset && $__boxSidebar}
	{capture assign='sidebar'}
		{@$__boxSidebar}
	{/capture}
{/if}

{include file='header' sidebarOrientation='right'}

<header class="boxHeadline todo jsTodo"
	data-todo-id="{@$todo->todoID}"{if $todo->canEdit()}
	data-can-edit="{if $todo->canEdit()}1{else}0{/if}"
	data-edit-url="{link controller='ToDoEdit' id=$todo->todoID}{/link}"{/if}
	data-user-id="{$todo->userID}"
	data-object-type="de.mysterycode.wcf.toDo.toDo"
	data-like-liked="{if $todoLikeData !== null}{@$todoLikeData->liked}{/if}"
	data-like-likes="{if $todoLikeData !== null}{@$todoLikeData->likes}{else}0{/if}"
	data-like-dislikes="{if $todoLikeData !== null}{@$todoLikeData->dislikes}{else}0{/if}"
	data-like-users='{if $todoLikeData !== null}{ {implode from=$todoLikeData->getUsers() item=likeUser}"{@$likeUser->userID}": { "username": "{$likeUser->username|encodeJSON}" }{/implode} }{else}{ }{/if}'
	{if $todo->canEdit()}
		data-is-disabled="{if $todo->isDisabled}1{else}0{/if}"
		data-is-deleted="{if $todo->isDeleted}1{else}0{/if}"
		data-can-enable="{@$todo->canEnable()}"
		data-can-delete="{@$todo->canDelete()}"
		data-can-delete-completely="{@$todo->canDeleteCompletely()}"
		data-can-restore="{@$todo->canRestore()}"
	{/if}>
	
	<h1>{lang}wcf.toDo.task.detail{/lang}</h1>
	
	<nav class="jsMobileNavigation buttonGroupNavigation">
		<ul class="buttonGroup">{*
			*}{if $todo->canEdit()}<li><a class="button jsTodoInlineEditor jsOnly"><span class="icon icon16 icon-pencil"></span> <span>{lang}wcf.global.button.edit{/lang}</span></a></li>{/if}{*
			*}<li class="jsReportTodo jsOnly" data-object-id="{@$todo->todoID}"><a title="{lang}wcf.moderation.report.reportContent{/lang}" class="button jsTooltip"><span class="icon icon16 icon-warning-sign"></span> <span class="invisible">{lang}wcf.moderation.report.reportContent{/lang}</span></a></li>{*
			*}{if $todo->canParticipate() && $todo->statusID != 1}<li class="jsParticipateTodo jsOnly" data-object-id="{@$todo->todoID}" data-user-id="{$__wcf->user->userID}"><a title="{lang}wcf.toDo.task.participate{/lang}" class="button jsTooltip"><span class="icon icon16 icon-signin"></span> <span class="invisible">{lang}wcf.toDo.task.participate{/lang}</span></a></li>{/if}{*
			*}{if $todo->canEditProgress() && $todo->statusID != 1}<li class="updateProgress jsOnly" data-object-id="{@$todo->todoID}" data-user-id="{$__wcf->user->userID}"><a title="{lang}wcf.toDo.task.progress.update{/lang}" class="button jsTooltip"><span class="icon icon16 icon-refresh"></span> <span class="invisible">{lang}wcf.toDo.task.progress.update{/lang}</span></a></li>{/if}{*
			*}{if $todo->canEditStatus() && $todo->statusID != 1}<li class="jsMarkSolvedTodo jsOnly" data-object-id="{@$todo->todoID}" data-user-id="{$__wcf->user->userID}"><a title="{lang}wcf.toDo.task.solve{/lang}" class="button jsTooltip"><span class="icon icon16 icon-check"></span> <span class="invisible">{lang}wcf.toDo.task.solve{/lang}</span></a></li>{/if}{*
			*}{event name='buttons'}{*
		*}</ul>
	</nav>
</header>

{include file='userNotice'}

<div class="contentNavigation">
	{hascontent}
		<nav>
			<ul>
				{content}
					{event name='contentNavigationButtonsTop'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</div>

{assign var='objectID' value=$todo->todoID}
<div class="todoQuoteContainer" data-object-id="{@$todo->todoID}">
	<div class="container containerPadding marginTop todoContainer{if $todo->isDeleted} todoDeleted{/if}{if $todo->isDisabled} todoDisabled{/if}">
		{event name='beforeInfo'}
		<fieldset>
			<legend>{$todo->title}</legend>
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
							<div class="progressbar_inner" style="width:calc(100% - {$todo->progress}% + 2px);"></div>
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
				<dd><span class="icon icon-{if $todo->private == 0}un{/if}lock"></span></dd>
				
				{if $todo->timestamp > 0}
					<dt>{lang}wcf.toDo.task.submitTime{/lang}</dt>
					<dd>{@$todo->timestamp|time}</dd>
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
				<dd>{if $todo->submitter != 0 && $submitterusername != ''}<a href="{link controller='User' id=$todo->submitter}{/link}" class="userLink" data-user-id="{$todo->submitter}">{$submitterusername}</a>{else}{lang}wcf.user.guest{/lang}{/if}</dd>
				
				{if $todo->getResponsibles() && $todo->canViewResponsibleUsers()}
					<dt>{lang}wcf.toDo.task.responsible.users{/lang}</dt>
					<dd>
						<ul>
							{foreach from=$todo->getResponsibles() item=responsible}
								<li><a href="{link controller='User' object=$responsible}{/link}" class="userLink" data-user-id="{$responsible->userID}">{$responsible->username}</a></li>
							{/foreach}
						</ul>
					</dd>
				{/if}
				
				{if $todo->getResponsibleGroups() && $todo->canViewResponsibleGroups()}
					<dt>{lang}wcf.toDo.task.responsible.groups{/lang}</dt>
					<dd>
						<ul>
							{foreach from=$todo->getResponsibleGroups() item=responsible}
								<li>{@'%s'|str_replace:$responsible->getName():$responsible->userOnlineMarking}</li>
							{/foreach}
						</ul>
					</dd>
				{/if}
			</dl>
			{event name='additionalInfo'}
		</fieldset>
	</div>
	{if $todo->description != ''}
		<div class="container containerPadding marginTop todoContainer todoDescription">
			<fieldset>
				<legend>{lang}wcf.toDo.task.description{/lang}</legend>
				<div>
					{@$todo->getFormattedDescription()}
				</div>
			</fieldset>
			{include file='attachments'}
		</div>
	{/if}
	{if $todo->note != ''}
		<div class="container containerPadding marginTop todoContainer todoNotes">
			<fieldset>
				<legend>{lang}wcf.toDo.task.note{/lang}</legend>
				<div>
					{@$todo->getFormattedNote()}
				</div>
			</fieldset>
		</div>
	{/if}
</div>

{event name='additionalContainer'}
{if TODO_COMMENTS_ENABLE}
	<div class="container containerPadding marginTop todoContainer" id="comments">
		<fieldset>
			<legend>{lang}wcf.toDo.comments{/lang} <span class="badge">{@$commentList->countObjects()}</span></legend>
			{include file='toDoCommentList'}
		</fieldset>
	</div>
{/if}

<div class="contentNavigation">
	{hascontent}
		<nav>
			<ul>
				{content}
					{event name='contentNavigationButtonsBottom'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</div>

{include file='footer'}

</body>
</html>

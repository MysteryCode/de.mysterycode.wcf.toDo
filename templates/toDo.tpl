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
				'wcf.todo.confirmDelete': '{lang}wcf.todo.confirmDelete{/lang}',
				'wcf.todo.confirmTrash': '{lang}wcf.todo.confirmTrash{/lang}',
				'wcf.todo.confirmTrash.reason': '{lang}wcf.todo.confirmTrash.reason{/lang}',
				'wcf.todo.edit': '{lang}wcf.toDo.edit{/lang}',
				'wcf.todo.edit.delete': '{lang}wcf.toDo.edit.delete{/lang}',
				'wcf.todo.edit.disable': '{lang}wcf.toDo.edit.disable{/lang}',
				'wcf.todo.edit.enable': '{lang}wcf.toDo.edit.enable{/lang}',
				'wcf.todo.edit.restore': '{lang}wcf.toDo.edit.restore{/lang}',
				'wcf.todo.edit.trash': '{lang}wcf.toDo.edit.trash{/lang}',
				'wcf.moderation.report.reportContent': '{lang}wcf.moderation.report.reportContent{/lang}',
				'wcf.moderation.report.success': '{lang}wcf.moderation.report.success{/lang}'
			});
			
			var $updateHandler = new WCF.Todo.UpdateHandler.Todo();
			var $inlineEditor = new WCF.Todo.InlineEditor('.todo');
			$inlineEditor.setUpdateHandler($updateHandler);
			$inlineEditor.setEnvironment('todo', {@$todo->id}, '{$todo->getLink()}');
			$inlineEditor.setPermissions({
				canEnableTodo: {if $todo->canEnable()}1{else}0{/if},
				canDeleteTodo: {if $todo->canDelete()}1{else}0{/if},
				canDeleteTodoCompletely: {if $todo->canDeleteCompletely()}1{else}0{/if},
				canRestoreTodo: {if $todo->canRestore()}1{else}0{/if}
			});

			new WCF.Moderation.Report.Content('de.mysterycode.wcf.toDo.toDo', '.jsReportTodo');

			{if $todo->isDisabled || $todo->isDeleted}
				$('.sidebar').addClass('{if $todo->isDeleted}deleted{else}disabled{/if}');
			{/if}

			{event name='javascriptInit'}
		});
		//]]>
	</script>
</head>

<body id="tpl{$templateName|ucfirst}">

{capture assign='headerNavigation'}
	
{/capture}

{if $__boxSidebar|isset && $__boxSidebar}
	{capture assign='sidebar'}
		{@$__boxSidebar}
	{/capture}
{/if}

{include file='header' sidebarOrientation='right'}

<header class="boxHeadline todo" data-todo-id="{@$todo->id}"{if $todo->canEdit()} data-can-edit="{if $todo->canEdit()}1{else}0{/if}" data-edit-url="{link controller='ToDoEdit' id=$todo->id}{/link}"{/if}  data-user-id="{@$todo->submitter}"
	{if $todo->canEdit()}
		data-is-disabled="{if $todo->isDisabled}1{else}0{/if}" data-is-deleted="{if $todo->isDeleted}1{else}0{/if}"
		data-can-enable="{@$todo->canEnable()}" data-can-delete="{@$todo->canDelete()}" data-can-delete-completely="{@$todo->canDeleteCompletely()}" data-can-restore="{@$todo->canRestore()}"
	{/if}>
	
	<h1>{lang}wcf.toDo.task.detail{/lang}</h1>
	
	<nav class="jsMobileNavigation buttonGroupNavigation">
		<ul class="buttonGroup">{*
			*}{if $todo->canEdit()}<li><a class="button jsTodoInlineEditor jsOnly"><span class="icon icon16 icon-pencil"></span> <span>{lang}wcf.global.button.edit{/lang}</span></a></li>{/if}{*
			*}<li class="jsReportTodo jsOnly" data-object-id="{@$todo->id}"><a title="{lang}wcf.moderation.report.reportContent{/lang}" class="button jsTooltip"><span class="icon icon16 icon-warning-sign"></span> <span class="invisible">{lang}wcf.moderation.report.reportContent{/lang}</span></a></li>{*
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

<div class="container containerPadding marginTop toDoContainer">
	{event name='beforeInfo'}
	<fieldset>
		<legend>{$todo->title}</legend>
		<dl>
			<dt>{lang}wcf.toDo.task.title{/lang}</dt>
			<dd>{$todo->title}</dd>
			{if TODO_CATEGORY_ENABLE}
				<dt>{lang}wcf.toDo.category{/lang}</dt>
				<dd>
					{if $todo->categorytitle != ''}
						<a href="{link controller='ToDoCategory' id=$todo->category}{/link}"><span class="label badge {$todo->categorycolor}">{$todo->categorytitle}</span></a>
					{else}
						<span class="label badge {$todo->categorycolor}">{$todo->categorytitle}</span>
					{/if}
				</dd>
			{/if}
			<dt>{lang}wcf.toDo.task.status{/lang}</dt>
			<dd>
				{if $__wcf->getSession()->getPermission('user.toDo.status.canView')}
					{if $todo->status == 1}
						<span class="label badge orange unsolvedBadge">{lang}wcf.toDo.task.unsolved{/lang}</span>
					{elseif $todo->status == 2}
						<span class="label badge yellow workBadge">{lang}wcf.toDo.task.work{/lang}</span>
					{elseif $todo->status == 3}
						<span class="label badge green solvedbadge">{lang}wcf.toDo.task.solved{/lang}</span>
					{elseif $todo->status == 4}
						<span class="label badge gray canceledBadge">{lang}wcf.toDo.task.canceled{/lang}</span>
					{elseif $todo->status == 5}
						<span class="label badge gray pendingBadge">{lang}wcf.toDo.task.preparation{/lang}</span>
					{elseif $todo->status == 6}
						<span class="label badge gray pausedBadge">{lang}wcf.toDo.task.paused{/lang}</span>
					{/if}
				{/if}
			</dd>
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
				{if $todo->priority == 0}<span class="label badge green">{lang}wcf.toDo.task.priority.low{/lang}</span>{/if}
				{if $todo->priority == 1}<span class="label badge grey">{lang}wcf.toDo.task.priority.normal{/lang}</span>{/if}
				{if $todo->priority == 2}<span class="label badge red">{lang}wcf.toDo.task.priority.high{/lang}</span>{/if}
			</dd>
			<dt>{lang}wcf.toDo.task.privacy{/lang}</dt>
			<dd>{if $todo->private == 1}{lang}wcf.toDo.task.privacy.private{/lang}{else}{lang}wcf.toDo.task.privacy.public{/lang}{/if}</dd>
			{if $todo->timestamp > 0}
				<dt>{lang}wcf.toDo.task.submitTime{/lang}</dt>
				<dd>{@$todo->timestamp|time}</dd>
			{/if}
			{if $todo->endTime > 0}
				<dt>{lang}wcf.toDo.task.endTime{/lang}</dt>
				<dd>{@$todo->endTime|time}</dd>
			{/if}
			{if $todo->remembertime > 0}
				<dt>{lang}wcf.toDo.task.remembertime{/lang}</dt>
				<dd>{@$todo->remembertime|date}</dd>
			{/if}
			<dt>{lang}wcf.toDo.task.submitter{/lang}</dt>
			<dd>{if $todo->submitter != 0 && $submitterusername != ''}<a href="{link controller='User' id=$todo->submitter}{/link}" class="userLink" data-user-id="{$todo->submitter}">{$submitterusername}</a>{else}{lang}wcf.user.guest{/lang}{/if}</dd>
			{if $todo->getResponsibles() && $__wcf->getSession()->getPermission('user.toDo.responsible.canView')}
				<dt>{lang}wcf.toDo.task.responsible{/lang}</dt>
				<dd>
					<ul>
						{foreach from=$todo->getResponsibles() item=responsible}
							<li><a href="{link controller='User' object=$responsible}{/link}" class="userLink" data-user-id="{$responsible->userID}">{$responsible->username}</a></li>
						{/foreach}
					</ul>
				</dd>
			{/if}
		</dl>
		{event name='additionalInfo'}
	</fieldset>
</div>
{if $todo->description != ''}
	<div class="container containerPadding marginTop toDoContainer">
		<fieldset>
			<legend>{lang}wcf.toDo.task.description{/lang}</legend>
			{@$todo->getFormattedDescription()}
		</fieldset>
	</div>
{/if}
{if $todo->note != ''}
	<div class="container containerPadding marginTop toDoContainer">
		<fieldset>
			<legend>{lang}wcf.toDo.task.note{/lang}</legend>
			{@$todo->getFormattedNote()}
		</fieldset>
	</div>
{/if}

{event name='additionalContainer'}

{if TODO_COMMENTS_ENABLE}
	<div class="container containerPadding marginTop toDoContainer" id="comments">
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
{include file='wysiwyg'}

</body>
</html>
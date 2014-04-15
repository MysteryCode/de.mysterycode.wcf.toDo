{include file='documentHeader'}

<head>
	<title>{lang}wcf.toDo.task.detail{/lang} - {PAGE_TITLE|language}</title>
	
	{include file='headInclude'}
	
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new WCF.Search.User('#responsibles', null, false, [ ], true);
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

<header class="boxHeadline">
	<h1>{lang}wcf.toDo.task.detail{/lang}</h1>
</header>

{include file='userNotice'}

<div class="contentNavigation">
	{hascontent}
		<nav>
			<ul>
				{content}
					{if $__wcf->getSession()->getPermission('user.toDo.toDo.canEdit')}<li><a href="{link controller='ToDoEdit' id=$todo->id}{/link}" title="{lang}wcf.toDo.task.edit{/lang}" class="button"><span class="icon icon16 icon-pencil"></span> <span>{lang}wcf.toDo.task.edit{/lang}</span></a></li>{/if}
					<li><a href="{link controller='ToDoList'}{/link}" title="{lang}wcf.toDo.task.list{/lang}" class="button"><span class="icon icon16 icon-reorder"></span> <span>{lang}wcf.toDo.task.list{/lang}</span></a></li>
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
						<span class="label badge red unsolvedBadge">{lang}wcf.toDo.task.unsolved{/lang}</span>
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
			<dd>{if $todo->important == 1}<span class="label badge red">{lang}wcf.toDo.task.priority.high{/lang}</span>{else}<span class="label badge gray">{lang}wcf.toDo.task.priority.normal{/lang}</span>{/if}</dd>
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
			{if TODO_DESCRIPTION_HTML_ENABLE && $todo->html_description == 1}{@$todo->description}{else}{@$todo->description|newlineToBreak}{/if}
		</fieldset>
	</div>
{/if}
{if $todo->note != ''}
	<div class="container containerPadding marginTop toDoContainer">
		<fieldset>
			<legend>{lang}wcf.toDo.task.note{/lang}</legend>
			{if TODO_NOTE_HTML_ENABLE && $todo->html_notes == 1}{@$todo->note}{else}{@$todo->note|newlineToBreak}{/if}
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
					{if $__wcf->getSession()->getPermission('user.toDo.toDo.canEdit')}<li><a href="{link controller='ToDoEdit' id=$todo->id}{/link}" title="{lang}wcf.toDo.task.edit{/lang}" class="button"><span class="icon icon16 icon-pencil"></span> <span>{lang}wcf.toDo.task.edit{/lang}</span></a></li>{/if}
					<li><a href="{link controller='ToDoList'}{/link}" title="{lang}wcf.toDo.task.list{/lang}" class="button"><span class="icon icon16 icon-reorder"></span> <span>{lang}wcf.toDo.task.list{/lang}</span></a></li>
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
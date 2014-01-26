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
{include file='header'}

<header class="boxHeadline">
	<h1>{lang}wcf.toDo.task.detail{/lang}</h1>
</header>

{include file='userNotice'}

<div class="contentNavigation">
	{hascontent}
		<nav>
			<ul>
				{content}
					{if $__wcf->getSession()->getPermission('user.toDo.toDo.canEdit')}<li><a href="{link controller='ToDoEdit' id=$toDo.id}{/link}" title="{lang}wcf.toDo.task.edit{/lang}" class="button"><span class="icon icon16 icon-pencil"></span> <span>{lang}wcf.toDo.task.edit{/lang}</span></a></li>{/if}
					<li><a href="{link controller='ToDoList'}{/link}" title="{lang}wcf.toDo.task.list{/lang}" class="button"><span class="icon icon16 icon-reorder"></span> <span>{lang}wcf.toDo.task.list{/lang}</span></a></li>
					{event name='contentNavigationButtonsTop'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</div>

<div class="container containerPadding marginTop toDoContainer">
	<fieldset>
		<legend>{$toDo.title}</legend>
		<dl>
			<dt>{lang}wcf.toDo.task.title{/lang}</dt>
			<dd>{$toDo.title}</dd>
			{if TODO_CATEGORY_ENABLE}
				<dt>{lang}wcf.toDo.category{/lang}</dt>
				<dd><span class="label badge {$categorycolor}">{$categoryname}</span></dd>
			{/if}
			<dt>{lang}wcf.toDo.task.status{/lang}</dt>
			<dd>
				{if $toDo.status == 1}
					<span class="label badge red">{lang}wcf.toDo.task.unsolved{/lang}</span>
				{elseif $toDo.status == 2}
					<span class="label badge yellow">{lang}wcf.toDo.task.work{/lang}</span>
				{elseif $toDo.status == 3}
					<span class="label badge green">{lang}wcf.toDo.task.solved{/lang}</span>
				{elseif $toDo.status == 4}
					<span class="label badge gray">{lang}wcf.toDo.task.canceled{/lang}</span>
				{else}
					<span class="label badge red">{lang}wcf.toDo.task.error{/lang}</span>
				{/if}
			</dd>
			<dt>{lang}wcf.toDo.task.priority{/lang}</dt>
			<dd>{if $toDo.important == 1}<span class="label badge red">{lang}wcf.toDo.task.priority.high{/lang}</span>{else}<span class="label badge gray">{lang}wcf.toDo.task.priority.normal{/lang}</span>{/if}</dd>
			<dt>{lang}wcf.toDo.task.privacy{/lang}</dt>
			<dd>{if $toDo.private == 1}{lang}wcf.toDo.task.privacy.private{/lang}{else}{lang}wcf.toDo.task.privacy.public{/lang}{/if}</dd>
			{if $toDo.timestamp > 0}
				<dt>{lang}wcf.toDo.task.submitTime{/lang}</dt>
				<dd>{@$toDo.timestamp|time}</dd>
			{/if}
			{if $toDo.endTime > 0}
				<dt>{lang}wcf.toDo.task.endTime{/lang}</dt>
				<dd>{@$toDo.endTime|time}</dd>
			{/if}
			<dt>{lang}wcf.toDo.task.submitter{/lang}</dt>
			<dd>{if $toDo.submitter != 0}<a href="{link controller='User' id=$toDo.submitter}{/link}" class="userLink" data-user-id="{$toDo.submitter}">{$submitterusername}</a>{else}{lang}wcf.user.guest{/lang}{/if}</dd>
			{if $responsibles && $__wcf->getSession()->getPermission('user.toDo.responsible.canView')}
				<dt>{lang}wcf.toDo.task.responsible{/lang}</dt>
				<dd>
					<ul>
						{foreach from=$responsibles item=responsible}
							<li><a href="{link controller='User' id=$responsible.id}{/link}" class="userLink" data-user-id="{$responsible.id}">{$responsible.username}</a></li>
						{/foreach}
					</ul>
				</dd>
			{/if}
		</dl>
	</fieldset>
</div>
{if $toDo.description != ''}
	<div class="container containerPadding marginTop toDoContainer">
		<fieldset>
			<legend>{lang}wcf.toDo.task.description{/lang}</legend>
			{if TODO_DESCRIPTION_HTML_ENABLE}{@$toDo.description}{else}{$toDo.description}{/if}
		</fieldset>
	</div>
{/if}
{if $toDo.note != ''}
	<div class="container containerPadding marginTop toDoContainer">
		<fieldset>
			<legend>{lang}wcf.toDo.task.note{/lang}</legend>
			{if TODO_NOTE_HTML_ENABLE}{@$toDo.note}{else}{$toDo.note}{/if}
		</fieldset>
	</div>
{/if}

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
					{if $__wcf->getSession()->getPermission('user.toDo.toDo.canEdit')}<li><a href="{link controller='ToDoEdit' id=$toDo.id}{/link}" title="{lang}wcf.toDo.task.edit{/lang}" class="button"><span class="icon icon16 icon-pencil"></span> <span>{lang}wcf.toDo.task.edit{/lang}</span></a></li>{/if}
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
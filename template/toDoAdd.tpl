{include file='documentHeader'}

<head>
	<title>{lang}wcf.toDo.task.{@$action}{/lang} - {PAGE_TITLE|language}</title>
	
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
	<h1>{lang}wcf.toDo.task.edit{/lang}</h1>
</header>

{include file='userNotice'}

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	{hascontent}
		<nav>
			<ul>
				{content}
					<li><a href="{link controller='ToDoList'}{/link}" title="{lang}wcf.toDo.task.list{/lang}" class="button"><span class="icon icon16 icon-reorder"></span> <span>{lang}wcf.toDo.task.list{/lang}</span></a></li>
					{event name='contentNavigationButtonsTop'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</div>

<form class="jsFormGuard toDoContainer" method="post" action="{if $action=='edit'}{link controller='ToDoEdit' id=$id}{/link}{else}{link controller='ToDoAdd'}{/link}{/if}">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.toDo.task.general{/lang}</legend>
			
			<dl{if $errorField == 'title'} class="formError"{/if}>
				<dt><label for="title">{lang}wcf.toDo.task.title{/lang}</label></dt>
				<dd>
					<input type="text" id="title" name="title" value="{$title}" required="required" maxlength="255" class="long" />
					{if $errorField == 'title'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			
			{if TODO_CATEGORY_ENABLE}
				<dl{if $errorField == 'category'} class="formError"{/if}>
					<dt><label for="category">{lang}wcf.toDo.category{/lang}</label></dt>
					<dd>
						{if $categoryList}
							<select id="category" name="category">
								<option value="" {if $category == 0}selected{/if}>{lang}wcf.toDo.category.choose{/lang}</option>
								<option value="" >{lang}wcf.toDo.category.placeholder{/lang}</option>
								{foreach from=$categoryList item=item}
									<option value="{$item.id}" {if $category == $item.id}selected{/if}>{$item.title}</option>
								{/foreach}
								
							</select>
						{/if}
						<input type="text" id="newCategory" name="newCategory" value="" maxlength="255" class="long" />
						{if $errorField == 'category'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{/if}
							</small>
						{/if}
					</dd>
				</dl>
			{/if}
			
			{if TODO_SET_STATUS_ON_CREATE || $action == 'edit'}
				<dl{if $errorField == 'status'} class="formError"{/if}>
					<dt><label for="status">{lang}wcf.toDo.task.status{/lang}</label></dt>
					<dd>
						<select id="status" name="status" required="required">
							<option value="" {if $category == 0}selected{/if}>{lang}wcf.toDo.status.choose{/lang}</option>
							<option value="" >{lang}wcf.toDo.status.placeholder{/lang}</option>
							<option value="1" {if $status==1}selected="selected"{/if}>{lang}wcf.toDo.task.unsolved{/lang}</option>
							<option value="2" {if $status==2}selected="selected"{/if}>{lang}wcf.toDo.task.work{/lang}</option>
							<option value="3" {if $status==3}selected="selected"{/if}>{lang}wcf.toDo.task.solved{/lang}</option>
							<option value="4" {if $status==4}selected="selected"{/if}>{lang}wcf.toDo.task.canceled{/lang}</option>
						</select>
						{if $errorField == 'status'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{/if}
							</small>
						{/if}
					</dd>
				</dl>
			{/if}
			
			<dl{if $errorField == 'endTime'} class="formError"{/if}>
				<dt><label for="endTime">{lang}wcf.toDo.task.endTime{/lang} <small>{lang}wcf.toDo.task.optional{/lang}</small></label></dt>
				<dd>
					<input type="datetime" id="endTime" name="endTime" value="{if $endTime > 0}{$endTime}{/if}" />
				</dd>
			</dl>
			
			<dl{if $errorField == 'private'} class="formError"{/if}>
				<dt><label for="private">{lang}wcf.toDo.task.private{/lang}</label></dt>
				<dd>
					<input type="checkbox" id="private" name="private" {if $private == 'on'}checked{/if} />
				</dd>
			</dl>
			
			<dl{if $errorField == 'important'} class="formError"{/if}>
				<dt><label for="important">{lang}wcf.toDo.task.important{/lang}</label></dt>
				<dd>
					<input type="checkbox" id="important" name="important" {if $important == 'on'}checked{/if} />
				</dd>
			</dl>
			
			<dl{if $errorField == 'description'} class="formError"{/if}>
				<dt><label for="description">{lang}wcf.toDo.task.description{/lang}</label></dt>
				<dd>
					<textarea id="description" name="description" rows="10" cols="10">{$description}</textarea>
					{if $errorField == 'description'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
		</fieldset>
		
		{if $__wcf->getSession()->getPermission('user.toDo.responsible.canEdit')}
			<fieldset>
				<legend>{lang}wcf.toDo.task.responsible{/lang} <small>{lang}wcf.toDo.task.optional{/lang}</small></legend>
				
				<dl{if $errorField == 'responsibles'} class="formError"{/if}>
					<dt><label for="responsibles">{lang}wcf.toDo.task.responsible{/lang}</label></dt>
					<dd>
						<textarea id="responsibles" name="responsibles" class="long" cols="20" rows="2">{if $responsibles && $action == 'edit'}{$responsibles}{/if}{if $responsibles && $action == 'edit'}{foreach from=$responsibles item=$responsible}{$responsible.username}{if !$responsible.isLast}, {/if}{/foreach}{/if}</textarea>
					</dd>
				</dl>
			</fieldset>
		{/if}
		
		<fieldset>
			<legend>{lang}wcf.toDo.task.note{/lang} <small>{lang}wcf.toDo.task.optional{/lang}</small></legend>
			
			<dl{if $errorField == 'note'} class="formError"{/if}>
				<dt><label for="note">{lang}wcf.toDo.task.note{/lang}</label></dt>
				<dd>
					<textarea id="note" name="note" rows="10" cols="20">{$note}</textarea>
				</dd>
			</dl>
		</fieldset>
		
		{event name='fieldsets'}
		
	</div>
	
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

<div class="contentNavigation">
	{hascontent}
		<nav>
			<ul>
				{content}
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
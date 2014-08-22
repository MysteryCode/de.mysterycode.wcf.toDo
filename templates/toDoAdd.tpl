{include file='documentHeader'}

<head>
	<title>{lang}wcf.toDo.task.{@$action}{/lang} - {PAGE_TITLE|language}</title>
	
	{include file='headInclude'}
	
	
	<script data-relocate="true">
		{if $canEditResponsible}
			//<![CDATA[
			$(function() {
				new WCF.Search.User('#responsibles', null, false, [ ], true);
			});
			//]]>
		{/if}
		//<![CDATA[
		$(function() {	
			WCF.Message.Submit.registerButton('text', $('#messageContainer > .formSubmit > input[type=submit]'));
			new WCF.Message.FormGuard();
			
			WCF.System.Dependency.Manager.register('CKEditor', function() { new WCF.Message.UserMention('text'); });
		});
		//]]>
	</script>
</head>

<body id="tpl{$templateName|ucfirst}">
{include file='header'}

<header class="boxHeadline">
	<h1>{lang}wcf.toDo.task.{@$action}{/lang}</h1>
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

<form id="messageContainer" class="jsFormGuard toDoContainer" method="post" action="{if $action=='edit'}{link controller='ToDoEdit' id=$id}{/link}{else}{link controller='ToDoAdd'}{/link}{/if}">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.toDo.task.general{/lang}</legend>
			
			{if $action == 'add' || ($action == 'edit' && $todo->canEdit())}
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
				
				{if (TODO_SET_STATUS_ON_CREATE || $action == 'edit') && $canEditStatus}
					<dl{if $errorField == 'status'} class="formError"{/if}>
						<dt><label for="status">{lang}wcf.toDo.task.status{/lang}</label></dt>
						<dd>
							<select id="status" name="status" required="required">
								<option value="" {if $toDoCategory == 0}selected{/if}>{lang}wcf.toDo.status.choose{/lang}</option>
								<option value="" >{lang}wcf.toDo.status.placeholder{/lang}</option>
								<option value="5" {if $status==5}selected="selected"{/if}>{lang}wcf.toDo.task.preparation{/lang}</option>
								<option value="1" {if $status==1}selected="selected"{/if}>{lang}wcf.toDo.task.unsolved{/lang}</option>
								<option value="2" {if $status==2}selected="selected"{/if}>{lang}wcf.toDo.task.work{/lang}</option>
								<option value="3" {if $status==3}selected="selected"{/if}>{lang}wcf.toDo.task.solved{/lang}</option>
								<option value="4" {if $status==4}selected="selected"{/if}>{lang}wcf.toDo.task.canceled{/lang}</option>
								<option value="6" {if $status==6}selected="selected"{/if}>{lang}wcf.toDo.task.paused{/lang}</option>
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
				
				<dl{if $errorField == 'important'} class="formError"{/if}>
					<dt></dt>
					<dd>
						<label for="important"><input type="checkbox" id="important" name="important" {if $important == '1'}checked{/if} /> {lang}wcf.toDo.task.important{/lang}</label>
					</dd>
				</dl>
				
				<dl{if $errorField == 'private'} class="formError"{/if}>
					<dt></dt>
					<dd>
						<label for="private"><input type="checkbox" id="private" name="private" {if $private == '1'}checked{/if} /> {lang}wcf.toDo.task.private{/lang}</label>
					</dd>
				</dl>
				
				{if TODO_CATEGORY_ENABLE}
					<dl{if $errorField == 'category'} class="formError"{/if}>
						<dt><label for="category">{lang}wcf.toDo.category{/lang}</label></dt>
						<dd>
							{if $toDoCategoryList}
								<select id="category" name="category">
									<option value="" {if $toDoCategory == 0}selected{/if}>{lang}wcf.toDo.category.choose{/lang}</option>
									<option value="" >{lang}wcf.toDo.category.placeholder{/lang}</option>
									{foreach from=$toDoCategoryList item=item}
										<option value="{$item.id}" {if $toDoCategory == $item.id}selected{/if}>{$item.title}</option>
									{/foreach}
								</select>
							{/if}
							{if $__wcf->getSession()->getPermission('user.toDo.toDo.canAddCategory')}
								<input type="text" id="newCategory" name="newCategory" value="" maxlength="255" class="small" />
							{/if}
							{if !$toDoCategoryList && !$__wcf->getSession()->getPermission('user.toDo.toDo.canAddCategory')}
								{lang}wcf.toDo.category.noCategory{/lang}
							{/if}
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
				
				{if TODO_PROGRESS_ENABLE}
					<dl{if $errorField == 'progress'} class="formError"{/if}>
						<dt><label for="progress">{lang}wcf.toDo.task.progress{/lang}</label></dt>
						<dd>
							<input type="text" id="progress" name="progress" value="{$progress}" maxlength="3" class="long" />
						</dd>
					</dl>
				{/if}
				
				<dl{if $errorField == 'endTime'} class="formError"{/if}>
					<dt><label for="endTime">{lang}wcf.toDo.task.endTime{/lang} <small>{lang}wcf.toDo.task.optional{/lang}</small></label></dt>
					<dd>
						<input type="datetime" id="endTime" name="endTime" value="{if $endTime > 0}{$endTime}{/if}" />
					</dd>
				</dl>
				
				<dl{if $errorField == 'remembertime'} class="formError"{/if}>
					<dt><label for="remembertime">{lang}wcf.toDo.task.remembertime{/lang} <small>{lang}wcf.toDo.task.optional{/lang}</small></label></dt>
					<dd>
						<input type="date" id="remembertime" name="remembertime" value="{if $remembertime > 0}{$remembertime}{/if}" />
					</dd>
				</dl>
			{/if}
		</fieldset>
		
		{if $canEditResponsible}
			<fieldset>
				<legend>{lang}wcf.toDo.task.responsible{/lang} <small>{lang}wcf.toDo.task.optional{/lang}</small></legend>
				
				<dl{if $errorField == 'responsibles'} class="formError"{/if}>
					<dt><label for="responsibles">{lang}wcf.toDo.task.responsible{/lang}</label></dt>
					<dd>
						<textarea id="responsibles" name="responsibles" class="long" cols="40" rows="4">{$responsibles}</textarea>
					</dd>
				</dl>
			</fieldset>
		{/if}
		
		{if $action == 'add' || ($action == 'edit' && $todo->canEdit())}
			<fieldset>
				<legend>{lang}wcf.global.description{/lang}</legend>
				
				<dl class="wide{if $errorField == 'description'} formError{/if}">
					<dt><label>{lang}wcf.toDo.task.description{/lang}</label></dt>
					<dd>
						<textarea id="text" name="description" rows="20" cols="40">{$description}</textarea>
						{if $errorField == 'description'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{/if}
							</small>
						{/if}
					</dd>
				</dl>
				
				{event name='messageFields'}
			</fieldset>
			
			{include file='messageFormTabs' wysiwygContainerID='text'}
			
			<fieldset>
				<legend>{lang}wcf.toDo.task.note{/lang} <small>{lang}wcf.toDo.task.optional{/lang}</small></legend>
				<dl class="wide{if $errorField == 'note'} formError{/if}">
					<dt><label>{lang}wcf.toDo.task.note{/lang}</label></dt>
					<dd>
						<textarea id="note" name="note" rows="10" cols="20">{$note}</textarea>
					</dd>
				</dl>
			</fieldset>
		{/if}
		
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
{include file='wysiwyg' wysiwygSelector='text'}

</body>
</html>
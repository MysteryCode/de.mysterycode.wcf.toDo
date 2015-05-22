{include file='documentHeader'}

<head>
	<title>{lang}wcf.toDo.task.{@$action}{/lang} - {PAGE_TITLE|language}</title>
	
	{include file='headInclude'}
	
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			{if $canEditResponsible}
				new WCF.Search.User('#responsibles', null, false, [ ], true);
			{/if}
			
			{include file='__messageQuoteManager' wysiwygSelector='text' supportPaste=true}
			new WCF.Conversation.Message.QuoteHandler($quoteManager);
			
			WCF.Message.Submit.registerButton('text', $('#messageContainer > .formSubmit > input[type=submit]'));
			new WCF.Message.FormGuard();
			
			WCF.System.Dependency.Manager.register('CKEditor', function() { new WCF.Message.UserMention('text'); });
		});
		
		$('#prioPicker span').on('click', function(event) {
			$(event.currentTarget).parent().children().css("font-weight","normal").css('text-decoration', 'none');
			$(event.currentTarget).css("font-weight","bolder").css('text-decoration', 'underline');
			$('#priority').val(WCF.String.escapeHTML($(event.currentTarget).data('prio')));
		});
		
		{event name='javascriptInit'}
		//]]>
	</script>
</head>

<body id="tpl_{$templateNameApplication}_{$templateName}" data-template="{$templateName}" data-application="{$templateNameApplication}">
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
	{if $action == 'edit'}{assign var='canEdit' value=$todo->canEdit()}{/if}
	
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.toDo.task.general{/lang}</legend>
			
			{if $action == 'add' || ($action == 'edit' && $canEdit)}
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
			{/if}
			
			{if (TODO_SET_STATUS_ON_CREATE || $action == 'edit') && $canEditStatus}
				<dl{if $errorField == 'statusID'} class="formError"{/if}>
					<dt><label for="statusID">{lang}wcf.toDo.task.status{/lang}</label></dt>
					<dd>
						<select id="statusID" name="statusID" required="required">
							<option value="" {if !$statusID}selected{/if}>{lang}wcf.toDo.status.choose{/lang}</option>
							<option value="" >{lang}wcf.toDo.status.placeholder{/lang}</option>
							{foreach from=$statusList item=status}
								<option value="{$status->statusID}" {if $statusID == $status->statusID}selected="selected"{/if}>{$status->getTitle()}</option>
							{/foreach}
						</select>
						{if $errorField == 'statusID'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{/if}
							</small>
						{/if}
					</dd>
				</dl>
			{/if}
			
			{if $action == 'add' || ($action == 'edit' && $canEdit)}
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
								<input type="text" id="newCategory" name="newCategory" value="" maxlength="255" class="long" />
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
			{/if}
			
			{if $action == 'add' || ($action == 'edit' && $canEdit)}
				{if TODO_PROGRESS_ENABLE}
					<dl{if $errorField == 'progress'} class="formError"{/if}>
						<dt><label for="progress">{lang}wcf.toDo.task.progress{/lang}</label></dt>
						<dd>
							<input type="number" id="progress" name="progress" value="{$progress}" min="0" max="100" maxlength="3" class="medium" />
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
				
				<dl{if $errorField == 'priority'} class="formError"{/if}>
					<dt><label for="priority">{lang}wcf.toDo.task.important{/lang}</label></dt>
					<dd id="prioPicker">
						<span data-prio="3" class="badge label grey"{if $important == 3} style="font-weight: bolder;"{/if}>{lang}wcf.toDo.task.priority.low{/lang}</span>
						<span data-prio="2" class="badge label blue"{if $important == 2} style="font-weight: bolder;"{/if}>{lang}wcf.toDo.task.priority.normal{/lang}</span>
						<span data-prio="1" class="badge label red"{if $important == 1} style="font-weight: bolder;"{/if}>{lang}wcf.toDo.task.priority.high{/lang}</span> 
						<input type="hidden" id="priority" name="priority" value="{$important}" />
					</dd>
				</dl>
				
				<dl{if $errorField == 'private'} class="formError"{/if}>
					<dt><label for="private">{lang}wcf.toDo.task.private{/lang}</label></dt>
					<dd>
						<input type="checkbox" id="private" name="private" {if $private == '1'}checked{/if} />
					</dd>
				</dl>
				
				{event name='additionalFields'}
			{/if}
		</fieldset>
		
		{if $action == 'add' || ($action == 'edit' && $canEdit)}
			<fieldset>
				<dl{if $errorField == 'text'} class="formError"{/if}>
					<dt><label for="text">{lang}wcf.global.description{/lang}</label></dt>
					<dd>
						<textarea id="text" name="description" rows="20" cols="40">{$description}</textarea>
						{include file='messageFormTabs' wysiwygContainerID='text'}
						{if $errorField == 'description'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{elseif $errorType == 'tooLong'}
									{lang}wcf.message.error.tooLong{/lang}
								{elseif $errorType == 'censoredWordsFound'}
									{lang}wcf.message.error.censoredWordsFound{/lang}
								{elseif $errorType == 'disallowedBBCodes'}
									{lang}wcf.message.error.disallowedBBCodes{/lang}
								{/if}
							</small>
						{/if}
					</dd>
				</dl>
				
				{event name='messageFields'}
			</fieldset>
		{/if}
		
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
				<legend>{lang}wcf.toDo.task.note{/lang} <small>{lang}wcf.toDo.task.optional{/lang}</small></legend>
				
				<dl{if $errorField == 'note'} class="formError"{/if}>
					<dt><label for="note">{lang}wcf.toDo.task.note{/lang}</label></dt>
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
		{include file='messageFormPreviewButton'}
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
{include file='wysiwyg' wysiwygSelector='note'}

</body>
</html>
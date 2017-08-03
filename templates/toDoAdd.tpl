{include file='documentHeader'}

<head>
	<title>{lang}wcf.toDo.task.{@$action}{/lang} - {PAGE_TITLE|language}</title>
	
	{include file='headInclude'}
	
	<script data-relocate="true" src="{@$__wcf->getPath()}js/WCF.Todo{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@$__wcfVersion}"></script>
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new WCF.Search.User('#responsibles', null, false, [ ], true);
			new WCF.Todo.Search.User('#responsibleGroups', null, [ ], true);
			
			{include file='__messageQuoteManager' wysiwygSelector='text' supportPaste=true}
			new WCF.Todo.QuoteHandler($quoteManager);
			
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

<form id="messageContainer" class="jsFormGuard toDoContainer" method="post" action="{if $action=='edit'}{link controller='ToDoEdit' id=$id}{/link}{else}{link controller='ToDoAdd' id=$categoryID}{/link}{/if}">
	{if $action == 'edit'}{assign var='canEdit' value=$todo->canEdit()}{/if}
	
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
			
			{if ($action == 'add' && $category->canEditStatus()) || ($action == 'edit' && $todo->canEditStatus())}
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
			
			{if TODO_PROGRESS_ENABLE}
				<dl{if $errorField == 'progress'} class="formError"{/if}>
					<dt><label for="progress">{lang}wcf.toDo.task.progress{/lang}</label></dt>
					<dd>
						<input type="number" id="progress" name="progress" value="{$progress}" min="0" max="100" maxlength="3" class="small" />
					</dd>
				</dl>
			{/if}
			
			{if ($action == 'add' && $category->canEditDeadline()) || ($action == 'edit' && $todo->canEditDeadline())}
				<dl{if $errorField == 'endTime'} class="formError"{/if}>
					<dt><label for="endTime">{lang}wcf.toDo.task.endTime{/lang}</label></dt>
					<dd>
						<input type="datetime" id="endTime" name="endTime" value="{if $endTime > 0}{$endTime}{/if}" />
					</dd>
				</dl>
			{/if}
			
			{if ($action == 'add' && $category->canEditReminder()) || ($action == 'edit' && $todo->canEditReminder())}
				<dl{if $errorField == 'remembertime'} class="formError"{/if}>
					<dt><label for="remembertime">{lang}wcf.toDo.task.remembertime{/lang}</label></dt>
					<dd>
						<input type="date" id="remembertime" name="remembertime" value="{if $remembertime > 0}{$remembertime}{/if}" />
					</dd>
				</dl>
			{/if}
			
			{if ($action == 'add' && $category->canEditPriority()) || ($action == 'edit' && $todo->canEditPriority())}
				<dl{if $errorField == 'priority'} class="formError"{/if}>
					<dt><label for="priority">{lang}wcf.toDo.task.important{/lang}</label></dt>
					<dd id="prioPicker">
						<span data-prio="3" class="badge label grey"{if $important == 3} style="font-weight: bolder;"{/if}>{lang}wcf.toDo.task.priority.low{/lang}</span>
						<span data-prio="2" class="badge label blue"{if $important == 2} style="font-weight: bolder;"{/if}>{lang}wcf.toDo.task.priority.normal{/lang}</span>
						<span data-prio="1" class="badge label red"{if $important == 1} style="font-weight: bolder;"{/if}>{lang}wcf.toDo.task.priority.high{/lang}</span> 
						<input type="hidden" id="priority" name="priority" value="{$important}" />
					</dd>
				</dl>
			{/if}
			
			<dl{if $errorField == 'private'} class="formError"{/if}>
				<dt><label for="private">{lang}wcf.toDo.task.private{/lang}</label></dt>
				<dd>
					<input type="checkbox" id="private" name="private" {if $private == '1'}checked{/if} />
					<p><small>{lang}wcf.toDo.task.private.description{/lang}</small></p>
				</dd>
			</dl>
			
			{event name='additionalFields'}
		</fieldset>

		<section class="section">
			<h2 class="sectionTitle">{lang}wcf.label.labels{/lang}</h2>

			<div id="labelSelectionContainer">
				{if $labelGroups|count}
					{foreach from=$labelGroups item=labelGroup}
						{if $labelGroup|count}
							<dl{if $errorField == 'label' && $errorType[$labelGroup->groupID]|isset} class="formError"{/if}>
								<dt><label>{$labelGroup->getTitle()}</label></dt>
								<dd>
									<ul class="labelList jsOnly" data-object-id="{@$labelGroup->groupID}">
										<li class="dropdown labelChooser" id="labelGroup{@$labelGroup->groupID}" data-group-id="{@$labelGroup->groupID}" data-force-selection="{if $labelGroup->forceSelection}true{else}false{/if}">
											<div class="dropdownToggle" data-toggle="labelGroup{@$labelGroup->groupID}"><span class="badge label">{lang}wcf.label.none{/lang}</span></div>
											<div class="dropdownMenu">
												<ul class="scrollableDropdownMenu">
													{foreach from=$labelGroup item=label}
														<li data-label-id="{@$label->labelID}"><span><span class="badge label{if $label->getClassNames()} {@$label->getClassNames()}{/if}">{lang}{$label->label}{/lang}</span></span></li>
													{/foreach}
												</ul>
											</div>
										</li>
									</ul>
									<noscript>
										<select name="labelIDs[{@$labelGroup->groupID}]">
											{foreach from=$labelGroup item=label}
												<option value="{@$label->labelID}">{lang}{$label->label}{/lang}</option>
											{/foreach}
										</select>
									</noscript>
									{if $errorField == 'label' && $errorType[$labelGroup->groupID]|isset}
										<small class="innerError">
											{if $errorType[$labelGroup->groupID] == 'missing'}
												{lang}wcf.label.error.missing{/lang}
											{else}
												{lang}wcf.label.error.invalid{/lang}
											{/if}
										</small>
									{/if}
								</dd>
							</dl>
						{/if}
					{/foreach}
				{/if}
			</div>
		</section>
		
		{if ($action == 'add' && $category->canEditResponsibles()) || ($action == 'edit' && $todo->canEditResponsibles())}
			<fieldset>
				<legend>{lang}wcf.toDo.task.responsible{/lang}</legend>
				
				<dl{if $errorField == 'responsibles'} class="formError"{/if}>
					<dt><label for="responsibles">{lang}wcf.toDo.task.responsible.users{/lang}</label></dt>
					<dd>
						<textarea id="responsibles" name="responsibles" class="long" cols="40" rows="4">{$responsibles}</textarea>
						<small>{lang}wcf.toDo.task.responsible.users.description{/lang}</small>
					</dd>
				</dl>
				<dl{if $errorField == 'responsibleGroups'} class="formError"{/if}>
					<dt><label for="responsibleGroups">{lang}wcf.toDo.task.responsible.groups{/lang}</label></dt>
					<dd>
						<textarea id="responsibleGroups" name="responsibleGroups" class="long" cols="40" rows="4">{$responsibleGroups}</textarea>
						<small>{lang}wcf.toDo.task.responsible.groups.description{/lang}</small>
					</dd>
				</dl>
			</fieldset>
		{/if}
	</div>
	
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.global.description{/lang}</legend>
			
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
			
			{event name='messageFields'}
		</fieldset>
	</div>
	
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.toDo.task.note{/lang}</legend>
			
			<textarea id="note" name="note" rows="10" cols="20">{$note}</textarea>
			{if $errorField == 'note'}
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
		</fieldset>
	</div>
	
	{hascontent}
		<div class="container containerPadding marginTop">
			{content}
				{event name='fieldsets'}
			{/content}
		</div>
	{/hascontent}
	
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

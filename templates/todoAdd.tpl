{capture assign='headContent'}
	{js file='WCF.Todo' application='wcf'}
{/capture}

{include file='header'}

<form id="messageContainer" class="jsFormGuard toDoContainer" method="post" action="{if $action=='edit'}{link controller='TodoEdit' id=$id}{/link}{else}{link controller='TodoAdd' id=$categoryID}{/link}{/if}">
	{if $action == 'edit'}{assign var='canEdit' value=$todo->canEdit()}{/if}

	<section class="section">
		<h2 class="sectionTitle">{lang}wcf.toDo.task.general{/lang}</h2>

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

		<dl>
			<dt>{lang}wcf.toDo.category{/lang}</dt>
			<dd><a href="{$category->getLink()}"><span class="label badge" style="background-color: {$category->color};">{$category->getTitle()}</span></a></dd>
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
					<input type="datetime" id="endTime" name="endTime" value="{if $endTime}{$endTime}{/if}" />
				</dd>
			</dl>
		{/if}

		{if ($action == 'add' && $category->canEditReminder()) || ($action == 'edit' && $todo->canEditReminder())}
			<dl{if $errorField == 'remembertime'} class="formError"{/if}>
				<dt><label for="remembertime">{lang}wcf.toDo.task.remembertime{/lang}</label></dt>
				<dd>
					<input type="date" id="remembertime" name="remembertime" value="{if $remembertime}{$remembertime}{/if}" />
				</dd>
			</dl>
		{/if}

		{if ($action == 'add' && $category->canEditPriority()) || ($action == 'edit' && $todo->canEditPriority())}
			<dl{if $errorField == 'priority'} class="formError"{/if}>
				<dt><label for="priority">{lang}wcf.toDo.task.important{/lang}</label></dt>
				<dd id="prioPicker">
					<span data-prio="3" class="button{if $important == 3} active{/if}">{lang}wcf.toDo.task.priority.low{/lang}</span>
					<span data-prio="2" class="button{if $important == 2 || !$important} active{/if}">{lang}wcf.toDo.task.priority.normal{/lang}</span>
					<span data-prio="1" class="button{if $important == 1} active{/if}">{lang}wcf.toDo.task.priority.high{/lang}</span>
					<input type="hidden" id="priority" name="priority" value="{$important}" />
				</dd>
			</dl>
		{/if}

		<dl{if $errorField == 'private'} class="formError"{/if}>
			<dt></dt>
			<dd>
				<label><input type="checkbox" id="private" name="private" value="1" {if $private}checked{/if} /> {lang}wcf.toDo.task.private{/lang}</label>
				<p><small>{lang}wcf.toDo.task.private.description{/lang}</small></p>
			</dd>
		</dl>

		<dl{if $errorField == 'enableComments'} class="formError"{/if}>
			<dt></dt>
			<dd>
				<label><input type="checkbox" id="enableComments" name="enableComments" value="1" {if $enableComments}checked{/if} /> {lang}wcf.toDo.task.enableComments{/lang}</label>
			</dd>
		</dl>

		{event name='additionalFields'}
	</section>

	<section class="section">
		<h2 class="sectionTitle">{lang}wcf.label.labels{/lang}</h2>

		<div id="labelSelectionContainer">
			{foreach from=$labelGroups item=labelGroup}
				{if $labelGroup !== null && $labelGroup->count()}
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
		</div>
	</section>

	{if ($action == 'add' && $category->canEditResponsibles()) || ($action == 'edit' && $todo->canEditResponsibles())}
		<section class="section">
			<h2 class="sectionTitle">{lang}wcf.toDo.task.responsible{/lang}</h2>

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
		</section>
	{/if}

	<section class="section">
		<h2 class="sectionTitle">{lang}wcf.global.description{/lang}</h2>

		<textarea id="text" name="description" rows="20" cols="40" data-autosave="de.mysterycode.wcf.toDo.todoAdd{if $todoID|isset}{@$todoID}{/if}.description">{$description}</textarea>
		{include file='messageFormTabsInline' wysiwygContainerID='text'}
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
	</section>
	
	<section class="section">
		<h2 class="sectionTitle">{lang}wcf.toDo.task.note{/lang}</h2>

		<textarea id="note" name="note" rows="10" cols="20" data-autosave="de.mysterycode.wcf.toDo.todoAdd{if $todoID|isset}{@$todoID}{/if}.notes">{$note}</textarea>
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
	</section>

	{event name='sections'}
	
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

<script data-relocate="true">
	require(['Language'], function (Language) {
		Language.addObject({
			'wcf.label.none': '{lang}wcf.label.none{/lang}'
		});

		new WCF.Search.User('#responsibles', null, false, [ ], true);
		new WCF.Todo.Search.User('#responsibleGroups', null, [ ], true);
		new WCF.Label.Chooser({ {implode from=$labelIDs key=groupID item=labelID}{@$groupID}: {@$labelID}{/implode} }, '#messageContainer');

		{include file='__messageQuoteManager' wysiwygSelector='text' supportPaste=true}
		new WCF.Todo.QuoteHandler($quoteManager);

		WCF.Message.Submit.registerButton('text', $('#messageContainer > .formSubmit > input[type=submit]'));
		new WCF.Message.FormGuard();

		WCF.System.Dependency.Manager.register('CKEditor', function() { new WCF.Message.UserMention('text'); });

		$('#prioPicker span').on('click', function(event) {
			$(event.currentTarget).parent().children().removeClass('active');
			$(event.currentTarget).addClass('active');
			$('#priority').val(WCF.String.escapeHTML($(event.currentTarget).data('prio')));
		});
	});
</script>

{include file='footer'}
{include file='wysiwyg'}
{include file='wysiwyg' wysiwygSelector='note'}

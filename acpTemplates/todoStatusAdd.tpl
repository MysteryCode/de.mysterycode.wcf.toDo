{include file='header'}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.todo.status.{$action}{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			{if $action == 'edit' && $availableStatus|count > 1}
				<li class="dropdown">
					<a class="button dropdownToggle"><span class="icon icon16 fa-sort"></span> <span>{lang}wcf.acp.todo.status.button.choose{/lang}</span></a>
					<div class="dropdownMenu">
						<ul class="scrollableDropdownMenu">
							{foreach from=$availableStatus item='item'}
								<li{if $item->statusID == $statusID} class="active"{/if}><a href="{link controller='TodoStatusEdit' object=$item}{/link}">{$item->getTitle()}</a></li>
							{/foreach}
						</ul>
					</div>
				</li>
			{/if}
			
			<li><a href="{link controller='TodoStatusList'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}wcf.acp.menu.link.todoStatus.list{/lang}</span></a></li>
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='TodoStatusAdd'}{/link}{else}{link controller='TodoStatusEdit' id=$statusID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.global.form.data{/lang}</legend>
			
			<dl{if $errorField == 'subject'} class="formError"{/if}>
				<dt><label for="subject">{lang}wcf.acp.todo.status.subject{/lang}</label></dt>
				<dd>
					<input type="text" id="subject" name="subject" value="{$i18nPlainValues['subject']}" autofocus="autofocus" class="long" />
					{if $errorField == 'subject'}
						<small class="innerError">
							{if $errorType == 'empty' || $errorType == 'multilingual'}
								{lang}wcf.global.form.error.{@$errorType}{/lang}
							{else}
								{lang}inventar.acp.field.fieldName.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			{include file='multipleLanguageInputJavascript' elementIdentifier='subject' forceSelection=false}
			
			<dl{if $errorField == 'description'} class="formError"{/if}>
				<dt><label for="description">{lang}wcf.acp.todo.status.description{/lang}</label></dt>
				<dd>
					<textarea id="description" name="description" class="long">{$i18nPlainValues['description']}</textarea>
					{if $errorField == 'description'}
						<small class="innerError">
							{if $errorType == 'empty' || $errorType == 'multilingual'}
								{lang}wcf.global.form.error.{@$errorType}{/lang}
							{else}
								{lang}inventar.acp.field.fieldName.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			{include file='multipleLanguageInputJavascript' elementIdentifier='description' forceSelection=false}
			
			<dl>
				<dt><label for="showOrder">{lang}wcf.acp.todo.status.cssClass{/lang}</label></dt>
				<dd>
					<input type="text" id="cssClass" name="cssClass" value="{$cssClass}" class="medium" />
				</dd>
			</dl>
			
			<dl>
				<dt><label for="showOrder">{lang}wcf.acp.todo.status.showOrder{/lang}</label></dt>
				<dd>
					<input type="number" id="showOrder" name="showOrder" value="{@$showOrder}" class="short" />
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

{include file='footer'}

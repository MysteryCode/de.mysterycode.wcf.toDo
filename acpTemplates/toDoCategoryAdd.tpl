{include file='header' pageTitle='wcf.acp.todo.category.'|concat:$action}

<script data-relocate="true" src="{@$__wcf->getPath()}js/WCF.ColorPicker.js?v={@$__wcfVersion}"></script>
<script data-relocate="true">
	//<![CDATA[
	$(function() {
		WCF.Language.addObject({
			'wcf.style.colorPicker': '{lang}wcf.style.colorPicker{/lang}',
			'wcf.style.colorPicker.new': '{lang}wcf.style.colorPicker.new{/lang}',
			'wcf.style.colorPicker.current': '{lang}wcf.style.colorPicker.current{/lang}',
			'wcf.style.colorPicker.button.apply': '{lang}wcf.style.colorPicker.button.apply{/lang}'
		});
		
		new WCF.ColorPicker('.jsColorPicker');
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}wcf.acp.todo.category.{$action}{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='ToDoCategoryList'}{/link}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}wcf.acp.todo.category.list{/lang}</span></a></li>
			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action=='edit'}{link controller='ToDoCategoryEdit' id=$id}{/link}{else}{link controller='ToDoCategoryAdd'}{/link}{/if}">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.global.form.data{/lang}</legend>
			
			<dl{if $errorField == 'title'} class="formError"{/if}>
				<dt><label for="title">{lang}wcf.acp.todo.category.title{/lang}</label></dt>
				<dd>
					<input type="text" id="title" name="title" value="{$title}" class="long" required />
					{if $errorField == 'title'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			
			<dl{if $errorField == 'color'} class="formError"{/if}>
				<dt><label for="color">{lang}wcf.acp.todo.category.color{/lang}</label></dt>
				<dd>
					<figure>
						<div class="colorPreview"><div class="jsColorPicker" style="background-color: {$color}" data-color="{$color}" data-store="color"></div></div>
						<input type="hidden" id="color" name="color" value="{$color}" />
					</figure>
					{if $errorField == 'color'}
						<small class="innerError">
							{lang}wbb.acp.board.iconColor.error.{@$errorType}{/lang}
						</small>
					{/if}
				</dd>
			</dl>
			
			{event name='dataFields'}
		</fieldset>
		
		{event name='fieldsets'}
	</div>
	
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}
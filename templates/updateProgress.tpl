<div>
	<fieldset>
		<dl>
			<dt><label for="progress">{lang}wcf.toDo.task.progress{/lang}</label></dt>
			<dd>
				<input type="number" id="progress" value="{$todo->progress}" min="0" max="100" required="required" class="short" />
			</dd>
		</dl>
	</fieldset>

	{event name='fieldsets'}
</div>

<div class="formSubmit">
	<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
</div>

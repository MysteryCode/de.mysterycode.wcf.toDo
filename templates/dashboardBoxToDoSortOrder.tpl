<form method="get" action="{link controller='ToDoList'}{/link}">
	<dl>
		<dt></dt>
		<dd>
			<select id="sortField" name="sortField">
				<option value="title"{if $sortField == 'title'} selected="selected"{/if}>{lang}wcf.toDo.task.title{/lang}</option>
				<option value="category"{if $sortField == 'category'} selected="selected"{/if}>{lang}wcf.toDo.category{/lang}</option>
				<option value="endTime"{if $sortField == 'endTime'} selected="selected"{/if}>{lang}wcf.toDo.task.endTime{/lang}</option>
				<option value="status"{if $sortField == 'status'} selected="selected"{/if}>{lang}wcf.toDo.task.status{/lang}</option>
				<option value="submitter"{if $sortField == 'submitter'} selected="selected"{/if}>{lang}wcf.toDo.task.submitter{/lang}</option>
				<option value="timestamp"{if $sortField == 'timestamp'} selected="selected"{/if}>{lang}wcf.toDo.task.submitTime{/lang}</option>
				<option value="updatetimestamp"{if $sortField == 'updatetimestamp'} selected="selected"{/if}>{lang}wcf.toDo.task.updatetimestamp{/lang}</option>
				<option value="important"{if $sortField == 'important'} selected="selected"{/if}>{lang}wcf.toDo.task.priority{/lang}</option>
				<option value="remembertime"{if $sortField == 'remembertime'} selected="selected"{/if}>{lang}wcf.toDo.task.remembertime{/lang}</option>
				{event name='sortField'}
			</select>
			<select name="sortOrder">
				<option value="ASC"{if $sortOrder == 'ASC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.ascending{/lang}</option>
				<option value="DESC"{if $sortOrder == 'DESC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.descending{/lang}</option>
			</select>
		</dd>
	</dl>
		
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>
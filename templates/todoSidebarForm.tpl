<section class="box">
	<h2 class="boxTitle"><label for="sortField">{lang}wcf.toDo.task.sort{/lang}</label></h2>

	<dl>
		<dt></dt>
		<dd>
			<select id="sortField" name="sortField">
				<option value="title"{if $sortField == 'title'} selected="selected"{/if}>{lang}wcf.toDo.task.title{/lang}</option>
				<option value="endTime"{if $sortField == 'endTime'} selected="selected"{/if}>{lang}wcf.toDo.task.endTime{/lang}</option>
				<option value="status"{if $sortField == 'status'} selected="selected"{/if}>{lang}wcf.toDo.task.status{/lang}</option>
				<option value="submitter"{if $sortField == 'submitter'} selected="selected"{/if}>{lang}wcf.toDo.task.submitter{/lang}</option>
				<option value="time"{if $sortField == 'time'} selected="selected"{/if}>{lang}wcf.toDo.task.submitTime{/lang}</option>
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
</section>

<section class="box">
	<h2 class="boxTitle"><label for="responsibleFilter">{lang}wcf.toDo.filter.responsible{/lang}</label></h2>

	<dl>
		<dt></dt>
		<dd>
			<input type="text" name="responsibleFilter" id="responsibleFilter" value="{$responsibleFilter}" class="long" placeholder="{lang}wcf.toDo.filter.responsible.placeholder{/lang}" />
		</dd>
	</dl>
</section>

{if !$statusList|empty}
	<section class="box">
		<h2 class="boxTitle"><label for="statusFilter">{lang}wcf.toDo.filter.status{/lang}</label></h2>

		<dl>
			<dt></dt>
			<dd>
				<select name="statusFilter" id="statusFilter" class="long">
					{foreach from=$statusList item=status}
					<select id="sortField" name="sortField">
						<option value="{$status->statusID}"{if $statusFilter == $status->statusID} selected{/if}>{$status->subject|language}</option>
					{/foreach}
				</select>
			</dd>
		</dl>
	</section>
{/if}

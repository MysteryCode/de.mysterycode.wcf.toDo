{hascontent}
	<dl class="plain inlineDataList">
		{content}
			{foreach from=$toDoList item=item}
				<dt>{lang}wcf.toDo.dashboard.stat.{$item.type}{/lang}</dt>
				<dd>{$item.count}</dd>
			{/foreach}
		{/content}
	</dl>
{/hascontent}
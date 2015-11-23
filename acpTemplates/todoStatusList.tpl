{include file='header'}

<script data-relocate="true">
	//<![CDATA[
	$(function() {
		new WCF.Action.Delete('wcf\\data\\todo\\status\\TodoStatusAction', '.jsStatusRow');
		
		var options = { };
		{if $pages > 1}
			options.refreshPage = true;
			{if $pages == $pageNo}
				options.updatePageNumber = -1;
			{/if}
		{else}
			options.emptyMessage = '{lang}wcf.global.noItems{/lang}';
		{/if}
		
		new WCF.Table.EmptyTableHandler($('#statusTableContainer'), 'jsStatusRow', options);
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}wcf.acp.menu.link.todoStatus.list{/lang}</h1>
</header>

<div class="contentNavigation">
	{pages print=true assign=pagesLinks controller='TodoStatusList' link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
	
	<nav>
		<ul>
			<li><a href="{link controller='TodoStatusAdd'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.acp.todo.status.add{/lang}</span></a></li>
			
			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>

{if $objects|count}
	<div id="statusTableContainer" class="tabularBox tabularBoxTitle marginTop">
		<table class="table">
			<thead>
				<tr>
					<th class="columnID columnStatusID{if $sortField == 'statusID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='TodoStatusList'}pageNo={@$pageNo}&sortField=statusID&sortOrder={if $sortField == 'statusID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnTitle{if $sortField == 'subject'} active {@$sortOrder}{/if}"><a href="{link controller='TodoStatusList'}pageNo={@$pageNo}&sortField=subject&sortOrder={if $sortField == 'subject' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.todo.status.subject{/lang}</a></th>
					<th class="columnDigits columnShowOrder{if $sortField == 'showOrder'} active {@$sortOrder}{/if}"><a href="{link controller='TodoStatusList'}pageNo={@$pageNo}&sortField=showOrder&sortOrder={if $sortField == 'showOrder' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.todo.status.showOrder{/lang}</a></th>
					
					{event name='columnHeads'}
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$objects item=status}
					<tr class="jsStatusRow">
						<td class="columnIcon">
							<a href="{link controller='TodoStatusEdit' object=$status}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
							<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$status->statusID}" data-confirm-message="{lang}wcf.acp.todo.status.delete.sure{/lang}"></span>
							
							{event name='rowButtons'}
						</td>
						<td class="columnID columnStatusID">{@$status->statusID}</td>
						<td class="columnTitle"><a href="{link controller='TodoStatusEdit' object=$status}{/link}">{$status->subject}</a></p></td>
						<td class="columnDigits columnShowOrder">{#$status->showOrder}</td>
						
						{event name='columns'}
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
	
	<div class="contentNavigation">
		{@$pagesLinks}

		<nav>
			<ul>
				<li><a href="{link controller='TodoStatusAdd'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.acp.todo.status.add{/lang}</span></a></li>
				
				{event name='contentNavigationButtonsBottom'}
			</ul>
		</nav>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}

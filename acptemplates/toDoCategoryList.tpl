{include file='header' pageTitle='wcf.acp.todo.category.list'}

<header class="boxHeadline">
	<h1>{lang}wcf.acp.todo.category.list{/lang}</h1>
</header>

<div class="contentNavigation">
	<nav>
		<ul>
			{if $__wcf->getSession()->getPermission('admin.content.toDo.category.canAdd')}<li><a href="{link controller='ToDoCategoryAdd'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.acp.todo.category.add{/lang}</span></a></li>{/if}
			
			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>

<div class="tabularBox tabularBoxTitle marginTop">
	<header>
		<h2>{lang}wcf.acp.todo.category.list{/lang}</h2>
	</header>
	
	<table class="table">
		<thead>
			<tr>
				<th class="columnID" colspan="2">
					{lang}wcf.global.objectID{/lang}</th>
				<th class="columnTitle">
					{lang}wcf.acp.todo.category.title{/lang}</th>
				<th class="columnTitle">
					{lang}wcf.acp.todo.category.preview{/lang}</th>
			</tr>
		</thead>
		<tbody>
			{if $categoryList}
				{foreach from=$categoryList item=category}
					<tr>
						<td class="columnIcon">
							{if $__wcf->getSession()->getPermission('admin.content.toDo.category.canEdit')}<a href="{link controller='ToDoCategoryEdit' id=$category.id}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>{/if}
							{if $__wcf->getSession()->getPermission('admin.content.toDo.category.canDelete')}<a href="{link controller='ToDoCategoryDelete' id=$category.id}{/link}" title="{lang}wcf.global.button.delete{/lang}" class="jsTooltip"><span class="icon icon16 icon-remove jsDeleteButton" title="{lang}wcf.global.button.delete{/lang}"></span></a>{/if}
							{event name='rowButtons'}
						</td>
						<td class="columnID">
							{$category.id}</td>
						<td class="columnTitle">
							<h3>{$category.title}</h3></td>
						<td class="columnTitle">
							<span class="label badge" style="background-color:{$category.color};">{$category.title}</span></td>
					</tr>
				{/foreach}
			{else}
				<p class="info">{lang}wcf.acp.todo.category.noItems{/lang}</p>
			{/if}
		</tbody>
	</table>
</div>

{include file='footer'}
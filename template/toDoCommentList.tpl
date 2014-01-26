{include file='__commentJavaScript' commentContainerID='toDoCommentList'}

{if $commentCanAdd}
	<ul id="toDoCommentList" class="commentList containerList" data-can-add="true" data-object-id="{@$toDo.id}" data-object-type-id="{@$commentObjectTypeID}" data-comments="{@$commentList->countObjects()}" data-last-comment-time="{@$lastCommentTime}">
		{include file='commentList'}
	</ul>
{else}
	{hascontent}
		<ul id="toDoCommentList" class="commentList containerList" data-can-add="false" data-object-id="{@$toDo.id}" data-object-type-id="{@$commentObjectTypeID}" data-comments="{@$commentList->countObjects()}" data-last-comment-time="{@$lastCommentTime}">
			{content}
				{include file='commentList'}
			{/content}
		</ul>
	{hascontentelse}
		<div class="containerPadding">
			{lang}wcf.toDo.comments.noEntries{/lang}
		</div>
	{/hascontent}
{/if}
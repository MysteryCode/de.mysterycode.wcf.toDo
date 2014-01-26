{if $__wcf->getUser()->userID != 0}
	{if $unsolvedToDoCount|isset && $unsolvedToDoCount > 0 && TODO_UNSOLVED_WARNING}
		<p class="info toDoInfo">{$unsolvedToDoCount} {lang}wcf.toDo.task.unsolved.reminder{if $unsolvedToDoCount == 1}Singular{else}Plural{/if}{/lang}</p>
	{/if}
	{if $overdueToDoCount|isset && $overdueToDoCount > 0 && TODO_OVERDUE_WARNING}
		<p class="error toDoWarning">{$overdueToDoCount} {lang}wcf.toDo.task.overdue.reminder{if $overdueToDoCount == 1}Singular{else}Plural{/if}{/lang}</p>
	{/if}
{/if}
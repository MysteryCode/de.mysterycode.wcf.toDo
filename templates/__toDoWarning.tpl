{if $__wcf->getUser()->userID && $__wcf->getSession()->getPermission('user.toDo.warning.canSee')}
	{if $unsolvedToDoCount|isset && $unsolvedToDoCount > 0 && TODO_UNSOLVED_WARNING}
		<div class="jsOnly info toDoInfo" id="todoWarningUnsolved">{$unsolvedToDoCount} {lang}wcf.toDo.task.unsolved.reminder{if $unsolvedToDoCount == 1}Singular{else}Plural{/if}{/lang}{if $__wcf->getSession()->getPermission('user.toDo.warning.canHide')}<a><span class="icon fa-remove pointer" style="float:right;"></span></a>{/if}</div>
	{/if}
	{if $overdueToDoCount|isset && $overdueToDoCount > 0 && TODO_OVERDUE_WARNING}
		<div class="jsOnly error toDoWarning" id="todoWarningOverdue">{$overdueToDoCount} {lang}wcf.toDo.task.overdue.reminder{if $overdueToDoCount == 1}Singular{else}Plural{/if}{/lang}{if $__wcf->getSession()->getPermission('user.toDo.warning.canHide')}<a><span class="icon fa-remove pointer" style="float:right;"></span></a>{/if}</div>
	{/if}
	{if $waitingToDoCount|isset && $waitingToDoCount > 0 && TODO_WAITING_WARNING}
		<div class="jsOnly error toDoWarning" id="todoWarningWaiting">{$waitingToDoCount} {lang}wcf.toDo.task.waiting.reminder{if $waitingToDoCount == 1}Singular{else}Plural{/if}{/lang}{if $__wcf->getSession()->getPermission('user.toDo.warning.canHide')}<a><span class="icon fa-remove pointer" style="float:right;"></span></a>{/if}</div>
	{/if}
	{if $__wcf->getSession()->getPermission('user.toDo.warning.canHide')}
		<script data-relocate="true">
			//<![CDATA[
				if (document.cookie.indexOf('todoWarningUnsolved=pause') > -1)
					$('#todoWarningUnsolved').hide();
				if (document.cookie.indexOf('todoWarningOverdue=pause') > -1)
					$('#todoWarningOverdue').hide();
				if (document.cookie.indexOf('todoWarningWaiting=pause') > -1)
					$('#todoWarningWaiting').hide();
				$('#todoWarningUnsolved a').click(function() {
					$('#todoWarningUnsolved').hide();
					var d = new Date(new Date().getTime() + 1000 * 3600 * {TODO_UNSOLVED_WARNING_HIDE_DURATION});
					document.cookie = 'todoWarningUnsolved=pause; expires='+d.toGMTString()+'; path=/';
				});
				$('#todoWarningOverdue a').click(function() {
					$('#todoWarningOverdue').hide();
					var d = new Date(new Date().getTime() + 1000 * 3600 * {TODO_OVERDUE_WARNING_HIDE_DURATION});
					document.cookie = 'todoWarningOverdue=pause; expires='+d.toGMTString()+'; path=/';
				});
				$('#todoWarningUnsolved a').click(function() {
					$('#todoWarningUnsolved').hide();
					var d = new Date(new Date().getTime() + 1000 * 3600 * {TODO_WAITING_WARNING_HIDE_DURATION});
					document.cookie = 'todoWarningUnsolved=pause; expires='+d.toGMTString()+'; path=/';
				});
			//]]>
		</script>
	{/if}
{/if}

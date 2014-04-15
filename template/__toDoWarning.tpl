{if $__wcf->getUser()->userID != 0 && $__wcf->getSession()->getPermission('user.toDo.warning.canSee')}
	{if $unsolvedToDoCount|isset && $unsolvedToDoCount > 0 && TODO_UNSOLVED_WARNING}
		<p class="jsOnly info toDoInfo" id="todoWarningUnsolved">{$unsolvedToDoCount} {lang}wcf.toDo.task.unsolved.reminder{if $unsolvedToDoCount == 1}Singular{else}Plural{/if}{/lang}{if $__wcf->getSession()->getPermission('user.toDo.warning.canHide')}<a><span class="icon icon-remove pointer" style="float:right;"></span></a>{/if}</p>
	{/if}
	{if $overdueToDoCount|isset && $overdueToDoCount > 0 && TODO_OVERDUE_WARNING}
		<p class="jsOnly error toDoWarning" id="todoWarningOverdue">{$overdueToDoCount} {lang}wcf.toDo.task.overdue.reminder{if $overdueToDoCount == 1}Singular{else}Plural{/if}{/lang}{if $__wcf->getSession()->getPermission('user.toDo.warning.canHide')}<a><span class="icon icon-remove pointer" style="float:right;"></span></a>{/if}</p>
	{/if}
	{if $__wcf->getSession()->getPermission('user.toDo.warning.canHide')}
		<script type="text/javascript" data-relocate="true">
			//<![CDATA[
				if (document.cookie.indexOf('todoWarningUnsolved=pause') > -1)
					$('#todoWarningUnsolved').hide();
				if (document.cookie.indexOf('todoWarningOverdue=pause') > -1)
					$('#todoWarningOverdue').hide();
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
			//]]>
		</script>
	{/if}
{/if}
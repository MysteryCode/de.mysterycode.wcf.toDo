{if $__wcf->getUser()->userID != 0 && $__wcf->getSession()->getPermission('user.toDo.warning.canSee')}
	{if $unsolvedToDoCount|isset && $unsolvedToDoCount > 0 && TODO_UNSOLVED_WARNING}
		<p class="info toDoInfo" id="todoWarningUnsolved">{$unsolvedToDoCount} {lang}wcf.toDo.task.unsolved.reminder{if $unsolvedToDoCount == 1}Singular{else}Plural{/if}{/lang}{if $__wcf->getSession()->getPermission('user.toDo.warning.canHide')}<span class="icon icon-remove pointer" style="float:right;" onclick="hideToDoWarning('todoWarningUnsolved');"></span>{/if}</p>
	{/if}
	{if $overdueToDoCount|isset && $overdueToDoCount > 0 && TODO_OVERDUE_WARNING}
		<p class="error toDoWarning" id="todoWarningOverdue">{$overdueToDoCount} {lang}wcf.toDo.task.overdue.reminder{if $overdueToDoCount == 1}Singular{else}Plural{/if}{/lang}{if $__wcf->getSession()->getPermission('user.toDo.warning.canHide')}<span class="icon icon-remove pointer" style="float:right;" onclick="hideToDoWarning('todoWarningOverdue');"></span>{/if}</p>
	{/if}
	
	{if $__wcf->getSession()->getPermission('user.toDo.warning.canHide')}
		<script type="text/javascript" data-relocate="true">
			//<![CDATA[
				{if $unsolvedToDoCount|isset && $unsolvedToDoCount > 0 && TODO_UNSOLVED_WARNING}
					if (document.cookie.indexOf("todoWarningUnsolved=pause") > -1)
						document.getElementById("todoWarningUnsolved").style.display = "none";
				{/if}
				{if $overdueToDoCount|isset && $overdueToDoCount > 0 && TODO_OVERDUE_WARNING}
					if (document.cookie.indexOf("todoWarningOverdue=pause") > -1)
						document.getElementById("todoWarningOverdue").style.display = "none";
				{/if}
				function hideToDoWarning($elementID) {
					document.getElementById($elementID).style.display = "none";
					if ($elementID == "todoWarningUnsolved") {
						var d = new Date(new Date().getTime() + 1000 * 3600 * {TODO_UNSOLVED_WARNING_HIDE_DURATION});
						document.cookie = $elementID + '=pause; expires='+d.toGMTString()+'; path=/';
					}
					if ($elementID == "todoWarningOverdue") {
						var d = new Date(new Date().getTime() + 1000 * 3600 * {TODO_OVERDUE_WARNING_HIDE_DURATION});
						document.cookie = $elementID + '=pause; expires='+d.toGMTString()+'; path=/';
					}
				}
			//]]>
		</script>
	{/if}
{/if}

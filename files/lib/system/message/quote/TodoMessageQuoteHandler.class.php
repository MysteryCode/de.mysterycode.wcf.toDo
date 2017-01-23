<?php
namespace wcf\system\message\quote;
use wcf\data\todo\ToDoList;

/**
 * 
 * 
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoMessageQuoteHandler extends AbstractMessageQuoteHandler {
	/**
	 * @inheritDoc
	 */
	protected function getMessages(array $data) {
		$todoList = new ToDoList();
		$todoList->getConditionBuilder()->add("todo_table.todoID IN (?)", [array_keys($data)]);
		$todoList->readObjects();
		$todos = $todoList->getObjects();
		
		$todoIDs = [];
		foreach ($todos as $todo) {
			$todoIDs[] = $todo->todoID;
		}
		
		$quotedMessages = [];
		
		// create QuotedMessage objects
		foreach ($todos as $todo) {
			$message = new QuotedMessage($todo);
			
			foreach (array_keys($data[$todo->todoID]) as $quoteID) {
				$message->addQuote(
					$quoteID,
					MessageQuoteManager::getInstance()->getQuote($quoteID, false),
					MessageQuoteManager::getInstance()->getQuote($quoteID, true)
				);
			}
			
			$quotedMessages[] = $message;
		}
		
		if (count($todoIDs) != count($data)) {
			$orphanedQuoteIDs = [];
			foreach ($data as $todoID => $quoteIDs) {
				if (!in_array($todoID, $todoIDs)) {
					foreach (array_keys($quoteIDs) as $quoteID) {
						$orphanedQuoteIDs[] = $quoteID;
					}
				}
			}
			
			MessageQuoteManager::getInstance()->removeOrphanedQuotes($orphanedQuoteIDs);
		}
		
		return $quotedMessages;
	}
}

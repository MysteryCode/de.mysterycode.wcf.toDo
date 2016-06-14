<?php
namespace wcf\system\message\quote;
use wcf\data\todo\ToDoList;
use wcf\system\message\quote\AbstractMessageQuoteHandler;
use wcf\system\message\quote\MessageQuoteManager;
use wcf\system\message\quote\QuotedMessage;

class TodoMessageQuoteHandler extends AbstractMessageQuoteHandler {
	/**
	 * @see	\wcf\system\message\quote\AbstractMessageQuoteHandler::getMessages()
	 */
	protected function getMessages(array $data) {
		$todoList = new ToDoList();
		$todoList->getConditionBuilder()->add("todo.todoID IN (?)", array(array_keys($data)));
		$todoList->readObjects();
		$todos = $todoList->getObjects();
		
		$todoIDs = array();
		foreach ($todos as $todo) {
			$todoIDs[] = $todo->todoID;
		}
		
		$quotedMessages = array();
		
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
			$orphanedQuoteIDs = array();
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

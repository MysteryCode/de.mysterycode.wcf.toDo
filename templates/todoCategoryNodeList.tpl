{if $categoryNodeList|count > 0}
	<div>
		<ul class="wbbBoardList todoCategoryList">
			{foreach from=$categoryNodeList item='categoryNode'}
				<li data-category-id="{@$categoryNode->categoryID}" class="wbbBoardContainer todoCategoryContainer wbbDepth1 todoDepth1 tabularBox wbbLastBoxElement todoLastBoxElement">
					<div class="{cycle name=categoryNodeAlternate values='wbbBoardNode1 todoCategoryNode1,wbbBoardNode2 todoCategoryNode2'} wbbBoard todoCategory box32">
						<span class="icon icon32 icon-folder-open"></span>
						
						<div>
							<div class="containerHeadline">
								<h3><a href="{link controller='TodoCategory' object=$categoryNode}{/link}">{$categoryNode->title|language}</a></h3>
								<p class="wbbBoardDescription todoCategoryDescription">{if $categoryNode->descriptionUseHtml}{@$categoryNode->description|language}{else}{$categoryNode->description|language}{/if}</p>
							</div>
							
							{if $categoryNode->getSubCategories()|count}
								<ul class="wbbSubBoards todoSubCategories">
									{foreach from=$categoryNode->getSubCategories() item=subCategoryNode}
										<li class="box16">
											<span class="icon icon16 icon-folder-open"></span>
											<div>
												<a href="{link controller='TodoCategory' object=$subCategoryNode}{/link}">{$subCategoryNode->title|language}</a>
											</div>
										</li>
									{/foreach}
								</ul>
							{/if}
							
							{event name='categoryData'}
						</div>
					</div>
				</li>
			{/foreach}
		</ul>
	</div>
{/if}
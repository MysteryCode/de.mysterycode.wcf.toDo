{if $categoryNodeList|count > 0}
	<section class="section tabularBox">
		<ul class="todoCategoryList">
			{foreach from=$categoryNodeList item='categoryNode'}
				<li data-category-id="{@$categoryNode->categoryID}">
					<div class="box32">
						<span class="icon icon32 fa-folder-open"></span>

						<div>
							<div class="containerHeadline">
								<h3><a href="{link controller='TodoCategory' object=$categoryNode}{/link}">{$categoryNode->title|language}</a></h3>
								<p class="todoCategoryDescription">{if $categoryNode->descriptionUseHtml}{@$categoryNode->description|language}{else}{$categoryNode->description|language|newlineToBreak}{/if}</p>
							</div>

							{if $categoryNode->getSubCategories()|count}
								<ul class="todoSubCategories">
									{foreach from=$categoryNode->getSubCategories() item=subCategoryNode}
										<li class="box16">
											<span class="icon icon16 fa-folder-open"></span>
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
	</section>
{/if}

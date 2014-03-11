<?php
namespace TYPO3\CMS\Workspaces\Tests\Functional\DataHandling\ManyToMany\Publish;

/***************************************************************
 * Copyright notice
 *
 * (c) 2014 Oliver Hader <oliver.hader@typo3.org>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once dirname(dirname(__FILE__)) . '/AbstractActionTestCase.php';

/**
 * Functional test for the DataHandler
 */
class ActionTest extends \TYPO3\CMS\Workspaces\Tests\Functional\DataHandling\ManyToMany\AbstractActionTestCase {

	/**
	 * @var string
	 */
	protected $assertionDataSetDirectory = 'typo3/sysext/workspaces/Tests/Functional/DataHandling/ManyToMany/Publish/DataSet/';

	/**
	 * MM Relations
	 */

	/**
	 * @test
	 * @see DataSet/Assertion/addCategoryRelation.csv
	 */
	public function addCategoryRelation() {
		parent::addCategoryRelation();
		$this->actionService->publishRecord(self::TABLE_Content, self::VALUE_ContentIdFirst);
		$this->assertAssertionDataSet('addCategoryRelation');

		$responseContent = $this->getFrontendResponse(self::VALUE_PageId, 0)->getResponseContent();
		$this->assertResponseContentStructureHasRecords(
			$responseContent, self::TABLE_Content . ':' . self::VALUE_ContentIdFirst, 'categories',
			self::TABLE_Category, 'title', array('Category A', 'Category B', 'Category A.A')
		);
	}

	/**
	 * @test
	 * @see DataSet/Assertion/deleteCategoryRelation.csv
	 */
	public function deleteCategoryRelation() {
		parent::deleteCategoryRelation();
		$this->actionService->publishRecord(self::TABLE_Content, self::VALUE_ContentIdFirst);
		$this->assertAssertionDataSet('deleteCategoryRelation');

		$responseContent = $this->getFrontendResponse(self::VALUE_PageId, 0)->getResponseContent();
		$this->assertResponseContentStructureHasRecords(
			$responseContent, self::TABLE_Content . ':' . self::VALUE_ContentIdFirst, 'categories',
			self::TABLE_Category, 'title', array('Category A')
		);
		$this->assertResponseContentStructureDoesNotHaveRecords(
			$responseContent, self::TABLE_Content . ':' . self::VALUE_ContentIdFirst, 'categories',
			self::TABLE_Category, 'title', array('Category B', 'Category C', 'Category A.A')
		);
	}

	/**
	 * @test
	 * @see DataSet/Assertion/changeCategoryRelationSorting.csv
	 */
	public function changeCategoryRelationSorting() {
		parent::changeCategoryRelationSorting();
		$this->actionService->publishRecord(self::TABLE_Content, self::VALUE_ContentIdFirst);
		$this->assertAssertionDataSet('changeCategoryRelationSorting');

		$responseContent = $this->getFrontendResponse(self::VALUE_PageId, 0)->getResponseContent();
		$this->assertResponseContentStructureHasRecords(
			$responseContent, self::TABLE_Content . ':' . self::VALUE_ContentIdFirst, 'categories',
			self::TABLE_Category, 'title', array('Category A', 'Category B')
		);
	}

	/**
	 * @test
	 * @see DataSet/Assertion/createContentRecordAndAddCategoryRelation.csv
	 */
	public function createContentAndAddRelation() {
		parent::createContentAndAddRelation();
		$this->actionService->publishRecord(self::TABLE_Content, $this->recordIds['newContentId']);
		$this->assertAssertionDataSet('createContentNAddRelation');

		$responseContent = $this->getFrontendResponse(self::VALUE_PageId, 0)->getResponseContent();
		$this->assertResponseContentHasRecords($responseContent, self::TABLE_Content, 'header', 'Testing #1');
		$this->assertResponseContentStructureHasRecords(
			$responseContent, self::TABLE_Content . ':' . $this->recordIds['newContentId'], 'categories',
			self::TABLE_Category, 'title', 'Category B'
		);
	}

	/**
	 * @test
	 * @see DataSet/Assertion/createCategoryRecordAndAddCategoryRelation.csv
	 */
	public function createCategoryAndAddRelation() {
		parent::createCategoryAndAddRelation();
		$this->actionService->publishRecord(self::TABLE_Category, $this->recordIds['newCategoryId']);
		$this->assertAssertionDataSet('createCategoryNAddRelation');

		// @todo Does not work due to the core bug of not setting the reference field in the MM record
		/*
			$responseContent = $this->getFrontendResponse(self::VALUE_PageId, 0)->getResponseContent();
			$this->assertResponseContentHasRecords($responseContent, self::TABLE_Category, 'title', 'Testing #1');
			$this->assertResponseContentStructureHasRecords(
				$responseContent, self::TABLE_Content . ':' . self::VALUE_ContentIdFirst, 'categories',
				self::TABLE_Category, 'title', 'Testing #1'
			);
		*/
	}

	/**
	 * @test
	 * @see DataSet/Assertion/createContentRecordAndCreateCategoryRelation.csv
	 */
	public function createContentAndCreateRelation() {
		parent::createContentAndCreateRelation();
		$this->actionService->publishRecords(
			array(
				self::TABLE_Category => array($this->recordIds['newCategoryId']),
				self::TABLE_Content => array($this->recordIds['newContentId']),
			)
		);
		$this->assertAssertionDataSet('createContentNCreateRelation');

		$responseContent = $this->getFrontendResponse(self::VALUE_PageId, 0)->getResponseContent();
		$this->assertResponseContentHasRecords($responseContent, self::TABLE_Content, 'header', 'Testing #1');

		// @todo New category is not resolved in new content element due to core bug
		// The frontend query ignores pid=-1 and thus the specific workspace record in sys_category:33
		/*
			$this->assertResponseContentStructureHasRecords(
				$responseContent, self::TABLE_Content . ':' . $this->recordIds['newContentId'], 'categories',
				self::TABLE_Category, 'title', 'Testing #1'
			);
		*/
	}

	/**
	 * @test
	 * @see DataSet/Assertion/createCategoryRecordAndCreateCategoryRelation.csv
	 */
	public function createCategoryAndCreateRelation() {
		parent::createCategoryAndCreateRelation();
		$this->actionService->publishRecords(
			array(
				self::TABLE_Content => array($this->recordIds['newContentId']),
				self::TABLE_Category => array($this->recordIds['newCategoryId']),
			)
		);
		$this->actionService->publishWorkspace(self::VALUE_WorkspaceId);
		$this->assertAssertionDataSet('createCategoryNCreateRelation');
	}

	/**
	 * @test
	 * @see DataSet/Assertion/modifyCategoryRecordOfCategoryRelation.csv
	 */
	public function modifyCategoryOfRelation() {
		parent::modifyCategoryOfRelation();
		$this->actionService->publishRecord(self::TABLE_Category, self::VALUE_CategoryIdFirst);
		$this->assertAssertionDataSet('modifyCategoryOfRelation');

		$responseContent = $this->getFrontendResponse(self::VALUE_PageId, 0)->getResponseContent();
		$this->assertResponseContentStructureHasRecords(
			$responseContent, self::TABLE_Content . ':' . self::VALUE_ContentIdFirst, 'categories',
			self::TABLE_Category, 'title', array('Testing #1', 'Category B')
		);
	}

	/**
	 * @test
	 * @see DataSet/Assertion/modifyContentRecordOfCategoryRelation.csv
	 */
	public function modifyContentOfRelation() {
		parent::modifyContentOfRelation();
		$this->actionService->publishRecord(self::TABLE_Content, self::VALUE_ContentIdFirst);
		$this->assertAssertionDataSet('modifyContentOfRelation');

		$responseContent = $this->getFrontendResponse(self::VALUE_PageId, 0)->getResponseContent();
		$this->assertResponseContentHasRecords($responseContent, self::TABLE_Content, 'header', 'Testing #1');
	}

	/**
	 * @test
	 * @see DataSet/Assertion/modifyBothRecordsOfCategoryRelation.csv
	 */
	public function modifyBothsOfRelation() {
		parent::modifyBothsOfRelation();
		$this->actionService->publishRecords(
			array(
				self::TABLE_Content => array(self::VALUE_ContentIdFirst),
				self::TABLE_Category => array(self::VALUE_CategoryIdFirst),
			)
		);
		$this->assertAssertionDataSet('modifyBothsOfRelation');

		$responseContent = $this->getFrontendResponse(self::VALUE_PageId, 0)->getResponseContent();
		$this->assertResponseContentStructureHasRecords(
			$responseContent, self::TABLE_Content . ':' . self::VALUE_ContentIdFirst, 'categories',
			self::TABLE_Category, 'title', array('Testing #1', 'Category B')
		);
		$this->assertResponseContentHasRecords($responseContent, self::TABLE_Content, 'header', 'Testing #1');
	}

	/**
	 * @test
	 * @see DataSet/Assertion/deleteContentRecordOfCategoryRelation.csv
	 */
	public function deleteContentOfRelation() {
		parent::deleteContentOfRelation();
		$this->actionService->publishRecord(self::TABLE_Content, self::VALUE_ContentIdLast);
		$this->assertAssertionDataSet('deleteContentOfRelation');

		$responseContent = $this->getFrontendResponse(self::VALUE_PageId, 0)->getResponseContent();
		$this->assertResponseContentDoesNotHaveRecords($responseContent, self::TABLE_Content, 'header', 'Testing #1');
	}

	/**
	 * @test
	 * @see DataSet/Assertion/deleteCategoryRecordOfCategoryRelation.csv
	 */
	public function deleteCategoryOfRelation() {
		parent::deleteCategoryOfRelation();
		$this->actionService->publishRecord(self::TABLE_Category, self::VALUE_CategoryIdFirst);
		$this->assertAssertionDataSet('deleteCategoryOfRelation');

		$responseContent = $this->getFrontendResponse(self::VALUE_PageId, 0)->getResponseContent();
		$this->assertResponseContentStructureDoesNotHaveRecords(
			$responseContent, self::TABLE_Content . ':' . self::VALUE_ContentIdFirst, 'categories',
			self::TABLE_Category, 'title', array('Category A')
		);
	}

	/**
	 * @test
	 * @see DataSet/Assertion/copyContentRecordOfCategoryRelation.csv
	 */
	public function copyContentOfRelation() {
		parent::copyContentOfRelation();
		$this->actionService->publishRecord(self::TABLE_Content, $this->recordIds['newContentId']);
		$this->assertAssertionDataSet('copyContentOfRelation');

		$responseContent = $this->getFrontendResponse(self::VALUE_PageId, 0)->getResponseContent();
		$this->assertResponseContentStructureHasRecords(
			$responseContent, self::TABLE_Content . ':' . $this->recordIds['newContentId'], 'categories',
			self::TABLE_Category, 'title', array('Category B', 'Category C')
		);
	}

	/**
	 * @test
	 * @see DataSet/Assertion/copyCategoryRecordOfCategoryRelation.csv
	 */
	public function copyCategoryOfRelation() {
		parent::copyCategoryOfRelation();
		$this->actionService->publishRecord(self::TABLE_Category, $this->recordIds['newCategoryId']);
		$this->assertAssertionDataSet('copyCategoryOfRelation');

		$responseContent = $this->getFrontendResponse(self::VALUE_PageId, 0)->getResponseContent();
		$this->assertResponseContentStructureHasRecords(
			$responseContent, self::TABLE_Content . ':' . self::VALUE_ContentIdFirst, 'categories',
			self::TABLE_Category, 'title', 'Category A'
			// @todo Actually it should be twice "Category A" since the category got copied
			// The frontend query ignores pid=-1 and thus the specific workspace record in sys_category:33
			// SELECT sys_category.* FROM sys_category JOIN sys_category_record_mm ON sys_category_record_mm.uid_local = sys_category.uid WHERE sys_category.uid IN (33,28,29)
			// AND sys_category_record_mm.uid_foreign=297 AND (sys_category.sys_language_uid IN (0,-1))
			// AND sys_category.deleted=0 AND (sys_category.t3ver_wsid=0 OR sys_category.t3ver_wsid=1) AND sys_category.pid<>-1
			// ORDER BY sys_category_record_mm.sorting_foreign
			// self::TABLE_Category, 'title', array('Category A', 'Category A')
		);
	}

	/**
	 * @test
	 * @see DataSet/Assertion/localizeContentRecordOfCategoryRelation.csv
	 */
	public function localizeContentOfRelation() {
		parent::localizeContentOfRelation();
		$this->actionService->publishRecord(self::TABLE_Content, $this->recordIds['localizedContentId']);
		$this->assertAssertionDataSet('localizeContentOfRelation');

		$responseContent = $this->getFrontendResponse(self::VALUE_PageId, self::VALUE_LanguageId)->getResponseContent();
		$this->assertResponseContentStructureHasRecords(
			$responseContent, self::TABLE_Content . ':' . self::VALUE_ContentIdLast, 'categories',
			self::TABLE_Category, 'title', array('Category B', 'Category C')
		);
	}

	/**
	 * @test
	 * @see DataSet/Assertion/localizeCategoryRecordOfCategoryRelation.csv
	 */
	public function localizeCategoryOfRelation() {
		parent::localizeCategoryOfRelation();
		$this->actionService->publishRecord(self::TABLE_Category, $this->recordIds['localizedCategoryId']);
		$this->assertAssertionDataSet('localizeCategoryOfRelation');

		$responseContent = $this->getFrontendResponse(self::VALUE_PageId, self::VALUE_LanguageId)->getResponseContent();
		$this->assertResponseContentStructureHasRecords(
			$responseContent, self::TABLE_Content . ':' . self::VALUE_ContentIdFirst, 'categories',
			self::TABLE_Category, 'title', array('[Translate to Dansk:] Category A', 'Category B')
		);
	}

	/**
	 * @test
	 * @see DataSet/Assertion/moveContentRecordOfCategoryRelationToDifferentPage.csv
	 */
	public function moveContentOfRelationToDifferentPage() {
		parent::moveContentOfRelationToDifferentPage();
		$this->actionService->publishRecord(self::TABLE_Content, self::VALUE_ContentIdLast);
		$this->assertAssertionDataSet('moveContentOfRelationToDifferentPage');

		$responseContent = $this->getFrontendResponse(self::VALUE_PageIdTarget, 0)->getResponseContent();
		$this->assertResponseContentStructureHasRecords(
			$responseContent, self::TABLE_Content . ':' . self::VALUE_ContentIdLast, 'categories',
			self::TABLE_Category, 'title', array('Category B', 'Category C')
		);
	}

}

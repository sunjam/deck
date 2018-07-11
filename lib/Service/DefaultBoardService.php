<?php
/**
 * @copyright Copyright (c) 2018 Ryan Fletcher <ryan.fletcher@codepassion.ca>
 *
 * @author Ryan Fletcher <ryan.fletcher@codepassion.ca>
 *
 * @license GNU AGPL version 3 or any later version
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Deck\Service;

use OCA\Deck\Db\BoardMapper;
use OCA\Deck\Service\BoardService;
use OCA\Deck\Service\StackService;
use OCA\Deck\Service\CardService;
use OCP\IConfig;

class DefaultBoardService {

	private $boardMapper;
	private $boardService;
	private $stackService;
	private $cardService;
	private $config;

    public function __construct(
			BoardMapper $boardMapper,
			BoardService $boardService,
			StackService $stackService,
			CardService $cardService,
			IConfig $config
			) {

		$this->boardService = $boardService;
		$this->stackService = $stackService;
		$this->cardService = $cardService;
		$this->config = $config;
		$this->boardMapper = $boardMapper;
    }
    
    public function checkFirstRun($userId, $appName) {        
		$firstRun = $this->config->getUserValue($userId,$appName,'firstRun','yes');
		$userBoards = $this->boardMapper->findAllByUser($userId);
		
		if ($firstRun === 'yes' && count($userBoards) === 0) {
			$this->config->setUserValue($userId,$appName,'firstRun','no');
			return true;
		}

		return false;
    }

    public function createDefaultBoard($title, $userId, $color) {
        $defaultBoard = $this->boardService->create($title, $userId, $color);
        $defaultStacks = [];
        $defaultCards = [];

		$boardId = $defaultBoard->getId();
                
		$defaultStacks[] = $this->stackService->create('To do', $boardId, 1);
		$defaultStacks[] = $this->stackService->create('Doing', $boardId, 1);
		$defaultStacks[] = $this->stackService->create('Done', $boardId, 1);
        
		$defaultCards[] = $this->cardService->create('Example Task 3', $defaultStacks[0]->getId(), 'text', 0, $userId);
		$defaultCards[] = $this->cardService->create('Example Task 2', $defaultStacks[1]->getId(), 'text', 0, $userId);
		$defaultCards[] = $this->cardService->create('Example Task 1', $defaultStacks[2]->getId(), 'text', 0, $userId);

		return $defaultBoard;
    }    
}
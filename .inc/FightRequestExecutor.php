<?php

require_once('./.inc/Executor.php');
require_once('./.inc/.db/DBConnection.php');

require_once('./.inc/.db/.manager/FightManager.php');
require_once('./.inc/.db/.manager/FRequestManager.php');

require_once('./.inc/.chess/Board.php');

class FightRequestExecutor extends Executor {

  public function doRequest (STemplate $template, Request $request) {
    $template->setOutput('requests.xtpl');

    if (strlen($request->getIAction()) > 0) {
      switch ($request->getIAction()) {
        case 'init':
          $template->swapDirs('executor');
          $template->setOutput('init.xtpl');

          break;

        default:
          break;
      }
    } else {
      switch ($request->getAction()) {
        case 'create_request':
          FRequestManager::createRequest($this->user->getId());
          break;

        case 'accept_request':
          $rid = $request->getAttribute('request');

          $frequest = FRequestManager::acceptRequest($this->user->getId(), $rid);
          if (!$frequest) {
            break;
          }

          $fight = FightManager::createFight($frequest);

          if (!$fight) {
            break;
          }
          break;

        case 'reject_request':
          $rid = $request->getAttribute('request');
          FRequestManager::rejectRequest($this->user->getId(), $rid);
          break;

        case 'cancel_request':
          $rid = $request->getAttribute('request');
          FRequestManager::cancelRequest($this->user->getId(), $rid);
          break;

        case 'attach_request':
          $rid = $request->getAttribute('request');
          FRequestManager::attachRequest($this->user->getId(), $rid);
          break;

        case 'unattach_request':
          $rid = $request->getAttribute('request');
          FRequestManager::unattachRequest($this->user->getId(), $rid);
          break;

      }
    }

    $this->getRequestsInfo($template);
    $this->getNonAttachedActiveFights($template);

    return;

    //    $template->expandDirs( 'requests' );

    $connection = DBConnection::getInstance();

    switch ($request->getAction()) {
      case 'create_request':
        break;

      case 'cancel_request':
        break;

      case 'attach_request':
        break;

      case 'unattach_request':
        break;

      case 'reject_request':
        break;

      case 'accept_request':
        break;

      case 'load':
        $template->setOutput("requests.xtpl");
        break;

      default:
        $template->setOutput("requests.xtpl");
        break;
    }

    $this->getRequestsInfo($template);
    $this->getNonAttachedActiveFights($template);

  }

  /**
   * @param STemplate $template
   */
  private function getRequestsInfo (STemplate $template) {
    $requests = FRequestManager::getAllRequest($this->user->getId(), 0);
    $template->assign('all_requests', $requests);
  }

  /**
   * @param STemplate $template
   */
  private function getNonAttachedActiveFights (STemplate $template) {
    $fights = FightManager::getNonAttachedActiveFightIds($this->user->getId());
    $template->assign('redirect_fights', $fights);
  }
}

?>
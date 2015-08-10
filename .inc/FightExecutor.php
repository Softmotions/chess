<?php

require_once('./.inc/.chess/Board.php');

require_once('./.inc/.db/.manager/FightManager.php');

require_once('./.inc/Executor.php');

class FightExecutor extends Executor {

  /**
   * @var Board
   */
  private $board;

  public function doRequest (STemplate $template, Request $request) {
    $template->setOutput('init.xtpl');

    if (strlen($request->getIAction()) > 0) {
      switch ($request->getIAction()) {
        case 'active': // выдам список активных партий
          $template->setOutput('active.xtpl');

          $this->loadActiveFights($template);

          return;
          break;
      }
    }

    if (!$request->hasAttribute('fightId')) {
      $template->assign('error', array('Не указана партия'));
      return;
    }

    $this->board = FightManager::loadFight($request->getAttribute('fightId'), $this->user->getId());
    if (!isset($this->board)) {
      $template->assign('error', array('Не найдена партия'));
      return;
    }

    FightManager::updateAttached($this->board->getId(), $this->board->getRPlayer() == 0, true);

    $template->assign_by_ref('board', $this->board);

    $fig_types = array(
      'Queen' => 'Ферзь',
      'Castle' => 'Ладья',
      'Bishop' => 'Слон',
      'Knight' => 'Конь'
    );

    $template->assign('fig_types', $fig_types);
    if (null != $request->getAttribute('rpawn')) {
      $rpawn = $request->getAttribute('rpawn');
      $_SESSION['rpawn'] = $rpawn;
      $template->assign('rpawn', $rpawn);
    } else {
      $template->assign('rpawn', $_SESSION['rpawn']);
    }

    if ('exit' == $request->getAction()) {
      if ($this->board->isActive() == 1) {
        $template->assign('message', 'Бой ещё идёт, низя никуда уходить =)');
      } else {
        FightManager::updateAttached(
          $this->board->getId(),
          $this->board->getRPlayer() == 0,
          false
        );

        $this->board = FightManager::loadFight($this->board->getId(), $this->user->getId());
        $template->assign_by_ref('board', $this->board);
        return;
      }
    }

    if ($this->board->getRPlayer() == $this->board->getPlayer()) {
      $this->board->addTurn();

      switch ($request->getAction()) {
        case 'defeate':

          $this->board->swapPlayer();

          FightManager::finishFight($this->board->getId(), $this->board->getPlayer()); // релоад партии
          $this->board = FightManager::loadFight($this->board->getId(), $this->user->getId());
          $this->board->swapPlayer();
          $template->assign_by_ref('board', $this->board);
          return;
          break;

        case 'smove':
        case 'move':
        case 'kill':
        case 'skill':
        case 'lrok':
        case 'srok':

          $sx = $request->getAttribute('sx');
          $sy = $request->getAttribute('sy');
          $tx = $request->getAttribute('tx');
          $ty = $request->getAttribute('ty');

          $move = null;

          if (!$this->board->isCell($sx, $sy)) {
            $template->assign('message', 'Хм. тут нету фигуры!!!');
            break;
          }

          $figure = $this->board->getCell($sx, $sy);
          if ($figure->getOwner() != $this->board->getRPlayer()) {
            $template->assign('message', 'Фигура не твоя!!!!!');
            break;
          }

          if ('lrok' != $request->getAction() && 'srok' != $request->getAction()) {
            $moves = $figure->getAvailableMoves($this->board);

            $this->board->registerSMove(0);
            foreach ($moves as $item) {
              if ($item['x'] == $tx && $item['y'] == $ty) {
                $move = $item;
                break;
              }
            }

            if ($move) {
              $kfigure = $this->board->moveFigure($figure, $tx, $ty);
            } else {
              $template->assign('message', 'Ход не допустим!');
              break;
            }
          } else {
            $this->board->registerSMove(0);

            $tmp = array();
            if ('lrok' == $request->getAction()) {
              $tmp = array(3, 4, 5);
            } else {
              $tmp = array(5, 6, 7);
            }

            $available = true;
            $moves = $this->getAvailableMoves($this->board, $this->board->getRPFigures(true), true);
            foreach ($moves as $move) {
              foreach ($move['moves'] as $item) {
                if ($item['x'] == $figure->getX() && in_array($item['y'], $tmp)) {
                  $available = false;
                  break 2;
                }
              }
            }

            if (!$available) {
              $this->board = FightManager::loadFight($this->board->getId(), $this->user->getId());
              $template->assign_by_ref('board', $this->board);

              break;
            }

            if ('lrok' == $request->getAction()) {
              $sfigure = $this->board->getCell($figure->getX(), 1);
              $this->board->moveFigure($figure, $figure->getX(), 3);
              $this->board->moveFigure($sfigure, $figure->getX(), 4);
            } else {
              $sfigure = $this->board->getCell($figure->getX(), 8);
              $this->board->moveFigure($figure, $figure->getX(), 7);
              $this->board->moveFigure($sfigure, $figure->getX(), 6);
            }
          }

          if ($figure->getType() == 1) {
            $this->board->flushLRok($this->board->getRPlayer());
            $this->board->flushSRok($this->board->getRPlayer());
          } else if ($figure->getType() == 6) {
            $this->board->updateATurn();

            if ('smove' == $request->getAction()) {
              $this->board->registerSMove($sy);
            } else if ('skill' == $request->getAction()) {
              $kfigure = $this->board->moveFigure($figure, $sx, $ty);
              $this->board->moveFigure($figure, $tx, $ty);
            }

            if ($figure->getX() == 1 && $figure->getOwner() == 1) {
              $this->board->createFigure($rpawn, 1, $figure->getX(), $figure->getY());
            }
            if ($figure->getX() == 8 && $figure->getOwner() == 0) {
              $this->board->createFigure($rpawn, 0, $figure->getX(), $figure->getY());
            }
          } else if ($figure->getType() == 3) {
            if ($sy == 1) {
              $this->board->flushLRok($this->board->getRPlayer());
            } else if ($sy == 8) {
              $this->board->flushSRok($this->board->getRPlayer());
            }
          }

          if ($kfigure) {
            $this->board->updateATurn();
          }

          if ($this->checkCheck($this->board, $this->board->getRPFigures(true), true)) {
            $this->board = FightManager::loadFight($this->board->getId(), $this->user->getId());
            $template->assign_by_ref('board', $this->board);

            break;
          }

          $nill = 0;
          if ($this->board->getPlayer() == 1 && ($this->board->getTurn() - $this->board->getATurn() > 99)) {
            $nill = 5;
          }

          FightManager::saveFightLog($this->board);

          if ($nill == 0 && FightManager::check3StateRepeate($this->board->getId())) {
            $nill = 6;
          }

          $this->board->swapPlayer();

          FightManager::saveFight($this->board);

          if ($nill > 0) {
            FightManager::finishFight($this->board->getId(), $nill);
            $this->board = FightManager::loadFight($this->board->getId(), $this->user->getId());
            $template->assign_by_ref('board', $this->board);

            return;
          }

          if ($this->checkCheck($this->board, $this->board->getRPFigures(), false)) {
            if ($this->checkMate($this->board, true)) {
              FightManager::finishFight($this->board->getId(), $this->board->getRPlayer());
              $this->board = FightManager::loadFight($this->board->getId(), $this->user->getId());
              $template->assign_by_ref('board', $this->board);
              return;
            }
          }

          $moves = $this->getAvailableMoves($this->board, $this->board->getRPFigures(true), true, true);
          $count = 0;
          foreach ($moves as $move) {
            foreach ($move['moves'] as $item) {
              ++$count;
            }
          }

          if ($count == 0) {
            FightManager::finishFight($this->board->getId(), 3);
            $this->board = FightManager::loadFight($this->board->getId(), $this->user->getId());
            $template->assign_by_ref('board', $this->board);
            return;
          }

          if (!$this->canMate($this->board->getRPFigures()) && !$this->canMate($this->board->getRPFigures(true))) {
            FightManager::finishFight($this->board->getId(), 4);
            $this->board = FightManager::loadFight($this->board->getId(), $this->user->getId());
            $template->assign_by_ref('board', $this->board);
            return;
          }

        default:
          break;
      }
    }

    if ($this->board->getRPlayer() == $this->board->getPlayer() && $this->board->isActive() == 1) {
      $moves = $this->getAvailableMoves($this->board, $this->board->getRPFigures(), false, true);

      $count = 0;
      foreach ($moves as $move) {
        foreach ($move['moves'] as $item) {
          ++$count;
        }
      }

      if ($count == 0) {
        FightManager::finishFight($this->board->getId(), 3);
        $this->board = FightManager::loadFight($this->board->getId(), $this->user->getId());
        $template->assign_by_ref('board', $this->board);
        return;
      }

      $template->assign('moves', $moves);

      if ($this->checkCheck($this->board, $this->board->getRPFigures(true), true)) {
        $template->assign('check', true);
      }
    }

    return;
  }

  public function getAvailableMoves ($board, $figures, $shift, $checkKing = false) {
    $result = array();

    foreach ($figures as $figure) {
      $tmoves = $figure->getAvailableMoves($board);
      $moves =
          array(
          );
      if ($checkKing) {
        $br2 = new Board();

        $br2->setRPlayer($board->getRPlayer());

        foreach ($tmoves as $item) {
          $br2->loadShortDump($board->shortDump());
          $br2->registerSmove($board->getSMove());

          $tfigure = $br2->getCell($figure->getX(), $figure->getY());

          $check = true;
          if ('lrok' != $item['type'] && 'srok' != $item['type']) {
            $rfigure = $br2->moveFigure($tfigure, $item['x'], $item['y']);
            if ('skill' == $item['type']) {
              $rfigure = $br2->moveFigure($tfigure, $item['start']['x'], $item['y']);
              $br2->moveFigure($tfigure, $item['x'], $item['y']);
            }

            $rmoves = $this->getAvailableMoves($br2, $br2->getRPFigures(!$shift), !$shift, false);
            foreach ($rmoves as $rmove) {
              foreach ($rmove['moves'] as $ritem) {
                if ($ritem['type'] == 'kill') {
                  $yfigure = $br2->getCell($ritem['x'], $ritem['y']);
                  if ($yfigure->getType() == 1) {
                    $check = false;
                    break 2;
                  }
                }
              }
            }
          } else {
            if ('lrok' == $item['type']) {
              $tmp = array(3, 4, 5);
            } else {
              $tmp = array(5, 6, 7);
            }

            $rmoves = $this->getAvailableMoves($br2, $br2->getRPFigures(!$shift), !$shift, false);
            foreach ($rmoves as $rmove) {
              foreach ($rmove['moves'] as $ritem) {
                if ($ritem['x'] == $figure->getX() && in_array($ritem['y'], $tmp)) {
                  $check = false;
                  break 2;
                }
              }
            }
          }

          if ($check) {
            $moves[] = $item;
          }
        }
      } else {
        $moves = $tmoves;
      }

      $result[] = array('start' => $figure->getXY(), 'moves' => $moves);
    }

    return $result;
  }

  public function checkCheck ($board, $figures, $shift) {
    $moves = $this->getAvailableMoves($board, $figures, $shift);
    foreach ($moves as $move) {
      foreach ($move['moves'] as $item) {
        if ($item['type'] == 'kill') {
          $figure = $board->getCell($item['x'], $item['y']);
          if ($figure->getType() == 1) {
            return true;
          }
        }
      }
    }

    return false;
  }

  public function checkMate ($board, $shift) {
    $br2 = new Board();

    $br2->setRPlayer($board->getRPlayer());

    $moves = $this->getAvailableMoves($board, $board->getRPFigures($shift), $shift);
    foreach ($moves as $move) {
      foreach ($move['moves'] as $item) {
        $br2->loadShortDump($board->shortDump());
        $br2->registerSmove($board->getSMove());
        $figure = $br2->getCell($move['start']['x'], $move['start']['y']);
        $rfigure = $br2->moveFigure($figure, $item['x'], $item['y']);
        if ('skill' == $item['type']) {
          $rfigure = $br2->moveFigure($figure, $move['start']['x'], $item['y']);
          $br2->moveFigure($figure, $item['x'], $item['y']);
        }

        if (!$this->checkCheck($br2, $br2->getRPFigures(!$shift), !$shift)) {
          return false;
        }
      }
    }

    return true;
  }

  public function canMate ($figures) {

    $bishco = 0;
    $bishop = -1;
    $knight = 0;

    foreach ($figures as $figure) {
      if ($figure->getType() == 6 || $figure->getType() == 2 || $figure->getType() == 3) {
        return true;
      }

      if ($figure->getType() == 1) {
        continue;
      }

      if ($figure->getType() == 4) {
        if ($bishop > -1 || $knight > 0) {
          return true;
        }

        ++$knight;
      } else {
        $bt = ($figure->getX() + $figure->getY()) % 2;
        if ($knight > 0) {
          return true;
        }

        if (($bishop > -1 && $bishop != $bt) || $bishco > 1) {
          return true;
        }

        ++$bishco;
        $bishop = $bt;
      }
    }

    return false;
  }

  /**
   * @param STemplate $template
   */
  private function loadActiveFights (STemplate $template) {
    $fights = FightManager::getActiveFights();

    $template->assign('fights', $fights);
  }
}


?>
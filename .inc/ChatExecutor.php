<?php

require_once('./.inc/Executor.php');

require_once('./.inc/.db/.manager/ChatManager.php');

class ChatExecutor extends Executor {

  public function doRequest (STemplate $template, Request $request) {
    $key = intval($request->getAttribute('key'));
    $template->assign('key', $key);

    if (strlen($request->getIAction()) > 0) {
      switch ($request->getIAction()) {
        case 'init':
          if ($key == 0) {
            $template->assign('last', ChatManager::getLastMessageId($key));
          } else {
            $template->assign('last', 0);
          }

          $template->setOutput('init.xtpl');
          return;
      }
    }

    switch ($request->getAction()) {
      case 'send':
        $message = trim($request->getAttribute('message'));
        $time = mktime();

        if (strlen($message) > 0) {
          ChatManager::postMessage($message, $time, $this->user->getId(), $key);
        }

      case 'list':
      default:
        $last = intval($request->getAttribute('last'));

        $messages = ChatManager::listMessages($last, $key);
        $template->assign('messages', $messages);

        $template->setOutput('messages.jtpl');

        break;
    }
  }
}

?>
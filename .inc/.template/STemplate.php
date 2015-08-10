<?php

/** @noinspection PhpIncludeInspection */
require_once(SMARTY_DIR . 'Smarty.class.php');

class STemplate extends Smarty {

  private $_output;

  private $_nameddirs;

  public function __construct () {

    $this->Smarty();

    $this->_nameddirs = array();

    $this->template_dir = CFG_TEMPLATES_DIR;
    $this->config_dir = CFG_CONFIGS_DIR;
    $this->compile_dir = CFG_COMPILE_DIR;
    $this->cache_dir = CFG_CACHE_DIR;

    $this->use_sub_dirs = true;

    $this->force_compile = true;
  }

  public function expandDirs ($dir) {
    if (!isset($dir)) {
      return;
    }

    if (ereg('^[a-zA-Z0-9_\.-]+$', $dir)) {
      $this->template_dir .= $dir . '/';
      $this->config_dir .= $dir . '/';
    }
  }

  public function markDirs ($mark) {
    if (!isset($mark) || empty($mark)) {
      return;
    }

    $this->_nameddirs[$mark] = array(
      'template_dir' => $this->template_dir,
      'config_dir' => $this->config_dir
    );
  }

  public function swapDirs ($mark) {
    if (!isset($mark) || empty($mark) || !isset($this->_nameddirs[$mark])) {
      return false;
    }

    $this->template_dir = $this->_nameddirs[$mark]['template_dir'];
    $this->config_dir = $this->_nameddirs[$mark]['config_dir'];

    return true;
  }

  public function setOutput ($_output) {
    if (!isset($_output) || empty($_output)) {
      return;
    }

    $this->_output = $_output;
  }

  public function show ($cache_id = null, $compile_id = null) {
    if (!isset($this->_output)) {
      throw new Exception('Template not initialized!');
    }

    $this->display($this->_output, $cache_id, $compile_id);
  }
}


function insert_header ($parameters) {
  if (empty($parameters['content'])) {
    return;
  }

  header($parameters['content']);
  return;
}

function insert_xml_header ($parameters) {
  header('Content-Type: text/xml; charset=UTF-8');
  echo '<?xml version="1.0" encoding="UTF-8"?>';
  return;
}

function insert_json_header ($parameters) {
  header('Content-Type: application/json; charset=UTF-8');
  return;
}

function insert_json_eval($parameters) {
  if (isset($parameters['header']) && $parameters['header'] == 'true') {
    insert_json_header($parameters);
  }

  echo json_encode($parameters['data']);
}

?>
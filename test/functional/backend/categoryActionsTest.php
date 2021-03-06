<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new sfTestFunctional(new sfBrowser());

$browser->
  get('/category/index')->

  with('request')->begin()->
    isParameter('module', 'category')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(401)->
    checkElement('body', '!/This is a temporary page/')->
  end()
;

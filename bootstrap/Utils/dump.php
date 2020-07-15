<?php
  namespace RamowkaTvRepublika\Bootstrap\Utils;
  
  function dump($whatever) {
    http_response_code(500);
    print("<pre>".print_r($whatever,true)."</pre>");
  }
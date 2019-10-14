<?php
require __DIR__.'/../vendor/autoload.php';

$loop = Carica\Io\React\LoopAdapter::get();

$loop->setInterval(
  static function() {
    static $i = 0;
    echo 'C', ++$i, ' ';
  },
  2000
);

$loop->getReactLoop()->addPeriodicTimer(
  3,
  static function() {
    static $i = 0;
    echo 'R', ++$i, ' ';
  }
);

$loop->run();

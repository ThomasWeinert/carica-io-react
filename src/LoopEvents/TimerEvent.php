<?php
declare(strict_types=1);

namespace Carica\Io\React\LoopEvents {

  use Carica\Io\React\LoopAdapter;
  use Carica\Io\React\LoopEvent;
  use React\EventLoop\TimerInterface;

  class TimerEvent extends LoopEvent {

    private $_timer;

    public function __construct(LoopAdapter $loop, TimerInterface $timer) {
      parent::__construct($loop);
      $this->_timer = $timer;
    }

    public function remove(): void {
      $this->getLoop()->reactLoop()->cancelTimer($this->_timer);
    }
  }
}

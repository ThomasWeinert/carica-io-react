<?php
declare(strict_types=1);

namespace Carica\Io\React {

  use Carica\Io\Event\Loop as EventLoop;
  use Carica\Io\Event\Loop\Listener as EventLoopListener;

  abstract class LoopEvent implements EventLoopListener {

    /**
     * @var LoopAdapter
     */
    private $_loop;

    /**
     * @param LoopAdapter $loop
     */
    public function __construct(LoopAdapter $loop) {
      $this->_loop = $loop;
    }

    /**
     * @return EventLoop|LoopAdapter
     */
    public function getLoop(): EventLoop {
      return $this->_loop;
    }
  }
}

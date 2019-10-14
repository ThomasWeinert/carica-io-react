<?php
declare(strict_types=1);

namespace Carica\Io\React\LoopEvents {

  use Carica\Io\React\LoopAdapter;
  use Carica\Io\React\LoopEvent;

  class StreamReadEvent extends LoopEvent {

    private $_stream;

    public function __construct(LoopAdapter $loop, $stream) {
      parent::__construct($loop);
      $this->_stream = $stream;
    }

    public function remove(): void {
      $this->getLoop()->reactLoop()->removeReadStream($this->_stream);
    }
  }
}

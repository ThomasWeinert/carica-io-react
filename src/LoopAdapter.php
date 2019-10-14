<?php
declare(strict_types=1);

namespace Carica\Io\React {

  use Carica\Io;
  use Carica\Io\Event\Loop as EventLoop;
  use Carica\Io\Event\Loop\Listener as EventListener;
  use React\EventLoop as ReactEventLoop;
  use React\EventLoop\LoopInterface as ReactEventLoopInterface;

  class LoopAdapter implements EventLoop {

    /**
     * @var ReactEventLoop\LoopInterface
     */
    private $_loop;

    public function __construct(ReactEventLoop\LoopInterface $loop) {
      $this->_loop = $loop;
    }

    public static function create(ReactEventLoop\LoopInterface $loop = NULL): EventLoop {
      return new self(
        $loop ?? ReactEventLoop\Factory::create()
      );
    }

    /**
     * @return EventLoop|self
     */
    public static function get(): EventLoop {
      return EventLoop\Factory::get(
        static function() {
          return self::create();
        }
      );
    }

    public function getReactLoop(): ReactEventLoop\LoopInterface {
      return $this->_loop;
    }

    /**
     * @param callable $callback
     * @param int $milliseconds
     * @return EventListener
     */
    public function setTimeout(callable $callback, int $milliseconds): EventListener {
      $timer = $this->_loop->addTimer($milliseconds / 1000, $callback);
      return new LoopEvents\StreamReadEvent($this, $timer);
    }

    /**
     * @param callable $callback
     * @param int $milliseconds
     * @return EventListener
     */
    public function setInterval(callable $callback, int $milliseconds): EventListener {
      $timer = $this->_loop->addPeriodicTimer($milliseconds / 1000, $callback);
      return new LoopEvents\StreamReadEvent($this, $timer);
    }

    /**
     * @param callable $callback
     * @param resource $stream
     * @return EventListener
     * @throws \Exception
     */
    public function setStreamReader(callable $callback, $stream): EventListener {
      $this->_loop->addReadStream($stream, $callback);
      return new LoopEvents\StreamReadEvent($this, $stream);
    }

    public function remove(EventListener $listener): void {
      $listener->remove();
    }

    public function run(Io\Deferred\Promise $for = NULL): void {
      $loop = $this->_loop;
      if (isset($for) && $for->state() === Io\Deferred::STATE_PENDING) {
        $for->always(
          static function () use ($loop) {
            $loop->stop();
          }
        );
      }
      $loop->run();
    }

    public function stop(): void {
      $this->_loop->stop();
    }
  }
}

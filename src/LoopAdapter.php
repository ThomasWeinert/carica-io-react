<?php
declare(strict_types=1);

namespace Carica\Io\React {

  use Carica\Io;
  use Carica\Io\Deferred\Promise;
  use Carica\Io\Event\Loop as EventLoop;
  use Carica\Io\Event\Loop\Listener as EventListener;
  use Exception;
  use React\EventLoop as ReactEventLoop;
  use React\EventLoop\LoopInterface as ReactEventLoopInterface;

  class LoopAdapter implements EventLoop {

    /**
     * @var ReactEventLoopInterface
     */
    private $_loop;

    /**
     * @param ReactEventLoopInterface $loop
     */
    public function __construct(ReactEventLoopInterface $loop) {
      $this->_loop = $loop;
    }

    /**
     * @param ReactEventLoopInterface|NULL $loop
     * @return EventLoop
     */
    public static function create(ReactEventLoopInterface $loop = NULL): EventLoop {
      return new self(
        $loop ?? ReactEventLoop\Factory::create()
      );
    }

    /**
     * @return EventLoop|self
     */
    public static function get(): EventLoop {
      return EventLoop\Factory::get(
        static function() { return self::create(); }
      );
    }

    public function getReactLoop(): ReactEventLoopInterface {
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
     * @throws Exception
     */
    public function setStreamReader(callable $callback, $stream): EventListener {
      $this->_loop->addReadStream($stream, $callback);
      return new LoopEvents\StreamReadEvent($this, $stream);
    }

    /**
     * @param EventListener $listener
     */
    public function remove(EventListener $listener): void {
      $listener->remove();
    }

    /**
     * @param Promise|NULL $for
     */
    public function run(Promise $for = NULL): void {
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

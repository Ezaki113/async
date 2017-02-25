<?php

final class UV
{
    public const RUN_DEFAULT = 0;
    public const RUN_ONCE = 1;
    public const RUN_NOWAIT = 2;
}

/**
 * @return resource
 */
function uv_loop_new() {};

/**
 * @var resource $loop
 * @return void
 */
function uv_loop_delete($loop) : void {};

/**
 * @param resource $loop
 * @param int $runMode
 * @return void
 */
function uv_run($loop, int $runMode) : void {};

/**
 * @param resource $loop
 * @return resource
 */
function uv_timer_init($loop) {};

/**
 * @param resource $timer
 * @param int $timeout
 * @param int $period
 * @param callable $closure
 */
function uv_timer_start($timer, int $timeout, int $period, callable $closure) : void {};

/**
 * @param resource $timer
 * @return void
 */
function uv_timer_stop($timer) : void {};

/**
 * @param resource $handler
 * @return void
 */
function uv_unref($handler) : void {};

/**
 * @param resource $handler
 * @return void
 */
function uv_ref($handler) : void {};

/**
 * @param resource $loop
 * @return void
 */
function uv_stop($loop) : void {};

/**
 * @var resource $loop
 * @return resource
 */
function uv_signal_init($loop) {};

/**
 * @param resource $signal
 * @param callable $callback
 * @param int $signum
 * @return void
 */
function uv_signal_start($signal, callable $callback, int $signo) : int {};

/**
 * @param resource $signal
 * @return int
 */
function uv_signal_stop($signal) : int {};

/**
 * @return float
 */
function uv_hrtime() : float {};

/**
 * @param resource $loop
 * @param callable $callback
 * @return resource
 */
function uv_async_init($loop, callable $callback) {};

/**
 * @param resource $async
 * @return void
 */
function uv_async_send($async) : void {};

/**
 * @param resource $handler
 * @param callable $callback
 * @return void
 */
function uv_close($handler, ?callable $callback = null) : void {};

/**
 * @param resource $stream
 * @param string $data
 * @param ?callable<resource, int> $callback with stream and status
 */
function uv_write($stream, string $data, ?callable $callback = null) {};

/**
 * @param resource $loop
 * @param bool $ipc
 * @return resource
 */
function uv_pipe_init($loop, bool $ipc) {};

/**
 * @param resource $handler
 * @param int $fd
 * @return void
 */
function uv_pipe_open($handler, $fd) : void {};

/**
 * @param resource $handler
 * @param callable $callback
 */
function uv_read_start($handler, callable $callback) : void {};

/**
 * @param resource $handler
 */
function uv_read_stop($handler) : void {};

/**
 * @param resource $loop
 */
function uv_tcp_init($loop) {};

/**
 * @param resource $tcp handler
 * @param resource $sockaddr
 */
function uv_tcp_bind($tcp, $sockaddr) : void {};

/**
 * @param string $address
 * @param int $port
 * @return resource
 */
function uv_ip4_addr(string $address, int $port) {};

/**
 * @param string $address
 * @param int $port
 * @return resource
 */
function uv_ip6_addr(string $address, int $port) {};

/**
 * @param resource $handle
 * @param int $backlog
 * @param callable $callback
 * @return void
 */
function uv_listen($handle, int $backlog, callable $callback) : void {};

/**
 * @param resource $server
 * @param resource $client
 */
function uv_accept($server, $client) {};

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
 * @return void
 */
function uv_close($handler) : void {};

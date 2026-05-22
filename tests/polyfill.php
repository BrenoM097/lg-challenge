<?php

namespace GuzzleHttp\Promise;

/* POLYFILL
 * Guzzle 2.0 removeu a função promise_for, porem o Laravel 7 ainda precisa dela.
 * Quando o laravel for tentar achar ela, esse codigo retorna o metodo correspondente na nova versão do guzzle
 */
if (!function_exists('GuzzleHttp\Promise\promise_for')) {
    function promise_for($value) {
        return \GuzzleHttp\Promise\Create::promiseFor($value);
    }
}
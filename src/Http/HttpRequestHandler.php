<?php
declare (strict_types = 1);

namespace Async\Http;

interface HttpRequestHandler
{
    public function serve(HttpRequest $request) : HttpResponse;
}

<?php

namespace App\Enums;

enum HttpStatusCode: int
{
    case CREATED = 201;
    case SUCCESSFUL = 200;
    case BAD_REQUEST = 400;
    case UNAUTHENTICATED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case VALIDATION_ERROR = 422;
    case SERVER_ERROR = 500;
    case REDIRECTION = 302;
}

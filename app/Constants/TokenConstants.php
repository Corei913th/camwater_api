<?php

namespace App\Constants;

class TokenConstants
{
  public const DEFAULT_ACCESS_TOKEN_NAME = 'auth_token';
  public const DEFAULT_REFRESH_TOKEN_NAME = 'refresh_token';

  public const DEFAULT_ACCESS_TOKEN_EXPIRY_MINUTES = 60;
  public const DEFAULT_REFRESH_TOKEN_EXPIRY_DAYS = 30;

  public const TOKEN_TYPE = 'Bearer';

  public const ABILITY_ALL = '*';
  public const ABILITY_REFRESH = 'refresh';
}

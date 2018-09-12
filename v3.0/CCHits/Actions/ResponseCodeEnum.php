<?php
/**
 *     CCHits. Where you make the charts.
 *     Copyright (C) 2018  CCHits.net
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

namespace CCHits\Actions;


class ResponseCodeEnum
{
    const SUCCESS = 0; // Success (or success with warning)
    const EXCEPTION = 1; // A generic not well identificated error occurred
    const HTTP_METHOD = 2; // The received http method is not supported for this method
    const TYPE = 3; // One of the received parameters has a value not respecting requirements such as type, range, format, etc
    const REQUIRED_PARAMETER = 4; // A required parameter was not been received, or it was empty
    const INVALID_CLIENT_ID = 5; // The client Id received does not exists or cannot be validated
    const RATE_LIMIT_EXCEEDED = 6; // This requester app or the requester IP have exceeded the permitted rate limit
    const METHOD_NOT_FOUND = 7; // This exception is raised when entity and/or subentity methods don't exist
    const NEED_PARAMETER = 8; // A parameter needed because of an imposed local condition was not received or/and has not the needed value
    const FORMAT = 9; // This exception is raised when the api call requests an unkown output format
    const ENTRY_POINT = 10; // The used IP and/or port is not recognized as valid entry point
    const SUSPENDED_APPLICATION = 11; // The client application has been suspended (illegal usage, ...)
    const ACCESS_TOKEN = 12; // Invalid Access Token.
    const INSUFFICIENT_SCOPE = 13; // Insufficient scope. The request requires higher privileges than provided by the access token
    const INVALID_USER = 21; // Some parameters of User is not valid.
    const EMAIL_ALREADY_EXIST = 22; // The email is already used by another user.
    const DUPLICATE_VALUE = 23; // This error is raised when a client tries to write or update a value which already exists and cannot be duplicated
    const INVALID_PLAYLIST = 24; // Check your playlist id.
    const ACCESS_CODE = 101; // Please check if you have correctly typed in your access code and if your subscription is still marked as being active in your client space.
}
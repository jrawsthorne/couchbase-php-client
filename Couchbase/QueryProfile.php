<?php

/**
 * Copyright 2014-Present Couchbase, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace Couchbase;

/**
 * Set of values for setting the profile mode of a query.
 */
interface QueryProfile
{
    /**
     * Set profiling to off
     */
    public const OFF = 1;

    /**
     * Set profiling to include phase timings
     */
    public const PHASES = 2;

    /**
     * Set profiling to include execution timings
     */
    public const TIMINGS = 3;
}

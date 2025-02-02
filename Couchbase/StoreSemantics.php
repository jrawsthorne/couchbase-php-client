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
 * An object which contains how to define the document level action to take
 * during a MutateIn operation.
 *
 * @deprecated
 * @see MutateInOptions::STORE_SEMANTICS_INSERT
 * @see MutateInOptions::STORE_SEMANTICS_UPSERT
 * @see MutateInOptions::STORE_SEMANTICS_REPLACE
 */
interface StoreSemantics
{
    /**
     * Replace the document, and fail if it does not exist.
     * @deprecated
     * @see MutateInOptions::STORE_SEMANTICS_REPLACE
     */
    public const REPLACE = 0;

    /**
     * Replace the document or create it if it does not exist.
     * @deprecated
     * @see MutateInOptions::STORE_SEMANTICS_UPSERT
     */
    public const UPSERT = 1;

    /**
     * Create the document or fail if it already exists.
     * @deprecated
     * @see MutateInOptions::STORE_SEMANTICS_INSERT
     */
    public const INSERT = 2;
}

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

use JsonSerializable;

/**
 * A FTS query that allows for simple matching on a given prefix.
 */
class PrefixSearchQuery implements JsonSerializable, SearchQuery
{
    private string $prefix;
    private ?float $boost;
    private ?string $field;

    /**
     * @param string $prefix
     */
    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Sets the boost for this query.
     *
     * @param float $boost the boost value to use.
     * @return PrefixSearchQuery
     * @since 4.0.0
     */
    public function boost(float $boost): PrefixSearchQuery
    {
        $this->boost = $boost;
        return $this;
    }

    /**
     * Sets the field for this query.
     *
     * @param string $field the field to use.
     * @return PrefixSearchQuery
     * @since 4.0.0
     */
    public function field(string $field): PrefixSearchQuery
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @private
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        return PrefixSearchQuery::export($this);
    }

    /**
     * @private
     */
    public static function export(PrefixSearchQuery $query): array
    {
        $json = [
            'prefix' => $query->prefix
        ];
        if ($query->boost != null) {
            $json['boost'] = $query->boost;
        }
        if ($query->field != null) {
            $json['field'] = $query->field;
        }

        return $json;
    }
}

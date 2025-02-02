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

class DecrementOptions
{
    private ?int $timeoutMilliseconds = null;
    private ?string $durabilityLevel = null;
    private ?int $durabilityTimeoutSeconds = null;
    private int $delta = 1;
    private ?int $initialValue = null;

    /**
     * Static helper to keep code more readable
     *
     * @return DecrementOptions
     * @since 4.0.0
     */
    public static function build(): DecrementOptions
    {
        return new DecrementOptions();
    }

    /**
     * Sets the value to increment the counter by.
     *
     * @param int $decrement the value to increment by
     * @return DecrementOptions
     * @since 4.0.0
     */
    public function delta(int $decrement): DecrementOptions
    {
        $this->delta = $decrement;
        return $this;
    }

    /**
     * Sets the value to initialize the counter to if the document does
     * not exist.
     *
     * @param int $initialValue the initial value to use if counter does not exist
     * @return DecrementOptions
     * @since 4.0.0
     */
    public function initial(int $initialValue): DecrementOptions
    {
        $this->initialValue = $initialValue;
        return $this;
    }

    /**
     * Sets the operation timeout in milliseconds.
     *
     * @param int $milliseconds the operation timeout to apply
     * @return DecrementOptions
     * @since 4.0.0
     */
    public function timeout(int $milliseconds): DecrementOptions
    {
        $this->timeoutMilliseconds = $milliseconds;
        return $this;
    }

    /**
     * Sets the durability level to enforce when writing the document.
     *
     * @param string $level the durability level to enforce
     * @param int|null $timeoutSeconds
     * @return DecrementOptions
     * @since 4.0.0
     */
    public function durabilityLevel(string $level, ?int $timeoutSeconds): DecrementOptions
    {
        $this->durabilityLevel = $level;
        $this->durabilityTimeoutSeconds = $timeoutSeconds;
        return $this;
    }

    /**
     * @private
     * @param DecrementOptions|null $options
     * @return array
     * @since 4.0.0
     */
    public static function export(?DecrementOptions $options): array
    {
        if ($options == null) {
            return [];
        }
        return [
            'timeoutMilliseconds' => $options->timeoutMilliseconds,
            'durabilityLevel' => $options->durabilityLevel,
            'durabilityTimeoutSeconds' => $options->durabilityTimeoutSeconds,
            'delta' => $options->delta,
            'initialValue' => $options->initialValue,
        ];
    }
}

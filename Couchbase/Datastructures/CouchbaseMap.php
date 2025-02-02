<?php

declare(strict_types=1);

/*
 *   Copyright 2020-2021 Couchbase, Inc.
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

namespace Couchbase\Datastructures;

use ArrayAccess;
use ArrayIterator;
use Couchbase\Collection;
use Couchbase\Exception\DocumentNotFoundException;
use Couchbase\Exception\PathMismatchException;
use Couchbase\LookupCountSpec;
use Couchbase\LookupExistsSpec;
use Couchbase\LookupGetSpec;
use Couchbase\MutateInOptions;
use Couchbase\MutateRemoveSpec;
use Couchbase\MutateUpsertSpec;
use Countable;
use EmptyIterator;
use IteratorAggregate;
use OutOfBoundsException;
use Traversable;

/**
 * Implementation of the List backed by JSON document in Couchbase collection
 */
class CouchbaseMap implements Countable, IteratorAggregate, ArrayAccess
{
    private string $id;
    private Collection $collection;
    private Options\CouchbaseMap $options;

    /**
     * CouchbaseList constructor.
     * @param string $id identifier of the backing document.
     * @param Collection $collection collection instance, where the document will be stored
     * @param Options\CouchbaseMap|null $options
     * @since 4.0.0
     */
    public function __construct(string $id, Collection $collection, ?Options\CouchbaseMap $options = null)
    {
        $this->id = $id;
        $this->collection = $collection;
        if ($options) {
            $this->options = $options;
        } else {
            $this->options = new Options\CouchbaseMap(null, null, null, null);
        }
    }

    /**
     * @return int number of elements in the map
     * @since 4.0.0
     */
    public function count(): int
    {
        try {
            $result = $this->collection->lookupIn(
                $this->id,
                [new LookupCountSpec("")],
                $this->options->lookupInOptions()
            );
            if (!$result->exists(0)) {
                return 0;
            }
            return (int)$result->content(0);
        } catch (DocumentNotFoundException $ex) {
            return 0;
        }
    }

    /**
     * @return bool true if the map is empty
     * @since 4.0.0
     */
    public function empty(): bool
    {
        return $this->count() == 0;
    }

    /**
     * Retrieves array value for given offset.
     * @param string $key key of the entry to be retrieved
     * @return mixed the value or null
     * @since 4.0.0
     */
    public function get(string $key)
    {
        try {
            $result = $this->collection->lookupIn(
                $this->id,
                [new LookupGetSpec($key)],
                $this->options->lookupInOptions()
            );
            return $result->exists(0) ? $result->content(0) : null;
        } catch (DocumentNotFoundException $ex) {
            return null;
        }
    }

    /**
     * Insert or update given key with new value.
     * @param string $key key of the entry to be inserted/updated
     * @param mixed $value new value
     * @throws \Couchbase\Exception\InvalidArgumentException
     * @since 4.0.0
     */
    public function set(string $key, $value): void
    {
        $options = clone $this->options->mutateInOptions();
        $options->storeSemantics(MutateInOptions::STORE_SEMANTICS_UPSERT);
        $this->collection->mutateIn(
            $this->id,
            [new MutateUpsertSpec($key, $value, false, false, false)],
            $options
        );
    }

    /**
     * Remove entry by its key.
     * @param string $key key of the entry to remove
     * @throws OutOfBoundsException if the index does not exist
     * @since 4.0.0
     */
    public function delete(string $key): void
    {
        try {
            $result = $this->collection->mutateIn(
                $this->id,
                [new MutateRemoveSpec($key, false)],
                $this->options->mutateInOptions()
            );
        } catch (PathMismatchException $ex) {
            throw new OutOfBoundsException(sprintf("Key %s does not exist", $key));
        }
    }

    /**
     * Checks whether a key exists.
     * @param string $key key of the entry to check
     * @return bool true if there is an entry associated with the offset
     * @since 4.0.0
     */
    public function existsAt(string $key): bool
    {
        try {
            $result = $this->collection->lookupIn(
                $this->id,
                [new LookupExistsSpec($key)],
                $this->options->lookupInOptions()
            );
            return $result->exists(0);
        } catch (DocumentNotFoundException $ex) {
            return false;
        }
    }

    /**
     * Clears the map. Effectively it removes backing document, because missing document is an equivalent of the empty collection.
     * @since 4.0.0
     */
    public function clear(): void
    {
        try {
            $this->collection->remove($this->id, $this->options->removeOptions());
        } catch (DocumentNotFoundException $ex) {
            return;
        }
    }

    /**
     * Checks whether an offset exists.
     * Implementation of {@link ArrayAccess}.
     * @param mixed $key key of the entry to check
     * @return bool true if there is an entry associated with the offset
     * @since 4.0.0
     */
    public function offsetExists($key): bool
    {
        return $this->existsAt((string)$key);
    }

    /**
     * Retrieves array value for given offset.
     * Implementation of {@link ArrayAccess}.
     * @param mixed $offset offset of the entry to get
     * @return mixed the value or null
     * @since 4.0.0
     */
    public function offsetGet($key): mixed
    {
        return $this->get((string)$key);
    }

    /**
     * Assign a value to the specified offset.
     * Implementation of {@link ArrayAccess}.
     * @param mixed $key key of the entry to assign
     * @param mixed $value new value
     * @since 4.0.0
     */
    public function offsetSet($key, $value): void
    {
        $this->set((string)$key, $value);
    }

    /**
     * Unset an offset.
     * Implementation of {@link ArrayAccess}.
     * @param mixed $key key of the entry to remove
     * @throws OutOfBoundsException if the index does not exist
     * @since 4.0.0
     */
    public function offsetUnset($key): void
    {
        $this->delete((string)$key);
    }

    /**
     * Create new iterator to walk through the list.
     * Implementation of {@link IteratorAggregate}
     * @return Traversable iterator to enumerate elements of the list
     * @since 4.0.0
     */
    public function getIterator(): Traversable
    {
        try {
            $result = $this->collection->get($this->id);
            if ($result->content() == null) {
                return new EmptyIterator();
            }
            return new ArrayIterator($result->content());
        } catch (DocumentNotFoundException $ex) {
            return new EmptyIterator();
        }
    }
}

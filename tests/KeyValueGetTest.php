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

use Couchbase\Exception\DocumentNotFoundException;

include_once __DIR__ . "/Helpers/CouchbaseTestCase.php";

class KeyValueGetTest extends Helpers\CouchbaseTestCase
{
    function testGetThrowsDocumentNotFoundException()
    {
        $collection = $this->defaultCollection();
        $this->expectException(DocumentNotFoundException::class);
        $collection->get($this->uniqueId("foo"));
    }

    function testGetReturnsCorrectCas()
    {
        $id = $this->uniqueId();
        $collection = $this->defaultCollection();
        $res = $collection->upsert($id, ["answer" => 42]);
        $cas = $res->cas();
        $this->assertNotNull($cas);
        $res = $collection->get($id);
        $this->assertEquals($cas, $res->cas());
    }
}

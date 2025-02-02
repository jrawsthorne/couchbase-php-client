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

use Couchbase\LookupGetFullSpec;
use Couchbase\LookupInOptions;
use Couchbase\UpsertOptions;

include_once __DIR__ . "/Helpers/CouchbaseTestCase.php";

class KeyValueLockingTest extends Helpers\CouchbaseTestCase
{
    function testPessimisticLockingWorkflow()
    {
        $id = $this->uniqueId("foo");
        $collection = $this->defaultCollection();

        $res = $collection->upsert($id, ["foo" => "bar"]);
        $originalCas = $res->cas();

        $res = $collection->getAndLock($id, 5);
        $lockedCas = $res->cas();
        $this->assertNotEquals($originalCas, $lockedCas);
        $this->assertEquals(["foo" => "bar"], $res->content());

        $res = $collection->get($id);
        $this->assertNotEquals($originalCas, $res->cas());
        $this->assertNotEquals($lockedCas, $res->cas());

        $collection->unlock($id, $lockedCas);

        $res = $collection->get($id);
        $this->assertEquals($lockedCas, $res->cas());
    }
}

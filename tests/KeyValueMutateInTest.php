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
use Couchbase\Exception\PathNotFoundException;
use Couchbase\MutateArrayAppendSpec;
use Couchbase\MutateArrayInsertSpec;
use Couchbase\MutateArrayPrependSpec;
use Couchbase\MutateInOptions;
use Couchbase\MutateUpsertSpec;

include_once __DIR__ . "/Helpers/CouchbaseTestCase.php";

class KeyValueMutateInTest extends Helpers\CouchbaseTestCase
{
    function testSubdocumentMutateCanCreateDocument()
    {
        $id = $this->uniqueId("foo");
        $collection = $this->defaultCollection();

        $res = $collection->mutateIn(
            $id,
            [
                MutateUpsertSpec::build("foo", "bar")
            ],
            MutateInOptions::build()->storeSemantics(MutateInOptions::STORE_SEMANTICS_UPSERT)
        );
        $this->assertNotNull($res->cas());
        $cas = $res->cas();

        $res = $collection->get($id);
        $this->assertEquals($cas, $res->cas());
        $this->assertEquals(["foo" => "bar"], $res->content());
    }

    function testArrayOperationsFlattenArguments()
    {
        $id = $this->uniqueId("foo");
        $collection = $this->defaultCollection();

        $collection->upsert($id, ["foo" => [1, 2, 3]]);

        $collection->mutateIn($id, [MutateArrayAppendSpec::build("foo", [4])]);
        $res = $collection->get($id);
        $this->assertEquals(["foo" => [1, 2, 3, 4]], $res->content());

        $collection->mutateIn($id, [MutateArrayPrependSpec::build("foo", [0])]);
        $res = $collection->get($id);
        $this->assertEquals(["foo" => [0, 1, 2, 3, 4]], $res->content());

        $collection->mutateIn($id, [MutateArrayInsertSpec::build("foo[4]", [3.14])]);
        $res = $collection->get($id);
        $this->assertEquals(["foo" => [0, 1, 2, 3, 3.14, 4]], $res->content());
    }


    function testArrayOperationsExpectsArrayAsValueArgument()
    {
        $this->expectException(TypeError::class);
        MutateArrayAppendSpec::build("foo", 4);
    }

    function testSubdocumentMutateRaisesExceptions()
    {
        $id = $this->uniqueId("foo");
        $collection = $this->defaultCollection();

        $collection->upsert($id, ["foo" => ["value" => 3.14]]);
        $this->expectException(PathNotFoundException::class);
        $collection->mutateIn($id, [MutateUpsertSpec::build("foo.bar.baz", 42)]);
    }

    function testSubdocumentMutateRaisesExceptionIfDocumentDoesNotExist()
    {
        $collection = $this->defaultCollection();
        $this->expectException(DocumentNotFoundException::class);
        $collection->mutateIn($this->uniqueId("foo"), [MutateUpsertSpec::build("foo", 42)]);
    }
}

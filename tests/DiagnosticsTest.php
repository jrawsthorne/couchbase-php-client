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

include_once __DIR__ . "/Helpers/CouchbaseTestCase.php";

class DiagnosticsTest extends Helpers\CouchbaseTestCase
{
    function testDiagnostics()
    {
        $cluster = $this->connectCluster();
        $result = $cluster->diagnostics();

        $this->assertNotEmpty($result['id']);
        $this->assertNotEmpty($result['sdk']);
        $this->assertNotEmpty($result['services']);
        $this->assertArrayHasKey('kv', $result['services']);
        $this->assertNotEmpty($result['services']['kv']);
    }
}

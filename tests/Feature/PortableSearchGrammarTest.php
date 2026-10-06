<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PortableSearchGrammarTest extends TestCase
{
    public function test_ilike_is_compiled_as_like_on_sqlite(): void
    {
        $sql = DB::table('users')->where('name', 'ilike', '%fahmi%')->toSql();

        $this->assertStringContainsString('like ?', strtolower($sql));
        $this->assertStringNotContainsString('ilike', strtolower($sql));
    }
}

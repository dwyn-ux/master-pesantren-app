<?php

namespace App\Database\Query\Grammars;

use Illuminate\Database\Query\Grammars\SQLiteGrammar as BaseSQLiteGrammar;

class SQLiteGrammar extends BaseSQLiteGrammar
{
    use NormalizesIlike;
}

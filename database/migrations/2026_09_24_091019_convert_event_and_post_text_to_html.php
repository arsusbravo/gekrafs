<?php

use App\Support\RichText;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Event descriptions and post bodies are now written with a rich text editor and stored as HTML.
 * Convert the existing plain text so paragraphs and lists keep their layout.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['events' => 'description', 'posts' => 'body'] as $table => $column) {
            DB::table($table)->whereNotNull($column)->orderBy('id')->each(function ($row) use ($table, $column) {
                if (! RichText::isHtml($row->{$column})) {
                    DB::table($table)->where('id', $row->id)->update([$column => RichText::sanitize($row->{$column})]);
                }
            });
        }
    }

    public function down(): void
    {
        // Converting back to plain text would lose formatting; nothing to do.
    }
};

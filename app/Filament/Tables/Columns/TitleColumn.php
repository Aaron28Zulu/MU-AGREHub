<?php

declare(strict_types=1);

namespace App\Filament\Tables\Columns;

use Filament\Tables\Columns\TextColumn;

/**
 * Primary label of a table row (study title, call edition, event title).
 *
 * Holds a readable measure instead of collapsing to one word per line, clamps to two lines and
 * reveals the full text on hover, so long titles never stretch a row to six or seven lines.
 */
class TitleColumn extends TextColumn
{
    /**
     * Titles longer than this are likely clamped, so they get a tooltip with the full text.
     */
    protected const TOOLTIP_AFTER = 70;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->weight('medium')
            ->wrap()
            ->lineClamp(2)
            ->tooltip(fn (?string $state): ?string => mb_strlen((string) $state) > self::TOOLTIP_AFTER ? $state : null)
            ->extraAttributes(['class' => 'rris-col-title']);
    }
}

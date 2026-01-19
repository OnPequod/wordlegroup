<div style="width: 100%;">
    <div style="display: flex; width: 100%; align-items: center; border-bottom: 1px solid #f3f4f6;">
        <div style="width: 5rem; padding: 0.75rem 1.25rem; font-weight: 600;">#{{ $score->board_number }}</div>
        <div style="flex: 1; padding: 0.75rem 0; font-size: 0.875rem; color: #71717a; text-align: center;">{{ $score->date->format('M d, Y') }}</div>
        <div style="width: 5rem; padding: 0.75rem 1.25rem; font-weight: 600; text-align: right;">{{ $score->score === 7 ? 'X' : $score->score }}/6{{ $score->hard_mode ? '*' : '' }}</div>
    </div>
    <div class="flex justify-center items-center py-6 h-44">
        @if($score->board)
        <div class="whitespace-nowrap whitespace-pre font-board">{{ $score->board }}</div>
        @else
            <div class="text-sm text-gray-500">No board recorded.</div>
        @endif
    </div>
</div>

<div class="leading-tight">
@for($i = 1; $i <= $score->score; $i++)
<span class="inline-block w-4 h-4 m-0.5 rounded-sm bg-zinc-300"></span><span class="inline-block w-4 h-4 m-0.5 rounded-sm bg-zinc-300"></span><span class="inline-block w-4 h-4 m-0.5 rounded-sm bg-zinc-300"></span><span class="inline-block w-4 h-4 m-0.5 rounded-sm bg-zinc-300"></span><span class="inline-block w-4 h-4 m-0.5 rounded-sm bg-zinc-300"></span>@if($i < $score->score)<br>@endif
@endfor
</div>

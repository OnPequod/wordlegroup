<!-- This example requires Tailwind CSS v2.0+ -->
<div class="flex flex-col">
    <div class="overflow-x-auto -my-2 sm:-mx-6 lg:-mx-8">
        <div class="inline-block py-2 min-w-full align-middle sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                    <tr>
                        <th scope="col" class="py-1 pr-3 pl-1 w-16 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase sm:px-4 sm:py-2"><span class="inline sm:hidden">Place</span><span class="hidden sm:inline">Place</span></th>
                        <th scope="col" class="px-1 py-1 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase sm:px-4 sm:py-2">Name</th>
                        <th scope="col" class="px-2 py-1 text-xs font-semibold tracking-wider text-left text-right text-gray-500 uppercase whitespace-nowrap sm:py-2"><span class="inline sm:hidden">Avg.</span><span class="hidden sm:inline">Avg. Score</span></th>
                        <th scope="col" class="px-2 py-1 text-xs font-semibold tracking-wider text-left text-right text-gray-500 uppercase whitespace-nowrap sm:py-2"><span class="inline sm:hidden">Num.</span><span class="hidden sm:inline">Games</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($leaderboard->leaderboard as $position)
                    <tr class="bg-white even:bg-gray-50/50">
                        <td class="text-center pl-1 pr-3 py-2 sm:px-4 sm:py-2 whitespace-nowrap @if(isset($leaderboard->leaderboard[$loop->index + 1]['place']) && $leaderboard->leaderboard[$loop->index + 1]['place'] != $position['place']) border-b border-gray-200 @endif">
                            @if($position['place'] === 1)
                                <span class="inline-flex justify-center items-center w-7 h-7 text-sm font-extrabold text-black rounded-full md:h-10 md:w-10 md:text-xl bg-gold shadow-sm">{{ $position['place'] }}</span>
                            @elseif($position['place'] === 2)
                                <span class="inline-flex justify-center items-center w-7 h-7 text-sm font-bold text-black rounded-full md:h-10 md:w-10 md:text-lg bg-silver shadow-sm">{{ $position['place'] }}</span>
                            @elseif($position['place'] === 3)
                                <span class="inline-flex justify-center items-center w-7 h-7 text-sm font-bold text-black rounded-full md:h-10 md:w-10 md:text-lg bg-bronze shadow-sm">{{ $position['place'] }}</span>
                            @else
                                <span class="text-sm font-medium">{{ $position['place'] }}</span>
                            @endif
                        </td>
                        <td class="px-1 py-2 sm:px-4 sm:py-2  @if(isset($leaderboard->leaderboard[$loop->index + 1]['place']) && $leaderboard->leaderboard[$loop->index + 1]['place'] != $position['place']) border-b border-gray-200 @endif
                            @if($position['place'] === 1)
                            text-sm sm:text-base md:text-xl font-bold
                            @elseif($position['place'] === 2)
                            text-sm sm:text-base md:text-lg font-bold sm:font-bold
                            @elseif($position['place'] === 3)
                            text-sm font-bold
                            @else
                            text-sm text-gray-900 font-medium
                            @endif
                        " title="{{ $position['name'] }}">@if($anonymizePrivateUsers && $position['user'] && $position['user']->private_profile) Anonymous User @else {{ $position['name'] }} @endif</td>
                        <td class="px-2 py-2 sm:py-2 whitespace-nowrap text-right @if(isset($leaderboard->leaderboard[$loop->index + 1]['place']) && $leaderboard->leaderboard[$loop->index + 1]['place'] != $position['place']) border-b border-gray-200 @endif
                            @if($position['place'] === 1)
                            text-sm sm:text-base md:text-xl font-bold
                            @elseif($position['place'] === 2)
                            text-sm sm:text-base md:text-lg font-bold sm:font-bold
                            @elseif($position['place'] === 3)
                            text-sm font-bold
                            @else
                            text-sm text-gray-900 font-medium
                            @endif
                        ">{!! $position['stats']['mean'] ?: "&#x2014;" !!}</td>
                        <td class="px-2 py-2 sm:py-2 whitespace-nowrap  text-right @if(isset($leaderboard->leaderboard[$loop->index + 1]['place']) && $leaderboard->leaderboard[$loop->index + 1]['place'] != $position['place']) border-b border-gray-200 @endif
                            @if($position['place'] === 1)
                            text-sm sm:text-base md:text-xl font-bold
                            @elseif($position['place'] === 2)
                            text-sm sm:text-base md:text-lg font-bold sm:font-bold
                            @elseif($position['place'] === 3)
                            text-sm font-bold
                            @else
                            text-sm text-gray-900 font-medium
                            @endif
                        ">{{ $position['stats']['count'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<x-layout :page="$page">
    <div class="max-w-2xl sm:mx-auto space-y-1 md:py-4 rounded mt-10 md:mt-5 mx-3">
        <div class="p-5 sm:p-8 bg-slate-300 shadow rounded-t-lg">
            <div class="text-right mb-3">
                <a class="text-xl hover:text-slate-700" href="{{ route('profile') }}"><i class="fa-solid fa-user-gear"></i></a>
                <a class="text-xl hover:text-slate-700" href="{{ route('createGroup') }}"><i class="fa-solid fa-pen-to-square"></i></a>
            </div>
            <div class="flex gap-4 pb-3 ">
                {{-- header --}}
               
                <div class="flex gap-3">
                    <div class="div text-2xl">
                        @if ($user->profile_image)
                            <img class="h-16 w-16 object-cover rounded-full" src="{{ asset('storage/' . $user->profile_image) }}" alt="Current profile photo" />
                        @else
                            <img class="h-16 w-16 object-cover rounded-full" src="https://via.placeholder.com/100/0f172a/ccc.png?text={{$user->imageName()}}" alt="Current profile photo" />
                        @endif 
                    </div>
                    <div class="div">
                        <h2 class="font-bold text-lg md:text-xl inline-block"> {{ $user->name }}</h2>
                        <p class="text-slate-500"><i class='fa-solid fa-circle text-green-700 text-sm'></i> Active now</p>
                    </div>
                    
                </div>
                
                <div class="ml-auto">
                    
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <input type="submit" class="px-3 py-2 bg-slate-900 hover:bg-slate-800 text-slate-100 rounded cursor-pointer" value="Logout">
                    </form>
                </div>
            </div>
        </div>

        {{-- messages per user and group --}}
        <div class="p-5 sm:p-8 bg-slate-200 shadow rounded-b-lg overflow-auto h-96 no-scrollbar" id="userList">

            @if ($otherUsersId)
                @foreach ($latest_messages as $chat)

                    <a href="{{ route('showChat', [$chat['id'], $chat['group'] != '' ? 'group' : null ]) }}">
                        <div class="hover:bg-slate-300 px-2 grid grid-cols-3 gap-4 ">
                            <div class="my-5 flex col-span-2">
                                <div class="text-xl pr-3 flex-none">
                                    @if ($chat['profile_image'])
                                        <img class="h-16 w-16 object-cover rounded-full" src="{{ asset('storage/' . $chat['profile_image']) }}" alt="Current profile photo" />
                                    @else
                                        <img class="h-16 w-16 object-cover rounded-full" src="https://via.placeholder.com/100/0f172a/ccc.png?text={{$chat['image_name']}}" alt="Current profile photo" />
                                    @endif 
                                </div>

                                <div class="w-3/4 lg:w-80">
                                    <h3 class="{{ $chat['hasRead'] != '' ? 'font-bold' : ''  }} text-base md:text-xl text-slate-700 truncate">{{$chat['name'] != "" ? $chat['name'] : $chat['group'] }}</h3>
                                    <p class="{{ $chat['hasRead'] != '' ? 'font-bold' : ''  }} text-slate-700 truncate">{{ $chat['sender'] == 'self' ? "You: " . $chat['message'] : ($chat['sender'] != "" ? "{$chat['sender']}: {$chat['message']}" : $chat['message'] ) }}</p>
                                </div>
                                                    
                            </div>

                            @if ($chat['status'] == 1)
                                <div class="ml-auto my-5 flex-none">
                                    <p class="text-xs md:text-sm text-slate-600 tracking-tight mt-0.5">{{ $chat['created_at'] }}</p>
                                    <h3 class="font-bold text-right mb-1"><i class="fa-solid fa-circle text-green-700 text-sm"></i></h3>
                                </div>
                            @else
                                <div class="ml-auto my-5  flex-none">
                                    <p class="text-xs md:text-sm text-slate-600 tracking-tight mt-0.5">{{ $chat['created_at'] }}</p>
                                    <h3 class="font-bold text-right mb-1"><i class="fa-solid fa-circle text-slate-700 text-sm"></i></h3>    
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach

            @else
                <p class="text-slate-200">No users available</p>
            @endif
        </div>
        
    </div>

    {{-- blade js and ajax script file --}}
    <x-scripts.home-fetch />
    
</x-layout>
<x-layout :page="$page">
    <div class="grid text-slate-100">
        <div class="justify-self-center mt-24 bg-slate-200 text-slate-800 py-8 px-7 md:px-16 w-4/5 md:w-auto">
            <div class="text-right mb-3">
                <a class="text-xl" href="{{ route('createGroup') }}"><i class="fa-solid fa-pen-to-square"></i></a>
            </div>
            <div class="flex gap-4 border-b-2 border-slate-500 pb-3 ">
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

            {{-- messages per user and group --}}
                <div id="userList" class="w-full overflow-auto h-96 no-scrollbar">
                    @if ($otherUsersId)
                        @foreach ($latest_messages as $chat)

                        <a href="{{ route('showChat', [$chat['id'], $chat['group'] != '' ? 'group' : null ]) }}">
                            <div class="flex hover:bg-slate-300 px-2"> 
                                <div class="my-5 flex gap-3">
                                    <div class="text-xl">
                                        @if ($chat['profile_image'])
                                            <img class="h-16 w-16 object-cover rounded-full" src="{{ asset('storage/' . $chat['profile_image']) }}" alt="Current profile photo" />
                                        @else
                                        <img class="h-16 w-16 object-cover rounded-full" src="https://via.placeholder.com/100/0f172a/ccc.png?text={{$chat['image_name']}}" alt="Current profile photo" />
                                        @endif 

                                    </div>
                                    <div class="w-36 md:w-60">
                                        <h3 class="font-bold text-base md:text-xl text-slate-700 truncate">{{$chat['name'] != "" ? $chat['name'] : $chat['group'] }}</h3>
                                        <p class="{{ $chat['hasRead'] != '' ? 'font-bold' : ''  }} text-slate-700 truncate">{{ $chat['sender'] == 'self' ? "You: " . $chat['message'] : ($chat['sender'] != "" ? "{$chat['sender']}: {$chat['message']}" : $chat['message'] ) }}</p>
                                    </div>
                                                
                                </div>

                                @if ($chat['status'] == 1)
                                    <div class="ml-auto my-5">
                                        <p class="text-xs md:text-sm text-slate-600 tracking-tight mt-0.5">{{ $chat['created_at'] }}</p>
                                        <h3 class="font-bold text-right mb-1"><i class="fa-solid fa-circle text-green-700 text-sm"></i></h3>
                                        
                                        
                                    </div>
                                @else
                                <div class="ml-auto my-5 ">
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
    </div>

    <script>
        setInterval(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{ route("fetchChatPerUser")}}',
                type: 'post',
                success: function (response) {
                    let latestChats = response.latest_messages;
                    if(latestChats.length > 0) {
                        $("#userList").empty();

                        latestChats.map((chat) =>{
                            let isGroup = chat.group != "" ? "group" : '';
                            let url = "{{route('showChat', '')}}"+"/"+chat.id+"/"+isGroup;
                            let profile_image = chat.profile_image ? "{{ asset('storage/') }}"+"/"+chat.profile_image : `https://via.placeholder.com/100/0f172a/ccc.png?text=${chat.image_name}`;
                            
                            $("#userList").append(`
                                <a href="${url}">
                                    <div class="flex hover:bg-slate-300 px-2">
                                        <div class="my-5 flex gap-3">
                                            <div class="text-xl">
                                                <img class="h-16 w-16 object-cover rounded-full" src="${profile_image}" alt="Current profile photo" />
                                                
                                            </div>

                                            <div class="w-36 md:w-60">
                                                <h3 class="font-bold text-base md:text-xl text-slate-700 truncate"> ${chat['name'] != '' ? chat['name'] : chat['group'] }</h3>
                                                <p class="${chat['hasRead'] != '' ? 'font-bold' : '' } text-slate-700 truncate">${(chat['sender'] == 'self') ? "You: " + chat['message'] : (chat['sender'] != "" ? chat['sender'] + ": " + chat['message'] : chat['message'])  }</p>
                                            </div>
                                            
                                        </div>
                                                                                
                                        <div class="ml-auto my-5">
                                                <p class="text-xs md:text-sm text-slate-600 tracking-tight mt-0.5"> ${chat['created_at']}</p>
                                                ${(chat['status'] == 1) ? '<h3 class="font-bold text-right mb-1"><i class="fa-solid fa-circle text-green-700 text-sm"></i></h3>' : '<h3 class="font-bold text-right mb-1"><i class="fa-solid fa-circle text-slate-700 text-sm"></i></h3>'}
                                            </div>
                                    </div>
                                </a>
                            `);

                        });
                    }
                }
            });
        }, 1000);

    </script>
    
</x-layout>
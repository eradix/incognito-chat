@php
    use Carbon\Carbon;
@endphp


<x-layout :page="$page">
    <div class="grid justify-items-stretch text-slate-100">
        <div class="justify-self-center mt-24 bg-slate-200 text-slate-800 py-10 px-7 md:px-16 w-4/5 md:w-auto">
            @if ($isGroup)
                <div class="text-right mb-3">
                    <a class="text-xl cursor-pointer" id="viewMembers"><i class="fa-solid fa-user-group"></i> {{count($receiverInfo->members)}}</a>
                </div>
                <div id="membersList" class="border border-slate-700 mb-5 hidden">
                    @foreach ($receiverInfo->members()->orderBy('name')->get() as $member)
                        <div class="flex gap-3 py-5 px-10 hover:bg-slate-300 cursor-pointer">
                            <div class="">
                                <a href="{{ $member->id != auth()->user()->id ? route('showChat', $member->id) : '' }}" >
                                    @if ($member->profile_image)
                                        <img class="h-16 w-16 object-cover rounded-full" src="{{ asset('storage/' . $member->profile_image) }}" alt="Current profile photo" />
                                    @else
                                        <img class="h-16 w-16 object-cover rounded-full" src="https://via.placeholder.com/100/0f172a/ccc.png?text={{$member->imageName()}}" alt="Current profile photo" />
                                    @endif
                                </a>
                            </div>
                            <div class="">
                                <a href="{{ $member->id != auth()->user()->id ? route('showChat', $member->id) : '' }}" >
                                    <p class="font-bold">{{$member->name}}</p>
                                    @if ($member->id == auth()->user()->id)
                                        <p class="text-slate-500"><i class='fa-solid fa-circle text-green-700 text-sm'></i> You</p>
                                    @else
                                        @if ($member->status == 1)
                                            <p class=" mb-1 text-slate-500">
                                                <i class='fa-solid fa-circle text-green-700 text-sm'></i> Active now
                                            </p>
                                        @else
                                            <p class=" mb-1 text-slate-500">
                                                <i class='fa-solid fa-circle text-slate-700 text-sm'></i> Offline
                                            </p>
                                        @endif
                                    @endif
                                </a>
                                
                            </div>
                        </div>
                    @endforeach
                    <div class="flex gap-3 py-5 px-10 flex-col">
                        <a class="font-bold hover:text-slate-700 cursor-pointer" id="addNewUser"><i class="fa-solid fa-user-plus"></i> Add User</a>
                        <div id="addForm" class="hidden">
                            <form action="{{route('addUserInAGroup', $receiverInfo->id)}}" method="post" class="">
                                @csrf
                                <div class="mb-3">
                                   <select name="user_id" id="user_id" class="rounded p-2 w-full border" required>
                                        <option disabled="true" selected="selected">Add user to group</option>
                                    @foreach ($notMembers as $notMember)
                                        <option value="{{ $notMember->id }}">{{ $notMember->name }}</option>
                                    @endforeach
                                        
                                   </select>
                               </div>
                               @error('user_id')
                                    <div class="text-red-600 mb-3" role="alert">
                                       <p class="italic">{{ $message }}</p>
                                    </div>
                               @enderror
                               <input type="submit" value=" Add user" class="w-full bg-slate-900 hover:bg-slate-800 cursor-pointer text-slate-100 hover:text-white px-5 py-2 rounded mb-3">
                            </form>
                        </div>
                        <div>
                            <form action="{{route('leaveInAGroup', $receiverInfo->id)}}" method="post">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="font-bold hover:text-red-600 text-red-700" onclick="return confirm('Are you sure you want to leave this group chat?')"><i class="fa-solid fa-arrow-left"></i> Leave</button>
                            </form>
                        </div>
                    </div>
                </div>

            @endif
            
            <div class=" border-b-2 border-slate-500 pb-3 ">
                {{-- header --}}
               
                <div class="flex gap-3 justify-center">
                    <a class="text-xl pt-1" href="{{ url()->previous() }}"><i class="fa-solid fa-arrow-left-long"></i></a>
                    <div class="flex gap-3">
                        <div class="div text-2xl">
                            @if ( $receiverInfo->profile_image)
                                <img class="h-16 w-16 object-cover rounded-full" src="{{ asset('storage/' . $receiverInfo->profile_image) }}" alt="Current profile photo" />
                            @else
                                <img class="h-16 w-16 object-cover rounded-full" src="https://via.placeholder.com/100/0f172a/ccc.png?text={{$receiverInfo->imageName()}}" alt="Current profile photo" />
                            @endif 
                        </div>
                        <div class="div">
                            <h2 class="font-bold text-lg md:text-xl inline-block pt-2"> {{ $isGroup ? $receiverInfo->group_name : $receiverInfo->name }}</h2>
                            @if (!$isGroup)
                                @if ( $receiverInfo->status == 1)
                                    <p class="text-slate-500"><i class='fa-solid fa-circle text-green-700 text-sm'></i> Active now</p>
                                @else
                                    <p class="text-slate-500"><i class='fa-solid fa-circle text-slate-700 text-sm'></i> Offline</p>
                                @endif
                            @endif
                            
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
            {{-- message body --}}

            <div class="border-b-2 border-t-2 border-slate-500 p-10 w-full overflow-auto h-96 no-scrollbar" id="messageBody">
                @unless (count($chats) == 0)
                    @foreach ($chats as $chat)

                        @php
                            $now = new Carbon();
                            $created_at = new Carbon($chat->created_at);
                            $differenceInDays = $now->diffInDays($created_at->format('Y-m-d'));
                        @endphp

                        @if ($chat->sender->name == auth()->user()->name)
                            <div class="mb-5 text-right">
                                <div class="inline-block text-left bg-slate-900 text-slate-100 py-2 px-5 rounded">
                                    <p class="block text-xs">{{$differenceInDays < 1 ? $chat->created_at->format('h:i A') : $created_at->format("m/d") . " " . $chat->created_at->format('h:i A') }}</p>
                                    <p class="block text-sm md:text-base">{{ $chat->message }}</p>
                                </div>
                            </div>
                        @else
                            <div class="flex gap-3">
                                <div>
                                    @if (!$isGroup)
                                        @if ($receiverInfo->profile_image)
                                            <img class="h-10 md:h-16 w-12 md:w-16 object-cover rounded-full" src="{{ asset('storage/' . $receiverInfo->profile_image) }}" alt="Current profile photo" />
                                        @else
                                            <img class="h-10 md:h-16 w-12 md:w-16 object-cover rounded-full" src="https://via.placeholder.com/100/0f172a/ccc.png?text={{$receiverInfo->imageName()}}" alt="Current profile photo" />
                                        @endif
                                    @else
                                        
                                        @if ($chat->sender->profile_image)
                                            <img class="h-10 md:h-16 w-12 md:w-16 object-cover rounded-full" src="{{ asset('storage/' . $chat->sender->profile_image) }}" alt="Current profile photo" />
                                        @else
                                            <img class="h-10 md:h-16 w-12 md:w-16 object-cover rounded-full" src="https://via.placeholder.com/100/0f172a/ccc.png?text={{$chat->sender->imageName()}}" alt="Current profile photo" />
                                        @endif
                                    @endif

                                </div>
            
                                <div class="bg-slate-300 text-slate-900 px-5 py-2 rounded mb-5">
                                    <p class="font-bold mb-2 ">{{ $isGroup ? $chat->sender->name : $receiverInfo->name }} <span class="text-xs"> {{$differenceInDays < 1 ? $chat->created_at->format('h:i A') : $created_at->format("m/d") . " " . $chat->created_at->format('h:i A') }}</span></p>
                                    <p class=""><span class="text-sm md:text-base">{{ $chat->message }}</span></p>
                                </div>
                            </div>
                       
                        @endif
                        
                    @endforeach
                @else
                    @if (!$isGroup)
                        <p class="">You're starting a new conversation with <span class="font-bold">{{$receiverInfo->name}}</span>.</p>
                        <p class="">Type your first message below.</p>
                    @else()
                        <p class="">You are a member of this group chat <span class="font-bold">{{$receiverInfo->group_name}}</span>.</p>
                        <p class="">Type your first message below.</p>
                    @endif

                @endif
            </div>

            <div class="relative w-full my-3">
                <input type="text" name="message" id="message"  class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-r-lg border-l-slate-800 border-2 border-slate-600 focus:ring-slate-900 focus:border-slate-800 " placeholder="Type a new message" required>
                <button type="button" id="sendBtn" class="absolute top-0 right-0 p-2.5 text-sm font-medium text-white bg-slate-900 rounded-r-lg border border-slate-700 hover:bg-slate-800 focus:ring-4 focus:outline-none focus:ring-slate-300 "><i class="fa-regular fa-paper-plane w-5 h-5"></i></button>
            </div>
            
        </div>
    </div>

    <script>

        //automatically scroll down to the latest message
        $('#messageBody').scrollTop($('#messageBody')[0].scrollHeight);

        //view members button
        $("#viewMembers").click(function(){
            $("#membersList").fadeToggle();
        });

        //addNewUser
        $("#addNewUser").click(function (){
            $("#addForm").fadeToggle();
        });

        //function to send chat via ajax to db
        document.querySelector("#sendBtn").addEventListener('click', ()=>{

            let message = $("#message").val();
            let receiver_id = null;
            let group_id = null;
            @if (!$isGroup) {
                receiver_id = {{ $receiverInfo->id }};
            }
            @else
                group_id = {{ $receiverInfo->id }};
            @endif

            console.log(message, receiver_id, group_id);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({

                url: "{{ route('storeChat') }}",
                type: 'post',
                data: { message: message, receiver_id : receiver_id, group_id : group_id },

                success: function (response) {
                    $("#message").val("");

                    let hoursMin = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

                    $("#messageBody").append(`
                        <div class="mb-5 text-right">
                            <div class="inline-block text-left bg-slate-900 text-slate-100 py-2 px-5 rounded">
                                <p class="block text-xs">${hoursMin}</p>
                                <p class="block text-sm md:text-base">${message}</p>
                            </div>
                        </div>
                    `);
                    //automatically scroll down to the latest message
                    $('#messageBody').scrollTop($('#messageBody')[0].scrollHeight);
                    
                },
            });
        });
        
        setInterval(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{ route("fetchChat", [$receiverInfo->id, ($isGroup) ? "group" : null ]) }}',
                type: 'post',
                success: function (response) {
                    let groupInfos = response.groupInfos;
                    let chats = response.chats;
                    let isGroup = response.isGroup;
                    let receiverInfo = "{{$receiverInfo->profile_image ?? ''}}";
                    let otherUserImage = (receiverInfo) ? "{{ asset('storage/' . $receiverInfo->profile_image) }}" : "https://via.placeholder.com/100/0f172a/ccc.png?text={{$receiverInfo->imageName()}}";

                    let messageBody = $('#messageBody');
                    let isScrolledToBottom = messageBody.scrollTop() + messageBody.innerHeight() >= messageBody[0].scrollHeight;

                    if(chats.length > 0) {
                        $("#messageBody").empty();
                        chats.map((chat,index) =>{
                            let now = new Date();
                            let created_at = new Date(chat.created_at);
                            let hoursMin = created_at.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                            let differenceInDays;
                            //compute date or time to display in chat
                            if (created_at.toDateString() === now.toDateString()) {
                                differenceInDays = 0;
                            } 
                            else{
                                let differenceInTime = now.getTime() - created_at.getTime();
                                differenceInDays = Math.ceil(differenceInTime / (1000 * 60 * 60 * 24));
                            }


                            let formattedDate = created_at.toLocaleString("default", { month: "2-digit", day: "2-digit" });
                            let otherUserImageInGroup;
                            if(isGroup){
                                otherUserImageInGroup = (groupInfos[index].profile_image) ? "{{ asset('storage/')}}/" + groupInfos[index].profile_image : `https://via.placeholder.com/100/0f172a/ccc.png?text=${groupInfos[index].imageName}`;
                            }
                           
                                                        
                            if (chat.sender_id == {{auth()->user()->id}}){
                                $("#messageBody").append(`
                                <div class="mb-5 text-right">
                                    <div class="inline-block text-left bg-slate-900 text-slate-100 py-2 px-5 rounded">
                                        <p class="block text-xs">${differenceInDays < 1 ? hoursMin : `${formattedDate} ${hoursMin} `}</p>
                                        <p class="block text-sm md:text-base">${chat.message}</p>
                                    </div>
                                </div>
                                
                                `); 
                            }
                            else{
                                if(isGroup){
                                    $("#messageBody").append(`
                                        <div class="flex gap-3">
                                            <div>
                                                <img class="h-10 md:h-16 w-12 md:w-16 object-cover rounded-full" src="${otherUserImageInGroup}" alt="Current profile photo" />
                                            </div>
                        
                                            <div class="bg-slate-300 text-slate-900 px-5 py-2 rounded mb-5">
                                                <p class="font-bold mb-2">${groupInfos[index].name} <span class="text-xs">${differenceInDays < 1 ? hoursMin : `${formattedDate} ${hoursMin} `}</span></p>
                                                <p class=""><span class="text-sm md:text-base">${chat.message}</span></p>
                                            </div>
                                        </div>
                                
                                `); 
                                }
                                else{
                                    $("#messageBody").append(`
                                        <div class="flex gap-3">
                                            <div>
                                                <img class="h-10 md:h-16 w-12 md:w-16 object-cover rounded-full" src="${otherUserImage}" alt="Current profile photo" />
                                            </div>
                        
                                            <div class="bg-slate-300 text-slate-900 px-5 py-2 rounded mb-5">
                                                <p class="font-bold mb-2">{{ $receiverInfo->name }} <span class="text-xs">${differenceInDays < 1 ? hoursMin : `${formattedDate} ${hoursMin} `}</span></p>
                                                <p class=""><span class="text-sm md:text-base">${chat.message}</span></p>
                                            </div>
                                        </div>
                                
                                `); 
                                }
                            }
                        });

                        if (!isScrolledToBottom) {
                            return;
                        }

                        $('#messageBody').scrollTop($('#messageBody')[0].scrollHeight);

                        
                    }
                }
            });
        }, 1000);
    </script>

    
</x-layout>
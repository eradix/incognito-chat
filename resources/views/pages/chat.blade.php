@php
    use Carbon\Carbon;
@endphp


<x-layout :page="$page">
    <div class="grid justify-items-stretch text-slate-100">
        <div class="justify-self-center mt-24 bg-slate-200 text-slate-800 py-10 px-7 md:px-16 w-4/5 md:w-auto">
            
            <div class=" border-b-2 border-slate-500 pb-3 ">
                {{-- header --}}
               
                <div class="flex gap-3 justify-center">
                    <a class="text-xl pt-1" href="{{route('home')}}"><i class="fa-solid fa-arrow-left-long"></i></a>
                    <div class="flex gap-3">
                        <div class="div text-2xl">
                            @if ($receiverUser->profile_image)
                                <img class="h-16 w-16 object-cover rounded-full" src="{{ asset('storage/' . $receiverUser->profile_image) }}" alt="Current profile photo" />
                            @else
                                <img class="h-16 w-16 object-cover rounded-full" src="https://via.placeholder.com/100/0f172a/ccc.png?text={{$receiverUser->imageName()}}" alt="Current profile photo" />
                            @endif 
                        </div>
                        <div class="div">
                            <h2 class="font-bold text-lg md:text-xl inline-block pt-2"> {{ $receiverUser->name }}</h2>
                            @if ( $receiverUser->status == 1)
                                <p class="text-slate-500">Active now</p>
                            @else
                                <p class="text-slate-500">Offline</p>
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
                                    @if ($receiverUser->profile_image)
                                        <img class="h-10 md:h-16 w-12 md:w-16 object-cover rounded-full" src="{{ asset('storage/' . $receiverUser->profile_image) }}" alt="Current profile photo" />
                                    @else
                                        <img class="h-10 md:h-16 w-12 md:w-16 object-cover rounded-full" src="https://via.placeholder.com/100/0f172a/ccc.png?text={{$receiverUser->imageName()}}" alt="Current profile photo" />
                                    @endif 
                                </div>
            
                                <div class="bg-slate-300 text-slate-900 px-5 py-2 rounded mb-5">
                                    <p class="font-bold mb-2 ">{{ $receiverUser->name }} <span class="text-xs"> {{$differenceInDays < 1 ? $chat->created_at->format('h:i A') : $created_at->format("m/d") . " " . $chat->created_at->format('h:i A') }}</span></p>
                                    <p class=""><span class="text-sm md:text-base">{{ $chat->message }}</span></p>
                                </div>
                            </div>
                       
                        @endif
                        
                    @endforeach
                @else
                    <p class="">You're starting a new conversation with <span class="font-bold">{{$receiverUser->name}}</span>.</p>
                    <p class="">Type your first message below.</p>
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

        //function to send chat via ajax to db
        document.querySelector("#sendBtn").addEventListener('click', ()=>{

            let message = $("#message").val();
            console.log(message);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({

                url: "{{ route('storeChat') }}",
                type: 'post',
                data: { message: message, receiver_id : {{ $receiverUser->id}} },

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
                url: '{{ route("fetchChat", $receiverUser->id) }}',
                type: 'post',
                success: function (response) {
                    let chats = response.chats;
                    let receiverUser = "{{$receiverUser->profile_image ?? ''}}";
                    let otherUserImage = (receiverUser) ? "{{ asset('storage/' . $receiverUser->profile_image) }}" : "https://via.placeholder.com/100/0f172a/ccc.png?text={{$receiverUser->imageName()}}";
                    // console.log(otherUserImage);
                    let messageBody = $('#messageBody');
                    let isScrolledToBottom = messageBody.scrollTop() + messageBody.innerHeight() >= messageBody[0].scrollHeight;


                    if(chats.length > 0) {
                        $("#messageBody").empty();
                        chats.map((chat) =>{
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
                                $("#messageBody").append(`
                                <div class="flex gap-3">
                                    <div>
                                        <img class="h-10 md:h-16 w-12 md:w-16 object-cover rounded-full" src="${otherUserImage}" alt="Current profile photo" />
                                    </div>
                
                                    <div class="bg-slate-300 text-slate-900 px-5 py-2 rounded mb-5">
                                        <p class="font-bold mb-2">{{ $receiverUser->name }} <span class="text-xs">${differenceInDays < 1 ? hoursMin : `${formattedDate} ${hoursMin} `}</span></p>
                                        <p class=""><span class="text-sm md:text-base">${chat.message}</span></p>
                                    </div>
                                </div>
                                
                                `); 
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
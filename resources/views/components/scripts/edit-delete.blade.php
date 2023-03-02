@props(['isGroup', 'id', 'profileImage', 'imageName', 'name', 'group'])

<script>


    let fetchChat = function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '{{ route("fetchChat", [$id, ($isGroup) ? "group" : null ]) }}',
            type: 'post',
            success: function (response) {

                let groupInfos = response.groupInfos;
                let chats = response.chats;
                let isGroup = response.isGroup;
                let receiverInfo = "{{$profileImage ?? ''}}";
                let otherUserImage = (receiverInfo) ? "{{ asset('storage/' . $profileImage) }}" : "https://via.placeholder.com/100/0f172a/ccc.png?text={{$imageName}}";

                let messageBody = $('#messageBody');
                let isScrolledToBottom = messageBody.scrollTop() + messageBody.innerHeight() >= messageBody[0].scrollHeight;
                console.log('setinterval');
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
                                <div id="options_${chat.chat_id}" class="text-right selfOptions ease-in-out duration-300">
                                    <div class="">
                                        <span class="cursor-pointer hover:text-slate-700" data-id="${chat.chat_id}" onclick="edit(this)"><i class="fa-solid fa-pen"></i></span>
                                        &emsp;<span class="cursor-pointer hover:text-red-800" data-id="${chat.chat_id}" onclick="deleteMsg(this)"><i class="fa-regular fa-trash-can"></i></a>
                                    </div>
                                </div>
                                <div id="chat_${chat.chat_id}" class="mb-5 text-right selfMessage ease-in-out duration-300">
                                    <div class="inline-block text-left bg-slate-900 text-slate-100 py-2 px-5 rounded">
                                        <p class="block text-xs">${differenceInDays < 1 ? hoursMin : `${formattedDate} ${hoursMin} `}</p>
                                        <p id="p_${chat.chat_id}" class="block text-sm md:text-base">${chat.message}</p>
                                    </div>
                                </div>
                                <div class="mb-5 text-right hidden" id="form_${chat.chat_id}">
                                    <div class="inline-block text-left py-2 px-5 rounded">
                                        <input type="text" name="message" id="message_${chat.chat_id}" class="rounded p-2 w-full mb-3">
                                        <input data-id="${chat.chat_id}" type="button" value="Update" class="bg-slate-900 hover:bg-slate-800 cursor-pointer text-slate-100 hover:text-white px-5 py-2 rounded" onclick="updateChat(this)">
                                        <input data-id="${chat.chat_id}" type="button" id="cancel_${chat.chat_id}" value="Cancel" class="bg-slate-100 hover:bg-slate-50 cursor-pointer text-slate-900 hover:text-slate-800 px-5 py-2 rounded" onclick="cancelEdit(this)">
                                    </div>
                                </div>
                                        
                            `); 
                        }
                        else {
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
                                            <p class="font-bold mb-2">{{ $name }} <span class="text-xs">${differenceInDays < 1 ? hoursMin : `${formattedDate} ${hoursMin} `}</span></p>
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
                else{
                    @if ($isGroup) 
                        $("#messageBody").empty();
                        $("#messageBody").append(`
                            <p>You are a member of this group chat <span class="font-bold">{{$group}}</span>.</p>
                            <p>Type your first message below.</p>
                        `);
                    @else 
                        $("#messageBody").empty();
                        $("#messageBody").append(`
                            <p class="">You're starting a new conversation with <span class="font-bold">{{$name}}</span>.</p>
                            <p class="">Type your first message below.</p>
                        `);
                    @endif
                }
            }
        });
    };
        
        //initialize the setinterval function
        let fetchRealTimeChat = setInterval(fetchChat, 3000);

        //function for editing a chat
        const edit = function (element){
            $("#chat_"+element.dataset.id).hide();
            $("#options_"+element.dataset.id).hide();
            $("#form_"+element.dataset.id).slideDown();

            $("#message_"+element.dataset.id).val($("#p_"+element.dataset.id).text());

            //clear the interval so that the edit form will not be interrupted
            clearInterval(fetchRealTimeChat);
        };

        //function for deleting a specific chat
        const deleteMsg = function (element){
            //clear the interval
            clearInterval(fetchRealTimeChat);

            let toDelete = confirm("Want to delete this chat?");

            if (toDelete) {
               
                let chat_id = element.dataset.id;

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({

                    url: "{{ route('deleteChatMsg') }}",
                    type: 'delete',
                    data: { chat_id : chat_id },

                    success: function (response) {
                        $("#chat_"+chat_id).remove();
                        $("#options_"+chat_id).remove();

                        if ($("#messageBody").children().length == 0){
                            @if ($isGroup) 
                                $("#messageBody").append(`
                                    <p>You are a member of <span class="font-bold">{{$group}}</span> group chat.</p>
                                    <p>Type your first message below.</p>
                                `);
                            @else 
                                $("#messageBody").append(`
                                <p class="">You're starting a new conversation with <span class="font-bold">{{$name}}</span>.</p>
                                <p class="">Type your first message below.</p>
                                `);
                            @endif
                            
                        }
                          
                    },
                });

                //restart the interval
                fetchRealTimeChat = setInterval(fetchChat, 3000);
                return true;
            } 
            else {
                //restart the interval
                fetchRealTimeChat = setInterval(fetchChat, 3000);
                return false;
            }


        };

        //cancel button in the edit form
        const cancelEdit = function (element){
            $("#chat_"+element.dataset.id).show();
            $("#options_"+element.dataset.id).show();
            $("#form_"+element.dataset.id).hide();

            //restart the interval
            fetchRealTimeChat = setInterval(fetchChat, 3000);
        };

        //function for updating a specific chat
        const updateChat = function (element){

            let chat_id = element.dataset.id;
            let message = $("#message_"+element.dataset.id).val();
            let receiver_id = null;
            let group_id = null;
            @if (!$isGroup) {
                receiver_id = {{ $id }};
            }
            @else
                group_id = {{ $id }};
            @endif

            // console.log(message, receiver_id, group_id, chatId);

            if(message != ""){

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({

                    url: "{{ route('updateChatMsg') }}",
                    type: 'put',
                    data: { message: message, receiver_id : receiver_id, group_id : group_id, chat_id : chat_id },

                    success: function (response) {
                        $("#chat_"+element.dataset.id).show();
                        $("#options_"+element.dataset.id).show();
                        $("#form_"+element.dataset.id).hide();

                        $("#p_"+element.dataset.id).text(message)
                        
                    },
                });

                //restart setinterval
                fetchRealTimeChat = setInterval(fetchChat, 3000);
            }
            else {
                alert('Please input a message!');
            }
        };

</script>
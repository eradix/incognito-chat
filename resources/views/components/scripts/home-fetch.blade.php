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
                                <div class="hover:bg-slate-300 px-2 grid grid-cols-3 gap-4 ">
                                    <div class="my-5 flex col-span-2">
                                        <div class="text-xl pr-3 flex-none">
                                            <img class="h-16 w-16 object-cover rounded-full" src="${profile_image}" alt="Current profile photo" />
                                            
                                        </div>

                                        <div class="w-3/4 lg:w-80">
                                            <h3 class="${chat['hasRead'] != '' ? 'font-bold' : '' } text-base md:text-xl text-slate-700 truncate"> ${chat['name'] != '' ? chat['name'] : chat['group'] }</h3>
                                            <p class="${chat['hasRead'] != '' ? 'font-bold' : '' } text-slate-700 truncate">${(chat['sender'] == 'self') ? "You: " + chat['message'] : (chat['sender'] != "" ? chat['sender'] + ": " + chat['message'] : chat['message'])  }</p>
                                        </div>
                                        
                                    </div>
                                                                            
                                    <div class="ml-auto my-5 ">
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
    }, 3000);

</script>
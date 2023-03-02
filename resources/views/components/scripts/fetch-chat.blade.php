@props(['isGroup', 'id'])

<script>

     //function to send chat via ajax to db
    document.querySelector("#sendBtn").addEventListener('click', () => {

        let message = $("#message").val();
        let receiver_id = null;
        let group_id = null;
        @if (!$isGroup) {
            receiver_id = {{ $id }};
        }
        @else
            group_id = {{ $id }};
        @endif

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

                if($("#messageBody > p").text()) $("#messageBody > p").empty();

                let chat = response.chat;

                let hoursMin = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

                $("#messageBody").append(`
                    <div id="options_${chat.id}" class="text-right selfOptions ease-in-out duration-300">
                        <div class="">
                            <span class="cursor-pointer hover:text-slate-700" data-id="${chat.id}" onclick="edit(this)"><i class="fa-solid fa-pen"></i></span>
                            &emsp;<span class="cursor-pointer hover:text-red-800" data-id="${chat.id}" onclick="deleteMsg(this)"><i class="fa-regular fa-trash-can"></i></a>
                        </div>
                    </div>

                    <div id="chat_${chat.id}" class="mb-5 text-right">
                        <div class="inline-block text-left bg-slate-900 text-slate-100 py-2 px-5 rounded">
                            <p class="block text-xs">${hoursMin}</p>
                            <p id="p_${chat.id}" class="block text-sm md:text-base">${message}</p>
                        </div>
                    </div>

                    <div class="mb-5 text-right hidden" id="form_${chat.id}">
                        <div class="inline-block text-left py-2 px-5 rounded">
                            <input type="text" name="message" id="message_${chat.id}" class="rounded p-2 w-full mb-3">
                            <input data-id="${chat.id}" type="button" value="Update" class="bg-slate-900 hover:bg-slate-800 cursor-pointer text-slate-100 hover:text-white px-5 py-2 rounded" onclick="updateChat(this)">
                            <input data-id="${chat.id}" type="button" id="cancel_${chat.id}" value="Cancel" class="bg-slate-100 hover:bg-slate-50 cursor-pointer text-slate-900 hover:text-slate-800 px-5 py-2 rounded" onclick="cancelEdit(this)">
                        </div>
                    </div>
                `);
                //automatically scroll down to the latest message
                $('#messageBody').scrollTop($('#messageBody')[0].scrollHeight);
                
            },
        });
    });

</script>
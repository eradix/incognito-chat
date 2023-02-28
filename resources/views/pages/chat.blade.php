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
                            <form action="{{route('addUserInAGroup', $receiverInfo->id)}}" method="post" id="addUserInAGroupForm">
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
                               <input type="button" value="Add user" onclick="submitAddUserForm()" class="w-full bg-slate-900 hover:bg-slate-800 cursor-pointer text-slate-100 hover:text-white px-5 py-2 rounded mb-3">
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
                    <a class="text-xl pt-1" href="{{ route('home') }}"><i class="fa-solid fa-arrow-left-long"></i></a>
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
                            <div id="options_{{$chat->chat_id}}" class="text-right selfOptions ease-in-out duration-300">
                                <div class="">
                                    <span class="cursor-pointer hover:text-slate-700" data-id="{{$chat->chat_id}}" onclick="edit(this)"><i class="fa-solid fa-pen"></i></span>
                                    &emsp;<span class="cursor-pointer hover:text-red-800" data-id="{{$chat->chat_id}}" onclick="deleteMsg(this)"><i class="fa-regular fa-trash-can"></i></a>
                                </div>
                            </div>
                            <div id="{{"chat_" . $chat->chat_id}}" class="mb-5 text-right selfMessage ease-in-out duration-300">
                                <div class="inline-block text-left bg-slate-900 text-slate-100 py-2 px-5 rounded">
                                    <p class="block text-xs">{{$differenceInDays < 1 ? $chat->created_at->format('h:i A') : $created_at->format("m/d") . " " . $chat->created_at->format('h:i A') }}</p>
                                    <p id="p_{{$chat->chat_id}}" class="block text-sm md:text-base">{{ $chat->message }}</p>
                                </div>
                            </div>
                            <div class="mb-5 text-right hidden" id="form_{{$chat->chat_id}}">
                                <div class="inline-block text-left py-2 px-5 rounded">
                                    <input type="text" name="message" id="message_{{$chat->chat_id}}" class="rounded p-2 w-full mb-3">
                                    <input data-id="{{$chat->chat_id}}" type="button" value="Update" class="bg-slate-900 hover:bg-slate-800 cursor-pointer text-slate-100 hover:text-white px-5 py-2 rounded" onclick="updateChat(this)">
                                    <input data-id="{{$chat->chat_id}}" type="button" id="cancel_${chat.id}" value="Cancel" class="bg-slate-100 hover:bg-slate-50 cursor-pointer text-slate-900 hover:text-slate-800 px-5 py-2 rounded" onclick="cancelEdit(this)">
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

    {{-- javascript components --}}

    {{-- utility functions such as in adding group page --}}
    <x-scripts.utils />

    {{-- component script for displaying newly created chat --}}
    <x-scripts.fetch-chat :isGroup="$isGroup" :id="$receiverInfo->id" />

    {{-- component for checking realtime chats in db, edit and deleting chats --}}
    <x-scripts.edit-delete :isGroup="$isGroup" :id="$receiverInfo->id" :profileImage="$receiverInfo->profile_image" :imageName="$receiverInfo->imageName()" :name="$receiverInfo->name" />
    
</x-layout>
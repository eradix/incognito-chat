<x-layout :page="$page">
    <div class="max-w-4xl mx-auto space-y-1 md:space-y-2 md:py-4 rounded">
        <div class="p-5 sm:p-8 bg-slate-300 shadow sm:rounded-lg">
            <div class="mb-3">
                <a class="text-xl hover:text-slate-700" href="{{ route('home') }}"><i class="fa-solid fa-house-user"></i> Home</a>
            </div>
            <div class="text-center">
                @if ( $user->profile_image)
                    <img class="h-24 w-24 object-cover rounded-full mx-auto mb-3" src="{{ asset('storage/' . $user->profile_image) }}" alt="Current profile photo" />
                @else
                    <img class="h-24 w-24 object-cover rounded-full mx-auto mb-3" src="https://via.placeholder.com/100/0f172a/ccc.png?text={{$user->imageName()}}" alt="Current profile photo" />
                @endif 
                <h2 class="font-bold text-xl text-center mb-3">{{ $user->name }}</h2>
                <h2 class="font-bold text-xl text-center text-slate-800"><i class="fa-solid fa-id-card"></i> Profile</h2>
            </div>
            
        </div>
        <div class="p-5 sm:p-8 bg-slate-200 shadow sm:rounded-lg">
            <div class="mb-3">
                <h2 class="font-bold text-lg text-center text-slate-700 mb-3"><i class="fa-solid fa-chart-line"></i> Quick Statistics</h2>
                <div class="grid gap-4 md:grid-cols-3 p-5 md:p-2">
                    <div class="p-5 bg-slate-900 text-slate-200 hover:bg-slate-800 cursor-pointer rounded">
                        <h2 class="mb-3"><i class="fa-regular fa-paper-plane"></i> Chats sent:</h2>
                        <span class="font-bold block text-center text-xl">{{ $user->sentMessages->count() }}</span>
                    </div>
                    <div class="p-5 bg-slate-900 text-slate-200 hover:bg-slate-800 cursor-pointer rounded">
                        <h2 class="mb-3"><i class="fa-regular fa-message"></i> Chats received:</h2>
                        <span class="font-bold block text-center text-xl">{{ $user->receivedMessages->count() }}</span>
                    </div>
                    <div class="p-5 bg-slate-900 hover:bg-slate-800 cursor-pointer text-slate-200 rounded">
                        <h2 class="mb-3"><i class="fa-solid fa-people-group"></i> Groups:</h2>
                        <div class="ml-3">
                            @if ($user->groups->count() > 0)
                                @foreach ($user->groups as $group)
                                    <li class="hover:font-bold"><a href="{{ route('showChat', [$group->id, 'group']) }}">{{$group->group_name}}</a></li>
                                @endforeach
                            @else
                                <p>You were not a member of any group chats.</p>
                            @endif
                        </div>
                    </div>
                  </div>
            </div>
        </div>
        <div class="p-5 sm:p-8 bg-slate-200 shadow sm:rounded-lg" id="updateProfile">
            <div class="mb-3">
                <h2 class="font-bold text-lg text-center text-slate-700 mb-3"><i class="fa-solid fa-circle-info"></i> Profile Information</h2>
                <p class="text-slate-700 mb-3 text-center">Stay up to date. Update your account's profile information.</p>
            </div>
            <div class="mb-3">
                <form action="{{ route('updateProfile') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3 md:px-5">
                        <label class="block font-medium text-sm text-gray-700" for="name">Name</label>
                        <input class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full p-2" id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required="required" autocomplete="name">
                    </div>
                    @error('name')
                    <div class="text-red-600 mb-3 md:px-5" role="alert">
                            <p class="italic">{{ $message }}</p>
                        </div>
                    @enderror
                    <div class="mb-3 md:px-5">
                        <label class="block font-medium text-sm text-gray-700" for="email">Email</label>
                        <input class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full p-2" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required="required" autocomplete="email">
                    </div>
                    @error('email')
                    <div class="text-red-600 mb-3 md:px-5" role="alert">
                            <p class="italic">{{ $message }}</p>
                        </div>
                    @enderror
                    <div class="mb-3 md:px-5">
                        <p class="font-medium text-sm text-gray-700 mb-3">Profile Image:</p>
                        @if ( $user->profile_image)
                            <img class="h-16 w-16 object-cover rounded-full mb-3" id="user_image" src="{{ asset('storage/' . $user->profile_image) }}" alt="Current profile photo" />
                        @else
                            <img class="h-16 w-16 object-cover rounded-full mb-3" id="user_image" src="https://via.placeholder.com/100/0f172a/ccc.png?text={{$user->imageName()}}" alt="Current profile photo" />
                        @endif 
                        <label class="block font-medium text-sm text-gray-700" for="profile_image">Select new profile image</label>
                        <input type="file" name="profile_image" id="profile_image" class="block w-full text-sm text-slate-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-violet-50 file:text-slate-700
                        hover:file:bg-violet-100" accept="image/*" onchange="livePreview(this,'user_image')">
                    </div>
                    <div class="md:px-5">
                        <input type="submit" value="Update" class=" bg-slate-900 hover:bg-slate-800 cursor-pointer text-slate-100 hover:text-white px-3 py-1 rounded">
                    </div>
                    
                </form>

            </div>
        </div>
        <div class="p-5 sm:p-8 bg-slate-200 shadow sm:rounded-lg" id="updateUserPassword">
            <div class="mb-3">
                <h2 class="font-bold text-lg text-center text-slate-700 mb-3"><i class="fa-solid fa-lock"></i> Update Password</h2>
                <p class="text-slate-700 mb-3 text-center">Secure your account by updating your password.</p>
            </div>
            <div class="mb-3">
                <form action="{{ route('updateUserPassword') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3 md:px-5">
                        <label class="block font-medium text-sm text-gray-700" for="old_password">Current Password</label>
                        <input class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full p-2" id="old_password" name="old_password" type="password" required>
                    </div>
                    @error('old_password')
                    <div class="text-red-600 mb-3 md:px-5" role="alert">
                            <p class="italic">{{ $message }}</p>
                        </div>
                    @enderror
                    <div class="mb-3 md:px-5">
                        <label class="block font-medium text-sm text-gray-700" for="email">New Password</label>
                        <input class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full p-2" id="password" name="password" type="password" required>
                    </div>
                    @error('password')
                    <div class="text-red-600 mb-3 md:px-5" role="alert">
                            <p class="italic">{{ $message }}</p>
                        </div>
                    @enderror
                    <div class="mb-3 md:px-5 ">
                        <label class="block font-medium text-sm text-gray-700" for="password_confirmation">Confirm Password</label>
                        <input class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full p-2" id="password_confirmation" name="password_confirmation" type="password" required>
                    </div>
                    <div class="md:px-5">
                        <input type="submit" value="Update" class=" bg-slate-900 hover:bg-slate-800 cursor-pointer text-slate-100 hover:text-white px-3 py-1 rounded">
                    </div>
                    
                </form>

            </div>
        </div>

        <div class="p-5 sm:p-8 bg-red-800 shadow sm:rounded-lg" id="updateUserPassword">
            <div class="mb-3">
                <h2 class="font-bold text-lg text-center text-slate-200 mb-3"><i class="fa-solid fa-skull-crossbones"></i> Delete account</h2>
                <p class="text-slate-200 mb-3 text-center">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                <div class="mb-3">
                    <form action="{{route('deleteUser')}}" method="post">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class=" bg-slate-200 hover:bg-red-500 cursor-pointer text-red-900 hover:text-white px-3 py-1 rounded mx-auto block" onclick="return confirm('Are you sure you want to delete your account?')" ><i class="fa-solid fa-triangle-exclamation"></i> Delete account</button>
                    </form>
                    
                </div>

            </div>
        </div>
    </div>

    <script>
        @if($errors->get('old_password') || $errors->get('password'))
            document.getElementById('updateUserPassword').scrollIntoView();
        @elseif($errors->get('name') || $errors->get('email'))
            document.getElementById('updateProfile').scrollIntoView();
        @endif
    </script>


    
</x-layout>
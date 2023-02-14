<x-layout :page="$page">
    <div class="grid justify-items-stretch text-slate-100">
        <div class="justify-self-center my-24 bg-slate-200 text-slate-800 py-10 px-7 md:px-16 w-4/5 md:w-auto">
            <h1 class="font-bold mb-4 text-2xl md:text-3xl"><i class="fa-regular fa-message"></i> Incognito Chat App</h1>
            <form action="{{ route('storeUser') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="name" class="block mb-3 md:text-lg">Name: </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Your name" class="rounded p-2 w-full">
                </div>
                @error('name')
                <div class="text-red-600 mb-3" role="alert">
                        <p class="italic">{{ $message }}</p>
                    </div>
                @enderror

                 <div class="mb-3">
                    <label for="email" class="block mb-3 md:text-lg">Email Address: </label>
                    <input type="text" name="email" id="email" value="{{ old('email') }}" placeholder="email@email.com" class="rounded p-2 w-full">
                </div>
                @error('email')
                <div class="text-red-600 mb-3" role="alert">
                        <p class="italic">{{ $message }}</p>
                    </div>
                @enderror

                <div class="mb-3">
                    <label for="password" class="block mb-3 md:text-lg">Password: </label>
                    <input type="password" name="password" id="password" class="rounded p-2 w-full" placeholder="Your password">
                </div>
                @error('password')
                <div class="text-red-600 mb-3" role="alert">
                        <p class="italic">{{ $message }}</p>
                    </div>
                @enderror
                <div class="mb-4">
                    <label for="password_confirmation" class="block mb-3 md:text-lg">Confirm Password: </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="rounded p-2 w-full" placeholder="Re-type password">
                </div>

                <div class="mb-3">
                    <label for="profile_image" class="block mb-3 md:text-lg">Profile image: </label>
                    <input type="file" name="profile_image" id="profile_image" class="block w-full text-sm text-slate-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-violet-50 file:text-slate-700
                    hover:file:bg-violet-100" accept="image/*">
                </div>
                @error('profile_image')
                <div class="text-red-600 mb-3" role="alert">
                        <p class="italic">{{ $message }}</p>
                    </div>
                @enderror
                <input type="submit" value="Register" class="w-full bg-slate-900 hover:bg-slate-800 cursor-pointer text-slate-100 hover:text-white px-5 py-2 rounded">
            </form>
            <div class="my-4 italic">
                <p>Already have an account? <a href="{{route('login')}}" class="font-bold">Login Now.</a></p>
            </div>
        </div>
    </div>

    
</x-layout>
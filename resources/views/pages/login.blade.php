<x-layout :page="$page">
    <div class="grid justify-items-stretch text-slate-100">
        <div class="justify-self-center mt-24 bg-slate-200 text-slate-800 py-10 px-7 md:px-16 w-4/5 md:w-auto">
            <h1 class="font-bold mb-4 text-2xl md:text-3xl"><i class="fa-regular fa-message"></i> Incognito Chat App</h1>
            <form action="{{ route('authenticateUser') }}" method="post">
                @csrf

                @error('invalid')
                    <div class="my-3" role="alert">
                        {{ $message }}
                    </div>
                @enderror

                 <div class="mb-3">
                    <label for="email" class="block mb-3 text-lg">Email Address: </label>
                    <input type="text" name="email" id="email" value="{{ old('email') }}" placeholder="email@email.com" class="rounded p-2 w-full">
                </div>
                @error('email')
                <div class="text-red-600 mb-3" role="alert">
                        <p class="italic">{{ $message }}</p>
                    </div>
                @enderror

                <div class="mb-3">
                    <label for="password" class="block mb-3 text-lg">Password: </label>
                    <input type="password" name="password" id="password" class="rounded p-2 w-full" placeholder="Enter password">
                </div>
                @error('password')
                <div class="text-red-600 mb-3" role="alert">
                        <p class="italic">{{ $message }}</p>
                    </div>
                @enderror
                <div class="mb-4">
                    <label for="password_confirmation" class="block mb-3 text-lg">Confirm Password: </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="rounded p-2 w-full" placeholder="Re-type password">
                </div>
                <input type="submit" value="Login" class="w-full bg-slate-900 hover:bg-slate-800 cursor-pointer text-slate-100 hover:text-white px-5 py-2 rounded">
            </form>
            <div class="my-4 italic">
                <p>Don't have an account? <a href="{{route('register')}}" class="font-bold">Register Now.</a></p>
            </div>
        </div>
    </div>

    
</x-layout>
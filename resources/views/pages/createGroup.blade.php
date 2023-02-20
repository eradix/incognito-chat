<x-layout :page="$page">
    <div class="grid justify-items-stretch text-slate-100">
        <div class="justify-self-center mt-24 bg-slate-200 text-slate-800 py-10 px-7 md:px-16 w-4/5 md:w-auto">
            <h1 class="font-bold mb-4 text-2xl md:text-3xl"><i class="fa-regular fa-message"></i> Create a group chat</h1>
            <form action="{{ route('storeGroup') }}" method="post">
                @csrf

                 <div class="mb-3">
                    <label for="group_name" class="block mb-3 text-lg">Group chat name: </label>
                    <input type="text" name="group_name" id="group_name" value="{{ old('group_name') }}" placeholder="Group chat name" class="rounded p-2 w-full">
                </div>
                @error('group_name')
                <div class="text-red-600 mb-3" role="alert">
                        <p class="italic">{{ $message }}</p>
                    </div>
                @enderror

                <div class="mb-3">
                    <label class="block mb-3 text-lg" for="grid-state">
                      Add users:
                      <div class="flex ml-5">
                        <div>
                        @foreach ($otherUsers as $otherUser)
                            <div class="form-check">
                                <input type="checkbox" name="user_ids[]" id="{{"user_ids_{$otherUser->id}"}}" value="{{$otherUser->id}}" class="rounded p-2 " @if (old('user_ids') && in_array($otherUser->id, old('user_ids'))) {{"checked"}} @endif>
                                <label for="{{"user_ids_{$otherUser->id}"}}" class="mb-3 text-lg text-slate-600 cursor-pointer">{{ $otherUser->name }} </label>
                            </div>
                        @endforeach

                        </div>
                      </div>
                  </div>
                  @error('user_ids')
                  <div class="text-red-600 mb-3" role="alert">
                          <p class="italic">Please select atleast 2 user to create a group chat</p>
                      </div>
                  @enderror


                <input type="submit" value="Create Group" class="w-full bg-slate-900 hover:bg-slate-800 cursor-pointer text-slate-100 hover:text-white px-5 py-2 rounded mb-3">
                <a href="{{ route('home') }}"  class="text-center block w-full bg-slate-600 hover:bg-slate-800 cursor-pointer text-slate-100 hover:text-white px-5 py-2 rounded">Cancel</a>
            </form>
        </div>
    </div>

    
</x-layout>
<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ChatController extends Controller
{
    //home page
    public function home()
    {
        $user = User::find(auth()->user()->id);

        $user_id = auth()->user()->id;

        //get other users id
        $otherUsersId = User::whereNot('id', auth()->user()->id)
            ->orderBy("status")
            ->orderBy("name")
            ->pluck('id');

        $latest_messages = [];

        foreach ($otherUsersId as $key => $other_user_id) {
            $chats = Chat::where(function ($query) use ($user_id, $other_user_id) {
                $query->where('sender_id', $user_id)
                    ->where('receiver_id', $other_user_id);
            })->orWhere(function ($query) use ($user_id, $other_user_id) {
                $query->where('sender_id', $other_user_id)
                    ->where('receiver_id', $user_id);
            })->latest()->first();

            //check if user has a message history
            if (null != $chats) {
                $now = new Carbon();
                $created_at = new Carbon($chats->created_at);
                $differenceInDays = $now->diffInDays($created_at->format('Y/m/d'));

                $latest_messages[$key]['name'] = User::find($other_user_id)->name;
                $latest_messages[$key]['status'] = User::find($other_user_id)->status;
                $latest_messages[$key]['message'] = $chats->message;
                $latest_messages[$key]['sender'] = $chats->sender_id == $user_id ? "self" : "";
                $latest_messages[$key]['id'] = $other_user_id;
                $latest_messages[$key]['created_at'] = $differenceInDays < 1 ? $chats->created_at->format('h:i A') : $created_at->format("m/d");
                $latest_messages[$key]['orig_date'] = $chats->created_at;
                $latest_messages[$key]['profile_image'] = User::find($other_user_id)->profile_image;
                $latest_messages[$key]['image_name'] = User::find($other_user_id)->imageName();
                $latest_messages[$key]['hasRead'] = ($chats->hasRead == 2 &&  $chats->sender_id != $user_id) ? "unread" : "";
            } else {
                $latest_messages[$key]['name'] = User::find($other_user_id)->name;
                $latest_messages[$key]['status'] = User::find($other_user_id)->status;
                $latest_messages[$key]['message'] = "";
                $latest_messages[$key]['sender'] = "";
                $latest_messages[$key]['id'] = $other_user_id;
                $latest_messages[$key]['created_at'] = "";
                $latest_messages[$key]['orig_date'] = "";
                $latest_messages[$key]['profile_image'] = User::find($other_user_id)->profile_image;
                $latest_messages[$key]['image_name'] = User::find($other_user_id)->imageName();
                $latest_messages[$key]['hasRead'] = "";
            }
        }

        //sort author messages by date
        usort($latest_messages, function ($a, $b) {
            return strtotime($b['orig_date']) - strtotime($a['orig_date']);
        });

        //ddd($latest_messages);

        return view('pages.home', [
            'page' => 'Home',
            'user' => $user,
            'latest_messages' => $latest_messages,
            'otherUsersId' => $other_user_id
        ]);
    }
    public function showChat($id)
    {
        $user_id = auth()->user()->id;
        $other_user_id = $id;

        $user = User::find($user_id);
        $receiverUser = User::find($id);

        $chats = Chat::where(function ($query) use ($user_id, $other_user_id) {
            $query->where('sender_id', $user_id)
                ->where('receiver_id', $other_user_id);
        })->orWhere(function ($query) use ($user_id, $other_user_id) {
            $query->where('sender_id', $other_user_id)
                ->where('receiver_id', $user_id);
        })->get();

        //update status to read
        Chat::where('sender_id', $other_user_id)
            ->where('receiver_id', $user_id)
            ->where('hasRead', 2)
            ->update(['hasRead' => 1]);

        return view('pages.chat', [
            'page' => 'Chat',
            'user' => $user,
            'receiverUser' => $receiverUser,
            'chats' => $chats,

        ]);
    }
    //send chat/message
    public function storeChat(Request $request)
    {
        $data = $request->validate([
            'message' => 'required',
            'receiver_id' => 'required'
        ]);
        $data['hasRead'] = 2; //set to unread
        $data['sender_id'] = auth()->user()->id;

        Chat::create($data);

        return  response()->json(['message' => "Created successfully"]);
    }

    //fetch the newest chat realtime
    public function fetchChat($id)
    {
        $user_id = auth()->user()->id;
        $other_user_id = $id;

        $chats = Chat::where(function ($query) use ($user_id, $other_user_id) {
            $query->where('sender_id', $user_id)
                ->where('receiver_id', $other_user_id);
        })->orWhere(function ($query) use ($user_id, $other_user_id) {
            $query->where('sender_id', $other_user_id)
                ->where('receiver_id', $user_id);
        })->get();

        //update status to read
        Chat::where('sender_id', $other_user_id)
            ->where('receiver_id', $user_id)
            ->where('hasRead', 2)
            ->update(['hasRead' => 1]);

        return  response()->json(['chats' => $chats]);
    }

    //fetch the newest chat per other user
    public function fetchChatPerUser()
    {
        $user_id = auth()->user()->id;

        //get other users id
        $otherUsersId = User::whereNot('id', auth()->user()->id)
            ->orderBy("status")
            ->orderBy("name")
            ->pluck('id');

        $latest_messages = [];

        foreach ($otherUsersId as $key => $other_user_id) {
            $chats = Chat::where(function ($query) use ($user_id, $other_user_id) {
                $query->where('sender_id', $user_id)
                    ->where('receiver_id', $other_user_id);
            })->orWhere(function ($query) use ($user_id, $other_user_id) {
                $query->where('sender_id', $other_user_id)
                    ->where('receiver_id', $user_id);
            })->latest()->first();

            //check if user has a message history
            if (null != $chats) {
                $now = new Carbon();
                $created_at = new Carbon($chats->created_at);
                $differenceInDays = $now->diffInDays($created_at->format('Y/m/d'));

                $latest_messages[$key]['name'] = User::find($other_user_id)->name;
                $latest_messages[$key]['status'] = User::find($other_user_id)->status;
                $latest_messages[$key]['message'] = $chats->message;
                $latest_messages[$key]['sender'] = $chats->sender_id == $user_id ? "self" : "";
                $latest_messages[$key]['id'] = $other_user_id;
                $latest_messages[$key]['created_at'] = $differenceInDays < 1 ? $chats->created_at->format('h:i A') : $created_at->format("m/d");
                $latest_messages[$key]['orig_date'] = $chats->created_at;
                $latest_messages[$key]['profile_image'] = User::find($other_user_id)->profile_image;
                $latest_messages[$key]['image_name'] = User::find($other_user_id)->imageName();
                $latest_messages[$key]['hasRead'] = ($chats->hasRead == 2 &&  $chats->sender_id != $user_id) ? "unread" : "";
            } else {
                $latest_messages[$key]['name'] = User::find($other_user_id)->name;
                $latest_messages[$key]['status'] = User::find($other_user_id)->status;
                $latest_messages[$key]['message'] = "";
                $latest_messages[$key]['sender'] = "";
                $latest_messages[$key]['id'] = $other_user_id;
                $latest_messages[$key]['created_at'] = "";
                $latest_messages[$key]['orig_date'] = "";
                $latest_messages[$key]['profile_image'] = User::find($other_user_id)->profile_image;
                $latest_messages[$key]['image_name'] = User::find($other_user_id)->imageName();
                $latest_messages[$key]['hasRead'] = "";
            }
        }
        //sort author messages by date
        usort($latest_messages, function ($a, $b) {
            return strtotime($b['orig_date']) - strtotime($a['orig_date']);
        });

        return  response()->json(['latest_messages' => $latest_messages]);
    }
}

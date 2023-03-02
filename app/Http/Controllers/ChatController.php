<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Chat;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupUser;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    //home page
    public function home()
    {

        $user = User::find(auth()->user()->id);

        $user_id = auth()->user()->id;

        //DIRECT MESSAGES
        //get other users id
        $otherUsersId = User::whereNot('id', auth()->user()->id)
            ->orderBy("status")
            ->orderBy("name")
            ->pluck('id');

        $latest_messages = [];

        foreach ($otherUsersId as $key => $other_user_id) {
            $chats = Chat::where(function ($query) use ($user_id, $other_user_id) {
                $query->where('sender_id', $user_id)
                    ->where('receiver_id', $other_user_id)
                    ->where('group_id', null);
            })->orWhere(function ($query) use ($user_id, $other_user_id) {
                $query->where('sender_id', $other_user_id)
                    ->where('receiver_id', $user_id)
                    ->where('group_id', null);
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
            $latest_messages[$key]['group'] = "";
        }

        //GROUP CHAT MESSAGES
        //get all group chat that the current user is a member
        $user_group = User::find($user_id)->groups()->orderBy('group_name')->get();

        foreach ($user_group as $group) {
            $group_msg =  $group->chat()->latest()->first();
            $members = $group->members;

            //check if group chat already has a message
            if (null != $group_msg) {

                $now = new Carbon();
                $created_at = new Carbon($group_msg->created_at);
                $differenceInDays = $now->diffInDays($created_at->format('Y/m/d'));


                $group_arr = [
                    'group' => $group->group_name,
                    'name' => '',
                    'status' => $members->contains(function ($user) use ($user_id) {
                        return $user->status == 1 && $user->id != $user_id;
                    }),
                    'message' => $group_msg->message,
                    'sender' => $group_msg->sender_id == $user_id ? "self" : User::find($group_msg->sender_id)->name,
                    'id' => $group->id, //id of group
                    'created_at' => $differenceInDays < 1 ? $group_msg->created_at->format('h:i A') : $created_at->format("m/d"),
                    'orig_date' =>  $group_msg->created_at,
                    'profile_image' => '',
                    'image_name' => $group->imageName(),
                    'hasRead' => (!in_array($user_id, explode(",", $group_msg->groupRead)) &&  $group_msg->sender_id != $user_id) ? "unread" : "",
                ];
            } else {
                $group_arr = [
                    'group' => $group->group_name,
                    'name' => '',
                    'status' => $members->contains(function ($user) use ($user_id) {
                        return $user->status == 1 && $user->id != $user_id;
                    }),
                    'message' => "",
                    'sender' => "",
                    'id' => $group->id, //id of group
                    'created_at' => "",
                    'orig_date' =>  "",
                    'profile_image' => '',
                    'image_name' => $group->imageName(),
                    'hasRead' =>  "",
                ];
            }

            $latest_messages[] =  $group_arr;
        }

        //sort messages by date desc
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
    public function showChat($id, $group = null)
    {
        $user_id = auth()->user()->id;
        $other_user_id = $id;
        $user = User::find($user_id);

        //if for group chat
        if ($group && strtolower($group) == "group") {
            $group_members = Group::find($id)->members;
            //check if current user is a member of group 
            if ($group_members->contains('id', $user_id)) {
                $chats = Group::find($id)->chat; //all chats in the group
                $receiverInfo = Group::find($id);
                $notMembers = User::whereNotIn('id', $group_members->pluck('id'))->orderBy('name')->get();
                //update the groupRead/has read for group chats
                foreach ($chats as $chat) {
                    $hasRead = $chat->groupRead ? explode(',', $chat->groupRead) : [];
                    if (!in_array($user_id, $hasRead)) {
                        $hasRead[] = $user_id;
                        $newGroupRead = implode(',', $hasRead);

                        //update the groupRead field
                        Chat::where('chat_id', $chat->chat_id)->update(['groupRead' => $newGroupRead]);
                    }
                }
            } else {
                return abort(404);
            }
        }
        //for direct message
        else if (!$group) {
            if ($id == $user_id || User::find($id) == null) {
                return abort(404);
            }

            $receiverInfo = User::find($id);

            $chats = Chat::where(function ($query) use ($user_id, $other_user_id) {
                $query->where('sender_id', $user_id)
                    ->where('receiver_id', $other_user_id);
            })->orWhere(function ($query) use ($user_id, $other_user_id) {
                $query->where('sender_id', $other_user_id)
                    ->where('receiver_id', $user_id);
            })->get();

            $chats = $chats->sortBy('created_at');
            //update status to read
            Chat::where('sender_id', $other_user_id)
                ->where('receiver_id', $user_id)
                ->where('hasRead', 2)
                ->update(['hasRead' => 1]);
        }
        //if group is not equal to group
        else if (strtolower($group) != "group") {
            return abort(404);
        }
        //dd($notMembers ?? "");
        return view('pages.chat', [
            'page' => 'Chat',
            'user' => $user,
            'receiverInfo' => $receiverInfo,
            'chats' => $chats,
            'isGroup' => $group,
            'notMembers' => $notMembers ?? "",
        ]);
    }

    //send chat/message
    public function storeChat(Request $request)
    {

        $data = $request->validate([
            'message' => 'required',
            'receiver_id' => 'sometimes',
            'group_id' => 'sometimes'
        ]);

        $data['hasRead'] = $data['receiver_id'] ? 2 : null; //set to unread if direct message, null if group chat
        $data['sender_id'] = auth()->user()->id;

        $chat = Chat::create($data);

        return  response()->json(['message' => "Created successfully", 'chat' => $chat]);
    }

    //update chat/message
    public function updateChat(Request $request)
    {

        $data = $request->validate([
            'message' => 'required',
            'receiver_id' => 'sometimes',
            'group_id' => 'sometimes',
            'chat_id' => 'required'
        ]);

        //update chat
        Chat::where('chat_id', $data['chat_id'])->update($data);

        return  response()->json(['message' => "updated successfully"]);
    }

    //delete chat/message
    public function deleteChatMsg(Request $request)
    {

        $data = $request->validate([
            'chat_id' => 'required'
        ]);

        //update chat
        Chat::where('chat_id', $data['chat_id'])->delete();

        return  response()->json(['message' => "deleted successfully"]);
    }

    //show create group page
    public function createGroup()
    {
        $user = User::find(auth()->user()->id);

        //get other users id
        $otherUsers = User::whereNot('id', $user->id)
            ->orderBy("name")
            ->get();

        return view('pages.createGroup', [
            'page' => 'Group',
            'user' => $user,
            'otherUsers' => $otherUsers
        ]);
    }

    //store a new groupchat along with its members
    public function storeGroup(Request $request)
    {
        $formData = $request->validate(
            [
                'group_name' => 'required',
                'user_ids' => 'required|array|min:2'
            ]
        );

        $formData['group_name'] = ucwords($formData['group_name']);
        $formData['creator_id'] = auth()->user()->id;
        array_push($formData['user_ids'], auth()->user()->id);

        Group::create([
            'group_name' => $formData['group_name'],
            'creator_id' => $formData['creator_id']
        ]);

        $newGroupId = Group::all()->last()->id;

        foreach ($formData['user_ids'] as $user_id) {
            GroupUser::create([
                'group_id' => $newGroupId,
                'user_id' => $user_id
            ]);
        }
        return to_route("home");
    }

    //store new user in a group
    public function addUserInAGroup(Request $request, $group_id)
    {
        $formData = $request->validate([
            'user_id' => 'required'
        ]);

        $formData['group_id'] = $group_id;

        GroupUser::create($formData);

        return to_route("showChat", [$group_id, 'group']);
    }


    //leave user in a group
    public function leaveInAGroup($group_id)
    {
        GroupUser::where('group_id', $group_id)
            ->where('user_id', auth()->user()->id)
            ->first()
            ->delete();

        //delete the group once it has only 2 members 
        if (count(Group::find($group_id)->members) < 3) {
            Group::find($group_id)->delete();
        }

        return to_route("home");
    }


    //fetch the newest chat realtime
    public function fetchChat($id, $group = null)
    {
        $user_id = auth()->user()->id;
        $other_user_id = $id;

        ////
        if ($group && strtolower($group) == "group") {
            $group_members = Group::find($id)->members;
            //check if current user is a member of group 
            if ($group_members->contains('id', $user_id)) {
                $chats = Group::find($id)->chat; //all chats in the group
                $group_infos = $chats->map(function ($chat) {
                    return ['name' => $chat->sender->name, 'profile_image' => $chat->sender->profile_image, 'imageName' => $chat->sender->imageName()];
                });

                foreach ($chats as $chat) {
                    $hasRead = $chat->groupRead ? explode(',', $chat->groupRead) : [];
                    if (!in_array($user_id, $hasRead)) {
                        $hasRead[] = $user_id;
                        $newGroupRead = implode(',', $hasRead);

                        //update the groupRead field
                        Chat::where('chat_id', $chat->chat_id)->update(['groupRead' => $newGroupRead]);
                    }
                }
            } else {
                return abort(404);
            }
        }
        //for direct message
        else if (!$group) {
            if ($id == $user_id || User::find($id) == null) {
                return abort(404);
            }

            $received = User::find($user_id)->receivedMessages()->where('sender_id', $other_user_id)->get();
            $sent = User::find($user_id)->sentMessages()->where('receiver_id', $other_user_id)->get();

            //merge two messages between the user and the receiver
            $messages = $sent->concat($received)->sortBy('created_at');

            $chats = [];
            $i = 0;
            foreach ($messages as $message) {
                $chats[$i]['chat_id'] = $message->chat_id;
                $chats[$i]['sender_id'] = $message->sender_id;
                $chats[$i]['receiver_id'] = $message->receiver_id;
                $chats[$i]['message'] = $message->message;
                $chats[$i]['hasRead'] = $message->hasRead;
                $chats[$i]['created_at'] = $message->created_at;
                $chats[$i]['group_id'] = $message->group_id;
                $chats[$i]['groupRead'] = $message->groupRead;
                $i++;
            }

            //update status to read
            Chat::where('sender_id', $other_user_id)
                ->where('receiver_id', $user_id)
                ->where('hasRead', 2)
                ->update(['hasRead' => 1]);
        }

        //if group is not equal to group
        else if (strtolower($group) != "group") {
            return abort(404);
        }

        return  response()->json(['chats' => $chats, 'isGroup' => $group, 'groupInfos' => $group_infos ?? null]);
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
                    ->where('receiver_id', $other_user_id)
                    ->where('group_id', null);
            })->orWhere(function ($query) use ($user_id, $other_user_id) {
                $query->where('sender_id', $other_user_id)
                    ->where('receiver_id', $user_id)
                    ->where('group_id', null);
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
            $latest_messages[$key]['group'] = "";
        }

        //group chats
        $user_group = User::find($user_id)->groups()->orderBy('group_name')->get();

        foreach ($user_group as $group) {
            $group_msg =  $group->chat()->latest()->first();

            $now = new Carbon();
            $created_at = new Carbon($group_msg->created_at);
            $differenceInDays = $now->diffInDays($created_at->format('Y/m/d'));
            $members = $group->members;

            $group_arr = [
                'group' => $group->group_name,
                'name' => '',
                'status' => $members->contains(function ($user) use ($user_id) {
                    return $user->status == 1 && $user->id != $user_id;
                }),
                'message' => $group_msg->message,
                'sender' => $group_msg->sender_id == $user_id ? "self" : User::find($group_msg->sender_id)->name,
                'id' => $group->id, //id of group
                'created_at' => $differenceInDays < 1 ? $group_msg->created_at->format('h:i A') : $created_at->format("m/d"),
                'orig_date' =>  $group_msg->created_at,
                'profile_image' => '',
                'image_name' => $group->imageName(),
                'hasRead' => (!in_array($user_id, explode(",", $group_msg->groupRead)) &&  $group_msg->sender_id != $user_id) ? "unread" : "",
            ];

            $latest_messages[] =  $group_arr;
        }
        //sort author messages by date
        usort($latest_messages, function ($a, $b) {
            return strtotime($b['orig_date']) - strtotime($a['orig_date']);
        });

        return  response()->json(['latest_messages' => $latest_messages]);
    }
}

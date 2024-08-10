<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;
use App\Library\Constants\ResponseCode;

class TodoController extends Controller
{

    public function getTasks(Request $request)
    {
        $tasks = Todo::all();

        $data = [
            'tasks' => $tasks,
        ];
        return view('home', $data);
    }

    public function createTasks(Request $request)
    {
        // validate the body
        $request->validate([
            'data.title' => 'required',
            'data.description' => 'required',
        ]);

        $data = $request->all();

        $todoTitle = $data['data']['title'];
        $todoDescription = $data['data']['description'];

        $newTodo = new Todo();
        $newTodo->title = $todoTitle;
        $newTodo->description = $todoDescription;
        $newTodo->save();
        $content = [
            'Message' => "Request Processed Successfully",
            'Code' => ResponseCode::STATUS_CREATED,
        ];
        return response($content, ResponseCode::STATUS_CREATED)
            ->header('Content-Type', 'application/json');
    }

    public function updateTasks(Request $request, $id)
    {
        // validate the body
        $request->validate([
            'data.title' => 'required',
            'data.description' => 'required',
        ]);

        $data = $request->all();

        $todoTitle = $data['data']['title'];
        $todoDescription = $data['data']['description'];

        // Todo::where('id', $id)->update(['title' => $todoTitle, 'description' => $todoDescription]);

        $content = [
            'Message' => "Request Processed Successfully",
            'Code' => ResponseCode::STATUS_OK,
        ];

        $existingTodo = Todo::find($id);

        if ($existingTodo === null) {
            $content['Message'] = "Task not found to update";
        } else {
            $existingTodo->title = $todoTitle;
            $existingTodo->description = $todoDescription;
            $existingTodo->save();
        }

        return response($content, ResponseCode::STATUS_OK)
            ->header('Content-Type', 'application/json');
    }

    public function deleteTask(Request $request, $id)
    {
        $deleted = Todo::where('id', $id)->delete();

        $content = [
            'Message' => "Request Processed Successfully",
            'Code' => ResponseCode::STATUS_OK,
        ];

        if ($deleted !== false) {
            $content['Message'] = "Couldn't delete the task";
        }
        return response($content, ResponseCode::STATUS_OK)
            ->header('Content-Type', 'application/json');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Todo;
use Validator;
class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Todo::latest()->get();
        return view('todo',compact('tasks'));
    }

    public function geTasks()
    {
        $tasks = Todo::latest()->where('status',0)->get();
        return $tasks;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'task'=>'required|unique:todos'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => $validator->errors()
            ]);
        }

        $task =  Todo::create($request->all());
        if(!empty($task)){
            return response()->json([
                'success' => true,
                'message' => "Task created successfully"
            ], 201); 
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function showAlltask()
    {
        $todos = Todo::withTrashed()->get();
        return $todos;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function completeTask(Request $request)
    {
        // dd($request->all());

       return Todo::find($request->id)->update(['status'=>1]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        // dd(123,$request->id);

        $taskDeleted = Todo::find($request->id)->delete();
        return $taskDeleted;
    }
}

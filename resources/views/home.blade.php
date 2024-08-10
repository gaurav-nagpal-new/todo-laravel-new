<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <style>
        .task-container {
            border: 2px solid grey;
            border-radius: 5px;
            padding: 1.2%;
        }
    </style>
</head>

<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="task-container">
            <h2 class="text-center">TODO APP</h2>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Created On</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="task-table-body">
                    @foreach($tasks as $task)
                    <tr id="{{$task->id}}">
                        <td class="task-title">{{$task->title}}</td>
                        <td class="task-desc">{{$task->description}}</td>
                        <td>{{(new DateTime($task->created_at))->format('d M Y')}}</td>
                        <td>
                            <button class="btn btn-primary edit-task" id="{{$task->id}}" data-bs-toggle="modal" data-bs-target="#addTaskModal">Edit</button>
                            <button class="btn btn-danger delete-task" id="{{$task->id}}">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-center">
                <button class="btn btn-primary" id="add-task" data-bs-toggle="modal" data-bs-target="#addTaskModal">Add Task</button>
                <!-- Modal  to add task-->
                <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="modal-task-header"></h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <label for="modal-title">Title</label>
                                <input type="text" class="form-control" id="modal-title">

                                <label for="modal-desc">Description</label>
                                <textarea type="text" class="form-control" id="modal-desc"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="add-task-btn"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var editRowID;
        // use jquery to define functions when the page loads
        $(document).ready(function() {
            // function to delete the task from the table 
            $('.delete-task').on('click', function() {
                // with the row
                var rowToDelete = $(this).closest('tr')
                var rowID = rowToDelete.attr('id');

                // remove from the backend - remove from UI only if ajax request is success
                deleteTaskAajx(rowID, rowToDelete);
            });

            $('#add-task-btn').on('click', function() {
                var title = $('#modal-title').val();
                var description = $('#modal-desc').val();

                // TODO: if title or description is empty then show warning toast
                if (title == "" || description == "") {
                    console.log("Title or description can't be empty")
                    return;
                }
                // now send this data to POST ajax request
                var data = {
                    'data': {
                        'title': title,
                        'description': description,
                    }
                };

                // check if its creation request or edit request
                if ($('#modal-task-header').text() == "Edit Task") {
                    return updateTaskAjax(data);
                }
                return createTaskAjax(data);
            });

            $('.edit-task').on('click', function() {
                // get the title and description with row selected
                var selectedRow = $(this).closest('tr');
                editRowID = selectedRow.attr('id');
                var title = selectedRow.find('.task-title').text();
                var desc = selectedRow.find('.task-desc').text();

                // change the modal text as this will be edit task modal.
                $('#modal-task-header').text('Edit Task');
                $('#modal-title').val(title);
                $('#modal-desc').val(desc);
                $('#add-task-btn').text('Edit');
            });

            $('#add-task').on('click', function() {
                // change the modal text as this will be add task modal.
                $('#modal-task-header').text('Add New Task');
                $('#modal-title').val('');
                $('#modal-desc').val('');
                $('#add-task-btn').text('Create');
            });

            function createTaskAjax(data) {
                $.ajax(
                    "api/tasks", {
                        type: "POST",
                        data: data,
                        success: function(data) {
                            if (data.Code == 201) {
                                //TODO: change to toast
                                console.log("Task Created");

                                // TODO: receive id and created_at from backend and add new row in UI
                                // instead of reloading
                                location.reload();
                                return
                            }
                            // change to toast
                            console.log("Request Unsuccess");
                        },
                        error: function() {
                            // change to toast
                            console.log("error performing request");
                        }
                    }
                )
            }

            function updateTaskAjax(data) {
                $.ajax(
                    `api/tasks/${editRowID}`, {
                        type: "PUT",
                        data: data,
                        success: function(data) {
                            if (data.Code == 200) {
                                //TODO: change to toast
                                console.log("Task Updated");

                                // TODO: receive id and created_at from backend and add new row in UI
                                // instead of reloading
                                location.reload();
                                return
                            }
                            // change to toast
                            console.log("Request Unsuccess");
                        },
                        error: function() {
                            // change to toast
                            console.log("error performing request");
                        }
                    }
                )
                editRowID = "";
            }

            function deleteTaskAajx(rowID, rowToDelete) {
                //TODO: can use loader 
                $.ajax(
                    `api/tasks/${rowID}`, {
                        type: "DELETE",
                        success: function(data) {
                            if (data.Code !== 200) {
                                // TODO: convert this to toast message
                                console.log("unable to process request")
                                return
                            }
                            // remove the row from UI
                            rowToDelete.remove();
                        },
                        error: function() {
                            // TODO: convert this to toast message
                            console.log("error occurred while performing request");
                        }
                    }
                );
            }
        });

        function showToastMessage(message) {
            Toast.create("Voila!", "How easy was that?", TOAST_STATUS.SUCCESS, 5000);
        }
    </script>
</body>

</html>
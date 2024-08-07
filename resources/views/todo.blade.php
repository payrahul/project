<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
         /* Custom CSS for modal */
         .modal-dialog {
            pointer-events: none; /* Disable clicking outside the modal */
        }
        .modal-content {
            pointer-events: auto; /* Enable clicking inside the modal */
        }
        .header {
            background-color: #f8f9fa; /* Light background color */
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center; /* Center align text */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="header">
                    A simple Todo list
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-6">
                <section >
                        <br><br>
                    <div class="form-group pt-3">
                        <input type="text" class="form-control" id="task">
                        <div id="task-message" style="color: red;"></div>
                    </div>
                    <div class="form-group">
                    <button type="button" id="add-task" class="btn btn-default">Add Task</button>
                    </div>

                    
                    <div class="button-section">
                        <button type="button" id="show-alltask" class="btn btn-default">Show all</button>
                    </div>
                </section>
                <section>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Task</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </section>
               
            </div>
            <div class="col-sm-2"></div>
        </div>
    </div>
    <div class="modal fade" id="deleteTaskModal" tabindex="-1" role="dialog" aria-labelledby="deleteTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteTaskModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure to delete this Task?
                    <input type="hidden" id="confirm_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary confirm yes">Yes</button>
                    <button type="button" class="btn btn-primary confirm no">No</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    
    <script>
    $(document).ready(function() {

        getTasksData();
        $("#task").focus(function() {
                $("#task-message").text('');
            });
        $("#add-task").click(function(event) { 

            event.preventDefault();

            var task = $("#task").val();
            if (task.trim() === "") {
                $('#task-message').text('Task cannot be empty!');
            } else if (task.length > 20) {
                $("#task").val('');
                $('#task-message').text('Task must be 20 characters or less!');
            } else {
                $("#task-message").text('');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/save-task",
                    type : 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "task":$('#task').val(),
                        "status":0
                    },
                    success: function(result){
                        $('#task-message').empty();
                        console.log(result);
                        if(result.success == true){
                            $('#task-message').text(result.message);
                        }else{
                            $('#task-message').text(result.message.task[0]);
                        }
                        $("#task").val('');
                        $("tbody").empty();
                        getTasksData()
                    }
                });
            }
        });

        function getTasksData(){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/get-tasks",
                type : 'GET',
                data: {
                    "_token": "{{ csrf_token() }}",
                    
                },
                success: function(result){
                    var i =1;
                    $.each(result, function(key, value) {
                        let checkboxHtml = value.status === 0? `<input class="form-check-input" type="checkbox" value="${value.id}" id="complete-btn">` : "&nbsp;&nbsp;&nbsp;";

                        var htmlToAdd = `
                            <tr>
                                <td>${i++}</td>
                                <td>${value.task}</td>
                                <td>${ (value.status == 1) ? "Completed" : "Non completed"}</td>
                                
                                <td>
                                    <div class="form-check">
                                    ${checkboxHtml}
                                    <i class="fa fa-window-close" aria-hidden="true" value="${value.id}" id="close-btn" ></i>
                                    </div>

                                </td>
                            </tr>
                            `;
                        $("tbody").append(htmlToAdd);
                    });
                }
            });
        }

        $(document).on('click', '#close-btn', function(event) {
            
            $('#deleteTaskModal').modal('show');
            $("#confirm_id").empty()
            $('#confirm_id').val($(this).attr('value'));

        });

        $(document).on('click', '.confirm', function(event) {
            if ($(this).hasClass('yes')) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/delete-task",
                    type : 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "id":$('#confirm_id').val()
                    },
                    success: function(result){
                    $("tbody").empty();
                    getTasksData()
                    $('#deleteTaskModal').modal('hide');
                }});
            } else {
                $('#deleteTaskModal').modal('hide');
            }
        });
        $(document).on('click', '#complete-btn', function(event) {
            if ($(this).is(':checked')) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/complete-task",
                    type : 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "id":$(this).attr('value')
                    },
                    success: function(result){
                    $("tbody").empty();
                    getTasksData()
                }});
            } else {
            }
        });

        $(document).on('click', '#show-alltask', function(event) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/show-alltask",
                type : 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(result){
                    $("tbody").empty();
                    var i =1;
                    $.each(result, function(key, value) {
                        let checkboxHtml = value.status === 0? `<input class="form-check-input" type="checkbox" value="${value.id}" id="complete-btn">` : "&nbsp;&nbsp;&nbsp;";

                        var htmlToAdd = `
                            <tr>
                                <td>${i++}</td>
                                <td>${value.task}</td>
                                <td>${ (value.status == 1) ? "Completed" : "Non completed"}</td>
                            </tr>
                            `;
                        $("tbody").append(htmlToAdd);
                    });
            }});
        });
    });
    </script>
</body>
</html>
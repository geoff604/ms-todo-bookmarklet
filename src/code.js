    /**
    * Load the available task lists.
    */
    function loadTaskLists() {
        var fnFail = function() {
            $("#outer").html("<p>Please <a href=\"backend.php\">Login</a> and try again.</p>");
        };

        $.get("backend.php?method=getCachedTaskLists", function(data) {
            showTaskLists(data);
            $.get("backend.php?method=getTaskLists", function(data) {
                showTaskListsPreservingSelection(data);
            }).fail(fnFail);
        });
    }

    /**
    * Show the returned task lists in the dropdown box.
    * @param {Array.<Object>} taskLists The task lists to show.
    */
    function showTaskLists(taskLists) {
        var select = $('#tasklist');
        select.empty();
        taskLists.forEach(function(taskList) {
            var option = $('<option>')
            .attr('value', taskList.id)
            .text(taskList.title);
            select.append(option);
        });
    }

    /**
    * Show the returned task lists in the dropdown box.
    * The original selection in the box is preserved.
    * @param {Array.<Object>} taskLists The task lists to show.
    */
    function showTaskListsPreservingSelection(taskLists) {
        var selectedValue = $('#tasklist').val();

        showTaskLists(taskLists);

        if (selectedValue) {
            $('#tasklist option[value=' + selectedValue + ']').prop('selected', true);
        }
    }

    /**
    * A callback function that runs when the new task form is submitted.
    */
    function onNewTaskFormSubmit() {
        var taskListId = $('#tasklist').val();
        var titleTextBox = $('#task-title');
        var noteTextBox = $('#task-note');
        var title = titleTextBox.val();
        var note = noteTextBox.val();
        var dateObj = $("#task-date").datepicker( "getDate" );
        var date = dateObj ? dateObj.getFullYear() + '-' + (dateObj.getMonth()+1) + '-' + dateObj.getDate() : "";

        var dataToSend = {
            postMethod: "addTask",
            taskListId: taskListId,
            title: title,
            note: note
        };

        if (date) {
            dataToSend.taskDate = date;
        }

        var jqXHR = $.ajax({
            type: "POST",
            url: 'backend.php',
            data: dataToSend,
            dataType: "text",
            success: function(data) {
                $("#outer").html("<p>Task added.</p>");
            }
        });
        jqXHR.fail(function(data) {
            $("#outer").html("<p>Unable to add task.</p>");
        });

        return false;
    }

    /**
    * Logs an error message and shows an alert to the user.
    */
    function showError(error) {
        console.log(error);
        window.alert('An error has occurred, please try again.');
    }

    // When the page loads.
    $(function() {
        $('#new-task').bind('submit', onNewTaskFormSubmit);

        datePicker = $("#task-date").datepicker({
            firstDay: 0
        });

        loadTaskLists();
    });
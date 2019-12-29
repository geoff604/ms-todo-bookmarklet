    // When the page loads.
    $(function() {
        $('#new-task').bind('submit', onNewTaskFormSubmit);

        datePicker = $("#task-date").datepicker({
            firstDay: 0
        });

        loadTaskLists();
    });

    /**
    * Load the available task lists.
    */
    function loadTaskLists() {  
        let jqXHR = $.get("backend.php?method=getTaskLists", function(data) {
            showTaskLists(data);
        });
        
        jqXHR.fail(function() {
            $("#outer").html("<p>Please <a href=\"backend.php\">Login</a> and try again.</p>");
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
        var dateArgument = "";
        if (date) {
            dateArgument = "&taskDate=" + date;
        }

        var jqXHR = $.get("backend.php?method=addTask&taskListId=" + encodeURIComponent(taskListId) +
            "&title=" + encodeURIComponent(title) + "&note=" + encodeURIComponent(note) +
            dateArgument,
            function() {
                $("#outer").html("<p>Task added.</p>");
            }
        );
        jqXHR.fail(function() {
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
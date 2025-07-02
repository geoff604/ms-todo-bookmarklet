    /**
    * Load the available task lists.
    */
    function loadTaskLists() {
        var fnFail = function() {
            $("#outer").html("<p>Please <a href=\"backend.php\">Login</a> and try again.</p>");
        };

        $.get("backend.php?method=getCachedTaskLists", function(cachedData) {
            showTaskLists(cachedData);
            $.get("backend.php?method=getTaskLists", function(freshData) {
                if (JSON.stringify(cachedData) !== JSON.stringify(freshData)) {
                    scheduleTaskListUpdate(freshData);
                }
            }).fail(fnFail);
        });
    }

    // Variables to track user interaction with dropdown
    var isUserInteracting = false;
    var interactionTimer = null;
    var pendingUpdate = null;

    /**
    * Schedule a task list update, but delay it if user is actively interacting
    * @param {Array.<Object>} taskLists The task lists to show.
    */
    function scheduleTaskListUpdate(taskLists) {
        pendingUpdate = taskLists;
        
        if (isUserInteracting) {
            // User is actively interacting, wait until they're done
            return;
        }
        
        // User is not interacting, update immediately
        showTaskListsPreservingSelection(taskLists);
        pendingUpdate = null;
    }

    /**
    * Execute any pending update after user interaction has stopped
    */
    function executePendingUpdate() {
        if (pendingUpdate) {
            showTaskListsPreservingSelection(pendingUpdate);
            pendingUpdate = null;
        }
    }

    /**
    * Mark that user interaction has started
    */
    function startUserInteraction() {
        isUserInteracting = true;
        
        // Clear any existing timer
        if (interactionTimer) {
            clearTimeout(interactionTimer);
            interactionTimer = null;
        }
    }

    /**
    * Mark that user interaction has potentially ended
    */
    function endUserInteraction() {
        // Clear any existing timer
        if (interactionTimer) {
            clearTimeout(interactionTimer);
        }
        
        // Set a timer to mark interaction as ended after 500ms
        interactionTimer = setTimeout(function() {
            isUserInteracting = false;
            interactionTimer = null;
            executePendingUpdate();
        }, 500);
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

    function getScrollInfoForSelect(select) {
        var scrollTop = 0;
        var wasOpen = false;
        
        // Check if dropdown is currently open and get scroll position
        // Different browsers handle this differently
        try {
            // For Chrome/Safari - check if the dropdown list is visible
            var selectElement = select[0];
            if (selectElement.size > 1 || (selectElement.multiple && selectElement.clientHeight > selectElement.scrollHeight)) {
                wasOpen = true;
                scrollTop = selectElement.scrollTop;
            }
            // Alternative method - check if dropdown has focus and was recently clicked
            else if (document.activeElement === selectElement) {
                // Store scroll position from the native select element
                scrollTop = selectElement.scrollTop;
                wasOpen = true;
            }
        } catch (e) {
            // Fallback - just capture scroll position if element has focus
            if (document.activeElement === select[0]) {
                scrollTop = select[0].scrollTop;
                wasOpen = true;
            }
        }
        return { scrollTop: scrollTop, wasOpen: wasOpen };
    }

    function restoreScrollPosition(select, scrollTop, wasOpen) {  
        // Restore scroll position if dropdown was open
        if (wasOpen && scrollTop > 0) {
            // Use setTimeout to ensure the DOM has updated
            setTimeout(function() {
                try {
                    select[0].scrollTop = scrollTop;
                    // Force focus back to maintain dropdown state
                    if (document.activeElement !== select[0]) {
                        select.focus();
                    }
                } catch (e) {
                    // Silently fail if scroll restoration doesn't work
                    console.log('Could not restore scroll position:', e);
                }
            }, 0);
        }
    }

    /**
    * Show the returned task lists in the dropdown box.
    * The original selection in the box is preserved, along with scroll position.
    * @param {Array.<Object>} taskLists The task lists to show.
    */
    function showTaskListsPreservingSelection(taskLists) {
        var select = $('#tasklist');
        var selectedValue = select.val();

        var { scrollTop, wasOpen } = getScrollInfoForSelect(select);

        showTaskLists(taskLists);

        // Restore selection
        if (selectedValue) {
            $('#tasklist').val(selectedValue);
        }

        restoreScrollPosition(select, scrollTop, wasOpen);
    }

    /**
    * A callback function that runs when the new task form is submitted.
    */
    function onNewTaskFormSubmit() {
        $("#message").html("<p>Please wait...</p>");

        var taskListId = $('#tasklist').val();
        var titleTextBox = $('#task-title');
        var noteTextBox = $('#task-note');
        var favStarCheck = $('#favstar');
        var isImportant = !!favStarCheck.prop("checked");
        var title = titleTextBox.val();
        var note = noteTextBox.val();
        var dateObj = $("#task-date").datepicker( "getDate" );
        var date = dateObj ? dateObj.getFullYear() + '-' + (dateObj.getMonth()+1) + '-' + dateObj.getDate() : "";

        var dataToSend = {
            postMethod: "addTask",
            taskListId: taskListId,
            title: title,
            note: note,
            isImportant: isImportant
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
                $("#message").html("<p>Task added.</p>");
                titleTextBox.val('');
                noteTextBox.val('');
            }
        });
        jqXHR.fail(function(data) {
            $("#message").html("<p>Unable to add task.</p>");
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

    /**
    * Apply theme-specific styling to jQuery UI datepicker
    */
    function updateDatepickerTheme() {
        const isDarkMode = document.documentElement.classList.contains('dark-mode');
        if (isDarkMode) {
            // Force redraw of datepicker with dark theme
            if (datePicker.datepicker('widget').is(':visible')) {
                datePicker.datepicker('hide');
                datePicker.datepicker('show');
            }
        }
    }

    // When the page loads.
    $(function() {
        $('#new-task').bind('submit', onNewTaskFormSubmit);

        datePicker = $("#task-date").datepicker({
            firstDay: 0,
            beforeShow: function(input, inst) {
                // Apply theme class to datepicker
                setTimeout(function() {
                    if (document.documentElement.classList.contains('dark-mode')) {
                        inst.dpDiv.addClass('dark-mode');
                    } else {
                        inst.dpDiv.removeClass('dark-mode');
                    }
                }, 0);
            }
        });

        // Listen for theme changes to update datepicker
        $('#theme-toggle').on('click', function() {
            setTimeout(updateDatepickerTheme, 10);
        });

        // Set up event listeners for dropdown interaction tracking
        var tasklistSelect = $('#tasklist');
        
        // Track mouse down events
        tasklistSelect.on('mousedown', function() {
            startUserInteraction();
        });
        
        // Track mouse up events
        tasklistSelect.on('mouseup', function() {
            endUserInteraction();
        });
        
        // Track scroll events
        tasklistSelect.on('scroll', function() {
            startUserInteraction();
            endUserInteraction(); // This will reset the timer
        });
        
        // Track focus events (keyboard navigation)
        tasklistSelect.on('focus', function() {
            startUserInteraction();
        });
        
        // Track blur events (when dropdown loses focus)
        tasklistSelect.on('blur', function() {
            endUserInteraction();
        });
        
        // Track keydown events for keyboard navigation
        tasklistSelect.on('keydown', function(e) {
            // Arrow keys, Page Up/Down, Home, End
            if (e.which >= 33 && e.which <= 40) {
                startUserInteraction();
                endUserInteraction(); // This will reset the timer
            }
        });

        loadTaskLists();
    });
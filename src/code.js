/**
 * MSAL Configuration
 */
const msalConfig = {
    auth: {
        clientId: "sample-123-234-1231-31231-231", // Remember to replace with your real Azure Client ID
        authority: "https://login.microsoftonline.com/common",
        redirectUri: window.location.origin + window.location.pathname
    },
    cache: {
        cacheLocation: "localStorage", // Stores tokens persistently across browser sessions
        storeAuthStateInCookie: false,
    }
};

const msalInstance = new msal.PublicClientApplication(msalConfig);
const loginRequest = {
    scopes: ["Tasks.ReadWrite", "User.Read"]
};

let datePicker;

// A variable to hold the active token request (Our "Lock" to prevent concurrency errors)
let activeTokenRequest = null;

/**
 * Authentication Management
 */
async function initializeAuth() {
    await msalInstance.initialize();
    
    // Check if user is already signed in
    const currentAccounts = msalInstance.getAllAccounts();
    if (currentAccounts.length > 0) {
        handleSignedInUser(currentAccounts[0]);
    }

    $('#login-btn').on('click', async () => {
        try {
            const response = await msalInstance.loginPopup(loginRequest);
            handleSignedInUser(response.account);
        } catch (error) {
            console.error("Login failed:", error);
            showError("Login failed. Please check console for details.");
        }
    });

    $('#logout-btn').on('click', () => {
        msalInstance.logoutPopup().then(() => {
            window.location.reload();
        });
    });
}

function handleSignedInUser(account) {
    msalInstance.setActiveAccount(account);
    $('#login-btn').hide();
    $('#logout-btn').show();
    $('#user-greeting').text(`Hello, ${account.name}`);
    
    // Explicitly set to flex instead of using .show() to preserve textarea flex-grow
    $('#app-content').css('display', 'flex'); 
    
    loadTaskLists();
}

/**
 * Gets a valid access token silently, or prompts if expired.
 * Includes concurrency locking to prevent 'interaction_in_progress' MSAL errors.
 */
async function getAccessToken() {
    // 1. If a token request is already running, wait for it instead of starting a new one
    if (activeTokenRequest) {
        return await activeTokenRequest;
    }

    // 2. Create the token request promise and store it in our lock variable
    activeTokenRequest = (async () => {
        try {
            // Try to get the token silently in the background
            const response = await msalInstance.acquireTokenSilent(loginRequest);
            return response.accessToken;
        } catch (error) {
            // If background fails, it means we need the user to interact
            if (error instanceof msal.InteractionRequiredAuthError) {
                try {
                    const response = await msalInstance.acquireTokenPopup(loginRequest);
                    return response.accessToken;
                } catch (popupError) {
                    // Catch the specific interaction error gracefully
                    if (popupError.errorCode === "interaction_in_progress") {
                        console.warn("An authentication popup is already open. Please complete the login.");
                    } else if (popupError.errorCode === "popup_window_error") {
                        // Browsers often block popups that aren't directly clicked by a user
                        $("#message").html("<p style='color:red;'>Popup blocked. Please click Login again.</p>");
                        $('#login-btn').show();
                        $('#logout-btn').hide();
                    }
                    throw popupError;
                }
            }
            throw error;
        } finally {
            // 3. Clear the lock when finished (whether it succeeded or failed)
            activeTokenRequest = null;
        }
    })();

    // Wait for the newly created request to resolve
    return await activeTokenRequest;
}

/**
 * Helper to execute calls against Microsoft Graph API directly
 * Now supports both relative endpoints and absolute URLs (for paging)
 */
async function callGraphApi(endpoint, method = 'GET', body = null) {
    const token = await getAccessToken();
    const headers = new Headers();
    headers.append("Authorization", `Bearer ${token}`);
    headers.append("Accept", "application/json");

    const options = { method, headers };
    if (body) {
        headers.append("Content-Type", "application/json");
        options.body = JSON.stringify(body);
    }

    // Handle full URLs provided by @odata.nextLink during paging
    const url = endpoint.startsWith('http') ? endpoint : `https://graph.microsoft.com/v1.0${endpoint}`;
    
    const response = await fetch(url, options);
    if (!response.ok) throw new Error(`Graph API error: ${response.status}`);
    
    // Return empty object for 204 No Content
    if (response.status === 204) return {};
    return await response.json();
}

/**
 * Removes emoji/symbols from the start of the title for sorting (Exact port from PHP logic)
 */
function removeEmojiForSort(title) {
    const regex = /^(?:[\u{0080}-\u{02AF}]|[\u{0300}-\u{03FF}]|[\u{0600}-\u{06FF}]|[\u{0C00}-\u{0C7F}]|[\u{1DC0}-\u{1DFF}]|[\u{1E00}-\u{1EFF}]|[\u{2000}-\u{209F}]|[\u{20D0}-\u{214F}]|[\u{2190}-\u{23FF}]|[\u{2460}-\u{25FF}]|[\u{2600}-\u{27EF}]|[\u{2900}-\u{29FF}]|[\u{2B00}-\u{2BFF}]|[\u{2C60}-\u{2C7F}]|[\u{2E00}-\u{2E7F}]|[\u{3000}-\u{303F}]|[\u{A490}-\u{A4CF}]|[\u{E000}-\u{F8FF}]|[\u{FE00}-\u{FE0F}]|[\u{FE30}-\u{FE4F}]|[\u{1F000}-\u{1F02F}]|[\u{1F0A0}-\u{1F0FF}]|[\u{1F100}-\u{1F64F}]|[\u{1F680}-\u{1F6FF}]|[\u{1F700}-\u{1F77F}]|[\u{1F780}-\u{1F7FF}]|[\u{1F800}-\u{1F8FF}]|[\u{1F900}-\u{1F9FF}]|[\u{1FA00}-\u{1FA6F}]|[\u{1FA70}-\u{1FAFF}]|[\u{20000}-\u{2FFFF}]|\s)+/u;
    return title.replace(regex, '');
}

/**
 * Sorts task list keeping the first entry pinned to top, matching PHP implementation
 */
function sortTaskListArray(resultArray) {
    if (resultArray.length === 0) return;
    
    // Keep the first entry
    const firstEntry = resultArray.shift();
    
    // Sort the remaining entries
    resultArray.sort((a, b) => {
        return removeEmojiForSort(a.title).localeCompare(removeEmojiForSort(b.title));
    });
    
    // Add the first entry back to the start
    resultArray.unshift(firstEntry);
}

/**
* Load the available task lists using localStorage caching and direct API calls.
* Fully supports API paging via @odata.nextLink.
*/
async function loadTaskLists() {
    const CACHE_KEY = 'taskListsCache';
    
    // Load from local cache immediately for snappy UI (Switched to localStorage)
    const cachedData = localStorage.getItem(CACHE_KEY);
    if (cachedData) {
        showTaskLists(JSON.parse(cachedData));
    }

    try {
        // Fetch fresh data directly from Graph API
        let endpoint = "/me/todo/lists/delta";
        let allLists = [];
        let keepGoing = true;

        while (keepGoing) {
            const data = await callGraphApi(endpoint);
            
            if (data && data.value) {
                const filteredLists = data.value
                    .filter(list => list.displayName !== 'Flagged Emails')
                    .map(list => ({ title: list.displayName, id: list.id }));
                
                allLists = allLists.concat(filteredLists);
            }

            // Paging logic matching PHP's approach
            if (data['@odata.nextLink']) {
                endpoint = data['@odata.nextLink']; // Feeds absolute URL back into callGraphApi
            } else {
                keepGoing = false;
            }
        }

        sortTaskListArray(allLists);
        // Save to persistent local storage
        localStorage.setItem(CACHE_KEY, JSON.stringify(allLists));

        const parsedCache = cachedData ? JSON.parse(cachedData) : [];
        if (JSON.stringify(parsedCache) !== JSON.stringify(allLists)) {
            showTaskListsPreservingSelection(allLists);
        }

    } catch (error) {
        $("#message").html("<p style='color:red;'>Failed to load lists. Please refresh.</p>");
        console.error(error);
    }
}

/**
* Show the returned task lists in the dropdown box.
* Automatically selects the first option by default.
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

    // Ensure first item is selected by default if we have items
    if (select.find('option').length > 0 && !select.val()) {
        select.prop('selectedIndex', 0);
    }
}

/**
* Show the returned task lists in the dropdown box.
* The original selection in the box is preserved, otherwise falls back to the first item.
* @param {Array.<Object>} taskLists The task lists to show.
*/
function showTaskListsPreservingSelection(taskLists) {
    var selectedValue = $('#tasklist').val();
    var selectedItemText = $('#tasklist option:selected').text();
    
    // Ensure we don't try to restore placeholder text
    var shouldRestoreSelection = selectedValue && selectedItemText && selectedItemText !== "Tasks" && selectedItemText !== "Loading...";

    showTaskLists(taskLists);

    if (shouldRestoreSelection) {
        $('#tasklist').val(selectedValue);
    } else {
        // Fallback to first item if we can't/shouldn't restore
        $('#tasklist').prop('selectedIndex', 0);
    }
}

/**
* A callback function that runs when the new task form is submitted.
*/
async function onNewTaskFormSubmit(e) {
    e.preventDefault(); // Prevent standard form submission
    $("#message").html("<p>Please wait...</p>");

    var taskListId = $('#tasklist').val();
    var titleTextBox = $('#task-title');
    var noteTextBox = $('#task-note');
    var favStarCheck = $('#favstar');
    var isImportant = !!favStarCheck.prop("checked");
    var title = titleTextBox.val();
    var note = noteTextBox.val();
    var dateObj = $("#task-date").datepicker("getDate");

    if (!title.trim()) {
        $("#message").html("<p style='color:red;'>Title is required.</p>");
        return false;
    }

    const payload = {
        title: title,
        body: { contentType: "text", content: note }
    };

    if (dateObj) {
        // Format: YYYY-MM-DD
        const formattedDate = `${dateObj.getFullYear()}-${String(dateObj.getMonth() + 1).padStart(2, '0')}-${String(dateObj.getDate()).padStart(2, '0')}`;
        payload.dueDateTime = { dateTime: `${formattedDate}T08:00:00.0000000`, timeZone: "UTC" };
    }

    if (isImportant) {
        payload.importance = "high";
    }

    try {
        await callGraphApi(`/me/todo/lists/${taskListId}/tasks`, 'POST', payload);
        $("#message").html("<p>Task added.</p>");
        titleTextBox.val('');
        noteTextBox.val('');
    } catch (error) {
        console.error(error);
        $("#message").html("<p style='color:red;'>Unable to add task.</p>");
    }

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
        if (datePicker && datePicker.datepicker('widget').is(':visible')) {
            datePicker.datepicker('hide');
            datePicker.datepicker('show');
        }
    }
}

/**
 * Handle URL parameters to pre-fill inputs (replaces PHP $_GET logic)
 */
function prefillFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('startingTitle')) {
        $('#task-title').val(urlParams.get('startingTitle'));
    }
    if (urlParams.has('startingNote')) {
        $('#task-note').val(urlParams.get('startingNote'));
    }
}

// When the page loads.
$(function() {
    prefillFromUrl();
    initializeAuth();

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
});

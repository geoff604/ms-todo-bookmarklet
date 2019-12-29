<?

$settings = array();

// Update the strings below for your app
$settings["client_id"] = "sample-123-234-1231-31231-231";
$settings["client_secret"] = "sample-123-234-1231-31231-231";

// Set this string to the https url of backend.php on your server. It should below
// the same as the redirect URL configured in Azure Active Directory console
$settings["redirect_uri"] = "https://mydomain.com/path/to/backend.php";

$settings["authority"] = "https://login.microsoftonline.com";

// Your App in Azure Active Directory console should have the permissions below.
$settings["scopes"] = array("offline_access", "openid");
array_push($settings["scopes"], "https://graph.microsoft.com/Tasks.ReadWrite");
array_push($settings["scopes"], "https://graph.microsoft.com/User.ReadBasic.All");
array_push($settings["scopes"], "https://graph.microsoft.com/User.Read");

$settings["auth_url"] = "/common/oauth2/v2.0/authorize";
$settings["auth_url"] .= "?client_id=".$settings["client_id"];
$settings["auth_url"] .= "&redirect_uri=".$settings["redirect_uri"];
$settings["auth_url"] .= "&response_type=code&scope=".implode(" ", $settings["scopes"]);
$settings["token_url"] = "/common/oauth2/v2.0/token";

// TODO: When Microsoft makes the API no longer beta, will need to provide the updated
// API URL here.
$settings["api_url"] = "https://graph.microsoft.com/beta";

function add_test_task() {
    global $settings;

    $headers = array(
        "User-Agent: php-tutorial/1.0",
        "Authorization: Bearer ".token()->access_token,
        "Accept: application/json",
        "Content-Type: application/json",
        "client-request-id: ".makeGuid(),
        "return-client-request-id: true"
    );
    $post_body = '{"subject": "Test Task 01",';
    $post_body .= '"body": {"contentType": "text","content": "Test body"},';
    $post_body .= '"dueDateTime": {"dateTime": "2019-12-27T08:00:00.0000000","timeZone": "UTC"}}';

    // TODO: The folder ID is hardcoded here, and will not work unless it is modified.
    $outlookApiUrl = $settings["api_url"] . "/me/outlook/taskFolders/AQMkADAwATM3ZmYAZS1jZjlhLTllMGEtMDACLTAwCgAuAAAD4QDr0T6dTEuSl2iiYjueDwEA48AWShDzTUqQN5ToEiORbwADDwnpmAAAAA==/tasks";

    $response = runCurl($outlookApiUrl, $post_body, $headers);
    echo "<pre>"; print_r($response); echo "</pre>";
}

function getHeadersForTaskLists() {
    $headers = array(
        "User-Agent: php-tutorial/1.0",
        "Authorization: Bearer ".token()->access_token,
        "Accept: application/json",
        "client-request-id: ".makeGuid(),
        "return-client-request-id: true"
    );
    return $headers;
}

function getTaskLists() {
    if (!token()) {
        http_response_code(500);
        echo "Not Logged In";
        die();
    }
    global $settings;
    $top = 50;
    $search = array (
        // Return at most n results
        "\$top" => $top
    );
    $outlookApiUrl = $settings["api_url"] . "/me/outlook/taskFolders?" . http_build_query($search);
    $response = runCurl($outlookApiUrl, null, getHeadersForTaskLists(), true);
    
    if ($response == null) {
        refresh_token(true);
        $response = runCurl($outlookApiUrl, null, getHeadersForTaskLists());
    }

    $response = explode("\n", trim($response));
    $response = $response[count($response) - 1];
    $response = json_decode($response, true);

    $resultArray = array();
    foreach ($response['value'] as $taskList) {
        $arrItem = array(
            'title' => $taskList['name'],
            'id' => $taskList['id']
        );
        array_push($resultArray, $arrItem);
    }

    $jsonTaskLists = json_encode($resultArray);

    header('Content-type: application/json');
    echo $jsonTaskLists;
    return;
}

function list_folders() {
    global $settings;
    $headers = array(
        "User-Agent: php-tutorial/1.0",
        "Authorization: Bearer ".token()->access_token,
        "Accept: application/json",
        "client-request-id: ".makeGuid(),
        "return-client-request-id: true"
    );
    $top = 50;
    $search = array (
        // Return at most n results
        "\$top" => $top
    );
    $outlookApiUrl = $settings["api_url"] . "/me/outlook/taskFolders?" . http_build_query($search);
    $response = runCurl($outlookApiUrl, null, $headers);
    $response = explode("\n", trim($response));
    $response = $response[count($response) - 1];
    $response = json_decode($response, true);
    echo "<pre>"; print_r($response); echo "</pre>";
}

function list_tasks_in_folder() {
    global $settings;
    $headers = array(
        "User-Agent: php-tutorial/1.0",
        "Authorization: Bearer ".token()->access_token,
        "Accept: application/json",
        "client-request-id: ".makeGuid(),
        "return-client-request-id: true"
    );
    $top = 50;
    $search = array (
        // Return at most n results
        "\$top" => $top
    );

    // TODO: The folder ID is hardcoded here, and will not work unless it is modified.
    $outlookApiUrl = $settings["api_url"] . "/me/outlook/taskFolders/AQMkADAwATM3ZmYAZS1jZjlhLTllMGEtMDACLTAwCgAuAAAD4QDr0T6dTEuSl2iiYjueDwEA48AWShDzTUqQN5ToEiORbwADDwnpmAAAAA==/tasks?" . http_build_query($search);

    $response = runCurl($outlookApiUrl, null, $headers);
    echo "<pre>"; print_r($response); echo "</pre>";
    $response = explode("\n", trim($response));
    $response = $response[count($response) - 1];
    $response = json_decode($response, true);
    echo "<pre>"; print_r($response); echo "</pre>";
}

function refresh_token($skipRedirect = false) {
    global $settings;
    
    $token = token();
    if ($token == null) {
        return;
    }
    $token_request_data = array (
        "grant_type" => "refresh_token",
        "refresh_token" => $token->refresh_token,
        "redirect_uri" => $settings["redirect_uri"],
        "scope" => implode(" ", $settings["scopes"]),
        "client_id" => $settings["client_id"],
        "client_secret" => $settings["client_secret"]
    );
    $body = http_build_query($token_request_data);
    $response = runCurl($settings["authority"].$settings["token_url"], $body);
    $response = json_decode($response);
    store_token($response);
    if (!$skipRedirect) {
        header("Location: " . $settings["redirect_uri"]);
    }
}

function view_profile($skipPrint = false) {
    global $settings;
    $headers = array(
        "User-Agent: php-tutorial/1.0",
        "Authorization: Bearer ".token()->access_token,
        "Accept: application/json",
        "client-request-id: ".makeGuid(),
        "return-client-request-id: true"
    );
    $outlookApiUrl = $settings["api_url"] . "/me";
    $response = runCurl($outlookApiUrl, null, $headers);
    $response = explode("\n", trim($response));
    $response = $response[count($response) - 1];
    $response = json_decode($response);
    if (!$skipPrint) {
        echo "<pre>"; print_r($response); echo "</pre>";
    }
}

function makeGuid(){
    global $settings;
    if (function_exists('com_create_guid')) {
        error_log("Using 'com_create_guid'.");
        return strtolower(trim(com_create_guid(), '{}'));
    }
    else {
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);
        $uuid = substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid, 12, 4).$hyphen
            .substr($charid, 16, 4).$hyphen
            .substr($charid, 20, 12);
        return $uuid;
    }
}

function flush_token() {
    file_put_contents("office_auth_config.txt", "");
}

function store_token($o) {
    file_put_contents("office_auth_config.txt", json_encode($o));
}

function token() {
    $text = file_exists("office_auth_config.txt") ? file_get_contents("office_auth_config.txt") : null;
    if($text != null && strlen($text) > 0) {
        return json_decode($text);
    }
    return null;
}

function runCurl($url, $post = null, $headers = null, $nullOnFailure = false) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, $post == null ? 0 : 1);
    if($post != null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    if($headers != null) {
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if($http_code >= 400) {
        if ($nullOnFailure) {
            return null;
        }
        http_response_code(500);
        echo "Error executing request to Office365 api with error code=$http_code<br/><br/>\n\n";
        echo "<pre>"; print_r($response); echo "</pre>";
        die();
    }
    return $response;
}

// taskDate is a string in form: 2010-10-15
function addTask($taskListId, $title, $note, $taskDate) {
    global $settings;

    $headers = array(
        "User-Agent: php-tutorial/1.0",
        "Authorization: Bearer ".token()->access_token,
        "Accept: application/json",
        "Content-Type: application/json",
        "client-request-id: ".makeGuid(),
        "return-client-request-id: true"
    );
    $post_body = '{"subject": ' . json_encode($title) .',';
    $post_body .= '"body": {"contentType": "text","content": '. json_encode($note) .'}';
    if ($taskDate != '') {
        $post_body .= ',"dueDateTime": {"dateTime": "' . $taskDate . 'T08:00:00.0000000","timeZone": "UTC"}';
    }
    $post_body .= '}';
    $outlookApiUrl = $settings["api_url"] . "/me/outlook/taskFolders/" . $taskListId ."/tasks";
    $response = runCurl($outlookApiUrl, $post_body, $headers);
    echo "<pre>"; print_r($response); echo "</pre>";
    return;
}

$token = token();
if($token && !isset($_GET['method']) && !isset($_GET["refresh_token"])) {
    echo "<a href='".$settings["redirect_uri"]."''>Home</a>";
    echo " || <a href='".$settings["redirect_uri"]."?refresh_token=true'>Refresh token</a>";
    echo " || <a href='".$settings["redirect_uri"]."?profile=true'>Profile</a>";
    echo " || <a href='".$settings["redirect_uri"]."?list_folders=true'>List Folders</a>";
    echo " || <a href='".$settings["redirect_uri"]."?list_tasks_in_folder=true'>List Tasks in Folder</a>";
    echo " || <a href='".$settings["redirect_uri"]."?add_test_task=true'>Add Test Task</a>";
    echo " || <a href='".$settings["redirect_uri"]."?logout=true'>Logout</a><br/><br/>\n\n";
}

if(isset($_GET["logout"])) {
    flush_token();
    echo "Logged out<br/>";
    echo "<a href='".$settings["redirect_uri"]."'>Start new session</a>";
    die();
}
else if(isset($_GET["profile"])) {
    view_profile();
}
else if(isset($_GET["refresh_token"])) {
    refresh_token();
}
else if(isset($_GET["list_folders"])) {
    list_folders();
}
else if(isset($_GET["list_tasks_in_folder"])) {
    list_tasks_in_folder();
}
else if(isset($_GET["add_test_task"])) {
    add_test_task();
}
else if(isset($_GET['method'])) {
    $method = $_GET['method'];
    if ($method == 'getTaskLists') {
        getTaskLists();
    } else if ($method == 'addTask') {
        addTask($_GET['taskListId'], $_GET['title'], $_GET['note'], $_GET['taskDate']);
    }
}
else if(isset($_GET["code"])) {
    $token_request_data = array (
        "grant_type" => "authorization_code",
        "code" => $_GET["code"],
        "redirect_uri" => $settings["redirect_uri"],
        "scope" => implode(" ", $settings["scopes"]),
        "client_id" => $settings["client_id"],
        "client_secret" => $settings["client_secret"]
    );
    $body = http_build_query($token_request_data);
    $response = runCurl($settings["authority"].$settings["token_url"], $body);
    $response = json_decode($response);

    store_token($response);
    header("Location: " . $settings["redirect_uri"]);
}
else if(token()) {
    echo "<pre>"; print_r(token()); echo "</pre>";
}
else {
    $accessUrl = $settings["authority"].$settings["auth_url"];
    echo "<a href='$accessUrl'>Login with Office 365</a>";
}

?>

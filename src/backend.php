<?

define('TASK_LISTS_CACHE_PATH', 'tasks-list-cache.json');

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

$settings["api_url"] = "https://graph.microsoft.com/v1.0";

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

function storeTaskListsInCache($jsonTaskLists) {
    file_put_contents(TASK_LISTS_CACHE_PATH, $jsonTaskLists);
}

function getCachedTaskLists() {
    header('Content-type: application/json');
    if (!is_readable(TASK_LISTS_CACHE_PATH)) {
        echo "[]";
        return;
    }
    $jsonTaskLists = file_get_contents(TASK_LISTS_CACHE_PATH);
    echo $jsonTaskLists;
    return;
}

// Function to remove emoji from the start of the title so that it can be sorted properly
function removeEmojiForSort($title) {
    return preg_replace(
        '/^(?:' .
        '[\x{0080}-\x{02AF}]|' .        // Latin-1 Supplement, IPA Extensions
        '[\x{0300}-\x{03FF}]|' .        // Combining Diacritical Marks, Greek
        '[\x{0600}-\x{06FF}]|' .        // Arabic
        '[\x{0C00}-\x{0C7F}]|' .        // Telugu
        '[\x{1DC0}-\x{1DFF}]|' .        // Combining Diacritical Marks Supplement
        '[\x{1E00}-\x{1EFF}]|' .        // Latin Extended Additional
        '[\x{2000}-\x{209F}]|' .        // General Punctuation
        '[\x{20D0}-\x{214F}]|' .        // Combining Diacritical Marks for Symbols, Letterlike Symbols
        '[\x{2190}-\x{23FF}]|' .        // Arrows, Mathematical Operators
        '[\x{2460}-\x{25FF}]|' .        // Enclosed Alphanumerics, Geometric Shapes
        '[\x{2600}-\x{27EF}]|' .        // Miscellaneous Symbols, Dingbats
        '[\x{2900}-\x{29FF}]|' .        // Supplemental Arrows-B
        '[\x{2B00}-\x{2BFF}]|' .        // Miscellaneous Symbols and Arrows
        '[\x{2C60}-\x{2C7F}]|' .        // Latin Extended-C
        '[\x{2E00}-\x{2E7F}]|' .        // Supplemental Punctuation
        '[\x{3000}-\x{303F}]|' .        // CJK Symbols and Punctuation
        '[\x{A490}-\x{A4CF}]|' .        // Yi Radicals
        '[\x{E000}-\x{F8FF}]|' .        // Private Use Area
        '[\x{FE00}-\x{FE0F}]|' .        // Variation Selectors
        '[\x{FE30}-\x{FE4F}]|' .        // CJK Compatibility Forms
        '[\x{1F000}-\x{1F02F}]|' .      // Mahjong Tiles
        '[\x{1F0A0}-\x{1F0FF}]|' .      // Playing Cards
        '[\x{1F100}-\x{1F64F}]|' .      // Enclosed Alphanumeric Supplement, Emoticons
        '[\x{1F680}-\x{1F6FF}]|' .      // Transport and Map Symbols
        '[\x{1F700}-\x{1F77F}]|' .      // Alchemical Symbols
        '[\x{1F780}-\x{1F7FF}]|' .      // Geometric Shapes Extended
        '[\x{1F800}-\x{1F8FF}]|' .      // Supplemental Arrows-C
        '[\x{1F900}-\x{1F9FF}]|' .      // Supplemental Symbols and Pictographs
        '[\x{1FA00}-\x{1FA6F}]|' .      // Chess Symbols, Symbols and Pictographs Extended-A
        '[\x{1FA70}-\x{1FAFF}]|' .      // Symbols and Pictographs Extended-B
        '[\x{20000}-\x{2FFFF}]|' .      // CJK Unified Ideographs Extension B-C
        '\s' .                          // Space
        ')+/u',
        '',
        $title
    );
}

// Sort the task list array with correct handling of lists with emoji's in the title
function sortTaskListArray(&$resultArray) {
    // Keep the first entry
    $firstEntry = array_shift($resultArray);

    // Sort the remaining entries
    usort($resultArray, function($a, $b) {
        return strcmp(removeEmojiForSort($a['title']), removeEmojiForSort($b['title']));
    });

    // Add the first entry back to the start
    array_unshift($resultArray, $firstEntry);
}

function getTaskLists() {
    if (!token()) {
        http_response_code(500);
        echo "Not Logged In";
        die();
    }
    global $settings;
    $nextLinkKey = "@odata.nextLink";
    $outlookApiUrl = $settings["api_url"] . "/me/todo/lists/delta";
    $keepGoing = true;
    $resultArray = array();

    while($keepGoing) {
        $response = runCurl($outlookApiUrl, null, getHeadersForTaskLists(), true);
        
        if ($response == null) {
            refresh_token(true);
            $response = runCurl($outlookApiUrl, null, getHeadersForTaskLists());
        }

        $response = explode("\n", trim($response));
        $response = $response[count($response) - 1];
        $response = json_decode($response, true);

        foreach ($response['value'] as $taskList) {
            if ($taskList['displayName'] == 'Flagged Emails') {
                continue;
            }
            $arrItem = array(
                'title' => $taskList['displayName'],
                'id' => $taskList['id']
            );
            array_push($resultArray, $arrItem);
        }
        if (array_key_exists($nextLinkKey, $response) && !empty($response[$nextLinkKey])) {
            $outlookApiUrl = $response[$nextLinkKey];
        } else {
            $keepGoing = false;
        }
    }

    sortTaskListArray($resultArray);

    $jsonTaskLists = json_encode($resultArray);
    storeTaskListsInCache($jsonTaskLists);

    header('Content-type: application/json');
    echo $jsonTaskLists;
    return;
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
function addTask($taskListId, $title, $note, $taskDate, $isImportant) {
    global $settings;

    $headers = array(
        "User-Agent: php-tutorial/1.0",
        "Authorization: Bearer ".token()->access_token,
        "Accept: application/json",
        "Content-Type: application/json",
        "client-request-id: ".makeGuid(),
        "return-client-request-id: true"
    );
    $post_body = '{"title": ' . json_encode($title) .',';
    $post_body .= '"body": {"contentType": "text","content": '. json_encode($note) .'}';
    if ($taskDate != '') {
        $post_body .= ',"dueDateTime": {"dateTime": "' . $taskDate . 'T08:00:00.0000000","timeZone": "UTC"}';
    }
	if ($isImportant == 'true') {
		$post_body .= ',"importance": "high"';
	}
    $post_body .= '}';
    $outlookApiUrl = $settings["api_url"] . "/me/todo/lists/" . $taskListId ."/tasks";
    $response = runCurl($outlookApiUrl, $post_body, $headers);
    echo "<pre>"; print_r($response); echo "</pre>";
    return;
}

$token = token();
if($token && !isset($_GET['method']) && !isset($_POST['postMethod']) && !isset($_GET["refresh_token"])) {
    echo "<a href='".$settings["redirect_uri"]."''>Home</a>";
    echo " || <a href='".$settings["redirect_uri"]."?refresh_token=true'>Refresh token</a>";
    echo " || <a href='".$settings["redirect_uri"]."?profile=true'>Profile</a>";
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
else if(isset($_GET['method'])) {
    $method = $_GET['method'];
    if ($method == 'getCachedTaskLists') {
        getCachedTaskLists();
    } else if ($method == 'getTaskLists') {
        getTaskLists();
    } else if ($method == 'addTask') {
        // Disabling GET for addTask as we now use POST
        http_response_code(400);
        echo "GET is no longer supported for addTask. Please use POST instead.";
        die();
    }
}
else if(isset($_POST['postMethod'])) {
    $postMethod = $_POST['postMethod'];
    if ($postMethod == 'addTask') {
		$note = isset($_POST['note']) ? $_POST['note'] : '';
		$taskDate = isset($_POST['taskDate']) ? $_POST['taskDate'] : '';
        addTask($_POST['taskListId'], $_POST['title'], $note, $taskDate, $_POST['isImportant']);
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

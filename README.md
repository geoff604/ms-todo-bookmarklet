# ms-todo-bookmarklet
A browser bookmarklet for Microsoft To Do https://todo.microsoft.com/ that allows adding tasks to your todo list from any website.

**Warning - Use at your own risk - script is not secure, unless you know what you're doing!**
- This script should **not** be simply uploaded to a web server with public access, as it will allow anyone to access 
your Office 365 account.
- For setting up this script securely, please see the note about Apache Basic authentication and HTTPS below.

## What is a Bookmarklet? 
From wikipedia:
> A bookmarklet is a bookmark stored in a web browser that contains JavaScript commands that add new features to the browser. Bookmarklets are unobtrusive JavaScripts stored as the URL of a bookmark in a web browser or as a hyperlink on a web page. Bookmarklets are usually JavaScript programs. Regardless of whether bookmarklet utilities are stored as bookmarks or hyperlinks, they add one-click functions to a browser or web page. When clicked, a bookmarklet performs one of a wide variety of operations, such as running a search query or extracting data from a table. For example, clicking on a bookmarklet after selecting text on a webpage could run an Internet search on the selected text and display a search engine results page.

See: https://en.wikipedia.org/wiki/Bookmarklet

# How it works:
- You'll need to set up the Microsoft To Do Bookmarklet in your browser bookmark toolbar, and configure the backend
PHP script on your server
- After it's set up, you can visit any web page, such as viewing a Gmail message, and then simply click the 
bookmarklet to open up a popup window for entering task details.
- In the popup window, the title of the webpage you are viewing will be populated into the title of the task, 
and the title and URL of the page will be populated into the notes section of the task.
- You are free to modify the title, notes, and set a date and choose your task list to add the task list.
- Click the Star icon next to the task Title to make the task marked Important when it is added.
- Then click the Add button and the task will be automatically added into your Microsoft To Do task list that you
selected.

# Set Up and Configuration
## Backend Support (Required)
The Microsoft Todo Bookmarklet works with the provided PHP script in this Github repo, that will take care of actually
adding the task to your todo list. You will need to host this PHP script yourself on your web server to make it work. 

If you are not able to do this, I could potentially host this for people on my server, but currently the script is
not sophisticated enough for a multi user scenario. Therefore, at the current time, to use this Bookmarklet requires
a working knowledge of how to set up and modify PHP scripts on a web server such as Apache.

PHP is a backend (server side) scripting language that is very common and still quite popular online.
For more details please see: https://www.php.net/

## Prerequisite:
- I'd only recommend attempting setting up this script if you have basic knowledge of PHP programming as well as
basic Apache web server config.

## Microsoft Account
You'll need a Microsoft account. You can create a free Microsoft account or you can use one provided by your
school or employer.

> How to create a new Microsoft account
> Go to account.microsoft.com, select Sign in, and then choose Create one!
> If you'd rather create a new email address, choose Get a new email address, choose Next, and then follow the instructions.
 
## Azure Active Directory Console
Check that you can log in to the Azure Active Directory console using your Microsoft account.
This is required to set up the API key and app permissions for this script.

The URL for Azure Active Directory Console is: http://aad.portal.azure.com

## Creating Your App in Azure Console
You'll need to create an App in Azure Console and give it the Microsoft Graph API permissions
stated in the Scopes section of backend.php, and set up a client secret and redirect URL.
Then, update backend.php with your app id and client secret, and redirect URL.

## Setting Up The Backend PHP Script
You'll need to modify backend.php to fill in the following placeholder settings:

    // Update the strings below for your app
    $settings["client_id"] = "sample-123-234-1231-31231-231";
    $settings["client_secret"] = "sample-123-234-1231-31231-231";

    // Set this string to the https url of backend.php on your server. It should below
    // the same as the redirect URL configured in Azure Active Directory console
    $settings["redirect_uri"] = "https://mydomain.com/path/to/backend.php";

### Using the Script Securely - Apache .htaccess Basic Authentication and HTTPS support
This script should **not** be simply uploaded to a web server with public access, as it will allow anyone to access 
your Office 365 account.

I recommend setting up a .htaccess file on your server so the directory is protected
by Basic Authentication (it should not be available publicly on your server as the script
is most likely NOT secure). 

Also, you should use HTTPS for accessing the script.

## Setting Up The Bookmarklet In Your Browser
You will need to add a bookmarklet to your browser bar, by opening the file
bookmarklet.txt, optionally modifying the script, and then adding a bookmark in your browser with
the contents of the file. Clicking on the bookmarklet will open the script in a popup window.

Note: you can replace my gmail address in the bookmarklet with your own, if you would like smarter
handling of window titles in Gmail when adding a task to Microsoft To Do from a Gmail message.
The current code will strip out my email address from the task title to make the title
more convenient to use in the Microsot Todo task list. By changing it to your gmail address
it will strip out your email address from the Gmail web page title when generating the initial
task title.

# Support
This script is evolving over time, and it may become something more sophisticated in the future.
Right now, it is intended for a highly technical audience and as a starting point for others' development.
However, if you have any questions, feel free to email me at geoff.peters@gmail.com

# License
This script is made available via the MIT License.

# My other To-Do and Google Tasks Bookmarklets
For similar Bookmarklets that work for Google Tasks instead, please check out:
- https://github.com/geoff604/google-tasks-bookmarklet-php (for a PHP version)
- https://github.com/geoff604/google-tasks-bookmarklet (for a Google Apps Script version)


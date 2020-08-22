# ms-todo-bookmarklet
A bookmarklet for Microsoft Todo that allows adding tasks to your todo list from any website.

It's a PHP script that you can host yourself on your web server to allowing adding of tasks
to your Microsoft Todo List from any website.

**Warning - Use at your own risk - script is not secure!**

Prerequisite:
- Knowledge of PHP programming and Apache web server config.
- A Microsoft account so you can access the Azure Active Directory console. http://aad.portal.azure.com

You'll need to create an App in Azure Console and give it the Microsoft Graph API permissions
stated in the Scopes section of backend.php, and set up a client secret and redirect URL.
Then, update backend.php with your app id and client secret,
and redirect URL.

I also recommend setting up a .htaccess file on your server so the directory is protected
by Basic Authentication (it should not be available publicly on your server as the script
is most likely NOT secure). Also, you should use HTTPS for accessing the script.

You will also need to add a bookmarklet to your browser bar, by opening the file
bookmarklet.txt, optionally modifying the script, and then adding a bookmark in your browser with
the contents of the file. Clicking on the bookmarklet will open the script in a popup window.

If you have any questions, feel free to email me at geoff.peters@gmail.com

p.s. For similar Bookmarklets that work for Google Tasks instead, please check out:
- https://github.com/geoff604/google-tasks-bookmarklet-php (for a PHP version)
- https://github.com/geoff604/google-tasks-bookmarklet (for a Google Apps Script version)

Best regards,
Geoff

# ms-todo-bookmarklet
A browser bookmarklet for Microsoft To Do https://todo.microsoft.com/ that allows adding tasks to your todo list from any website.

# Microsoft To-Do Web App

A lightweight, secure, Single Page Application (SPA) for adding tasks to Microsoft To Do directly from your browser. 

This app allows you to quickly add tasks, pre-fill task details via URL parameters (great for bookmarklets), and manage your lists. 
It is built using HTML, CSS, JavaScript, and the Microsoft Authentication Library (MSAL.js).

## 🚀 Key Features
* **Zero Backend Required:** 100% client-side authentication. No server-side secrets or databases to maintain.
* **Highly Secure:** Uses the modern OAuth 2.0 Authorization Code Flow with PKCE via MSAL.js. Tokens are stored securely in your browser.
* **Lightning Fast:** Caches your task lists in local browser storage so the app loads instantly on return visits.
* **Bookmarklet Ready:** Accepts `startingTitle` and `startingNote` URL parameters to easily add tasks from any web page.
* **Dark/Light Mode:** Includes a theme toggle that remembers your preference.

---

## 🛠️ How to Set Up Your Own Instance

Because this is a pure SPA, you do not need PHP or a complex web server. You just need to register the application with Microsoft to get a Client ID, and then host the HTML/JS files anywhere that serves static web pages.

### Step 1: Get a Free Microsoft Azure Account
To connect to the Microsoft Graph API, you need an App Registration in the Azure Portal.
1. Go to [portal.azure.com](https://portal.azure.com/).
2. Sign in with your standard Microsoft account (the same one you use for To-Do). 
3. *Note: You do not need a paid Azure subscription to register an app for personal use.*

### Step 2: Create an App Registration
1. In the Azure Portal search bar at the top, type **Microsoft Entra ID** (formerly Azure Active Directory) and select it.
2. In the left-hand menu, scroll down and click on **App registrations**.
3. Click the **+ New registration** button at the top.
4. **Name:** Give your app a name (e.g., "My To-Do SPA").
5. **Supported account types:** Select the 3rd option: *"Accounts in any organizational directory and personal Microsoft accounts (e.g. Skype, Xbox)"*.
6. **Redirect URI:** * In the dropdown, select **Single-page application (SPA)**.
   * Enter the exact URL where you will host this app (e.g., `https://your-domain.com/index.html`). 
   * *Tip for local testing: You can enter `http://localhost:8000/index.html` for now and add your live URL later.*
7. Click **Register**.

### Step 3: Get Your Client ID
1. Once registered, you will be taken to the app's **Overview** page.
2. Look for the **Application (client) ID**. It is a long string of numbers and letters.
3. **Copy this Client ID.** 4. Open the `code.js` file in your text editor.
5. Replace the placeholder `"sample-123-234-1231-31231-231"` inside the `msalConfig` object with your actual Client ID.

### Step 4: Configure API Permissions
1. On your App Registration page in Azure, click **API permissions** in the left menu.
2. Click **+ Add a permission**.
3. Select **Microsoft Graph**, then choose **Delegated permissions**.
4. Search for and check the following permissions:
   * `Tasks.ReadWrite` (Allows the app to read and add tasks)
   * `User.Read` (Allows the app to read your basic profile to say "Hello, [Name]")
5. Click **Add permissions** at the bottom.

---

## 🌐 Hosting the App

Because this app consists solely of static files (`index.html` and `code.js`), you can host it almost anywhere for free.

**Option A: GitHub Pages (Recommended & Free)**
1. Create a new repository on GitHub and upload the project files.
2. Go to the repository **Settings** -> **Pages**.
3. Under "Source", select the `main` branch and click Save.
4. In a few minutes, your app will be live. *Don't forget to add this new GitHub Pages URL to your Azure App Registration's Redirect URIs!*

**Option B: Local Testing**
You cannot simply double-click `index.html` to open it as a `file://` because modern browser security blocks API authentication from local files. You must serve it over a local web server.
* **If you have Python installed:** Open your terminal in the project folder and run `python -m http.server 8000`. Then visit `http://localhost:8000` in your browser.
* **If you use VS Code:** Install the "Live Server" extension and click "Go Live" at the bottom right of the screen.

---

## What is a Bookmarklet? 
From wikipedia:
> A bookmarklet is a bookmark stored in a web browser that contains JavaScript commands that add new features to the browser. Bookmarklets are unobtrusive JavaScripts stored as the URL of a bookmark in a web browser or as a hyperlink on a web page. Bookmarklets are usually JavaScript programs. Regardless of whether bookmarklet utilities are stored as bookmarks or hyperlinks, they add one-click functions to a browser or web page. When clicked, a bookmarklet performs one of a wide variety of operations, such as running a search query or extracting data from a table. For example, clicking on a bookmarklet after selecting text on a webpage could run an Internet search on the selected text and display a search engine results page.

See: https://en.wikipedia.org/wiki/Bookmarklet

## 🔖 Using the Bookmarklet

You can use a browser bookmarklet to highlight text on any webpage and instantly send it to your Tasks app.

Create a new bookmark in your browser, name it "Add Task", and paste the following code into the **URL** field. *(Make sure to replace `https://your-hosted-url.com/index.html` with your actual live app URL).*

```javascript
javascript:(function(){
    var title = document.title;
    var note = window.getSelection().toString() || window.location.href;
    var appUrl = "https://your-hosted-url.com/index.html";
    var fullUrl = appUrl + "?startingTitle=" + encodeURIComponent(title) + "&startingNote=" + encodeURIComponent(note);
    window.open(fullUrl, '_blank', 'width=500,height=600');
})();
```

## Setting Up The Bookmarklet In Your Browser
You will need to add a bookmarklet to your browser bar, by opening the file
bookmarklet-improved.txt, modifying the script as suggested below, and then adding a
bookmark in your browser with the contents of the file. Clicking on the bookmarklet will open
the script in a popup window.

### How to Modify the Bookmarklet Before Using It
You'll see in bookmarklet-improved.txt that there is an example URL such as:
https://gpeters.com/tasks/ms/index.php
You'll need to update that URL in the bookmarklet to point to the location of the index.html of this project
on your own server.

You will also see my email address contained in the bookmarklet. The purpose of this is to
remove my email address from the task title when adding a task from Gmail on the web.
You can replace my gmail address in the bookmarklet with your own, if you like.
The bookmarklet populates the initial task title with the current window's title.
When you are adding a task to Microsoft To Do from a Gmail message, the code will strip out
my email address from the task title to make the title more convenient to use in the
Microsoft Todo task list. By changing it to your gmail address it will strip out your
email address from the Gmail web page title when generating the initial task title.

# Support
This script is evolving over time, and it may become something more sophisticated in the future.
Right now, it is intended for a highly technical audience and as a starting point for others' development.
However, if you have any questions, feel free to email me at geoff.peters@gmail.com

# License
This script is made available via the MIT License.

This is with exception of the icons and css in the fontawesome folder which follow the license below:
* Font Awesome Free 6.2.0 by @fontawesome - https://fontawesome.com
* License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License)
* Copyright 2022 Fonticons, Inc.

# My other To-Do and Google Tasks Bookmarklets
For similar Bookmarklets that work for Google Tasks instead, please check out:
- https://github.com/geoff604/google-tasks-bookmarklet-php (for a PHP version)
- https://github.com/geoff604/google-tasks-bookmarklet (for a Google Apps Script version)


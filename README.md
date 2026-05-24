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
* **Smart Fuzzy Search:** Type any part of a task list name to instantly filter and navigate the dropdown — no need to open it first. Supports emoji-aware matching, keyboard navigation, and auto-scrolling to the best match.
* **Dark/Light Mode:** Includes a theme toggle that remembers your preference.

---

## 🔍 Smart Task List Search

After logging in, the task list dropdown supports a powerful type-to-search feature:

* **Just start typing** — the dropdown focuses automatically on load, so you can search immediately without clicking anything.
* **Fuzzy matching** — finds lists even with minor typos, using a tiered scoring system: exact match → prefix → word boundary → substring → fuzzy (Levenshtein distance).
* **Emoji-aware** — leading emojis in list names are ignored during matching, so typing `work` finds `💼 Work` as expected.
* **Keyboard navigation** — use `↑`/`↓` to move through results, `Enter` to select, `Escape` or `Backspace` to clear the search.
* **Auto-scroll** — the highlighted result is always kept in view within the dropdown.
* **Auto-reset** — the search buffer clears automatically after 2 minutes of inactivity, or immediately when the dropdown loses focus.

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
6. **Redirect URI:**
   * In the dropdown, select **Single-page application (SPA)**.
   * Enter the exact URL where you will host this app (e.g., `https://your-domain.com/index.html`).
   * *Tip for local testing: You can enter `http://localhost:8000/index.html` for now and add your live URL later.*
7. Click **Register**.

### Step 3: Get Your Client ID
1. Once registered, you will be taken to the app's **Overview** page.
2. Look for the **Application (client) ID**. It is a long string of numbers and letters.
3. **Copy this Client ID.**
4. Open the `code.js` file in your text editor.
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

## 🔖 What is a Bookmarklet?

A bookmarklet is a special bookmark that runs a small script instead of opening a URL. It looks like a normal bookmark in your browser's toolbar, but when you click it, it performs an action on whatever page you're currently viewing — like capturing the page title and sending it to your To-Do app.

**In plain terms:** you click the bookmarklet on any webpage, and a small popup opens with the task title and notes already filled in, ready for you to add to Microsoft To Do in one click.

See: https://en.wikipedia.org/wiki/Bookmarklet

---

## 🖱️ How to Use the Bookmarklet

### What it does

When you click the bookmarklet on any webpage:
1. It captures the **page title** and uses it as the task title.
2. It captures any **text you have selected** on the page as the task note. If you haven't selected any text, it uses the **page URL** instead.
3. It opens a small popup window with your To-Do app, already pre-filled with those details.
4. You review the title and note, choose your task list, and click **Add**.

### Basic Bookmarklet

This is the simple version. It works on any webpage and requires no customization beyond your app URL.

**Step 1 — Edit the code below.** Replace `https://your-hosted-url.com/index.html` with the actual URL where you have hosted the app (e.g. `https://myname.github.io/ms-todo-bookmarklet/index.html`):

```javascript
javascript:(function(){
    var title = document.title;
    var note = window.getSelection().toString() || window.location.href;
    var appUrl = "https://your-hosted-url.com/index.html";
    var fullUrl = appUrl + "?startingTitle=" + encodeURIComponent(title) + "&startingNote=" + encodeURIComponent(note);
    window.open(fullUrl, '_blank', 'width=500,height=600');
})();
```

**Step 2 — Create the bookmark.** In your browser:
* **Chrome / Edge:** Press `Ctrl+Shift+B` (Windows) or `Cmd+Shift+B` (Mac) to show the bookmarks bar. Right-click on the bookmarks bar and choose **Add page...** or **New bookmark**. 
* **Firefox:** Right-click the bookmarks toolbar and choose **New Bookmark**.

**Step 3 — Fill in the bookmark details:**
* **Name:** Give it a short label, e.g. `📋 Add Task`
* **URL:** Paste the full `javascript:(function(){...})();` code from Step 1 into the URL field. *(Yes, the entire block of code goes in the URL field — this is what makes it a bookmarklet.)*

**Step 4 — Click Save.** The bookmarklet now appears in your bookmarks bar. Navigate to any webpage, optionally select some text, then click it to add a task.

---

### Advanced Bookmarklet (Gmail-friendly)

The file `bookmarklet-improved.txt` in this repository contains a more capable version of the bookmarklet with one extra feature: **it automatically cleans up Gmail subject lines** used as task titles.

When you open an email in Gmail, the browser tab title looks something like:

```
Re: Project update - your.name@gmail.com - Gmail
```

The advanced bookmarklet strips out your email address and " - Gmail" from the title, leaving just:

```
Re: Project update
```

This makes the task title much cleaner without any manual editing.

#### How to set it up

**Step 1 — Open `bookmarklet-improved.txt`** from this repository in a text editor.

**Step 2 — Replace the placeholder URL.** Find the line containing:
```
https://your-hosted-url.com/index.html
```
Replace it with the actual URL of your hosted app (the same URL you used in the basic bookmarklet above).

**Step 3 — Replace the placeholder email address.** Find the line containing:
```
replace.with.your.email@gmail.com
```
Replace it with your own Gmail address. This is the address that will be stripped from Gmail tab titles. If you don't use Gmail, you can leave this as-is or remove the email-stripping line entirely — the bookmarklet will still work.

**Step 4 — Create the bookmark** using the same process as the basic bookmarklet above: right-click your bookmarks bar, choose **New Bookmark**, paste the entire modified contents of `bookmarklet-improved.txt` into the **URL** field, and give it a name like `📋 Add Task (Gmail)`.

#### Choosing between the two

| | Basic bookmarklet | Advanced bookmarklet |
|---|---|---|
| Works on any webpage | ✅ | ✅ |
| Captures page title & URL | ✅ | ✅ |
| Captures selected text as note | ✅ | ✅ |
| Cleans up Gmail subject lines | ❌ | ✅ |
| Requires editing an email address | ❌ | ✅ (optional) |

You can install both and use whichever is appropriate — the basic one for general browsing, and the advanced one when working in Gmail.

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

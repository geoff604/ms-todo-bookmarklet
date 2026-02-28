<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Tasks App</title>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.14.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="fontawesome/css/fontawesome.min.css">
<link rel="stylesheet" href="fontawesome/css/solid.min.css">

<script src="https://alcdn.msauth.net/browser/2.37.1/js/msal-browser.min.js"></script>

<style>
  :root {
    /* Light mode variables */
    --bg-primary: #f9f9f9;
    --bg-secondary: #fff;
    --text-primary: #333;
    --text-secondary: #555;
    --border-color: #ddd;
    --glow-color: rgb(77, 145, 254);
    --button-bg: #4285f4;
    --button-hover: #3367d6;
    --shadow-color: rgba(0,0,0,0.1);
    --input-shadow: rgba(0,0,0,0.05);
    --glow-shadow: rgba(77,144,254,0.2);
    --star-color: #C8C8C8;
    --star-checked: #FFC107;
    --star-shadow: #000;
	--star-focus-shadow-width: 1px;
    --scrollbar-thumb: rgba(66, 133, 244, 0.4);
    --scrollbar-track: rgba(77, 144, 254, 0.05);
  }

  :root.dark-mode {
    /* Dark mode variables */
    --bg-primary: #121212;
    --bg-secondary: #1e1e1e;
    --text-primary: #e0e0e0;
    --text-secondary: #b0b0b0;
    --border-color: #444;
    --glow-color: rgb(77, 145, 254);
    --button-bg: #4285f4;
    --button-hover: #5294ff;
    --shadow-color: rgba(0,0,0,0.3);
    --input-shadow: rgba(0,0,0,0.2);
    --glow-shadow: rgba(77,144,254,0.4);
    --star-color: #333;
    --star-checked: #FFC107;
    --star-shadow: #000;
	--star-focus-shadow-width: 2px;
    --scrollbar-thumb: rgba(66, 133, 244, 0.6);
    --scrollbar-track: rgba(77, 144, 254, 0.1);
  }

  body {
    width: 100%;
    font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    font-size: 14px;
    margin: 0;
    padding: 0;
    color: var(--text-primary);
    background-color: var(--bg-primary);
    transition: background-color 0.3s, color 0.3s;
  }

  #tasks-panel {
    margin-top: 8px;
  }

  #tasks {
    padding: 0;
    list-style-type: none;
  }

  #tasklist {
    margin-bottom: 5px;
    padding: 8px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    background-color: var(--bg-secondary);
    box-shadow: 0 1px 2px var(--shadow-color);
    font-size: 14px;
    width: auto;
    color: var(--text-primary);
  }

  #tasklist:focus {
    border-color: var(--glow-color);
    outline: none;
    box-shadow: 0 0 0 2px var(--glow-shadow);
  }

  #task-title {
    flex-grow: 1;
    min-width: 0; 
    padding: 8px 10px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-size: 14px;
    box-shadow: inset 0 1px 2px var(--input-shadow);
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    background-color: var(--bg-secondary);
    color: var(--text-primary);
  }

  #task-title:focus {
    border-color: var(--glow-color);
    outline: none;
    box-shadow: 0 0 0 2px var(--glow-shadow);
  }

  #task-note {
    flex-grow: 1;
    margin-top: 5px;
    resize: none;
    padding: 10px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-size: 14px;
    font-family: inherit;
    box-shadow: inset 0 1px 2px var(--input-shadow);
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    background-color: var(--bg-secondary);
    color: var(--text-primary);
  }

  #task-note:focus {
    border-color: var(--glow-color);
    outline: none;
    box-shadow: 0 0 0 2px var(--glow-shadow);
  }

  #outer {
    display: flex;
    flex-direction: column;
    width: 100%;
    height: 100%;
    padding: 12px;
    position: absolute;
    box-sizing: border-box;
    background-color: var(--bg-secondary);
    box-shadow: 0 2px 10px var(--shadow-color);
  }

  .fixed-container {
    flex-shrink: 0;
  }

  #form-container {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    margin-bottom: 10px;
  }

  #add-button {
    padding: 8px 16px;
    background-color: var(--button-bg);
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    font-size: 14px;
    transition: background-color 0.2s ease;
  }

  #add-button:hover {
    background-color: var(--button-hover);
  }

  input.star {
    border: 0;
    width: 1px;
    height: 1px;
    overflow: hidden;
    position: absolute !important;
    clip: rect(1px 1px 1px 1px);
    clip: rect(1px, 1px, 1px, 1px);
    opacity: 0;
  }

  label.star {
    color: var(--star-color);
  }

  label.star:before {
    font: var(--fa-font-solid);
    text-rendering: auto;
    -webkit-font-smoothing: antialiased;
    margin: 5px;
    content: "\f005";
    display: inline-block;
    font-size: 1.3em;
    text-shadow: 0px 0px 1px var(--star-shadow), 0px 0px 1px var(--star-shadow), 0px 0px 1px var(--star-shadow), 0px 0px 1px var(--star-shadow), 0px 0px 1px var(--star-shadow);
    color: white;  
    -webkit-user-select: none;
    -moz-user-select: none;
    user-select: none;
	transition: transform 0.1s ease-in-out;
    cursor: pointer;
  }

  label.star:active:before {
    transform: scale(0.85);
  }

  input.star:checked ~ label.star:before {
    color: var(--star-checked);
    text-shadow: none;
  }

  .dark-mode input.star:checked ~ label.star:before {
    background: 
      repeating-linear-gradient(45deg, rgba(0, 0, 0, 0.25) 0, rgba(0, 0, 0, 0.25) 1px, transparent 1px, transparent 5px),
      repeating-linear-gradient(-45deg, rgba(0, 0, 0, 0.25) 0, rgba(0, 0, 0, 0.25) 1px, transparent 1px, transparent 5px),
      var(--star-checked);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    color: transparent; 
    text-shadow: none;
  }

  .dark-mode input.star:focus-visible:checked ~ label.star:before {
    text-shadow: none;
    filter: drop-shadow(0px 0px var(--star-focus-shadow-width) var(--glow-color))
      drop-shadow(0px 0px var(--star-focus-shadow-width) var(--glow-color))
      drop-shadow(0px 0px var(--star-focus-shadow-width) var(--glow-color));
  }

  input.star:focus-visible ~ label.star:before {
    border-color: var(--glow-color);
    outline: none;
    text-shadow: 
      0px 0px var(--star-focus-shadow-width) var(--glow-color),
      0px 0px var(--star-focus-shadow-width) var(--glow-color),
      0px 0px var(--star-focus-shadow-width) var(--glow-color),
      0px 0px var(--star-focus-shadow-width) var(--glow-color),
      0px 0px var(--star-focus-shadow-width) var(--glow-color);
  }

  .inline-label-input {
    display: flex;
    width: 100%;
    margin-bottom: 8px;
    align-items: center;
  }

  label {
    font-weight: 500;
    color: var(--text-secondary);
    margin-right: 5px;
    white-space: nowrap; 
  }

  /* Added specific spacing for the date label when it shares a row */
  .date-label {
    margin-left: 15px;
  }

  #task-date {
    /* Prevent the date input from being squeezed by the growing title field */
    width: 110px;
    flex-shrink: 0;
    padding: 8px 10px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-size: 14px;
    box-shadow: inset 0 1px 2px var(--input-shadow);
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    background-color: var(--bg-secondary);
    color: var(--text-primary);
  }

  #task-date:focus {
    border-color: var(--glow-color);
    outline: none;
    box-shadow: 0 0 0 2px var(--glow-shadow);
  }

  select::-webkit-scrollbar {
    width: 40px;
  }

  select::-webkit-scrollbar-thumb {
    background-color: var(--scrollbar-thumb);
    border-radius: 6px;
  }

  select::-webkit-scrollbar-track {
    background-color: var(--scrollbar-track);
  }

  #theme-toggle {
    position: absolute;
    top: 12px;
    right: 12px;
    background: none;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    padding: 5px 10px;
    cursor: pointer;
    color: var(--text-primary);
    background-color: var(--bg-secondary);
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  }

  #theme-toggle:hover {
    background-color: var(--border-color);
  }

  #theme-toggle:focus {
    border-color: var(--glow-color);
    outline: none;
    box-shadow: 0 0 0 2px var(--glow-shadow);
  }

  .dark-mode .ui-widget-content {
    background: var(--bg-secondary);
    color: var(--text-primary);
    border-color: var(--border-color);
  }

  .dark-mode .ui-widget-header {
    background: var(--bg-primary);
    color: var(--text-primary);
    border-color: var(--border-color);
  }

  .dark-mode .ui-state-default {
    background: var(--bg-secondary);
    color: var(--text-primary);
    border-color: var(--border-color);
  }

  .dark-mode .ui-state-highlight {
    background: var(--button-bg);
    color: white;
  }

  .dark-mode .ui-state-active {
    background: var(--button-hover);
    color: white;
  }

  #auth-bar {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 100;
  }
  
  .auth-btn {
    padding: 5px 10px;
    background-color: var(--button-bg);
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
  }
  
  .auth-btn:hover { background-color: var(--button-hover); }
  
  #app-content { 
    display: none; 
    flex-direction: column;
    flex-grow: 1;
    height: 100%;
  }
</style>
</head>
<body>
<div id="outer">
  <div id="auth-bar">
    <button id="login-btn" class="auth-btn">Login with Microsoft</button>
    <button id="logout-btn" class="auth-btn" style="display: none;">Logout</button>
    <span id="user-greeting" style="margin-left: 10px; font-weight: bold;"></span>
  </div>

  <button id="theme-toggle">🌓 Toggle Theme</button>
  
  <div id="app-content">
    <div class="fixed-container" style="margin-top: 40px;">
      <div id="message"></div>
      
      <div class="inline-label-input">
        <label for="tasklist">Select a task list: </label>
        <select id="tasklist">
          <option>Loading...</option>
        </select>
      </div>
      
      <div class="inline-label-input">
        <input type="checkbox" class="star" name="favstar" id="favstar" value="1" />
        <label title="Mark as important" for="favstar" class="star"></label>
        
        <label for="task-title">Title:</label>
        <input type="text" maxlength="250" name="task-title" id="task-title" autofocus />
        
        <label for="task-date" class="date-label">Date:</label>
        <input type="text" name="task-date" id="task-date" autocomplete="off" />
      </div>
      
    </div>
    <div id="form-container">
      <textarea name="task-note" id="task-note"></textarea>
    </div>
    <div class="fixed-container">
      <form name="new-task" id="new-task"> 
        <input type="submit" name="add" id="add-button" value="Add" />
      </form>
    </div>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.14.1/jquery-ui.min.js"></script>

<script src="code.js"></script>

<script>
  // Theme management
  function setTheme(theme) {
    if (theme === 'dark') {
      document.documentElement.classList.add('dark-mode');
      localStorage.setItem('theme', 'dark');
    } else {
      document.documentElement.classList.remove('dark-mode');
      localStorage.setItem('theme', 'light');
    }
  }

  // Initialize theme
  function initTheme() {
    const savedTheme = localStorage.getItem('theme');
    
    // Check for saved preference, default to light mode if none
    if (savedTheme === 'dark') {
      setTheme('dark');
    } else {
      setTheme('light');
    }
  }

  // Theme toggle event listener
  document.getElementById('theme-toggle').addEventListener('click', () => {
    const currentTheme = document.documentElement.classList.contains('dark-mode') ? 'dark' : 'light';
    setTheme(currentTheme === 'dark' ? 'light' : 'dark');
  });

  // Initialize theme on page load
  initTheme();
</script>
</body>
</html>

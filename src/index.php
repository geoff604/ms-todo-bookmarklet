<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.14.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="fontawesome/css/fontawesome.min.css">
<link rel="stylesheet" href="fontawesome/css/solid.min.css">

<!-- Custom styles. -->
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
    width: 450px;
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
    font-size: 1.2em;
    text-shadow: 0px 0px 1px var(--star-shadow), 0px 0px 1px var(--star-shadow), 0px 0px 1px var(--star-shadow), 0px 0px 1px var(--star-shadow), 0px 0px 1px var(--star-shadow);
    color: white;  
    -webkit-user-select: none;
    -moz-user-select: none;
    user-select: none;
  }

  input.star:checked ~ label.star:before {
    color: var(--star-checked);
    text-shadow: none;
  }

  input.star:focus ~ label.star:before {
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
    display: inline-block;
    margin-right: 10px;
    margin-bottom: 8px;
    align-items: center;
  }

  label {
    font-weight: 500;
    color: var(--text-secondary);
    margin-right: 5px;
  }

  #task-date {
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
    width: 40px; /* Adjust the width */
  }

  select::-webkit-scrollbar-thumb {
    background-color: var(--scrollbar-thumb); /* Color of the scrollbar thumb */
    border-radius: 6px;
  }

  select::-webkit-scrollbar-track {
    background-color: var(--scrollbar-track); /* Track background color */
  }

  /* Theme toggle button */
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
    transition: all 0.2s ease;
  }

  #theme-toggle:hover {
    background-color: var(--border-color);
  }

  /* jQuery UI datepicker overrides for dark mode */
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
</style>
</head>
<body>
<div id="outer">
  <button id="theme-toggle">🌓 Toggle Theme</button>
  <div class="fixed-container">
    <div id="message"></div>
    <div class="inline-label-input">
      <label for="tasklist">Select a task list: </label>
      <select id="tasklist">
        <option>Loading...</option>
      </select>
    </div>
    <br/>
    <div class="inline-label-input">
      <input type="checkbox" class="star" name="favstar" id="favstar" value="1" />
      <label title="Mark as important" for="favstar" class="star"></label>
      <label for="task-title">Title:</label>
      <input type="text" maxlength="250" name="task-title" id="task-title" autofocus value="<? print(isSet($_GET['startingTitle']) ? htmlEntities($_GET['startingTitle'], ENT_QUOTES) : ''); ?>"/>
    </div>
    <div class="inline-label-input">
      <label for="task-date">Date:</label>
      <input type="text" name="task-date" id="task-date" />
    </div>
  </div>
  <div id="form-container">
    <textarea name="task-note" id="task-note"><? print(isSet($_GET['startingNote']) ? htmlEntities($_GET['startingNote'], ENT_QUOTES) : ''); ?></textarea>
  </div>
  <div class="fixed-container">
    <form name="new-task" id="new-task"> 
      <input type="submit" name="add" id="add-button" value="Add" />
    </form>
  </div>
</div>

<!-- Load the jQuery and jQuery UI libraries. -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.14.1/jquery-ui.min.js"></script>

<!-- Custom client-side JavaScript code. -->
<script src="code.js"></script>
<!-- Theme management JavaScript -->
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

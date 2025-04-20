<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.14.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="fontawesome/css/fontawesome.min.css">
<link rel="stylesheet" href="fontawesome/css/solid.min.css">

<!-- Custom styles. -->
<style>
  body {
    width: 100%;
    font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    font-size: 14px;
    margin: 0;
    padding: 0;
    color: #333;
    background-color: #f9f9f9;
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
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #fff;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    font-size: 14px;
    width: auto;
  }

  #tasklist:focus {
    border-color: #4d90fe;
    outline: none;
    box-shadow: 0 0 0 2px rgba(77,144,254,0.2);
  }

  #task-title {
    width: 450px;
    padding: 8px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  }

  #task-title:focus {
    border-color: #4d90fe;
    outline: none;
    box-shadow: 0 0 0 2px rgba(77,144,254,0.2);
  }

  #task-note {
    flex-grow: 1;
    margin-top: 5px;
    resize: none;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    font-family: inherit;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  }

  #task-note:focus {
    border-color: #4d90fe;
    outline: none;
    box-shadow: 0 0 0 2px rgba(77,144,254,0.2);
  }

  #outer {
    display: flex;
    flex-direction: column;
    width: 100%;
    height: 100%;
    padding: 12px;
    position: absolute;
    box-sizing: border-box;
    background-color: #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
    background-color: #4285f4;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    font-size: 14px;
    transition: background-color 0.2s ease;
  }

  #add-button:hover {
    background-color: #3367d6;
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
    color: #C8C8C8;
  }

  label.star:before {
    font: var(--fa-font-solid);
    text-rendering: auto;
    -webkit-font-smoothing: antialiased;
    margin: 5px;
    content: "\f005";
    display: inline-block;
    font-size: 1.2em;
    text-shadow: 0px 0px 1px #000, 0px 0px 1px #000, 0px 0px 1px #000, 0px 0px 1px #000, 0px 0px 1px #000;
    color: white;  
    -webkit-user-select: none;
    -moz-user-select: none;
    user-select: none;
  }

  input.star:checked ~ label.star:before {
    color: #FFC107;
    text-shadow: none;
  }

  input.star:focus ~ label.star:before {
    border-color: #4d90fe;
    outline: none;
    box-shadow: 0 0 0 2px rgba(77,144,254,0.2);
  }

  .inline-label-input {
    display: inline-block;
    margin-right: 10px;
    margin-bottom: 8px;
    align-items: center;
  }

  label {
    font-weight: 500;
    color: #555;
    margin-right: 5px;
  }

  #task-date {
    padding: 8px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  }

  #task-date:focus {
    border-color: #4d90fe;
    outline: none;
    box-shadow: 0 0 0 2px rgba(77,144,254,0.2);
  }

</style>
</head>
<body>
<div id="outer">
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
</body>
</html>

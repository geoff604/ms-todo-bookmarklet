<!DOCTYPE html>
<html>
<head>
<!-- Load the jQuery UI styles. -->
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">

<!-- Custom styles. -->
<style>
  body {
    width: 100%;
    font-family: Arial, sans-serif;
    font-size: 13px;
    margin: 0px;
    padding: 0px;
  }
  #tasks-panel {
    margin-top: 10px;
  }
  #tasks {
    padding: 0;
    list-style-type: none;
  }
  #tasklist {
     margin-bottom:5px;
  }
  #task-title {
    width: 450px;
  }
  #task-note {
    width: calc(100% - 20px);
    height: 150px;
    margin-top:5px;
  }
  #outer {
    width: 100%;
    padding: 10px;
    position: relative;
    box-sizing: border-box; 
  }
</style>

<!-- Load the jQuery and jQuery UI libraries. -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<!-- Custom client-side JavaScript code. -->
<script src="code.js"></script>

</head>
<body>
<div id="outer">
<label for="tasklist">Select a task list: </label>
<select id="tasklist">
  <option>Loading...</option>
</select>
<br/>
    <label for="task-title">Title:</label>
    <input type="text" name="task-title" id="task-title" autofocus value="<? print(htmlEntities($_GET['startingTitle'], ENT_QUOTES)); ?>"/>
    <label for="task-date">Date:</label>
    <input type="text" name="task-date" id="task-date" />
    <textarea name="task-note" id="task-note"><? print(htmlEntities($_GET['startingNote'], ENT_QUOTES)); ?></textarea>
    <form name="new-task" id="new-task"> 
    <input type="submit" name="add" id="add-button" value="Add" />
  </form>
</div>
</body>
</html>

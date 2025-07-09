import React, { useState, useEffect, useRef } from 'react';
import { Star, Moon, Sun } from 'lucide-react';

const TodoTaskEntry = () => {
  const [taskLists, setTaskLists] = useState([]);
  const [selectedTaskList, setSelectedTaskList] = useState('');
  const [title, setTitle] = useState('');
  const [note, setNote] = useState('');
  const [date, setDate] = useState('');
  const [isImportant, setIsImportant] = useState(false);
  const [message, setMessage] = useState('');
  const [isDarkMode, setIsDarkMode] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const titleInputRef = useRef(null);

  // Initialize theme and focus
  useEffect(() => {
    // Initialize theme from localStorage or default to light
    const savedTheme = localStorage?.getItem('theme') || 'light';
    setIsDarkMode(savedTheme === 'dark');
    
    // Focus on title input
    if (titleInputRef.current) {
      titleInputRef.current.focus();
    }

    // Load task lists on component mount
    loadTaskLists();
  }, []);

  // Apply theme to document
  useEffect(() => {
    if (isDarkMode) {
      document.documentElement.classList.add('dark-mode');
    } else {
      document.documentElement.classList.remove('dark-mode');
    }
  }, [isDarkMode]);

  // Load task lists function
  const loadTaskLists = async () => {
    try {
      // First try to get cached data
      const cachedResponse = await fetch('backend.php?method=getCachedTaskLists');
      if (cachedResponse.ok) {
        const cachedData = await cachedResponse.json();
        setTaskLists(cachedData);
        
        // Then try to get fresh data
        const freshResponse = await fetch('backend.php?method=getTaskLists');
        if (freshResponse.ok) {
          const freshData = await freshResponse.json();
          if (JSON.stringify(cachedData) !== JSON.stringify(freshData)) {
            // Preserve selection when updating
            const currentSelection = selectedTaskList;
            setTaskLists(freshData);
            if (currentSelection) {
              setSelectedTaskList(currentSelection);
            }
          }
        }
      }
    } catch (error) {
      console.error('Failed to load task lists:', error);
      setMessage('Please login and try again.');
    }
  };

  // Handle form submission
  const handleSubmit = async () => {
    setMessage('Please wait...');
    setIsLoading(true);

    const dataToSend = {
      postMethod: 'addTask',
      taskListId: selectedTaskList,
      title: title,
      note: note,
      isImportant: isImportant
    };

    if (date) {
      dataToSend.taskDate = date;
    }

    try {
      const response = await fetch('backend.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(dataToSend)
      });

      if (response.ok) {
        setMessage('Task added.');
        setTitle('');
        setNote('');
        setDate('');
        setIsImportant(false);
        if (titleInputRef.current) {
          titleInputRef.current.focus();
        }
      } else {
        throw new Error('Failed to add task');
      }
    } catch (error) {
      console.error('Error adding task:', error);
      setMessage('Unable to add task.');
    } finally {
      setIsLoading(false);
    }
  };

  // Toggle theme
  const toggleTheme = () => {
    const newTheme = !isDarkMode;
    setIsDarkMode(newTheme);
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('theme', newTheme ? 'dark' : 'light');
    }
  };

  // Format date for display
  const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString();
  };

  return (
    <div className="todo-container">
      <style jsx>{`
        .todo-container {
          --bg-primary: ${isDarkMode ? '#121212' : '#f9f9f9'};
          --bg-secondary: ${isDarkMode ? '#1e1e1e' : '#fff'};
          --text-primary: ${isDarkMode ? '#e0e0e0' : '#333'};
          --text-secondary: ${isDarkMode ? '#b0b0b0' : '#555'};
          --border-color: ${isDarkMode ? '#444' : '#ddd'};
          --glow-color: rgb(77, 145, 254);
          --button-bg: #4285f4;
          --button-hover: ${isDarkMode ? '#5294ff' : '#3367d6'};
          --shadow-color: ${isDarkMode ? 'rgba(0,0,0,0.3)' : 'rgba(0,0,0,0.1)'};
          --input-shadow: ${isDarkMode ? 'rgba(0,0,0,0.2)' : 'rgba(0,0,0,0.05)'};
          --glow-shadow: ${isDarkMode ? 'rgba(77,144,254,0.4)' : 'rgba(77,144,254,0.2)'};
          --star-color: ${isDarkMode ? '#333' : '#C8C8C8'};
          --star-checked: #FFC107;
          
          width: 100%;
          height: 100vh;
          font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
          font-size: 14px;
          margin: 0;
          padding: 0;
          color: var(--text-primary);
          background-color: var(--bg-primary);
          transition: background-color 0.3s, color 0.3s;
          position: relative;
        }

        .outer {
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

        .theme-toggle {
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
          display: flex;
          align-items: center;
          gap: 5px;
        }

        .theme-toggle:hover {
          background-color: var(--border-color);
        }

        .theme-toggle:focus {
          border-color: var(--glow-color);
          outline: none;
          box-shadow: 0 0 0 2px var(--glow-shadow);
        }

        .fixed-container {
          flex-shrink: 0;
        }

        .message {
          margin-bottom: 10px;
          min-height: 20px;
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

        .tasklist-select {
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

        .tasklist-select:focus {
          border-color: var(--glow-color);
          outline: none;
          box-shadow: 0 0 0 2px var(--glow-shadow);
        }

        .task-title {
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

        .task-title:focus {
          border-color: var(--glow-color);
          outline: none;
          box-shadow: 0 0 0 2px var(--glow-shadow);
        }

        .task-date {
          padding: 8px 10px;
          border: 1px solid var(--border-color);
          border-radius: 4px;
          font-size: 14px;
          box-shadow: inset 0 1px 2px var(--input-shadow);
          transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
          background-color: var(--bg-secondary);
          color: var(--text-primary);
        }

        .task-date:focus {
          border-color: var(--glow-color);
          outline: none;
          box-shadow: 0 0 0 2px var(--glow-shadow);
        }

        .form-container {
          display: flex;
          flex-direction: column;
          flex-grow: 1;
          margin-bottom: 10px;
        }

        .task-note {
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
          min-height: 100px;
        }

        .task-note:focus {
          border-color: var(--glow-color);
          outline: none;
          box-shadow: 0 0 0 2px var(--glow-shadow);
        }

        .star-container {
          display: flex;
          align-items: center;
          margin-right: 10px;
        }

        .star-input {
          position: absolute;
          opacity: 0;
          width: 0;
          height: 0;
        }

        .star-label {
          cursor: pointer;
          color: var(--star-color);
          transition: color 0.2s;
          margin-right: 5px;
        }

        .star-label:hover {
          color: var(--star-checked);
        }

        .star-input:checked + .star-label {
          color: var(--star-checked);
        }

        .star-input:focus + .star-label {
          outline: 2px solid var(--glow-color);
          outline-offset: 2px;
        }

        .add-button {
          padding: 8px 16px;
          background-color: var(--button-bg);
          color: white;
          border: none;
          border-radius: 4px;
          cursor: pointer;
          font-weight: 500;
          font-size: 14px;
          transition: background-color 0.2s ease;
          disabled: ${isLoading};
        }

        .add-button:hover:not(:disabled) {
          background-color: var(--button-hover);
        }

        .add-button:disabled {
          opacity: 0.6;
          cursor: not-allowed;
        }

        .title-row {
          display: flex;
          align-items: center;
          margin-bottom: 8px;
        }
      `}</style>

      <div className="outer">
        <button className="theme-toggle" onClick={toggleTheme}>
          {isDarkMode ? <Sun size={16} /> : <Moon size={16} />}
          Toggle Theme
        </button>

        <div className="fixed-container">
          <div className="message">
            {message && <p>{message}</p>}
          </div>

          <div className="inline-label-input">
            <label htmlFor="tasklist">Select a task list: </label>
            <select 
              id="tasklist"
              className="tasklist-select"
              value={selectedTaskList}
              onChange={(e) => setSelectedTaskList(e.target.value)}
            >
              {taskLists.length === 0 ? (
                <option>Loading...</option>
              ) : (
                taskLists.map((taskList) => (
                  <option key={taskList.id} value={taskList.id}>
                    {taskList.title}
                  </option>
                ))
              )}
            </select>
          </div>

          <br />

          <div className="title-row">
            <div className="star-container">
              <input
                type="checkbox"
                className="star-input"
                id="favstar"
                checked={isImportant}
                onChange={(e) => setIsImportant(e.target.checked)}
              />
              <label htmlFor="favstar" className="star-label" title="Mark as important">
                <Star size={20} fill={isImportant ? '#FFC107' : 'currentColor'} />
              </label>
            </div>
            <label htmlFor="task-title">Title:</label>
            <input
              ref={titleInputRef}
              type="text"
              id="task-title"
              className="task-title"
              maxLength="250"
              value={title}
              onChange={(e) => setTitle(e.target.value)}
            />
          </div>

          <div className="inline-label-input">
            <label htmlFor="task-date">Date:</label>
            <input
              type="date"
              id="task-date"
              className="task-date"
              value={date}
              onChange={(e) => setDate(e.target.value)}
            />
          </div>
        </div>

        <div className="form-container">
          <textarea
            className="task-note"
            placeholder="Add a note..."
            value={note}
            onChange={(e) => setNote(e.target.value)}
          />
        </div>

        <div className="fixed-container">
          <button 
            onClick={handleSubmit}
            className="add-button" 
            disabled={isLoading || !selectedTaskList || !title.trim()}
          >
            {isLoading ? 'Adding...' : 'Add'}
          </button>
        </div>
      </div>
    </div>
  );
};

export default TodoTaskEntry;
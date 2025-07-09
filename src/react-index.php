<?php
// react-todo.php - PHP file to load React TSX component

// Get starting values from URL parameters if provided
$startingTitle = isset($_GET['startingTitle']) ? htmlspecialchars($_GET['startingTitle'], ENT_QUOTES) : '';
$startingNote = isset($_GET['startingNote']) ? htmlspecialchars($_GET['startingNote'], ENT_QUOTES) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo Task Entry</title>
    
    <!-- React and ReactDOM from CDN -->
    <script crossorigin src="https://unpkg.com/react@18/umd/react.development.js"></script>
    <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
    
    <!-- Babel for JSX transpilation -->
    <script crossorigin src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
    
    <!-- Lucide React Icons -->
    <script crossorigin src="https://unpkg.com/lucide-react@latest/dist/umd/lucide-react.js"></script>
    
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen',
                'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue',
                sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        #root {
            height: 100vh;
            width: 100vw;
        }
        
        /* Loading spinner */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-size: 18px;
            color: #666;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
            margin-right: 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div id="root">
        <div class="loading">
            <div class="spinner"></div>
            Loading Todo App...
        </div>
    </div>

    <script type="text/babel">
        const { useState, useEffect, useRef } = React;
        //const { Star, Moon, Sun } = lucideReact;

        const TodoTaskEntry = () => {
            const [taskLists, setTaskLists] = useState([]);
            const [selectedTaskList, setSelectedTaskList] = useState('');
            const [title, setTitle] = useState(<?php echo json_encode($startingTitle); ?>);
            const [note, setNote] = useState(<?php echo json_encode($startingNote); ?>);
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

            // Handle Enter key in title input
            const handleTitleKeyDown = (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    handleSubmit();
                }
            };

            const containerStyle = {
                '--bg-primary': isDarkMode ? '#121212' : '#f9f9f9',
                '--bg-secondary': isDarkMode ? '#1e1e1e' : '#fff',
                '--text-primary': isDarkMode ? '#e0e0e0' : '#333',
                '--text-secondary': isDarkMode ? '#b0b0b0' : '#555',
                '--border-color': isDarkMode ? '#444' : '#ddd',
                '--glow-color': 'rgb(77, 145, 254)',
                '--button-bg': '#4285f4',
                '--button-hover': isDarkMode ? '#5294ff' : '#3367d6',
                '--shadow-color': isDarkMode ? 'rgba(0,0,0,0.3)' : 'rgba(0,0,0,0.1)',
                '--input-shadow': isDarkMode ? 'rgba(0,0,0,0.2)' : 'rgba(0,0,0,0.05)',
                '--glow-shadow': isDarkMode ? 'rgba(77,144,254,0.4)' : 'rgba(77,144,254,0.2)',
                '--star-color': isDarkMode ? '#333' : '#C8C8C8',
                '--star-checked': '#FFC107',
                
                width: '100%',
                height: '100vh',
                fontFamily: '\'Segoe UI\', Roboto, \'Helvetica Neue\', Arial, sans-serif',
                fontSize: '14px',
                margin: 0,
                padding: 0,
                color: 'var(--text-primary)',
                backgroundColor: 'var(--bg-primary)',
                transition: 'background-color 0.3s, color 0.3s',
                position: 'relative'
            };

            const outerStyle = {
                display: 'flex',
                flexDirection: 'column',
                width: '100%',
                height: '100%',
                padding: '12px',
                position: 'absolute',
                boxSizing: 'border-box',
                backgroundColor: 'var(--bg-secondary)',
                boxShadow: '0 2px 10px var(--shadow-color)'
            };

            const themeToggleStyle = {
                position: 'absolute',
                top: '12px',
                right: '12px',
                background: 'none',
                border: '1px solid var(--border-color)',
                borderRadius: '4px',
                padding: '5px 10px',
                cursor: 'pointer',
                color: 'var(--text-primary)',
                backgroundColor: 'var(--bg-secondary)',
                transition: 'border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out',
                display: 'flex',
                alignItems: 'center',
                gap: '5px'
            };

            const selectStyle = {
                marginBottom: '5px',
                padding: '8px',
                border: '1px solid var(--border-color)',
                borderRadius: '4px',
                backgroundColor: 'var(--bg-secondary)',
                boxShadow: '0 1px 2px var(--shadow-color)',
                fontSize: '14px',
                width: 'auto',
                color: 'var(--text-primary)'
            };

            const inputStyle = {
                padding: '8px 10px',
                border: '1px solid var(--border-color)',
                borderRadius: '4px',
                fontSize: '14px',
                boxShadow: 'inset 0 1px 2px var(--input-shadow)',
                transition: 'border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out',
                backgroundColor: 'var(--bg-secondary)',
                color: 'var(--text-primary)'
            };

            const titleInputStyle = {
                ...inputStyle,
                width: '450px',
                maxWidth: 'calc(100vw - 200px)'
            };

            const textareaStyle = {
                ...inputStyle,
                flexGrow: 1,
                marginTop: '5px',
                resize: 'none',
                padding: '10px',
                fontFamily: 'inherit',
                minHeight: '100px'
            };

            const buttonStyle = {
                padding: '8px 16px',
                backgroundColor: 'var(--button-bg)',
                color: 'white',
                border: 'none',
                borderRadius: '4px',
                cursor: 'pointer',
                fontWeight: '500',
                fontSize: '14px',
                transition: 'background-color 0.2s ease',
                opacity: (isLoading || !selectedTaskList || !title.trim()) ? 0.6 : 1
            };

/* {isDarkMode ? <Sun size={16} /> : <Moon size={16} />}
<Star size={20} fill={isImportant ? '#FFC107' : 'var(--star-color)'} color={isImportant ? '#FFC107' : 'var(--star-color)'} />
*/

            return (
                <div style={containerStyle}>
                    <div style={outerStyle}>
                        <button style={themeToggleStyle} onClick={toggleTheme}>
                            
                            Toggle Theme
                        </button>

                        <div style={{flexShrink: 0}}>
                            <div style={{marginBottom: '10px', minHeight: '20px'}}>
                                {message && <p>{message}</p>}
                            </div>

                            <div style={{display: 'inline-block', marginRight: '10px', marginBottom: '8px'}}>
                                <label htmlFor="tasklist" style={{fontWeight: '500', color: 'var(--text-secondary)', marginRight: '5px'}}>
                                    Select a task list: 
                                </label>
                                <select 
                                    id="tasklist"
                                    style={selectStyle}
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

                            <div style={{display: 'flex', alignItems: 'center', marginBottom: '8px'}}>
                                <div style={{display: 'flex', alignItems: 'center', marginRight: '10px'}}>
                                    <input
                                        type="checkbox"
                                        id="favstar"
                                        checked={isImportant}
                                        onChange={(e) => setIsImportant(e.target.checked)}
                                        style={{position: 'absolute', opacity: 0, width: 0, height: 0}}
                                    />
                                    <label htmlFor="favstar" title="Mark as important" style={{cursor: 'pointer', marginRight: '5px'}}>
                                        
                                    </label>
                                </div>
                                <label htmlFor="task-title" style={{fontWeight: '500', color: 'var(--text-secondary)', marginRight: '5px'}}>
                                    Title:
                                </label>
                                <input
                                    ref={titleInputRef}
                                    type="text"
                                    id="task-title"
                                    style={titleInputStyle}
                                    maxLength="250"
                                    value={title}
                                    onChange={(e) => setTitle(e.target.value)}
                                    onKeyDown={handleTitleKeyDown}
                                />
                            </div>

                            <div style={{display: 'inline-block', marginRight: '10px', marginBottom: '8px'}}>
                                <label htmlFor="task-date" style={{fontWeight: '500', color: 'var(--text-secondary)', marginRight: '5px'}}>
                                    Date:
                                </label>
                                <input
                                    type="date"
                                    id="task-date"
                                    style={inputStyle}
                                    value={date}
                                    onChange={(e) => setDate(e.target.value)}
                                />
                            </div>
                        </div>

                        <div style={{display: 'flex', flexDirection: 'column', flexGrow: 1, marginBottom: '10px'}}>
                            <textarea
                                style={textareaStyle}
                                placeholder="Add a note..."
                                value={note}
                                onChange={(e) => setNote(e.target.value)}
                            />
                        </div>

                        <div style={{flexShrink: 0}}>
                            <button 
                                onClick={handleSubmit}
                                style={buttonStyle}
                                disabled={isLoading || !selectedTaskList || !title.trim()}
                            >
                                {isLoading ? 'Adding...' : 'Add'}
                            </button>
                        </div>
                    </div>
                </div>
            );
        };

        // Render the component
        ReactDOM.render(<TodoTaskEntry />, document.getElementById('root'));
    </script>
</body>
</html>
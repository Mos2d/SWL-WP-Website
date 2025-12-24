/**
 * Sarah Loz Interactive Game Framework
 * Base class for all interactive games
 */

class GameFramework {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.error(`Container with ID "${containerId}" not found.`);
            return;
        }

        // Default options
        this.options = Object.assign({
            width: 800,
            height: 600,
            backgroundColor: '#f0f0f0',
            difficulty: 'medium',
            maxTime: 0, // 0 means no time limit
            showScore: true,
            showTimer: true,
            autoStart: false,
            onComplete: null,
            onScoreUpdate: null,
            mobileResponsive: true, // New option for mobile responsiveness
            minMobileWidth: 320,    // Minimum width for mobile devices
        }, options);

        // Game state
        this.state = {
            started: false,
            paused: false,
            completed: false,
            score: 0,
            time: 0,
            timerInterval: null,
            elements: [],
            isLandscape: window.innerWidth > window.innerHeight,
            isMobile: this.detectMobile()
        };

        // Initialize
        this.init();
        
        // Add resize and orientation change listeners for mobile responsiveness
        if (this.options.mobileResponsive) {
            window.addEventListener('resize', this.handleResize.bind(this));
            window.addEventListener('orientationchange', this.handleOrientationChange.bind(this));
            // Initial check
            this.updateLayout();
        }
    }

    init() {
        // Create game container
        this.gameContainer = document.createElement('div');
        this.gameContainer.className = 'sarah-loz-game-container';
        
        // Set initial dimensions as CSS custom properties instead of inline styles
        this.gameContainer.style.setProperty('--game-width', `${this.options.width}px`);
        this.gameContainer.style.setProperty('--game-height', `${this.options.height}px`);
        this.gameContainer.style.setProperty('--game-bg-color', this.options.backgroundColor);
        
        // Only set essential styles that won't conflict with responsive CSS
        this.gameContainer.style.position = 'relative';
        this.gameContainer.style.overflow = 'hidden';
        this.gameContainer.style.margin = '0 auto';
        
        // Let the new responsive CSS handle width, height, and styling
        // Remove direct width/height setting to allow CSS to control responsiveness

        // Create UI elements container
        this.uiContainer = document.createElement('div');
        this.uiContainer.className = 'sarah-loz-game-ui';
        this.uiContainer.style.position = 'absolute';
        this.uiContainer.style.top = '10px';
        this.uiContainer.style.left = '10px';
        this.uiContainer.style.right = '10px';
        this.uiContainer.style.display = 'flex';
        this.uiContainer.style.justifyContent = 'space-between';
        this.uiContainer.style.zIndex = '100';

        // Add score display if enabled
        if (this.options.showScore) {
            this.scoreDisplay = document.createElement('div');
            this.scoreDisplay.className = 'sarah-loz-game-score';
            this.scoreDisplay.textContent = `النقاط: 0`;
            this.scoreDisplay.style.padding = '5px 10px';
            this.scoreDisplay.style.backgroundColor = 'rgba(255, 255, 255, 0.7)';
            this.scoreDisplay.style.borderRadius = '4px';
            this.uiContainer.appendChild(this.scoreDisplay);
        }

        // Add timer display if enabled
        if (this.options.showTimer) {
            this.timeDisplay = document.createElement('div');
            this.timeDisplay.className = 'sarah-loz-game-timer';
            this.timeDisplay.textContent = `الوقت: 0`;
            this.timeDisplay.style.padding = '5px 10px';
            this.timeDisplay.style.backgroundColor = 'rgba(255, 255, 255, 0.7)';
            this.timeDisplay.style.borderRadius = '4px';
            this.uiContainer.appendChild(this.timeDisplay);
        }

        // Create game content area
        this.gameContent = document.createElement('div');
        this.gameContent.className = 'sarah-loz-game-content';
        this.gameContent.style.width = '100%';
        this.gameContent.style.height = '100%';
        this.gameContent.style.position = 'relative';
        this.gameContent.style.display = 'flex';
        this.gameContent.style.flexDirection = 'column';
        this.gameContent.style.overflow = 'hidden';

        // Create overlay for start/pause/end screens
        this.overlay = document.createElement('div');
        this.overlay.className = 'sarah-loz-game-overlay';
        this.overlay.style.position = 'absolute';
        this.overlay.style.top = '0';
        this.overlay.style.left = '0';
        this.overlay.style.width = '100%';
        this.overlay.style.height = '100%';
        this.overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
        this.overlay.style.display = 'flex';
        this.overlay.style.flexDirection = 'column';
        this.overlay.style.justifyContent = 'center';
        this.overlay.style.alignItems = 'center';
        this.overlay.style.color = 'white';
        this.overlay.style.textAlign = 'center';
        this.overlay.style.zIndex = '200';

        // Create start button
        this.startButton = document.createElement('button');
        this.startButton.className = 'sarah-loz-game-start-btn';
        this.startButton.textContent = 'ابدأ اللعبة';
        this.startButton.style.padding = '10px 20px';
        this.startButton.style.fontSize = '18px';
        this.startButton.style.backgroundColor = '#4caf50';
        this.startButton.style.color = 'white';
        this.startButton.style.border = 'none';
        this.startButton.style.borderRadius = '4px';
        this.startButton.style.cursor = 'pointer';
        this.startButton.style.margin = '20px 0';
        this.startButton.addEventListener('click', () => this.start());
        this.overlay.appendChild(this.startButton);
        
        // Create orientation message for mobile (initially hidden)
        this.orientationMessage = document.createElement('div');
        this.orientationMessage.className = 'sarah-loz-game-orientation-message';
        this.orientationMessage.style.position = 'absolute';
        this.orientationMessage.style.top = '0';
        this.orientationMessage.style.left = '0';
        this.orientationMessage.style.width = '100%';
        this.orientationMessage.style.height = '100%';
        this.orientationMessage.style.backgroundColor = 'rgba(0, 0, 0, 0.9)';
        this.orientationMessage.style.display = 'none';
        this.orientationMessage.style.flexDirection = 'column';
        this.orientationMessage.style.justifyContent = 'center';
        this.orientationMessage.style.alignItems = 'center';
        this.orientationMessage.style.color = 'white';
        this.orientationMessage.style.textAlign = 'center';
        this.orientationMessage.style.zIndex = '300';
        this.orientationMessage.style.padding = '20px';
        
        // Add rotation icon
        const rotateIcon = document.createElement('div');
        rotateIcon.innerHTML = `<svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 12C4 7.58172 7.58172 4 12 4V4C16.4183 4 20 7.58172 20 12V12C20 16.4183 16.4183 20 12 20V20C7.58172 20 4 16.4183 4 12V12Z" stroke="white" stroke-width="2"/>
            <path d="M16 12L12 8M12 8L8 12M12 8V16" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>`;
        rotateIcon.style.marginBottom = '20px';
        rotateIcon.style.animation = 'rotate 2s infinite ease';
        
        // Add animation style
        const style = document.createElement('style');
        style.textContent = `
            @keyframes rotate {
                0% { transform: rotate(0deg); }
                25% { transform: rotate(90deg); }
                50% { transform: rotate(90deg); }
                75% { transform: rotate(0deg); }
                100% { transform: rotate(0deg); }
            }
        `;
        document.head.appendChild(style);
        
        // Add message text
        const messageText = document.createElement('p');
        messageText.textContent = 'من فضلك قم بتدوير الجهاز للعب بشكل أفضل';
        messageText.style.fontSize = '18px';
        
        this.orientationMessage.appendChild(rotateIcon);
        this.orientationMessage.appendChild(messageText);

        // Assemble the game elements
        this.gameContainer.appendChild(this.gameContent);
        this.gameContainer.appendChild(this.uiContainer);
        this.gameContainer.appendChild(this.overlay);
        this.gameContainer.appendChild(this.orientationMessage);
        this.container.appendChild(this.gameContainer);

        // Auto start if configured
        if (this.options.autoStart) {
            this.start();
        }
    }

    start() {
        this.state.started = true;
        this.state.paused = false;
        this.state.completed = false;
        this.state.score = 0;
        this.state.time = 0;
        
        // Hide overlay
        this.overlay.style.display = 'none';
        
        // Start timer
        if (this.options.showTimer) {
            this.startTimer();
        }
        
        // Update score display
        if (this.options.showScore) {
            this.updateScore(0);
        }
        
        // Call game-specific start method (to be implemented by child classes)
        this.onGameStart();
    }

    pause() {
        if (!this.state.started || this.state.completed) return;
        
        this.state.paused = true;
        clearInterval(this.state.timerInterval);
        
        // Show pause overlay
        this.overlay.style.display = 'flex';
        this.startButton.textContent = 'استمرار';
        
        // Call game-specific pause method
        this.onGamePause();
    }

    resume() {
        if (!this.state.started || !this.state.paused || this.state.completed) return;
        
        this.state.paused = false;
        
        // Hide overlay
        this.overlay.style.display = 'none';
        
        // Resume timer
        if (this.options.showTimer) {
            this.startTimer();
        }
        
        // Call game-specific resume method
        this.onGameResume();
    }

    complete(score = this.state.score) {
        this.state.completed = true;
        clearInterval(this.state.timerInterval);
        
        // Update final score
        this.updateScore(score);
        
        // Show completion overlay
        this.overlay.style.display = 'flex';
        this.overlay.innerHTML = `
            <h2>اللعبة انتهت!</h2>
            <p>النقاط النهائية: ${this.state.score}</p>
            <button class="sarah-loz-game-restart-btn">العب مرة أخرى</button>
        `;
        
        const restartBtn = this.overlay.querySelector('.sarah-loz-game-restart-btn');
        if (restartBtn) {
            restartBtn.style.padding = '10px 20px';
            restartBtn.style.fontSize = '18px';
            restartBtn.style.backgroundColor = '#4caf50';
            restartBtn.style.color = 'white';
            restartBtn.style.border = 'none';
            restartBtn.style.borderRadius = '4px';
            restartBtn.style.cursor = 'pointer';
            restartBtn.style.margin = '20px 0';
            restartBtn.addEventListener('click', () => this.start());
        }
        
        // Call completion callback if provided
        if (typeof this.options.onComplete === 'function') {
            this.options.onComplete(this.state.score, this.state.time);
        }
        
        // Track progress via AJAX if user is logged in
        this.trackProgress();
        
        // Call game-specific complete method
        this.onGameComplete();
    }

    updateScore(score) {
        this.state.score = score;
        if (this.options.showScore && this.scoreDisplay) {
            this.scoreDisplay.textContent = `النقاط: ${score}`;
        }
        
        // Call score update callback if provided
        if (typeof this.options.onScoreUpdate === 'function') {
            this.options.onScoreUpdate(score);
        }
    }

    startTimer() {
        clearInterval(this.state.timerInterval);
        const startTime = Date.now() - (this.state.time * 1000);
        
        this.state.timerInterval = setInterval(() => {
            if (this.state.paused || this.state.completed) return;
            
            const elapsedSeconds = Math.floor((Date.now() - startTime) / 1000);
            this.state.time = elapsedSeconds;
            
            if (this.timeDisplay) {
                this.timeDisplay.textContent = `الوقت: ${elapsedSeconds}`;
            }
            
            // Check for time limit
            if (this.options.maxTime > 0 && elapsedSeconds >= this.options.maxTime) {
                this.complete();
            }
        }, 1000);
    }

    trackProgress() {
        if (typeof wp === 'undefined' || !wp.ajax || !wp.ajax.post) {
            console.error('WordPress AJAX not available');
            return;
        }
        
        wp.ajax.post('sarah_loz_track_game_progress', {
            game_id: this.options.gameId || 0,
            score: this.state.score,
            time: this.state.time,
            completed: this.state.completed,
            nonce: this.options.nonce || ''
        }).done(response => {
            console.log('Progress tracked successfully', response);
        }).fail(error => {
            console.error('Failed to track progress', error);
        });
    }

    // Methods for mobile responsiveness
    detectMobile() {
        return (
            (navigator.userAgent.match(/Android/i)) ||
            (navigator.userAgent.match(/webOS/i)) ||
            (navigator.userAgent.match(/iPhone/i)) ||
            (navigator.userAgent.match(/iPad/i)) ||
            (navigator.userAgent.match(/iPod/i)) ||
            (navigator.userAgent.match(/BlackBerry/i)) ||
            (navigator.userAgent.match(/Windows Phone/i))
        );
    }

    handleResize() {
        this.updateLayout();
    }

    handleOrientationChange() {
        // Wait for the orientation change to complete
        setTimeout(() => {
            this.updateLayout();
        }, 100);
    }

    updateLayout() {
        // Check if mobile device and responsiveness is enabled
        if (!this.options.mobileResponsive) {
            return;
        }

        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        this.state.isLandscape = viewportWidth > viewportHeight;
        
        // Update CSS custom properties for responsive calculations
        this.gameContainer.style.setProperty('--viewport-width', `${viewportWidth}px`);
        this.gameContainer.style.setProperty('--viewport-height', `${viewportHeight}px`);
        this.gameContainer.style.setProperty('--is-landscape', this.state.isLandscape ? '1' : '0');
        this.gameContainer.style.setProperty('--is-mobile', this.state.isMobile ? '1' : '0');
        
        // Always show the game (remove orientation message logic)
        this.showGame();
        
        // Instead of setting inline dimensions, let CSS handle responsiveness
        // Only update CSS custom properties that CSS can use for calculations
        const gameAspectRatio = this.options.width / this.options.height;
        this.gameContainer.style.setProperty('--game-aspect-ratio', gameAspectRatio);
        
        // Add responsive classes for CSS to use
        this.gameContainer.classList.toggle('is-mobile', this.state.isMobile);
        this.gameContainer.classList.toggle('is-landscape', this.state.isLandscape);
        this.gameContainer.classList.toggle('is-portrait', !this.state.isLandscape);
        
        // Trigger a custom event that other parts can listen to
        this.gameContainer.dispatchEvent(new CustomEvent('gameLayoutUpdate', {
            detail: {
                isMobile: this.state.isMobile,
                isLandscape: this.state.isLandscape,
                viewportWidth,
                viewportHeight,
                aspectRatio: gameAspectRatio
            }
        }));
        
        // Update game-specific layouts (for internal game elements, not container size)
        this.onLayoutUpdate();
    }

    showOrientationMessage() {
        // Keep this method but modify it to not hide the game content
        // This way existing code that calls this method won't break
        if (this.orientationMessage) {
            // Just don't show the orientation message - we're adapting instead
            this.orientationMessage.style.display = 'none';
            this.gameContent.style.visibility = 'visible';
            this.uiContainer.style.visibility = 'visible';
            
            if (this.overlay) {
                this.overlay.style.visibility = 'visible';
            }
        }
    }

    showGame() {
        if (this.orientationMessage) {
            this.orientationMessage.style.display = 'none';
            this.gameContent.style.visibility = 'visible';
            this.uiContainer.style.visibility = 'visible';
            
            if (this.overlay) {
                this.overlay.style.visibility = 'visible';
            }
        }
    }

    // Methods to be implemented by child classes
    onGameStart() { /* Override in child class */ }
    onGamePause() { /* Override in child class */ }
    onGameResume() { /* Override in child class */ }
    onGameComplete() { /* Override in child class */ }
    onLayoutUpdate() { /* Override in child class - called when layout changes */ }

    // Helper methods for creating game elements
    createElement(type, options = {}) {
        const element = document.createElement(type);
        
        // Apply styles
        if (options.styles) {
            Object.assign(element.style, options.styles);
        }
        
        // Apply attributes
        if (options.attributes) {
            for (const [key, value] of Object.entries(options.attributes)) {
                element.setAttribute(key, value);
            }
        }
        
        // Add text content
        if (options.text) {
            element.textContent = options.text;
        }
        
        // Add HTML content
        if (options.html) {
            element.innerHTML = options.html;
        }
        
        // Add CSS classes
        if (options.classes) {
            if (Array.isArray(options.classes)) {
                element.classList.add(...options.classes);
            } else {
                element.className = options.classes;
            }
        }
        
        // Append to parent
        if (options.parent) {
            options.parent.appendChild(element);
        } else {
            this.gameContent.appendChild(element);
        }
        
        // Store reference
        this.state.elements.push(element);
        
        return element;
    }

    removeElement(element) {
        if (element && element.parentNode) {
            element.parentNode.removeChild(element);
            this.state.elements = this.state.elements.filter(el => el !== element);
        }
    }

    clearElements() {
        this.state.elements.forEach(element => {
            if (element && element.parentNode) {
                element.parentNode.removeChild(element);
            }
        });
        this.state.elements = [];
    }
} 
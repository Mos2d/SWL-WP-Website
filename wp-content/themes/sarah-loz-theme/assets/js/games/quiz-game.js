/**
 * Quiz Game
 * Multiple choice questions with scoring
 */

class QuizGame extends GameFramework {
    constructor(containerId, options = {}) {
        // Set default options for quiz game
        const quizGameOptions = Object.assign({
            questions: [], // Array of question objects
            shuffleQuestions: true,
            shuffleAnswers: true,
            timePerQuestion: 20, // Seconds per question
            showExplanations: true,
            nextQuestionDelay: 1500, // Delay in milliseconds before moving to next question
            difficulty: 'medium',
            mobileResponsive: true, // Enable mobile responsiveness
            adaptiveFontSize: true // Adjust font size based on screen size
        }, options);

        super(containerId, quizGameOptions);

        // Initialize default font sizes first
        const defaultFontSizes = {
            question: 24,
            answer: 18,
            progress: 18,
            explanation: 16
        };

        // Game-specific state
        this.gameState = {
            questions: [],
            currentQuestion: 0,
            correctAnswers: 0,
            questionContainer: null,
            answered: false,
            // Add responsive design state variables
            defaultFontSizes: defaultFontSizes,
            currentFontSizes: {
                question: defaultFontSizes.question,
                answer: defaultFontSizes.answer,
                progress: defaultFontSizes.progress,
                explanation: defaultFontSizes.explanation
            }
        };
        
        // Ensure layout is updated after proper initialization
        setTimeout(() => {
            this.updateResponsiveLayout();
        }, 0);
    }

    onGameStart() {
        // Clear previous game
        this.clearElements();
        this.gameContent.innerHTML = '';
        
        // Reset game-specific state
        if (!this.gameState) {
            // Safety check - reinitialize gameState if it's undefined
            console.warn('gameState was undefined in onGameStart, reinitializing');
            this.gameState = {
                questions: [],
                currentQuestion: 0,
                correctAnswers: 0,
                questionContainer: null,
                answered: false,
                defaultFontSizes: {
                    question: 24,
                    answer: 18,
                    progress: 18,
                    explanation: 16
                },
                currentFontSizes: {
                    question: 24,
                    answer: 18,
                    progress: 18,
                    explanation: 16
                }
            };
        } else {
            // Just reset game state values
            this.gameState.currentQuestion = 0;
            this.gameState.correctAnswers = 0;
            this.gameState.answered = false;
        }
        
        // Get questions based on difficulty
        this.prepareQuestions();
        
        // Create question container
        this.gameState.questionContainer = this.createElement('div', {
            classes: 'quiz-question-container',
            styles: {
                width: '100%',
                height: '100%',
                padding: this.state.isMobile ? '15px' : '20px',
                display: 'flex',
                flexDirection: 'column',
                justifyContent: 'center',
                alignItems: 'center',
                overflow: 'auto', // Add scrolling for small screens
                boxSizing: 'border-box' // Ensure padding doesn't add to element size
            }
        });
        
        // Display first question
        this.displayQuestion(0);
    }

    prepareQuestions() {
        let questions = [...this.options.questions];
        
        // Apply difficulty filter if needed
        if (this.options.difficulty && questions.some(q => q.difficulty)) {
            questions = questions.filter(q => q.difficulty === this.options.difficulty);
        }
        
        // Fallback to all questions if none match the difficulty
        if (questions.length === 0) {
            questions = [...this.options.questions];
        }
        
        // Shuffle questions if enabled
        if (this.options.shuffleQuestions) {
            questions = this.shuffleArray(questions);
        }
        
        this.gameState.questions = questions;
    }

    displayQuestion(index) {
        const question = this.gameState.questions[index];
        if (!question) {
            this.complete();
            return;
        }
        
        // Clear previous question
        this.gameState.questionContainer.innerHTML = '';
        this.gameState.answered = false;
        
        // Update layout for current device size before adding elements
        this.updateResponsiveLayout();
        
        // Create progress indicator
        const progress = this.createElement('div', {
            classes: 'quiz-progress',
            parent: this.gameState.questionContainer,
            text: `سؤال ${index + 1} من ${this.gameState.questions.length}`,
            styles: {
                marginBottom: this.state.isMobile ? '15px' : '20px',
                fontSize: `${this.gameState.currentFontSizes.progress}px`,
                color: '#555',
                textAlign: 'center',
                width: '100%'
            }
        });
        
        // Create question text
        const questionText = this.createElement('div', {
            classes: 'quiz-question-text',
            parent: this.gameState.questionContainer,
            html: question.text,
            styles: {
                fontSize: `${this.gameState.currentFontSizes.question}px`,
                fontWeight: 'bold',
                marginBottom: this.state.isMobile ? '20px' : '30px',
                textAlign: 'center',
                color: '#333',
                width: '100%',
                wordBreak: 'break-word' // Ensure long words don't overflow
            }
        });
        
        // Add image if available
        if (question.image) {
            const questionImage = this.createElement('img', {
                classes: 'quiz-question-image',
                parent: this.gameState.questionContainer,
                attributes: {
                    src: question.image,
                    alt: 'Question Image',
                    loading: 'lazy' // Add lazy loading for better performance
                },
                styles: {
                    maxWidth: '90%',
                    maxHeight: this.state.isMobile ? '150px' : '200px',
                    marginBottom: this.state.isMobile ? '15px' : '20px',
                    borderRadius: '8px',
                    boxShadow: '0 2px 5px rgba(0,0,0,0.1)',
                    objectFit: 'contain' // Maintain aspect ratio
                }
            });
            
            // Add error handling for image loading
            questionImage.addEventListener('error', () => {
                questionImage.style.display = 'none';
                console.warn('Failed to load question image');
            });
        }
        
        // Create answers container with improved mobile layout
        const answersContainer = this.createElement('div', {
            classes: 'quiz-answers-container',
            parent: this.gameState.questionContainer,
            styles: {
                width: '100%',
                maxWidth: this.state.isMobile ? '100%' : '600px',
                display: 'flex',
                flexDirection: 'column',
                gap: this.state.isMobile ? '8px' : '10px',
                boxSizing: 'border-box',
                padding: this.state.isMobile ? '0 5px' : '0'
            }
        });
        
        // Prepare answers
        let answers = [...question.answers];
        if (this.options.shuffleAnswers) {
            answers = this.shuffleArray(answers);
        }
        
        // Create answer buttons with touch-friendly styles
        answers.forEach((answer, answerIndex) => {
            const answerButton = this.createElement('button', {
                classes: 'quiz-answer-button',
                parent: answersContainer,
                html: answer.text,
                styles: {
                    padding: this.state.isMobile ? '12px 10px' : '15px',
                    fontSize: `${this.gameState.currentFontSizes.answer}px`,
                    backgroundColor: '#f0f0f0',
                    border: '2px solid #ddd',
                    borderRadius: '8px',
                    cursor: 'pointer',
                    textAlign: 'right',
                    transition: 'all 0.2s ease',
                    touchAction: 'manipulation', // Prevents double-tap zoom on mobile
                    webkitTapHighlightColor: 'transparent', // Removes tap highlight on iOS
                    width: '100%',
                    boxSizing: 'border-box', // Ensure padding doesn't add to element size
                    position: 'relative', // For touch feedback effect
                    minHeight: this.state.isMobile ? '44px' : '50px' // Minimum touch target size
                }
            });
            
            // Add touch ripple effect for mobile
            if (this.state.isMobile) {
                answerButton.addEventListener('touchstart', function(e) {
                    if (this.gameState && this.gameState.answered) return;
                    
                    const rect = this.getBoundingClientRect();
                    const ripple = document.createElement('span');
                    const size = Math.max(rect.width, rect.height);
                    const x = e.touches[0].clientX - rect.left - size / 2;
                    const y = e.touches[0].clientY - rect.top - size / 2;
                    
                    ripple.className = 'quiz-answer-ripple';
                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        top: ${y}px;
                        left: ${x}px;
                        background-color: rgba(0, 0, 0, 0.1);
                        border-radius: 50%;
                        transform: scale(0);
                        transition: transform 0.3s, opacity 0.3s;
                        opacity: 1;
                        pointer-events: none;
                    `;
                    
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.style.transform = 'scale(2)';
                        ripple.style.opacity = '0';
                        
                        setTimeout(() => {
                            if (ripple.parentNode) {
                                ripple.parentNode.removeChild(ripple);
                            }
                        }, 400);
                    }, 10);
                });
            }
            
            // Hover effect (only for non-touch devices)
            if (!this.state.isMobile) {
                answerButton.addEventListener('mouseover', () => {
                    if (!this.gameState.answered) {
                        answerButton.style.backgroundColor = '#e0e0e0';
                        answerButton.style.borderColor = '#ccc';
                    }
                });
                
                answerButton.addEventListener('mouseout', () => {
                    if (!this.gameState.answered) {
                        answerButton.style.backgroundColor = '#f0f0f0';
                        answerButton.style.borderColor = '#ddd';
                    }
                });
            }
            
            // Click/Touch event with improved handling
            const handleSelection = () => {
                if (this.gameState.answered) return;
                
                this.gameState.answered = true;
                answerButton.style.transform = 'scale(0.98)'; // Subtle press effect
                
                // Add slight delay for visual feedback
                setTimeout(() => {
                    answerButton.style.transform = 'scale(1)';
                    this.handleAnswer(answer.correct, answerButton, answers);
                }, 150);
            };
            
            answerButton.addEventListener('click', handleSelection);
            
            if (this.state.isMobile) {
                // Ensure touch events don't trigger multiple times
                let touchStarted = false;
                
                answerButton.addEventListener('touchstart', () => {
                    touchStarted = true;
                });
                
                answerButton.addEventListener('touchend', (e) => {
                    if (touchStarted) {
                        e.preventDefault();
                        handleSelection();
                    }
                    touchStarted = false;
                });
                
                // Cancel touch if moved significantly
                answerButton.addEventListener('touchmove', (e) => {
                    const touch = e.touches[0];
                    const rect = answerButton.getBoundingClientRect();
                    
                    if (touch.clientX < rect.left || touch.clientX > rect.right ||
                        touch.clientY < rect.top || touch.clientY > rect.bottom) {
                        touchStarted = false;
                    }
                });
            }
        });
        
        // Add timer for this question if enabled
        if (this.options.timePerQuestion > 0) {
            let timeLeft = this.options.timePerQuestion;
            
            const timerBar = this.createElement('div', {
                classes: 'quiz-timer-bar',
                parent: this.gameState.questionContainer,
                styles: {
                    width: '100%',
                    height: this.state.isMobile ? '8px' : '10px',
                    backgroundColor: '#ddd',
                    borderRadius: '5px',
                    marginTop: this.state.isMobile ? '15px' : '20px',
                    position: 'relative',
                    overflow: 'hidden'
                }
            });
            
            const timerFill = this.createElement('div', {
                classes: 'quiz-timer-fill',
                parent: timerBar,
                styles: {
                    width: '100%',
                    height: '100%',
                    backgroundColor: '#4caf50',
                    transition: 'width 1s linear'
                }
            });
            
            const timerId = setInterval(() => {
                if (this.state.paused || this.state.completed || this.gameState.answered) {
                    clearInterval(timerId);
                    return;
                }
                
                timeLeft--;
                const percentage = (timeLeft / this.options.timePerQuestion) * 100;
                timerFill.style.width = `${percentage}%`;
                
                // Change color based on time left
                if (timeLeft <= this.options.timePerQuestion * 0.25) {
                    timerFill.style.backgroundColor = '#f44336';
                } else if (timeLeft <= this.options.timePerQuestion * 0.5) {
                    timerFill.style.backgroundColor = '#ff9800';
                }
                
                if (timeLeft <= 0) {
                    clearInterval(timerId);
                    if (!this.gameState.answered) {
                        this.gameState.answered = true;
                        this.handleTimeout(answers);
                    }
                }
            }, 1000);
        }
    }

    handleAnswer(isCorrect, selectedButton, answers) {
        // Apply visual feedback
        answers.forEach((answer, index) => {
            const button = this.gameState.questionContainer.querySelectorAll('.quiz-answer-button')[index];
            
            if (answer.correct) {
                button.style.backgroundColor = '#4caf50';
                button.style.borderColor = '#388e3c';
                button.style.color = 'white';
            }
            
            if (button === selectedButton && !isCorrect) {
                button.style.backgroundColor = '#f44336';
                button.style.borderColor = '#d32f2f';
                button.style.color = 'white';
            }
            
            // Disable pointer events
            button.style.pointerEvents = 'none';
        });
        
        // Add explanation if available and enabled
        const currentQuestion = this.gameState.questions[this.gameState.currentQuestion];
        if (this.options.showExplanations && currentQuestion.explanation) {
            const explanation = this.createElement('div', {
                classes: 'quiz-explanation',
                parent: this.gameState.questionContainer,
                html: currentQuestion.explanation,
                styles: {
                    marginTop: '20px',
                    padding: '15px',
                    backgroundColor: '#e8f5e9',
                    borderRadius: '8px',
                    fontSize: `${this.gameState.currentFontSizes.explanation}px`,
                    color: '#333'
                }
            });
        }
        
        // Update game state
        if (isCorrect) {
            this.gameState.correctAnswers++;
        }
        
        // Calculate and update score
        const score = Math.floor((this.gameState.correctAnswers / this.gameState.questions.length) * 100);
        this.updateScore(score);
        
        // Move to next question after delay
        setTimeout(() => {
            this.gameState.currentQuestion++;
            this.displayQuestion(this.gameState.currentQuestion);
        }, this.options.nextQuestionDelay);
    }

    handleTimeout(answers) {
        // Highlight correct answer(s)
        answers.forEach((answer, index) => {
            const button = this.gameState.questionContainer.querySelectorAll('.quiz-answer-button')[index];
            
            if (answer.correct) {
                button.style.backgroundColor = '#4caf50';
                button.style.borderColor = '#388e3c';
                button.style.color = 'white';
            }
            
            // Disable pointer events
            button.style.pointerEvents = 'none';
        });
        
        // Add timeout message
        const timeoutMessage = this.createElement('div', {
            classes: 'quiz-timeout-message',
            parent: this.gameState.questionContainer,
            text: 'انتهى الوقت!',
            styles: {
                marginTop: '20px',
                padding: '10px',
                backgroundColor: '#ffebee',
                color: '#c62828',
                borderRadius: '8px',
                fontWeight: 'bold',
                fontSize: `${this.gameState.currentFontSizes.explanation}px`
            }
        });
        
        // Add explanation if available and enabled
        const currentQuestion = this.gameState.questions[this.gameState.currentQuestion];
        if (this.options.showExplanations && currentQuestion.explanation) {
            const explanation = this.createElement('div', {
                classes: 'quiz-explanation',
                parent: this.gameState.questionContainer,
                html: currentQuestion.explanation,
                styles: {
                    marginTop: '15px',
                    padding: '15px',
                    backgroundColor: '#e8f5e9',
                    borderRadius: '8px',
                    fontSize: `${this.gameState.currentFontSizes.explanation}px`,
                    color: '#333'
                }
            });
        }
        
        // Calculate and update score
        const score = Math.floor((this.gameState.correctAnswers / this.gameState.questions.length) * 100);
        this.updateScore(score);
        
        // Move to next question after delay
        setTimeout(() => {
            this.gameState.currentQuestion++;
            this.displayQuestion(this.gameState.currentQuestion);
        }, this.options.nextQuestionDelay);
    }

    complete() {
        // Calculate final score
        const finalScore = Math.floor((this.gameState.correctAnswers / this.gameState.questions.length) * 100);
        
        // Call parent complete method
        super.complete(finalScore);
        
        // Add additional results information to completion overlay
        const resultsInfo = document.createElement('div');
        resultsInfo.innerHTML = `
            <p>الإجابات الصحيحة: ${this.gameState.correctAnswers} من ${this.gameState.questions.length}</p>
            <p>النسبة المئوية: ${finalScore}%</p>
        `;
        
        this.overlay.insertBefore(resultsInfo, this.overlay.querySelector('button'));
    }

    // Override parent's onLayoutUpdate method for responsive design
    onLayoutUpdate() {
        // Call the updateResponsiveLayout method only if gameState exists
        if (this.gameState) {
            this.updateResponsiveLayout();
            
            // Update current question display if game is active
            if (this.state.started && !this.state.completed && this.gameState.questionContainer) {
                this.updateCurrentQuestionDisplay();
            }
        }
    }

    // New method to update responsive layout
    updateResponsiveLayout() {
        if (!this.options.adaptiveFontSize || !this.gameState) {
            return;
        }
        
        // Ensure defaultFontSizes exists
        if (!this.gameState.defaultFontSizes) {
            this.gameState.defaultFontSizes = {
                question: 24,
                answer: 18,
                progress: 18,
                explanation: 16
            };
        }

        // Get the current viewport dimensions
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        
        // Update isMobile state
        this.state.isMobile = this.detectMobile() || viewportWidth < 768;
        
        // Calculate font scale based on viewport width and device
        let fontScale = 1;
        if (this.state.isMobile) {
            if (viewportWidth < 360) {
                fontScale = 0.7; // Smallest screens
            } else if (viewportWidth < 480) {
                fontScale = 0.8; // Small mobile
            } else if (viewportWidth < 768) {
                fontScale = 0.9; // Large mobile/small tablet
            }
        }
        
        // Apply font scaling with minimum sizes to ensure readability
        this.gameState.currentFontSizes = {
            question: Math.max(16, Math.round(this.gameState.defaultFontSizes.question * fontScale)),
            answer: Math.max(14, Math.round(this.gameState.defaultFontSizes.answer * fontScale)),
            progress: Math.max(12, Math.round(this.gameState.defaultFontSizes.progress * fontScale)),
            explanation: Math.max(12, Math.round(this.gameState.defaultFontSizes.explanation * fontScale))
        };
    }

    // New method to update the current question display
    updateCurrentQuestionDisplay() {
        // Update question text font size
        const questionText = this.gameState.questionContainer.querySelector('.quiz-question-text');
        if (questionText) {
            questionText.style.fontSize = `${this.gameState.currentFontSizes.question}px`;
        }
        
        // Update progress text font size
        const progressText = this.gameState.questionContainer.querySelector('.quiz-progress');
        if (progressText) {
            progressText.style.fontSize = `${this.gameState.currentFontSizes.progress}px`;
        }
        
        // Update answer buttons font size
        const answerButtons = this.gameState.questionContainer.querySelectorAll('.quiz-answer-button');
        answerButtons.forEach(button => {
            button.style.fontSize = `${this.gameState.currentFontSizes.answer}px`;
            button.style.padding = this.state.isMobile ? '12px 10px' : '15px';
        });
        
        // Update explanation font size if present
        const explanation = this.gameState.questionContainer.querySelector('.quiz-explanation');
        if (explanation) {
            explanation.style.fontSize = `${this.gameState.currentFontSizes.explanation}px`;
        }
        
        // Update timeout message font size if present
        const timeoutMessage = this.gameState.questionContainer.querySelector('.quiz-timeout-message');
        if (timeoutMessage) {
            timeoutMessage.style.fontSize = `${this.gameState.currentFontSizes.explanation}px`;
        }
        
        // Adjust the padding and width of the answer container for small screens
        const answersContainer = this.gameState.questionContainer.querySelector('.quiz-answers-container');
        if (answersContainer) {
            answersContainer.style.width = '100%';
            answersContainer.style.maxWidth = this.state.isMobile ? '100%' : '600px';
            answersContainer.style.padding = this.state.isMobile ? '0 5px' : '0';
        }
    }

    // Helper to shuffle array
    shuffleArray(array) {
        const newArray = [...array];
        for (let i = newArray.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [newArray[i], newArray[j]] = [newArray[j], newArray[i]];
        }
        return newArray;
    }

    onGamePause() {
        // Additional pause logic specific to quiz game
    }

    onGameResume() {
        // Additional resume logic specific to quiz game
    }

    onGameComplete() {
        // Additional completion logic specific to quiz game
    }
} 
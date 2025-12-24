/**
 * Audio Matching Game
 * Children listen to sounds and match them with the correct visual options
 */

class AudioMatchingGame extends GameFramework {
    constructor(containerId, options = {}) {
        // Set default options for audio matching game
        const audioGameOptions = Object.assign({
            gameData: null, // Game data with sounds and options
            speechRate: 0.8,
            speechPitch: 1.2,
            speechVolume: 1.0,
            language: 'ar-SA',
            showFeedback: true,
            autoPlayIntro: true,
            shuffleOptions: true,
            maxAttempts: 3,
            successSound: '🎉',
            errorSound: '😞',
            mobileResponsive: true,
            backgroundColor: '#667eea',
            voiceInstructions: {
                welcomeMessageType: 'text',
                welcomeMessageText: 'مرحباً! اضغط على الأصوات واختر الإجابة الصحيحة',
                welcomeMessageFile: '',
                instructionMessageType: 'text',
                instructionMessageText: 'اضغط على الزر لسماع الصوت، ثم اختر الإجابة الصحيحة من الخيارات المتاحة',
                instructionMessageFile: ''
            },
            completionMessages: {
                correctAnswerType: 'text',
                correctAnswerText: 'أحسنت! إجابة صحيحة',
                correctAnswerFiles: [],
                wrongAnswerType: 'text',
                wrongAnswerText: 'حاول مرة أخرى',
                wrongAnswerFiles: [],
                gameCompleteType: 'text',
                gameCompleteText: 'ممتاز! لقد أكملت جميع الأسئلة بنجاح! أحسنت العمل',
                gameCompleteFile: ''
            }
        }, options);

        super(containerId, audioGameOptions);

        // Game-specific state
        this.gameState = {
            currentLevel: 0,
            currentQuestion: null,
            selectedSound: null,
            correctAnswers: 0,
            totalQuestions: 0,
            attempts: 0,
            maxAttempts: this.options.maxAttempts,
            canSelect: false,
            completedQuestions: new Set(),
            speechSynthesis: window.speechSynthesis,
            currentUtterance: null,
            currentAudio: null
        };

        // Default game data if none provided
        if (!this.options.gameData) {
            this.options.gameData = this.getDefaultGameData();
        }

        this.gameState.totalQuestions = this.options.gameData.questions.length;
        
        console.log('AudioMatchingGame constructor: gameState initialized', this.gameState);
    }

    getDefaultGameData() {
        return {
            title: "لعبة ربط الأصوات",
            description: "اضغط على الصوت واختر الإجابة الصحيحة",
            questions: [
                {
                    id: 1,
                    text: "أحمر",
                    type: "color",
                    sound: "أحمر",
                    options: [
                        { text: "أحمر", color: "#dc3545", correct: true },
                        { text: "أزرق", color: "#007bff", correct: false },
                        { text: "أخضر", color: "#28a745", correct: false },
                        { text: "أصفر", color: "#ffc107", correct: false }
                    ]
                },
                {
                    id: 2,
                    text: "أزرق",
                    type: "color",
                    sound: "أزرق",
                    options: [
                        { text: "أحمر", color: "#dc3545", correct: false },
                        { text: "أزرق", color: "#007bff", correct: true },
                        { text: "أخضر", color: "#28a745", correct: false },
                        { text: "أصفر", color: "#ffc107", correct: false }
                    ]
                },
                {
                    id: 3,
                    text: "أخضر",
                    type: "color",
                    sound: "أخضر",
                    options: [
                        { text: "أحمر", color: "#dc3545", correct: false },
                        { text: "أزرق", color: "#007bff", correct: false },
                        { text: "أخضر", color: "#28a745", correct: true },
                        { text: "أصفر", color: "#ffc107", correct: false }
                    ]
                },
                {
                    id: 4,
                    text: "أصفر",
                    type: "color",
                    sound: "أصفر",
                    options: [
                        { text: "أحمر", color: "#dc3545", correct: false },
                        { text: "أزرق", color: "#007bff", correct: false },
                        { text: "أخضر", color: "#28a745", correct: false },
                        { text: "أصفر", color: "#ffc107", correct: true }
                    ]
                }
            ]
        };
    }

    onGameStart() {
        // Clear previous game
        this.clearElements();
        this.gameContent.innerHTML = '';
        
        // Ensure gameState is properly initialized
        if (!this.gameState) {
            console.warn('gameState was undefined, reinitializing');
            this.gameState = {
                currentLevel: 0,
                currentQuestion: null,
                selectedSound: null,
                correctAnswers: 0,
                totalQuestions: this.options.gameData.questions.length,
                attempts: 0,
                maxAttempts: this.options.maxAttempts,
                canSelect: false,
                completedQuestions: new Set(),
                speechSynthesis: window.speechSynthesis,
                currentUtterance: null
            };
        }
        
        // Reset game-specific state
        this.gameState.currentLevel = 0;
        this.gameState.correctAnswers = 0;
        this.gameState.completedQuestions.clear();
        this.gameState.selectedSound = null;
        this.gameState.attempts = 0;
        
        // Create game layout
        this.createGameLayout();
        
        // Play intro if enabled
        if (this.options.autoPlayIntro) {
            setTimeout(() => {
                this.playWelcomeMessage();
            }, 1000);
        }
        
        // Allow interaction after a short delay
        setTimeout(() => {
            this.gameState.canSelect = true;
        }, 500);
    }

    createGameLayout() {
        // Create main container
        this.mainContainer = this.createElement('div', {
            classes: 'audio-game-main'
            // Let CSS handle all styling
        });

        // Create header
        this.createHeader();
        
        // Create game board
        this.createGameBoard();
        
        // Create feedback area
        this.createFeedbackArea();
        
        // Create control buttons
        this.createControlButtons();
    }

    createHeader() {
        const header = this.createElement('div', {
            classes: 'audio-game-header',
            parent: this.mainContainer
            // Let CSS handle styling
        });

        this.createElement('h2', {
            text: this.options.gameData.title || 'لعبة ربط الأصوات',
            parent: header
            // Let CSS handle styling
        });

        this.createElement('p', {
            text: this.options.gameData.description || 'اضغط على الصوت واختر الإجابة الصحيحة',
            parent: header
            // Let CSS handle styling
        });
    }

    createGameBoard() {
        this.gameBoard = this.createElement('div', {
            classes: 'audio-game-board',
            parent: this.mainContainer
            // Let CSS handle all styling
        });

        // Create sounds section
        this.createSoundsSection();
        
        // Create options section
        this.createOptionsSection();
    }

    createSoundsSection() {
        this.soundsSection = this.createElement('div', {
            classes: 'audio-sounds-section',
            parent: this.gameBoard
            // Let CSS handle all styling
        });

        this.createElement('h3', {
            text: '🔊 اضغط لسماع الصوت',
            parent: this.soundsSection
            // Let CSS handle styling
        });

        // Create sound buttons for all questions
        this.options.gameData.questions.forEach((question, index) => {
            const button = this.createElement('button', {
                text: `${this.getEmojiForType(question.type)} اضغط لسماع الصوت`,
                parent: this.soundsSection,
                classes: 'sound-button',
                attributes: {
                    'data-question-id': question.id,
                    'data-sound': question.sound
                },
                styles: {
                    display: 'block',
                    width: '100%',
                    background: 'linear-gradient(145deg, #4CAF50, #45a049)',
                    color: 'white',
                    border: 'none',
                    borderRadius: '12px',
                    padding: '15px 20px',
                    marginBottom: '15px',
                    fontSize: 'clamp(0.9rem, 2.5vw, 1.2rem)',
                    cursor: 'pointer',
                    transition: 'all 0.3s ease',
                    boxShadow: '0 4px 8px rgba(0,0,0,0.2)',
                    position: 'relative'
                }
            });

            // Add hover and active effects
            button.addEventListener('mouseenter', () => {
                if (!button.classList.contains('selected')) {
                    button.style.transform = 'translateY(-2px)';
                    button.style.boxShadow = '0 6px 12px rgba(0,0,0,0.3)';
                }
            });

            button.addEventListener('mouseleave', () => {
                if (!button.classList.contains('selected')) {
                    button.style.transform = '';
                    button.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
                }
            });

            button.addEventListener('click', () => this.playSound(question));
        });
    }

    createOptionsSection() {
        this.optionsSection = this.createElement('div', {
            classes: 'audio-options-section',
            parent: this.gameBoard
            // Let CSS handle all styling
        });

        this.createElement('h3', {
            text: '🎯 اختر الإجابة الصحيحة',
            parent: this.optionsSection
            // Let CSS handle styling
        });

        // Create a container for all options
        this.optionsContainer = this.createElement('div', {
            classes: 'options-container',
            parent: this.optionsSection
            // Let CSS handle all styling
        });

        // We'll populate options dynamically when a sound is played
        this.updateOptionsDisplay();
    }

    updateOptionsDisplay() {
        // Clear existing options
        this.optionsContainer.innerHTML = '';

        if (!this.gameState.currentQuestion) {
            // Show all unique options when no question is selected
            const allOptions = new Map();
            this.options.gameData.questions.forEach(question => {
                question.options.forEach(option => {
                    if (!allOptions.has(option.text)) {
                        allOptions.set(option.text, option);
                    }
                });
            });

            Array.from(allOptions.values()).forEach(option => {
                this.createOptionElement(option, false);
            });
        } else {
            // Show options for current question
            let options = [...this.gameState.currentQuestion.options];
            if (this.options.shuffleOptions) {
                options = this.shuffleArray(options);
            }

            options.forEach(option => {
                this.createOptionElement(option, true);
            });
        }
    }

    createOptionElement(option, interactive) {
        const optionElement = this.createElement('button', {
            text: option.text,
            parent: this.optionsContainer,
            classes: 'option-button',
            attributes: {
                'data-correct': option.correct || false,
                'data-text': option.text
            },
            styles: {
                display: 'block',
                width: '100%',
                background: option.color ? `linear-gradient(145deg, ${option.color}, ${this.darkenColor(option.color, 0.1)})` : '#e9ecef',
                color: this.getContrastColor(option.color || '#e9ecef'),
                border: '3px dashed #dee2e6',
                borderRadius: '12px',
                padding: '20px',
                fontSize: 'clamp(0.9rem, 2.5vw, 1.3rem)',
                textAlign: 'center',
                cursor: interactive ? 'pointer' : 'default',
                transition: 'all 0.3s ease',
                fontWeight: 'bold',
                opacity: interactive ? '1' : '0.7',
                pointerEvents: interactive ? 'auto' : 'none'
            }
        });

        if (interactive) {
            optionElement.addEventListener('mouseenter', () => {
                if (!optionElement.classList.contains('correct') && !optionElement.classList.contains('wrong')) {
                    optionElement.style.transform = 'scale(1.05)';
                    optionElement.style.boxShadow = '0 5px 15px rgba(0,0,0,0.2)';
                }
            });

            optionElement.addEventListener('mouseleave', () => {
                if (!optionElement.classList.contains('correct') && !optionElement.classList.contains('wrong')) {
                    optionElement.style.transform = '';
                    optionElement.style.boxShadow = '';
                }
            });

            optionElement.addEventListener('click', () => this.selectOption(option, optionElement));
        }

        return optionElement;
    }

    createFeedbackArea() {
        this.feedbackArea = this.createElement('div', {
            classes: 'audio-feedback-area',
            parent: this.mainContainer,
            styles: {
                minHeight: '60px',
                background: 'rgba(255, 255, 255, 0.9)',
                borderRadius: '12px',
                padding: '15px',
                marginBottom: '20px',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                fontSize: 'clamp(1rem, 3vw, 1.2rem)',
                fontWeight: 'bold',
                textAlign: 'center',
                opacity: '0',
                transform: 'translateY(20px)',
                transition: 'all 0.3s ease'
            }
        });
    }

    createControlButtons() {
        const controlsContainer = this.createElement('div', {
            classes: 'audio-controls',
            parent: this.mainContainer,
            styles: {
                display: 'flex',
                justifyContent: 'center',
                gap: '15px',
                flexWrap: 'wrap'
            }
        });

        // Reset button
        this.createElement('button', {
            text: '🔄 إعادة اللعب',
            parent: controlsContainer,
            classes: 'control-button reset-button',
            styles: {
                background: 'linear-gradient(145deg, #FF9800, #F57C00)',
                color: 'white',
                border: 'none',
                borderRadius: '12px',
                padding: '12px 30px',
                fontSize: 'clamp(0.9rem, 2.5vw, 1.1rem)',
                cursor: 'pointer',
                transition: 'all 0.3s ease',
                boxShadow: '0 4px 8px rgba(0,0,0,0.2)'
            }
        }).addEventListener('click', () => this.resetGame());

        // Stop speech button
        this.createElement('button', {
            text: '🔇 إيقاف الصوت',
            parent: controlsContainer,
            classes: 'control-button stop-speech-button',
            styles: {
                background: 'linear-gradient(145deg, #f44336, #d32f2f)',
                color: 'white',
                border: 'none',
                borderRadius: '12px',
                padding: '12px 30px',
                fontSize: 'clamp(0.9rem, 2.5vw, 1.1rem)',
                cursor: 'pointer',
                transition: 'all 0.3s ease',
                boxShadow: '0 4px 8px rgba(0,0,0,0.2)'
            }
        }).addEventListener('click', () => {
            this.stopSpeech();
            this.stopAudio();
        });

        // Instructions button
        this.createElement('button', {
            text: '📢 التعليمات',
            parent: controlsContainer,
            classes: 'control-button instructions-button',
            styles: {
                background: 'linear-gradient(145deg, #28a745, #1e7e34)',
                color: 'white',
                border: 'none',
                borderRadius: '12px',
                padding: '12px 30px',
                fontSize: 'clamp(0.9rem, 2.5vw, 1.1rem)',
                cursor: 'pointer',
                transition: 'all 0.3s ease',
                boxShadow: '0 4px 8px rgba(0,0,0,0.2)',
                marginLeft: '10px'
            }
        }).addEventListener('click', () => this.playInstructionsMessage());
    }

    playSound(question) {
        if (!this.gameState.canSelect) {
            this.showFeedback('انتظر قليلاً!', 'warning');
            return;
        }

        // Stop any current speech or audio
        this.stopSpeech();
        this.stopAudio();

        // Update selected sound state
        this.gameState.currentQuestion = question;
        this.gameState.selectedSound = question.sound;

        // Update UI to show selected sound
        this.updateSoundButtonsState(question.id);
        
        // Update options display
        this.updateOptionsDisplay();

        // Play the sound - check if it's an audio file or text-to-speech
        if (question.audioUrl) {
            // Play audio file
            this.playAudioFile(question.audioUrl);
            this.showFeedback(`تم تشغيل الملف الصوتي`, 'info');
        } else if (this.gameState.speechSynthesis) {
            // Use text-to-speech
            this.speakText(question.sound);
            this.showFeedback(`تم تشغيل صوت: ${question.sound}`, 'info');
        } else {
            this.showFeedback('عذراً، لا يمكن تشغيل الصوت', 'error');
        }
    }

    updateSoundButtonsState(selectedQuestionId) {
        const soundButtons = this.soundsSection.querySelectorAll('.sound-button');
        soundButtons.forEach(button => {
            const questionId = parseInt(button.getAttribute('data-question-id'));
            if (questionId === selectedQuestionId) {
                button.classList.add('selected');
                button.style.background = 'linear-gradient(145deg, #FF6B6B, #FF5252)';
                button.style.animation = 'pulse 1s infinite';
            } else {
                button.classList.remove('selected');
                button.style.background = 'linear-gradient(145deg, #4CAF50, #45a049)';
                button.style.animation = '';
            }
        });
    }

    selectOption(option, optionElement) {
        if (!this.gameState.canSelect || !this.gameState.currentQuestion) {
            this.showFeedback('اختر صوتاً أولاً!', 'warning');
            return;
        }

        if (this.gameState.completedQuestions.has(this.gameState.currentQuestion.id)) {
            this.showFeedback('تم ربط هذا الصوت بالفعل!', 'info');
            return;
        }

        this.gameState.attempts++;

        if (option.correct) {
            this.handleCorrectAnswer(optionElement);
        } else {
            this.handleWrongAnswer(optionElement);
        }
    }

    handleCorrectAnswer(optionElement) {
        // Visual feedback
        optionElement.classList.add('correct');
        optionElement.style.background = 'linear-gradient(145deg, #4CAF50, #45a049)';
        optionElement.style.color = 'white';
        optionElement.style.borderColor = '#4CAF50';
        optionElement.style.animation = 'correctAnswer 0.6s ease';

        // Audio feedback
        this.playCompletionMessage('correct');

        // Update game state
        this.gameState.completedQuestions.add(this.gameState.currentQuestion.id);
        this.gameState.correctAnswers++;
        this.gameState.attempts = 0; // Reset attempts for next question

        // Update score
        const score = Math.floor((this.gameState.correctAnswers / this.gameState.totalQuestions) * 100);
        this.updateScore(score);

        // Show feedback
        this.showFeedback('🎉 إجابة صحيحة! أحسنت!', 'success');

        // Remove selection state
        this.clearSoundSelection();

        // Check for game completion
        if (this.gameState.correctAnswers >= this.gameState.totalQuestions) {
            setTimeout(() => {
                this.complete();
            }, 2000);
        } else {
            // Clear current question after a delay
            setTimeout(() => {
                this.gameState.currentQuestion = null;
                this.updateOptionsDisplay();
                this.showFeedback('اختر صوتاً آخر للمتابعة', 'info');
            }, 2000);
        }
    }

    handleWrongAnswer(optionElement) {
        // Visual feedback
        optionElement.classList.add('wrong');
        optionElement.style.background = 'linear-gradient(145deg, #f44336, #d32f2f)';
        optionElement.style.color = 'white';
        optionElement.style.borderColor = '#f44336';
        optionElement.style.animation = 'wrongAnswer 0.6s ease';

        // Audio feedback
        if (this.gameState.attempts >= this.gameState.maxAttempts) {
            this.speakText('المحاولة الصحيحة هي: ' + this.gameState.currentQuestion.options.find(opt => opt.correct).text);
            this.showFeedback('حاول مرة أخرى! الإجابة الصحيحة: ' + this.gameState.currentQuestion.options.find(opt => opt.correct).text, 'error');
            
            // Reset for next question
            setTimeout(() => {
                this.gameState.attempts = 0;
                this.gameState.currentQuestion = null;
                this.clearSoundSelection();
                this.updateOptionsDisplay();
            }, 3000);
        } else {
            this.playCompletionMessage('wrong');
            this.showFeedback(`إجابة خاطئة! حاول مرة أخرى (المحاولة ${this.gameState.attempts}/${this.gameState.maxAttempts})`, 'error');
        }

        // Remove wrong class after animation
        setTimeout(() => {
            optionElement.classList.remove('wrong');
            optionElement.style.background = optionElement.getAttribute('data-original-bg') || '#e9ecef';
            optionElement.style.color = optionElement.getAttribute('data-original-color') || '#333';
            optionElement.style.borderColor = '#dee2e6';
            optionElement.style.animation = '';
        }, 1000);
    }

    clearSoundSelection() {
        const soundButtons = this.soundsSection.querySelectorAll('.sound-button');
        soundButtons.forEach(button => {
            button.classList.remove('selected');
            button.style.background = 'linear-gradient(145deg, #4CAF50, #45a049)';
            button.style.animation = '';
        });
        this.gameState.selectedSound = null;
    }

    showFeedback(message, type) {
        if (!this.options.showFeedback) return;

        this.feedbackArea.textContent = message;
        this.feedbackArea.style.opacity = '1';
        this.feedbackArea.style.transform = 'translateY(0)';

        // Set color based on type
        switch (type) {
            case 'success':
                this.feedbackArea.style.background = 'linear-gradient(145deg, #d4edda, #c3e6cb)';
                this.feedbackArea.style.color = '#155724';
                break;
            case 'error':
                this.feedbackArea.style.background = 'linear-gradient(145deg, #f8d7da, #f5c6cb)';
                this.feedbackArea.style.color = '#721c24';
                break;
            case 'warning':
                this.feedbackArea.style.background = 'linear-gradient(145deg, #fff3cd, #ffeaa7)';
                this.feedbackArea.style.color = '#856404';
                break;
            default:
                this.feedbackArea.style.background = 'linear-gradient(145deg, #d1ecf1, #bee5eb)';
                this.feedbackArea.style.color = '#0c5460';
        }

        // Auto-hide after delay
        setTimeout(() => {
            this.feedbackArea.style.opacity = '0';
            this.feedbackArea.style.transform = 'translateY(20px)';
        }, 4000);
    }

    speakText(text, callback = null) {
        if (!this.gameState.speechSynthesis || !text) {
            if (callback) callback();
            return;
        }

        // Stop any current speech
        this.stopSpeech();

        // Create new utterance
        this.gameState.currentUtterance = new SpeechSynthesisUtterance(text);
        this.gameState.currentUtterance.lang = this.options.language;
        this.gameState.currentUtterance.rate = this.options.speechRate;
        this.gameState.currentUtterance.pitch = this.options.speechPitch;
        this.gameState.currentUtterance.volume = this.options.speechVolume;

        // Add callback
        if (callback) {
            this.gameState.currentUtterance.onend = callback;
        }

        // Speak the text
        this.gameState.speechSynthesis.speak(this.gameState.currentUtterance);
    }

    stopSpeech() {
        if (this.gameState.speechSynthesis) {
            this.gameState.speechSynthesis.cancel();
        }
        this.gameState.currentUtterance = null;
    }

    playAudioFile(audioUrl, callback = null, errorCallback = null) {
        try {
            // Stop any current audio
            this.stopAudio();
            
            // Create new audio element
            this.gameState.currentAudio = new Audio(audioUrl);
            this.gameState.currentAudio.volume = this.options.speechVolume || 1.0;
            
            // Add event listeners
            this.gameState.currentAudio.addEventListener('ended', () => {
                console.log('Audio playback finished');
                if (callback) callback();
            });
            
            this.gameState.currentAudio.addEventListener('error', (e) => {
                console.error('Audio playback error:', e);
                if (errorCallback) {
                    errorCallback();
                } else {
                    this.showFeedback('خطأ في تشغيل الملف الصوتي', 'error');
                }
            });
            
            // Play the audio
            const playPromise = this.gameState.currentAudio.play();
            
            if (playPromise !== undefined) {
                playPromise.catch(error => {
                    console.error('Audio play error:', error);
                    if (errorCallback) {
                        errorCallback();
                    } else {
                        this.showFeedback('لا يمكن تشغيل الصوت تلقائياً، يرجى النقر على الزر مرة أخرى', 'warning');
                    }
                });
            }
        } catch (error) {
            console.error('Error creating audio:', error);
            if (errorCallback) {
                errorCallback();
            } else {
                this.showFeedback('خطأ في تحميل الملف الصوتي', 'error');
            }
        }
    }

    stopAudio() {
        if (this.gameState.currentAudio) {
            this.gameState.currentAudio.pause();
            this.gameState.currentAudio.currentTime = 0;
            this.gameState.currentAudio = null;
        }
    }

    resetGame() {
        this.stopSpeech();
        this.stopAudio();
        this.start(); // Restart the game
    }

    playWelcomeMessage() {
        const instructions = this.options.voiceInstructions;
        this.playInstructionAudio(
            instructions.welcomeMessageType,
            instructions.welcomeMessageText,
            instructions.welcomeMessageFile,
            () => {
                // After welcome message, play instructions if enabled
                setTimeout(() => {
                    this.playInstructionsMessage();
                }, 1500);
            }
        );
    }

    playInstructionsMessage() {
        const instructions = this.options.voiceInstructions;
        this.playInstructionAudio(
            instructions.instructionMessageType,
            instructions.instructionMessageText,
            instructions.instructionMessageFile
        );
    }

    playInstructionAudio(messageType, messageText, messageFile, callback = null) {
        if (messageType === 'file' && messageFile) {
            // Play audio file
            this.playAudioFile(messageFile, callback);
        } else if (messageType === 'both' && messageFile) {
            // Try audio file first, fallback to text
            this.playAudioFile(messageFile, callback, () => {
                // Fallback to text-to-speech if file fails
                this.speakText(messageText, callback);
            });
        } else {
            // Use text-to-speech
            this.speakText(messageText, callback);
        }
    }

    playCompletionMessage(messageType) {
        const messages = this.options.completionMessages;
        let audioType, text, files;

        switch (messageType) {
            case 'correct':
                audioType = messages.correctAnswerType;
                text = messages.correctAnswerText;
                files = messages.correctAnswerFiles;
                break;
            case 'wrong':
                audioType = messages.wrongAnswerType;
                text = messages.wrongAnswerText;
                files = messages.wrongAnswerFiles;
                break;
            case 'complete':
                audioType = messages.gameCompleteType;
                text = messages.gameCompleteText;
                files = [{ file: messages.gameCompleteFile }];
                break;
            default:
                return;
        }

        if (audioType === 'file' && files && files.length > 0) {
            // Play audio file (random if multiple)
            const randomFile = files[Math.floor(Math.random() * files.length)];
            if (randomFile.file) {
                this.playAudioFile(randomFile.file);
            }
        } else if (audioType === 'random' && files && files.length > 0) {
            // Play random audio from multiple files
            const randomFile = files[Math.floor(Math.random() * files.length)];
            if (randomFile.file) {
                this.playAudioFile(randomFile.file);
            }
        } else if (audioType === 'both' && files && files.length > 0) {
            // Try audio file first, fallback to text
            const randomFile = files[Math.floor(Math.random() * files.length)];
            if (randomFile.file) {
                this.playAudioFile(randomFile.file, null, () => {
                    // Fallback to text-to-speech if file fails
                    this.speakText(text);
                });
            } else {
                this.speakText(text);
            }
        } else {
            // Use text-to-speech
            this.speakText(text);
        }
    }

    getEmojiForType(type) {
        const emojiMap = {
            'color': '🎨',
            'animal': '🐾',
            'number': '🔢',
            'letter': '🔤',
            'shape': '🔶',
            'fruit': '🍎',
            'vehicle': '🚗'
        };
        return emojiMap[type] || '🔊';
    }

    // Helper methods
    shuffleArray(array) {
        const newArray = [...array];
        for (let i = newArray.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [newArray[i], newArray[j]] = [newArray[j], newArray[i]];
        }
        return newArray;
    }

    darkenColor(color, amount) {
        const usePound = color[0] === '#';
        const col = usePound ? color.slice(1) : color;
        const num = parseInt(col, 16);
        let r = (num >> 16) + amount;
        let g = (num >> 8 & 0x00FF) + amount;
        let b = (num & 0x0000FF) + amount;
        r = r > 255 ? 255 : r < 0 ? 0 : r;
        g = g > 255 ? 255 : g < 0 ? 0 : g;
        b = b > 255 ? 255 : b < 0 ? 0 : b;
        return (usePound ? '#' : '') + (r << 16 | g << 8 | b).toString(16);
    }

    getContrastColor(hexColor) {
        const r = parseInt(hexColor.substr(1, 2), 16);
        const g = parseInt(hexColor.substr(3, 2), 16);
        const b = parseInt(hexColor.substr(5, 2), 16);
        const brightness = ((r * 299) + (g * 587) + (b * 114)) / 1000;
        return brightness > 125 ? '#000000' : '#FFFFFF';
    }

    // Override parent methods
    onLayoutUpdate() {
        if (this.state.isMobile && window.innerWidth < 768) {
            // On mobile, stack sections vertically
            if (this.gameBoard) {
                this.gameBoard.style.gridTemplateColumns = '1fr';
                this.gameBoard.style.gap = '15px';
                this.gameBoard.style.padding = '15px';
            }
            
            if (this.optionsContainer) {
                this.optionsContainer.style.gridTemplateColumns = 'repeat(auto-fit, minmax(120px, 1fr))';
                this.optionsContainer.style.gap = '10px';
            }
        } else {
            // Desktop layout
            if (this.gameBoard) {
                this.gameBoard.style.gridTemplateColumns = '1fr 1fr';
                this.gameBoard.style.gap = '20px';
                this.gameBoard.style.padding = '20px';
            }
            
            if (this.optionsContainer) {
                this.optionsContainer.style.gridTemplateColumns = 'repeat(auto-fit, minmax(150px, 1fr))';
                this.optionsContainer.style.gap = '15px';
            }
        }
    }

    onGameComplete() {
        this.stopSpeech();
        this.stopAudio();
        
        // Speak completion message
        setTimeout(() => {
            this.playCompletionMessage('complete');
        }, 500);

        // Create celebration effect
        this.createCelebrationEffect();
    }

    createCelebrationEffect() {
        const celebration = this.createElement('div', {
            classes: 'audio-game-celebration',
            parent: this.gameContent,
            styles: {
                position: 'absolute',
                top: '0',
                left: '0',
                width: '100%',
                height: '100%',
                pointerEvents: 'none',
                zIndex: '160',
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center',
                flexDirection: 'column'
            }
        });

        // Add trophy and stars
        const trophy = this.createElement('div', {
            parent: celebration,
            html: '🏆⭐🎉',
            styles: {
                fontSize: '80px',
                marginBottom: '20px',
                animation: 'bounce 1s infinite alternate'
            }
        });

        const congrats = this.createElement('div', {
            parent: celebration,
            html: 'أحسنت! لقد أكملت اللعبة بنجاح!',
            styles: {
                fontSize: '24px',
                fontWeight: 'bold',
                color: '#4CAF50',
                textAlign: 'center',
                padding: '20px',
                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                borderRadius: '15px',
                animation: 'fadeIn 1s',
                boxShadow: '0 8px 16px rgba(0,0,0,0.2)'
            }
        });

        // Add animation styles
        if (!document.getElementById('audio-game-animations')) {
            const style = document.createElement('style');
            style.id = 'audio-game-animations';
            style.textContent = `
                @keyframes pulse {
                    0% { box-shadow: 0 0 0 0 rgba(255, 107, 107, 0.7); }
                    70% { box-shadow: 0 0 0 10px rgba(255, 107, 107, 0); }
                    100% { box-shadow: 0 0 0 0 rgba(255, 107, 107, 0); }
                }
                @keyframes correctAnswer {
                    0%, 100% { transform: scale(1); }
                    50% { transform: scale(1.1); }
                }
                @keyframes wrongAnswer {
                    0%, 100% { transform: translateX(0); }
                    25% { transform: translateX(-10px); }
                    75% { transform: translateX(10px); }
                }
                @keyframes bounce {
                    0% { transform: translateY(0); }
                    100% { transform: translateY(-20px); }
                }
                @keyframes fadeIn {
                    0% { opacity: 0; transform: scale(0.8); }
                    100% { opacity: 1; transform: scale(1); }
                }
            `;
            document.head.appendChild(style);
        }

        // Remove celebration after a delay
        setTimeout(() => {
            celebration.style.opacity = '0';
            celebration.style.transition = 'opacity 1s';
            setTimeout(() => {
                if (celebration.parentNode) {
                    celebration.parentNode.removeChild(celebration);
                }
            }, 1000);
        }, 5000);
    }
}

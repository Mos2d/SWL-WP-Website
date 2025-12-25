/**
 * Assessment Game - DEBUG VERSION
 */

class AssessmentGame extends GameFramework {
    constructor(containerId, options = {}) {
        console.log('🎮 AssessmentGame Constructor Called');
        console.log('Container ID:', containerId);
        console.log('Options:', options);
        
        const gameOptions = Object.assign({
            questions: [], 
            shuffleQuestions: false,
            shuffleAnswers: false,
            timePerQuestion: 0,
            showExplanations: true,
            nextQuestionDelay: 1500,
            mobileResponsive: true,
            adaptiveFontSize: true
        }, options);

        // Call Parent Constructor
        super(containerId, gameOptions);
        
        console.log('Parent constructor called. State:', this.state);

        // Define Fonts
        this.defaultFontSizes = {
            question: 24, answer: 18, progress: 18, explanation: 16
        };

        // Initialize game state
        this.gameState = {
            questions: [],
            currentQuestion: 0,
            scores: {},
            totals: {},
            questionContainer: null,
            answered: false,
            defaultFontSizes: this.defaultFontSizes,
            currentFontSizes: { ...this.defaultFontSizes }
        };
        
        console.log('Game state initialized:', this.gameState);
    }

    onGameStart() {
        console.log('🚀 AssessmentGame.onGameStart() called');
        console.log('Container:', this.container);
        console.log('Questions available:', this.options.questions ? this.options.questions.length : 0);
        
        this.clearElements();
        this.gameState.currentQuestion = 0;
        this.gameState.answered = false;
        
        // Prepare questions
        this.prepareQuestions();
        console.log('Questions prepared:', this.gameState.questions.length);
        
        // Create question container
        this.gameState.questionContainer = this.createElement('div', {
            classes: 'assessment-question-container',
            styles: {
                width: '100%',
                height: '100%',
                padding: this.state.isMobile ? '15px' : '20px',
                display: 'flex',
                flexDirection: 'column',
                justifyContent: 'center',
                alignItems: 'center',
                overflowY: 'auto',
                boxSizing: 'border-box',
                backgroundColor: '#ffffff' // Add background to make it visible
            }
        });
        
        console.log('Question container created:', this.gameState.questionContainer);
        
        // Add container to game container
        if (this.container) {
            this.container.appendChild(this.gameState.questionContainer);
            console.log('Question container added to game container');
        } else {
            console.error('Game container not found!');
        }
        
        // Display first question
        if (this.gameState.questions.length > 0) {
            this.displayQuestion(0);
        } else {
            this.showNoQuestionsMessage();
        }
        
        this.updateResponsiveLayout();
    }

    prepareQuestions() {
        console.log('📝 prepareQuestions() called');
        console.log('Options questions:', this.options.questions);
        
        let questions = [...this.options.questions];
        console.log('Questions array (raw):', questions);
        
        if (this.options.shuffleQuestions) {
            questions = this.shuffleArray(questions);
        }
        
        this.gameState.questions = questions;
        this.gameState.totals = {};
        this.gameState.scores = {};

        this.gameState.questions.forEach(q => {
            const cat = q.criteria_category || 'general';
            if (!this.gameState.totals[cat]) {
                this.gameState.totals[cat] = 0;
                this.gameState.scores[cat] = 0;
            }
            this.gameState.totals[cat]++;
        });
        
        console.log('Processed questions:', this.gameState.questions);
        console.log('Categories totals:', this.gameState.totals);
    }

    displayQuestion(index) {
        console.log(`📄 displayQuestion(${index}) called`);
        
        // Check if question container exists
        if (!this.gameState.questionContainer) {
            console.error('Question container is null! Creating new one...');
            this.gameState.questionContainer = this.createElement('div', {
                classes: 'assessment-question-container',
                styles: {
                    width: '100%',
                    height: '100%',
                    padding: this.state.isMobile ? '15px' : '20px',
                    display: 'flex',
                    flexDirection: 'column',
                    justifyContent: 'center',
                    alignItems: 'center',
                    overflowY: 'auto',
                    boxSizing: 'border-box',
                    backgroundColor: '#ffffff'
                }
            });
            
            if (this.container) {
                this.container.appendChild(this.gameState.questionContainer);
            }
        }

        const question = this.gameState.questions[index];
        console.log('Current question:', question);
        
        if (!question) {
            console.log('No question found at index', index, '- completing game');
            this.complete();
            return;
        }
        
        // Clear container
        this.gameState.questionContainer.innerHTML = '';
        this.gameState.answered = false;
        
        console.log('Displaying question text:', question.text);
        
        // Progress indicator
        this.createElement('div', {
            parent: this.gameState.questionContainer,
            text: `سؤال ${index + 1} من ${this.gameState.questions.length}`,
            styles: {
                marginBottom: '15px',
                fontSize: `${this.gameState.currentFontSizes.progress}px`,
                color: '#666', textAlign: 'center', width: '100%',
                backgroundColor: '#f0f0f0',
                padding: '5px',
                borderRadius: '5px'
            }
        });
        
        // Question text
        this.createElement('div', {
            parent: this.gameState.questionContainer,
            html: question.text || 'سؤال بدون نص',
            styles: {
                fontSize: `${this.gameState.currentFontSizes.question}px`,
                fontWeight: 'bold', marginBottom: '20px', textAlign: 'center', color: '#333', width: '100%',
                backgroundColor: '#e8f4fd',
                padding: '15px',
                borderRadius: '10px'
            }
        });

        // Image
        if (question.image) {
            console.log('Adding image:', question.image);
            this.createElement('img', {
                parent: this.gameState.questionContainer,
                attributes: { src: question.image },
                styles: {
                    maxWidth: '100%', maxHeight: this.state.isMobile ? '150px' : '200px',
                    marginBottom: '20px', borderRadius: '8px', objectFit: 'contain',
                    border: '2px solid #ddd'
                }
            });
        }

        // Audio
        if (question.audio_prompt) {
            console.log('Adding audio:', question.audio_prompt);
             this.createElement('audio', {
                parent: this.gameState.questionContainer,
                attributes: { controls: 'true', src: question.audio_prompt },
                styles: { width: '80%', marginBottom: '20px', borderRadius: '30px' }
            });
        }

        // Answers container
        const answersContainer = this.createElement('div', {
            parent: this.gameState.questionContainer,
            styles: {
                width: '100%', maxWidth: '600px', display: 'flex', flexDirection: 'column', gap: '10px',
                backgroundColor: '#f9f9f9',
                padding: '15px',
                borderRadius: '10px'
            }
        });
        
        let answers = [...question.answers];
        console.log('Question answers:', answers);
        
        if (this.options.shuffleAnswers) {
            answers = this.shuffleArray(answers);
        }

        if (answers.length === 0) {
            console.error('No answers for this question!');
            this.createElement('div', {
                parent: answersContainer,
                text: '⚠️ لا توجد إجابات لهذا السؤال',
                styles: { color: 'red', textAlign: 'center', padding: '10px' }
            });
        } else {
            answers.forEach((answer, i) => {
                console.log(`Answer ${i}:`, answer);
                
                const btnContent = answer.type === 'image' 
                    ? `<img src="${answer.image_url}" style="height: 50px; object-fit: contain;">` 
                    : (answer.text || 'إجابة بدون نص');

                const answerButton = this.createElement('button', {
                    parent: answersContainer,
                    html: btnContent,
                    classes: 'assessment-answer-btn',
                    styles: {
                        padding: '15px',
                        fontSize: `${this.gameState.currentFontSizes.answer}px`,
                        backgroundColor: '#f8f9fa',
                        border: '2px solid #e9ecef',
                        borderRadius: '8px',
                        cursor: 'pointer',
                        transition: 'all 0.2s',
                        width: '100%',
                        display: 'flex', alignItems: 'center', justifyContent: 'center', minHeight: '60px'
                    }
                });

                answerButton.onclick = () => {
                    console.log('Answer clicked:', answer.text, 'Correct:', answer.correct);
                    if (this.gameState.answered) return;
                    this.handleAnswer(answer.correct, answerButton, answersContainer);
                };
            });
        }

        // Start timer if needed
        if (this.options.timePerQuestion > 0) {
            setTimeout(() => {
                this.startTimer(answersContainer);
            }, 100);
        }
        
        this.updateResponsiveLayout();
        
        console.log('Question display completed');
    }

    startTimer(container) {
        console.log('⏰ startTimer() called');
        
        // Safety check
        if (!this.gameState.questionContainer) {
            console.error('Cannot start timer: questionContainer is null');
            return;
        }
        
        let timeLeft = this.options.timePerQuestion;
        
        const timerBar = this.createElement('div', {
            parent: this.gameState.questionContainer,
            styles: {
                width: '100%', height: '6px', backgroundColor: '#eee', 
                marginTop: '20px', borderRadius: '3px', overflow: 'hidden'
            }
        });
        
        const timerFill = this.createElement('div', {
            parent: timerBar,
            styles: {
                width: '100%', height: '100%', backgroundColor: '#4caf50', transition: 'width 1s linear'
            }
        });

        this.currentTimer = setInterval(() => {
            if (this.state.completed || this.gameState.answered) {
                console.log('Timer stopped - game completed or answered');
                clearInterval(this.currentTimer);
                return;
            }
            timeLeft--;
            timerFill.style.width = `${(timeLeft / this.options.timePerQuestion) * 100}%`;
            
            if (timeLeft <= 0) {
                console.log('Time expired');
                clearInterval(this.currentTimer);
                if (!this.gameState.answered) {
                    this.handleTimeout(container);
                }
            }
        }, 1000);
        
        console.log('Timer started with', timeLeft, 'seconds');
    }

    handleAnswer(isCorrect, selectedBtn, container) {
        console.log('✅ handleAnswer() called - Correct:', isCorrect);
        this.gameState.answered = true;
        if(this.currentTimer) {
            clearInterval(this.currentTimer);
            console.log('Timer cleared');
        }

        const buttons = container.querySelectorAll('button');
        buttons.forEach(btn => {
            btn.style.pointerEvents = 'none';
            if (btn === selectedBtn) {
                btn.style.backgroundColor = isCorrect ? '#d4edda' : '#f8d7da';
                btn.style.borderColor = isCorrect ? '#c3e6cb' : '#f5c6cb';
            }
        });

        if (isCorrect) {
            const currentQ = this.gameState.questions[this.gameState.currentQuestion];
            const category = currentQ.criteria_category || 'general';
            if (this.gameState.scores[category] !== undefined) {
                this.gameState.scores[category]++;
                console.log('Score updated for category', category, ':', this.gameState.scores[category]);
            }
        }
        this.showExplanation();
        setTimeout(() => {
            this.gameState.currentQuestion++;
            this.displayQuestion(this.gameState.currentQuestion);
        }, this.options.nextQuestionDelay);
    }

    handleTimeout(container) {
        console.log('⏱️ handleTimeout() called');
        this.gameState.answered = true;
        const buttons = container.querySelectorAll('button');
        buttons.forEach(btn => btn.style.pointerEvents = 'none');

        this.createElement('div', {
            parent: this.gameState.questionContainer,
            text: 'انتهى الوقت!',
            styles: { color: 'red', marginTop: '10px', fontWeight: 'bold' }
        });
        this.showExplanation();
        setTimeout(() => {
            this.gameState.currentQuestion++;
            this.displayQuestion(this.gameState.currentQuestion);
        }, this.options.nextQuestionDelay);
    }

    showExplanation() {
        const currentQuestion = this.gameState.questions[this.gameState.currentQuestion];
        console.log('💡 showExplanation() - Question has explanation:', !!currentQuestion.explanation);
        if (this.options.showExplanations && currentQuestion.explanation) {
            this.createElement('div', {
                parent: this.gameState.questionContainer,
                html: currentQuestion.explanation,
                styles: {
                    marginTop: '15px', padding: '10px', backgroundColor: '#e2e3e5',
                    borderRadius: '5px', fontSize: '14px', color: '#383d41'
                }
            });
        }
    }

    showNoQuestionsMessage() {
        console.log('Showing "no questions" message');
        if (this.gameState.questionContainer) {
            this.gameState.questionContainer.innerHTML = '';
            this.createElement('div', {
                parent: this.gameState.questionContainer,
                html: '<div style="text-align: center; padding: 40px; color: #666;"><h3>⚠️ لا توجد أسئلة متاحة</h3><p>يرجى التحقق من إعدادات اللعبة.</p></div>',
                styles: {
                    width: '100%',
                    height: '100%',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center'
                }
            });
        }
    }

    complete() {
        console.log('Game complete! Showing results...');
        let reportHTML = '<div style="width:100%; max-width:400px; margin:20px auto; text-align:right; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">';
        reportHTML += '<h3 style="text-align:center; margin-bottom:15px; color: #333;">🎉 تقرير النتائج</h3>';
        
        let hasResults = false;
        for (const [category, score] of Object.entries(this.gameState.scores)) {
            const total = this.gameState.totals[category];
            if (total > 0) {
                hasResults = true;
                const percent = total === 0 ? 0 : Math.round((score / total) * 100);
                const catName = this.getCategoryName(category);
                reportHTML += `
                    <div style="margin-bottom:15px;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                            <strong>${catName}</strong>
                            <span>${score}/${total} (${percent}%)</span>
                        </div>
                        <div style="background:#eee; height:10px; border-radius:5px; overflow:hidden;">
                            <div style="background:#4caf50; width:${percent}%; height:100%;"></div>
                        </div>
                    </div>
                `;
            }
        }
        
        if (!hasResults) {
            reportHTML += '<p style="text-align:center; color: #666;">لم يتم تسجيل أي نتائج.</p>';
        }
        
        reportHTML += '</div>';
        
        super.complete(0);
        const contentBox = this.overlay.querySelector('.game-content') || this.overlay;
        const resultsDiv = document.createElement('div');
        resultsDiv.innerHTML = reportHTML;
        const btn = contentBox.querySelector('button');
        if (btn) contentBox.insertBefore(resultsDiv, btn);
        else contentBox.appendChild(resultsDiv);
    }

    getCategoryName(slug) {
        const names = {
            'listening': 'الاستماع', 'vocabulary': 'المفردات', 'grammar': 'القواعد', 'reading': 'القراءة', 'general': 'عام'
        };
        return names[slug] || slug;
    }

    updateResponsiveLayout() {
        if (!this.gameState) return;
        const width = window.innerWidth;
        this.state.isMobile = width < 768;
        let scale = 1;
        if (width < 480) scale = 0.8;
        const defaults = this.gameState.defaultFontSizes || { question: 24, answer: 18, progress: 18, explanation: 16 };
        this.gameState.currentFontSizes = {
            question: Math.max(16, defaults.question * scale),
            answer: Math.max(14, defaults.answer * scale),
            progress: Math.max(12, defaults.progress * scale),
            explanation: Math.max(12, defaults.explanation * scale)
        };
    }

    shuffleArray(array) {
        return array.sort(() => Math.random() - 0.5);
    }
}
/**
 * Audio Game Loader
 * Utility class for loading and managing audio game data
 */

class AudioGameLoader {
    constructor() {
        this.gameData = null;
        this.currentTemplate = null;
        this.customGames = new Map();
        this.loadingPromise = null;
    }

    /**
     * Load game data from JSON file
     */
    async loadGameData() {
        if (this.loadingPromise) {
            return this.loadingPromise;
        }

        this.loadingPromise = this.fetchGameData();
        return this.loadingPromise;
    }

    /**
     * Fetch game data from the JSON file
     */
    async fetchGameData() {
        try {
            const response = await fetch('/wp-content/themes/sarah-loz-theme/assets/js/games/audio-game-data.json');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            this.gameData = await response.json();
            console.log('Audio game data loaded successfully', this.gameData);
            return this.gameData;
        } catch (error) {
            console.error('Failed to load audio game data:', error);
            // Fallback to default data
            this.gameData = this.getDefaultGameData();
            return this.gameData;
        }
    }

    /**
     * Get a specific game template by ID
     */
    async getGameTemplate(templateId = 'colors') {
        if (!this.gameData) {
            await this.loadGameData();
        }

        const template = this.gameData.gameTemplates[templateId];
        if (!template) {
            console.warn(`Game template '${templateId}' not found, using default`);
            return this.gameData.gameTemplates.colors;
        }

        this.currentTemplate = template;
        return template;
    }

    /**
     * Get all available game templates
     */
    async getAvailableTemplates() {
        if (!this.gameData) {
            await this.loadGameData();
        }

        return Object.keys(this.gameData.gameTemplates).map(key => ({
            id: key,
            title: this.gameData.gameTemplates[key].title,
            description: this.gameData.gameTemplates[key].description,
            type: this.gameData.gameTemplates[key].type,
            difficulty: this.gameData.gameTemplates[key].difficulty,
            ageGroup: this.gameData.gameTemplates[key].ageGroup
        }));
    }

    /**
     * Create a new audio matching game instance
     */
    async createGame(containerId, templateId = 'colors', customOptions = {}) {
        try {
            const template = await this.getGameTemplate(templateId);
            
            // Merge template data with custom options
            const gameOptions = {
                gameData: template,
                ...this.gameData.gameSettings.speechSettings,
                ...this.gameData.gameSettings.uiSettings,
                ...this.gameData.gameSettings.gameplaySettings,
                ...customOptions
            };

            // Apply theme if specified
            if (customOptions.theme && this.gameData.customizationOptions.themes[customOptions.theme]) {
                gameOptions.theme = this.gameData.customizationOptions.themes[customOptions.theme];
            }

            console.log('Creating audio game with options:', gameOptions);
            
            // Create and return the game instance
            return new AudioMatchingGame(containerId, gameOptions);
        } catch (error) {
            console.error('Failed to create audio game:', error);
            throw error;
        }
    }

    /**
     * Create a custom game from user-defined data
     */
    createCustomGame(containerId, customGameData, options = {}) {
        const gameOptions = {
            gameData: customGameData,
            ...this.gameData?.gameSettings?.speechSettings || {},
            ...this.gameData?.gameSettings?.uiSettings || {},
            ...this.gameData?.gameSettings?.gameplaySettings || {},
            ...options
        };

        return new AudioMatchingGame(containerId, gameOptions);
    }

    /**
     * Save a custom game template
     */
    saveCustomTemplate(templateId, templateData) {
        this.customGames.set(templateId, {
            ...templateData,
            id: templateId,
            isCustom: true,
            createdAt: new Date().toISOString()
        });

        // Save to localStorage for persistence
        this.saveCustomGamesToStorage();
        
        console.log(`Custom game template '${templateId}' saved`);
    }

    /**
     * Load custom games from localStorage
     */
    loadCustomGamesFromStorage() {
        try {
            const stored = localStorage.getItem('sarahLozCustomAudioGames');
            if (stored) {
                const customGames = JSON.parse(stored);
                Object.entries(customGames).forEach(([id, data]) => {
                    this.customGames.set(id, data);
                });
                console.log('Custom games loaded from storage:', this.customGames.size);
            }
        } catch (error) {
            console.error('Failed to load custom games from storage:', error);
        }
    }

    /**
     * Save custom games to localStorage
     */
    saveCustomGamesToStorage() {
        try {
            const customGamesObj = Object.fromEntries(this.customGames);
            localStorage.setItem('sarahLozCustomAudioGames', JSON.stringify(customGamesObj));
        } catch (error) {
            console.error('Failed to save custom games to storage:', error);
        }
    }

    /**
     * Get a custom game template
     */
    getCustomTemplate(templateId) {
        return this.customGames.get(templateId);
    }

    /**
     * Delete a custom game template
     */
    deleteCustomTemplate(templateId) {
        const deleted = this.customGames.delete(templateId);
        if (deleted) {
            this.saveCustomGamesToStorage();
            console.log(`Custom game template '${templateId}' deleted`);
        }
        return deleted;
    }

    /**
     * Get all custom game templates
     */
    getCustomTemplates() {
        return Array.from(this.customGames.entries()).map(([id, data]) => ({
            id,
            ...data
        }));
    }

    /**
     * Validate game template data
     */
    validateTemplate(templateData) {
        const errors = [];

        // Check required fields
        if (!templateData.title) errors.push('Title is required');
        if (!templateData.description) errors.push('Description is required');
        if (!templateData.questions || !Array.isArray(templateData.questions)) {
            errors.push('Questions array is required');
        } else if (templateData.questions.length === 0) {
            errors.push('At least one question is required');
        }

        // Validate questions
        templateData.questions?.forEach((question, index) => {
            if (!question.text) errors.push(`Question ${index + 1}: Text is required`);
            if (!question.sound) errors.push(`Question ${index + 1}: Sound is required`);
            if (!question.options || !Array.isArray(question.options)) {
                errors.push(`Question ${index + 1}: Options array is required`);
            } else {
                const correctOptions = question.options.filter(opt => opt.correct);
                if (correctOptions.length !== 1) {
                    errors.push(`Question ${index + 1}: Exactly one correct option is required`);
                }
                
                question.options.forEach((option, optIndex) => {
                    if (!option.text) {
                        errors.push(`Question ${index + 1}, Option ${optIndex + 1}: Text is required`);
                    }
                });
            }
        });

        return {
            isValid: errors.length === 0,
            errors
        };
    }

    /**
     * Get game settings
     */
    async getGameSettings() {
        if (!this.gameData) {
            await this.loadGameData();
        }
        return this.gameData.gameSettings;
    }

    /**
     * Update game settings
     */
    updateGameSettings(newSettings) {
        if (this.gameData) {
            this.gameData.gameSettings = {
                ...this.gameData.gameSettings,
                ...newSettings
            };
        }
    }

    /**
     * Get available themes
     */
    async getAvailableThemes() {
        if (!this.gameData) {
            await this.loadGameData();
        }
        return this.gameData.customizationOptions.themes;
    }

    /**
     * Generate a sample game template
     */
    generateSampleTemplate(type = 'colors') {
        const sampleTemplates = {
            colors: {
                title: "لعبة الألوان المخصصة",
                description: "اختر الألوان الصحيحة",
                type: "color",
                difficulty: "easy",
                questions: [
                    {
                        id: 1,
                        text: "أحمر",
                        sound: "أحمر",
                        type: "color",
                        options: [
                            { text: "أحمر", color: "#dc3545", correct: true },
                            { text: "أزرق", color: "#007bff", correct: false }
                        ]
                    }
                ]
            },
            animals: {
                title: "لعبة الحيوانات المخصصة",
                description: "اختر الحيوانات الصحيحة",
                type: "animal",
                difficulty: "medium",
                questions: [
                    {
                        id: 1,
                        text: "قطة",
                        sound: "مياو - هذا صوت القطة",
                        type: "animal",
                        options: [
                            { text: "قطة", image: "🐱", correct: true },
                            { text: "كلب", image: "🐶", correct: false }
                        ]
                    }
                ]
            }
        };

        return sampleTemplates[type] || sampleTemplates.colors;
    }

    /**
     * Export game template for sharing
     */
    exportTemplate(templateId) {
        const template = this.gameData?.gameTemplates[templateId] || this.customGames.get(templateId);
        if (template) {
            const exportData = {
                template,
                exportedAt: new Date().toISOString(),
                version: this.gameData?.version || '1.0.0'
            };
            return JSON.stringify(exportData, null, 2);
        }
        return null;
    }

    /**
     * Import game template from JSON
     */
    importTemplate(jsonData) {
        try {
            const importData = JSON.parse(jsonData);
            const template = importData.template;
            
            const validation = this.validateTemplate(template);
            if (!validation.isValid) {
                throw new Error('Invalid template: ' + validation.errors.join(', '));
            }

            return template;
        } catch (error) {
            throw new Error('Failed to import template: ' + error.message);
        }
    }

    /**
     * Get default fallback data
     */
    getDefaultGameData() {
        return {
            gameTemplates: {
                colors: {
                    id: "colors",
                    title: "لعبة ربط الألوان",
                    description: "اضغط على الصوت واختر اللون الصحيح",
                    type: "color",
                    difficulty: "easy",
                    questions: [
                        {
                            id: 1,
                            text: "أحمر",
                            sound: "أحمر",
                            type: "color",
                            options: [
                                { text: "أحمر", color: "#dc3545", correct: true },
                                { text: "أزرق", color: "#007bff", correct: false },
                                { text: "أخضر", color: "#28a745", correct: false },
                                { text: "أصفر", color: "#ffc107", correct: false }
                            ]
                        }
                    ]
                }
            },
            gameSettings: {
                speechSettings: {
                    defaultLanguage: "ar-SA",
                    speechRate: 0.8,
                    speechPitch: 1.2,
                    speechVolume: 1.0
                },
                uiSettings: {
                    showFeedback: true,
                    autoPlayIntro: true,
                    shuffleOptions: true,
                    maxAttempts: 3
                },
                gameplaySettings: {
                    timeLimit: 0,
                    showTimer: false,
                    autoAdvance: false
                }
            }
        };
    }

    /**
     * Initialize the loader
     */
    async initialize() {
        this.loadCustomGamesFromStorage();
        await this.loadGameData();
        console.log('AudioGameLoader initialized');
    }
}

// Create global instance
window.AudioGameLoader = window.AudioGameLoader || new AudioGameLoader();

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.AudioGameLoader.initialize();
    });
} else {
    window.AudioGameLoader.initialize();
}
